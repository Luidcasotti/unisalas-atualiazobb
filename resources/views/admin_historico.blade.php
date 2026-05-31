@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <h2 class="fw-bold mb-4" style="color: #7010a8;"><i class="fas fa-database me-2"></i> Histórico Geral</h2>

    <div class="card p-3 mb-4 border-0 shadow-sm">
        <form action="{{ route('admin.historico') }}" method="GET" class="row align-items-center">
            <div class="col-md-4">
                <input type="date" name="data" class="form-control" value="{{ request('data') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn text-white w-100" style="background: #7010a8;">Filtrar</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.historico') }}" class="btn btn-outline-secondary w-100">Limpar</a>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Professor</th>
                    <th>Sala</th>
                    <th>Data</th>
                    <th>Período</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservas as $r)
                <tr>
                    <td>{{ $r->user->name ?? 'N/A' }}</td>
                    <td>{{ $r->sala->nome ?? 'N/A' }}</td>
                    <td>{{ date('d/m/Y', strtotime($r->data_reserva)) }}</td>
                    <td>
                        <span class="badge bg-secondary">
                            {{ $r->periodo ?? 'Não informado' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $r->status == 'aprovada' ? 'bg-success' : ($r->status == 'pendente' ? 'bg-warning text-dark' : 'bg-danger') }}">
                            {{ ucfirst($r->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection