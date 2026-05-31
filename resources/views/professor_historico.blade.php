@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex align-items-center mb-4">
        <div class="bg-white p-3 rounded shadow-sm me-3" style="color: #7010a8;">
            <i class="fas fa-history fa-2x"></i>
        </div>
        <div>
            <h2 class="fw-bold m-0">Histórico de Reservas</h2>
            <p class="text-muted m-0">Consulte suas solicitações anteriores e status.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th class="ps-4 py-3">Sala</th>
                        <th class="py-3">Data</th>
                        <th class="py-3">Período</th>
                        <th class="py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservas as $reserva)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $reserva->sala->nome ?? 'Sala Removida' }}</td>
                        <td>{{ date('d/m/Y', strtotime($reserva->data_reserva)) }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $reserva->periodo }}</span></td>
                        <td class="text-center">
                            @if($reserva->status == 'aprovada')
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Aprovada</span>
                            @elseif($reserva->status == 'rejeitada')
                                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">Rejeitada</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">Pendente</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-5 text-muted">Nenhum histórico encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection