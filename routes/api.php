<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthAppMovilController;
use App\Http\Controllers\Api\AuthTelegramasController;
use App\Http\Controllers\Api\EnvioController;
use App\Http\Controllers\Api\SacasController;
use App\Http\Controllers\Api\EnviosController;
use App\Http\Controllers\Api\ViajesController;
use App\Http\Controllers\Api\EnvioCedulaController;
use App\Http\Controllers\Api\OficinasController;
use App\Http\Controllers\Api\incidenciasController;
use App\Http\Controllers\Api\encaminamientoEnvioController;
use App\Http\Controllers\Api\TelegramaFlutterController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ============================
// RUTAS APP MÓVIL
// ============================
Route::prefix('app')->group(function () {
    // Registro de usuario - Rate limit: 3 intentos por hora (comentado para testing)
    Route::post('/register', [AuthAppMovilController::class, 'register'])
        ->middleware('throttle:3,60');

    // Login de usuario - Rate limit: 5 intentos por minuto (comentado para testing)
    Route::post('/login', [AuthAppMovilController::class, 'login'])
        ->middleware('throttle:5,1');

    // Cambiar contraseña
    Route::post('/cambiar-contraseña', [AuthAppMovilController::class, 'cambiarContraseña'])
        ->middleware('throttle:5,1');

    // Cambiar correo
    Route::post('/cambiar-correo', [AuthAppMovilController::class, 'cambiarCorreo'])
        ->middleware('throttle:5,1');

    // Eliminar cuenta
    Route::post('/eliminar-cuenta', [AuthAppMovilController::class, 'eliminarCuenta'])
        ->middleware('throttle:3,60');
});

// ============================
// RUTAS SERVICIO DE TELEGRAMAS
// ============================
Route::prefix('telegramas')->group(function () {
    // Rutas públicas (sin autenticación)
    // Catálogos para la app Flutter (tipos remitente, lugares emisión, circuitos)
    Route::get('/catalogos', [TelegramaFlutterController::class, 'catalogos']);

    // Registro de usuario - Servicio de Telegramas
    Route::post('/register', [AuthTelegramasController::class, 'register']);

    // Login específico para servicio de telegramas
    Route::post('/login', [AuthTelegramasController::class, 'login']);

    // Recuperación de contraseña (ruta pública - temporal)
    // Reutiliza cambiarContraseña: requiere correo + contraseña actual + nueva
    // TODO: Reemplazar con flujo OTP cuando se implemente el servicio de correo
    Route::post('/recuperar-contrasena', [AuthTelegramasController::class, 'cambiarContraseña']);

    // Rutas protegidas con token Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // Cambiar contraseña - Servicio de Telegramas (PUT según contrato SISPVEN)
        Route::put('/cambiar-contraseña', [AuthTelegramasController::class, 'cambiarContraseña']);

        // Cambiar correo - Servicio de Telegramas (PUT según contrato SISPVEN)
        Route::put('/cambiar-correo', [AuthTelegramasController::class, 'cambiarCorreo']);

        // Eliminar cuenta - Servicio de Telegramas (DELETE según contrato SISPVEN)
        Route::delete('/eliminar-cuenta', [AuthTelegramasController::class, 'eliminarCuenta']);

        // ── Flutter Telegramas App ──
        Route::post('/consignar', [TelegramaFlutterController::class, 'consignar']);
        Route::get('/mis-enviados', [TelegramaFlutterController::class, 'misEnviados']);
        Route::get('/mis-recibidos', [TelegramaFlutterController::class, 'misRecibidos']);

        // Logout: revoca el token Sanctum actual.
        Route::post('/logout', [AuthTelegramasController::class, 'logout']);
    });
});

// ============================
// RUTAS SISTEMA WEB (existentes)
// ============================
Route::post('login', [AuthController::class, 'login']);

// Oficinas
Route::get('/oficinas', [OficinasController::class, 'index']);
Route::get('/oficinas/{id}', [OficinasController::class, 'show']);

// Estados (Para Oficinas)
Route::get('/estados', [OficinasController::class, 'estados']);
Route::get('/oficinas_estado/{estado}', [OficinasController::class, 'oficinas_relacionadas']);
Route::get('/oficinas_relacion/{id}', [OficinasController::class, 'opt_relacionadas']);

