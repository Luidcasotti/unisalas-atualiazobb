@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Novo Usuário</h3>
    
    <form action="{{ route('usuario.salvar') }}" method="POST">
        @csrf 
        
        <div class="mb-3">
            <label>Nome:</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label>E-mail:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label>Senha:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label>Perfil:</label>
            <select name="tipo" class="form-control">
                <option value="professor">Professor</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Salvar Usuário</button>
    </form>
</div>
@endsection