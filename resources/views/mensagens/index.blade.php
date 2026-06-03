@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="section-kicker mb-2">Comunica&ccedil;&atilde;o</div>
            <h2 class="fw-bold mb-1">Central de mensagens</h2>
            <p class="text-muted mb-0">Escolha um contato para iniciar ou continuar uma conversa.</p>
        </div>
        <span class="data-chip"><i class="fas fa-comments"></i>{{ $contatos->count() }} contatos</span>
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

    <style>
        .contact-card {
            background: linear-gradient(135deg, #13263d, #0f1c2f);
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--text);
            text-decoration: none;
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.18);
            transition: 0.18s ease;
        }
        .contact-card:hover {
            color: var(--text);
            border-color: #2d90cb;
            transform: translateY(-2px);
        }
        .contact-card.has-new {
            border-color: #f6c343;
            box-shadow: 0 0 0 1px rgba(246, 195, 67, 0.32), 0 16px 34px rgba(0, 0, 0, 0.22);
        }
        .new-pulse {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #f6c343;
            box-shadow: 0 0 0 6px rgba(246, 195, 67, 0.12);
        }
        .contact-avatar {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            background: #123354;
            color: #5cc9ff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #2d90cb;
        }
        .recipient-list {
            max-height: 220px;
            overflow-y: auto;
        }
        .recipient-option {
            background: #0b1727;
            border: 1px solid var(--line);
            border-radius: 8px;
            cursor: pointer;
        }
        .recipient-option:hover {
            border-color: #2d90cb;
        }
    </style>

    <div class="page-card p-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h5 class="fw-bold mb-1">Nova mensagem</h5>
                <p class="text-muted small mb-0">Pesquise um contato, selecione e envie a mensagem.</p>
            </div>
            <span class="data-chip"><i class="fas fa-paper-plane"></i>Envio direto</span>
        </div>

        <form action="{{ route('admin.enviarMensagem') }}" method="POST">
            @csrf
            <input type="hidden" name="destinatario_id" id="destinatario_id" required>

            <div class="row g-3">
                <div class="col-12 col-xl-5">
                    <label class="form-label">Pesquisar contato</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-transparent border-secondary text-primary"><i class="fas fa-search"></i></span>
                        <input type="text" id="buscarContato" class="form-control" placeholder="{{ Auth::user()->tipo == 'admin' ? 'Digite o nome do professor...' : 'Digite o nome do administrador...' }}">
                    </div>

                    <div class="recipient-list d-grid gap-2">
                        @foreach($contatos as $contato)
                            <label class="recipient-option p-3 contato-opcao" data-name="{{ strtolower($contato->name . ' ' . $contato->email) }}">
                                <input type="radio" name="contato_visual" value="{{ $contato->id }}" class="form-check-input me-2">
                                <strong>{{ $contato->name }}</strong>
                                <span class="text-muted small d-block ms-4">{{ $contato->email }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="col-12 col-xl-7">
                    <label class="form-label">Mensagem</label>
                    <textarea name="mensagem" class="form-control mb-3" rows="7" required placeholder="Escreva a mensagem..."></textarea>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Enviar mensagem
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="row g-3">
        @forelse($contatos as $contato)
            @php
                $novas = \App\Models\MensagemDireta::where('remetente_id', $contato->id)
                    ->where('destinatario_id', Auth::id())
                    ->where('lida', false)
                    ->count();
            @endphp
            <div class="col-12 col-md-6 col-xl-4">
                <a href="{{ route('chat.show', $contato->id) }}" class="contact-card {{ $novas > 0 ? 'has-new' : '' }} d-flex align-items-center gap-3 p-3 h-100">
                    <span class="contact-avatar"><i class="fas fa-user"></i></span>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold">{{ $contato->name }}</span>
                            @if($novas > 0)
                                <span class="new-pulse"></span>
                                <span class="badge text-bg-warning">{{ $novas }} nova{{ $novas > 1 ? 's' : '' }}</span>
                            @endif
                        </div>
                        <div class="text-muted small">{{ $contato->email }}</div>
                        <div class="data-chip mt-2"><i class="fas fa-shield-alt"></i>{{ $contato->tipo == 'admin' ? 'Administrador' : 'Professor' }}</div>
                    </div>
                    <i class="fas fa-chevron-right text-primary"></i>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="page-card text-center py-5">
                    <i class="fas fa-comments fa-2x text-primary mb-3"></i>
                    <h5 class="fw-bold">Nenhum contato dispon&iacute;vel</h5>
                    <p class="text-muted mb-0">Quando houver contatos habilitados, eles aparecer&atilde;o aqui.</p>
                </div>
            </div>
        @endforelse
    </div>

    <script>
        document.querySelectorAll('.contato-opcao input[type="radio"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                document.getElementById('destinatario_id').value = radio.value;
            });
        });

        document.getElementById('buscarContato')?.addEventListener('input', (event) => {
            const termo = event.target.value.toLowerCase();
            document.querySelectorAll('.contato-opcao').forEach((opcao) => {
                opcao.style.display = opcao.dataset.name.includes(termo) ? '' : 'none';
            });
        });
    </script>
</div>
@endsection
