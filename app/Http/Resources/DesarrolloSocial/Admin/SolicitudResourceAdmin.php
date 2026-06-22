<?php

namespace App\Http\Resources\DesarrolloSocial\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
USE App\Http\Resources\DesarrolloSocial\TramiteResource;
use App\Http\Resources\DesarrolloSocial\EstadoResource;
use App\Http\Resources\DesarrolloSocial\BitacoraResource;

class SolicitudResourceAdmin extends JsonResource
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
            'tramite' => new TramiteResource($this->whenLoaded('tramite')),
            'estado' => new EstadoResource($this->estado),
            'bitacoras' => BitacoraResource::collection(
                $this->whenLoaded('bitacoras')
            ),
            'documentos' => DocumentoSolicitudResource::collection(
                $this->whenLoaded('detallesSolicitudes')
            ),
            'estado_id'     => $this->estado_id,
            'created_at'    => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
