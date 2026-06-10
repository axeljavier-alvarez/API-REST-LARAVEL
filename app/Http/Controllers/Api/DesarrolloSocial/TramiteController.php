<?php

namespace App\Http\Controllers\Api\DesarrolloSocial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DesarrolloSocial\Tramite;
use App\Http\Resources\DesarrolloSocial\TramiteResource;

class TramiteController extends Controller
{
    public function index()
    {
        // try {
        //     $tramites = Tramite::select('id', 'nombre')->get();
        //     return TramiteResource::collection($tramites);
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'message' => 'Error al cargar los trámites',
        //         'error' => $th->getMessage()
        //     ], 500);
        // }
        try {
            $tramites = Tramite::with('requisitos:id,nombre')
                ->select('id', 'nombre')
                ->get();
            return TramiteResource::collection($tramites);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error al cargar los trámites',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
