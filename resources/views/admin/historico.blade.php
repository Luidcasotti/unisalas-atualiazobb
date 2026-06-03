@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="section-kicker mb-2">Base operacional</div>
            <h2 class="fw-bold mb-1">Hist&oacute;rico geral</h2>
            <p class="text-muted mb-0">Consulta organizada de reservas por professor, bloco, sala, data, per&iacute;odo e status.</p>
        </div>
        <span class="data-chip"><i class="fas fa-database"></i>{{ $reservas->count() }} registros sem pendentes</span>
    </div>

    <div class="page-card p-3 mb-4">
        <form action="{{ route('admin.historico') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-md-6 col-xl-2">
                <label class="form-label">Data</label>
                <input type="date" name="data" class="form-control" value="{{ request('data') }}">
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <label class="form-label">Bloco</label>
                <select name="bloco_id" id="filtroBloco" class="form-select">
                    <option value="">Todos os blocos</option>
                    @foreach($blocos as $bloco)
                        <option value="{{ $bloco->id }}" @selected(request('bloco_id') == $bloco->id)>{{ $bloco->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <label class="form-label">Sala</label>
                <select name="sala_id" id="filtroSala" class="form-select">
                    <option value="">Todas as salas</option>
                    @foreach($salas as $sala)
                        <option value="{{ $sala->id }}" data-bloco-id="{{ $sala->bloco_id }}" @selected(request('sala_id') == $sala->id)>
                            {{ $sala->nome }}{{ $sala->bloco ? ' - ' . $sala->bloco->nome : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl-2">
                <label class="form-label">Turno</label>
                <select name="periodo" class="form-select">
                    <option value="">Todos</option>
                    @foreach($periodos as $periodo)
                        <option value="{{ $periodo }}" @selected(request('periodo') == $periodo)>{{ $periodo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-xl-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
            <div class="col-6 col-xl-1">
                <a href="{{ route('admin.historico') }}" class="btn btn-outline-primary w-100"><i class="fas fa-eraser"></i></a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-3">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-3">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
        </div>
    @endif

    <div class="d-grid gap-3">
        @forelse($reservas as $r)
            @php
                $statusClass = $r->status == 'aprovada' ? 'text-bg-success' : ($r->status == 'pendente' ? 'text-bg-warning' : 'text-bg-danger');
            @endphp
            <div class="page-card p-3">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-xl-3">
                        <div class="small text-muted">Professor</div>
                        <div class="fw-bold">{{ $r->user->name ?? 'N/A' }}</div>
                        <div class="small text-muted">{{ $r->user->email ?? 'E-mail indispon&iacute;vel' }}</div>
                    </div>

                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="small text-muted">Bloco</div>
                        <div class="data-chip mt-1"><i class="fas fa-building"></i>{{ $r->sala->bloco->nome ?? 'N/A' }}</div>
                    </div>

                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="small text-muted">Sala</div>
                        <div class="data-chip mt-1"><i class="fas fa-door-open"></i>{{ $r->sala->nome ?? 'N/A' }}</div>
                    </div>

                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="small text-muted">Data</div>
                        <div class="fw-bold">{{ $r->data_reserva ? date('d/m/Y', strtotime($r->data_reserva)) : 'N/A' }}</div>
                    </div>

                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="small text-muted">Per&iacute;odo</div>
                        <div class="data-chip mt-1"><i class="fas fa-clock"></i>{{ $r->periodo ?? 'N&atilde;o informado' }}</div>
                    </div>

                    <div class="col-12 col-xl-1 text-xl-end">
                        <span class="badge {{ $statusClass }}">{{ ucfirst($r->status) }}</span>
                    </div>
                </div>

                @if($r->comentario_adm)
                    <div class="tech-panel p-3 mt-3">
                        <div class="small text-muted mb-1">Coment&aacute;rio do administrador</div>
                        <div>{{ $r->comentario_adm }}</div>
                    </div>
                @endif

                @if($r->status !== 'cancelada')
                    <div class="collapse mt-3" id="cancelarReserva{{ $r->id }}">
                        <form action="{{ route('reserva.cancelar', $r->id) }}" method="POST" class="tech-panel p-3">
                            @csrf
                            <label class="form-label">Motivo do cancelamento</label>
                            <textarea name="comentario_adm" class="form-control mb-3" rows="3" required placeholder="Explique o motivo para o professor..."></textarea>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fas fa-ban me-2"></i>Confirmar cancelamento
                                </button>
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#cancelarReserva{{ $r->id }}">
                                    Fechar
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="text-end mt-3">
                        <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#cancelarReserva{{ $r->id }}">
                            <i class="fas fa-ban me-1"></i>Cancelar reserva
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="page-card text-center py-5">
                <i class="fas fa-database fa-2x text-primary mb-3"></i>
                <h5 class="fw-bold">Nenhum registro encontrado</h5>
                <p class="text-muted mb-0">Ajuste o filtro ou aguarde novas reservas.</p>
            </div>
        @endforelse
    </div>
</div>
<script>
    const filtroBloco = document.getElementById('filtroBloco');
    const filtroSala = document.getElementById('filtroSala');

    function filtrarSalasPorBloco() {
        const blocoSelecionado = filtroBloco.value;
        const salaSelecionada = filtroSala.value;
        let salaAtualContinuaVisivel = true;

        filtroSala.querySelectorAll('option').forEach((option) => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            const pertenceAoBloco = !blocoSelecionado || option.dataset.blocoId === blocoSelecionado;
            option.hidden = !pertenceAoBloco;

            if (option.value === salaSelecionada && !pertenceAoBloco) {
                salaAtualContinuaVisivel = false;
            }
        });

        if (!salaAtualContinuaVisivel) {
            filtroSala.value = '';
        }
    }

    filtroBloco?.addEventListener('change', filtrarSalasPorBloco);
    filtrarSalasPorBloco();
</script>
@endsection
