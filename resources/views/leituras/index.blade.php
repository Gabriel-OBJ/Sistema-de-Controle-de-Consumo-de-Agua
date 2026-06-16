@extends('layouts.app')

@section('title', 'Leituras')
@section('page-title', 'Leituras Registradas')

@section('content')
{{-- Filtro de mês/ano --}}
<form method="GET" action="{{ route('leituras.index') }}" class="card mb-4" id="form-filtro-leituras">
    <div class="card-body py-3">
        <div class="row align-items-end g-2">
            <div class="col-auto">
                <label class="form-label mb-1 small fw-500">Mês</label>
                <select name="mes" class="form-select form-select-sm" id="filtro-mes">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-1 small fw-500">Ano</label>
                <select name="ano" class="form-select form-select-sm" id="filtro-ano">
                    @foreach(range(now()->year - 2, now()->year + 1) as $a)
                        <option value="{{ $a }}" {{ $ano == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-agua" id="btn-filtrar">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
            </div>
            <div class="col text-end">
                <a href="{{ route('leituras.create') }}" class="btn btn-sm btn-agua" id="btn-nova-leitura-lista">
                    <i class="bi bi-plus-lg me-1"></i>Nova Leitura
                </a>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        @if($leituras->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-speedometer2 display-4 d-block mb-3 opacity-25"></i>
                <p>Nenhuma leitura encontrada para este período.</p>
                <a href="{{ route('leituras.create') }}" class="btn btn-agua btn-sm">Registrar leitura</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tabela-leituras">
                    <thead>
                        <tr>
                            <th class="ps-4">Consumidor</th>
                            <th>Anterior (m³)</th>
                            <th>Atual (m³)</th>
                            <th>Consumo (m³)</th>
                            <th>Consumo (L)</th>
                            <th>Fatura</th>
                            <th class="text-end pe-4">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leituras as $leitura)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-500">{{ $leitura->consumidor->nome }}</div>
                                <small class="text-muted">Medidor: {{ $leitura->consumidor->numero_medidor }}</small>
                            </td>
                            <td>{{ number_format($leitura->leitura_anterior, 3, ',', '.') }}</td>
                            <td>{{ number_format($leitura->leitura_atual, 3, ',', '.') }}</td>
                            <td class="fw-500 text-primary">{{ number_format($leitura->consumo_m3, 3, ',', '.') }}</td>
                            <td class="text-muted">{{ number_format($leitura->consumo_m3 * 1000, 0, ',', '.') }} L</td>
                            <td>
                                @if($leitura->fatura)
                                    <span class="badge-{{ $leitura->fatura->status }}">
                                        R$ {{ number_format($leitura->fatura->valor_total, 2, ',', '.') }}
                                        — {{ ucfirst($leitura->fatura->status) }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 text-muted small">
                                {{ $leitura->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3">
                {{ $leituras->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
