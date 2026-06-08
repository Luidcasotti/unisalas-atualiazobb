@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="section-kicker mb-2">Administra&ccedil;&atilde;o</div>
            <h2 class="fw-bold mb-1"><i class="fas fa-users me-2 text-primary"></i>Usu&aacute;rios</h2>
            <p class="text-muted mb-0">Lista compacta com detalhes em slide-down para consulta r&aacute;pida.</p>
        </div>

        <a href="{{ route('usuario.novo') }}" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>Novo usu&aacute;rio
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

    <style>
        .users-accordion .accordion-item {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.18);
        }
        .users-accordion .accordion-button {
            background: linear-gradient(90deg, #13263d, #0f1c2f);
            color: var(--text);
            box-shadow: none;
            padding: 16px 18px;
        }
        .users-accordion .accordion-button:not(.collapsed) {
            background: linear-gradient(90deg, #173454, #10243b);
            color: var(--text);
            border-bottom: 1px solid var(--line);
        }
        .users-accordion .accordion-body { background: #0f1c2f; }
        .avatar-node {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #123354;
            color: #5cc9ff;
            border: 1px solid #2d90cb;
        }
        .reservation-row {
            background: #101d2f;
            border: 1px solid var(--line);
            border-radius: 8px;
        }
        .reservation-title {
            color: #f8fbff;
            text-shadow: 0 0 14px rgba(92, 201, 255, 0.18);
        }
        .reservation-meta {
            color: #c6d6ea;
        }
        .edit-profile-box .form-label {
            color: #e8f2ff;
            font-weight: 700;
        }
        .edit-profile-box .form-text {
            color: #c6d6ea;
        }
    </style>

    <div class="accordion users-accordion d-grid gap-3" id="accordionUsuarios">
        @forelse($usuarios as $user)
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingUser{{ $user->id }}">
                    <button
                        class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseUser{{ $user->id }}"
                        aria-expanded="false"
                        aria-controls="collapseUser{{ $user->id }}"
                    >
                        <div class="d-flex flex-grow-1 align-items-center gap-3">
                            <span class="avatar-node"><i class="fas fa-user"></i></span>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="fw-bold">{{ $user->name }}</span>
                                    @if($user->tipo == 'admin')
                                        <span class="badge text-bg-danger">Administrador</span>
                                    @else
                                        <span class="badge text-bg-primary">Professor</span>
                                    @endif
                                    @if(auth()->id() === $user->id)
                                        <span class="data-chip">Voc&ecirc;</span>
                                    @endif
                                </div>
                                <div class="text-muted small">{{ $user->email }}</div>
                            </div>
                        </div>
                    </button>
                </h2>

                <div
                    id="collapseUser{{ $user->id }}"
                    class="accordion-collapse collapse"
                    aria-labelledby="headingUser{{ $user->id }}"
                    data-bs-parent="#accordionUsuarios"
                >
                    <div class="accordion-body p-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-3">
                                <div class="small text-muted">ID do usu&aacute;rio</div>
                                <div class="data-chip mt-1"><i class="fas fa-fingerprint"></i>#{{ $user->id }}</div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="small text-muted">E-mail</div>
                                <div class="fw-bold">{{ $user->email }}</div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="small text-muted">Perfil</div>
                                <div class="data-chip mt-1"><i class="fas fa-shield-alt"></i>{{ $user->tipo == 'admin' ? 'Administrador' : 'Professor' }}</div>
                            </div>
                            <div class="col-12 col-md-2 text-md-end">
                                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#editarUser{{ $user->id }}">
                                        <i class="fas fa-user-pen me-1"></i>Editar
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#reservasUser{{ $user->id }}">
                                        <i class="fas fa-calendar-alt me-1"></i>Ver reservas
                                    </button>
                                    @php
                                        $reservasAtivas = $user->reservas->where('data_reserva', '>=', now()->toDateString())->whereNotIn('status', ['cancelada', 'rejeitada', 'recusada'])->count();
                                        $mensagensNaoLidasUsuario = \App\Models\MensagemDireta::where('remetente_id', $user->id)->where('destinatario_id', auth()->id())->where('lida', false)->count();
                                    @endphp
                                    <form
                                        action="{{ route('usuario.excluir', $user->id) }}"
                                        method="POST"
                                        class="swal-delete-user"
                                        data-name="{{ $user->name }}"
                                        data-reservas="{{ $reservasAtivas }}"
                                        data-mensagens="{{ $mensagensNaoLidasUsuario }}"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            @if(auth()->id() === $user->id) disabled @endif
                                        >
                                            <i class="fas fa-trash-alt me-1"></i>Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="collapse mt-4" id="editarUser{{ $user->id }}">
                            <form action="{{ route('usuario.atualizar', $user->id) }}" method="POST" class="tech-panel edit-profile-box p-3">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label">Nome</label>
                                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label">E-mail</label>
                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label">Nova senha</label>
                                        <input type="password" name="password" class="form-control" placeholder="Deixe vazio para manter">
                                        <div class="form-text">As reservas deste professor ser&atilde;o mantidas.</div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Salvar perfil
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="collapse mt-4" id="reservasUser{{ $user->id }}">
                            @php
                                $reservasFuturas = $user->reservas->where('data_reserva', '>=', now()->toDateString());
                                $reservasPassadas = $user->reservas->where('data_reserva', '<', now()->toDateString());
                            @endphp

                            <div class="row g-3">
                                <div class="col-12 col-xl-6">
                                    <h6 class="fw-bold text-primary">Reservas futuras</h6>
                                    <div class="d-grid gap-2">
                                        @forelse($reservasFuturas as $reserva)
                                            <div class="reservation-row p-3">
                                                <div class="fw-bold reservation-title">{{ $reserva->sala->nome ?? 'Sala removida' }}</div>
                                                <div class="small reservation-meta">
                                                    {{ $reserva->sala->bloco->nome ?? 'Bloco indispon&iacute;vel' }} |
                                                    {{ date('d/m/Y', strtotime($reserva->data_reserva)) }} |
                                                    {{ $reserva->periodo }} |
                                                    {{ ucfirst($reserva->status) }}
                                                </div>
                                                @if($reserva->data_reserva >= now()->toDateString() && $reserva->status !== 'cancelada')
                                                    <div class="collapse mt-3" id="cancelarReservaUsuario{{ $reserva->id }}">
                                                        <form action="{{ route('reserva.cancelar', $reserva->id) }}" method="POST">
                                                            @csrf
                                                            <textarea name="comentario_adm" class="form-control form-control-sm mb-2" rows="2" required placeholder="Motivo do cancelamento..."></textarea>
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                Confirmar cancelamento
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-danger mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#cancelarReservaUsuario{{ $reserva->id }}">
                                                        <i class="fas fa-ban me-1"></i>Cancelar reserva
                                                    </button>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="small reservation-meta">Nenhuma reserva futura.</div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="col-12 col-xl-6">
                                    <h6 class="fw-bold text-primary">Reservas passadas</h6>
                                    <div class="d-grid gap-2">
                                        @forelse($reservasPassadas as $reserva)
                                            <div class="reservation-row p-3">
                                                <div class="fw-bold reservation-title">{{ $reserva->sala->nome ?? 'Sala removida' }}</div>
                                                <div class="small reservation-meta">
                                                    {{ $reserva->sala->bloco->nome ?? 'Bloco indispon&iacute;vel' }} |
                                                    {{ date('d/m/Y', strtotime($reserva->data_reserva)) }} |
                                                    {{ $reserva->periodo }} |
                                                    {{ ucfirst($reserva->status) }}
                                                </div>
                                            </div>
                                        @empty
                                            <div class="small reservation-meta">Nenhuma reserva passada.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="page-card text-center py-5">
                <i class="fas fa-users fa-2x text-primary mb-3"></i>
                <h5 class="fw-bold">Nenhum usu&aacute;rio cadastrado</h5>
                <p class="text-muted mb-0">Crie o primeiro acesso para come&ccedil;ar.</p>
            </div>
        @endforelse
    </div>
</div>
<script>
    document.querySelectorAll('.swal-delete-user').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const reservas = Number(form.dataset.reservas || 0);
            const mensagens = Number(form.dataset.mensagens || 0);
            const detalhes = [];

            if (reservas > 0) detalhes.push(`${reservas} reserva(s) ativa(s) serão liberadas`);
            if (mensagens > 0) detalhes.push(`${mensagens} mensagem(ns) não lida(s) serão apagadas`);

            const result = await Swal.fire({
                title: `Excluir ${form.dataset.name}?`,
                html: detalhes.length
                    ? `<p>${detalhes.join('<br>')}</p><p>O chat do usuário também será apagado.</p>`
                    : '<p>O usuário será removido do sistema.</p>',
                icon: detalhes.length ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar',
                background: document.documentElement.dataset.theme === 'light' ? '#ffffff' : '#101d2f',
                color: document.documentElement.dataset.theme === 'light' ? '#142235' : '#e8f2ff',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#1d9bf0'
            });

            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endsection
