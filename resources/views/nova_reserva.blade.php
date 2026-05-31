@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="mb-5 text-center">
        <h2 class="fw-bold display-6" style="color: #7010a8;">Nova Solicitação</h2>
        <p class="text-muted">Selecione o ambiente ideal para sua aula</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg border-0 p-4" style="border-radius: 25px; background: rgba(255, 255, 255, 0.9);">
                <form action="{{ route('professor.salvar') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Bloco</label>
                        <select id="bloco_id" class="form-select form-select-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 12px;" onchange="carregarSalas(this.value)" required>
                            <option value="">Selecione um bloco...</option>
                            @foreach($blocos as $b) <option value="{{$b->id}}">{{$b->nome}}</option> @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Sala</label>
                        <select name="sala_id" id="sala_id" class="form-select form-select-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 12px;" required></select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Data</label>
                            <input type="date" name="data_reserva" id="data_reserva" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 12px;" min="{{ date('Y-m-d') }}" onchange="verificar()" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Período</label>
                            <select name="periodo" id="periodo" class="form-select form-select-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 12px;" onchange="verificar()" required>
                                <option value="">Escolha...</option>
                                <option value="Matutino">Matutino</option>
                                <option value="Vespertino">Vespertino</option>
                                <option value="Noturno">Noturno</option>
                            </select>
                        </div>
                    </div>

                    <div id="statusReserva" class="mb-4 p-3 rounded-3 text-center fw-bold" style="display:none;"></div>

                    <button type="submit" id="btnEnviar" class="btn btn-lg w-100 text-white shadow-sm fw-bold py-3" style="background: #7010a8; border-radius: 15px;" disabled>
                        SOLICITAR RESERVA
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div id="cardDescricao" class="card shadow-lg border-0 p-4 h-100" style="border-radius: 25px; background: #7010a8; color: white; display: none;">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i> Detalhes da Sala</h5>
                <hr style="opacity: 0.2;">
                <div id="infoSala" class="lh-lg" style="font-size: 1.1rem; font-weight: 300;"></div>
            </div>
        </div>
    </div>
</div>

<script>
window.listaSalas = [];
async function carregarSalas(id) {
    const res = await fetch(`/professor/api/salas/${id}`);
    const salas = await res.json();
    window.listaSalas = salas;
    let html = '<option value="">Selecione uma sala...</option>';
    salas.forEach(s => html += `<option value="${s.id}">${s.nome}</option>`);
    document.getElementById('sala_id').innerHTML = html;
    document.getElementById('cardDescricao').style.display = 'none';
}

document.getElementById('sala_id').addEventListener('change', function() {
    const infoDiv = document.getElementById('infoSala');
    const card = document.getElementById('cardDescricao');
    const sala = window.listaSalas.find(s => s.id == this.value);
    
    if(sala && sala.observacao) {
        card.style.display = 'block';
        infoDiv.innerHTML = sala.observacao;
    } else { 
        card.style.display = 'none'; 
    }
    verificar();
});

async function verificar() {
    const sala = document.getElementById('sala_id').value;
    const data = document.getElementById('data_reserva').value;
    const periodo = document.getElementById('periodo').value;
    const msg = document.getElementById('statusReserva');
    const btn = document.getElementById('btnEnviar');
    if(!sala || !data || !periodo) return;
    
    const res = await fetch(`/professor/verificar?sala_id=${sala}&data=${data}&periodo=${periodo}`);
    const json = await res.json();
    
    msg.style.display = 'block';
    if(json.disponivel) {
        msg.className = 'my-3 p-3 rounded-3 text-center fw-bold bg-white text-success border border-success';
        msg.innerHTML = '<i class="fas fa-check-circle me-2"></i> Sala disponível para este período';
        btn.disabled = false;
    } else {
        msg.className = 'my-3 p-3 rounded-3 text-center fw-bold bg-white text-danger border border-danger';
        msg.innerHTML = '<i class="fas fa-times-circle me-2"></i> Sala indisponível';
        btn.disabled = true;
    }
}
</script>
@endsection