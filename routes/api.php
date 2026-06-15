<?php   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\DesarrolloSocial\SolicitudController;
use App\Http\Controllers\Api\DesarrolloSocial\TramiteController;
use App\Models\DesarrolloSocial\Solicitud;

// ruta nueva
Route::post('/solicitudes/validar-paso', [SolicitudController::class, 'validarPaso']);
Route::apiResource('solicitudes', SolicitudController::class);
Route::apiResource('tramites', TramiteController::class);
Route::post('solicitudes/consultar', [SolicitudController::class, 'consultar']);


Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout', [AuthController::class, 'logout']);
Route::post('auth/refresh', [AuthController::class, 'refresh']);
Route::post('auth/me', [AuthController::class, 'me']);

Route::apiResource('users', UserController::class);
Route::apiResource('tasks', TaskController::class);
Route::apiResource('categories', CategoryController::class);


Route::apiResource('permissions', PermissionController::class);

Route::apiResource('roles', RoleController::class);

Route::post('posts/{post}/tags', [PostController::class, 'syncTags']);
Route::apiResource('posts', PostController::class);
Route::get('prueba', function(){
    return auth('api')->user();
});