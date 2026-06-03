@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="mb-4">
        <div class="section-kicker mb-2">Minhas reservas</div>
        <h2 class="fw-bold mb-1">Hist&oacute;rico de reservas</h2>
        <p class="text-muted mb-0">Consulte suas solicita&ccedil;&otilde;es, status e respostas da administra&ccedil;&atilde;o.</p>
    </div>

    <div class="page-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Sala</th>
                        <th class="py-3">Data</th>
                        <th class="py-3">Per&iacute;odo</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 pe-4">Resposta do ADM</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservas as $reserva)
                        <tr>
                            <td class="ps-4 fw-bold">
                                {{ $reserva->sala->nome ?? 'Sala removida' }}
                                @if($reserva->recorrente)
                                    <span class="badge text-bg-info ms-2">Recorrente</span>
                                @endif
                            </td>
                            <td>{{ date('d/m/Y', strtotime($reserva->data_reserva)) }}</td>
                            <td><span class="data-chip"><i class="fas fa-clock"></i>{{ $reserva->periodo }}</span></td>
                            <td class="text-center">
                                @if($reserva->status == 'aprovada')
                                    <span class="badge text-bg-success">Aprovada</span>
                                @elseif($reserva->status == 'em_analise')
                                    <span class="badge text-bg-info">Em an&aacute;lise</span>
                                @elseif($reserva->status == 'cancelada')
                                    <span class="badge text-bg-danger">Cancelada</span>
                                @elseif($reserva->status == 'rejeitada' || $reserva->status == 'recusada')
                                    <span class="badge text-bg-danger">Rejeitada</span>
                                @else
                                    <span class="badge text-bg-warning">Pendente</span>
                                @endif
                            </td>
                            <td class="pe-4">
                                @if($reserva->comentario_adm)
                                    <div class="tech-panel p-2 small">{{ $reserva->comentario_adm }}</div>
                                @else
                                    <span class="text-muted small">Sem resposta.</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Nenhum hist&oacute;rico encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
