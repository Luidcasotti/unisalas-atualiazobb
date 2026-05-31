<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniSalas - Sistema de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        #sidebar { min-width: 250px; background: #7010a8; min-height: 100vh; color: white; display: flex; flex-direction: column; }
        #sidebar a { color: white; text-decoration: none; display: block; padding: 15px; border-bottom: 1px solid #8c26c7; transition: 0.3s; }
        #sidebar a:hover { background: #8c26c7; }
        .user-profile { padding: 20px; background: rgba(0,0,0,0.1); text-align: center; border-bottom: 1px solid #8c26c7; }
        .main-content { flex-grow: 1; background: #f8f9fa; }
    </style>
</head>
<body>
<div class="d-flex">
    <nav id="sidebar">
        <div class="p-4 text-center"><h4 class="fw-bold">UniSalas</h4></div>
        @auth
        <div class="user-profile">
            <i class="fas fa-user-circle fa-2x mb-2"></i>
            <div class="small">Logado como:</div>
            <strong class="d-block">{{ auth()->user()->name }}</strong>
        </div>
        <div class="flex-grow-1">
            @if(auth()->user()->tipo == 'admin')
                <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home me-2"></i> Dashboard</a>
                <a href="{{ route('admin.usuarios') }}"><i class="fas fa-users me-2"></i> Usuários</a>
                <a href="{{ route('admin.blocos') }}"><i class="fas fa-building me-2"></i> Blocos e Salas</a>
                <a href="{{ route('admin.reservas') }}"><i class="fas fa-check-circle me-2"></i> Aprovar Reservas</a>
                <a href="{{ route('admin.historico') }}"><i class="fas fa-database me-2"></i> Histórico Geral</a>
            @else
                <a href="{{ route('professor.painel') }}"><i class="fas fa-tachometer-alt me-2"></i> Painel Principal</a>
                <a href="{{ route('professor.reservas') }}"><i class="fas fa-calendar-check me-2"></i> Minhas Reservas</a>
                <a href="{{ route('professor.solicitar') }}"><i class="fas fa-plus-circle me-2"></i> Solicitar Reserva</a>
                <a href="{{ route('professor.historico') }}"><i class="fas fa-history me-2"></i> Histórico</a>
            @endif
        </div>
        <div><a href="{{ route('logout') }}" class="text-white" style="background: #a31548; padding: 15px;"><i class="fas fa-sign-out-alt me-2"></i> Sair</a></div>
        @endauth
    </nav>
    <main class="main-content p-4">@yield('content')</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>