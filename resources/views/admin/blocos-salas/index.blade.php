@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="section-kicker mb-2">Infraestrutura</div>
            <h2 class="fw-bold mb-1">Blocos e salas</h2>
            <p class="text-muted mb-0">Controle a estrutura f&iacute;sica dispon&iacute;vel para reservas.</p>
        </div>

        <a href="{{ route('bloco.novo') }}" class="btn btn-primary px-4">
            <i class="fas fa-plus-circle me-2"></i>Adicionar bloco
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-3">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-3">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="metric-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Blocos cadastrados</div>
                        <div class="h2 fw-bold mb-0">{{ $blocos->count() }}</div>
                    </div>
                    <span class="metric-icon bg-primary-subtle text-primary"><i class="fas fa-building"></i></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="metric-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Salas totais</div>
                        <div class="h2 fw-bold mb-0">{{ $blocos->sum(fn($bloco) => $bloco->salas->count()) }}</div>
                    </div>
                    <span class="metric-icon bg-info-subtle text-info"><i class="fas fa-door-open"></i></span>
                </div>
            </div>
        </div>
    </div>

    <style>
        .infra-accordion .accordion-item {
            border: 1px solid var(--line);
            border-radius: 8px;
            overflow: hidden;
            background: var(--surface);
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.2);
        }
        .infra-accordion .accordion-button {
            background: linear-gradient(90deg, #13263d, #0f1c2f);
            color: var(--text);
            padding: 18px 20px;
            box-shadow: none;
        }
        .infra-accordion .accordion-button:not(.collapsed) {
            color: var(--text);
            background: linear-gradient(90deg, #173454, #10243b);
            border-bottom: 1px solid var(--line);
        }
        .infra-accordion .accordion-body { background: #0f1c2f; }
        .infra-accordion .accordion-button::after { margin-left: 16px; }
        .block-color {
            width: 12px;
            height: 46px;
            border-radius: 10px;
            background: var(--block-color);
            box-shadow: 0 0 0 4px rgba(40, 215, 255, 0.12);
        }
        .room-row {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #101d2f;
            transition: 0.18s ease;
        }
        .room-row:hover {
            border-color: #2d90cb;
            transform: translateY(-1px);
        }
        .block-title,
        .room-title {
            color: #f4f9ff;
            text-shadow: 0 0 14px rgba(92, 201, 255, 0.18);
        }
        .block-helper {
            color: #b8cbe2;
        }
        .room-section-title {
            color: #f8fbff;
            letter-spacing: 0.01em;
            text-shadow: 0 0 16px rgba(92, 201, 255, 0.22);
        }
        .room-section-helper {
            color: #c6d6ea;
        }
        .maintenance-form .form-label,
        .maintenance-form .form-check-label {
            color: #eef7ff;
            font-weight: 700;
        }
        .maintenance-form .form-control {
            color: #f7fbff;
            border-color: #3f5f82;
        }
        .maintenance-form .form-control::placeholder {
            color: #a8bfd8;
            opacity: 1;
        }
        .room-delete-button {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-color: rgba(255, 99, 132, 0.38) !important;
            color: #ff7d93 !important;
            background: rgba(255, 99, 132, 0.08) !important;
            transition: transform 0.16s ease, background 0.16s ease, border-color 0.16s ease;
        }
        .room-delete-button:hover,
        .room-delete-button:focus {
            color: #fff !important;
            background: #d93450 !important;
            border-color: #ff6b85 !important;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(217, 52, 80, 0.28);
        }
        .block-delete-button {
            border-color: rgba(255, 99, 132, 0.5) !important;
            color: #ff8aa0 !important;
            background: rgba(255, 99, 132, 0.08) !important;
            font-weight: 700;
            transition: transform 0.16s ease, background 0.16s ease, border-color 0.16s ease;
        }
        .block-delete-button:hover,
        .block-delete-button:focus {
            color: #fff !important;
            background: #d93450 !important;
            border-color: #ff6b85 !important;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(217, 52, 80, 0.28);
        }
        .swal2-popup.unisalas-delete-modal {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
            color: var(--text);
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.42);
        }
        .swal2-popup.unisalas-delete-modal .swal2-title {
            color: var(--text);
            font-size: 1.35rem;
        }
        .swal2-popup.unisalas-delete-modal .swal2-html-container {
            color: var(--muted);
            font-size: 0.95rem;
        }
        .delete-target-name {
            display: inline-block;
            margin-top: 6px;
            padding: 6px 10px;
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--text);
            background: rgba(255, 255, 255, 0.04);
            font-weight: 700;
        }
    </style>

    <div class="accordion infra-accordion d-grid gap-3" id="accordionBlocos">
        @forelse($blocos as $bloco)
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingBloco{{ $bloco->id }}">
                    <button
                        class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseBloco{{ $bloco->id }}"
                        aria-expanded="false"
                        aria-controls="collapseBloco{{ $bloco->id }}"
                    >
                        <div class="d-flex flex-grow-1 align-items-center gap-3">
                            <span class="block-color" style="--block-color: {{ $bloco->cor ?? '#0b5cad' }}"></span>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="fw-bold fs-5 block-title">{{ $bloco->nome }}</span>
                                    <span class="data-chip">{{ $bloco->salas->count() }} salas</span>
                                    @if($bloco->manutencao_ativa)
                                        <span class="badge text-bg-warning">Em manuten&ccedil;&atilde;o</span>
                                    @endif
                                </div>
                                <div class="small block-helper">Clique para visualizar e administrar as salas deste bloco.</div>
                            </div>
                        </div>
                    </button>
                </h2>

                <div
                    id="collapseBloco{{ $bloco->id }}"
                    class="accordion-collapse collapse"
                    aria-labelledby="headingBloco{{ $bloco->id }}"
                    data-bs-parent="#accordionBlocos"
                >
                    <div class="accordion-body p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <h5 class="fw-bold mb-1 room-section-title">Salas do bloco</h5>
                                <p class="small mb-0 room-section-helper">Edite salas, remova registros ou adicione novos espa&ccedil;os.</p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('bloco.editar', $bloco->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i>Editar bloco
                                </a>
                                <form action="{{ route('bloco.excluir', $bloco->id) }}" method="POST" class="delete-block-form" data-block-name="{{ $bloco->nome }}" data-room-count="{{ $bloco->salas->count() }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm block-delete-button" title="Excluir bloco" aria-label="Excluir bloco {{ $bloco->nome }}">
                                        <i class="fas fa-trash me-1"></i>Excluir
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="tech-panel p-3 mb-3">
                            <form action="{{ route('bloco.manutencao', $bloco->id) }}" method="POST" class="row g-3 align-items-end maintenance-form">
                                @csrf
                                <div class="col-12 col-lg-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input maintenance-toggle" type="checkbox" name="manutencao_ativa" value="1" @checked($bloco->manutencao_ativa)>
                                        <label class="form-check-label">Manuten&ccedil;&atilde;o</label>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-2">
                                    <div class="form-check">
                                        <input class="form-check-input maintenance-indefinite" type="checkbox" name="manutencao_indeterminada" value="1" @checked($bloco->manutencao_indeterminada)>
                                        <label class="form-check-label">Tempo indeterminado</label>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-2 maintenance-date-wrap">
                                    <label class="form-label">T&eacute;rmino</label>
                                    <input type="date" name="manutencao_fim" class="form-control" value="{{ $bloco->manutencao_fim }}">
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label class="form-label">Aviso opcional</label>
                                    <input type="text" name="manutencao_aviso" class="form-control" value="{{ $bloco->manutencao_aviso }}" placeholder="Ex.: Reforma el&eacute;trica">
                                </div>
                                <div class="col-12 col-lg-2">
                                    <button class="btn btn-sm btn-primary w-100" type="submit">Salvar</button>
                                </div>
                            </form>
                        </div>

                        <div class="d-grid gap-2">
                            @forelse($bloco->salas as $sala)
                                <div class="room-row p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                                    <div>
                                        <div class="fw-bold room-title">
                                            <i class="fas fa-door-open me-2 text-primary"></i>{{ $sala->nome }}
                                            @if($sala->manutencao_ativa)
                                                <span class="badge text-bg-warning ms-2">Em manuten&ccedil;&atilde;o</span>
                                            @endif
                                        </div>
                                        <div class="text-muted small">{{ $sala->observacao ?: 'Sem observa&ccedil;&atilde;o cadastrada.' }}</div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('sala.editar', $sala->id) }}" class="btn btn-sm btn-light border">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('sala.excluir', $sala->id) }}" method="POST" class="delete-room-form" data-room-name="{{ $sala->nome }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm border room-delete-button" type="submit" title="Excluir sala" aria-label="Excluir sala {{ $sala->nome }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="w-100">
                                        <div class="collapse" id="manutencaoSala{{ $sala->id }}">
                                            <form action="{{ route('sala.manutencao', $sala->id) }}" method="POST" class="tech-panel p-3 mt-2 row g-3 align-items-end maintenance-form">
                                                @csrf
                                                <div class="col-12 col-lg-2">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input maintenance-toggle" type="checkbox" name="manutencao_ativa" value="1" @checked($sala->manutencao_ativa)>
                                                        <label class="form-check-label">Manuten&ccedil;&atilde;o</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input maintenance-indefinite" type="checkbox" name="manutencao_indeterminada" value="1" @checked($sala->manutencao_indeterminada)>
                                                        <label class="form-check-label">Tempo indeterminado</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-2 maintenance-date-wrap">
                                                    <label class="form-label">T&eacute;rmino</label>
                                                    <input type="date" name="manutencao_fim" class="form-control" value="{{ $sala->manutencao_fim }}">
                                                </div>
                                                <div class="col-12 col-lg-4">
                                                    <label class="form-label">Aviso opcional</label>
                                                    <input type="text" name="manutencao_aviso" class="form-control" value="{{ $sala->manutencao_aviso }}" placeholder="Ex.: Ar-condicionado em reparo">
                                                </div>
                                                <div class="col-12 col-lg-2">
                                                    <button class="btn btn-sm btn-primary w-100" type="submit">Salvar</button>
                                                </div>
                                            </form>
                                        </div>
                                        <button class="btn btn-sm btn-outline-warning mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#manutencaoSala{{ $sala->id }}">
                                            <i class="fas fa-screwdriver-wrench me-1"></i>Manuten&ccedil;&atilde;o da sala
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4 tech-panel">
                                    Nenhuma sala cadastrada neste bloco.
                                </div>
                            @endforelse
                        </div>

                        <a href="{{ route('sala.nova', $bloco->id) }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-2"></i>Nova sala neste bloco
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="page-card text-center p-5">
                <i class="fas fa-building fa-2x text-primary mb-3"></i>
                <h5 class="fw-bold">Nenhum bloco cadastrado</h5>
                <p class="text-muted mb-3">Crie o primeiro bloco para organizar as salas do sistema.</p>
                <a href="{{ route('bloco.novo') }}" class="btn btn-primary">Adicionar bloco</a>
            </div>
        @endforelse
    </div>
