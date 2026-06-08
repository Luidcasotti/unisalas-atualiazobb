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
            <h2 class="fw-bold mb-1">Cadastrar novo bloco</h2>
            <p class="text-muted mb-0">Crie uma unidade f&iacute;sica para organizar as salas do sistema.</p>
        </div>

        <div class="data-chip">
            <i class="fas fa-building text-primary"></i>
            Novo bloco
        </div>
    </div>

    <style>
        .block-form-shell {
            max-width: 920px;
        }
        .block-form-card {
            background: linear-gradient(180deg, #13263d, #0f1c2f);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
        }
        .block-form-icon {
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
        .block-color-preview {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #0b1727;
        }
        .block-color-strip {
            width: 14px;
            min-height: 86px;
            border-radius: 8px;
            background: var(--preview-color, #1d9bf0);
            box-shadow: 0 0 0 5px rgba(92, 201, 255, 0.12);
        }
        .block-form-card .form-label {
            color: #eef7ff;
        }
        .block-form-card .form-text {
            color: #91a8c5;
        }
        .block-form-card .form-control-color {
            width: 100%;
            height: 54px;
            padding: 0.35rem;
        }
        .block-actions {
            border-top: 1px solid var(--line);
        }
        @media (max-width: 576px) {
            .block-form-card .btn {
                width: 100%;
            }
        }
    </style>

    <div class="block-form-shell">
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-3">
                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <div class="block-form-card p-4 p-lg-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="block-form-icon">
                    <i class="fas fa-building"></i>
                </span>
                <div>
                    <h4 class="fw-bold mb-1">Dados do bloco</h4>
                    <div class="text-muted small">Defina um nome e uma cor para identificar o bloco nas telas de reserva.</div>
                </div>
            </div>

            <form action="{{ route('bloco.salvar') }}" method="POST">
                @csrf

                <div class="row g-4">
                    <div class="col-12 col-lg-7">
                        <label for="nome" class="form-label fw-bold">Nome do bloco ou pr&eacute;dio</label>
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            class="form-control form-control-lg @error('nome') is-invalid @enderror"
                            value="{{ old('nome') }}"
                            placeholder="Ex.: Bloco C - Administrativo"
                            maxlength="80"
                            required
                            autofocus
                        >
                        <div class="form-text">Use um nome que ajude professores e administra&ccedil;&atilde;o a localizar as salas.</div>
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-lg-5">
                        <label for="cor" class="form-label fw-bold">Cor de identifica&ccedil;&atilde;o</label>
                        <input
                            type="color"
                            id="cor"
                            name="cor"
                            class="form-control form-control-color @error('cor') is-invalid @enderror"
                            value="{{ old('cor', '#1d9bf0') }}"
                        >
                        <div class="form-text">Essa cor aparece como refer&ecirc;ncia visual do bloco.</div>
                        @error('cor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="block-color-preview p-3 d-flex align-items-center gap-3" id="blockPreview" style="--preview-color: {{ old('cor', '#1d9bf0') }}">
                            <span class="block-color-strip" aria-hidden="true"></span>
                            <div>
                                <div class="small text-muted mb-1">Pr&eacute;via na lista de infraestrutura</div>
                                <div class="fw-bold" id="blockPreviewName">{{ old('nome') ?: 'Nome do bloco' }}</div>
                                <div class="small text-muted">As salas cadastradas depois ficar&atilde;o agrupadas aqui.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="block-actions d-flex flex-wrap justify-content-end gap-2 mt-4 pt-4">
                    <a href="{{ route('admin.blocos') }}" class="btn btn-outline-secondary px-4">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="fas fa-save me-2"></i>Salvar bloco
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const blockNameInput = document.getElementById('nome');
    const blockColorInput = document.getElementById('cor');
    const blockPreview = document.getElementById('blockPreview');
    const blockPreviewName = document.getElementById('blockPreviewName');

    blockNameInput?.addEventListener('input', () => {
        blockPreviewName.textContent = blockNameInput.value.trim() || 'Nome do bloco';
    });

    blockColorInput?.addEventListener('input', () => {
        blockPreview.style.setProperty('--preview-color', blockColorInput.value);
    });
</script>
@endsection
