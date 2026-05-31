@extends('layouts.app')

@section('content')
<h1 class="orange-text fw-bold"><i class="fas fa-map-marked-alt"></i> Estrutura da Universidade</h1>
<p class="text-muted">Clique em um bloco para ver as salas disponíveis.</p>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body text-center">
                <div class="display-4 text-purple mb-2"><i class="fas fa-building"></i></div>
                <h3 class="fw-bold">Bloco A</h3>
                <p class="badge bg-info text-dark">12 Salas de Aula</p>
                <hr>
                <p class="small text-muted">Engenharias e Arquitetura</p>
                <a href="/reserva/passo1" class="btn btn-outline-primary btn-sm w-100">Ver Salas</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body text-center">
                <div class="display-4 text-orange mb-2"><i class="fas fa-laptop-code"></i></div>
                <h3 class="fw-bold">Bloco B</h3>
                <p class="badge bg-info text-dark">8 Laboratórios</p>
                <hr>
                <p class="small text-muted">Tecnologia da Informação</p>
                <a href="/reserva/passo1" class="btn btn-outline-primary btn-sm w-100">Ver Salas</a>
            </div>
        </div>
    </div>
</div>
@endsection