@extends('layouts.app')
@section('content')
<div class="container p-4">
    <div class="card p-4 shadow-sm border-0" style="border-radius: 15px;">
        <h3 class="fw-bold mb-4" style="color: #7010a8;">Nova Sala</h3>
        <form action="{{ route('sala.salvar') }}" method="POST">
            @csrf
            <input type="hidden" name="bloco_id" value="{{ $bloco_id }}">
            <div class="mb-3">
                <label class="fw-bold">Nome da Sala</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="fw-bold">Observações (Descrição/Tecnologia)</label>
                <textarea name="observacao" class="form-control" rows="4" placeholder="Ex: Possui projetor e ar-condicionado..."></textarea>
            </div>
            <button class="btn text-white w-100 fw-bold" style="background: #7010a8;">SALVAR SALA</button>
        </form>
    </div>
</div>
@endsection