@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold" style="color: #7010a8;"><i class="fas fa-users"></i> Gerenciar Usuários</h2>
            <p class="text-muted">Lista de todos os usuários cadastrados no sistema UniSalas.</p>
        </div>
        <a href="{{ route('usuario.novo') }}" class="btn text-white fw-bold shadow-sm" style="background: #7010a8;">
            <i class="fas fa-user-plus me-2"></i> NOVO USUÁRIO
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $user)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->tipo == 'admin')
                                    <span class="badge bg-danger">Administrador</span>
                                @else
                                    <span class="badge bg-primary">Professor</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('usuario.excluir', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0" title="Excluir" 
                                            onclick="return confirm('Deseja realmente excluir o usuário {{ $user->name }}?')">
                                        <i class="fas fa-trash-alt fa-lg"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Nenhum usuário cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection