<?php

/**
 * Script de teste da Fase 3 — CobrancaService
 * Execute com: php tests/fase3_teste.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\CobrancaService;
use App\Models\ConfiguracaoTaxa;
use App\Models\Consumidor;
use App\Models\Leitura;

$service = new CobrancaService();
$config  = ConfiguracaoTaxa::ativa();

echo "╔══════════════════════════════════════════╗\n";
echo "║   TESTE FASE 3 — CobrancaService         ║\n";
echo "╚══════════════════════════════════════════╝\n\n";

echo "Configuração ativa:\n";
echo "  Taxa fixa:        R\$ {$config->taxa_fixa}\n";
echo "  Valor excedente:  R\$ {$config->valor_excedente}/m³\n\n";

// Cenários de teste
$cenarios = [
    ['consumo' => 0.0,  'esperado' => 25.00, 'descricao' => '0 m³  (sem consumo)'],
    ['consumo' => 5.0,  'esperado' => 25.00, 'descricao' => '5 m³  (abaixo do limite)'],
    ['consumo' => 10.0, 'esperado' => 25.00, 'descricao' => '10 m³ (exatamente no limite)'],
    ['consumo' => 11.0, 'esperado' => 27.00, 'descricao' => '11 m³ (1 m³ excedente)'],
    ['consumo' => 15.0, 'esperado' => 35.00, 'descricao' => '15 m³ (5 m³ excedentes) [EXEMPLO DO ENUNCIADO]'],
    ['consumo' => 20.0, 'esperado' => 45.00, 'descricao' => '20 m³ (10 m³ excedentes)'],
    ['consumo' => 25.5, 'esperado' => 56.00, 'descricao' => '25.5 m³ (15.5 m³ excedentes)'],
];

echo "--- Cenários de Cálculo ---\n";
$passou = 0;
$falhou = 0;

foreach ($cenarios as $c) {
    $resultado = $service->calcularValor($c['consumo'], $config);
    $ok = abs($resultado - $c['esperado']) < 0.01;

    $status = $ok ? '✅ OK' : '❌ FALHOU';
    $ok ? $passou++ : $falhou++;

    echo sprintf(
        "  %s  %-50s → R\$ %6.2f  (esperado: R\$ %6.2f)\n",
        $status,
        $c['descricao'],
        $resultado,
        $c['esperado']
    );
}

echo "\n--- Resultado: {$passou} passou / {$falhou} falhou ---\n\n";

// Teste de banco: verifica se ConfiguracaoTaxa::ativa() funciona
echo "--- Teste de Banco de Dados ---\n";
try {
    $cfg = ConfiguracaoTaxa::ativa();
    echo "  ✅ ConfiguracaoTaxa::ativa() → id={$cfg->id}, taxa_fixa={$cfg->taxa_fixa}\n";
} catch (\Exception $e) {
    echo "  ❌ Erro: " . $e->getMessage() . "\n";
}

// Conta consumidores e leituras
$totalConsumidores = Consumidor::count();
$totalLeituras     = Leitura::count();
echo "  ℹ  Consumidores cadastrados: {$totalConsumidores}\n";
echo "  ℹ  Leituras registradas:     {$totalLeituras}\n";

echo "\n✅ Teste da Fase 3 concluído!\n";
