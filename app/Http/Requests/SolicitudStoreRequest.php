<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\DesarrolloSocial\Tramite;

class SolicitudStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rulesByStep(int $step): array
    {
        $rules = [
            1 => [
                'nombres'   => 'required|string|max:60',
                'apellidos' => 'required|string|max:60',
                'email' => 'required|email|unique:solicitudes,email|max:45',
                'telefono'  => 'required|numeric|digits_between:8,15',
                'domicilio' => 'required|string|max:255',
                'zona'      => 'required|integer',
                'cui' => [
                    'required',
                    'string',
                    'digits:13',
                    'unique:solicitudes,cui,' . ($this->solicitud?->id ?? 'NULL'),
                    function ($attribute, $value, $fail) {
                        if (!$this->cuiEsValido($value)) {
                            $fail('El número de CUI no es válido');
                        }
                    }
                ]
            ],
            2 => [
                'razon'      => 'required|string|min:10|max:1000',
                'tramite_id' => 'required|exists:tramites,id',
            ],
            3 => [
                'observaciones' => 'nullable|string|max:1000',
            ]
        ];

        return $rules[$step] ?? [];
    }

    /**
     * Retorna las reglas de validación que Laravel aplicará dinámicamente.
     */
    public function rules(): array
    {
        if ($this->has('step')) {

            $rules = $this->rulesByStep((int) $this->input('step'));

            // Si estamos validando el paso 2
            if ((int) $this->input('step') === 2 && $this->tramite_id) {

                $tramite = Tramite::with('requisitos')
                    ->find($this->tramite_id);

                if ($tramite) {

                    foreach ($tramite->requisitos as $requisito) {

                        $campo = 'requisito_' . $requisito->id;

                        $rules[$campo] = [
                            'required',
                            'file',
                            'mimes:pdf,jpg,jpeg,png',
                            'max:2048'
                        ];
                    }
                }
            }

            return $rules;
        }

        $rules = array_merge(
            $this->rulesByStep(1),
            $this->rulesByStep(2),
            $this->rulesByStep(3)
        );

        if ($this->tramite_id) {

            $tramite = Tramite::with('requisitos')
                ->find($this->tramite_id);

            if ($tramite) {

                foreach ($tramite->requisitos as $requisito) {

                    $campo = 'requisito_' . $requisito->id;

                    $rules[$campo] = [
                        'required',
                        'file',
                        'mimes:pdf,jpg,jpeg,png',
                        'max:2048'
                    ];
                }
            }
        }

        return $rules;
    }
    public function messages(): array
    {
        $messages = [
            'nombres.required'     => 'El nombre es requerido.',
            'apellidos.required'   => 'El apellido es requerido.',
            'email.required'       => 'El correo electrónico es requerido.',
            'email.email'          => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'El correo ya fue registrado',
            'telefono.required'    => 'El teléfono es requerido.',
            'telefono.numeric'     => 'El teléfono debe contener solo números.',
            'cui.required'         => 'El DPI (CUI) es requerido.',
            'cui.digits'           => 'El DPI debe tener exactamente 13 dígitos.',
            'cui.unique'           => 'Este DPI ya tiene una solicitud registrada.',
            'domicilio.required'   => 'El domicilio es requerido.',
            'zona.required'        => 'La zona es requerida.',
            'razon.required'       => 'La razón de la solicitud es requerida.',
            'razon.min'            => 'La razón debe tener al menos 10 caracteres.',
            'tramite_id.required'  => 'Debe seleccionar un tipo de trámite.',
            'tramite_id.exists'    => 'El trámite seleccionado no es válido.',
        ];

        if ($this->tramite_id) {
            $tramite = Tramite::with('requisitos')
                ->find($this->tramite_id);
            if ($tramite) {
                foreach ($tramite->requisitos as $requisito) {
                    $campo = 'requisito_' . $requisito->id;
                    $messages["{$campo}.required"] =
                        "Debe adjuntar {$requisito->nombre}.";
                    $messages["{$campo}.file"] =
                        "{$requisito->nombre} debe ser un archivo válido.";
                    $messages["{$campo}.mimes"] =
                        "{$requisito->nombre} debe ser PDF, JPG o PNG";

                    $messages["{$campo}.max"] =
                        "{$requisito->nombre} no debe superar los 2 MB.";
                }
            }
        }

        return $messages;
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
    private function cuiEsValido(string $cui): bool
    {
        $cui = preg_replace('/[^0-9]/', '', $cui);
        if (strlen($cui) !== 13) return false;

        $numero = substr($cui, 0, 8);
        $verificador = (int)substr($cui, 8, 1);
        $depto = (int)substr($cui, 9, 2);
        $muni = (int)substr($cui, 11, 2);

        $munisPorDepto = [17, 8, 16, 16, 13, 14, 19, 8, 24, 21, 9, 30, 32, 21, 8, 17, 14, 5, 11, 11, 7, 17];

        if ($depto < 1 || $depto > count($munisPorDepto)) return false;
        if ($muni < 1 || $muni > $munisPorDepto[$depto - 1]) return false;

        $total = 0;
        for ($i = 0; $i < 8; $i++) {
            $total += (int)$numero[$i] * ($i + 2);
        }

        return ($total % 11) === $verificador;
    }
}
