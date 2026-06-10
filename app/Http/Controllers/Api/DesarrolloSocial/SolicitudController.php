<?php

namespace App\Http\Controllers\Api\DesarrolloSocial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DesarrolloSocial\Solicitud;
use App\Http\Resources\DesarrolloSocial\SolicitudResource;
use Illuminate\Support\Facades\DB;
use App\Models\DesarrolloSocial\Bitacora;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\SolicitudStoreRequest;
use Illuminate\Http\JsonResponse;

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
}