// Envios
Route::get('/envios', [EnviosController::class, 'index']);
Route::get('/envios/{id}', [EnviosController::class, 'show']);
Route::get('/almacen/{id}', [EnviosController::class, 'getEnviosInAlmacen']);

// Sacas
Route::get('/sacas/{id}', [SacasController::class, 'index']);
Route::post('/insacas', [SacasController::class, 'store']);
Route::post('/linksacas', [SacasController::class, 'link']);
Route::get('/get/{id}', [SacasController::class, 'getEnviosBySaca']);
Route::get('/codesaca/{code}', [SacasController::class, 'codesaca']);
Route::get('/tiposaca', [SacasController::class, 'tiposaca']);

// Registrar Entrada / Salida
Route::post('/register', [encaminamientoEnvioController::class, 'register']);

// Viajes
Route::get('/viajes/{oficina}-{fecha}', [ViajesController::class, 'show']);
Route::get('/getviaje/{codigo}', [ViajesController::class, 'getViajeByCode']);

// Manifiesto
Route::post('/manifiesto', [encaminamientoEnvioController::class, 'manifiesto']);

//incidencias
Route::get('/incidencias', [incidenciasController::class, 'incidencias']);
Route::post('/incidenciasinsert', [incidenciasController::class, 'registerincidencia']);
Route::post('/EncaminamientoClientes', [encaminamientoEnvioController::class, 'showEncaminamientoCliente']);

//Envios - Búsqueda por guía/código (compatible con contrato SISPVEN)
Route::middleware('api.key')->group(function () {
    Route::post('/envios', [EnvioController::class, 'showPost']);
    Route::post('/envios/buscar', [EnvioCedulaController::class, 'buscar']);
});

// Consumo de Combustible para gráficas dashboard flota
Route::middleware('web')->group(function () {
    Route::get('/consumo-combustible', [\App\Http\Controllers\ConsumoCombustibleApiController::class, 'index']);
    Route::get('/consumo-combustible-test', [\App\Http\Controllers\ConsumoCombustibleApiController::class, 'test']);
});

// ============================
// RUTAS APP MÓVIL CARTERO
// ============================
// Endpoints aislados para la app Flutter "Cartero IPOSTEL".
// Prefijo: /api/cartero/v1/*  ·  Sanctum + rol cartero (1 o 7).
Route::prefix('cartero/v1')->group(function () {
    Route::post('auth/login', [\App\Http\Controllers\Api\Cartero\AuthCarteroController::class, 'login'])
        ->middleware('throttle:10,1');
    Route::post('auth/refresh', [\App\Http\Controllers\Api\Cartero\AuthCarteroController::class, 'refresh'])
        ->middleware('throttle:20,1');

    Route::middleware(['auth:sanctum', 'cartero'])->group(function () {
        Route::post('auth/logout',  [\App\Http\Controllers\Api\Cartero\AuthCarteroController::class, 'logout']);
        Route::get ('profile',      [\App\Http\Controllers\Api\Cartero\AuthCarteroController::class, 'profile']);

        Route::get ('routes/daily', [\App\Http\Controllers\Api\Cartero\RouteCarteroController::class, 'daily']);

        Route::get ('deliveries/{id}',          [\App\Http\Controllers\Api\Cartero\DeliveryCarteroController::class, 'show']);
        Route::put ('deliveries/{id}/status',   [\App\Http\Controllers\Api\Cartero\DeliveryCarteroController::class, 'updateStatus']);
        Route::post('deliveries/{id}/final',    [\App\Http\Controllers\Api\Cartero\DeliveryCarteroController::class, 'final']);
        Route::post('deliveries/{id}/attempt',  [\App\Http\Controllers\Api\Cartero\DeliveryCarteroController::class, 'attempt']);
        Route::post('deliveries/{id}/evidence', [\App\Http\Controllers\Api\Cartero\EvidenceCarteroController::class, 'upload']);

        Route::get ('catalogs/return-reasons', [\App\Http\Controllers\Api\Cartero\CatalogCarteroController::class, 'returnReasons']);

        Route::get ('settings/recipient-requirements', [\App\Http\Controllers\Api\Cartero\RecipientRequirementController::class, 'show']);
        Route::put ('settings/recipient-requirements', [\App\Http\Controllers\Api\Cartero\RecipientRequirementController::class, 'update'])
            ->middleware('cartero.admin');

        Route::post('devices/register', [\App\Http\Controllers\Api\Cartero\DeviceCarteroController::class, 'register']);
        Route::get ('stats/me',         [\App\Http\Controllers\Api\Cartero\StatsCarteroController::class, 'me']);
    });
});

