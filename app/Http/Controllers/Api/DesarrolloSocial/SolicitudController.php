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

    // public array $validationRules = [
    //     'nombres'       => 'required|string|max:60',
    //     'apellidos'     => 'required|string|max:60',
    //     'email'         => 'required|email|max:45|unique:solicitudes,email',
    //     'telefono'      => 'required|string|min:8',
    //     'domicilio'     => 'required|string',
    //     'observaciones' => 'nullable|string',
    //     'razon'         => 'required|string',
    //     'zona'          => 'required|integer',
    //     'tramite_id'    => 'required|integer',
    //     'cui'           => 'required|integer',

    // ];

    // public array $validationMessages = [
    //     'nombres.required'    => 'Por favor, ingresa los nombres del solicitante.',
    //     'apellidos.required'  => 'Por favor, ingresa los apellidos del solicitante.',
    //     'email.email'         => 'El formato del correo electrónico no es válido.',
    //     'email.unique'        => 'Este correo electrónico ya tiene una solicitud registrada.',
    //     'telefono.required'   => 'El número de teléfono es obligatorio.',
    //     'telefono.min'   => 'El número de teléfono debe contener 8 caracteres.',
    //     'domicilio.required'  => 'La dirección de domicilio es obligatoria.',
    //     'zona.required'       => 'Debes especificar la zona.',
    //     'tramite_id.required' => 'El tipo de trámite es obligatorio.',
    //     'cui.required'        => 'El número de CUI es obligatorio.',
    //     'cui.unique'          => 'Este número de CUI ya tiene una solicitud registrada.', 
    // ];

    // public function getRules(): array
    // {
    //     $rules = $this->validationRules;
    //     $rules['cui'] = [
    //         'required',
    //         'string',
    //         'max:13',
    //         'unique:solicitudes,cui',
    //         function ($attribute, $value, $fail) {
    //             if (!$this->cuiEsValido($value)) {
    //                 $fail('El número de CUI ingresado no es válido para Guatemala.');
    //             }
    //         },
    //     ];

    //     return $rules;
    // }
    public function validarPaso(SolicitudStoreRequest $request): JsonResponse
    {
        return response()->json([
            'message' => 'Paso ' . $request->input('step') . ' válido.'
        ], 200);
    }

    public function store(SolicitudStoreRequest $request)
    {
        // $validator = Validator::make($request->all(), $this->getRules(), $this->validationMessages);
        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }

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
