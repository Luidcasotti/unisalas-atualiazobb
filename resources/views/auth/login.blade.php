<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniSalas - Login</title>
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <style>
        :root {
            --brand: #1d9bf0;
            --accent: #28d7ff;
            --surface: #101d2f;
            --line: #243b55;
            --text: #e8f2ff;
            --muted: #94a9c4;
        }

        body {
            min-height: 100vh;
            color: var(--text);
            background:
                radial-gradient(circle at 18% 18%, rgba(40, 215, 255, 0.18), transparent 28rem),
                radial-gradient(circle at 82% 12%, rgba(29, 155, 240, 0.18), transparent 24rem),
                linear-gradient(135deg, #07111f, #0b1626 48%, #08111f);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-shell {
            width: min(960px, 100%);
            display: grid;
            grid-template-columns: 1fr 420px;
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 28px 80px rgba(0, 0, 0, 0.36);
            background: rgba(16, 29, 47, 0.82);
            backdrop-filter: blur(12px);
        }

        .login-brand {
            padding: 48px;
            background: linear-gradient(160deg, rgba(29, 155, 240, 0.18), rgba(16, 29, 47, 0.42));
            border-right: 1px solid var(--line);
        }

        .login-card { padding: 42px; }
        .form-control {
            background: #0b1727;
            border-color: var(--line);
            color: var(--text);
            padding: 12px 14px;
        }
        .form-control:focus {
            background: #0d1b2d;
            color: var(--text);
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(40, 215, 255, 0.12);
        }
        .form-control::placeholder { color: #6f829d; }
        .text-muted { color: var(--muted) !important; }

        @media (max-width: 840px) {
            .login-shell { grid-template-columns: 1fr; }
            .login-brand { border-right: 0; border-bottom: 1px solid var(--line); }
        }

        @media (max-width: 520px) {
            body {
                align-items: flex-start;
                padding: 12px;
            }
            .login-shell {
                border-radius: 10px;
            }
            .login-brand,
            .login-card {
                padding: 24px;
            }
            .login-brand h1 {
                font-size: 2rem;
            }
            .login-brand .fs-5 {
                font-size: 1rem !important;
            }
            .form-control,
            .btn,
            input[type="submit"] {
                min-height: 44px;
            }
        }

        @media (max-width: 375px) {
            .login-brand,
            .login-card {
                padding: 18px;
            }
            .login-brand h1 {
                font-size: 1.7rem;
            }
            .login-card h2 {
                font-size: 1.35rem;
            }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="login-brand">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-4" style="border:1px solid var(--line); background:#0b1727;">
                <i class="fas fa-door-open text-info"></i>
                <span class="small text-muted">Sistema de reservas</span>
            </div>
            <h1 class="fw-bold display-5 mb-3">UniSalas</h1>
            <p class="text-muted fs-5 mb-4">Controle de salas, solicita&ccedil;&otilde;es e comunica&ccedil;&atilde;o entre professores e administra&ccedil;&atilde;o.</p>
            <div class="d-grid gap-2">
                <div><i class="fas fa-check-circle text-info me-2"></i>Reservas com acompanhamento em tempo real</div>
                <div><i class="fas fa-check-circle text-info me-2"></i>Fluxo de aprova&ccedil;&atilde;o organizado</div>
                <div><i class="fas fa-check-circle text-info me-2"></i>Hist&oacute;rico e mensagens integrados</div>
            </div>
        </section>

        <section class="login-card">
            <h2 class="fw-bold mb-1">Acessar conta</h2>
            <p class="text-muted mb-4">Entre com seu e-mail e senha institucional.</p>

            @if($errors->any())
                <div class="alert alert-danger border-0">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ app()->environment('local') ? route('login.local', [], false) : route('login.post', [], false) }}" method="{{ app()->environment('local') ? 'GET' : 'POST' }}" novalidate>
                @unless(app()->environment('local'))
                    @csrf
                @endunless
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="text" name="email" class="form-control" value="{{ old('email') }}" placeholder="seu@email.com" autocomplete="username">
                </div>
                <div class="mb-4">
                    <label class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" placeholder="Digite sua senha" autocomplete="current-password">
                </div>
                <input type="submit" class="btn btn-primary w-100 py-3 fw-bold" value="Entrar no sistema">
            </form>
        </section>
    </main>
</body>
</html>
