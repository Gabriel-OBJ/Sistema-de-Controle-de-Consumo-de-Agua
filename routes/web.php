<?php

use App\Http\Controllers\ConfiguracaoTaxaController;
use App\Http\Controllers\ConsumidorController;
use App\Http\Controllers\FaturaController;
use App\Http\Controllers\LeituraController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Sistema de Controle de Consumo de Água
|--------------------------------------------------------------------------
*/

// Redireciona a raiz para o painel de consumidores
Route::get('/', fn () => redirect()->route('consumidores.index'));

// ── Consumidores ───────────────────────────────────────────────────────────
// ->parameters() corrige singularização incorreta do português:
// Laravel gerava {consumidore} ao invés de {consumidor}, quebrando o model binding.
Route::resource('consumidores', ConsumidorController::class)
    ->parameters(['consumidores' => 'consumidor'])
    ->only(['index', 'create', 'store', 'destroy']);

// ── Leituras ───────────────────────────────────────────────────────────────
Route::resource('leituras', LeituraController::class)
    ->only(['index', 'create', 'store']);

// ── Faturas ────────────────────────────────────────────────────────────────
Route::resource('faturas', FaturaController::class)
    ->only(['index']);

// Marcar fatura como paga (PATCH para semântica REST correta)
Route::patch('faturas/{fatura}/pagar', [FaturaController::class, 'pagar'])
    ->name('faturas.pagar');

// ── Configuração de Taxa ────────────────────────────────────────────────────
Route::get('configuracao', [ConfiguracaoTaxaController::class, 'index'])
    ->name('configuracao.index');

Route::post('configuracao', [ConfiguracaoTaxaController::class, 'store'])
    ->name('configuracao.store');
