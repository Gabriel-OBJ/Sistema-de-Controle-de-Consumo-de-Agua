<?php

use App\Http\Controllers\ConfiguracaoTaxaController;
use App\Http\Controllers\ConsumidorController;
use App\Http\Controllers\FaturaController;
use App\Http\Controllers\LeituraController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Página inicial ──────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('consumidores.index'));

// ── Dashboard (Breeze) ──────────────────────────────────────────────────────
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ── Perfil do usuário (Breeze) ──────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Rotas do Sistema (requerem autenticação) ────────────────────────────────
Route::middleware('auth')->group(function () {

    // ── Rotas exclusivas para ADMIN ─────────────────────────────────────────
    // Apenas administradores podem cadastrar, editar consumidores e alterar taxas.
    Route::middleware('admin')->group(function () {

        // Consumidores: cadastro e remoção
        Route::resource('consumidores', ConsumidorController::class)
            ->parameters(['consumidores' => 'consumidor'])
            ->only(['create', 'store', 'destroy']);

        // Configuração de taxa de cobrança
        Route::get('configuracao', [ConfiguracaoTaxaController::class, 'index'])
            ->name('configuracao.index');
        Route::post('configuracao', [ConfiguracaoTaxaController::class, 'store'])
            ->name('configuracao.store');
    });

    // ── Rotas acessíveis a todos os usuários autenticados ───────────────────
    // Listagem de consumidores (sem ações de escrita)
    Route::get('consumidores', [ConsumidorController::class, 'index'])
        ->name('consumidores.index');

    // Leituras: listar e registrar (leiturista e admin)
    Route::resource('leituras', LeituraController::class)
        ->only(['index', 'create', 'store']);

    // Faturas: visualizar e marcar como paga
    Route::get('faturas', [FaturaController::class, 'index'])
        ->name('faturas.index');
    Route::patch('faturas/{fatura}/pagar', [FaturaController::class, 'pagar'])
        ->name('faturas.pagar');
});

require __DIR__ . '/auth.php';
