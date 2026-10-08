<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * Sesión/token CSRF expirado (error 419): en vez de mostrar la pantalla
     * "Page Expired", devolvemos al usuario al login limpio con un aviso.
     * Cubre login, dashboard y cambio de rol (peticiones normales); las
     * peticiones de Livewire se manejan aparte en resources/js/app.js.
     */
    public function render($request, Throwable $e)
    {
        if ($this->esSesionExpirada($e)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tu sesión expiró. Por favor ingresa de nuevo.',
                ], 419);
            }

            // Solo mostramos el aviso "tu sesión se cerró" cuando el token
            // expirado ocurre estando dentro del sistema (petición distinta al
            // propio login). Si el 419 sucede al enviar el formulario de login
            // (usuario aún no autenticado), redirigimos al login limpio para
            // que reintente con un token fresco, sin un mensaje confuso.
            if ($request->routeIs('login')) {
                return redirect()->route('login');
            }

            return redirect()->route('login', ['expirado' => 1]);
        }

        return parent::render($request, $e);
    }

    /**
     * Determina si la excepción corresponde a una sesión/token CSRF expirado.
     */
    protected function esSesionExpirada(Throwable $e): bool
    {
        return $e instanceof TokenMismatchException
            || ($e instanceof HttpExceptionInterface && $e->getStatusCode() === 419);
    }
}
