<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracaoTaxa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfiguracaoTaxaController extends Controller
{
    /**
     * Exibe o formulário de configuração de taxa com os valores atuais.
     */
    public function index(): View
    {
        // Busca a configuração ativa ou cria um objeto default para evitar erro na view
        $configuracao = ConfiguracaoTaxa::latest()->first() ?? new ConfiguracaoTaxa([
            'taxa_fixa'       => 25.00,
            'valor_excedente' => 2.00,
        ]);

        return view('configuracao.index', compact('configuracao'));
    }

    /**
     * Persiste uma nova configuração de taxa.
     * Cria um novo registro para manter histórico de alterações.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'taxa_fixa'       => 'required|numeric|min:0',
            'valor_excedente' => 'required|numeric|min:0',
        ], [
            'taxa_fixa.required'       => 'A taxa fixa é obrigatória.',
            'taxa_fixa.numeric'        => 'A taxa fixa deve ser um valor numérico.',
            'taxa_fixa.min'            => 'A taxa fixa não pode ser negativa.',
            'valor_excedente.required' => 'O valor excedente é obrigatório.',
            'valor_excedente.numeric'  => 'O valor excedente deve ser um valor numérico.',
            'valor_excedente.min'      => 'O valor excedente não pode ser negativo.',
        ]);

        // Cria um novo registro ao invés de sobrescrever para preservar histórico
        ConfiguracaoTaxa::create($validated);

        return redirect()
            ->route('configuracao.index')
            ->with('success', 'Configuração de taxa atualizada com sucesso!');
    }
}
