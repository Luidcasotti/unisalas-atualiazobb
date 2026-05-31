@extends('layouts.app')
@section('content')
<div class="container-fluid p-4">
    <h2 class="fw-bold mb-4" style="color: #7010a8;">Minhas Reservas</h2>
    
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>Sala</th>
                    <th>Data</th>
                    <th>Período</th>
                    <th>Status</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservas as $reserva)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $reserva->sala->nome ?? 'Indisponível' }}</div>
                        
                        {{-- Exibe o pedido original do professor --}}
                        @if($reserva->comentario_professor)
                            <div class="mt-2 p-2 rounded" style="background-color: #f8f9fa; border-left: 4px solid #6c757d;">
                                <small class="fw-bold text-uppercase text-muted" style="font-size: 0.7rem;">Seu Pedido:</small>
                                <p class="mb-0 small text-secondary">{{ $reserva->comentario_professor }}</p>
                            </div>
                        @endif

                        {{-- Exibe a resposta do ADM --}}
                        @if($reserva->comentario_adm)
                            <div class="mt-2 p-2 rounded" style="background-color: #f1e9f7; border-left: 4px solid #7010a8;">
                                <small class="fw-bold text-uppercase" style="color: #7010a8; font-size: 0.7rem;">Resposta do ADM:</small>
                                <p class="mb-0 small text-dark">{{ $reserva->comentario_adm }}</p>
                            </div>
                        @endif
                    </td>
                    <td>{{ date('d/m/Y', strtotime($reserva->data_reserva)) }}</td>
                    <td>{{ $reserva->periodo }}</td>
                    <td>
                        <span class="badge {{ $reserva->status == 'aprovada' ? 'bg-success' : ($reserva->status == 'recusada' ? 'bg-danger' : 'bg-warning') }}">
                            {{ ucfirst($reserva->status) }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('professor.desistir', $reserva->id) }}" method="POST" onsubmit="return confirm('Desistir desta reserva?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Desistir</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection