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
use App\Http\Requests\SolicitudConsultaRequest;
use App\Models\DesarrolloSocial\Dependiente;
use App\Models\DesarrolloSocial\Requisito;

class SolicitudController extends Controller
{
    public function validarPaso(SolicitudStoreRequest $request): JsonResponse
    {
        return response()->json([
            'message' => 'Paso ' . $request->input('step') . ' válido.'
        ], 200);
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
            ->paginate(10);
        return SolicitudResource::collection($solicitudes);
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
            // mensajes de bitacora
                if($request->tramite_id == 6){
                    if($request->tipo_persona_penal == 'menor'){
                        Bitacora::create([
                            'solicitud_id' => $solicitud->id,
                            'user_id' => null,
                            'evento' => 'MENOR DE EDAD',
                            'descripcion' => 'Adolescente en conflicto con la ley penal'
                        ]);
                    }
                    if($request->tipo_persona_penal == 'mayor'){
                        Bitacora::create([
                            'solicitud_id' => $solicitud->id,
                            'user_id' => null,
                            'evento' => 'MAYOR DE EDAD',
                            'descripcion' => 'Persona mayor de edad en conflicto con la ley penal'                
                        ]);
                    }
                }
                $tramite = Tramite::with('requisitos')
                ->findOrFail($request->tramite_id);
                $requisitos = $tramite->requisitos;
                if($request->tramite_id == 6){
                    if($request->tipo_persona_penal == 'menor'){
                        $requisitos = $tramite->requisitos
                            ->whereIn('id', [1,2,3,4]);
                    }
                    if($request->tipo_persona_penal == 'mayor'){
                        $requisitos = $tramite->requisitos
                            ->whereIn('id', [1,5,6,7,8]);
                    }
                }
            
            foreach ($requisitos as $requisito) {
                
                $campo = 'requisito_' . $requisito->id;
                $esCargaFamiliar = mb_strtolower(trim($requisito->nombre))
                    === 'cargas familiares';
                /* CARGA FAMILIAR */
                if ($esCargaFamiliar) {
                    $requisitoTramite = RequisitoTramite::where('tramite_id', $tramite->id)
                        ->where('requisito_id', $requisito->id)
                        ->first();
                    // si indicó que tiene dependientes
                    if ($request->boolean('tiene_dependientes') && $request->dependientes) {
                        foreach ($request->dependientes as $index => $dependiente) {
                            $path = null;
                            if ($request->hasFile("dependientes.$index.archivo")) {
                                $path = $request
                                    ->file("dependientes.$index.archivo")
                                    ->store(
                                        'dependientes/' . $solicitud->id,
                                        'public'
                                    );
                            }

                            $detalle = DetalleSolicitud::create([
                                'path' => $path,
                                'tipo' => 'dependiente',
                                'solicitud_id' => $solicitud->id,
                                'user_id' => null,
                                'requisito_tramite_id' => $requisitoTramite?->id
                            ]);

                            Dependiente::create([
                                'nombres' => mb_convert_case(
                                    trim($dependiente['nombres']),
                                    MB_CASE_TITLE,
                                    "UTF-8"
                                ),
                                'apellidos' => mb_convert_case(
                                    trim($dependiente['apellidos']),
                                    MB_CASE_TITLE,
                                    "UTF-8"
                                ),
                                'detalle_solicitud_id' => $detalle->id
                            ]);
                        }
                        Bitacora::create([
                            'solicitud_id' => $solicitud->id,
                            'user_id' => null,
                            'evento' => 'CARGAS FAMILIARES',
                            'descripcion' => 'Se registraron ' . count($request->dependientes) . ' dependientes.'
                        ]);
                    }
                    continue;
                }
                // DOCUMENTOS NORMALES
                if ($request->hasFile($campo)) {
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
                        'tipo' => 'documento',
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
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function consultar(SolicitudConsultaRequest $request)
    {
        $solicitud = Solicitud::with([
            'tramite.requisitos',
            'estado'
        ])
            ->where('cui', $request->cui)
            ->where('no_solicitud', $request->no_solicitud)
            ->first();

        if (!$solicitud) {
            return response()->json([
                'message' => 'Los datos ingresados no coinciden con ninguna solicitud.'
            ], 404);
        }

        return new SolicitudResource($solicitud);
    }
}
