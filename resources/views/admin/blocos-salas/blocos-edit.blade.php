@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 p-4" style="border-radius: 25px;">
                <h3 class="fw-bold mb-4" style="color: #7010a8;"><i class="fas fa-edit me-2"></i> Editar Bloco</h3>
                
                <form action="{{ route('bloco.atualizar', $bloco->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="fw-bold text-muted small text-uppercase">Nome do Bloco</label>
                        <input type="text" name="nome" class="form-control form-control-lg" value="{{ $bloco->nome }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold text-muted small text-uppercase">Cor do Bloco</label>
                        <input type="color" name="cor" class="form-control form-control-lg" style="height: 50px;" value="{{ $bloco->cor ?? '#7010a8' }}">
                    </div>

                    <button type="submit" class="btn btn-lg w-100 text-white fw-bold" style="background: #7010a8; border-radius: 12px;">ATUALIZAR BLOCO</button>
                    <a href="{{ route('admin.blocos') }}" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection