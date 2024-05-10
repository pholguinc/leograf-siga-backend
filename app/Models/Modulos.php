<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulos extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'modulos';
    protected $primaryKey = 'id_modulo';
    public $incrementing = true;
    protected $fillable = [
        'id_modulo',
        'codigo',
        'nombre',
        'estado',
        'fecha_creacion',
        'usuario_creacion',
        'fecha_modificacion',
        'usuario_modificacion'
    ];


    public function setUpdatedAt($value)
    {
        if ($this->isDirty()) {
            parent::setUpdatedAt($value);
        }
    }
}
