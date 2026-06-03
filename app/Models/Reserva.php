<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos no banco
    protected $fillable = [
        'user_id', 
        'sala_id', 
        'data_reserva', 
        'periodo', 
        'status', 
        'recorrente',
        'grupo_recorrencia',
        'comentario_professor', 
        'comentario_adm'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sala()
    {
        return $this->belongsTo(Sala::class);
    }
}
