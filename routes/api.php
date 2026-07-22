<?php   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DesarrolloSocial\Admin\AdminEstadoController;
use App\Http\Controllers\Api\DesarrolloSocial\Admin\AdminTramiteController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\DesarrolloSocial\Public\SolicitudController;
use App\Http\Controllers\Api\DesarrolloSocial\Public\TramiteController;
use App\Http\Controllers\Api\DesarrolloSocial\Admin\AdminSolicitudController;
use App\Models\DesarrolloSocial\Estado;
use App\Models\DesarrolloSocial\Bitacora;
use Illuminate\Support\Facades\DB;

Route::get('solicitudes/{solicitud}/pdf',
[AdminSolicitudController::class, 'pdf']);

Route::apiResource('tramitesDashboard', AdminTramiteController::class)->only(['index']);
Route::apiResource('estadosDashboard', AdminEstadoController::class)->only(['index']);
Route::apiResource(
    'solicitudesDashboard',
    AdminSolicitudController::class
)->only(['index']);
// ver solicitudes analisis
Route::get(
    'solicitudesAnalisis',
    [AdminSolicitudController::class, 'analisis']
);
// ver solicitudes visitas de campo
Route::get(
    'solicitudesPorAutorizar',
    [AdminSolicitudController::class, 'solicitudesPorAutorizar']
);

// ver solicitudes visita de campo
Route::get(
    'solicitudesVisitas',
    [AdminSolicitudController::class, 'visitas']
);


// admin
Route::post(
    'solicitudes/{solicitud}/cambiar-estado',
    [AdminSolicitudController::class, 'cambiarEstado']
);

// subir fotos de visita de campo
Route::post(
    'solicitudes/{solicitud}/visita',
    [AdminSolicitudController::class, 'guardarVisita']
);

// autorizar
Route::get(
    'autorizar',
    [AdminSolicitudController::class, 'autorizar']
);

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