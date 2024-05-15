<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;
    public $timestamps = [
        'created_at' => 'Y-m-d H:i:s',
        'updated_at' => false,
    ];

    //Relación uno a muchos
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function submenus()
    {
        return $this->hasMany(Submenu::class);
    }
}
