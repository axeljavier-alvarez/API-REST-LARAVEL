<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
         return response()->json([
            'message' => 'Listado de usuarios'
         ]);
    }


    public function store()
    {
        return response()->json([
            'message' => 'Usuario creado'

        ]);
    }

    public function show($id)
    {
        return response()->json([
            'message' => 'Usuario recuperado: '. $id
        ]);
    }

    public function update($id)
    {
        return response()->json([
            'message' => 'Usuario actualizado: '. $id
        ]);
    }

    public function destroy($id)
    {
        return response()->json([
            'message' => 'Usuario eliminado: '. $id
        ]);
    }

    
}
