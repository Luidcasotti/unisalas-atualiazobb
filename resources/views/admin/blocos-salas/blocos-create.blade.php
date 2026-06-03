@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.blocos') }}" class="text-decoration-none text-muted small"><i class="fas fa-arrow-left"></i> Voltar para Blocos</a>
        <h2 class="fw-bold mt-2" style="color: #7010a8;"><i class="fas fa-plus-circle"></i> Cadastrar Novo Bloco</h2>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4">
                <form action="{{ route('bloco.salvar') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nome do Bloco / Prédio</label>
                        <input type="text" name="nome" class="form-control" placeholder="Ex: Bloco C - Administrativo" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">Cor de Identificação</label>
                        {{-- O "name='cor'" é OBRIGATÓRIO para salvar no banco --}}
                        <input type="color" name="cor" class="form-control form-control-color w-100" value="#7010a8" style="height: 50px;">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.blocos') }}" class="btn btn-light fw-bold">Cancelar</a>
                        <button type="submit" class="btn text-white fw-bold px-4" style="background: #7010a8;">SALVAR BLOCO</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 
@endsection