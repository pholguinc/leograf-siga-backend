<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'rol';
    protected $fillable = [
        'id',
        'codigo',
        'rol',
        'estado',
        'fecha_creacion',
        'usuario_creacion',
        'fecha_modificacion',
        'usuario_modificacion'
    ];

    // protected $appends = [
    //     'codigo',
    // ];

    // public function getCodigoAttribute() 
    // {
    //     if (!$this->codigo) {
    //         $this->codigo = '000' . $this->id; 
    //         $this->save();
    //     }

    //     return $this->codigo; 
    // }
}
