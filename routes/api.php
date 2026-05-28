<?php   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Hola desde la API de laravel'
    ]);
});

// listar registros

Route::get('users', function(){
    return response()->json([
        'message' => 'Listado de usuarios'
    ]);
});

// crear registros
Route::post('users', function(){
    return response()->json([
        'message' => 'Usuario creado'
    ]);
});
// recuperar registros
Route::get('users/{id}', function($id){
    return response()->json([
        'message' => 'Usuario recuperado: '. $id
    ]);
});
// actualizar registros
Route::put('users/{id}', function($id){
    return response()->json([
        'message' => 'Usuario actualizado: '. $id
    ]);
});

// Route::patch('users/{id}', function($id){
//     return response()->json([
//         'message' => 'Usuario actualizado: '. $id
//     ]);
// });

// eliminar registros
Route::delete('users/{id}', function($id){
    return response()->json([
        'message' => 'Usuario eliminado: '. $id
    ]); 
});