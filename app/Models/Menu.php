<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $table = 'menus';
    public $primaryKey = 'id_menu';
    public $incrementing = true;

    protected $fillable = [
        'id_menu',
        'codigo',
        'id_modulo',
        'nombre',
        'estado',
        'fecha_creacion',
        'usuario_creacion',
        'fecha_modificacion',
        'usuario_modificacion'
    ];


}
