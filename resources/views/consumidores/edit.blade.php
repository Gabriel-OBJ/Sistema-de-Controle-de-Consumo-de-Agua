@extends('layouts.app')

@section('title', 'Editar Consumidor')
@section('page-title', 'Editar Consumidor')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square text-warning"></i>
                Editar: <strong class="ms-1">{{ $consumidor->nome }}</strong>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('consumidores.update', $consumidor) }}" method="POST" id="form-editar-consumidor">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome completo <span class="text-danger">*</span></label>
                        <input type="text" name="nome" id="nome"
                               class="form-control @error('nome') is-invalid @enderror"
                               value="{{ old('nome', $consumidor->nome) }}" autofocus>
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço <span class="text-danger">*</span></label>
                        <input type="text" name="endereco" id="endereco"
                               class="form-control @error('endereco') is-invalid @enderror"
                               value="{{ old('endereco', $consumidor->endereco) }}">
                        @error('endereco')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="telefone" id="telefone"
                                   class="form-control @error('telefone') is-invalid @enderror"
                                   value="{{ old('telefone', $consumidor->telefone) }}">
                            <div class="form-text">Somente números.</div>
                            @error('telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="numero_medidor" class="form-label">Número do Medidor <span class="text-danger">*</span></label>
                            <input type="text" name="numero_medidor" id="numero_medidor"
                                   class="form-control @error('numero_medidor') is-invalid @enderror"
                                   value="{{ old('numero_medidor', $consumidor->numero_medidor) }}">
                            @error('numero_medidor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('consumidores.index') }}" class="btn btn-outline-secondary" id="btn-cancelar">
                            <i class="bi bi-x-lg me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning" id="btn-atualizar-consumidor">
                            <i class="bi bi-check-lg me-1"></i> Atualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
