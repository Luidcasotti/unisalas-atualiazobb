@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <span class="data-chip mb-2"><i class="fas fa-chart-line"></i>In&iacute;cio</span>
            <h2 class="fw-bold mb-1">In&iacute;cio do UniSalas</h2>
            <p class="text-muted mb-0">Acompanhe reservas, usu&aacute;rios, salas e avisos do sistema.</p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.reservas') }}" class="btn btn-primary">
                <i class="fas fa-check-circle me-2"></i>Aprovar reservas
            </a>
            <a href="{{ route('mensagens.index') }}" class="btn btn-outline-primary position-relative">
                <i class="fas fa-comments me-2"></i>Mensagens
                @if(isset($mensagensNaoLidas) && $mensagensNaoLidas > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $mensagensNaoLidas }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="row g-3 mb-4 dashboard-metrics">
        <div class="col-6 col-md-6 col-xl">
            <div class="metric-card dashboard-metric p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Usu&aacute;rios</div>
                        <div class="display-6 fw-bold mb-0">{{ $totalUsuarios }}</div>
                    </div>
                    <span class="metric-icon bg-primary-subtle text-primary"><i class="fas fa-users"></i></span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-6 col-xl">
            <div class="metric-card dashboard-metric p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Salas</div>
                        <div class="display-6 fw-bold mb-0">{{ $totalSalas }}</div>
                    </div>
                    <span class="metric-icon bg-info-subtle text-info"><i class="fas fa-door-open"></i></span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-6 col-xl">
            <div class="metric-card dashboard-metric p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Blocos</div>
                        <div class="display-6 fw-bold mb-0">{{ $totalBlocos }}</div>
                    </div>
                    <span class="metric-icon bg-primary-subtle text-primary"><i class="fas fa-building"></i></span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-6 col-xl">
            <div class="metric-card dashboard-metric p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Pendentes</div>
                        <div class="display-6 fw-bold mb-0">{{ $reservasPendentes }}</div>
                    </div>
                    <span class="metric-icon bg-warning-subtle text-warning"><i class="fas fa-clock"></i></span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-6 col-xl">
            <div class="metric-card dashboard-metric p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Aprovadas</div>
                        <div class="display-6 fw-bold mb-0">{{ $reservasAprovadas }}</div>
                    </div>
                    <span class="metric-icon bg-success-subtle text-success"><i class="fas fa-calendar-check"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-5">
            <div class="page-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">A&ccedil;&otilde;es r&aacute;pidas</h5>
                        <p class="text-muted small mb-0">Atalhos para as rotinas mais usadas.</p>
                    </div>
                    <i class="fas fa-bolt text-warning"></i>
                </div>

                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.usuarios') }}" class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-user-cog me-2 text-primary"></i>Gerenciar usu&aacute;rios</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('admin.blocos') }}" class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-building me-2 text-info"></i>Organizar blocos e salas</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('admin.historico') }}" class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-database me-2 text-secondary"></i>Consultar hist&oacute;rico</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="page-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Publicar aviso geral</h5>
                        <p class="text-muted small mb-0">O aviso aparece para os professores no painel.</p>
                    </div>
                    <i class="fas fa-bullhorn text-primary"></i>
                </div>

                <form action="{{ route('admin.salvarAviso') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">T&iacute;tulo</label>
                        <input type="text" name="titulo" class="form-control" placeholder="Ex.: Manuten&ccedil;&atilde;o no bloco B" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mensagem</label>
                        <textarea name="mensagem" class="form-control" rows="4" placeholder="Escreva o conte&uacute;do do aviso..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-2"></i>Publicar aviso
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="page-card p-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-1">Avisos atuais</h5>
                <p class="text-muted small mb-0">Comunicados publicados recentemente.</p>
            </div>
        </div>

        @forelse($avisos as $aviso)
            <div class="tech-panel p-3 mb-2 d-flex flex-wrap justify-content-between gap-3">
                <div>
                    <strong>{{ $aviso->titulo }}</strong>
                    <p class="text-muted mb-0">{{ $aviso->mensagem }}</p>
                </div>
                <form action="{{ route('admin.excluirAviso', $aviso->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash-alt me-1"></i>Remover
                    </button>
                </form>
            </div>
        @empty
            <div class="text-center text-muted py-4 tech-panel">
                Nenhum aviso publicado.
            </div>
        @endforelse
    </div>
</div>
@endsection
