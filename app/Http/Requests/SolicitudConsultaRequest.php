<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SolicitudConsultaRequest extends FormRequest
{
    public function authroize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'cui' => [
                'required',
                'digits:13'
            ],
            'no_solicitud' => [
                'required',
                'string'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'cui.required' => 'Debe ingresar el número de DPI/CUI.',
            'cui.digits' => 'El DPI debe contener exactamente 13 dígitos.',
            'no_solicitud.required' => 'Debe ingresar el número de solicitud.'
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}