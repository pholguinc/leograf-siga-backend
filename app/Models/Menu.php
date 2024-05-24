<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    public $timestamps = [
        'created_at' => 'Y-m-d H:i:s',
        'updated_at' => false,
    ];

    protected $fillable = [
        'id',
        'nombre',
        'id_modulo',
        'estado'
    ];


    //Relación uno a  muchos inversa
    public function submenu(){
        return $this->belongsTo(Submenu::class);
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class);
    }
}
