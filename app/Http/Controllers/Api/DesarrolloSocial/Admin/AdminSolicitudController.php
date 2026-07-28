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
use Illuminate\Support\Facades\Storage;
use Override;
use App\Http\Requests\VisitaCampoStoreRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DesarrolloSocial\DetalleSolicitud;
// composer require barryvdh/laravel-dompdf
class AdminSolicitudController extends Controller implements HasMiddleware
{

    public function pdf(Solicitud $solicitud)
    {

        $solicitud->load([
            'tramite'
        ]);

        $view = $solicitud->tramite_id == 1
            ? 'pdf.magisterio_sin_cargas'
            : 'pdf.solicitudes_varias';

        $fecha = now();

        $pdf = Pdf::loadView($view, [
            'solicitud' => $solicitud,
            'dia' => $fecha->format('d'),
            'mes' => $fecha->translatedFormat('F'),
            'anio' => $fecha->format('Y')
        ]);
        // nombre del archivo
        $nombreArchivo = 'constancias/constancia_' .
            $solicitud->id . '_' . time() . '.pdf';
        
        Storage::disk('public')->put(
            $nombreArchivo,
            $pdf->output()
        );

        try {
            DB::transaction(function() use($solicitud, $nombreArchivo){
                $estadoAnterior = $solicitud->estado;
                $estadoNuevo = Estado::where(
                    'nombre',
                    'Emitido'
                )->firstOrFail();
                $solicitud->update([
                    'estado_id' => $estadoNuevo->id
                ]);
                Bitacora::create([
                    'solicitud_id' => $solicitud->id,
                    'user_id' => auth('api')->id(),
                    'evento' => 'Emisión de constancia',
                    'descripcion' => "Se emitió la constancia. Estado cambiado de '{$estadoAnterior->nombre}' a '{$estadoNuevo->nombre}'."
                ]);
                DetalleSolicitud::create([
                    'path' => $nombreArchivo,
                    'tipo' => 'constancia',
                    'solicitud_id' => $solicitud->id,
                    'user_id' => auth('api')->id(),
                    'requisito_tramite_id' => null,
                ]);
            });
        } catch(\Throwable $e){
            Storage::disk('public')->delete($nombreArchivo);
            throw $e;
        }
        return $pdf->download('constancia.pdf');
    }

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
    public function autorizaciones()
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
                    'Emitido',
                    'Autorizado',                    
                ]);
            })
            ->latest()
            ->paginate(15);
        return SolicitudResourceAdmin::collection(
            $solicitudes
        );
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
    // ver solicitudes en visita de campo
    public function visitas()
    {
        $solicitudes = Solicitud::query()
            ->with([
                'tramite',
                'estado',
                'bitacoras.user',
                'detallesSolicitudes'
            ])
            ->whereHas('estado', function ($query) {
                $query->whereIn('nombre', [
                    'Visita asignada',
                    'Visita realizada'
                ]);
            })
            ->latest()
            ->paginate(15);

        return SolicitudResourceAdmin::collection($solicitudes);
    }

    public function solicitudesPorAutorizar()
    {
        $solicitudes = Solicitud::query()
            ->with([
                'tramite',
                'estado',
                'bitacoras.user',
                'detallesSolicitudes'
            ])
            ->whereHas('estado', function ($query) {
                $query->whereIn('nombre', [
                    'Por autorizar',
                    'Emitido'
                ]);
            })
            ->latest()
            ->paginate(15);

        return SolicitudResourceAdmin::collection($solicitudes);
    }


    public function guardarVisita(
        VisitaCampoStoreRequest $request,
        Solicitud $solicitud
    ) {

        DB::transaction(function () use ($request, $solicitud) {

            foreach ($request->file('fotos') as $foto) {

                $path = $foto->store(
                    'visitas',
                    'public'
                );

                $solicitud->detallesSolicitudes()->create([

                    'path' => $path,

                    'tipo' => 'foto_visita',

                    'user_id' => auth('api')->id(),

                    'requisito_tramite_id' => null

                ]);
            }

            $estadoAnterior = $solicitud->estado;

            $estadoNuevo = Estado::where(
                'nombre',
                'Visita realizada'
            )->firstOrFail();

            $solicitud->update([
                'estado_id' => $estadoNuevo->id
            ]);

            $descripcion = $request->filled('descripcion')
                ? $request->descripcion
                : "Se realizó la visita de campo y se adjuntaron fotografías. Estado cambiado de '{$estadoAnterior->nombre}' a '{$estadoNuevo->nombre}'.";

            Bitacora::create([

                'solicitud_id' => $solicitud->id,

                'user_id' => auth('api')->id(),

                'evento' => 'Visita de campo',

                'descripcion' => $descripcion

            ]);
        });

        return response()->json([

            'message' => 'Visita registrada correctamente.'

        ]);
    }
}
