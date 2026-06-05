<?php

namespace App\Http\Resources\DesarrolloSocial;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'no_solicitud' => $this->no_solicitud,
            'anio' => $this->anio,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'cui' => $this->cui,
            'domicilio' => $this->domicilio,
            'observaciones' => $this->observaciones,
            'razon' => $this->razon,
            'created_at' => $this->created_at,
        ];
    }
}
