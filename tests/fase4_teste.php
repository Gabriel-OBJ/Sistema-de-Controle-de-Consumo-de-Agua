<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Consumidor;
use App\Models\LogAcesso;
use Illuminate\Support\Facades\Schema;

echo "╔══════════════════════════════════════════════════╗\n";
echo "║   FASE 4 — LGPD NA PRÁTICA (TESTES)              ║\n";
echo "╚══════════════════════════════════════════════════╝\n\n";

$pass = 0; $fail = 0;

function check(string $label, bool $result, string $detail = ''): void {
    global $pass, $fail;
    if ($result) { echo "  ✅ PASSOU  $label" . ($detail ? " → $detail" : '') . "\n"; $pass++; }
    else         { echo "  ❌ FALHOU  $label" . ($detail ? " → $detail" : '') . "\n"; $fail++; }
}

// 1. Verificando estrutura do banco (SoftDeletes)
check('Tabela consumidores possui deleted_at', Schema::hasColumn('consumidores', 'deleted_at'));

// 2. Verificando tabela log_acessos
check('Tabela log_acessos existe', Schema::hasTable('log_acessos'));
check('Tabela log_acessos possui os campos necessários', Schema::hasColumns('log_acessos', ['user_id', 'consumidor_id', 'acao', 'ip_address']));

// 3. Verificando Soft Deletes no Model
$modelConsumidor = new Consumidor();
$usesSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($modelConsumidor));
check('Model Consumidor usa SoftDeletes trait', $usesSoftDeletes);

// 4. Teste Prático: Soft Delete
$testConsumidor = Consumidor::create([
    'nome' => 'LGPD Test User',
    'endereco' => 'Rua dos Bobos, 0',
    'telefone' => '11900000000',
    'numero_medidor' => 'MED-LGPD-' . rand(1000, 9999),
]);

$testConsumidor->delete();
$isSoftDeleted = Consumidor::withTrashed()->where('id', $testConsumidor->id)->whereNotNull('deleted_at')->exists();
check('Consumidor é removido usando Soft Deletes no banco', $isSoftDeleted);

// 5. Teste Prático: Auditoria de Controller
// Nós iremos simular uma requisição HTTP para o controller usando test framework (ou testando manualmente aqui chamando o controller)
$request = Illuminate\Http\Request::create('/consumidores', 'GET');
$request->setRouteResolver(function () use ($request) {
    return (new Illuminate\Routing\Route('GET', 'consumidores', []))->bind($request);
});
app()->instance('request', $request);

try {
    $controller = app(\App\Http\Controllers\ConsumidorController::class);
    $controller->index();
    
    // Verifica se salvou o log
    $logCount = LogAcesso::where('acao', 'Visualizou listagem de consumidores')->count();
    check('Log de acesso registrado ao listar consumidores', $logCount > 0);
} catch (\Exception $e) {
    check('Log de acesso registrado ao listar consumidores', false, 'Erro: ' . $e->getMessage());
}

try {
    $controller->edit($testConsumidor);
    $logCount2 = LogAcesso::where('acao', 'Acessou formulário de edição do consumidor')
        ->where('consumidor_id', $testConsumidor->id)
        ->count();
    check('Log de acesso registrado ao editar um consumidor', $logCount2 > 0);
} catch (\Exception $e) {
    check('Log de acesso registrado ao editar um consumidor', false, 'Erro: ' . $e->getMessage());
}

echo "\n══════════════════════════════════════════════════\n";
echo "  Resultado: {$pass} passou / {$fail} falhou\n";
echo "══════════════════════════════════════════════════\n\n";

if ($fail === 0) echo "🎉 FASE 4 COMPLETAMENTE VALIDADA!\n";
else            echo "⚠️  Há {$fail} problema(s) a corrigir.\n";

// Cleanup
$testConsumidor->forceDelete();
LogAcesso::where('acao', 'like', '%consumidor%')->delete();
