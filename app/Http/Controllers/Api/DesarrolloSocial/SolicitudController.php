<?php

namespace App\Http\Controllers\Api\DesarrolloSocial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DesarrolloSocial\Solicitud;
use App\Http\Resources\DesarrolloSocial\SolicitudResource;
use Illuminate\Support\Facades\DB;
use App\Models\DesarrolloSocial\Bitacora;
use Illuminate\Support\Facades\Validator;

class SolicitudController extends Controller
{

    public array $validationRules = [
        'nombres'       => 'required|string|max:255',
        'apellidos'     => 'required|string|max:255',
        'email'         => 'required|email|max:255|unique:solicitudes,email',
        'telefono'      => 'required|string|min:8',
        'domicilio'     => 'required|string',
        'observaciones' => 'nullable|string',
        'razon'         => 'required|string',
        'zona'          => 'required|integer',
        'tramite_id'    => 'required|integer',
        'cui'           => 'required|integer',

    ];

    public array $validationMessages = [
        'nombres.required'    => 'Por favor, ingresa los nombres del solicitante.',
        'apellidos.required'  => 'Por favor, ingresa los apellidos del solicitante.',
        'email.email'         => 'El formato del correo electrónico no es válido.',
        'email.unique'        => 'Este correo electrónico ya tiene una solicitud registrada.',
        'telefono.required'   => 'El número de teléfono es obligatorio.',
        'telefono.min'   => 'El número de teléfono debe contener 8 caracteres.',
        'domicilio.required'  => 'La dirección de domicilio es obligatoria.',
        'zona.required'       => 'Debes especificar la zona.',
        'tramite_id.required' => 'El tipo de trámite es obligatorio.',
        'cui.required'        => 'El número de CUI es obligatorio.',
        'cui.unique'          => 'Este número de CUI ya tiene una solicitud registrada.', 
    ];

    public function getRules(): array
    {
        $rules = $this->validationRules;
        $rules['cui'] = [
            'required',
            'string',
            'max:13',
            'unique:solicitudes,cui',
            function ($attribute, $value, $fail) {
                if (!$this->cuiEsValido($value)) {
                    $fail('El número de CUI ingresado no es válido para Guatemala.');
                }
            },
        ];

        return $rules;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getRules(), $this->validationMessages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

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


    private function cuiEsValido(string $cui): bool
    {
        $cui = preg_replace('/[^0-9]/', '', $cui);

        if (strlen($cui) !== 13) return false;

        $numero = substr($cui, 0, 8);
        $verificador = (int)substr($cui, 8, 1);
        $depto = (int)substr($cui, 9, 2);
        $muni = (int)substr($cui, 11, 2);

        $munisPorDepto = [
            17,
            8,
            16,
            16,
            13,
            14,
            19,
            8,
            24,
            21,
            9,
            30,
            32,
            21,
            8,
            17,
            14,
            5,
            11,
            11,
            7,
            17
        ];

        if ($depto < 1 || $depto > count($munisPorDepto)) return false;
        if ($muni < 1 || $muni > $munisPorDepto[$depto - 1]) return false;

        $total = 0;
        for ($i = 0; $i < 8; $i++) {
            $total += (int)$numero[$i] * ($i + 2);
        }

        $digitoCalculado = $total % 11;

        return $digitoCalculado === $verificador;
    }
}
