<?php

namespace App\Http\Controllers;

use App\Models\Fatura;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaturaController extends Controller
{
    /**
     * Lista as faturas com filtro opcional por mês e ano.
     * Por padrão exibe as faturas do mês e ano correntes.
     */
    public function index(Request $request): View
    {
        $mes = $request->integer('mes', now()->month);
        $ano = $request->integer('ano', now()->year);

        $faturas = Fatura::with(['consumidor', 'leitura'])
            ->whereHas('leitura', fn ($q) => $q
                ->where('mes_referencia', $mes)
                ->where('ano_referencia', $ano)
            )
            ->orderBy('status') // pendente primeiro
            ->orderBy('created_at', 'desc')
            ->get();

        // Totais para o cabeçalho do painel
        $totalPendente = $faturas->where('status', 'pendente')->sum('valor_total');
        $totalPago     = $faturas->where('status', 'pago')->sum('valor_total');

        return view('faturas.index', compact('faturas', 'mes', 'ano', 'totalPendente', 'totalPago'));
    }

    /**
     * Marca uma fatura como paga.
     */
    public function pagar(Fatura $fatura): RedirectResponse
    {
        if ($fatura->isPaga()) {
            return back()->with('info', 'Esta fatura já está marcada como paga.');
        }

        $fatura->update(['status' => 'pago']);

        return back()->with('success', "Fatura de {$fatura->consumidor->nome} marcada como paga!");
    }
}
