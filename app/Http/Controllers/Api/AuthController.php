g<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\User;

/**
 * Controlador encargado de la autenticación mediante JWT.
 *
 * Funcionalidades:
 * - Iniciar sesión y generar un token JWT.
 * - Obtener el usuario autenticado.
 * - Cerrar sesión invalidando el token.
 * - Refrescar un token expirado o próximo a expirar.
 */
class AuthController extends Controller implements HasMiddleware
{
    /**
     * Middleware aplicado a las rutas del controlador.
     *
     * Todas las acciones requieren autenticación JWT
     * excepto el método login.
     *
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['login', 'register']),
        ];
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
        ]); 

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return $this->login();
    }

    /**
     * Autentica un usuario y genera un token JWT.
     *
     * Credenciales esperadas:
     * - email
     * - password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        // Obtiene email y password de la petición
        $credentials = request(['email', 'password']);

        // Verifica las credenciales y genera un token
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'error' => 'No autorizado'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Obtiene la información del usuario autenticado.
     *
     * Requiere un token JWT válido.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(
            auth('api')->user()
        );
    }

    /**
     * Cierra la sesión del usuario.
     *
     * Invalida el token JWT actual para que no pueda
     * volver a utilizarse.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'mensaje' => 'Cierre de sesión exitoso'
        ]);
    }

    /**
     * Genera un nuevo token JWT a partir del token actual.
     *
     * Permite extender la sesión sin volver a ingresar
     * las credenciales.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(
            auth('api')->refresh()
        );
    }

    /**
     * Construye la respuesta estándar del token JWT.
     *
     * Devuelve:
     * - access_token: token generado
     * - token_type: tipo de token (Bearer)
     * - expires_in: tiempo de expiración en segundos
     *
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')
                ->factory()
                ->getTTL() * 60
        ]);
    }
}