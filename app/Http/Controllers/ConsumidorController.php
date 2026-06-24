<?php

namespace App\Http\Controllers;

use App\Models\Consumidor;
use App\Models\LogAcesso;
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
        LogAcesso::create([
            'user_id' => auth()->id(),
            'consumidor_id' => null, // null for listing all
            'acao' => 'Visualizou listagem de consumidores',
            'ip_address' => request()->ip(),
        ]);

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
            'nome' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'numero_medidor' => 'required|string|max:50|unique:consumidores,numero_medidor',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'endereco.required' => 'O endereço é obrigatório.',
            'telefone.required' => 'O telefone é obrigatório.',
            'numero_medidor.required' => 'O número do medidor é obrigatório.',
            'numero_medidor.unique' => 'Este número de medidor já está cadastrado.',
        ]);

        Consumidor::create($validated);

        return redirect()
            ->route('consumidores.index')
            ->with('success', 'Consumidor cadastrado com sucesso!');
    }

    /**
     * Exibe os detalhes de um consumidor específico.
     */
    public function show(Consumidor $consumidor): View
    {
        LogAcesso::create([
            'user_id' => auth()->id(),
            'consumidor_id' => $consumidor->id,
            'acao' => 'Visualizou detalhes do consumidor',
            'ip_address' => request()->ip(),
        ]);

        $consumidor->load(['leituras' => fn($q) => $q->orderByDesc('ano_referencia')->orderByDesc('mes_referencia')]);

        return view('consumidores.show', compact('consumidor'));
    }

    /**
     * Exibe o formulário de edição de um consumidor.
     */
    public function edit(Consumidor $consumidor): View
    {
        LogAcesso::create([
            'user_id' => auth()->id(),
            'consumidor_id' => $consumidor->id,
            'acao' => 'Acessou formulário de edição do consumidor',
            'ip_address' => request()->ip(),
        ]);

        return view('consumidores.edit', compact('consumidor'));
    }

    /**
     * Atualiza os dados de um consumidor no banco de dados.
     */
    public function update(Request $request, Consumidor $consumidor): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'numero_medidor' => 'required|string|max:50|unique:consumidores,numero_medidor,' . $consumidor->id,
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'endereco.required' => 'O endereço é obrigatório.',
            'telefone.required' => 'O telefone é obrigatório.',
            'numero_medidor.required' => 'O número do medidor é obrigatório.',
            'numero_medidor.unique' => 'Este número de medidor já está cadastrado.',
        ]);

        $consumidor->update($validated);

        return redirect()
            ->route('consumidores.index')
            ->with('success', 'Consumidor atualizado com sucesso!');
    }

    /**
     * Remove um consumidor do banco de dados.
     */
    public function destroy(Consumidor $consumidor): RedirectResponse
    {
        $consumidor->delete();

        return redirect()
            ->route('consumidores.index')
            ->with('success', 'Consumidor removido com sucesso!');
    }
}
