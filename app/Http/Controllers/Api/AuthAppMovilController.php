<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginAppMovilRequest;
use App\Http\Requests\RegisterAppMovilRequest;
use App\Http\Requests\CambiarContraseñaRequest;
use App\Http\Requests\CambiarCorreoRequest;
use App\Http\Requests\EliminarCuentaRequest;
use App\Models\UsuarioAppMovil;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthAppMovilController extends Controller
{
    /**
     * Registrar nuevo usuario en la app móvil
     *
     * @param RegisterAppMovilRequest $request
     * @return JsonResponse
     */
    public function register(RegisterAppMovilRequest $request): JsonResponse
    {
        try {
            // Crear el usuario (el mutator setPasswordAttribute encripta automáticamente)
            $usuario = UsuarioAppMovil::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'contraseña' => $request->contraseña, // Se encripta automáticamente
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'cedula' => $request->cedula,
                'tipo_documento' => strtoupper($request->tipo_documento),
                'rol' => 'usuario',
                'activo' => true,
                'estado_id' => null,
                'correo_verificado' => null,
                'otp' => null,
                'imagen_perfil' => null,
                'imagen_documento' => null,
                'identidad_verificada' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'data' => [
                    'usuario_id' => $usuario->usuario_id,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'correo' => $usuario->correo,
                    'cedula' => $usuario->cedula_completa,
                    'telefono' => $usuario->telefono,
                    'direccion' => $usuario->direccion,
                    'fecha_nacimiento' => $usuario->fecha_nacimiento->format('Y-m-d'),
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login de usuario en la app móvil
     *
     * @param LoginAppMovilRequest $request
     * @return JsonResponse
     */
    public function login(LoginAppMovilRequest $request): JsonResponse
    {
        try {
            // Buscar usuario por correo
            $usuario = UsuarioAppMovil::where('correo', $request->correo)->first();

            // Validar si el usuario existe
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ], 401);
            }

            // Validar si el usuario está activo
            if (!$usuario->activo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario deshabilitado. Contacte al administrador.'
                ], 403);
            }

            // Verificar la contraseña
            if (!Hash::check($request->contraseña, $usuario->contraseña)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ], 401);
            }

            // Login exitoso
            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'data' => [
                    'usuario_id' => $usuario->usuario_id,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'correo' => $usuario->correo,
                    'cedula' => $usuario->cedula_completa, // Formato: V-12345678
                    'telefono' => $usuario->telefono,
                    'direccion' => $usuario->direccion,
                    'fecha_nacimiento' => $usuario->fecha_nacimiento ? $usuario->fecha_nacimiento->format('Y-m-d') : null,
                    'rol' => $usuario->rol,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar contraseña de usuario
     *
     * @param CambiarContraseñaRequest $request
     * @return JsonResponse
     */
    public function cambiarContraseña(CambiarContraseñaRequest $request): JsonResponse
    {
        try {
            // Buscar usuario por correo
            $usuario = UsuarioAppMovil::where('correo', $request->correo)->first();

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar contraseña actual
            if (!Hash::check($request->contraseña_actual, $usuario->contraseña)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta'
                ], 401);
            }

            // Actualizar contraseña
            $usuario->contraseña = $request->contraseña_nueva;
            $usuario->save();

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar la contraseña',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar correo de usuario
     *
     * @param CambiarCorreoRequest $request
     * @return JsonResponse
     */
    public function cambiarCorreo(CambiarCorreoRequest $request): JsonResponse
    {
        try {
            // Buscar usuario por correo actual
            $usuario = UsuarioAppMovil::where('correo', $request->correo_actual)->first();

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar contraseña
            if (!Hash::check($request->contraseña, $usuario->contraseña)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña incorrecta'
                ], 401);
            }

            // Actualizar correo
            $usuario->correo = $request->correo_nuevo;
            $usuario->save();

            return response()->json([
                'success' => true,
                'message' => 'Correo actualizado exitosamente',
                'data' => [
                    'correo_nuevo' => $usuario->correo
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el correo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar cuenta de usuario
     *
     * @param EliminarCuentaRequest $request
     * @return JsonResponse
     */
    public function eliminarCuenta(EliminarCuentaRequest $request): JsonResponse
    {
        try {
            // Buscar usuario por correo
            $usuario = UsuarioAppMovil::where('correo', $request->correo)->first();

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Verificar contraseña
            if (!Hash::check($request->contraseña, $usuario->contraseña)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña incorrecta'
                ], 401);
            }

            // Eliminar usuario
            $usuario->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cuenta eliminada exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la cuenta',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
