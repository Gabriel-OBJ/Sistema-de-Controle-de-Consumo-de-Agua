@extends('layouts.app')

@section('title', 'Registrar Leitura')
@section('page-title', 'Registrar Nova Leitura')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">

        @if($errors->any())
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-4">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
            <div>
                <strong>Corrija os erros abaixo:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-speedometer2 text-primary"></i>
                Dados da Leitura
            </div>
            <div class="card-body p-4">
                <form action="{{ route('leituras.store') }}" method="POST" id="form-leitura">
                    @csrf

                    {{-- Consumidor --}}
                    <div class="mb-3">
                        <label for="consumidor_id" class="form-label">
                            Consumidor <span class="text-danger">*</span>
                        </label>
                        <select name="consumidor_id" id="consumidor_id"
                                class="form-select @error('consumidor_id') is-invalid @enderror">
                            <option value="">— Selecione o consumidor —</option>
                            @foreach($consumidores as $consumidor)
                                <option value="{{ $consumidor->id }}"
                                    {{ old('consumidor_id') == $consumidor->id ? 'selected' : '' }}>
                                    {{ $consumidor->nome }} — Medidor: {{ $consumidor->numero_medidor }}
                                </option>
                            @endforeach
                        </select>
                        @error('consumidor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mês e Ano --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mes_referencia" class="form-label">
                                Mês de Referência <span class="text-danger">*</span>
                            </label>
                            <select name="mes_referencia" id="mes_referencia"
                                    class="form-select @error('mes_referencia') is-invalid @enderror">
                                @foreach($meses as $num => $nome)
                                    <option value="{{ $num }}"
                                        {{ old('mes_referencia', now()->month) == $num ? 'selected' : '' }}>
                                        {{ $nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mes_referencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="ano_referencia" class="form-label">
                                Ano <span class="text-danger">*</span>
                            </label>
                            <select name="ano_referencia" id="ano_referencia"
                                    class="form-select @error('ano_referencia') is-invalid @enderror">
                                @foreach($anos as $ano)
                                    <option value="{{ $ano }}"
                                        {{ old('ano_referencia', now()->year) == $ano ? 'selected' : '' }}>
                                        {{ $ano }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ano_referencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Leitura Atual --}}
                    <div class="mb-3">
                        <label for="leitura_atual" class="form-label">
                            Leitura Atual (m³) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="leitura_atual" id="leitura_atual"
                                   class="form-control @error('leitura_atual') is-invalid @enderror"
                                   value="{{ old('leitura_atual') }}"
                                   step="0.001" min="0"
                                   placeholder="Ex: 1250.500">
                            <span class="input-group-text">m³</span>
                            @error('leitura_atual')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            A leitura anterior será preenchida automaticamente pelo sistema com base no último registro.
                        </div>
                    </div>

                    {{-- Info das regras --}}
                    <div class="alert alert-light border d-flex gap-2 align-items-start">
                        <i class="bi bi-shield-check text-success mt-1"></i>
                        <div class="small text-muted">
                            <strong>Validações automáticas:</strong><br>
                            • A leitura atual não pode ser menor que a anterior.<br>
                            • Apenas uma leitura por consumidor por mês/ano.<br>
                            • A fatura será gerada automaticamente após o registro.
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('leituras.index') }}" class="btn btn-outline-secondary" id="btn-cancelar-leitura">
                            <i class="bi bi-x-lg me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-agua" id="btn-registrar-leitura">
                            <i class="bi bi-check-lg me-1"></i> Registrar e Gerar Fatura
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
