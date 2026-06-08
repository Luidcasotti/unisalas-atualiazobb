@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <a href="{{ route('admin.blocos') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-2 mb-2">
                <i class="fas fa-arrow-left"></i>
                Voltar para blocos e salas
            </a>
            <div class="section-kicker mb-2">Infraestrutura</div>
            <h2 class="fw-bold mb-1">Nova sala</h2>
            <p class="text-muted mb-0">Cadastre um novo espa&ccedil;o para reservas no bloco selecionado.</p>
        </div>

        <div class="data-chip">
            <span
                class="room-block-dot"
                style="--block-color: {{ $bloco->cor ?? '#1d9bf0' }}"
                aria-hidden="true"
            ></span>
            {{ $bloco->nome }}
        </div>
    </div>

    <style>
        .room-form-shell {
            max-width: 920px;
        }
        .room-form-card {
            background: linear-gradient(180deg, #13263d, #0f1c2f);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
        }
        .room-form-icon {
            width: 46px;
            height: 46px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #123354;
            color: #5cc9ff;
            flex: 0 0 auto;
        }
        .room-block-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--block-color);
            box-shadow: 0 0 0 5px rgba(92, 201, 255, 0.12);
        }
        .room-form-card .form-label {
            color: #eef7ff;
        }
        .room-form-card .form-text {
            color: #91a8c5;
        }
        .room-preview {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #0b1727;
        }
        .room-actions {
            border-top: 1px solid var(--line);
        }
        @media (max-width: 576px) {
            .room-form-card .btn {
                width: 100%;
            }
        }
    </style>

    <div class="room-form-shell">
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-3">
                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <div class="room-form-card p-4 p-lg-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="room-form-icon">
                    <i class="fas fa-door-open"></i>
                </span>
                <div>
                    <h4 class="fw-bold mb-1">Dados da sala</h4>
                    <div class="text-muted small">Informe um nome claro e os recursos dispon&iacute;veis.</div>
                </div>
            </div>

            <form action="{{ route('sala.salvar') }}" method="POST">
                @csrf
                <input type="hidden" name="bloco_id" value="{{ $bloco_id }}">

                <div class="row g-4">
                    <div class="col-12 col-lg-7">
                        <label for="nome" class="form-label fw-bold">Nome da sala</label>
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            class="form-control form-control-lg @error('nome') is-invalid @enderror"
                            value="{{ old('nome') }}"
                            placeholder="Ex.: Sala 204"
                            maxlength="80"
                            required
                            autofocus
                        >
                        <div class="form-text">Use um nome curto, f&aacute;cil de identificar no mapa de reservas.</div>
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-lg-5">
                        <div class="room-preview p-3 h-100">
                            <div class="small text-muted mb-2">Bloco selecionado</div>
                            <div class="d-flex align-items-center gap-2 fw-bold">
                                <span
                                    class="room-block-dot"
                                    style="--block-color: {{ $bloco->cor ?? '#1d9bf0' }}"
                                    aria-hidden="true"
                                ></span>
                                {{ $bloco->nome }}
                            </div>
                            <div class="small text-muted mt-3">
                                A sala ser&aacute; exibida dentro deste bloco na lista de infraestrutura.
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="observacao" class="form-label fw-bold">Observa&ccedil;&otilde;es</label>
                        <textarea
                            id="observacao"
                            name="observacao"
                            class="form-control @error('observacao') is-invalid @enderror"
                            rows="5"
                            maxlength="1000"
                            placeholder="Ex.: Possui projetor, ar-condicionado, 40 cadeiras e quadro branco."
                        >{{ old('observacao') }}</textarea>
                        <div class="form-text">Descreva recursos, capacidade ou detalhes importantes para professores.</div>
                        @error('observacao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="room-actions d-flex flex-wrap justify-content-end gap-2 mt-4 pt-4">
                    <a href="{{ route('admin.blocos') }}" class="btn btn-outline-secondary px-4">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="fas fa-save me-2"></i>Salvar sala
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
