<?php

namespace App\Http\Controllers\Api\DesarrolloSocial\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesarrolloSocial\SolicitudResource;
use App\Models\DesarrolloSocial\Solicitud;

use App\Http\Resources\DesarrolloSocial\Admin\SolicitudResourceAdmin;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Override;

class AdminSolicitudController extends Controller implements HasMiddleware
{
    #[Override]
    public static function middleware()
    {
        return [
            new Middleware('auth:api')
        ];
    }

    public function index()
    {
        $solicitudes = Solicitud::query()
        ->with([
            'tramite',
            'estado',
            'bitacoras.user'
        ])
        
        ->latest()
        ->paginate(15);

        return SolicitudResource::collection($solicitudes);
    }

    public function analisis()
    {
        $solicitudes = Solicitud::query()
        ->with([
            'tramite',
            'estado',
            'bitacoras.user',
            'detallesSolicitudes.requisitoTramite.requisito'
        ])
        ->whereHas('estado', function($query){
            $query->whereIn('nombre', [
                'Pendiente',
                'Analisis',
                'Visita asignada',
                'Visita realizada'
            ]);
        })
        ->latest()
        ->paginate(15);
        return SolicitudResourceAdmin::collection(
            $solicitudes
        );
    }
}