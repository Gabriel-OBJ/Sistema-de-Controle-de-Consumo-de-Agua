<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\FaturaCalculatorService;
use App\Services\CobrancaService;

$calculator = new FaturaCalculatorService();

echo "╔══════════════════════════════════════════════════╗\n";
echo "║   FASE 2 — TESTES DE VALIDAÇÃO                   ║\n";
echo "╚══════════════════════════════════════════════════╝\n\n";

$pass = 0; $fail = 0;

function check(string $label, bool $result, string $detail = ''): void {
    global $pass, $fail;
    if ($result) { echo "  ✅ PASSOU  $label" . ($detail ? " → $detail" : '') . "\n"; $pass++; }
    else         { echo "  ❌ FALHOU  $label" . ($detail ? " → $detail" : '') . "\n"; $fail++; }
}

// ── 1. FaturaCalculatorService — arquivo e assinatura ───────────────────────
$file = app_path('Services/FaturaCalculatorService.php');
check('FaturaCalculatorService.php existe', file_exists($file));

$src = file_get_contents($file);
check('Método calcular() com 4 parâmetros tipados', str_contains($src, 'calcular(') && str_contains($src, 'float $consumoM3') && str_contains($src, 'float $taxaFixa') && str_contains($src, 'float $limiteM3') && str_contains($src, 'float $valorExcedente'));
check('Retorno do método é float', str_contains($src, '): float'));

// ── 2. Regras matemáticas do FaturaCalculatorService ────────────────────────
echo "\n  Casos de teste matemáticos:\n";

// Caso A: consumo dentro do limite
$v = $calculator->calcular(consumoM3: 8.0, taxaFixa: 25.0, limiteM3: 10.0, valorExcedente: 2.0);
check('8 m³ (≤ limite 10) → apenas taxa fixa R$25,00', $v === 25.0, "calculado: R$$v");

// Caso B: consumo exatamente no limite
$v = $calculator->calcular(consumoM3: 10.0, taxaFixa: 25.0, limiteM3: 10.0, valorExcedente: 2.0);
check('10 m³ (= limite 10) → taxa fixa R$25,00', $v === 25.0, "calculado: R$$v");

// Caso C: consumo acima do limite
$v = $calculator->calcular(consumoM3: 15.0, taxaFixa: 25.0, limiteM3: 10.0, valorExcedente: 2.0);
check('15 m³ (5 excedentes × R$2) → R$35,00', $v === 35.0, "calculado: R$$v");

// Caso D: consumo zero
$v = $calculator->calcular(consumoM3: 0.0, taxaFixa: 30.0, limiteM3: 10.0, valorExcedente: 2.5);
check('0 m³ → apenas taxa fixa R$30,00', $v === 30.0, "calculado: R$$v");

// Caso E: excedente com casas decimais
$v = $calculator->calcular(consumoM3: 12.5, taxaFixa: 30.0, limiteM3: 10.0, valorExcedente: 2.5);
check('12.5 m³ (2.5 excedentes × R$2.50) → R$36,25', $v === 36.25, "calculado: R$$v");

// ── 3. CobrancaService usa FaturaCalculatorService via DI ──────────────────
echo "\n  Injeção de dependência:\n";

$cobSrc = file_get_contents(app_path('Services/CobrancaService.php'));
check('CobrancaService recebe FaturaCalculatorService no __construct', str_contains($cobSrc, 'FaturaCalculatorService') && str_contains($cobSrc, '__construct'));
check('CobrancaService NÃO usa "new FaturaCalculatorService"', !str_contains($cobSrc, 'new FaturaCalculatorService'));

// Testa via container do Laravel (DI automático)
$cobranca = app(CobrancaService::class);
check('Container Laravel resolve CobrancaService com DI automático', $cobranca instanceof CobrancaService);

// ── 4. LeituraController usa FaturaCalculatorService via DI ─────────────────
$ctrlSrc = file_get_contents(app_path('Http/Controllers/LeituraController.php'));
check('LeituraController injeta FaturaCalculatorService no __construct', str_contains($ctrlSrc, 'FaturaCalculatorService') && str_contains($ctrlSrc, '__construct'));
check('LeituraController NÃO usa "new" diretamente', !str_contains($ctrlSrc, 'new FaturaCalculatorService') && !str_contains($ctrlSrc, 'new CobrancaService'));
check('LeituraController chama ->calcular() no store()', str_contains($ctrlSrc, '->calcular(') && str_contains($ctrlSrc, 'faturaCalculator'));

// Testa resolução do controller pelo container
$ctrl = app(\App\Http\Controllers\LeituraController::class);
check('Container Laravel resolve LeituraController com DI automático', $ctrl instanceof \App\Http\Controllers\LeituraController);

echo "\n══════════════════════════════════════════════════\n";
echo "  Resultado: {$pass} passou / {$fail} falhou\n";
echo "══════════════════════════════════════════════════\n\n";

if ($fail === 0) echo "🎉 FASE 2 COMPLETAMENTE VALIDADA!\n";
else            echo "⚠️  Há {$fail} problema(s) a corrigir.\n";
