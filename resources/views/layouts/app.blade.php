<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniSalas - Sistema de Reservas</title>
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <style>
        :root {
            --brand: #1d9bf0;
            --brand-dark: #07111f;
            --brand-mid: #0d223a;
            --brand-soft: #102842;
            --accent: #28d7ff;
            --surface: #101d2f;
            --surface-2: #13263d;
            --line: #243b55;
            --text: #e8f2ff;
            --muted: #94a9c4;
        }

        body {
            color: var(--text);
            background: #08111f;
            font-size: 0.95rem;
            overflow-x: hidden;
            scrollbar-width: none;
        }
        body::-webkit-scrollbar,
        .main-content::-webkit-scrollbar,
        *::-webkit-scrollbar { width: 0; height: 0; }
        #sidebar {
            width: 260px;
            min-width: 260px;
            background: linear-gradient(180deg, var(--brand-dark), var(--brand-mid));
            min-height: 100vh;
            color: white;
            display: flex;
            flex-direction: column;
            box-shadow: 10px 0 28px rgba(12, 23, 42, 0.18);
        }
        #sidebar .brand-mark { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.12); }
        #sidebar a {
            color: rgba(255,255,255,0.88);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 4px 12px;
            padding: 12px 14px;
            border-radius: 8px;
            transition: 0.2s ease;
        }
        #sidebar a:hover { background: rgba(255,255,255,0.12); color: white; }
        #sidebar a.active {
            background: linear-gradient(90deg, #143a5d, #0f2c48);
            color: #dff6ff;
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(0, 166, 200, 0.18);
        }
        .user-profile {
            padding: 18px;
            background: rgba(0,0,0,0.12);
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }
        .main-content {
            flex-grow: 1;
            background:
                radial-gradient(circle at top right, rgba(40, 215, 255, 0.12), transparent 34rem),
                linear-gradient(180deg, #0b1626, #08111f);
            min-height: 100vh;
        }
        .page-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
        }
        .section-kicker {
            color: var(--accent);
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .metric-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: linear-gradient(180deg, #14263d, #101d2f);
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.18);
        }
        .metric-icon {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-primary {
            --bs-btn-bg: #1d9bf0;
            --bs-btn-border-color: #1d9bf0;
            --bs-btn-hover-bg: #1587d3;
            --bs-btn-hover-border-color: #1587d3;
        }
        .btn-outline-primary {
            --bs-btn-color: #5cc9ff;
            --bs-btn-border-color: #2d90cb;
            --bs-btn-hover-bg: #1d9bf0;
            --bs-btn-hover-border-color: #1d9bf0;
        }
        .text-primary { color: #5cc9ff !important; }
        .text-muted { color: var(--muted) !important; }
        .bg-primary-subtle { background-color: #123354 !important; }
        .bg-info-subtle { background-color: #123947 !important; }
        .bg-success-subtle { background-color: #14392f !important; }
        .bg-warning-subtle { background-color: #443617 !important; }
        .table { --bs-table-color: var(--text); --bs-table-bg: transparent; --bs-table-border-color: var(--line); }
        .table-light { --bs-table-color: #dcecff; --bs-table-bg: #152a43; --bs-table-border-color: var(--line); }
        .list-group-item {
            background: transparent;
            color: var(--text);
            border-color: var(--line);
        }
        .list-group-item-action:hover { background: #172d48; color: var(--text); }
        .form-control,
        .form-select {
            background-color: #0b1727;
            border-color: var(--line);
            color: var(--text);
        }
        .form-control:focus,
        .form-select:focus {
            background-color: #0d1b2d;
            color: var(--text);
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(40, 215, 255, 0.12);
        }
        .form-control::placeholder { color: #6f829d; }
        .tech-panel {
            background: linear-gradient(180deg, #13263d, #0f1c2f);
            border: 1px solid var(--line);
            border-radius: 8px;
        }
        .data-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #0b1727;
            border: 1px solid var(--line);
            color: #d9e9ff;
            font-size: 0.82rem;
        }
        .notification-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #f6c343;
            box-shadow: 0 0 0 6px rgba(246, 195, 67, 0.12);
        }
        @media (max-width: 900px) {
            .app-shell {
                flex-direction: column;
            }
            #sidebar {
                width: 100%;
                min-width: 100%;
                min-height: auto;
                box-shadow: none;
            }
            #sidebar .flex-grow-1 {
                display: flex;
                gap: 6px;
                overflow-x: auto;
                padding: 8px;
                scrollbar-width: none;
                -webkit-overflow-scrolling: touch;
            }
            #sidebar .flex-grow-1::-webkit-scrollbar { width: 0; height: 0; }
            #sidebar a {
                margin: 0;
                justify-content: center;
                text-align: center;
                padding: 10px;
            }
            .main-content {
                padding: 16px !important;
            }
        }
        @media (max-width: 520px) {
            #sidebar .flex-grow-1 {
                display: flex;
            }
            .page-card,
            .metric-card,
            .tech-panel {
                border-radius: 6px;
            }
        }
        @media (max-width: 768px) {
            body { font-size: 0.9rem; }
            .container-fluid { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
            .main-content { padding: 14px !important; }
            h1, .display-5 { font-size: 1.8rem; }
            h2 { font-size: 1.45rem; }
            h3, h4 { font-size: 1.2rem; }
            .page-card,
            .metric-card,
            .tech-panel { padding: 1rem !important; }
            .row.g-4 { --bs-gutter-y: 1rem; }
            .table {
                display: block;
                width: 100%;
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
            }
            .table th,
            .table td { vertical-align: middle; }
            .btn,
            .form-control,
            .form-select { min-height: 42px; }
            form .btn,
            form input[type="submit"] { white-space: normal; }
            .modal-dialog {
                margin: 0.75rem;
                max-width: calc(100% - 1.5rem);
            }
            .d-flex.flex-wrap,
            form.d-flex { gap: 0.5rem !important; }
            #sidebar {
                position: sticky;
                top: 0;
                z-index: 20;
            }
            #sidebar .brand-mark {
                padding: 14px 12px;
            }
            #sidebar .user-profile {
                display: none;
            }
            #sidebar .flex-grow-1 {
                display: flex;
                gap: 6px;
                overflow-x: auto;
                padding: 8px;
                scrollbar-width: none;
                -webkit-overflow-scrolling: touch;
            }
            #sidebar .flex-grow-1::-webkit-scrollbar { width: 0; height: 0; }
            #sidebar .flex-grow-1 a {
                flex: 0 0 auto;
                min-width: 128px;
                justify-content: center;
                white-space: nowrap;
                font-size: 0.84rem;
                line-height: 1.15;
                min-height: 46px;
                background: rgba(255,255,255,0.06);
            }
            #sidebar .pb-3 {
                padding: 0 8px 8px !important;
            }
            #sidebar .pb-3 a {
                justify-content: center;
                margin: 0;
            }
            .metric-card .display-6 {
                font-size: 1.6rem;
            }
            .professor-metrics,
            .dashboard-metrics {
                --bs-gutter-x: 0.55rem;
                --bs-gutter-y: 0.55rem;
            }
            .professor-metric,
            .dashboard-metric {
                min-height: 92px;
                padding: 0.75rem !important;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .professor-metric .small,
            .dashboard-metric .small {
                font-size: 0.72rem;
                line-height: 1.1;
            }
            .professor-metric .display-6,
            .dashboard-metric .display-6 {
                font-size: 1.45rem;
                line-height: 1;
                margin-top: 0.25rem;
            }
            .dashboard-metric .metric-icon {
                width: 34px;
                height: 34px;
                font-size: 0.85rem;
                flex: 0 0 auto;
                margin-left: 0.35rem;
            }
            .container-fluid > .d-flex,
            .container-fluid > .mb-4.d-flex {
                align-items: stretch !important;
                flex-direction: column;
                gap: 0.75rem !important;
            }
            .container-fluid > .d-flex .btn,
            .container-fluid > .mb-4.d-flex .btn {
                width: 100%;
            }
            .list-group-item,
            .contact-card {
                word-break: break-word;
            }
        }
        @media (max-width: 414px) {
            #sidebar .brand-mark { padding: 16px 12px; }
            .user-profile { padding: 12px; }
            #sidebar a {
                font-size: 0.86rem;
                padding: 9px 8px;
            }
            #sidebar .flex-grow-1 {
                display: flex;
            }
            .main-content { padding: 10px !important; }
            .section-kicker { font-size: 0.68rem; }
            .btn { width: 100%; }
            .btn-sm { min-height: 38px; }
            .input-group {
                flex-direction: column;
                align-items: stretch;
            }
            .input-group > .form-control,
            .input-group > .btn {
                width: 100%;
                border-radius: 8px !important;
            }
        }
        @media (max-width: 375px) {
            body { font-size: 0.86rem; }
            .main-content { padding: 8px !important; }
            #sidebar .flex-grow-1 a {
                font-size: 0.8rem;
                padding: 8px 7px;
            }
            .page-card,
            .metric-card,
            .tech-panel {
                padding: 0.8rem !important;
            }
            .btn,
            .form-control,
            .form-select {
                font-size: 0.88rem;
            }
        }
        @media (max-width: 320px) {
            #sidebar .brand-mark h4 { font-size: 1rem; }
            #sidebar .flex-grow-1 {
                display: flex;
            }
            #sidebar .flex-grow-1 a {
                gap: 6px;
                min-width: 118px;
            }
            h2 { font-size: 1.25rem; }
            .section-kicker { font-size: 0.62rem; }
        }
    </style>
