<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    use HasFactory;

    // Atualizado: Removido capacidade, adicionado observacao
    protected $fillable = [
        'nome',
        'observacao',
        'bloco_id',
        'manutencao_ativa',
        'manutencao_fim',
        'manutencao_indeterminada',
        'manutencao_aviso',
    ];

    // Relacionamento com Bloco
    public function bloco() {
        return $this->belongsTo(Bloco::class);
    }

    // Relacionamento com Reservas
    public function reservas() {
        return $this->hasMany(Reserva::class);
    }
}
