<?php

namespace App\Http\Controllers\Api\Cartero;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Login / logout / refresh / profile para la app móvil del cartero.
 *
 * Convención de tokens:
 *   - access_token: Sanctum PAT con ability "cartero:access", TTL 12h.
 *   - refresh_token: Sanctum PAT con ability "cartero:refresh", TTL 30d.
 */
class AuthCarteroController extends Controller
{
    private const ACCESS_TTL_MINUTES = 60 * 12;       // 12h
    private const REFRESH_TTL_MINUTES = 60 * 24 * 30; // 30d
    private const ALLOWED_ROLE_IDS = [1, 7];
    private const ALLOWED_ROLE_NAMES = ['SuperAdmin', 'Repartidor Postal Telegrafico'];

    /** POST /cartero/v1/auth/login */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cedula' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('cedula', $data['cedula'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'cedula' => ['Cédula o contraseña incorrectas'],
            ]);
        }

        if (!$user->activo) {
            return response()->json(['message' => 'Cuenta desactivada'], 403);
        }

        $hasRole = $user->roles()
            ->where(function ($q) {
                $q->whereIn('id', self::ALLOWED_ROLE_IDS)
                  ->orWhereIn('name', self::ALLOWED_ROLE_NAMES);
            })
            ->exists();
        if (!$hasRole) {
            return response()->json(['message' => 'No tiene rol de cartero'], 403);
        }

        $tokens = $this->issueTokens($user);

        return response()->json([
            'access_token' => $tokens['access'],
            'refresh_token' => $tokens['refresh'],
            'user' => $this->serializeUser($user),
        ]);
    }

    /** POST /cartero/v1/auth/refresh */
    public function refresh(Request $request): JsonResponse
    {
        $data = $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        /** @var \Laravel\Sanctum\PersonalAccessToken|null $token */
        $token = \Laravel\Sanctum\PersonalAccessToken::findToken($data['refresh_token']);
        if (!$token || !$token->can('cartero:refresh')) {
            return response()->json(['message' => 'Refresh token inválido'], 401);
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            $token->delete();
            return response()->json(['message' => 'Refresh token expirado'], 401);
        }

        $user = $token->tokenable;
        if (!$user instanceof User) {
            return response()->json(['message' => 'Usuario no disponible'], 401);
        }

        // Rotación: invalidar el refresh viejo.
        $token->delete();

        $tokens = $this->issueTokens($user);

        return response()->json([
            'access_token' => $tokens['access'],
            'refresh_token' => $tokens['refresh'],
        ]);
    }

    /** POST /cartero/v1/auth/logout */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            // Revoca todos los tokens cartero del usuario actual.
            $user->tokens()
                ->where(function ($q) {
                    $q->where('name', 'like', 'cartero-%');
                })
                ->delete();
        }
        return response()->json(['message' => 'ok']);
    }

    /** GET /cartero/v1/profile */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->serializeUser($request->user()),
        ]);
    }

    private function issueTokens(User $user): array
    {
        $sessionId = (string) Str::uuid();

        $access = $user->createToken(
            "cartero-access-{$sessionId}",
            ['cartero:access'],
            now()->addMinutes(self::ACCESS_TTL_MINUTES)
        );

        $refresh = $user->createToken(
            "cartero-refresh-{$sessionId}",
            ['cartero:refresh'],
            now()->addMinutes(self::REFRESH_TTL_MINUTES)
        );

        return [
            'access' => $access->plainTextToken,
            'refresh' => $refresh->plainTextToken,
        ];
    }

    private function serializeUser(User $user): array
    {
        $user->loadMissing(['roles', 'oficina']);
        $roleIds = $user->roles->pluck('id')->all();
        $roleNames = $user->roles->pluck('name')->all();
        $isAdmin = in_array(1, $roleIds, true) || in_array('SuperAdmin', $roleNames, true);

        return [
            'id' => (string) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'cedula' => $user->cedula,
            'phone' => $user->telefono,
            'employee_code' => $user->cedula,
            'zone' => optional($user->oficina)->nombre ?? '',
            'is_admin' => $isAdmin,
            'is_active' => (bool) $user->activo,
            'roles' => $roleIds,
        ];
    }
}