</head>
<body>
<div class="d-flex app-shell">
    <nav id="sidebar">
        <div class="brand-mark text-center">
            <h4 class="fw-bold mb-0">UniSalas</h4>
        </div>

        @auth
            <div class="user-profile">
                <i class="fas fa-user-circle fa-2x mb-2"></i>
                <div class="small opacity-75">Logado como</div>
                <strong class="d-block">{{ auth()->user()->name }}</strong>
            </div>

            <div class="flex-grow-1 py-2">
                @if(auth()->user()->tipo == 'admin')
                    @php
                        $novasReservasMenu = \App\Models\Reserva::whereIn('status', ['pendente', 'em_analise'])
                            ->get(['id'])
                            ->reject(fn($reserva) => \App\Models\NotificacaoVisualizada::where('user_id', auth()->id())
                                ->where('tipo', 'admin_reserva_pendente')
                                ->where('referencia', (string) $reserva->id)
                                ->exists())
                            ->count();
                    @endphp
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> In&iacute;cio
                    </a>
                    <a href="{{ route('admin.usuarios') }}" class="{{ request()->routeIs('admin.usuarios') || request()->routeIs('usuario.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Usu&aacute;rios
                    </a>
                    <a href="{{ route('admin.blocos') }}" class="{{ request()->routeIs('admin.blocos') || request()->routeIs('bloco.*') || request()->routeIs('sala.*') ? 'active' : '' }}">
                        <i class="fas fa-building"></i> Blocos e Salas
                    </a>
                    <a href="{{ route('admin.reservas') }}" class="{{ request()->routeIs('admin.reservas') ? 'active' : '' }}">
                        <i class="fas fa-check-circle"></i> Aprovar Reservas
                        @if($novasReservasMenu > 0)
                            <span class="notification-dot ms-auto"></span>
                        @endif
                    </a>
                    <a href="{{ route('admin.historico') }}" class="{{ request()->routeIs('admin.historico') ? 'active' : '' }}">
                        <i class="fas fa-database"></i> Hist&oacute;rico Geral
                    </a>
                    <a href="{{ route('mensagens.index') }}" class="{{ request()->routeIs('mensagens.*') || request()->routeIs('chat.*') ? 'active' : '' }}">
                        <i class="fas fa-comments"></i> Mensagens
                        @php
                            $menuMensagensNaoLidas = \App\Models\MensagemDireta::where('destinatario_id', auth()->id())->where('lida', false)->count();
                        @endphp
                        @if($menuMensagensNaoLidas > 0)
                            <span class="badge bg-danger ms-auto">{{ $menuMensagensNaoLidas }}</span>
                        @endif
                    </a>
                @else
                    @php
                        $professorMensagensNaoLidas = \App\Models\MensagemDireta::where('destinatario_id', auth()->id())->where('lida', false)->count();
                        $professorReservasRespondidas = \App\Models\Reserva::where('user_id', auth()->id())
                            ->whereIn('status', ['aprovada', 'rejeitada', 'cancelada'])
                            ->get(['id', 'updated_at'])
                            ->reject(fn($reserva) => \App\Models\NotificacaoVisualizada::where('user_id', auth()->id())
                                ->where('tipo', 'reserva_respondida')
                                ->where('referencia', $reserva->id . ':' . optional($reserva->updated_at)->timestamp)
                                ->exists())
                            ->count();
                    @endphp
                    <a href="{{ route('professor.painel') }}" class="{{ request()->routeIs('professor.painel') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> Painel Principal
                    </a>
                    <a href="{{ route('professor.reservas') }}" class="{{ request()->routeIs('professor.reservas') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i> Minhas Reservas
                        @if($professorReservasRespondidas > 0)
                            <span class="notification-dot ms-auto"></span>
                        @endif
                    </a>
                    <a href="{{ route('professor.solicitar') }}" class="{{ request()->routeIs('professor.solicitar') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i> Solicitar Reserva
                    </a>
                    <a href="{{ route('professor.historico') }}" class="{{ request()->routeIs('professor.historico') ? 'active' : '' }}">
                        <i class="fas fa-history"></i> Hist&oacute;rico
                    </a>
                    <a href="{{ route('mensagens.index') }}" class="{{ request()->routeIs('mensagens.*') || request()->routeIs('chat.*') ? 'active' : '' }}">
                        <i class="fas fa-comments"></i> Mensagens
                        @if($professorMensagensNaoLidas > 0)
                            <span class="badge bg-danger ms-auto">{{ $professorMensagensNaoLidas }}</span>
                        @endif
                    </a>
                @endif
            </div>

            <div class="pb-3">
                <a href="{{ route('logout') }}" class="text-white" style="background: rgba(163,21,72,0.95);">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        @endauth
    </nav>

    <main class="main-content p-4">
        @yield('content')
    </main>
