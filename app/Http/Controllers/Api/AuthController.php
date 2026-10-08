<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Oficina;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Rate limiting: máximo 3 intentos por minuto por IP
        $key = 'login:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => 'Demasiados intentos de inicio de sesión. Intente de nuevo en ' . $seconds . ' segundos.',
                'errors' => 'Too Many Attempts',
                'retry_after' => $seconds
            ], 429);
        }

        RateLimiter::hit($key, 60);

        if ( !Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')], $request->filled('remember'))) {
            return response()->json([
                'success' => false,
                'message' => 'Error de credenciales',
                'errors' => 'Unauthorized'
            ], 401);
        }

        // En un futuro es necesario validar la version de la app, y asi gestionar las version de la misma

        // Ya desde la app se manda la info de la version de la app

        $user = User::where('email', $request->email)
        ->firstOrFail();

        if (! $user->activo) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no activo',
                'errors' => 'Non-active user'
            ], 401);
        }

        // Login exitoso: limpiar el contador de intentos
        RateLimiter::clear($key);

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->getRoleNames()->first();
        
        $user->oficina;
        
        $office = Oficina::find($user->oficina);
        
        return response()->json([
            'success' => true,
            'message' => 'Usuario iniciado exitosamente!',
            'data' => [
                'user'       => $user,
                'office'     => $office,
                'token'      => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }
}

