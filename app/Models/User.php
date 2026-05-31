<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'tipo', 'is_admin'];

    protected $hidden = ['password', 'remember_token'];

    // Relacionamento com Reservas
    public function reservas() {
        return $this->hasMany(Reserva::class);
    }
}