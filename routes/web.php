<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, AdminController};

Route::get('/', function() { return redirect()->route('login'); });
Route::get('/login', [AuthController::class, 'telaLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/entrar', [AuthController::class, 'loginLocal'])->name('login.local');
Route::get('/demo-login/{perfil}', [AuthController::class, 'loginDemo'])->name('login.demo');
Route::get('/teste-post', function () {
    return <<<'HTML'
<!doctype html>
<html lang="pt-br">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Teste POST</title></head>
<body style="font-family:Arial;padding:24px">
    <h1>Teste POST</h1>
    <form method="post" action="/teste-post">
        <input type="hidden" name="_token" value="TOKEN">
        <input name="teste" value="celular">
        <button type="submit" style="display:block;margin-top:16px;padding:16px;width:100%">Enviar teste</button>
    </form>
</body>
</html>
HTML;
})->name('teste.post.form');
Route::post('/teste-post', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Log::info('POST teste recebido', [
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'input' => $request->except('_token'),
    ]);

    return 'POST recebido em '.now()->format('H:i:s');
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    
    // Rotas de Mensagens (Fora do prefixo admin)
    Route::get('/mensagens', [AdminController::class, 'verMensagens'])->name('mensagens.index');
    Route::get('/chat/{id}', [AdminController::class, 'chat'])->name('chat.show');
    Route::post('/mensagem/enviar', [AdminController::class, 'enviarMensagem'])->name('admin.enviarMensagem');
    Route::delete('/chat/{id}', [AdminController::class, 'apagarChat'])->name('chat.apagar');

    if (app()->environment('local')) {
        Route::get('/mensagem/enviar', [AdminController::class, 'enviarMensagem']);
        Route::get('/chat/{id}/apagar', [AdminController::class, 'apagarChat']);
    }

    // Rotas Admin
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        
        // USUÁRIOS
        Route::get('/usuarios', [AdminController::class, 'listarUsuarios'])->name('admin.usuarios');
        Route::get('/usuarios/novo', [AdminController::class, 'novoUsuario'])->name('usuario.novo');
        Route::post('/usuarios/salvar', [AdminController::class, 'salvarUsuario'])->name('usuario.salvar');
        Route::post('/usuarios/atualizar/{id}', [AdminController::class, 'atualizarUsuario'])->name('usuario.atualizar');
        Route::delete('/usuarios/excluir/{id}', [AdminController::class, 'excluirUsuario'])->name('usuario.excluir');

        // BLOCOS E SALAS
        Route::get('/blocos', [AdminController::class, 'listarBlocosSalas'])->name('admin.blocos');
        Route::get('/blocos/novo', [AdminController::class, 'cadastrarBloco'])->name('bloco.novo');
        Route::post('/blocos/salvar', [AdminController::class, 'salvarBloco'])->name('bloco.salvar');
        Route::get('/blocos/editar/{id}', [AdminController::class, 'editarBloco'])->name('bloco.editar');
        Route::post('/blocos/atualizar/{id}', [AdminController::class, 'atualizarBloco'])->name('bloco.atualizar');
        Route::post('/blocos/manutencao/{id}', [AdminController::class, 'atualizarManutencaoBloco'])->name('bloco.manutencao');
        Route::delete('/blocos/excluir/{id}', [AdminController::class, 'excluirBloco'])->name('bloco.excluir');
        
        Route::get('/sala/nova/{bloco_id}', [AdminController::class, 'novaSala'])->name('sala.nova');
        Route::post('/sala/salvar', [AdminController::class, 'salvarSala'])->name('sala.salvar');
        Route::get('/sala/editar/{id}', [AdminController::class, 'editarSala'])->name('sala.editar');
        Route::post('/sala/atualizar/{id}', [AdminController::class, 'atualizarSala'])->name('sala.atualizar');
        Route::post('/sala/manutencao/{id}', [AdminController::class, 'atualizarManutencaoSala'])->name('sala.manutencao');
        Route::delete('/sala/excluir/{id}', [AdminController::class, 'excluirSala'])->name('sala.excluir');

        // RESERVAS E AVISOS
        Route::get('/reservas', [AdminController::class, 'listarReservasPendentes'])->name('admin.reservas');
        Route::get('/historico-completo', [AdminController::class, 'historicoGeral'])->name('admin.historico');
        
        // ROTA  BLADE
        Route::post('/reservas/status/{id}', [AdminController::class, 'mudarStatusReserva'])->name('reserva.mudarStatus');
        Route::post('/reservas/grupo/{grupo}/status', [AdminController::class, 'mudarStatusGrupoReservas'])->name('reserva.grupoStatus');
        Route::post('/reservas/cancelar/{id}', [AdminController::class, 'cancelarReserva'])->name('reserva.cancelar');
        
        Route::post('/avisos/salvar', [AdminController::class, 'salvarAviso'])->name('admin.salvarAviso');
        Route::delete('/avisos/excluir/{id}', [AdminController::class, 'excluirAviso'])->name('admin.excluirAviso');

        if (app()->environment('local')) {
            Route::get('/usuarios/salvar', [AdminController::class, 'salvarUsuario']);
            Route::get('/usuarios/atualizar/{id}', [AdminController::class, 'atualizarUsuario']);
            Route::get('/usuarios/excluir/{id}', [AdminController::class, 'excluirUsuario']);

            Route::get('/blocos/salvar', [AdminController::class, 'salvarBloco']);
            Route::get('/blocos/atualizar/{id}', [AdminController::class, 'atualizarBloco']);
            Route::get('/blocos/manutencao/{id}', [AdminController::class, 'atualizarManutencaoBloco']);
            Route::get('/blocos/excluir/{id}', [AdminController::class, 'excluirBloco']);

            Route::get('/sala/salvar', [AdminController::class, 'salvarSala']);
            Route::get('/sala/atualizar/{id}', [AdminController::class, 'atualizarSala']);
            Route::get('/sala/manutencao/{id}', [AdminController::class, 'atualizarManutencaoSala']);
            Route::get('/sala/excluir/{id}', [AdminController::class, 'excluirSala']);

            Route::get('/reservas/status/{id}', [AdminController::class, 'mudarStatusReserva']);
            Route::get('/reservas/grupo/{grupo}/status', [AdminController::class, 'mudarStatusGrupoReservas']);
            Route::get('/reservas/cancelar/{id}', [AdminController::class, 'cancelarReserva']);

            Route::get('/avisos/salvar', [AdminController::class, 'salvarAviso']);
            Route::get('/avisos/excluir/{id}', [AdminController::class, 'excluirAviso']);
        }
    });

    // Rotas Professor
    Route::prefix('professor')->group(function () {
        Route::get('/painel', [AdminController::class, 'painelProfessor'])->name('professor.painel');
        Route::get('/minhas-reservas', [AdminController::class, 'minhasReservas'])->name('professor.reservas');
        Route::get('/solicitar', [AdminController::class, 'solicitarReserva'])->name('professor.solicitar');
        Route::post('/solicitar', [AdminController::class, 'salvarReserva'])->name('professor.salvar');
        Route::get('/historico', [AdminController::class, 'professorHistorico'])->name('professor.historico');
        Route::delete('/desistir/{id}', [AdminController::class, 'desistirReserva'])->name('professor.desistir');
        Route::get('/api/salas/{bloco_id}', [AdminController::class, 'getSalasPorBloco']);
        Route::get('/verificar', [AdminController::class, 'verificarDisponibilidade']);

        if (app()->environment('local')) {
            Route::get('/desistir/{id}', [AdminController::class, 'desistirReserva']);
        }
    });
});
