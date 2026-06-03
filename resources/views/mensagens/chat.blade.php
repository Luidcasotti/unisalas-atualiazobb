@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="section-kicker mb-2">Canal direto</div>
            <h2 class="fw-bold mb-1">Conversa com {{ $usuario->name }}</h2>
            <p class="text-muted mb-0">{{ $usuario->email }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form action="{{ route('chat.apagar', $usuario->id) }}" method="POST" onsubmit="return confirm('Apagar todo este chat?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-trash-alt me-2"></i>Apagar chat
                </button>
            </form>
            <a href="{{ route('mensagens.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-3">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <style>
        .chat-shell {
            height: min(62vh, 620px);
            overflow-y: auto;
            background:
                linear-gradient(180deg, rgba(29, 155, 240, 0.08), transparent),
                #0b1727;
            border: 1px solid var(--line);
            border-radius: 8px;
        }
        .bubble {
            max-width: min(680px, 82%);
            border-radius: 8px;
            padding: 10px 14px;
            border: 1px solid var(--line);
        }
        .bubble-out {
            background: linear-gradient(135deg, #1d9bf0, #1468a6);
            color: white;
            border-color: rgba(92, 201, 255, 0.38);
        }
        .bubble-in {
            background: #13263d;
            color: var(--text);
        }
    </style>

    <div class="page-card p-3">
        <div class="chat-shell p-3 mb-3">
            @forelse($mensagens as $msg)
                <div class="d-flex {{ $msg->remetente_id == Auth::id() ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                    <div class="bubble {{ $msg->remetente_id == Auth::id() ? 'bubble-out' : 'bubble-in' }}">
                        <p class="mb-1">{{ $msg->mensagem }}</p>
                        <small class="opacity-75">{{ $msg->created_at->format('d/m H:i') }}</small>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="fas fa-satellite-dish fa-2x text-primary mb-3"></i>
                    <div>Nenhuma mensagem ainda.</div>
                </div>
            @endforelse
        </div>

        <form action="{{ route('admin.enviarMensagem') }}" method="POST">
            @csrf
            <input type="hidden" name="destinatario_id" value="{{ $usuario->id }}">
            <div class="input-group">
                <textarea name="mensagem" class="form-control" rows="2" required placeholder="Digite sua mensagem..."></textarea>
                <button class="btn btn-primary px-4" type="submit">
                    <i class="fas fa-paper-plane me-2"></i>Enviar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
