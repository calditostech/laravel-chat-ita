<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atendimento extends Model
{
    protected $fillable = [
        'nome',
        'cpf',
        'servico_realizado',
        'protocolo',
        'data_hora',
    ];

    public $timestamps = true;
}
