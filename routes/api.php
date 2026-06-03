<?php   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\AuthController;

// Route::get('/', function () {
//     return response()->json([
//         'message' => 'Hola desde la API de laravel'
//     ]);
// });

// // listar registros

// // Route::get('users', function(){
// //     return response()->json([
// //         'message' => 'Listado de usuarios'
// //     ]);
// // });

// Route::get('users', [UserController::class, 'index']);

// // crear registros
// // Route::post('users', function(){
// //     return response()->json([
// //         'message' => 'Usuario creado'
// //     ]);
// // });

// Route::post('users', [UserController::class, 'store']);
// // recuperar registros
// // Route::get('users/{id}', function($id){
// //     return response()->json([
// //         'message' => 'Usuario recuperado: '. $id
// //     ]);
// // });
// Route::get('users/{id}', [UserController::class, 'show']);


// // actualizar registros
// // Route::put('users/{id}', function($id){
// //     return response()->json([
// //         'message' => 'Usuario actualizado: '. $id
// //     ]);
// // });
// Route::patch('users/{id}', [UserController::class, 'update']);

// // Route::patch('users/{id}', function($id){
// //     return response()->json([
// //         'message' => 'Usuario actualizado: '. $id
// //     ]);
// // });
// // Route::delete('uses')
// // eliminar registros
// // Route::delete('users/{id}', function($id){
// //     return response()->json([
// //         'message' => 'Usuario eliminado: '. $id
// //     ]); 
// // });
// Route::delete('users/{id}', [UserController::class, 'destroy']);


// Route::get('users/cursos', function(){
//     return response()->json([
//         'message' => 'Listado de cursos'
//     ]);
// });

// registrar
Route::post('auth/register', [AuthController::class, 'register']);


Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout', [AuthController::class, 'logout']);
Route::post('auth/refresh', [AuthController::class, 'refresh']);
Route::post('auth/me', [AuthController::class, 'me']);

Route::apiResource('users', UserController::class);
Route::apiResource('tasks', TaskController::class);

Route::get('prueba', function(){
    return auth('api')->user();
});