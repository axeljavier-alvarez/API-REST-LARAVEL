<?php

namespace App\Http\Controllers\Api\DesarrolloSocial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DesarrolloSocial\Solicitud;
use App\Http\Resources\DesarrolloSocial\SolicitudResource;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
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

        $validatedData['anio'] = date('Y');

        $solicitud = DB::transaction(function() use ($validatedData){
            $solicitud = Solicitud::create($validatedData);

            $solicitud->update([
                'no_solicitud' => $solicitud->id . '-' . $solicitud->anio
            ]);

            return $solicitud;
        });

        return (new SolicitudResource($solicitud->fresh()))
        ->response()
        ->setStatusCode(201);
    }
}
