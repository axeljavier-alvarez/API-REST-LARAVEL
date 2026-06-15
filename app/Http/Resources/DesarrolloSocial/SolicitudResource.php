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
            'zona' => $this->zona,
            'tramite' => [
                'id' => $this->tramite?->id,
                'nombre' => $this->tramite?->nombre,
                'requisitos' => $this->tramite?->requisitos->map(function ($requisito){
                    return [
                        'id' => $requisito->id,
                        'nombre' => $requisito->nombre
                    ];
                })
            ],
            'estado' => [
                'id' => $this->estado?->id,
                'nombre' => $this->estado?->nombre
            ],
            'estado_id'     => $this->estado_id,
            'created_at' => $this->created_at,
        ];
    }
}
