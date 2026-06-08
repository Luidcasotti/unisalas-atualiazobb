@extends('layouts.app')

@section('content')
@php
    $diasSemana = ['domingo', 'segunda-feira', 'ter&ccedil;a-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 's&aacute;bado'];
    $grupos = $reservas->groupBy(fn($reserva) => $reserva->grupo_recorrencia ?: 'reserva-' . $reserva->id);
@endphp

<style>
    .reservation-detail-row {
        background: #101d2f;
        border: 1px solid var(--line);
    }

    html[data-theme="light"] .reservation-detail-row {
        background: #ffffff;
        color: var(--text);
        border-color: var(--line);
        box-shadow: 0 10px 24px rgba(44, 72, 104, 0.10);
    }
</style>

<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="section-kicker mb-2">Workflow de reservas</div>
            <h2 class="fw-bold mb-1">Aprovar reservas</h2>
            <p class="text-muted mb-0">Solicita&ccedil;&otilde;es normais e recorrentes organizadas para an&aacute;lise.</p>
        </div>
        <span class="data-chip"><i class="fas fa-clock"></i>{{ $reservas->count() }} pendentes</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="d-grid gap-3">
        @forelse($grupos as $grupoId => $grupo)
            @php
                $primeira = $grupo->first();
                $recorrente = $primeira->recorrente;
                $diaSemana = $diasSemana[\Carbon\Carbon::parse($primeira->data_reserva)->dayOfWeek];
                $collapseId = 'grupo' . md5($grupoId);
                $manterAberto = session('open_group') === $collapseId;
            @endphp

            <div class="page-card p-3">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-xl-3">
                        <div class="small text-muted">Professor</div>
                        <div class="fw-bold d-flex flex-wrap align-items-center gap-2">
                            {{ $primeira->user->name }}
                            @if($recorrente)
                                <span class="data-chip"><i class="fas fa-repeat text-primary"></i>Recorrente</span>
                            @endif
                            @if($primeira->comentario_professor)
                                <span class="data-chip" title="Esta solicita&ccedil;&atilde;o possui coment&aacute;rio">
                                    <i class="fas fa-comment-dots text-warning"></i>
                                </span>
                            @endif
                        </div>
                        <div class="text-muted small">{{ $primeira->user->email }}</div>
                    </div>

                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="small text-muted">Bloco</div>
                        <div class="data-chip mt-1"><i class="fas fa-building"></i>{{ $primeira->sala->bloco->nome ?? 'N/A' }}</div>
                    </div>

                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="small text-muted">Sala</div>
                        <div class="data-chip mt-1"><i class="fas fa-door-open"></i>{{ $primeira->sala->nome }}</div>
                    </div>

                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="small text-muted">{{ $recorrente ? 'Recorr&ecirc;ncia' : 'Data' }}</div>
                        @if($recorrente)
                            <div class="fw-bold">Todas as {!! $diaSemana !!}s</div>
                            <div class="small text-muted">{{ $grupo->count() }} datas em 3 meses</div>
                        @else
                            <div class="fw-bold">{{ date('d/m/Y', strtotime($primeira->data_reserva)) }}</div>
                        @endif
                    </div>

                    <div class="col-6 col-md-3 col-xl-1">
                        <div class="small text-muted">Per&iacute;odo</div>
                        <div class="data-chip mt-1"><i class="fas fa-clock"></i>{{ $primeira->periodo }}</div>
                    </div>

                    <div class="col-12 col-xl-2 text-xl-end">
                        @if($recorrente)
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                                <i class="fas fa-chevron-down me-1"></i>Ver datas
                            </button>
                        @else
                            <div class="d-flex flex-wrap justify-content-xl-end gap-2">
                                <form action="{{ route('reserva.mudarStatus', $primeira->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="aprovada">
                                    <input type="hidden" name="comentario_adm" value="">
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>Aprovar</button>
                                </form>
                                <form action="{{ route('reserva.mudarStatus', $primeira->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="rejeitada">
                                    <input type="hidden" name="comentario_adm" value="">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-times me-1"></i>Recusar</button>
                                </form>
                                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                                    <i class="fas fa-chevron-down me-1"></i>Ver
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="collapse mt-3 {{ $manterAberto ? 'show' : '' }}" id="{{ $collapseId }}">
                    <div class="tech-panel p-3">
                        <div class="mb-3">
                            <div class="small text-muted mb-1">Coment&aacute;rio do professor</div>
                            <div>{{ $primeira->comentario_professor ?? 'Nenhum coment&aacute;rio informado.' }}</div>
                        </div>

                        <div class="d-grid gap-2">
                            @foreach($grupo as $reserva)
                                <div class="reservation-detail-row p-3 rounded">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                        <div>
                                            <div class="fw-bold">{{ date('d/m/Y', strtotime($reserva->data_reserva)) }}</div>
                                            <span class="badge {{ $reserva->status === 'em_analise' ? 'text-bg-info' : 'text-bg-warning' }}">
                                                {{ $reserva->status === 'em_analise' ? 'Em an&aacute;lise' : 'Pendente' }}
                                            </span>
                                            @if($reserva->status === 'em_analise' && $reserva->comentario_adm)
                                                <div class="small text-warning mt-2">
                                                    <i class="fas fa-triangle-exclamation me-1"></i>{{ $reserva->comentario_adm }}
                                                </div>
                                            @endif
                                        </div>

                                        <form action="{{ route('reserva.mudarStatus', $reserva->id) }}" method="POST" class="d-flex flex-wrap gap-2 align-items-start">
                                            @csrf
                                            <input type="hidden" name="open_group" value="{{ $collapseId }}">
                                            <textarea name="comentario_adm" class="form-control form-control-sm" rows="1" placeholder="Coment&aacute;rio opcional"></textarea>
                                            <button type="submit" name="status" value="aprovada" class="btn btn-sm btn-success">
                                                <i class="fas fa-check me-1"></i>Aprovar
                                            </button>
                                            <button type="submit" name="status" value="rejeitada" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-times me-1"></i>Recusar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($recorrente)
                            <form action="{{ route('reserva.grupoStatus', $grupoId) }}" method="POST" class="mt-3 pt-3 border-top" style="border-color: var(--line) !important;">
                                @csrf
                                <input type="hidden" name="open_group" value="{{ $collapseId }}">
                                <div class="row g-2 align-items-end">
                                    <div class="col-12 col-lg">
                                        <label class="form-label small text-muted">Coment&aacute;rio para todas</label>
                                        <textarea name="comentario_adm" class="form-control form-control-sm" rows="2" placeholder="Coment&aacute;rio opcional para todas as datas"></textarea>
                                    </div>
                                    <div class="col-12 col-lg-auto d-flex flex-wrap gap-2 justify-content-lg-end">
                                        <button type="submit" name="status" value="aprovada" class="btn btn-success">
                                            <i class="fas fa-check-double me-1"></i>Aprovar todas
                                        </button>
                                        <button type="submit" name="status" value="rejeitada" class="btn btn-outline-danger">
                                            <i class="fas fa-ban me-1"></i>Recusar todas
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="page-card text-center py-5">
                <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                <h5 class="fw-bold">Nenhuma solicita&ccedil;&atilde;o pendente</h5>
                <p class="text-muted mb-0">Quando houver novos pedidos, eles aparecer&atilde;o aqui.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
