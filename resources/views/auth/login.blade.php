<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #7010a8; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: white; padding: 40px; border-radius: 15px; width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
    </style>
</head>
<body>
    <div class="login-card text-center">
        <h3 class="fw-bold mb-4">Acesso UniNorte</h3>
        @if(session('erro')) <div class="alert alert-danger">{{ session('erro') }}</div> @endif
        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3 text-start">
                <label class="form-label fw-bold">E-mail:</label>
                <input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
            </div>
            <div class="mb-4 text-start">
                <label class="form-label fw-bold">Senha:</label>
                <input type="password" name="password" class="form-control" placeholder="******" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">ENTRAR NO SISTEMA</button>
        </form>
    </div>
</body>
</html>