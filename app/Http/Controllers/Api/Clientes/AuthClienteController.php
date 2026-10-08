<?php

namespace App\Http\Controllers\Api\Clientes;

use App\Http\Controllers\Controller;
use App\Models\UsuarioAppMovil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Login / logout / refresh / profile para la app móvil de clientes
 * (sispven_app.usuarios). Mismo esquema de tokens que la API cartero:
 *
 *   - access_token: Sanctum PAT con ability "cliente:access" (TTL config recolectas.token_access_horas).
 *   - refresh_token: Sanctum PAT con ability "cliente:refresh" (TTL config recolectas.token_refresh_dias), con rotación.
 *
 * El registro de cuenta sigue en POST /api/app/register (no se toca).
 */
class AuthClienteController extends Controller
{
    /** POST /clientes/v1/auth/login */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'correo' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $usuario = UsuarioAppMovil::where('correo', $data['correo'])->first();
        if (!$usuario || !Hash::check($data['password'], $usuario->getAuthPassword())) {
            throw ValidationException::withMessages([
                'correo' => ['Correo o contraseña incorrectos'],
            ]);
        }

        if (!$usuario->activo) {
            return response()->json(['message' => 'Cuenta desactivada'], 403);
        }

        $tokens = $this->issueTokens($usuario);

        return response()->json([
            'access_token' => $tokens['access'],
            'refresh_token' => $tokens['refresh'],
            'user' => $this->serializeUsuario($usuario),
        ]);
    }

    /** POST /clientes/v1/auth/refresh */
    public function refresh(Request $request): JsonResponse
    {
        $data = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        /** @var \Laravel\Sanctum\PersonalAccessToken|null $token */
        $token = \Laravel\Sanctum\PersonalAccessToken::findToken($data['refresh_token']);
        if (!$token || !$token->can('cliente:refresh')) {
            return response()->json(['message' => 'Refresh token inválido'], 401);
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            $token->delete();
            return response()->json(['message' => 'Refresh token expirado'], 401);
        }

        $usuario = $token->tokenable;
        if (!$usuario instanceof UsuarioAppMovil) {
            return response()->json(['message' => 'Usuario no disponible'], 401);
        }

        // Rotación: invalidar el refresh viejo.
        $token->delete();

        $tokens = $this->issueTokens($usuario);

        return response()->json([
            'access_token' => $tokens['access'],
            'refresh_token' => $tokens['refresh'],
        ]);
    }

    /** POST /clientes/v1/auth/logout */
    public function logout(Request $request): JsonResponse
    {
        $usuario = $request->user();
        if ($usuario) {
            $usuario->tokens()
                ->where('name', 'like', 'cliente-%')
                ->delete();
        }

        return response()->json(['message' => 'ok']);
    }

    /** GET /clientes/v1/profile */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->serializeUsuario($request->user()),
        ]);
    }

    private function issueTokens(UsuarioAppMovil $usuario): array
    {
        $sessionId = (string) Str::uuid();

        $access = $usuario->createToken(
            "cliente-access-{$sessionId}",
            ['cliente:access'],
            now()->addHours((int) config('recolectas.token_access_horas'))
        );

        $refresh = $usuario->createToken(
            "cliente-refresh-{$sessionId}",
            ['cliente:refresh'],
            now()->addDays((int) config('recolectas.token_refresh_dias'))
        );

        return [
            'access' => $access->plainTextToken,
            'refresh' => $refresh->plainTextToken,
        ];
    }

    private function serializeUsuario(UsuarioAppMovil $usuario): array
    {
        return [
            'id' => (string) $usuario->usuario_id,
            'nombre' => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'correo' => $usuario->correo,
            'telefono' => $usuario->telefono,
            'direccion' => $usuario->direccion,
            'tipo_documento' => $usuario->tipo_documento,
            'cedula' => (string) $usuario->cedula,
            'estado_id' => $usuario->estado_id !== null ? (string) $usuario->estado_id : null,
            'identidad_verificada' => (bool) $usuario->identidad_verificada,
        ];
    }
}
