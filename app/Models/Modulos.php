<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulos extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'id',
        'codigo',
        'nombre',
        'estado',
        'fecha_creacion',
        'usuario_creacion',
        'fecha_modificacion',
        'usuario_modificacion'
    ];
}
