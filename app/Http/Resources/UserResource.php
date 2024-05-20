<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'uuid'              => $this->uuid,
            'nombres'            => $this->nombre,
            'apellidos'          => $this->apellido,
            'email'             => $this->email,
            'estado'            => $this->estado,
            'rol_id'            => $this->rol_id,
        ];

        return $data;
    }
}
