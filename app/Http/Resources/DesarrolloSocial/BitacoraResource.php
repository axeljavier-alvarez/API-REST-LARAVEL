<?php
namespace App\Http\Resources\DesarrolloSocial;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BitacoraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'evento' => $this->evento,
            'descripcion' => $this->descripcion,
            'usuario' => $this->user?->name,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}