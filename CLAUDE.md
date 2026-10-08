# CLAUDE.md — Backend IPOSTEL

Contexto para asistentes IA y desarrolladores nuevos en el repo. Léelo antes de tocar código.

## Qué es

Backend monolítico Laravel + frontend web (Blade + Vite + Livewire) para IPOSTEL, el servicio postal estatal de Venezuela. Cubre:

- Gestión de envíos postales (sacas, viajes, encaminamiento, incidencias).
- **Servicio de telegramas** — frontend Blade tradicional para operadores en oficina.
- **API móvil** para una **app Flutter** independiente (repo: `c:\Proyectos\telegrama_app_sispven`) donde ciudadanos crean y consultan sus propios telegramas.

## Stack

- Laravel 11 + PHP 8.x
- PostgreSQL (importante: tiene **dos schemas** — ver abajo)
- Sanctum para autenticación de la API móvil (tokens 24h)
- Blade + Livewire + Vite + Tailwind para la web
- `php -l` para validar sintaxis sin levantar Laravel

## Dos schemas de PostgreSQL — gotcha crítico

| Schema | Tabla usuarios | Modelo | Usado por |
|---|---|---|---|
| `public` (default) | `users` | `User` (estándar Laravel) | Sistema web (operadores IPOSTEL) |
| `sispven_app` | `sispven_app.usuarios` | `UsuarioAppMovil` | App móvil Flutter |

**Los IDs de las dos tablas NO se cruzan.** Un `usuario_id = 5` en `sispven_app.usuarios` no es el mismo que `id = 5` en `users`.

### Implicación clave para `envios.usuario_id`

La tabla `envios` tiene la columna `usuario_id` con **FK a `users.id`** (sistema web), ver migración [database/migrations/2024_10_01_001915_create_envios_table.php:68](database/migrations/2024_10_01_001915_create_envios_table.php#L68).

**Cuando un usuario de la app móvil crea un telegrama, `envios.usuario_id` se guarda como `NULL`.** Si intentas guardar `$usuario->usuario_id` de `UsuarioAppMovil`, el FK constraint puede rechazar el insert o (peor) hacer match accidental con un usuario web cuyo `id` coincida → fuga de datos.

Ver [TelegramaFlutterController.php:79-91](app/Http/Controllers/Api/TelegramaFlutterController.php#L79-L91) — está documentado con un comentario.

El owner del telegrama móvil se identifica por `documento_rem` + `tipo_documento_rem` que deben coincidir con `cedula` + `tipo_documento` del usuario autenticado. La app pre-pobla esos campos para garantizar coincidencia.

## Rama `fix-Telegrama`

**Toda la integración con la app móvil vive en la rama `fix-Telegrama`.** No tocar `main`/`master` para cambios relacionados con el servicio de telegramas Flutter sin avisar al equipo. La rama está adelantada >429 commits del origin de la misma rama.

## Endpoints de la API móvil

Prefijo: `/api/telegramas` (ver [routes/api.php](routes/api.php)).

### Públicos
- `POST /register` → `AuthTelegramasController::register`
- `POST /login` → `AuthTelegramasController::login` (devuelve token Sanctum 24h)
- `POST /recuperar-contrasena` → reutiliza `cambiarContraseña` (TODO: reemplazar con OTP)
- `GET /catalogos` → `TelegramaFlutterController::catalogos`

### Autenticados con `auth:sanctum`
- `POST /logout` → `AuthTelegramasController::logout` — revoca solo el token actual.
- `PUT /cambiar-contraseña` → `AuthTelegramasController::cambiarContraseña`
- `PUT /cambiar-correo` → `AuthTelegramasController::cambiarCorreo`
- `DELETE /eliminar-cuenta` → `AuthTelegramasController::eliminarCuenta`
- `POST /consignar` → `TelegramaFlutterController::consignar`
- `GET /mis-enviados?page=N` → paginador Laravel, 15 por página
- `GET /mis-recibidos?page=N` → paginador Laravel, 15 por página

## Convenciones del contrato API móvil

### Snake_case singular en payloads de remitente/destinatario

[ConsignarTelegramaRequest.php](app/Http/Requests/Telegramas/ConsignarTelegramaRequest.php) valida `nombre`, `apellido`, `correo`, `telefono`, `direccion` (no `nombres`, `apellidos`, `email`). La app móvil tiene `toApiJson()` que respeta esto.

### Login response

```json
{
  "success": true,
  "data": {
    "user": {
      "id", "nombre", "apellido", "nombre_completo",
      "username", "correo", "cedula", "tipo_documento",
      "telefono", "direccion", "rol", "oficina_asignada"
    },
    "token": "..."
  }
}
```

El campo `tipo_documento` fue añadido para que la app pueda pre-poblar el remitente. **Cualquier cambio futuro debe ser aditivo** (agregar campos, no quitar ni renombrar).

### Errores

Todo error 4xx/5xx devuelve `{success: false, message: "..."}`. La app sabe parsear esa estructura.

## Modelos relevantes

- `UsuarioAppMovil` — `app/Models/UsuarioAppMovil.php`. Tabla `sispven_app.usuarios`. PK `usuario_id`. Hash de password con mutator (cuidado con doble hash).
- `Envio` — `app/Models/Envio.php`. Tabla `envios`.
- `TelegramaRecibido` — campos específicos de telegramas (`contenido_telegrama`, `palabras_tasables`, ids de catálogos, flag `recibido`). FK `envio_id`.
- `CircuitoJudicialTribunalTelegrama`, `LugarEmisionTelegrama`, etc. — catálogos del servicio de telegramas.

## Validación

Antes de commit:

```bash
php -l app/Http/Controllers/Api/SomeController.php   # syntax
php artisan migrate:status                            # ver migraciones
php artisan route:list --path=api/telegramas         # listar rutas
```

## Cosas peligrosas

- **No** cambies el FK de `envios.usuario_id`. Es un constraint que la web depende.
- **No** modifiques `AuthTelegramasController::cambiarContraseña` para usar `$request->user()` sin antes coordinar con el equipo web (actualmente acepta el correo del payload, lo cual es inseguro pero documentado como pendiente).
- **No** activar los métodos OTP comentados en `AuthTelegramasController` hasta que configuren `MAIL_*` en `.env`.
- **Sanctum**: revocar `currentAccessToken()`, no `tokens()` (no tirar la sesión de otros dispositivos del mismo usuario).
- **`migrate:fresh`** en producción borrará `public` pero **no** `sispven_app` (ver guard en `2025_11_24_113115_create_usuarios_in_app_movil_schema.php`).

## Repo de la app móvil

`c:\Proyectos\telegrama_app_sispven`, rama `main`. Flutter. Ver `CLAUDE.md` allí para detalles del cliente.

## Pendientes documentados

- **#4**: `cambiar-contraseña` debe usar `$request->user()` en vez de `$request->correo` (vulnerabilidad: un usuario logueado puede cambiar la contraseña de otro si conoce ambas).
- **#14**: Flujo OTP real para `recuperar-contrasena` — los métodos `solicitarRecuperacion()` y `resetearContrasena()` ya están en el controlador pero comentados. Activar cuando haya servicio de correo configurado.
