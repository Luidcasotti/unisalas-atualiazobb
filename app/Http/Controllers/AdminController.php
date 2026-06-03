<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Bloco, Sala, Reserva, Aviso, MensagemDireta, NotificacaoVisualizada};
use Illuminate\Support\Facades\{Hash, Auth, DB};
use Illuminate\Support\Str;

class AdminController extends Controller 
{
    private function ordenarSalas($salas)
    {
        return $salas->sortBy(function ($sala) {
            preg_match('/\d+/', $sala->nome ?? '', $matches);

            return [
                empty($matches) ? 1 : 0,
                empty($matches) ? PHP_INT_MAX : (int) $matches[0],
                mb_strtolower($sala->nome ?? ''),
            ];
        })->values();
    }

    private function limparDadosAntigos(): void
    {
        MensagemDireta::where('created_at', '<', now()->subMonths(6))->delete();
        Reserva::where('data_reserva', '<', now()->subMonths(6)->toDateString())->delete();
    }

    private function manutencaoAtivaParaData(Sala $sala, string $data): ?string
    {
        $sala->loadMissing('bloco');

        if ($sala->manutencao_ativa && ($sala->manutencao_indeterminada || !$sala->manutencao_fim || $data <= $sala->manutencao_fim)) {
            return 'Sala em manutencao' . ($sala->manutencao_aviso ? ': ' . $sala->manutencao_aviso : '.');
        }

        if ($sala->bloco && $sala->bloco->manutencao_ativa && ($sala->bloco->manutencao_indeterminada || !$sala->bloco->manutencao_fim || $data <= $sala->bloco->manutencao_fim)) {
            return 'Bloco em manutencao' . ($sala->bloco->manutencao_aviso ? ': ' . $sala->bloco->manutencao_aviso : '.');
        }

        return null;
    }

    private function cancelarReservasPorManutencao($query, string $motivo, ?string $fim = null): void
    {
        $query->where('data_reserva', '>=', now()->toDateString())
            ->when($fim, fn($q) => $q->where('data_reserva', '<=', $fim))
            ->whereIn('status', ['pendente', 'em_analise', 'aprovada'])
            ->update([
                'status' => 'cancelada',
                'comentario_adm' => $motivo,
            ]);
    }

