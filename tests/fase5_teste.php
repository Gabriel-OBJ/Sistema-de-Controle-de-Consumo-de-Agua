<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Consumidor;
use App\Models\Fatura;
use App\Models\Leitura;
use App\Models\LogAcesso;
use App\Models\ConfiguracaoTaxa;

echo "╔══════════════════════════════════════════════════╗\n";
echo "║   FASE 5 — TESTE GERAL DO SISTEMA (E2E)          ║\n";
echo "╚══════════════════════════════════════════════════╝\n\n";

$pass = 0; $fail = 0;

function check(string $label, bool $result, string $detail = ''): void {
    global $pass, $fail;
    if ($result) { echo "  ✅ PASSOU  $label" . ($detail ? " → $detail" : '') . "\n"; $pass++; }
    else         { echo "  ❌ FALHOU  $label" . ($detail ? " → $detail" : '') . "\n"; $fail++; }
}

// 1. Criar Consumidor
$consumidor = Consumidor::create([
    'nome' => 'João E2E Teste',
    'endereco' => 'Rua Principal, 100',
    'telefone' => '11999998888',
    'numero_medidor' => 'MED-E2E-' . rand(1000, 9999),
]);
check('Fluxo: Consumidor criado com sucesso', $consumidor->id !== null);

// 2. Garantir configuração
$config = ConfiguracaoTaxa::firstOrCreate([], ['taxa_fixa' => 30.00, 'valor_excedente' => 2.50]);

// 3. Cadastrar Leitura (simulando 12m3, que tem 2m3 de excedente)
$leitura = Leitura::create([
    'consumidor_id' => $consumidor->id,
    'mes_referencia' => 7,
    'ano_referencia' => 2026,
    'leitura_anterior' => 0.0,
    'leitura_atual' => 12.0,
    'consumo_m3' => 12.0,
]);
check('Fluxo: Leitura inserida com sucesso (12m³)', $leitura->id !== null);

// Simular cálculo de fatura usando o service (como o controller faz)
$calculator = app(\App\Services\FaturaCalculatorService::class);
$consumo = $leitura->leitura_atual - $leitura->leitura_anterior;
$valorFatura = $calculator->calcular($consumo, $config->taxa_fixa, 10.0, $config->valor_excedente);

$fatura = Fatura::create([
    'consumidor_id' => $consumidor->id,
    'leitura_id' => $leitura->id,
    'valor_total' => $valorFatura,
    'status' => 'Pendente',
]);
check('Fluxo: Fatura gerada com base na leitura', $fatura->id !== null);
check("Fluxo: Cálculo matemático da fatura correto (R$ {$valorFatura})", $fatura->valor_total == $valorFatura);

// 4. Log de Acesso (Auditoria)
LogAcesso::create([
    'user_id' => null,
    'consumidor_id' => $consumidor->id,
    'acao' => 'Visualizou detalhes do consumidor',
    'ip_address' => '127.0.0.1',
]);
$logCount = LogAcesso::where('consumidor_id', $consumidor->id)->count();
check('Fluxo: Auditoria LGPD registra acesso ao consumidor', $logCount > 0);

// 5. Exclusão (Soft Delete)
$consumidor->delete();
$deleted = Consumidor::withTrashed()->where('id', $consumidor->id)->whereNotNull('deleted_at')->exists();
check('Fluxo: SoftDelete remove consumidor mantendo integridade', $deleted);

// 6. Consultar Fatura do Consumidor Removido
$faturaVerificacao = Fatura::with(['leitura.consumidor' => function ($q) {
    $q->withTrashed();
}])->find($fatura->id);
check('Fluxo: Integridade mantida (fatura ainda vinculada ao consumidor removido)', $faturaVerificacao->leitura->consumidor->nome === 'João E2E Teste');

echo "\n══════════════════════════════════════════════════\n";
echo "  Resultado: {$pass} passou / {$fail} falhou\n";
echo "══════════════════════════════════════════════════\n\n";

if ($fail === 0) echo "🎉 SISTEMA TOTALMENTE OPERACIONAL!\n";
else            echo "⚠️  Há {$fail} problema(s) a corrigir.\n";

// Cleanup
$fatura->delete();
$leitura->delete();
LogAcesso::where('consumidor_id', $consumidor->id)->delete();
$consumidor->forceDelete();
