<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $fillable = ['user_id', 'sala_id', 'data_reserva', 'periodo', 'status'];

    public function sala()
    {
        return $this->belongsTo(Sala::class, 'sala_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}