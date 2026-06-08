@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="section-kicker mb-2">Painel do professor</div>
            <h2 class="fw-bold mb-1">In&iacute;cio</h2>
            <p class="text-muted mb-0">Acompanhe suas reservas, status e avisos recentes.</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('professor.solicitar') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Solicitar reserva
            </a>
            <a href="{{ route('mensagens.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-comments me-2"></i>Mensagens
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4 professor-metrics">
        <div class="col-6 col-md-6 col-xl-3">
            <div class="metric-card professor-metric p-3 h-100">
                <div class="text-muted small">Total</div>
                <div class="display-6 fw-bold">{{ $total }}</div>
            </div>
        </div>
        <div class="col-6 col-md-6 col-xl-3">
            <div class="metric-card professor-metric p-3 h-100">
                <div class="text-muted small">Aprovadas</div>
                <div class="display-6 fw-bold text-success">{{ $aprovadas }}</div>
            </div>
        </div>
        <div class="col-6 col-md-6 col-xl-3">
            <div class="metric-card professor-metric p-3 h-100">
                <div class="text-muted small">Pendentes</div>
                <div class="display-6 fw-bold text-warning">{{ $pendentes }}</div>
            </div>
        </div>
        <div class="col-6 col-md-6 col-xl-3">
            <div class="metric-card professor-metric p-3 h-100">
                <div class="text-muted small">Recusadas/Canceladas</div>
                <div class="display-6 fw-bold text-danger">{{ $recusadas }}</div>
            </div>
        </div>
    </div>

    @if($lembrete)
        <div class="alert alert-success border-0 shadow-sm">
            <strong>Lembrete:</strong> voc&ecirc; tem uma reserva confirmada para hoje:
            {{ $lembrete->sala->bloco->nome ?? 'Bloco n&atilde;o informado' }} -
            {{ $lembrete->sala->nome ?? 'Sala' }},
            turno {{ $lembrete->periodo ?? 'n&atilde;o informado' }}.
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12">
            <div class="page-card p-4 h-100">
                <h5 class="fw-bold mb-3">Avisos recentes</h5>
                @forelse($avisos as $aviso)
                    <div class="tech-panel p-3 mb-2">
                        <strong>{{ $aviso->titulo }}</strong>
                        <p class="text-muted mb-0">{{ $aviso->mensagem }}</p>
                    </div>
                @empty
                    <p class="text-muted mb-0">Nenhum aviso novo nas &uacute;ltimas 24 horas.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
