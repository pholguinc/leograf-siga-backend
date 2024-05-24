<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id'              => $this->id,
            'nombre'            => $this->nombre,
            'id_modulo'          => $this->id_modulo,
            'estado'            => $this->estado,
        ];

        return $data;
    }
}
