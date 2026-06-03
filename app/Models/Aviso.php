<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aviso extends Model
{
    // Define a tabela caso o nome não seja o plural padrão
    protected $table = 'avisos';

    // Permite que estes campos sejam preenchidos ao criar um novo aviso
    protected $fillable = [
        'titulo', 
        'mensagem'
    ];
}