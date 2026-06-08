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
        $reserva->loadMissing(['sala.bloco']);
        $local = trim(($reserva->sala->bloco->nome ?? 'Bloco nao informado') . ' - ' . ($reserva->sala->nome ?? 'Sala nao informada'));
        $data = $reserva->data_reserva ? date('d/m/Y', strtotime($reserva->data_reserva)) : 'data nao informada';

        Reserva::where('id', '!=', $reserva->id)
            ->where('sala_id', $reserva->sala_id)
            ->where('data_reserva', $reserva->data_reserva)
            ->where('periodo', $reserva->periodo)
            ->whereIn('status', ['pendente', 'em_analise'])
            ->update([
                'status' => 'cancelada',
                'comentario_adm' => "Cancelada automaticamente por conflito: {$local}, em {$data}, no periodo {$reserva->periodo}, foi aprovada para outra solicitacao.",
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
            $concorrente->sala?->loadMissing('bloco');

            return [
                'status' => 'em_analise',
                'comentario_adm' => 'Conflito nesta data: ja existe uma solicitacao/reserva para ' .
                    ((optional(optional($concorrente->sala)->bloco)->nome ?? 'bloco nao informado') . ' - ' . ($concorrente->sala->nome ?? 'esta sala')) .
                    ' em ' . date('d/m/Y', strtotime($concorrente->data_reserva)) .
                    ' no periodo ' . $concorrente->periodo .
                    ' com status ' . $concorrente->status . '.',
            ];
        }

        if ($concorrente) {
            $concorrente->loadMissing(['user', 'sala.bloco']);
            $local = ($concorrente->sala->bloco->nome ?? 'Bloco nao informado') . ' - ' . ($concorrente->sala->nome ?? 'Sala nao informada');

            return [
                'status' => 'cancelada',
                'comentario_adm' => 'Reserva cancelada por conflito: ' . $local .
                    ' ja possui solicitacao/reserva registrada' .
                    ' em ' . date('d/m/Y', strtotime($data)) .
                    ' no periodo ' . $periodo . '.',
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
            'totalSalas' => Sala::whereNull('arquivado_em')->count(),
            'totalBlocos' => Bloco::whereNull('arquivado_em')->count(),
            'reservasPendentes' => Reserva::where('status', 'pendente')->count(),
            'reservasAprovadas' => Reserva::where('status', 'aprovada')->count(),
            'reservasSemana' => Reserva::whereBetween('data_reserva', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])->count(),
            'solicitacoesPendentesRecentes' => Reserva::whereIn('status', ['pendente', 'em_analise'])->with(['user', 'sala.bloco'])->latest()->limit(5)->get(),
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
        $blocos = Bloco::whereNull('arquivado_em')
            ->with(['salas' => fn($query) => $query->whereNull('arquivado_em')])
            ->orderBy('nome')
            ->get()
            ->map(function ($bloco) {
            $bloco->setRelation('salas', $this->ordenarSalas($bloco->salas));
            return $bloco;
        });

        return view('admin.blocos-salas.index', ['blocos' => $blocos]);
    }
    public function cadastrarBloco() { return view('admin.blocos-salas.blocos-create'); }
    public function salvarBloco(Request $request) { Bloco::create(['nome' => $request->nome, 'cor' => $request->cor]); return redirect()->route('admin.blocos'); }
    public function editarBloco($id) { return view('admin.blocos-salas.blocos-edit', ['bloco' => Bloco::findOrFail($id)]); }
    public function atualizarBloco(Request $request, $id) { Bloco::findOrFail($id)->update(['nome' => $request->nome, 'cor' => $request->cor]); return redirect()->route('admin.blocos'); }
    public function excluirBloco($id) {
        $bloco = Bloco::findOrFail($id);

        DB::transaction(function () use ($bloco) {
            $salaIds = Sala::where('bloco_id', $bloco->id)->whereNull('arquivado_em')->pluck('id');

            if ($salaIds->isNotEmpty()) {
                Reserva::whereIn('sala_id', $salaIds)
                    ->where('data_reserva', '>=', now()->toDateString())
                    ->whereIn('status', ['pendente', 'em_analise', 'aprovada'])
                    ->update([
                        'status' => 'cancelada',
                        'comentario_adm' => 'Bloco excluido pelo administrador. As reservas futuras vinculadas foram canceladas para preservar o historico.',
                    ]);

                Sala::whereIn('id', $salaIds)->update(['arquivado_em' => now()]);
            }

            $bloco->update(['arquivado_em' => now()]);
        });

        return back()->with('success', 'Bloco excluido da lista ativa. O historico de reservas foi preservado.');
    }
    public function excluirSala($id) {
        $sala = Sala::findOrFail($id);

        DB::transaction(function () use ($sala) {
            Reserva::where('sala_id', $sala->id)
                ->where('data_reserva', '>=', now()->toDateString())
                ->whereIn('status', ['pendente', 'em_analise', 'aprovada'])
                ->update([
                    'status' => 'cancelada',
                    'comentario_adm' => 'Sala excluida pelo administrador. As reservas futuras vinculadas foram canceladas para preservar o historico.',
                ]);

            $sala->update(['arquivado_em' => now()]);
        });

        return back()->with('success', 'Sala excluida da lista ativa. O historico de reservas foi preservado.');
    }
    
    public function novaSala($bloco_id) {
        $bloco = Bloco::findOrFail($bloco_id);
        return view('admin.blocos-salas.salas-create', ['bloco_id' => $bloco_id, 'bloco' => $bloco]);
    }
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

        if ($request->filled('data_inicio')) {
            $query->where('data_reserva', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->where('data_reserva', '<=', $request->data_fim);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipo_reserva')) {
            $query->where('recorrente', $request->tipo_reserva === 'recorrente');
        }

        if ($request->filled('professor_id')) {
            $query->where('user_id', $request->professor_id);
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
            'professores' => User::where('tipo', 'professor')->orderBy('name')->get(),
            'periodos' => Reserva::whereNotNull('periodo')->distinct()->orderBy('periodo')->pluck('periodo'),
            'statusDisponiveis' => Reserva::whereNotIn('status', ['pendente', 'em_analise'])->distinct()->orderBy('status')->pluck('status'),
        ]);
    }

    public function painelProfessor() {
        $user = Auth::user();
        $minhasReservas = Reserva::where('user_id', $user->id)->with('sala.bloco')->get();
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
            'reservas' => Reserva::where('user_id', Auth::id())
                ->with('sala')
                ->orderByDesc('created_at')
                ->orderByDesc('data_reserva')
                ->get()
        ]);
    }
    public function solicitarReserva(Request $request) {
        if (app()->environment('local') && $request->boolean('__local_submit')) {
            return $this->salvarReserva($request);
        }

        $blocos = Bloco::whereNull('arquivado_em')
            ->with(['salas' => fn($query) => $query->whereNull('arquivado_em')])
            ->orderBy('nome')
            ->get();

        return view('professor.nova-reserva', ['blocos' => $blocos]);
    }
    public function professorHistorico() { return view('professor.historico', ['reservas' => Reserva::where('user_id', Auth::id())->with('sala')->get()]); }

    public function getSalasPorBloco($bloco_id) { return response()->json($this->ordenarSalas(Sala::where('bloco_id', $bloco_id)->whereNull('arquivado_em')->get())); }
    public function verificarDisponibilidade(Request $request) {
        $reserva = $this->existeReservaConcorrente((int) $request->sala_id, $request->data, $request->periodo);
        $reserva?->loadMissing(['user', 'sala.bloco']);
        $local = $reserva
            ? (($reserva->sala->bloco->nome ?? 'Bloco nao informado') . ' - ' . ($reserva->sala->nome ?? 'Sala nao informada'))
            : null;

        return response()->json([
            'disponivel' => !$reserva,
            'mensagem' => $reserva
                ? ($reserva->status === 'manutencao'
                    ? $this->manutencaoAtivaParaData($reserva->sala, $request->data)
                    : 'Indisponivel: ' . $local . ' ja possui solicitacao/reserva registrada em ' . date('d/m/Y', strtotime($request->data)) . ' no periodo ' . $request->periodo . '.')
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

        if (Sala::where('id', $request->sala_id)->whereNotNull('arquivado_em')->exists()) {
            return back()
                ->withErrors(['sala_id' => 'Esta sala foi excluida da lista ativa e nao aceita novas reservas. Escolha outra sala ativa.'])
                ->withInput();
        }

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