</div>
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        background: '#101d2f',
        color: '#e8f2ff'
    });

    @if(session('success'))
        Toast.fire({ icon: 'success', title: @json(session('success')) });
    @endif

    @if(session('error'))
        Toast.fire({ icon: 'error', title: @json(session('error')) });
    @endif

    @if($errors->any())
        Toast.fire({ icon: 'error', title: @json($errors->first()) });
    @endif

    @auth
        @php
            $toastEventosPersistidos = [];
            $usuarioAtual = auth()->user();

            if ($usuarioAtual->tipo == 'admin' && !request()->routeIs('admin.reservas')) {
                $reservasPendentesToast = \App\Models\Reserva::whereIn('status', ['pendente', 'em_analise'])->latest()->limit(10)->get();
                $reservasNaoVisualizadas = $reservasPendentesToast->reject(fn($reserva) => \App\Models\NotificacaoVisualizada::where('user_id', $usuarioAtual->id)->where('tipo', 'admin_reserva_pendente')->where('referencia', (string) $reserva->id)->exists());

                if ($reservasNaoVisualizadas->isNotEmpty()) {
                    foreach ($reservasNaoVisualizadas as $reserva) {
                        \App\Models\NotificacaoVisualizada::firstOrCreate([
                            'user_id' => $usuarioAtual->id,
                            'tipo' => 'admin_reserva_pendente',
                            'referencia' => (string) $reserva->id,
                        ], ['visualizada_em' => now()]);
                    }
                    $toastEventosPersistidos[] = ['icon' => 'info', 'title' => 'Existem novas solicitações de reserva.'];
                }
            }

            if ($usuarioAtual->tipo != 'admin' && !request()->routeIs('chat.*')) {
                $mensagensToast = \App\Models\MensagemDireta::where('destinatario_id', $usuarioAtual->id)->where('lida', false)->latest()->limit(10)->get();
                $mensagensNaoVisualizadas = $mensagensToast->reject(fn($mensagem) => \App\Models\NotificacaoVisualizada::where('user_id', $usuarioAtual->id)->where('tipo', 'mensagem_recebida')->where('referencia', (string) $mensagem->id)->exists());

                if ($mensagensNaoVisualizadas->isNotEmpty()) {
                    foreach ($mensagensNaoVisualizadas as $mensagem) {
                        \App\Models\NotificacaoVisualizada::firstOrCreate([
                            'user_id' => $usuarioAtual->id,
                            'tipo' => 'mensagem_recebida',
                            'referencia' => (string) $mensagem->id,
                        ], ['visualizada_em' => now()]);
                    }
                    $toastEventosPersistidos[] = ['icon' => 'info', 'title' => 'Você tem mensagem nova.'];
                }
            }

            if ($usuarioAtual->tipo != 'admin' && !request()->routeIs('professor.reservas')) {
                $reservasRespondidasToast = \App\Models\Reserva::where('user_id', $usuarioAtual->id)->whereIn('status', ['aprovada', 'rejeitada', 'cancelada'])->latest('updated_at')->limit(10)->get();
                $reservasRespondidasNaoVisualizadas = $reservasRespondidasToast->reject(fn($reserva) => \App\Models\NotificacaoVisualizada::where('user_id', $usuarioAtual->id)->where('tipo', 'reserva_respondida')->where('referencia', $reserva->id . ':' . optional($reserva->updated_at)->timestamp)->exists());

                if ($reservasRespondidasNaoVisualizadas->isNotEmpty()) {
                    foreach ($reservasRespondidasNaoVisualizadas as $reserva) {
                        \App\Models\NotificacaoVisualizada::firstOrCreate([
                            'user_id' => $usuarioAtual->id,
                            'tipo' => 'reserva_respondida',
                            'referencia' => $reserva->id . ':' . optional($reserva->updated_at)->timestamp,
                        ], ['visualizada_em' => now()]);
                    }
                    $toastEventosPersistidos[] = ['icon' => 'success', 'title' => 'Uma solicitação de reserva foi respondida.'];
                }
            }
        @endphp

        @foreach($toastEventosPersistidos as $eventoPersistido)
            Toast.fire({ icon: @json($eventoPersistido['icon']), title: @json($eventoPersistido['title']) });
        @endforeach
    @endauth

    @if(false)
    @auth
        @if(auth()->user()->tipo == 'admin' && isset($novasReservasMenu) && $novasReservasMenu > 0 && !request()->routeIs('admin.reservas'))
            Toast.fire({ icon: 'info', title: 'Existem novas solicitações de reserva.' });
        @endif
        @if(auth()->user()->tipo != 'admin' && isset($professorMensagensNaoLidas) && $professorMensagensNaoLidas > 0 && !request()->routeIs('chat.*'))
            Toast.fire({ icon: 'info', title: 'Você tem mensagem nova.' });
        @endif
        @if(auth()->user()->tipo != 'admin' && isset($professorReservasRespondidas) && $professorReservasRespondidas > 0 && !request()->routeIs('professor.reservas'))
            Toast.fire({ icon: 'success', title: 'Uma solicitação de reserva foi respondida.' });
        @endif
    @endauth
    @endif
</script>
@if(app()->environment('local'))
<script>
    document.addEventListener('submit', function (event) {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || String(form.method).toLowerCase() !== 'post') {
            return;
        }

        event.preventDefault();

        const data = new FormData(form);
        const methodOverride = String(data.get('_method') || '').toUpperCase();
        let url = new URL(form.getAttribute('action') || window.location.href, window.location.href);

        if (methodOverride === 'DELETE' && /^\/chat\/[^/]+$/.test(url.pathname)) {
            url.pathname = url.pathname + '/apagar';
        }

        url.searchParams.set('__local_submit', '1');

        for (const [key, value] of data.entries()) {
            if (key === '_token' || key === '_method') {
                continue;
            }

            url.searchParams.append(key, value);
        }

        if (event.submitter && event.submitter.name && event.submitter.value) {
            url.searchParams.append(event.submitter.name, event.submitter.value);
        }

        window.location.assign(url.toString());
    }, true);
</script>
@endif
</body>
</html>
