@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold" style="color: #7010a8;"><i class="fas fa-tasks me-2"></i> Solicitações Pendentes</h2>
        <p class="text-muted">Analise e gerencie os pedidos de reserva de salas.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover m-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">Professor</th>
                        <th>Sala</th>
                        <th>Data</th>
                        <th>Período</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservas as $reserva)
                        <tr>
                            <td class="px-4 align-middle">
                                <div class="fw-bold">{{ $reserva->user->name ?? 'Usuário' }}</div>
                                <small class="text-muted">{{ $reserva->user->email ?? '' }}</small>
                            </td>
                            <td class="align-middle">{{ $reserva->sala->nome ?? 'N/A' }}</td>
                            <td class="align-middle">{{ date('d/m/Y', strtotime($reserva->data_reserva)) }}</td>
                            <td class="align-middle">{{ $reserva->periodo }}</td>
                            <td class="text-center align-middle">
                                <form action="{{ route('admin.reserva.status', [$reserva->id, 'aprovada']) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success fw-bold px-3 me-1">APROVAR</button>
                                </form>
                                <form action="{{ route('admin.reserva.status', [$reserva->id, 'recusada']) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger fw-bold px-3">RECUSAR</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Não há solicitações aguardando aprovação.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection