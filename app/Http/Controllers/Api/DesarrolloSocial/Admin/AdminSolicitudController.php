<?php

namespace App\Http\Controllers\Api\DesarrolloSocial\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesarrolloSocial\SolicitudResource;
use App\Models\DesarrolloSocial\Solicitud;

use App\Http\Resources\DesarrolloSocial\Admin\SolicitudResourceAdmin;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use App\Models\DesarrolloSocial\Estado;
use App\Models\DesarrolloSocial\Bitacora;
use Illuminate\Support\Facades\DB;
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
            ->whereHas('estado', function ($query) {
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

    public function cambiarEstado(Request $request, Solicitud $solicitud)
    {
        $request->validate([
            'estado_id' => 'required|exists:estados,id'
        ]);

        $estadoAnterior = $solicitud->estado;
        $estadoNuevo = Estado::findOrFail($request->estado_id);

        $solicitud->estado_id = $estadoNuevo->id;
        $solicitud->save();

        Bitacora::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => auth()->id(),
            'evento' => 'Cambio de estado',
            'descripcion'  => "Se cambió el estado de '{$estadoAnterior->nombre}' a '{$estadoNuevo->nombre}'."
        ]);

        return response()->json([
            'message' => 'Estado actualiado correctamente',
            'solicitud' => new SolicitudResource(
                $solicitud->load('estado', 'bitacoras')
            )
        ]);
    }

    
}
