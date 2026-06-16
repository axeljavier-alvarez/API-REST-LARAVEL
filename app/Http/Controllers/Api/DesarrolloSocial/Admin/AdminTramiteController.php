<?php

namespace App\Http\Controllers\Api\DesarrolloSocial\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesarrolloSocial\Tramite;
use App\Http\Resources\DesarrolloSocial\Admin\AdminTramiteResource;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminTramiteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api')
        ];
    }

    public function index()
    {
        $tramites = Tramite::with('requisitos:id,nombre')
            ->withCount('solicitudes')
            ->get();
        return AdminTramiteResource::collection($tramites);
    }
}