// ============================
// RUTAS APP MÓVIL CLIENTES (Recolectas)
// ============================
// Endpoints aislados para la feature Recolectas de la app de clientes.
// Prefijo: /api/clientes/v1/*  ·  Sanctum + cliente (sispven_app.usuarios).
// El registro de cuenta sigue en POST /api/app/register.
Route::prefix('clientes/v1')->group(function () {
    Route::post('auth/login', [\App\Http\Controllers\Api\Clientes\AuthClienteController::class, 'login'])
        ->middleware('throttle:10,1');
    Route::post('auth/refresh', [\App\Http\Controllers\Api\Clientes\AuthClienteController::class, 'refresh'])
        ->middleware('throttle:20,1');

    Route::middleware(['auth:sanctum', 'cliente'])->group(function () {
        Route::post('auth/logout', [\App\Http\Controllers\Api\Clientes\AuthClienteController::class, 'logout']);
        Route::get ('profile',     [\App\Http\Controllers\Api\Clientes\AuthClienteController::class, 'profile']);

        // Catálogos para el formulario de recolecta
        Route::get ('catalogs/estados',                                [\App\Http\Controllers\Api\Clientes\CatalogoClienteController::class, 'estados']);
        Route::get ('catalogs/estados/{estado}/municipios',            [\App\Http\Controllers\Api\Clientes\CatalogoClienteController::class, 'municipios']);
        Route::get ('catalogs/municipios/{municipio}/parroquias',      [\App\Http\Controllers\Api\Clientes\CatalogoClienteController::class, 'parroquias']);
        Route::get ('catalogs/municipios/{municipio}/ciudades',        [\App\Http\Controllers\Api\Clientes\CatalogoClienteController::class, 'ciudades']);
        Route::get ('catalogs/parroquias/{parroquia}/codigos-postales', [\App\Http\Controllers\Api\Clientes\CatalogoClienteController::class, 'codigosPostales']);
        Route::get ('catalogs/tipos-documento',                        [\App\Http\Controllers\Api\Clientes\CatalogoClienteController::class, 'tiposDocumento']);
        Route::get ('catalogs/metodos-pago',                           [\App\Http\Controllers\Api\Clientes\CatalogoClienteController::class, 'metodosPago']);
        Route::get ('catalogs/bancos',                                 [\App\Http\Controllers\Api\Clientes\CatalogoClienteController::class, 'bancos']);
        Route::get ('catalogs/estados-recolecta',                      [\App\Http\Controllers\Api\Clientes\CatalogoClienteController::class, 'estadosRecolecta']);

        // Recolectas
        Route::post('recolectas/cotizar',        [\App\Http\Controllers\Api\Clientes\RecolectaClienteController::class, 'cotizar']);
        Route::post('recolectas',                [\App\Http\Controllers\Api\Clientes\RecolectaClienteController::class, 'store']);
        Route::get ('recolectas',                [\App\Http\Controllers\Api\Clientes\RecolectaClienteController::class, 'index']);
        Route::get ('recolectas/{id}',           [\App\Http\Controllers\Api\Clientes\RecolectaClienteController::class, 'show']);
        Route::post('recolectas/{id}/cancelar',  [\App\Http\Controllers\Api\Clientes\RecolectaClienteController::class, 'cancelar']);

        // Pago por adelantado (estructura; verificación en stand-by)
        Route::post('recolectas/{id}/pago', [\App\Http\Controllers\Api\Clientes\PagoRecolectaClienteController::class, 'store']);
    });
});
