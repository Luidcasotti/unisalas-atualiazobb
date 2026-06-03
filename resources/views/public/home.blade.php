@extends('layouts.app') {{-- Aqui dizemos para usar a "moldura" que criamos acima --}}

@section('content') {{-- Tudo dentro daqui vai aparecer lá no @yield --}}
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h1 class="display-5">Olá, Luid! 👋</h1>
                <p class="lead">Bem-vindo ao novo sistema de reserva de salas.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-calendar-plus text-primary"></i> Nova Reserva</h5>
                        <p class="card-text">Solicite o uso de uma sala ou laboratório.</p>
                        <a href="#" class="btn btn-primary">Começar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection