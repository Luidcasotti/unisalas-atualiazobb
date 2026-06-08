@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4 text-center">
        <div class="section-kicker mb-2">Reserva de sala</div>
        <h2 class="fw-bold mb-1">Nova solicita&ccedil;&atilde;o</h2>
        <p class="text-muted mb-0">Selecione sala, data e per&iacute;odo para verificar disponibilidade antes de confirmar.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-7">
            <div class="page-card p-4">
                <form action="{{ route('professor.salvar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="recorrente" id="recorrente" value="0">

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Bloco</label>
                        <select id="bloco_id" class="form-select form-select-lg" onchange="carregarSalas(this.value)" required>
                            <option value="">Selecione um bloco...</option>
                            @foreach($blocos as $b)
                                <option value="{{ $b->id }}">{{ $b->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Sala</label>
                        <select name="sala_id" id="sala_id" class="form-select form-select-lg" required></select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Data</label>
                            <input type="date" name="data_reserva" id="data_reserva" class="form-control form-control-lg" min="{{ date('Y-m-d') }}" onchange="verificar()" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Per&iacute;odo</label>
                            <select name="periodo" id="periodo" class="form-select form-select-lg" onchange="verificar()" required>
                                <option value="">Escolha...</option>
                                <option value="Matutino">Matutino</option>
                                <option value="Vespertino">Vespertino</option>
                                <option value="Noturno">Noturno</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#boxRecorrente" onclick="ativarRecorrencia()">
                            <i class="fas fa-calendar-alt me-1"></i> Reservas recorrentes
                        </button>

                        <div class="collapse mt-3" id="boxRecorrente">
                            <div class="tech-panel p-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Escolher dia base da recorr&ecirc;ncia</label>
                                <div class="input-group mb-2">
                                    <input type="date" id="data_recorrente" class="form-control" min="{{ date('Y-m-d') }}">
                                    <button class="btn btn-primary" type="button" onclick="adicionarDataRecorrente()">Gerar 3 meses</button>
                                </div>
                                <div id="listaDatasRecorrentes" class="d-grid gap-2"></div>
                                <small class="text-muted d-block mt-2">Exemplo: se selecionar uma ter&ccedil;a-feira, o sistema solicitar&aacute; todas as ter&ccedil;as pelos pr&oacute;ximos 3 meses.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Observa&ccedil;&otilde;es para o ADM</label>
                        <textarea name="comentario_professor" class="form-control" rows="3" placeholder="Ex.: Preciso de projetor..."></textarea>
                    </div>

                    <div id="statusReserva" class="mb-4 p-3 rounded-3 text-center fw-bold" style="display:none;"></div>

                    <button type="submit" id="btnEnviar" class="btn btn-primary btn-lg w-100 fw-bold py-3" disabled>
                        Solicitar reserva
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div id="cardDescricao" class="page-card p-3 room-detail-card" style="display: none;">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Detalhes da sala</h5>
                <div id="infoSala" class="lh-lg text-muted"></div>
            </div>

            <div class="page-card p-4 mt-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Grade de disponibilidade</h5>
                        <p class="text-muted small mb-0">Proximos 7 dias da sala selecionada.</p>
                    </div>
                    <i class="fas fa-calendar-days text-primary"></i>
                </div>
                <div id="gradeDisponibilidade" class="availability-grid-placeholder">
                    Selecione bloco e sala para visualizar a grade.
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .availability-grid {
        display: grid;
        gap: 8px;
    }
    .availability-day {
        border: 1px solid var(--line);
        border-radius: 8px;
        background: var(--surface-2);
        padding: 10px;
    }
    .availability-date {
        color: var(--text);
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }
    .availability-periods {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 6px;
    }
    .availability-slot {
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 7px 5px;
        text-align: center;
        font-size: 0.78rem;
        background: var(--surface);
        cursor: pointer;
        transition: 0.16s ease;
    }
    .availability-slot.available {
        color: #117a3a;
        border-color: rgba(17, 122, 58, 0.35);
        background: rgba(25, 135, 84, 0.1);
    }
    .availability-slot.unavailable {
        color: #9a6700;
        border-color: rgba(154, 103, 0, 0.38);
        background: rgba(255, 193, 7, 0.12);
    }
    .availability-slot:hover {
        transform: translateY(-1px);
        background: rgba(29, 155, 240, 0.14);
    }
    .availability-grid-placeholder {
        border: 1px dashed var(--line);
        border-radius: 8px;
        padding: 18px;
        color: var(--muted);
        text-align: center;
    }
    .room-detail-card {
        height: auto;
    }
    .room-detail-card h5 {
        font-size: 1rem;
        margin-bottom: 8px !important;
    }
    .room-detail-card #infoSala {
        line-height: 1.45 !important;
        font-size: 0.92rem;
        white-space: pre-wrap;
    }
    html:not([data-theme="light"]) .availability-slot.available {
        color: #70e4a2;
        border-color: rgba(112, 228, 162, 0.38);
    }
    html:not([data-theme="light"]) .availability-slot.unavailable {
        color: #ffc767;
        border-color: rgba(255, 199, 103, 0.38);
    }
</style>

<script>
window.listaSalas = [];
window.datasRecorrentes = [];
const periodosGrade = ['Matutino', 'Vespertino', 'Noturno'];
const salasPorBlocoUrl = @json(url('/professor/api/salas'));
const verificarDisponibilidadeUrl = @json(url('/professor/verificar'));

async function carregarSalas(id) {
    const res = await fetch(`${salasPorBlocoUrl}/${id}`);
    const salas = await res.json();
    window.listaSalas = salas;
    let html = '<option value="">Selecione uma sala...</option>';
    salas.forEach(s => html += `<option value="${s.id}">${s.nome}</option>`);
    document.getElementById('sala_id').innerHTML = html;
    document.getElementById('cardDescricao').style.display = 'none';
    renderizarGradeDisponibilidade();
    verificar();
}

document.getElementById('sala_id').addEventListener('change', function() {
    const infoDiv = document.getElementById('infoSala');
    const card = document.getElementById('cardDescricao');
    const sala = window.listaSalas.find(s => s.id == this.value);

    if (sala && sala.observacao) {
        card.style.display = 'block';
        infoDiv.innerHTML = sala.observacao;
    } else {
        card.style.display = 'none';
    }

    verificar();
    renderizarGradeDisponibilidade();
});

function ativarRecorrencia() {
    document.getElementById('recorrente').value = '1';
    document.getElementById('data_reserva').required = false;
    verificar();
}

async function consultarDisponibilidade(data, periodoConsulta = null) {
    const sala = document.getElementById('sala_id').value;
    const periodo = periodoConsulta || document.getElementById('periodo').value;

    if (!sala || !data || !periodo) return null;

    const params = new URLSearchParams({ sala_id: sala, data, periodo });
    const res = await fetch(`${verificarDisponibilidadeUrl}?${params.toString()}`);
    return await res.json();
}

function formatarDataCurta(dataIso) {
    const data = new Date(dataIso + 'T00:00:00');
    return data.toLocaleDateString('pt-BR', { weekday: 'short', day: '2-digit', month: '2-digit' });
}

function selecionarHorarioGrade(data, periodo) {
    document.getElementById('data_reserva').value = data;
    document.getElementById('periodo').value = periodo;
    document.getElementById('recorrente').value = '0';
    verificar();
}

async function renderizarGradeDisponibilidade() {
    const sala = document.getElementById('sala_id').value;
    const grade = document.getElementById('gradeDisponibilidade');

    if (!sala) {
        grade.className = 'availability-grid-placeholder';
        grade.innerHTML = 'Selecione bloco e sala para visualizar a grade.';
        return;
    }

    grade.className = 'availability-grid-placeholder';
    grade.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Carregando disponibilidade...';

    const hoje = new Date();
    const dias = [];
    let offset = 0;

    while (dias.length < 7 && offset < 14) {
        const data = new Date(hoje);
        data.setDate(hoje.getDate() + offset);

        if (data.getDay() !== 0) {
            dias.push(data.toISOString().slice(0, 10));
        }

        offset++;
    }

    const linhas = [];

    for (const data of dias) {
        const slots = [];
        const diaSemana = new Date(data + 'T00:00:00').getDay();
        const periodosDoDia = diaSemana === 6
            ? periodosGrade.filter((periodo) => periodo !== 'Noturno')
            : periodosGrade;

        for (const periodo of periodosDoDia) {
            const disponibilidade = await consultarDisponibilidade(data, periodo);

            const disponivel = disponibilidade?.disponivel;
            slots.push(`
                <button type="button" class="availability-slot ${disponivel ? 'available' : 'unavailable'}" onclick="selecionarHorarioGrade('${data}', '${periodo}')">
                    ${periodo}<br>
                    <strong>${disponivel ? 'Livre' : 'Ocupada'}</strong>
                </button>
            `);
        }

        linhas.push(`
            <div class="availability-day">
                <div class="availability-date">${formatarDataCurta(data)}</div>
                <div class="availability-periods">${slots.join('')}</div>
            </div>
        `);
    }

    grade.className = 'availability-grid';
    grade.innerHTML = linhas.join('');
}

async function adicionarDataRecorrente() {
    ativarRecorrencia();

    const input = document.getElementById('data_recorrente');
    const data = input.value;

    if (!data || window.datasRecorrentes.includes(data)) return;

    window.datasRecorrentes = gerarDatasSemanaPorTresMeses(data);
    input.value = '';
    await renderizarDatasRecorrentes();
    verificar();
}

function gerarDatasSemanaPorTresMeses(dataBase) {
    const datas = [];
    const atual = new Date(dataBase + 'T00:00:00');
    const fim = new Date(atual);
    fim.setMonth(fim.getMonth() + 3);

    while (atual <= fim) {
        datas.push(atual.toISOString().slice(0, 10));
        atual.setDate(atual.getDate() + 7);
    }

    return datas;
}

function removerDataRecorrente(data) {
    window.datasRecorrentes = window.datasRecorrentes.filter(item => item !== data);
    renderizarDatasRecorrentes();
    verificar();
}

async function renderizarDatasRecorrentes() {
    const lista = document.getElementById('listaDatasRecorrentes');
    lista.innerHTML = '';

    for (const data of window.datasRecorrentes) {
        const disponibilidade = await consultarDisponibilidade(data);
        const emAnalise = disponibilidade && !disponibilidade.disponivel;
        const badge = emAnalise ? 'text-bg-warning' : 'text-bg-success';
        const label = emAnalise ? 'Em analise' : 'Disponivel';

        lista.innerHTML += `
            <div class="d-flex justify-content-between align-items-center gap-2 p-2 rounded" style="background:#101d2f; border:1px solid #243b55;">
                <input type="hidden" name="datas_recorrentes[]" value="${data}">
                <span>${data}</span>
                <span class="badge ${badge}">${label}</span>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerDataRecorrente('${data}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }
}

async function verificar() {
    const sala = document.getElementById('sala_id').value;
    const data = document.getElementById('data_reserva').value;
    const periodo = document.getElementById('periodo').value;
    const msg = document.getElementById('statusReserva');
    const btn = document.getElementById('btnEnviar');
    const recorrente = document.getElementById('recorrente').value === '1';

    if (recorrente) {
        btn.disabled = !(sala && periodo && window.datasRecorrentes.length > 0);
        msg.style.display = window.datasRecorrentes.length > 0 ? 'block' : 'none';
        msg.className = 'my-3 p-3 rounded-3 text-center fw-bold text-primary border border-primary';
        msg.innerHTML = '<i class="fas fa-calendar-check me-2"></i> Recorr&ecirc;ncia gerada por 3 meses. Se n&atilde;o for usar alguma data, cancele aquela reserva espec&iacute;fica depois em Minhas Reservas.';
        return;
    }

    if (!sala || !data || !periodo) return;

    const json = await consultarDisponibilidade(data);
    msg.style.display = 'block';

    if (json.disponivel) {
        msg.className = 'my-3 p-3 rounded-3 text-center fw-bold text-success border border-success';
        msg.innerHTML = '<i class="fas fa-check-circle me-2"></i> Sala disponivel.';
        btn.disabled = false;
    } else {
        msg.className = 'my-3 p-3 rounded-3 text-center fw-bold text-warning border border-warning';
        msg.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> ' + json.mensagem + ' Se continuar, sua reserva sera cancelada automaticamente com esse motivo.';
        btn.disabled = false;
    }
}
</script>
@endsection