    private function existeReservaConcorrente(int $salaId, string $data, string $periodo, bool $bloquear = false): ?Reserva
    {
        $sala = Sala::with('bloco')->find($salaId);

        if ($sala && $this->manutencaoAtivaParaData($sala, $data)) {
            $reserva = new Reserva([
                'sala_id' => $salaId,
                'data_reserva' => $data,
                'periodo' => $periodo,
                'status' => 'manutencao',
            ]);
            $reserva->setRelation('sala', $sala);
            return $reserva;
        }

        $query = Reserva::where('sala_id', $salaId)
            ->where('data_reserva', $data)
            ->where('periodo', $periodo)
            ->whereIn('status', ['pendente', 'em_analise', 'aprovada'])
            ->orderBy('created_at');

        if ($bloquear) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    private function cancelarConflitosDaReserva(Reserva $reserva): void
    {
        Reserva::where('id', '!=', $reserva->id)
            ->where('sala_id', $reserva->sala_id)
            ->where('data_reserva', $reserva->data_reserva)
            ->where('periodo', $reserva->periodo)
            ->whereIn('status', ['pendente', 'em_analise'])
            ->update([
                'status' => 'cancelada',
                'comentario_adm' => 'Sala ocupada nesse horario. Outra solicitacao para essa sala, data e periodo foi aprovada.',
            ]);
    }

    private function aprovarPendentesExpiradas(): void
    {
        Reserva::where('status', 'pendente')
            ->where('created_at', '<=', now()->subHours(12))
            ->get()
            ->each(function (Reserva $reserva) {
                $reserva->update([
                    'status' => 'aprovada',
                    'comentario_adm' => $reserva->comentario_adm ?: 'Aprovada automaticamente: prazo de 12 horas sem resposta do administrador.',
                ]);

                $this->cancelarConflitosDaReserva($reserva);
            });
    }

    private function definirStatusInicialReserva(int $salaId, string $data, string $periodo, bool $recorrente, bool $bloquear = false): array
    {
        $concorrente = $this->existeReservaConcorrente($salaId, $data, $periodo, $bloquear);

        if ($concorrente && $concorrente->status === 'manutencao') {
            $sala = $concorrente->sala;
            return [
                'status' => 'cancelada',
                'comentario_adm' => $sala ? $this->manutencaoAtivaParaData($sala, $data) : 'Sala ou bloco em manutencao.',
            ];
        }

        if ($concorrente && $recorrente) {
            $concorrente->loadMissing(['user', 'sala']);

            return [
                'status' => 'em_analise',
                'comentario_adm' => 'Conflito nesta data: ja existe uma solicitacao/reserva de ' .
                    ($concorrente->user->name ?? 'outro professor') .
                    ' para ' . ($concorrente->sala->nome ?? 'esta sala') .
                    ' em ' . date('d/m/Y', strtotime($concorrente->data_reserva)) .
                    ' no periodo ' . $concorrente->periodo .
                    ' com status ' . $concorrente->status . '.',
            ];
        }

        if ($concorrente) {
            return [
                'status' => 'cancelada',
                'comentario_adm' => 'Sala ocupada nesse horario. Ja existe uma solicitacao ou reserva para a mesma sala, data e periodo.',
            ];
        }

        if ($data === now()->toDateString()) {
            return [
                'status' => 'aprovada',
                'comentario_adm' => 'Aprovada automaticamente: solicitacao realizada no mesmo dia da reserva.',
            ];
        }

        return [
            'status' => 'pendente',
            'comentario_adm' => null,
        ];
    }

    private function gerarDatasRecorrentes(array $datasBase): \Illuminate\Support\Collection
    {
        return collect($datasBase)
            ->filter()
            ->flatMap(function ($dataBase) {
                $data = \Carbon\Carbon::parse($dataBase)->startOfDay();
                $fim = $data->copy()->addMonths(3);
                $datas = [];

                while ($data->lte($fim)) {
                    $datas[] = $data->toDateString();
                    $data->addWeek();
                }

                return $datas;
            })
            ->unique()
            ->sort()
            ->values();
    }

    private function marcarReservasPendentesVisualizadasPeloAdmin(): void
    {
        Reserva::whereIn('status', ['pendente', 'em_analise'])
            ->get(['id'])
            ->each(function (Reserva $reserva) {
                NotificacaoVisualizada::firstOrCreate([
                    'user_id' => Auth::id(),
                    'tipo' => 'admin_reserva_pendente',
                    'referencia' => (string) $reserva->id,
                ], [
                    'visualizada_em' => now(),
                ]);
            });
    }

    private function marcarReservasRespondidasVisualizadasPeloProfessor(): void
    {
        Reserva::where('user_id', Auth::id())
            ->whereIn('status', ['aprovada', 'rejeitada', 'cancelada'])
            ->get(['id', 'updated_at'])
            ->each(function (Reserva $reserva) {
                NotificacaoVisualizada::firstOrCreate([
                    'user_id' => Auth::id(),
                    'tipo' => 'reserva_respondida',
                    'referencia' => $reserva->id . ':' . optional($reserva->updated_at)->timestamp,
                ], [
                    'visualizada_em' => now(),
                ]);
            });
    }

    public function index() {
        $this->limparDadosAntigos();

        return view('admin.dashboard', [
            'totalUsuarios' => User::count(),
            'totalSalas' => Sala::count(),
            'totalBlocos' => Bloco::count(),
            'reservasPendentes' => Reserva::where('status', 'pendente')->count(),
            'reservasAprovadas' => Reserva::where('status', 'aprovada')->count(),
            'avisos' => Aviso::latest()->get(),
            // Adicionado para o botão de mensagens no dashboard
            'mensagensNaoLidas' => MensagemDireta::where('destinatario_id', Auth::id())->where('lida', false)->count()
        ]);
    }

    public function listarUsuarios() {
        $this->limparDadosAntigos();

        return view('admin.usuarios.index', [
            'usuarios' => User::with(['reservas' => function ($query) {
                $query->with('sala.bloco')->latest();
            }])->get()
        ]);
    }
    public function novoUsuario() { return view('admin.usuarios.create'); }
    public function salvarUsuario(Request $request) {
        User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password), 'tipo' => $request->tipo]);
        return redirect()->route('admin.usuarios')->with('success', 'Usuário criado com sucesso!');
    }
    public function atualizarUsuario(Request $request, $id) {
        $usuario = User::findOrFail($id);

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $usuario->name = $dados['name'];
        $usuario->email = $dados['email'];

        if (!empty($dados['password'])) {
            $usuario->password = Hash::make($dados['password']);
        }

        $usuario->save();

        return back()->with('success', 'Perfil atualizado sem alterar as reservas do usuario.');
    }

    public function excluirUsuario($id) {
        $usuario = User::findOrFail($id);

        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'Voce nao pode excluir o seu proprio usuario enquanto esta logado.');
        }

        Reserva::where('user_id', $usuario->id)->delete();
        MensagemDireta::where('remetente_id', $usuario->id)
            ->orWhere('destinatario_id', $usuario->id)
            ->delete();

        $usuario->delete();

        return back()->with('success', 'Usuario excluido. Reservas e chats vinculados foram liberados/removidos.');
    }

    public function listarBlocosSalas() {
        $blocos = Bloco::with('salas')->get()->map(function ($bloco) {
            $bloco->setRelation('salas', $this->ordenarSalas($bloco->salas));
            return $bloco;
        });

        return view('admin.blocos-salas.index', ['blocos' => $blocos]);
    }
    public function cadastrarBloco() { return view('admin.blocos-salas.blocos-create'); }
    public function salvarBloco(Request $request) { Bloco::create(['nome' => $request->nome, 'cor' => $request->cor]); return redirect()->route('admin.blocos'); }
    public function editarBloco($id) { return view('admin.blocos-salas.blocos-edit', ['bloco' => Bloco::findOrFail($id)]); }
    public function atualizarBloco(Request $request, $id) { Bloco::findOrFail($id)->update(['nome' => $request->nome, 'cor' => $request->cor]); return redirect()->route('admin.blocos'); }
    public function excluirBloco($id) { Bloco::findOrFail($id)->delete(); return back(); }
    public function excluirSala($id) { Sala::findOrFail($id)->delete(); return back(); }
    
    public function novaSala($bloco_id) { return view('admin.blocos-salas.salas-create', ['bloco_id' => $bloco_id]); }
    public function salvarSala(Request $request) {
        Sala::create(['bloco_id' => $request->bloco_id, 'nome' => $request->nome, 'observacao' => $request->observacao]);
        return redirect()->route('admin.blocos')->with('success', 'Sala criada com sucesso!');
    }
    public function editarSala($id) { return view('admin.blocos-salas.salas-edit', ['sala' => Sala::findOrFail($id)]); }
    public function atualizarSala(Request $request, $id) {
        Sala::findOrFail($id)->update(['nome' => $request->nome, 'observacao' => $request->observacao]);
        return redirect()->route('admin.blocos');
    }

    public function atualizarManutencaoBloco(Request $request, $id) {
        $bloco = Bloco::findOrFail($id);
        $ativa = $request->boolean('manutencao_ativa');
        $indeterminada = $request->boolean('manutencao_indeterminada');

        $request->validate([
            'manutencao_fim' => [$ativa && !$indeterminada ? 'required' : 'nullable', 'nullable', 'date', 'after_or_equal:today'],
            'manutencao_aviso' => ['nullable', 'string', 'max:1000'],
        ]);

        $bloco->update([
            'manutencao_ativa' => $ativa,
            'manutencao_indeterminada' => $ativa && $indeterminada,
            'manutencao_fim' => $ativa && !$indeterminada ? $request->manutencao_fim : null,
            'manutencao_aviso' => $ativa ? $request->manutencao_aviso : null,
        ]);

        if ($ativa) {
            $fim = $indeterminada ? null : $request->manutencao_fim;
            $motivo = 'Bloco em manutencao' . ($request->manutencao_aviso ? ': ' . $request->manutencao_aviso : '.');
            $salaIds = Sala::where('bloco_id', $bloco->id)->pluck('id');
            $this->cancelarReservasPorManutencao(Reserva::whereIn('sala_id', $salaIds), $motivo, $fim);
        }

        return back()->with('success', $ativa ? 'Bloco colocado em manutencao e reservas do periodo canceladas.' : 'Manutencao do bloco removida.');
    }

    public function atualizarManutencaoSala(Request $request, $id) {
        $sala = Sala::findOrFail($id);
        $ativa = $request->boolean('manutencao_ativa');
        $indeterminada = $request->boolean('manutencao_indeterminada');

        $request->validate([
            'manutencao_fim' => [$ativa && !$indeterminada ? 'required' : 'nullable', 'nullable', 'date', 'after_or_equal:today'],
            'manutencao_aviso' => ['nullable', 'string', 'max:1000'],
        ]);

        $sala->update([
            'manutencao_ativa' => $ativa,
            'manutencao_indeterminada' => $ativa && $indeterminada,
            'manutencao_fim' => $ativa && !$indeterminada ? $request->manutencao_fim : null,
            'manutencao_aviso' => $ativa ? $request->manutencao_aviso : null,
        ]);

        if ($ativa) {
            $fim = $indeterminada ? null : $request->manutencao_fim;
            $motivo = 'Sala em manutencao' . ($request->manutencao_aviso ? ': ' . $request->manutencao_aviso : '.');
            $this->cancelarReservasPorManutencao(Reserva::where('sala_id', $sala->id), $motivo, $fim);
        }

        return back()->with('success', $ativa ? 'Sala colocada em manutencao e reservas do periodo canceladas.' : 'Manutencao da sala removida.');
    }

    public function listarReservasPendentes() {
        $this->limparDadosAntigos();
        $this->aprovarPendentesExpiradas();
        $this->marcarReservasPendentesVisualizadasPeloAdmin();

        return view('admin.reservas.aprovar', ['reservas' => Reserva::whereIn('status', ['pendente', 'em_analise'])->with(['user', 'sala.bloco'])->get()]); 
    }

    public function mudarStatusReserva(Request $request, $id) { 
        $request->validate([
            'status' => ['required', 'in:aprovada,rejeitada'],
            'comentario_adm' => ['nullable', 'string', 'max:1000'],
            'open_group' => ['nullable', 'string', 'max:255'],
        ]);

        $reserva = Reserva::findOrFail($id);

        $reserva->update([
            'status' => $request->status, 
            'comentario_adm' => $request->comentario_adm
        ]); 

        if ($request->status === 'aprovada') {
            $this->cancelarConflitosDaReserva($reserva);
        }

        return back()->with('open_group', $request->open_group); 
    }

    public function mudarStatusGrupoReservas(Request $request, string $grupo)
    {
        $request->validate([
            'status' => ['required', 'in:aprovada,rejeitada'],
            'comentario_adm' => ['nullable', 'string', 'max:1000'],
            'open_group' => ['nullable', 'string', 'max:255'],
        ]);

        $reservas = Reserva::where('grupo_recorrencia', $grupo)
            ->whereIn('status', ['pendente', 'em_analise'])
            ->orderBy('data_reserva')
            ->get();

        foreach ($reservas as $reserva) {
            $reserva->update([
                'status' => $request->status,
                'comentario_adm' => $request->comentario_adm,
            ]);

            if ($request->status === 'aprovada') {
                $this->cancelarConflitosDaReserva($reserva);
            }
        }

        return back()
            ->with('success', $reservas->count() . ' reserva(s) recorrente(s) atualizada(s).')
            ->with('open_group', $request->open_group);
    }

    public function cancelarReserva(Request $request, $id) {
        $request->validate([
            'comentario_adm' => ['required', 'string', 'max:1000'],
        ]);

        Reserva::findOrFail($id)->update([
            'status' => 'cancelada',
            'comentario_adm' => $request->comentario_adm,
        ]);

        return back()->with('success', 'Reserva cancelada e motivo enviado ao professor.');
    }
    
    public function historicoGeral(Request $request) {
        $this->limparDadosAntigos();

        $query = Reserva::query()
            ->with(['user', 'sala.bloco'])
            ->whereNotIn('status', ['pendente', 'em_analise']);

        if ($request->filled('data')) {
            $query->where('data_reserva', $request->data);
        }

        if ($request->filled('bloco_id')) {
            $query->whereHas('sala', function ($salaQuery) use ($request) {
                $salaQuery->where('bloco_id', $request->bloco_id);
            });
        }

        if ($request->filled('sala_id')) {
            $query->where('sala_id', $request->sala_id);
        }

        if ($request->filled('periodo')) {
            $query->where('periodo', $request->periodo);
        }

        return view('admin.historico', [
            'reservas' => $query->latest()->get(),
            'blocos' => Bloco::with('salas')->orderBy('nome')->get(),
            'salas' => Sala::with('bloco')->orderBy('nome')->get(),
            'periodos' => Reserva::whereNotNull('periodo')->distinct()->orderBy('periodo')->pluck('periodo'),
        ]);
    }

    public function painelProfessor() {
        $user = Auth::user();
        $minhasReservas = Reserva::where('user_id', $user->id)->get();
        $avisos = Aviso::where('created_at', '>=', now()->subHours(24))->get();
        
        return view('professor.painel', [
            'total'     => $minhasReservas->count(),
            'aprovadas' => $minhasReservas->where('status', 'aprovada')->count(),
            'pendentes' => $minhasReservas->whereIn('status', ['pendente', 'em_analise'])->count(),
            'recusadas' => $minhasReservas->whereIn('status', ['rejeitada', 'cancelada'])->count(),
            'lembrete'  => $minhasReservas->where('data_reserva', date('Y-m-d'))->where('status', 'aprovada')->first(),
            'avisos'    => $avisos
        ]);
    }
    
    public function minhasReservas() {
        $this->marcarReservasRespondidasVisualizadasPeloProfessor();

        return view('professor.minhas-reservas', [
            'reservas' => Reserva::where('user_id', Auth::id())->with('sala')->latest('data_reserva')->get()
        ]);
    }
    public function solicitarReserva(Request $request) {
        if (app()->environment('local') && $request->boolean('__local_submit')) {
            return $this->salvarReserva($request);
        }

        return view('professor.nova-reserva', ['blocos' => Bloco::orderBy('nome')->get()]);
    }
    public function professorHistorico() { return view('professor.historico', ['reservas' => Reserva::where('user_id', Auth::id())->with('sala')->get()]); }

    public function getSalasPorBloco($bloco_id) { return response()->json($this->ordenarSalas(Sala::where('bloco_id', $bloco_id)->get())); }
    public function verificarDisponibilidade(Request $request) {
        $reserva = $this->existeReservaConcorrente((int) $request->sala_id, $request->data, $request->periodo);

        return response()->json([
            'disponivel' => !$reserva,
            'mensagem' => $reserva
                ? ($reserva->status === 'manutencao'
                    ? $this->manutencaoAtivaParaData($reserva->sala, $request->data)
                    : 'Sala indisponivel: ja existe uma solicitacao ou reserva para essa sala, data e periodo.')
                : 'Sala disponivel para solicitacao.',
            'status' => $reserva?->status,
        ]);
    }
    public function salvarReserva(Request $request)
    {
        $recorrente = $request->boolean('recorrente');

        $request->validate([
            'sala_id' => ['required', 'exists:salas,id'],
            'periodo' => ['required'],
            'data_reserva' => [$recorrente ? 'nullable' : 'required', 'nullable', 'after_or_equal:today'],
            'datas_recorrentes' => [$recorrente ? 'required' : 'nullable', 'array'],
            'datas_recorrentes.*' => ['date', 'after_or_equal:today'],
        ]);

        $datas = $recorrente
            ? $this->gerarDatasRecorrentes($request->datas_recorrentes ?? [])
            : collect([$request->data_reserva]);

        $grupoRecorrencia = $recorrente ? (string) Str::uuid() : null;

        DB::transaction(function () use ($datas, $request, $recorrente, $grupoRecorrencia) {
            foreach ($datas as $data) {
                $regra = $this->definirStatusInicialReserva((int) $request->sala_id, $data, $request->periodo, $recorrente, true);

                Reserva::create([
                    'user_id' => Auth::id(),
                    'sala_id' => $request->sala_id,
                    'data_reserva' => $data,
                    'periodo' => $request->periodo,
                    'status' => $regra['status'],
                    'recorrente' => $recorrente,
                    'grupo_recorrencia' => $grupoRecorrencia,
                    'comentario_professor' => trim(($recorrente ? '[Solicitacao recorrente] ' : '') . ($request->comentario_professor ?? '')),
                    'comentario_adm' => $regra['comentario_adm'],
                ]);
            }
        });

        return redirect()->route('professor.reservas')->with('success', $recorrente ? 'Solicitacoes recorrentes enviadas!' : 'Solicitacao enviada!');
    }

    private function salvarReservaAntiga(Request $request) { 
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

    public function verMensagens() { 
        $this->limparDadosAntigos();

        $user = Auth::user();
        if ($user->tipo === 'admin') {
            $contatos = User::where('tipo', 'professor')->orderBy('name')->get();
        } else {
            $contatos = User::where('tipo', 'admin')->orderBy('name')->get();
        }
        return view('mensagens.index', ['contatos' => $contatos]);
    }

    public function chat($id) {
        $this->limparDadosAntigos();

        MensagemDireta::where('remetente_id', $id)->where('destinatario_id', Auth::id())->update(['lida' => true]);
        $outroUsuario = User::findOrFail($id);
        $mensagens = MensagemDireta::where(function($q) use ($id) {
            $q->where('remetente_id', Auth::id())->where('destinatario_id', $id);
        })->orWhere(function($q) use ($id) {
            $q->where('remetente_id', $id)->where('destinatario_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        return view('mensagens.chat', ['usuario' => $outroUsuario, 'mensagens' => $mensagens]);
    }

    public function salvarAviso(Request $request) {
        Aviso::create(['titulo' => $request->titulo, 'mensagem' => $request->mensagem]);
        return back()->with('success', 'Aviso publicado com sucesso!');
    }
    
    public function excluirAviso($id) {
        Aviso::findOrFail($id)->delete();
        return back()->with('success', 'Aviso removido!');
    }

    public function enviarMensagem(Request $request) {
        $request->validate([
            'destinatario_id' => ['required', 'exists:users,id'],
            'mensagem' => ['required', 'string', 'max:2000'],
        ]);

        MensagemDireta::create([
            'remetente_id' => Auth::id(),
            'destinatario_id' => $request->destinatario_id,
            'mensagem' => $request->mensagem,
            'lida' => false
        ]);

        return redirect()->route('chat.show', $request->destinatario_id)->with('success', 'Mensagem enviada!');
    }

    public function apagarChat($id) {
        User::findOrFail($id);

        MensagemDireta::where(function ($query) use ($id) {
            $query->where('remetente_id', Auth::id())->where('destinatario_id', $id);
        })->orWhere(function ($query) use ($id) {
            $query->where('remetente_id', $id)->where('destinatario_id', Auth::id());
        })->delete();

        return redirect()->route('mensagens.index')->with('success', 'Chat apagado com sucesso.');
    }
}
