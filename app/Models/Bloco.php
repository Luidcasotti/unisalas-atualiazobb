<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bloco extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos no banco (adicionado 'cor')
    protected $fillable = ['nome', 'cor'];

    /**
     * Relacionamento: Um bloco possui muitas salas.
     * Isso permite fazer $bloco->salas no código.
     */
    public function salas()
    {
        return $this->hasMany(Sala::class, 'bloco_id');
    }
}