@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold" style="color: #7010a8;">Olá, {{ auth()->user()->name }}</h2>
            <p class="text-muted"><i class="fas fa-user-circle me-1"></i> Painel do Professor</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card p-4 shadow-sm border-0 text-center" style="border-radius: 15px;">
                <i class="fas fa-list-ul fa-2x mb-3 text-secondary"></i>
                <small class="text-uppercase text-muted fw-bold">Total</small>
                <h2 class="fw-bold mt-2">{{ $total }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow-sm border-0 text-center" style="border-radius: 15px;">
                <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>
                <small class="text-uppercase text-muted fw-bold">Aprovadas</small>
                <h2 class="fw-bold mt-2 text-success">{{ $aprovadas }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow-sm border-0 text-center" style="border-radius: 15px;">
                <i class="fas fa-clock fa-2x mb-3 text-warning"></i>
                <small class="text-uppercase text-muted fw-bold">Pendentes</small>
                <h2 class="fw-bold mt-2 text-warning">{{ $pendentes }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 shadow-sm border-0 text-center" style="border-radius: 15px;">
                <i class="fas fa-times-circle fa-2x mb-3 text-danger"></i>
                <small class="text-uppercase text-muted fw-bold">Recusadas</small>
                <h2 class="fw-bold mt-2 text-danger">{{ $recusadas }}</h2>
            </div>
        </div>
    </div>
</div>
@endsection