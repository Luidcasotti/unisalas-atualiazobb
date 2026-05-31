@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 20px;">
                <div class="card-body p-5">
                    <h3 class="fw-bold mb-4" style="color: #7010a8;">
                        <i class="fas fa-edit me-2"></i> Editar Sala: {{ $sala->nome }}
                    </h3>
                    
                    <form action="{{ route('sala.atualizar', $sala->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nome da Sala</label>
                            <input type="text" name="nome" class="form-control form-control-lg" value="{{ $sala->nome }}" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Observações (Descrição/Tecnologia)</label>
                            <textarea name="observacao" class="form-control form-control-lg" rows="5" placeholder="Ex: Projetor, Ar-condicionado, 40 cadeiras...">{{ $sala->observacao }}</textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-lg text-white flex-grow-1 fw-bold" style="background: #7010a8;">
                                ATUALIZAR INFORMAÇÕES
                            </button>
                            <a href="{{ route('admin.blocos') }}" class="btn btn-lg btn-outline-secondary">CANCELAR</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection