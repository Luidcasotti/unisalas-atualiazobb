<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Bloco, Sala, Reserva};
use Illuminate\Support\Facades\{Hash, Auth, DB};

class AdminController extends Controller 
{
    public function index() {
        return view('admin_dashboard', [
            'totalUsuarios' => User::count(),
            'totalSalas' => Sala::count(),
            'totalBlocos' => Bloco::count(),
            'reservasPendentes' => Reserva::where('status', 'pendente')->count(),
            'reservasAprovadas' => Reserva::where('status', 'aprovada')->count()
        ]);
    }

    public function listarUsuarios() { return view('gerenciar_usuarios', ['usuarios' => User::all()]); }
    public function novoUsuario() { return view('cadastrar_usuario'); }
    public function salvarUsuario(Request $request) {
        User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password), 'tipo' => $request->tipo]);
        return redirect()->route('admin.usuarios')->with('success', 'Usuário criado com sucesso!');
    }
    public function excluirUsuario($id) { User::findOrFail($id)->delete(); return back(); }

    public function listarBlocosSalas() { return view('gerenciar-blocos-salas', ['blocos' => Bloco::with('salas')->get()]); }
    public function cadastrarBloco() { return view('cadastrar_bloco'); }
    public function salvarBloco(Request $request) { Bloco::create(['nome' => $request->nome, 'cor' => $request->cor]); return redirect()->route('admin.blocos'); }
    public function editarBloco($id) { return view('editar_bloco', ['bloco' => Bloco::findOrFail($id)]); }
    public function atualizarBloco(Request $request, $id) { Bloco::findOrFail($id)->update(['nome' => $request->nome, 'cor' => $request->cor]); return redirect()->route('admin.blocos'); }
    public function excluirBloco($id) { Bloco::findOrFail($id)->delete(); return back(); }
    public function excluirSala($id) { Sala::findOrFail($id)->delete(); return back(); }
    
    public function novaSala($bloco_id) { return view('cadastrar_sala', ['bloco_id' => $bloco_id]); }
    public function salvarSala(Request $request) {
        Sala::create(['bloco_id' => $request->bloco_id, 'nome' => $request->nome, 'observacao' => $request->observacao]);
        return redirect()->route('admin.blocos')->with('success', 'Sala criada com sucesso!');
    }
    public function editarSala($id) { return view('editar_sala', ['sala' => Sala::findOrFail($id)]); }
    public function atualizarSala(Request $request, $id) {
        Sala::findOrFail($id)->update(['nome' => $request->nome, 'observacao' => $request->observacao]);
        return redirect()->route('admin.blocos');
    }

    public function listarReservasPendentes() {
        // APROVAÇÃO AUTOMÁTICA DE 24H
        Reserva::where('status', 'pendente')
            ->where('created_at', '<=', now()->subHours(24))
            ->update([
                'status' => 'aprovada', 
                'comentario_adm' => 'Aprovado automaticamente (timeout 24h).'
            ]);

        return view('aprovar_reservas', ['reservas' => Reserva::where('status', 'pendente')->with(['user', 'sala'])->get()]); 
    }

    public function mudarStatusReserva(Request $request, $id) { 
        Reserva::findOrFail($id)->update([
            'status' => $request->status, 
            'comentario_adm' => $request->comentario_adm
        ]); 
        return back(); 
    }
    
    public function historicoGeral(Request $request) {
        $query = Reserva::query()->with(['user', 'sala']);
        if ($request->has('data') && $request->data != '') { $query->where('data_reserva', $request->data); }
        return view('admin_historico', ['reservas' => $query->latest()->get()]);
    }

    public function painelProfessor() {
        $user = Auth::user();
        $minhasReservas = Reserva::where('user_id', $user->id)->get();
        return view('painel_professor', [
            'total'     => $minhasReservas->count(),
            'aprovadas' => $minhasReservas->where('status', 'aprovada')->count(),
            'pendentes' => $minhasReservas->where('status', 'pendente')->count(),
            'recusadas' => $minhasReservas->where('status', 'rejeitada')->count(),
            'lembrete'  => $minhasReservas->where('data_reserva', date('Y-m-d'))->where('status', 'aprovada')->first()
        ]);
    }
    
    public function minhasReservas() { return view('minhas_reservas', ['reservas' => Reserva::where('user_id', Auth::id())->with('sala')->get()]); }
    public function solicitarReserva() { return view('nova_reserva', ['blocos' => Bloco::all()]); }
    public function professorHistorico() { return view('professor_historico', ['reservas' => Reserva::where('user_id', Auth::id())->with('sala')->get()]); }

    public function getSalasPorBloco($bloco_id) { return response()->json(Sala::where('bloco_id', $bloco_id)->get()); }
    public function verificarDisponibilidade(Request $request) {
        $existe = Reserva::where('sala_id', $request->sala_id)->where('data_reserva', $request->data)->where('periodo', $request->periodo)->where('status', 'aprovada')->exists();
        return response()->json(['disponivel' => !$existe]);
    }
    public function salvarReserva(Request $request) { 
        $request->validate(['sala_id' => 'required', 'data_reserva' => 'required|after_or_equal:today', 'periodo' => 'required']);
        Reserva::create([
            'user_id' => Auth::id(), 
            'sala_id' => $request->sala_id, 
            'data_reserva' => $request->data_reserva, 
            'periodo' => $request->periodo, 
            'status' => 'pendente',
            'comentario_professor' => $request->comentario_professor
        ]);
        return redirect()->route('professor.reservas')->with('success', 'Solicitação enviada!');
    }
    public function desistirReserva($id) {
        $reserva = Reserva::where('id', $id)->where('user_id', Auth::id())->first();
        if ($reserva) { $reserva->delete(); }
        return redirect()->route('professor.reservas')->with('success', 'Reserva cancelada!');
    }
}