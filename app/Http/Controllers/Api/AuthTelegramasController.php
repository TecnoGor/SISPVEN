<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Telegramas\LoginTelegramasRequest;
use App\Http\Requests\Telegramas\RegisterTelegramasRequest;
use App\Http\Requests\Telegramas\CambiarContraseñaTelegramasRequest;
use App\Http\Requests\Telegramas\CambiarCorreoTelegramasRequest;
use App\Http\Requests\Telegramas\EliminarCuentaTelegramasRequest;
use App\Http\Requests\Telegramas\RecuperarContrasenaTelegramasRequest;
use App\Http\Requests\Telegramas\ResetearContrasenaTelegramasRequest;
use App\Models\UsuarioAppMovil;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthTelegramasController extends Controller
{
    /**
     * Registrar nuevo usuario - Servicio de Telegramas
     *
     * @param RegisterTelegramasRequest $request
     * @return JsonResponse
     */
    public function register(RegisterTelegramasRequest $request): JsonResponse
    {
        try {
            // Hashear la contraseña antes de insertar
            $hashedPassword = Hash::make($request->password);

            // Usar DB::table() directamente para evitar problemas con el carácter ñ en Eloquent
            $usuarioId = DB::table('sispven_app.usuarios')->insertGetId([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'contraseña' => $hashedPassword,
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
                'created_at' => now(),
                'updated_at' => now(),
            ], 'usuario_id');

            // Log de verificación
            \Log::info('TELEGRAMAS REGISTER: Usuario creado', [
                'usuario_id' => $usuarioId,
                'correo' => $request->correo,
                'hash_starts_with_2y' => str_starts_with($hashedPassword, '$2y$'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente - Servicio de Telegramas',
            ], 201);

        } catch (\Exception $e) {
            \Log::error('TELEGRAMAS REGISTER ERROR: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el usuario',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Login de usuario - Servicio de Telegramas
     * Genera un token Sanctum para autenticación JWT-like
     *
     * @param LoginTelegramasRequest $request
     * @return JsonResponse
     */
    public function login(LoginTelegramasRequest $request): JsonResponse
    {
        try {
            // Buscar usuario por correo — intentar Eloquent primero
            $usuario = UsuarioAppMovil::where('correo', $request->correo)->first();

            // Fallback a raw DB si Eloquent no encontró el usuario
            // (puede ocurrir por problemas de schema/search_path en PostgreSQL)
            if (!$usuario) {
                $usuarioRaw = DB::table('sispven_app.usuarios')
                    ->where('correo', $request->correo)
                    ->first();

                if (!$usuarioRaw) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Credenciales inválidas',
                    ], 401);
                }

                // Recargar por ID para obtener un modelo Eloquent completo (necesario para Sanctum)
                $usuario = UsuarioAppMovil::find($usuarioRaw->usuario_id);

                if (!$usuario) {
                    \Log::error('TELEGRAMAS LOGIN: No se pudo cargar modelo Eloquent por ID', [
                        'usuario_id' => $usuarioRaw->usuario_id,
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Error al procesar el usuario',
                    ], 500);
                }
            }

            // Validar si el usuario está activo
            if (!$usuario->activo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario deshabilitado. Contacte al administrador.',
                ], 403);
            }

            // Obtener el hash directo de la BD (evita doble-hash del mutator en versiones antiguas)
            $hashEnBD = DB::table('sispven_app.usuarios')
                ->where('usuario_id', $usuario->usuario_id)
                ->value('contraseña');

            // Verificar contraseña: soporta hash bcrypt y texto plano (legacy)
            $esHashValido = $hashEnBD && Hash::check($request->password, $hashEnBD);
            $esTextoPlanoValido = $hashEnBD === $request->password;

            if (!$esHashValido && !$esTextoPlanoValido) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales inválidas',
                ], 401);
            }

            // Revocar tokens anteriores del usuario
            $usuario->tokens()->delete();

            // Generar token Sanctum con expiración de 24 horas
            $token = $usuario->createToken('sispven-telegramas', ['*'], now()->addHours(24))->plainTextToken;

            // Login exitoso - formato según contrato de API SISPVEN
            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'user' => [
                        'id' => $usuario->usuario_id,
                        'nombre' => $usuario->nombre,
                        'apellido' => $usuario->apellido,
                        'nombre_completo' => trim($usuario->nombre . ' ' . $usuario->apellido),
                        'username' => $usuario->correo,
                        'correo' => $usuario->correo,
                        'cedula' => (string) $usuario->cedula,
                        'tipo_documento' => $usuario->tipo_documento,
                        'telefono' => $usuario->telefono,
                        'direccion' => $usuario->direccion,
                        'rol' => $usuario->rol ?? 'usuario',
                        'oficina_asignada' => null,
                    ],
                    'token' => $token,
                ],
            ], 200);

        } catch (\Exception $e) {
            \Log::error('TELEGRAMAS LOGIN ERROR: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar sesión',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Logout - Servicio de Telegramas
     * Revoca el token Sanctum actual del usuario.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function logout(\Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $usuario = $request->user();
            // Revocar SOLO el token actual (no todos los del usuario).
            $token = $usuario?->currentAccessToken();
            if ($token) {
                $token->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada correctamente',
            ], 200);
        } catch (\Exception $e) {
            \Log::error('TELEGRAMAS LOGOUT ERROR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Cambiar contraseña de usuario - Servicio de Telegramas
     *
     * @param CambiarContraseñaTelegramasRequest $request
     * @return JsonResponse
     */
    public function cambiarContraseña(CambiarContraseñaTelegramasRequest $request): JsonResponse
    {
        try {
            // Buscar usuario por correo
            $usuario = UsuarioAppMovil::where('correo', $request->correo)->first();

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            // Verificar contraseña actual (soporta hash o texto plano)
            $esHashValido = Hash::check($request->password_actual, $usuario->contraseña);
            $esTextoPlanoValido = $usuario->contraseña === $request->password_actual;

            if (!$esHashValido && !$esTextoPlanoValido) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta',
                ], 401);
            }

            // Actualizar contraseña (bypass del mutator en el modelo)
            DB::table('sispven_app.usuarios')
                ->where('usuario_id', $usuario->usuario_id)
                ->update(['contraseña' => Hash::make($request->password_nueva)]);

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar la contraseña',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Cambiar correo de usuario - Servicio de Telegramas
     *
     * @param CambiarCorreoTelegramasRequest $request
     * @return JsonResponse
     */
    public function cambiarCorreo(CambiarCorreoTelegramasRequest $request): JsonResponse
    {
        try {
            // Buscar usuario por correo actual
            $usuario = UsuarioAppMovil::where('correo', $request->correo_actual)->first();

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            // Verificar contraseña (soporta hash o texto plano)
            $esHashValido = Hash::check($request->password, $usuario->contraseña);
            $esTextoPlanoValido = $usuario->contraseña === $request->password;

            if (!$esHashValido && !$esTextoPlanoValido) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña incorrecta',
                ], 401);
            }

            // Actualizar correo
            $usuario->correo = $request->correo_nuevo;
            $usuario->save();

            return response()->json([
                'success' => true,
                'message' => 'Correo actualizado correctamente',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el correo',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RECUPERACIÓN CON OTP (PENDIENTE - Requiere servicio de correo)
    |--------------------------------------------------------------------------
    | Estos métodos se activarán cuando se implemente el servicio de envío
    | de mensajes OTP por correo electrónico. Por ahora, la recuperación
    | de contraseña se maneja pidiendo la contraseña actual al usuario
    | a través de la ruta pública POST /api/telegramas/recuperar-contrasena
    | que reutiliza el método cambiarContraseña().
    |
    | Para activar el flujo OTP:
    | 1. Configurar MAIL_* en .env
    | 2. Descomentar los métodos solicitarRecuperacion() y resetearContrasena()
    | 3. Actualizar las rutas en routes/api.php
    | 4. Actualizar la app Flutter para usar el flujo de 2 pasos
    |--------------------------------------------------------------------------
    */

    // /**
    //  * Solicitar recuperación de contraseña - Genera OTP y envía por correo
    //  *
    //  * @param RecuperarContrasenaTelegramasRequest $request
    //  * @return JsonResponse
    //  */
    // public function solicitarRecuperacion(RecuperarContrasenaTelegramasRequest $request): JsonResponse
    // {
    //     try {
    //         // Buscar usuario por correo
    //         $usuario = DB::table('sispven_app.usuarios')
    //             ->where('correo', $request->correo)
    //             ->first();
    //
    //         if (!$usuario) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'No existe una cuenta con ese correo electrónico',
    //             ], 404);
    //         }
    //
    //         // Generar código OTP de 6 dígitos
    //         $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    //
    //         // Guardar OTP en la base de datos
    //         DB::table('sispven_app.usuarios')
    //             ->where('usuario_id', $usuario->usuario_id)
    //             ->update([
    //                 'otp' => $otp,
    //                 'updated_at' => now(),
    //             ]);
    //
    //         // Enviar correo con el OTP
    //         try {
    //             Mail::raw(
    //                 "Su código de recuperación de contraseña es: {$otp}\n\n"
    //                 . "Este código es válido por 15 minutos.\n\n"
    //                 . "Si usted no solicitó este cambio, ignore este mensaje.\n\n"
    //                 . "IPOSTEL - Servicio de Telegramas",
    //                 function ($message) use ($request) {
    //                     $message->to($request->correo)
    //                         ->subject('IPOSTEL - Código de recuperación de contraseña');
    //                 }
    //             );
    //         } catch (\Exception $mailError) {
    //             \Log::warning('TELEGRAMAS RECUPERAR: Error al enviar correo', [
    //                 'error' => $mailError->getMessage(),
    //                 'correo' => $request->correo,
    //             ]);
    //         }
    //
    //         \Log::info('TELEGRAMAS RECUPERAR: OTP generado', [
    //             'correo' => $request->correo,
    //             'otp' => $otp,
    //         ]);
    //
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Se ha enviado un código de verificación a su correo electrónico',
    //         ], 200);
    //
    //     } catch (\Exception $e) {
    //         \Log::error('TELEGRAMAS RECUPERAR ERROR: ' . $e->getMessage());
    //
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error al procesar la solicitud',
    //             'error' => config('app.debug') ? $e->getMessage() : null,
    //         ], 500);
    //     }
    // }

    // /**
    //  * Resetear contraseña con código OTP
    //  *
    //  * @param ResetearContrasenaTelegramasRequest $request
    //  * @return JsonResponse
    //  */
    // public function resetearContrasena(ResetearContrasenaTelegramasRequest $request): JsonResponse
    // {
    //     try {
    //         // Buscar usuario por correo
    //         $usuario = DB::table('sispven_app.usuarios')
    //             ->where('correo', $request->correo)
    //             ->first();
    //
    //         if (!$usuario) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'No existe una cuenta con ese correo electrónico',
    //             ], 404);
    //         }
    //
    //         // Verificar OTP
    //         if ($usuario->otp !== $request->otp) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'El código de verificación es incorrecto',
    //             ], 401);
    //         }
    //
    //         // Verificar que el OTP no haya expirado (15 minutos)
    //         $updatedAt = $usuario->updated_at ? \Carbon\Carbon::parse($usuario->updated_at) : null;
    //         if ($updatedAt && $updatedAt->diffInMinutes(now()) > 15) {
    //             DB::table('sispven_app.usuarios')
    //                 ->where('usuario_id', $usuario->usuario_id)
    //                 ->update(['otp' => null]);
    //
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'El código ha expirado. Solicite uno nuevo.',
    //             ], 401);
    //         }
    //
    //         // Actualizar contraseña y limpiar OTP
    //         DB::table('sispven_app.usuarios')
    //             ->where('usuario_id', $usuario->usuario_id)
    //             ->update([
    //                 'contraseña' => Hash::make($request->password_nueva),
    //                 'otp' => null,
    //                 'updated_at' => now(),
    //             ]);
    //
    //         \Log::info('TELEGRAMAS RESETEAR: Contraseña actualizada', [
    //             'correo' => $request->correo,
    //         ]);
    //
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Contraseña actualizada correctamente. Ya puede iniciar sesión.',
    //         ], 200);
    //
    //     } catch (\Exception $e) {
    //         \Log::error('TELEGRAMAS RESETEAR ERROR: ' . $e->getMessage());
    //
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error al resetear la contraseña',
    //             'error' => config('app.debug') ? $e->getMessage() : null,
    //         ], 500);
    //     }
    // }

    /**
     * Eliminar cuenta de usuario - Servicio de Telegramas
     *
     * @param EliminarCuentaTelegramasRequest $request
     * @return JsonResponse
     */
    public function eliminarCuenta(EliminarCuentaTelegramasRequest $request): JsonResponse
    {
        try {
            // Buscar usuario por correo
            $usuario = UsuarioAppMovil::where('correo', $request->correo)->first();

            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            // Verificar contraseña (soporta hash o texto plano)
            $esHashValido = Hash::check($request->password, $usuario->contraseña);
            $esTextoPlanoValido = $usuario->contraseña === $request->password;

            if (!$esHashValido && !$esTextoPlanoValido) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña incorrecta. No se puede eliminar la cuenta.',
                ], 401);
            }

            // Revocar todos los tokens antes de eliminar
            $usuario->tokens()->delete();

            // Eliminar usuario
            $usuario->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cuenta eliminada correctamente',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la cuenta',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