</div>
<script>
    function refreshMaintenanceDate(form) {
        const indefinite = form.querySelector('.maintenance-indefinite')?.checked;
        const dateWrap = form.querySelector('.maintenance-date-wrap');
        if (dateWrap) dateWrap.style.display = indefinite ? 'none' : '';
    }

    document.querySelectorAll('.maintenance-form').forEach((form) => {
        refreshMaintenanceDate(form);
        form.querySelector('.maintenance-indefinite')?.addEventListener('change', () => refreshMaintenanceDate(form));
    });

    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = value;
        return element.innerHTML;
    }

    document.querySelectorAll('.delete-room-form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') {
                return;
            }

            event.preventDefault();

            const roomName = form.dataset.roomName || 'esta sala';

            Swal.fire({
                icon: 'warning',
                title: 'Excluir sala?',
                html: `A sala sairá da lista ativa e reservas futuras serão canceladas, mas o histórico será preservado.<br><span class="delete-target-name">${escapeHtml(roomName)}</span>`,
                showCancelButton: true,
                confirmButtonText: 'Excluir sala',
                cancelButtonText: 'Manter sala',
                reverseButtons: true,
                focusCancel: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'unisalas-delete-modal',
                    confirmButton: 'btn btn-danger px-4',
                    cancelButton: 'btn btn-light border px-4 me-2',
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.requestSubmit();
                }
            });
        });
    });

    document.querySelectorAll('.delete-block-form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') {
                return;
            }

            event.preventDefault();

            const blockName = form.dataset.blockName || 'este bloco';
            const roomCount = Number(form.dataset.roomCount || 0);
            const roomText = roomCount === 1 ? '1 sala' : `${roomCount} salas`;

            Swal.fire({
                icon: 'warning',
                title: 'Excluir bloco?',
                html: `O bloco e ${roomText} sairão da lista ativa. Reservas futuras serão canceladas, mas o histórico será preservado.<br><span class="delete-target-name">${escapeHtml(blockName)}</span>`,
                showCancelButton: true,
                confirmButtonText: 'Excluir bloco',
                cancelButtonText: 'Manter bloco',
                reverseButtons: true,
                focusCancel: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'unisalas-delete-modal',
                    confirmButton: 'btn btn-danger px-4',
                    cancelButton: 'btn btn-light border px-4 me-2',
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.requestSubmit();
                }
            });
        });
    });
</script>
@endsection
