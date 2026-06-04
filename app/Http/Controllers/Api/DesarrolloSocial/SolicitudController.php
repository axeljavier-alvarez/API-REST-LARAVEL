<?php

namespace App\Http\Controllers\Api\DesarrolloSocial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DesarrolloSocial\Solicitud;
use App\Http\Resources\DesarrolloSocial\SolicitudResource;

class SolicitudController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'anio' => 'required',
            'nombres' => 'required|string|max:60',
            'apellidos' => 'required|string|max:60',
            'email' => 'required|email|max:45',
            'telefono' => 'required|string|max:20',
            'cui' => 'required|string|min:13|max:13',
            'domicilio' => 'required|string|max:255',
            'observaciones' => 'nullable|string|max:500',
            'razon'         => 'nullable|string|max:255'
        ], [
            'cui.required' => 'El número de CUI es obligatorio',
            'cui.min' => 'El CUI Debe tener exactamente 13 dígitos',
            'cui.max' => 'El CUI debe tener exactamente 13 dígitos',
            'email.email' => 'El formato del correo no es valido'
        ]);

      $validatedData['no_solicitud'] = 'SOL-' . $validatedData['anio'] . '-' . rand(1000, 9999);

      // 3. Crear el registro en la base de datos de Desarrollo Social
        $solicitud = Solicitud::create($validatedData);

        // 4. Responder con el Resource y un estado 201 (Creado)
        return (new SolicitudResource($solicitud))
            ->response()
            ->setStatusCode(201);
    }
}
