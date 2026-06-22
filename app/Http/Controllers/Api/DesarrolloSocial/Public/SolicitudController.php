<?php
namespace App\Http\Controllers\Api\DesarrolloSocial\Public;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DesarrolloSocial\Solicitud;
use App\Http\Resources\DesarrolloSocial\SolicitudResource;
use Illuminate\Support\Facades\DB;
use App\Models\DesarrolloSocial\Bitacora;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\SolicitudStoreRequest;
use Illuminate\Http\JsonResponse;
use App\Models\DesarrolloSocial\DetalleSolicitud;
use App\Models\DesarrolloSocial\RequisitoTramite;
use App\Models\DesarrolloSocial\Tramite;

class SolicitudController extends Controller
{
    public function validarPaso(SolicitudStoreRequest $request): JsonResponse
    {
        return response()->json([
            'message' => 'Paso ' . $request->input('step') . ' válido.'
        ], 200);
    }

    public function store(SolicitudStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $nombres = mb_convert_case(trim($request->nombres), MB_CASE_TITLE, "UTF-8");
            $apellidos = mb_convert_case(trim($request->apellidos), MB_CASE_TITLE, "UTF-8");
            $solicitud = Solicitud::create([
                'nombres'       => $nombres,
                'apellidos'     => $apellidos,
                'email'         => $request->email,
                'telefono'      => '+502' . trim($request->telefono),
                'cui'           => $request->cui,
                'domicilio'     => $request->domicilio,
                'observaciones' => $request->observaciones,
                'razon'         => $request->razon,
                'zona'       => $request->zona,
                'tramite_id'    => $request->tramite_id,
                'anio'          => date('Y'),
                'estado_id'     => 1
            ]);

            $solicitud->no_solicitud = $solicitud->id . '-' . date('Y');
            $solicitud->save();

            Bitacora::create([
                'solicitud_id' => $solicitud->id,
                'user_id'      => null,
                'evento' => 'CREACIÓN',
                'descripcion'  => 'Solicitud creada exitosamente desde el formulario.',
            ]);

            $tramite = Tramite::with('requisitos')
            ->findOrFail($request->tramite_id);

            foreach($tramite->requisitos as $requisito){
                $campo = 'requisito_' . $requisito->id;
                if ($request->hasFile($campo)){
                    $archivo = $request->file($campo);
                    // guardar archivo
                    $path = $archivo->store(
                        'solicitudes/' . $solicitud->id,
                        'public'
                    );
                    // buscar relación pivote
                    $requisitoTramite = RequisitoTramite::where('tramite_id', $tramite->id)
                    ->where('requisito_id', $requisito->id)
                    ->first();

                    // creando detalle
                    DetalleSolicitud::create([
                       'path' => $path,
                       'tipo' => $archivo->getClientOriginalExtension(),
                       'solicitud_id' => $solicitud->id,
                       'user_id' => null,
                       'requisito_tramite_id' => $requisitoTramite?->id
                    ]);
                }
            }
            DB::commit();
            return (new SolicitudResource($solicitud->fresh()))
                ->response()
                ->setStatusCode(201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()], 500);
        }
    }

    public function consultar(Request $request)
    {
        $request->validate([
            'cui' => 'required|string',
            'no_solicitud' => 'required|string'
        ]);

        // laravel hace consultas optimizadas
        $solicitud = Solicitud::with([
            'tramite.requisitos',
            'estado'
        ])
        ->where('cui', $request->cui)
        ->where('no_solicitud', $request->no_solicitud)
        ->first();

        if(!$solicitud){
            return response()->json([
                'message' => 'Solicitud no encontrada'
            ], 404);
        }

        return new SolicitudResource($solicitud);

    }
}
