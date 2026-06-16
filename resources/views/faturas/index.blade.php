@extends('layouts.app')

@section('title', 'Faturas')
@section('page-title', 'Faturas do Mês')

@section('content')

{{-- Filtro de mês/ano --}}
<form method="GET" action="{{ route('faturas.index') }}" class="card mb-4" id="form-filtro-faturas">
    <div class="card-body py-3">
        <div class="row align-items-end g-2">
            <div class="col-auto">
                <label class="form-label mb-1 small fw-500">Mês</label>
                <select name="mes" class="form-select form-select-sm" id="filtro-mes-faturas">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-1 small fw-500">Ano</label>
                <select name="ano" class="form-select form-select-sm" id="filtro-ano-faturas">
                    @foreach(range(now()->year - 2, now()->year + 1) as $a)
                        <option value="{{ $a }}" {{ $ano == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-agua" id="btn-filtrar-faturas">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
            </div>
        </div>
    </div>
</form>

{{-- Cards de totais --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label"><i class="bi bi-receipt me-1"></i>Total de Faturas</div>
            <div class="stat-value">{{ $faturas->count() }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card warning">
            <div class="stat-label"><i class="bi bi-hourglass-split me-1"></i>Total Pendente</div>
            <div class="stat-value">R$ {{ number_format($totalPendente, 2, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card success">
            <div class="stat-label"><i class="bi bi-check-circle me-1"></i>Total Pago</div>
            <div class="stat-value">R$ {{ number_format($totalPago, 2, ',', '.') }}</div>
        </div>
    </div>
</div>

{{-- Tabela de faturas --}}
<div class="card">
    <div class="card-header">
        Faturas —
        @php
            $meses = [1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
                      7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'];
        @endphp
        {{ $meses[$mes] ?? '' }} / {{ $ano }}
    </div>
    <div class="card-body p-0">
        @if($faturas->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-receipt display-4 d-block mb-3 opacity-25"></i>
                <p>Nenhuma fatura encontrada para este período.</p>
                <a href="{{ route('leituras.create') }}" class="btn btn-agua btn-sm">Registrar leitura</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tabela-faturas">
                    <thead>
                        <tr>
                            <th class="ps-4">Consumidor</th>
                            <th>Medidor</th>
                            <th>Leitura Ant. (m³)</th>
                            <th>Leitura Atual (m³)</th>
                            <th>Consumo</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($faturas as $fatura)
                        @php
                            $leitura    = $fatura->leitura;
                            $consumidor = $fatura->consumidor;
                            $consumoL   = $leitura->consumo_m3 * 1000;

                            // Mensagem WhatsApp pré-preenchida
                            $mensagem = "Olá, {$consumidor->nome}! 👋\n\n"
                                . "📅 *Referência:* {$leitura->nome_mes} / {$leitura->ano_referencia}\n"
                                . "📊 *Leitura anterior:* " . number_format($leitura->leitura_anterior, 3, ',', '.') . " m³\n"
                                . "📊 *Leitura atual:* "    . number_format($leitura->leitura_atual,    3, ',', '.') . " m³\n"
                                . "💧 *Consumo:* " . number_format($leitura->consumo_m3, 3, ',', '.') . " m³ (" . number_format($consumoL, 0, ',', '.') . " litros)\n"
                                . "💰 *Valor da fatura:* R$ " . number_format($fatura->valor_total, 2, ',', '.') . "\n\n"
                                . "Para pagamento ou dúvidas, entre em contato conosco. Obrigado! 🙏";

                            $telefone  = preg_replace('/\D/', '', $consumidor->telefone);
                            $whatsappUrl = 'https://wa.me/55' . $telefone . '?text=' . rawurlencode($mensagem);
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-500">{{ $consumidor->nome }}</div>
                                <small class="text-muted">{{ $consumidor->endereco }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $consumidor->numero_medidor }}</span></td>
                            <td>{{ number_format($leitura->leitura_anterior, 3, ',', '.') }}</td>
                            <td>{{ number_format($leitura->leitura_atual, 3, ',', '.') }}</td>
                            <td>
                                <span class="text-primary fw-500">{{ number_format($leitura->consumo_m3, 3, ',', '.') }} m³</span><br>
                                <small class="text-muted">{{ number_format($consumoL, 0, ',', '.') }} L</small>
                            </td>
                            <td class="fw-600 text-dark">
                                R$ {{ number_format($fatura->valor_total, 2, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge-{{ $fatura->status }}">
                                    {{ $fatura->status === 'pago' ? '✅ Pago' : '⏳ Pendente' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex gap-1 justify-content-end">
                                    {{-- Botão WhatsApp --}}
                                    <a href="{{ $whatsappUrl }}"
                                       target="_blank"
                                       class="btn btn-sm btn-success"
                                       title="Enviar fatura via WhatsApp"
                                       id="btn-whatsapp-{{ $fatura->id }}">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>

                                    {{-- Botão Marcar como Pago --}}
                                    @if($fatura->isPendente())
                                    <form action="{{ route('faturas.pagar', $fatura) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-success"
                                                title="Marcar como paga"
                                                id="btn-pagar-{{ $fatura->id }}"
                                                onclick="return confirm('Confirmar pagamento de {{ addslashes($consumidor->nome) }}?')">
                                            <i class="bi bi-check-lg"></i> Pago
                                        </button>
                                    </form>
                                    @else
                                    <span class="btn btn-sm btn-outline-secondary disabled">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
