@extends('layouts.app')

@section('title', 'Consumidores')
@section('page-title', 'Consumidores Cadastrados')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Total: <strong>{{ $consumidores->total() }}</strong> consumidor(es)</p>
    <a href="{{ route('consumidores.create') }}" class="btn btn-agua" id="btn-novo-consumidor">
        <i class="bi bi-plus-lg me-1"></i> Novo Consumidor
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($consumidores->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people display-4 d-block mb-3 opacity-25"></i>
                <p class="mb-2">Nenhum consumidor cadastrado ainda.</p>
                <a href="{{ route('consumidores.create') }}" class="btn btn-agua btn-sm">Cadastrar o primeiro</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tabela-consumidores">
                    <thead>
                        <tr>
                            <th class="ps-4">Nome</th>
                            <th>Endereço</th>
                            <th>Telefone</th>
                            <th>Nº Medidor</th>
                            <th class="text-end pe-4">Remover</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consumidores as $consumidor)
                        <tr>
                            <td class="ps-4 fw-500">{{ $consumidor->nome }}</td>
                            <td class="text-muted">{{ $consumidor->endereco }}</td>
                            <td>
                                <a href="tel:{{ $consumidor->telefone }}" class="text-decoration-none">
                                    <i class="bi bi-telephone me-1 text-muted"></i>{{ $consumidor->telefone }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-500">
                                    <i class="bi bi-speedometer2 me-1"></i>{{ $consumidor->numero_medidor }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if($consumidor->leituras()->exists())
                                    {{-- Consumidor com histórico: remoção bloqueada --}}
                                    <button class="btn btn-sm btn-outline-secondary"
                                            disabled
                                            title="Não é possível remover: consumidor possui histórico de leituras">
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                @else
                                    {{-- Consumidor sem histórico: pode ser removido --}}
                                    <form action="{{ route('consumidores.destroy', $consumidor) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Remover {{ addslashes($consumidor->nome) }}?\n\nEsta ação não pode ser desfeita.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                id="btn-remover-{{ $consumidor->id }}"
                                                title="Remover consumidor">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3">
                {{ $consumidores->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
