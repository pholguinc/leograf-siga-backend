<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    public $timestamps = [
        'created_at' => 'Y-m-d H:i:s',
        'updated_at' => false,
    ];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class);
    }
}
