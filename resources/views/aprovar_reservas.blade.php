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

    @forelse($reservas as $reserva)
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="fw-bold">{{ $reserva->user->name }}</div>
                        <small class="text-muted">{{ $reserva->user->email }}</small>
                    </div>
                    <div class="col-md-2"><strong>{{ $reserva->sala->nome }}</strong></div>
                    <div class="col-md-2">{{ date('d/m/Y', strtotime($reserva->data_reserva)) }}</div>
                    <div class="col-md-2 text-muted">{{ $reserva->periodo }}</div>
                    <div class="col-md-3 text-end">
                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$reserva->id}}">Ver Detalhes/Responder</button>
                    </div>
                </div>
                
                <div class="collapse mt-3" id="collapse{{$reserva->id}}">
                    <div class="bg-light p-3 rounded">
                        <p><strong>Comentário do Professor:</strong> {{ $reserva->comentario_professor ?? 'Nenhum' }}</p>
                        <form action="{{ route('reserva.mudarStatus', $reserva->id) }}" method="POST">
                            @csrf
                            <textarea name="comentario_adm" class="form-control mb-2" placeholder="Sua resposta ao professor (opcional)..."></textarea>
                            <div class="d-flex gap-2">
                                <button type="submit" name="status" value="aprovada" class="btn btn-success fw-bold">APROVAR</button>
                                <button type="submit" name="status" value="rejeitada" class="btn btn-danger fw-bold">RECUSAR</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5 text-muted">Não há solicitações pendentes.</div>
    @endforelse
</div>
@endsection