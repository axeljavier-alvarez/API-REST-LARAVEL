<?php

namespace App\Http\Resources\DesarrolloSocial\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentoSolicitudResource extends JsonResource
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
            'path' => $this->path,
            'tipo' => $this->tipo,
            'requisito_tramite_id' => $this->requisito_tramite_id,
            'requisito' => [
              'id' => $this->requisitoTramite?->requisito?->id,
              'nombre' => $this->requisitoTramite?->requisito?->nombre,
            ]
        ];
    }
}
