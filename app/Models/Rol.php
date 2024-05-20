<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = "roles";
    public $timestamps = [
        'created_at' => 'Y-m-d H:i:s',
        'updated_at' => false,
    ];

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permisos');
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    

}
