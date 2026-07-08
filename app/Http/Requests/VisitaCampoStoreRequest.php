<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class VisitaCampoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return  [
            'fotos' => 'required|array|min:1|max:5',
            'fotos.*' => [
                'image',
                'mimes:jpg,jpeg,png',
                'max:4096'
            ]
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'fotos.required' => 'Debe subir al menos una fotografía.',

            'fotos.array' => 'Las fotografías son inválidas.',

            'fotos.min' => 'Debe subir al menos una fotografía.',

            'fotos.max' => 'Solo puede subir hasta 5 fotografías.',

            'fotos.*.image' => 'Cada archivo debe ser una imagen.',

            'fotos.*.mimes' => 'Solo se permiten JPG y PNG.',

            'fotos.*.max' => 'Cada imagen no debe superar los 4 MB.'
        ];
    }
}
