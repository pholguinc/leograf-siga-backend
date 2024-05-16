<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolPermiso extends Model
{
    use HasFactory;
    protected $fillable = ['rol_id', 'permiso_id', 'estado'];

    public function permiso()
    {
        return $this->belongsTo(Permiso::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_permisos');
    }
}
