@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="section-kicker mb-2">Minhas reservas</div>
            <h2 class="fw-bold mb-1">Reservas ativas</h2>
            <p class="text-muted mb-0">Se n&atilde;o for usar uma data espec&iacute;fica, cancele somente aquela reserva.</p>
        </div>
        <a href="{{ route('professor.solicitar') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Nova reserva
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="d-grid gap-3">
        @forelse($reservas as $reserva)
            <div class="page-card p-3">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-xl-4">
                        <div class="small text-muted">Sala</div>
                        <div class="fw-bold">
                            {{ $reserva->sala->nome ?? 'Indispon&iacute;vel' }}
                            @if($reserva->recorrente)
                                <span class="badge text-bg-info ms-2">Recorrente</span>
                            @endif
                        </div>
                        @if($reserva->comentario_professor)
                            <div class="tech-panel p-2 mt-2 small">{{ $reserva->comentario_professor }}</div>
                        @endif
                        @if($reserva->comentario_adm)
                            <div class="tech-panel p-2 mt-2 small text-primary">{{ $reserva->comentario_adm }}</div>
                        @endif
                    </div>

                    <div class="col-6 col-xl-2">
                        <div class="small text-muted">Data</div>
                        <div class="fw-bold">{{ date('d/m/Y', strtotime($reserva->data_reserva)) }}</div>
                    </div>

                    <div class="col-6 col-xl-2">
                        <div class="small text-muted">Per&iacute;odo</div>
                        <div class="data-chip mt-1"><i class="fas fa-clock"></i>{{ $reserva->periodo }}</div>
                    </div>

                    <div class="col-6 col-xl-2">
                        <div class="small text-muted">Status</div>
                        @if($reserva->status == 'aprovada')
                            <span class="badge text-bg-success">Aprovada</span>
                        @elseif($reserva->status == 'em_analise')
                            <span class="badge text-bg-info">Em an&aacute;lise</span>
                        @elseif(in_array($reserva->status, ['recusada', 'rejeitada', 'cancelada']))
                            <span class="badge text-bg-danger">{{ ucfirst($reserva->status) }}</span>
                        @else
                            <span class="badge text-bg-warning">Pendente</span>
                        @endif
                    </div>

                    <div class="col-6 col-xl-2 text-xl-end">
                        <form action="{{ route('professor.desistir', $reserva->id) }}" method="POST" onsubmit="return confirm('Cancelar esta reserva especifica?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-ban me-1"></i>Cancelar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="page-card text-center py-5">
                <i class="fas fa-calendar-check fa-2x text-primary mb-3"></i>
                <h5 class="fw-bold">Nenhuma reserva encontrada</h5>
                <p class="text-muted mb-0">Suas solicita&ccedil;&otilde;es aparecer&atilde;o aqui.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
