@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="mb-4">
        <div class="section-kicker mb-2">Comunica&ccedil;&atilde;o</div>
        <h2 class="fw-bold mb-1">Central de mensagens</h2>
        <p class="text-muted mb-0">Hist&oacute;rico de mensagens do sistema.</p>
    </div>

    <div class="page-card p-4">
        @forelse($mensagens as $msg)
            <div class="tech-panel p-3 mb-3 {{ $msg->remetente_id == Auth::id() ? 'text-end' : '' }}">
                <small class="d-block mb-1 fw-bold text-primary">
                    {{ $msg->remetente_id == Auth::id() ? 'Voc&ecirc; enviou' : 'De: ' . ($msg->remetente->name ?? 'Sistema') }}
                </small>
                <p class="mb-1">{{ $msg->mensagem }}</p>
                <small class="text-muted">{{ $msg->created_at->format('d/m/Y H:i') }}</small>
            </div>
        @empty
            <p class="text-center text-muted mb-0">Nenhuma mensagem encontrada.</p>
        @endforelse
    </div>

    <div class="mt-4 text-center">
        <a href="{{ Auth::user()->tipo == 'admin' ? route('admin.dashboard') : route('professor.painel') }}" class="btn btn-outline-primary">Voltar ao painel</a>
    </div>
</div>
@endsection
