@extends('layouts.app')

@section('title', 'Configuração de Taxa')
@section('page-title', 'Configuração de Taxa de Cobrança')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">

        {{-- Card informativo com a taxa atual --}}
        <div class="card mb-4 border-0" style="background: linear-gradient(135deg, #0077b6, #00b4d8); color: #fff; border-radius: 12px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <i class="bi bi-currency-dollar" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    <div>
                        <div style="font-size: 0.8rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.1em;">Taxa Vigente</div>
                        <div style="font-size: 0.85rem; opacity: 0.7;">Configuração atual do sistema</div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div style="background: rgba(255,255,255,0.15); border-radius: 8px; padding: 0.75rem;">
                            <div style="font-size: 0.75rem; opacity: 0.8;">Taxa Fixa (até 10 m³)</div>
                            <div style="font-size: 1.5rem; font-weight: 700;">
                                R$ {{ number_format($configuracao->taxa_fixa ?? 25, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background: rgba(255,255,255,0.15); border-radius: 8px; padding: 0.75rem;">
                            <div style="font-size: 0.75rem; opacity: 0.8;">Por m³ Excedente</div>
                            <div style="font-size: 1.5rem; font-weight: 700;">
                                R$ {{ number_format($configuracao->valor_excedente ?? 2, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulário de atualização --}}
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-gear-fill text-primary"></i>
                Alterar Configuração
            </div>
            <div class="card-body p-4">
                <form action="{{ route('configuracao.store') }}" method="POST" id="form-configuracao">
                    @csrf

                    <div class="mb-3">
                        <label for="taxa_fixa" class="form-label">
                            Taxa Fixa Mensal (R$) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" name="taxa_fixa" id="taxa_fixa"
                                   class="form-control @error('taxa_fixa') is-invalid @enderror"
                                   value="{{ old('taxa_fixa', $configuracao->taxa_fixa ?? 25) }}"
                                   step="0.01" min="0" placeholder="25.00">
                            @error('taxa_fixa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Cobrado para qualquer consumo até 10.000 litros (10 m³).</div>
                    </div>

                    <div class="mb-3">
                        <label for="valor_excedente" class="form-label">
                            Valor por m³ Excedente (R$) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" name="valor_excedente" id="valor_excedente"
                                   class="form-control @error('valor_excedente') is-invalid @enderror"
                                   value="{{ old('valor_excedente', $configuracao->valor_excedente ?? 2) }}"
                                   step="0.01" min="0" placeholder="2.00">
                            <span class="input-group-text">/m³</span>
                            @error('valor_excedente')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Cobrado por cada m³ consumido acima de 10 m³.</div>
                    </div>

                    {{-- Exemplo dinâmico --}}
                    <div class="alert alert-light border p-3 mb-3" id="exemplo-calculo">
                        <strong class="small">📌 Exemplo com 15 m³ consumidos:</strong><br>
                        <span class="small text-muted">
                            R$ <span id="ex-fixa">{{ number_format($configuracao->taxa_fixa ?? 25, 2, ',', '.') }}</span> (fixa)
                            + R$ <span id="ex-excedente">{{ number_format(($configuracao->valor_excedente ?? 2) * 5, 2, ',', '.') }}</span>
                            (5 m³ × R$ <span id="ex-por-m3">{{ number_format($configuracao->valor_excedente ?? 2, 2, ',', '.') }}</span>)
                            = <strong>R$ <span id="ex-total">{{ number_format(($configuracao->taxa_fixa ?? 25) + ($configuracao->valor_excedente ?? 2) * 5, 2, ',', '.') }}</span></strong>
                        </span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-agua" id="btn-salvar-configuracao">
                            <i class="bi bi-check-lg me-1"></i> Salvar Nova Configuração
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Atualiza o exemplo de cálculo dinamicamente ao alterar os campos
    const taxaInput      = document.getElementById('taxa_fixa');
    const excedenteInput = document.getElementById('valor_excedente');

    function atualizarExemplo() {
        const taxa      = parseFloat(taxaInput.value) || 0;
        const excedente = parseFloat(excedenteInput.value) || 0;
        const total     = taxa + (excedente * 5);

        document.getElementById('ex-fixa').textContent      = taxa.toFixed(2).replace('.', ',');
        document.getElementById('ex-excedente').textContent = (excedente * 5).toFixed(2).replace('.', ',');
        document.getElementById('ex-por-m3').textContent    = excedente.toFixed(2).replace('.', ',');
        document.getElementById('ex-total').textContent     = total.toFixed(2).replace('.', ',');
    }

    taxaInput.addEventListener('input', atualizarExemplo);
    excedenteInput.addEventListener('input', atualizarExemplo);
</script>
@endpush
