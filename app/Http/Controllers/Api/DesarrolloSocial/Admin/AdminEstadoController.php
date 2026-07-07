<?php

namespace App\Http\Controllers\Api\DesarrolloSocial\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesarrolloSocial\Estado;
use App\Models\DesarrolloSocial\Solicitud;
use App\Http\Resources\DesarrolloSocial\Admin\AdminEstadoResource;
use App\Http\Resources\DesarrolloSocial\SolicitudResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminEstadoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('auth:api')];
    }

    public function index()
    {
        // $estados = Estado::withCount('solicitudes')->get();
        $estados = Estado::query()
            ->withCount('solicitudes')
            ->whereNotIn('nombre', [

                'Visita asignada',
                'Visita realizada'
            ])
            ->get();
        return AdminEstadoResource::collection($estados);
    }

    
}
