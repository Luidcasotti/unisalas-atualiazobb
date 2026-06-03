<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacaoVisualizada extends Model
{
    protected $fillable = [
        'user_id',
        'tipo',
        'referencia',
        'visualizada_em',
    ];

    protected $casts = [
        'visualizada_em' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
