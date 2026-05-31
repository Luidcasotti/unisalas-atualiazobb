@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h2 class="fw-bold mb-4" style="color: #7010a8;"><i class="fas fa-th-large"></i> Painel Geral - UniSalas</h2>
    
    <div class="row g-3">
        <div class="col-md-2">
            <div class="card p-3 shadow-sm border-0 text-center">
                <small class="text-muted">Usuários</small>
                <h3 class="fw-bold mt-2">{{ $totalUsuarios }}</h3>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card p-3 shadow-sm border-0 text-center">
                <small class="text-muted">Blocos</small>
                <h3 class="fw-bold mt-2">{{ $totalBlocos }}</h3>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card p-3 shadow-sm border-0 text-center">
                <small class="text-muted">Salas</small>
                <h3 class="fw-bold mt-2">{{ $totalSalas }}</h3>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0 text-center bg-warning text-dark">
                <small>Reservas Pendentes</small>
                <h3 class="fw-bold mt-2">{{ $reservasPendentes }}</h3>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0 text-center bg-success text-white">
                <small>Reservas Aprovadas</small>
                <h3 class="fw-bold mt-2">{{ $reservasAprovadas }}</h3>
            </div>
        </div>
    </div>
</div>
@endsection