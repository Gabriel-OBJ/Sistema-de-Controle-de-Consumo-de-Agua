<?php

namespace App\Http\Controllers;

use App\Models\Consumidor;
use App\Models\Leitura;
use App\Services\CobrancaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LeituraController extends Controller
{
    public function __construct(
        private readonly CobrancaService $cobrancaService
    ) {}

    /**
     * Lista as leituras registradas com filtro por mês/ano.
     */
    public function index(Request $request): View
    {
        $mes = $request->integer('mes', now()->month);
        $ano = $request->integer('ano', now()->year);

        $leituras = Leitura::with(['consumidor', 'fatura'])
            ->where('mes_referencia', $mes)
            ->where('ano_referencia', $ano)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('leituras.index', compact('leituras', 'mes', 'ano'));
    }

    /**
     * Exibe o formulário para registrar nova leitura.
     */
    public function create(): View
    {
        $consumidores = Consumidor::orderBy('nome')->get();
        $meses        = $this->listaMeses();
        $anos         = $this->listaAnos();

        return view('leituras.create', compact('consumidores', 'meses', 'anos'));
    }

    /**
     * Valida e persiste a leitura, calcula o consumo e gera a fatura automaticamente.
     *
     * Regras de negócio aplicadas:
     *   1. leitura_atual >= leitura_anterior (não pode regredir)
     *   2. Apenas uma leitura por consumidor/mês/ano
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'consumidor_id'  => 'required|exists:consumidores,id',
            'mes_referencia' => 'required|integer|min:1|max:12',
            'ano_referencia' => 'required|integer|min:2000|max:2100',
            'leitura_atual'  => 'required|numeric|min:0',
        ], [
            'consumidor_id.required'  => 'Selecione um consumidor.',
            'consumidor_id.exists'    => 'Consumidor inválido.',
            'mes_referencia.required' => 'Selecione o mês de referência.',
            'ano_referencia.required' => 'Informe o ano de referência.',
            'leitura_atual.required'  => 'A leitura atual é obrigatória.',
            'leitura_atual.numeric'   => 'A leitura atual deve ser um número.',
            'leitura_atual.min'       => 'A leitura atual não pode ser negativa.',
        ]);

        $consumidor = Consumidor::findOrFail($validated['consumidor_id']);

        // ── Regra: mês único por consumidor ─────────────────────────────────
        $leituraExistente = Leitura::where('consumidor_id', $consumidor->id)
            ->where('mes_referencia', $validated['mes_referencia'])
            ->where('ano_referencia', $validated['ano_referencia'])
            ->exists();

        if ($leituraExistente) {
            throw ValidationException::withMessages([
                'mes_referencia' => "Já existe uma leitura registrada para {$consumidor->nome} neste mês/ano.",
            ]);
        }

        // ── Busca a leitura anterior (último mês registrado) ─────────────────
        $ultimaLeitura = Leitura::where('consumidor_id', $consumidor->id)
            ->orderByDesc('ano_referencia')
            ->orderByDesc('mes_referencia')
            ->first();

        $leituraAnterior = $ultimaLeitura ? (float) $ultimaLeitura->leitura_atual : 0.0;

        // ── Regra: leitura atual >= anterior ────────────────────────────────
        if ((float) $validated['leitura_atual'] < $leituraAnterior) {
            throw ValidationException::withMessages([
                'leitura_atual' => "A leitura atual ({$validated['leitura_atual']} m³) não pode ser menor que a anterior ({$leituraAnterior} m³).",
            ]);
        }

        // ── Cálculo do consumo ───────────────────────────────────────────────
        $consumo = round((float) $validated['leitura_atual'] - $leituraAnterior, 3);

        // ── Persistência da leitura ──────────────────────────────────────────
        $leitura = Leitura::create([
            'consumidor_id'  => $consumidor->id,
            'mes_referencia' => $validated['mes_referencia'],
            'ano_referencia' => $validated['ano_referencia'],
            'leitura_anterior' => $leituraAnterior,
            'leitura_atual'    => $validated['leitura_atual'],
            'consumo_m3'       => $consumo,
        ]);

        // ── Geração automática da fatura ─────────────────────────────────────
        $this->cobrancaService->gerarFatura($leitura);

        return redirect()
            ->route('faturas.index', [
                'mes' => $validated['mes_referencia'],
                'ano' => $validated['ano_referencia'],
            ])
            ->with('success', "Leitura de {$consumidor->nome} registrada! Fatura gerada automaticamente.");
    }

    // ── Helpers privados ──────────────────────────────────────────────────────

    private function listaMeses(): array
    {
        return [
            1  => 'Janeiro',
            2  => 'Fevereiro',
            3  => 'Março',
            4  => 'Abril',
            5  => 'Maio',
            6  => 'Junho',
            7  => 'Julho',
            8  => 'Agosto',
            9  => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];
    }

    private function listaAnos(): array
    {
        $anoAtual = (int) now()->year;

        return range($anoAtual - 2, $anoAtual + 1);
    }
}
