<?php

namespace App\Http\Controllers;

use App\Models\Consumidor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsumidorController extends Controller
{
    /**
     * Lista todos os consumidores cadastrados.
     */
    public function index(): View
    {
        $consumidores = Consumidor::orderBy('nome')->paginate(15);

        return view('consumidores.index', compact('consumidores'));
    }

    /**
     * Exibe o formulário de cadastro de novo consumidor.
     */
    public function create(): View
    {
        return view('consumidores.create');
    }

    /**
     * Persiste um novo consumidor no banco de dados.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome'            => 'required|string|max:255',
            'endereco'        => 'required|string|max:255',
            'telefone'        => 'required|string|max:20',
            'numero_medidor'  => 'required|string|max:50|unique:consumidores,numero_medidor',
        ], [
            'nome.required'           => 'O nome é obrigatório.',
            'endereco.required'       => 'O endereço é obrigatório.',
            'telefone.required'       => 'O telefone é obrigatório.',
            'numero_medidor.required' => 'O número do medidor é obrigatório.',
            'numero_medidor.unique'   => 'Este número de medidor já está cadastrado.',
        ]);

        Consumidor::create($validated);

        return redirect()
            ->route('consumidores.index')
            ->with('success', 'Consumidor cadastrado com sucesso!');
    }

    /**
     * Remove um consumidor do banco de dados.
     *
     * Regra de negócio: consumidores com leituras ou faturas registradas
     * NÃO podem ser removidos, pois representam histórico financeiro da associação.
     * Apenas cadastros sem histórico (erros de digitação, etc.) podem ser excluídos.
     */
    public function destroy(Consumidor $consumidor): RedirectResponse
    {
        $temHistorico = $consumidor->leituras()->exists();

        if ($temHistorico) {
            return redirect()
                ->route('consumidores.index')
                ->with('error', "Não é possível remover \"{$consumidor->nome}\": este consumidor possui leituras e faturas registradas. O histórico financeiro deve ser preservado.");
        }

        $nome = $consumidor->nome;
        $consumidor->delete();

        return redirect()
            ->route('consumidores.index')
            ->with('success', "Consumidor \"{$nome}\" removido com sucesso.");
    }
}
