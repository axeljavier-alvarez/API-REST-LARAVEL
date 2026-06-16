<?php
namespace App\Http\Resources\DesarrolloSocial\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminTramiteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'nombre'            => $this->nombre,
            'requisitos'        => $this->requisitos,
            'total_solicitudes' => $this->solicitudes_count ?? 0
        ];
    }
}