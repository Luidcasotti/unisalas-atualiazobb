@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="/professor/reservas" class="text-decoration-none text-muted small"><i class="fas fa-arrow-left"></i> Voltar</a>
        <h2 class="fw-bold mt-2" style="color: #7010a8;"><i class="fas fa-edit"></i> Editar Reserva</h2>
    </div>

    <div class="card border-0 shadow-sm p-4" style="max-width: 600px;">
        <form>
            <div class="mb-3">
                <label class="form-label fw-bold small">Sala</label>
                <select class="form-select">
                    <option selected>Sala 204 - Bloco B</option>
                    <option>Sala 205 - Bloco B</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold small">Data</label>
                    <input type="date" class="form-control" value="2026-02-18">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold small">Hora</label>
                    <input type="time" class="form-control" value="19:00">
                </div>
            </div>
            <button type="submit" class="btn text-white fw-bold w-100 mt-3" style="background: #7010a8;">SALVAR ALTERAÇÕES</button>
        </form>
    </div>
</div>
@endsection