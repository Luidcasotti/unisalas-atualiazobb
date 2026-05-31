@extends('layouts.app')
@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold" style="color: #7010a8;"><i class="fas fa-building me-2"></i> Infraestrutura</h2>
            <p class="text-muted">Gerenciamento de blocos e salas.</p>
        </div>
        <a href="{{ route('bloco.novo') }}" class="btn text-white fw-bold shadow-sm px-4 py-2" style="background: #7010a8; border-radius: 10px;">
            <i class="fas fa-plus-circle me-2"></i> ADICIONAR BLOCO
        </a>
    </div>

    <div class="row g-4">
        @foreach($blocos as $bloco)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; overflow: hidden;">
                {{-- Cabeçalho com Cor Dinâmica --}}
                <div class="card-header border-0 p-4 d-flex justify-content-between" style="background-color: {{ $bloco->cor }}; color: white;">
                    <h5 class="fw-bold m-0"><i class="fas fa-map-marker-alt me-2"></i> {{ $bloco->nome }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('bloco.editar', $bloco->id) }}" class="text-white"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('bloco.excluir', $bloco->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este bloco?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-white border-0 bg-transparent p-0"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th class="ps-4">Sala</th><th class="text-end pe-4">Ação</th></tr></thead>
                        <tbody>
                            @forelse($bloco->salas as $sala)
                            <tr>
                                <td class="ps-4">{{ $sala->nome }} <br><small class="text-muted">{{ $sala->observacao }}</small></td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('sala.editar', $sala->id) }}" class="btn btn-sm btn-link text-primary"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('sala.excluir', $sala->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-link text-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center py-3 text-muted">Nenhuma sala cadastrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light border-0 p-3">
                    <a href="{{ route('sala.nova', $bloco->id) }}" class="btn btn-sm btn-outline-dark w-100 fw-bold">NOVA SALA</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection