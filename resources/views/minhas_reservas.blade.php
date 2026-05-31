@extends('layouts.app')
@section('content')
<div class="container-fluid p-4">
    <h2 class="fw-bold mb-4" style="color: #7010a8;">Minhas Reservas</h2>
    <div class="card shadow-sm border-0">
        <table class="table align-middle">
            <thead class="table-light">
                <tr><th>Sala</th><th>Data</th><th>Período</th><th>Status</th><th>Ação</th></tr>
            </thead>
            <tbody>
                @foreach($reservas as $reserva)
                <tr>
                    <td>{{ $reserva->sala->nome ?? 'Indisponível' }}</td>
                    <td>{{ date('d/m/Y', strtotime($reserva->data_reserva)) }}</td>
                    <td>{{ $reserva->periodo }}</td>
                    <td><span class="badge {{ $reserva->status == 'aprovada' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($reserva->status) }}</span></td>
                    <td>
                        <form action="{{ route('professor.desistir', $reserva->id) }}" method="POST" onsubmit="return confirm('Desistir desta reserva?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Desistir</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection