<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, AdminController};

Route::get('/', function() { return redirect()->route('login'); });
Route::get('/login', [AuthController::class, 'telaLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        
        // USUÁRIOS
        Route::get('/usuarios', [AdminController::class, 'listarUsuarios'])->name('admin.usuarios');
        Route::get('/usuarios/novo', [AdminController::class, 'novoUsuario'])->name('usuario.novo');
        Route::post('/usuarios/salvar', [AdminController::class, 'salvarUsuario'])->name('usuario.salvar');
        Route::delete('/usuarios/excluir/{id}', [AdminController::class, 'excluirUsuario'])->name('usuario.excluir');

        // BLOCOS E SALAS
        Route::get('/blocos', [AdminController::class, 'listarBlocosSalas'])->name('admin.blocos');
        Route::get('/blocos/novo', [AdminController::class, 'cadastrarBloco'])->name('bloco.novo');
        Route::post('/blocos/salvar', [AdminController::class, 'salvarBloco'])->name('bloco.salvar');
        Route::get('/blocos/editar/{id}', [AdminController::class, 'editarBloco'])->name('bloco.editar');
        Route::match(['post', 'put'], '/blocos/atualizar/{id}', [AdminController::class, 'atualizarBloco'])->name('bloco.atualizar');
        Route::delete('/salas/excluir/{id}', [AdminController::class, 'excluirSala'])->name('sala.excluir');
        Route::delete('/admin/blocos/excluir/{id}', [AdminController::class, 'excluirBloco'])->name('bloco.excluir');

        // RESERVAS ADMIN
        // Adicione dentro do grupo admin
Route::get('/sala/nova/{bloco_id}', [AdminController::class, 'novaSala'])->name('sala.nova');
Route::post('/sala/salvar', [AdminController::class, 'salvarSala'])->name('sala.salvar');
Route::get('/sala/editar/{id}', [AdminController::class, 'editarSala'])->name('sala.editar');
Route::post('/sala/atualizar/{id}', [AdminController::class, 'atualizarSala'])->name('sala.atualizar');
        Route::get('/reservas', [AdminController::class, 'listarReservasPendentes'])->name('admin.reservas');
        Route::get('/historico-completo', [AdminController::class, 'historicoGeral'])->name('admin.historico');
        Route::post('/reserva/status/{id}/{status}', [AdminController::class, 'mudarStatusReserva'])->name('admin.reserva.status');
        Route::post('/admin/reservas/status/{id}', [AdminController::class, 'mudarStatusReserva'])->name('reserva.mudarStatus');
    });

    Route::prefix('professor')->group(function () {
        Route::get('/painel', [AdminController::class, 'painelProfessor'])->name('professor.painel');
        Route::get('/minhas-reservas', [AdminController::class, 'minhasReservas'])->name('professor.reservas');
        Route::get('/solicitar', [AdminController::class, 'solicitarReserva'])->name('professor.solicitar');
        Route::post('/solicitar', [AdminController::class, 'salvarReserva'])->name('professor.salvar');
        Route::get('/historico', [AdminController::class, 'professorHistorico'])->name('professor.historico');
        Route::delete('/desistir/{id}', [AdminController::class, 'desistirReserva'])->name('professor.desistir');
        Route::get('/api/salas/{bloco_id}', [AdminController::class, 'getSalasPorBloco']);
        Route::get('/verificar', [AdminController::class, 'verificarDisponibilidade']);
    });
});