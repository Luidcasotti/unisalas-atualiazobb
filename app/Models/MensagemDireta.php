<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MensagemDireta extends Model
{
    protected $table = 'mensagens_diretas';

    protected $fillable = [
        'remetente_id',
        'destinatario_id',
        'mensagem',
        'lida'
    ];

    // Opcional: converte o campo lida automaticamente para booleano
    protected $casts = [
        'lida' => 'boolean',
    ];

    public function remetente()
    {
        return $this->belongsTo(User::class, 'remetente_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }
}
