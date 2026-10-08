# Manual Completo: Módulo de Correspondencia (Despacho Digital) - IPOSTEL

---

## Tabla de Contenidos

1. [Introducción](#1-introducción)
2. [Arquitectura Técnica y Archivos del Sistema](#2-arquitectura-técnica-y-archivos-del-sistema)
3. [Base de Datos: Tablas y Relaciones](#3-base-de-datos-tablas-y-relaciones)
4. [Roles, Permisos y Jerarquía](#4-roles-permisos-y-jerarquía)
5. [Panel del Administrador](#5-panel-del-administrador)
6. [Panel del Usuario General](#6-panel-del-usuario-general)
7. [Panel del Analista](#7-panel-del-analista)
8. [Panel del Gerente](#8-panel-del-gerente)
9. [Panel del Director](#9-panel-del-director)
10. [Panel del Presidente](#10-panel-del-presidente)
11. [Tipos de Documentos](#11-tipos-de-documentos)
12. [Estatus de los Comunicados](#12-estatus-de-los-comunicados)
13. [Flujo Completo de un Documento](#13-flujo-completo-de-un-documento)
14. [Sistema de Notificaciones](#14-sistema-de-notificaciones)
15. [Generación de PDF](#15-generación-de-pdf)
16. [Servicio de Flujo de Correspondencia](#16-servicio-de-flujo-de-correspondencia)

---

## 1. Introducción

El Módulo de Correspondencia (también llamado "Despacho Digital") es un sistema interno de gestión documental diseñado para el Instituto Postal Telegráfico de Venezuela (IPOSTEL). Su propósito es digitalizar la creación, envío, seguimiento, aprobación y archivo de comunicados oficiales entre las distintas dependencias y niveles jerárquicos de la institución.

### ¿Qué permite hacer el sistema?

- **Crear comunicados oficiales** (Memorandos, Circulares, Oficios, Minutas, Puntos de Cuenta, etc.) con formato institucional.
- **Enviar comunicados** a personas específicas dentro de la organización, según su rol jerárquico.
- **Recibir y confirmar** la recepción de documentos.
- **Responder** a comunicados recibidos, generando cadenas de conversación con trazabilidad completa.
- **Remitir** (reenviar) documentos a otros niveles jerárquicos (de Director a Gerente, de Gerente a Analista, etc.).
- **Devolver para corrección** documentos que contengan errores, con observaciones obligatorias.
- **Aprobar, firmar o archivar** documentos (exclusivo de Presidencia).
- **Generar PDFs** con formato oficial institucional listos para imprimir.
- **Dar seguimiento** a documentos importantes marcándolos para rastreo.
- **Administrar usuarios** del módulo, asignando y revocando roles de correspondencia.

### ¿Cómo se accede?

El usuario ingresa a la URL `/correspondencia`. El sistema detecta automáticamente su rol de correspondencia y lo redirige a la interfaz correspondiente:

- Presidente → `/correspondencia-presidente`
- Director → `/correspondencia-director`
- Gerente → `/correspondencia-gerente`
- Analista → `/correspondencia-analista`
- Usuario → `/correspondencia-usuario`
- Administrador → `/correspondencia-admin`

Cada ruta está protegida por un middleware de permisos (`can:Ver Correspondencia-[Rol]`), por lo que un usuario sin el rol asignado no puede acceder.

---

## 2. Arquitectura Técnica y Archivos del Sistema

El módulo está construido con **Laravel + Livewire** (componentes reactivos sin necesidad de JavaScript pesado). A continuación se detalla cada archivo, su ubicación y qué función cumple.

### 2.1 Componentes Livewire (Backend - Lógica de Negocio)

Ubicación: `app/Livewire/GestionCorrespondencia/`

| Archivo | Función |
|---------|---------|
| `CorrespondenciaMain.php` | **Enrutador principal.** Detecta el rol del usuario y lo redirige a la interfaz correcta. No tiene vista propia, solo lógica de redirección en su método `mount()`. |
| `CorrespondenciaAdmin.php` | **Panel del Administrador.** Gestiona la asignación y revocación de roles de correspondencia a usuarios del sistema. Contiene la lógica de búsqueda de usuarios, asignación de roles vía Spatie, estadísticas de distribución de roles y eliminación de acceso. |
| `CorrespondenciaUsuario.php` | **Panel del Usuario General.** Interfaz de solo lectura. Permite ver documentos recibidos, confirmar recepción y ver PDFs. No puede crear, responder ni remitir documentos. |
| `CorrespondenciaAnalista.php` | **Panel del Analista.** El más completo en creación de documentos. Contiene el formulario dinámico para los 12 tipos de documentos, validación de campos, lógica de respuesta, edición de documentos devueltos, carga de adjuntos y generación de códigos de control automáticos. |
| `CorrespondenciaGerente.php` | **Panel del Gerente.** Extiende las funcionalidades del Analista agregando: remisión de documentos (a Director u otro Gerente), devolución para corrección con observaciones, y seguimiento de documentos marcados. |
| `CorrespondenciaDirector.php` | **Panel del Director.** Similar al Gerente pero con alcance jerárquico mayor. Puede remitir a Presidente, otros Directores o Gerentes. Incluye opción de devolver al emisor o al creador original. |
| `CorrespondenciaPresidente.php` | **Panel del Presidente.** Nivel máximo de autoridad. Además de todas las funciones anteriores, puede: Aprobar y Firmar documentos (bloquea el documento definitivamente), Delegar Instrucciones (enviar a cualquier persona), y Negar y Archivar (cierra el documento permanentemente). |

### 2.2 Trait de Paginación

Ubicación: `app/Livewire/GestionCorrespondencia/Concerns/HasPaginacion.php`

Proporciona paginación reutilizable para la bandeja de entrada y la actividad reciente:
- `$pagina_inbox`, `$por_pagina_inbox` — control de página para la bandeja de entrada (10 por página).
- `$pagina_reciente`, `$por_pagina_reciente` — control de página para actividad reciente.
- `$busqueda_actividad` — campo de búsqueda en actividad reciente.
- Métodos `inbox_pagina()`, `reciente_pagina()`, `resetPaginaInbox()`, `resetPaginaReciente()`.

### 2.3 Servicio de Flujo

Ubicación: `app/Services/CorrespondenceFlowService.php`

Centraliza toda la lógica de movimiento de documentos entre usuarios. Contiene los métodos: `remitir()`, `devolver()`, `aprobarYFirmar()`, `archivar()`, `delegar()`. Se detalla en la [Sección 16](#16-servicio-de-flujo-de-correspondencia).

### 2.4 Modelos de Base de Datos

Ubicación: `app/Models/`

| Archivo | Función |
|---------|---------|
| `Comunicado.php` | Modelo principal. Representa un documento/comunicado. Contiene el método `toComponentArray()` que transforma el registro de BD en un array listo para la vista con toda la información de estatus, remitente, destinatario, trazabilidad, adjuntos, etc. También tiene el método `generateControlCode()` para generar códigos únicos automáticos. |
| `ComunicadoDestinatario.php` | Modelo pivote. Cada registro representa **un paso** en la cadena del documento: quién lo recibió (`usuario_id`), quién se lo envió (`emisor_id`), en qué estatus está (`estatus_id`), por qué motivo (`motivo_id`), y observaciones (`observacion`). Un documento puede tener muchos registros en esta tabla. |
| `ComunicadoAdjunto.php` | Modelo de adjuntos. Almacena archivos adjuntos (PDF, DOC, DOCX, XLS, XLSX) con nombre original, ruta en disco, tipo MIME y tamaño. Incluye un accessor `tamano_formateado` que muestra el tamaño en KB/MB. |
| `EstatusComunicacion.php` | Catálogo de los 8 estatus posibles de un comunicado (Pendiente, Leído, Confirmado, etc.) con su nombre y color de badge para la interfaz. |

### 2.5 Vistas Blade (Frontend - Interfaz de Usuario)

Ubicación: `resources/views/livewire/gestion-correspondencia/`

| Archivo | Función |
|---------|---------|
| `correspondencia-admin.blade.php` | Vista completa del panel de administración: dashboard con tarjetas de estadísticas, listado de usuarios con filtros, formulario de creación/edición, modal de confirmación de eliminación. |
| `correspondencia-usuario.blade.php` | Vista del usuario general: bienvenida, bandeja de entrada, detalle de documento con visor PDF. |
| `correspondencia-analista.blade.php` | Vista del analista: dashboard con métricas, bandeja de entrada con filtros, formulario de creación, detalle con acciones. |
| `correspondencia-gerente.blade.php` | Vista del gerente: igual que analista pero con botones adicionales de remitir, devolver y seguimiento. |
| `correspondencia-director.blade.php` | Vista del director: igual que gerente con opciones adicionales de destino al devolver. |
| `correspondencia-presidente.blade.php` | Vista del presidente: todos los botones anteriores más aprobar/firmar, delegar y archivar. |

### 2.6 Vistas Parciales (Partials)

Ubicación: `resources/views/livewire/gestion-correspondencia/partials/`

| Archivo | Función |
|---------|---------|
| `create.blade.php` | **Formulario de creación de documentos** (~93KB). Es el formulario más grande del sistema. Contiene el selector de tipo de documento, campos de remitente/destinatario, y secciones condicionales para cada uno de los 12 tipos de documento con todos sus campos específicos. Incluye búsqueda de destinatario, carga de adjuntos y botones de enviar/cancelar. |
| `detail.blade.php` | **Vista de detalle de un documento recibido** (~38KB). Muestra: encabezado con tipo/asunto/referencia, grilla de metadatos (De, Para, Fecha, Estatus, Límite), visor PDF embebido, sección de trazabilidad (recorrido del documento), y todos los botones de acción según el rol del usuario (Confirmar, Responder, Remitir, Devolver, Aprobar, etc.). |
| `detail-sent.blade.php` | **Vista de detalle de un documento enviado** (~16KB). Similar al detail pero para documentos que el usuario creó/envió. Muestra la trazabilidad de cómo ha sido procesado el documento por los destinatarios. |
| `inbox.blade.php` | **Bandeja de entrada** (~11KB). Sección de filtros (prioridad, estatus, rango de fechas), listado paginado de documentos recibidos con: ícono de tipo, asunto, remitente, fecha, badge de prioridad, badge de estatus. Controles de paginación. |
| `actividad-reciente.blade.php` | **Actividad reciente / Documentos enviados** (~11KB). Listado de documentos que el usuario ha creado o enviado, con búsqueda y paginación. Muestra el estatus actual de cada envío. |
| `remitir-modal.blade.php` | **Modal para remitir documentos** (~13KB). Ventana emergente que permite seleccionar un nuevo destinatario para reenviar un documento. Incluye filtro por rol y búsqueda por nombre/email. |
| `devolver-modal.blade.php` | **Modal para devolver documentos** (~6KB). Ventana emergente con campo de texto obligatorio para escribir el motivo de la devolución (mínimo 5 caracteres). Botones de confirmar y cancelar. |
| `devolver-form.blade.php` | **Formulario inline de devolución** (~4KB). Versión simplificada del modal de devolución para uso inline. |

### 2.7 Vistas de PDF

Ubicación: `resources/views/pdf/`

Cada tipo de documento tiene su propia plantilla PDF con formato oficial institucional. Ver [Sección 15](#15-generación-de-pdf).

### 2.8 Rutas

Ubicación: `routes/web.php`

```
GET /correspondencia              → CorrespondenciaMain (enrutador)
GET /correspondencia-presidente   → CorrespondenciaPresidente
GET /correspondencia-director     → CorrespondenciaDirector
GET /correspondencia-gerente      → CorrespondenciaGerente
GET /correspondencia-analista     → CorrespondenciaAnalista
GET /correspondencia-usuario      → CorrespondenciaUsuario
GET /correspondencia-admin        → CorrespondenciaAdmin
```

Todas protegidas por middleware de autenticación y permisos de Spatie.

---

## 3. Base de Datos: Tablas y Relaciones

### 3.1 Tabla `estatus_comunicaciones`

Catálogo de los 8 posibles estatus de un comunicado.

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | BIGINT (PK) | Identificador del estatus |
| `nombre` | VARCHAR(255) UNIQUE | Nombre del estatus (ej: "Pendiente") |
| `color_badge` | VARCHAR(255) | Clases CSS para el badge de color (ej: "bg-red-100 text-red-800 border-red-200") |

Migración: `2026_03_11_000000_create_estatus_comunicaciones_table.php`
Seeder: `database/seeders/EstatusComunicacionSeeder.php`

### 3.2 Tabla `comunicados`

Tabla principal que almacena cada documento/comunicado creado.

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `comunicado_id` | BIGINT (PK, Auto) | Identificador único del comunicado |
| `codigo` | VARCHAR(255) UNIQUE | Código de control generado automáticamente (ej: "MEM-0001-2026", "CIR-0003-2026") |
| `tipo` | VARCHAR(255) | Tipo de documento (ej: "MEMORANDO", "Circular", "Agenda al Decisor") |
| `asunto` | VARCHAR(255) | Asunto o título del comunicado |
| `prioridad` | VARCHAR(255) | Nivel de prioridad: "Urgente", "Alta" o "Normal" |
| `remitente_id` | BIGINT (FK → users.id) | ID del usuario que creó el comunicado (autor original) |
| `respuesta_comunicado_id` | BIGINT (FK → comunicados.comunicado_id, NULL) | Si es una respuesta, apunta al comunicado padre |
| `datos_json` | JSON (NULL) | Campo flexible que almacena todos los datos específicos del tipo de documento (campos del formulario, información de firmas, devoluciones, etc.) |
| `fecha_limite` | TIMESTAMP (NULL) | Fecha límite de respuesta (opcional) |
| `seguimiento` | BOOLEAN (default: false) | Si el documento está marcado para seguimiento |
| `correcciones` | SMALLINT (default: 0) | Contador de cuántas veces ha sido editado/corregido después de una devolución |

Migraciones: `2026_03_12_000000_create_comunicados_table.php` y `2026_04_01_000000_add_correcciones_seguimiento_to_comunicados.php`

**Relaciones del modelo `Comunicado`:**
- `remitente()` → BelongsTo User (el autor/creador)
- `respuesta_a()` → BelongsTo Comunicado (el documento padre si es respuesta)
- `respuestas()` → HasMany Comunicado (los documentos que responden a este)
- `destinatarios_pivot()` → HasMany ComunicadoDestinatario (todos los pasos/movimientos)
- `adjuntos()` → HasMany ComunicadoAdjunto (archivos adjuntos)

### 3.3 Tabla `comunicado_destinatario`

**Tabla pivote fundamental.** Cada registro representa **un movimiento** del documento en la cadena jerárquica. Un comunicado puede tener múltiples registros aquí (uno por cada persona que lo recibe, lo remite, lo devuelve, etc.).

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | BIGINT (PK, Auto) | Identificador único del movimiento |
| `comunicado_id` | BIGINT (FK → comunicados.comunicado_id) | El comunicado al que pertenece |
| `usuario_id` | BIGINT (FK → users.id) | El usuario que **recibe** el documento en este paso |
| `emisor_id` | BIGINT (FK → users.id, NULL) | El usuario que **envió/remitió** el documento a este destinatario. Es NULL en el primer envío (cuando el creador envía directamente). |
| `estatus_id` | BIGINT (FK → estatus_comunicaciones.id, default: 1) | El estatus actual de este paso para este usuario |
| `motivo_id` | BIGINT (FK → estatus_comunicaciones.id, NULL) | El motivo por el cual se creó este registro (ej: 5=Remitido, 7=Devuelto) |
| `observacion` | TEXT (NULL) | Texto de observación (usado principalmente en devoluciones para indicar qué corregir) |

Migraciones: `2026_03_12_100000_create_comunicado_destinatario_table.php` y `2026_03_31_000000_add_emisor_id_to_comunicado_destinatario_table.php`

**Ejemplo de cómo se pobla esta tabla:**

Escenario: Analista crea un comunicado y lo envía al Gerente. Gerente lo remite al Director. Director lo devuelve al Gerente.

| id | comunicado_id | usuario_id | emisor_id | estatus_id | motivo_id | observacion |
|----|--------------|------------|-----------|------------|-----------|-------------|
| 1 | 10 | 5 (Gerente) | NULL | 5 (Remitido) | NULL | NULL |
| 2 | 10 | 8 (Director) | 5 (Gerente) | 7 (Devuelto) | 5 (Remitido) | NULL |
| 3 | 10 | 5 (Gerente) | 8 (Director) | 1 (Pendiente) | 7 (Devuelto) | "Corregir la fecha del documento" |

### 3.4 Tabla `comunicado_adjuntos`

Almacena los archivos adjuntos de cada comunicado.

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | BIGINT (PK, Auto) | Identificador del adjunto |
| `comunicado_id` | BIGINT (FK → comunicados.comunicado_id) | Comunicado al que pertenece |
| `nombre_original` | VARCHAR(255) | Nombre original del archivo subido |
| `ruta_archivo` | VARCHAR(255) | Ruta del archivo en el disco (storage) |
| `tipo_mime` | VARCHAR(255, NULL) | Tipo MIME del archivo (ej: "application/pdf") |
| `tamano` | BIGINT (default: 0) | Tamaño del archivo en bytes |

Migración: `2026_03_26_000000_create_comunicado_adjuntos_table.php`

Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX. Tamaño máximo: 5MB por archivo.

---

## 4. Roles, Permisos y Jerarquía

### 4.1 Jerarquía de Roles

El sistema tiene 5 roles jerárquicos de correspondencia y 1 rol administrativo:

```
Nivel 5 (Máximo):  Presidente Correspondencia
Nivel 4:           Director Correspondencia
Nivel 3:           Gerente Correspondencia
Nivel 2:           Analista Correspondencia
Nivel 1 (Mínimo):  Usuario Correspondencia
Administrativo:    Administrador (configuración)
```

### 4.2 Permisos Asignados

Definidos en `database/seeders/RoleSeeder.php`:

| Permiso | Roles que lo tienen |
|---------|---------------------|
| `Acceso Modulo Correspondencia` | Presidente, Director, Gerente, Analista, Usuario, Admin |
| `Ver Correspondencia-Presidente` | Presidente |
| `Ver Correspondencia-Director` | Director |
| `Ver Correspondencia-Gerente` | Gerente |
| `Ver Correspondencia-Analista` | Analista |
| `Ver Correspondencia-Usuario` | Usuario |
| `Ver Correspondencia-Admin` | Admin |
| `Seguimiento de Instrucciones Asignadas` | Presidente, Admin |

### 4.3 Capacidades por Rol

| Acción | Usuario | Analista | Gerente | Director | Presidente |
|--------|---------|----------|---------|----------|------------|
| Ver documentos recibidos | Si | Si | Si | Si | Si |
| Confirmar recepción | Si | Si | Si | Si | Si |
| Crear documentos | No | Si | Si | Si | Si |
| Responder documentos | No | Si | Si | Si | Si |
| Editar documento devuelto | No | Si (si es autor) | No | No | No |
| Remitir a otro usuario | No | No | Si | Si | Si |
| Devolver para corrección | No | No | Si | Si | Si |
| Marcar seguimiento | No | No | Si | Si | Si |
| Aprobar y Firmar | No | No | No | No | Si |
| Delegar Instrucción | No | No | No | No | Si |
| Negar y Archivar | No | No | No | No | Si |
| Administrar usuarios | No | No | No | No | No (Admin) |

### 4.4 ¿A quién puede remitir cada rol?

| Rol | Puede remitir a |
|-----|-----------------|
| Gerente | Director, otro Gerente |
| Director | Presidente, otro Director, Gerente |
| Presidente | Cualquier rol |

---

## 5. Panel del Administrador

**Archivo backend:** `app/Livewire/GestionCorrespondencia/CorrespondenciaAdmin.php`
**Archivo vista:** `resources/views/livewire/gestion-correspondencia/correspondencia-admin.blade.php`

### 5.1 Dashboard (Vista principal)

Al ingresar, el administrador ve 3 tarjetas principales:

| Tarjeta | Qué muestra | De dónde obtiene la información |
|---------|-------------|----------------------------------|
| **Total Habilitados** | Número total de usuarios que tienen algún rol de correspondencia asignado | Cuenta usuarios que tengan cualquiera de los 5 roles de correspondencia |
| **Candidatos** | Número de usuarios del sistema que NO tienen ningún rol de correspondencia (potenciales nuevos usuarios) | Cuenta usuarios excluyendo los que ya tienen rol |
| **Roles Registrados** | Muestra "5" (los 5 roles jerárquicos disponibles) | Valor fijo |

Debajo de las tarjetas principales hay dos secciones:

**Últimas Altas:** Lista de los últimos 5 usuarios a los que se les asignó un rol de correspondencia, mostrando nombre, email y rol asignado.

**Distribución de Roles:** Barras de progreso que muestran cuántos usuarios hay en cada rol:
- Presidentes: X usuarios
- Directores: X usuarios
- Gerentes: X usuarios
- Analistas: X usuarios
- Usuarios Registrados: X usuarios

### 5.2 Listado de Usuarios

Tabla paginada (10 por página) con todos los usuarios que tienen rol de correspondencia:

- **Filtros disponibles:** Búsqueda por nombre/email, filtro por rol específico.
- **Columnas:** Nombre, Email, Rol de Correspondencia, Acciones (Editar, Eliminar).
- **Botón Editar:** Abre formulario de edición donde se puede cambiar el rol del usuario.
- **Botón Eliminar:** Muestra modal de confirmación. Al confirmar, le quita TODOS los roles de correspondencia al usuario (llama a `delete_user()` que remueve los 5 roles y limpia la caché de Spatie).

### 5.3 Formulario de Asignación (Nuevo/Editar)

- **Campo "Usuario":** Campo de búsqueda que acepta nombre o email. Al escribir, el sistema busca usuarios que coincidan y los muestra como sugerencias. Al seleccionar uno, se autocompleta el campo.
- **Campo "Rol":** Dropdown con los 5 roles disponibles: Presidente, Director, Gerente, Analista, Usuario.
- **Botón "Guardar":** Ejecuta `save_user()` que:
  1. Valida que el usuario y rol existan.
  2. Remueve cualquier rol de correspondencia previo del usuario.
  3. Asigna el nuevo rol usando Spatie.
  4. Limpia la caché de permisos de Spatie (`PermissionRegistrar::forgetCachedPermissions()`).
  5. Redirige al listado con mensaje de éxito.

---

## 6. Panel del Usuario General

**Archivo backend:** `app/Livewire/GestionCorrespondencia/CorrespondenciaUsuario.php`
**Archivo vista:** `resources/views/livewire/gestion-correspondencia/correspondencia-usuario.blade.php`

### 6.1 Dashboard

Vista de bienvenida simple. No muestra tarjetas estadísticas. Solo un mensaje: "Bienvenido al Módulo de Correspondencia" con instrucciones básicas.

**Filtros de fecha:** Rango de fechas (inicio/fin) que filtra los documentos mostrados en la bandeja. Por defecto muestra el mes actual.

### 6.2 Bandeja de Entrada

Lista paginada de documentos recibidos. Cada documento muestra:
- Ícono del tipo de documento
- Asunto
- Remitente (nombre y rol)
- Fecha de envío
- Badge de prioridad (Urgente/Alta/Normal con colores)
- Badge de estatus (con colores según estado actual)

**Filtros disponibles:** Prioridad, Estatus, Rango de fechas.

Al hacer clic en un documento se abre la vista de detalle.

### 6.3 Vista de Detalle

Muestra toda la información del documento:
- **Encabezado:** Tipo de documento, asunto, código de referencia, prioridad.
- **Grilla de metadatos:** De (Remitente con nombre y rol), Para (Destinatario), Fecha de envío, Estatus actual, Fecha límite de respuesta.
- **Visor PDF:** Previsualización embebida del documento en formato PDF.
- **Trazabilidad:** Recorrido completo del documento mostrando cada paso (quién lo envió a quién, cuándo, y en qué estatus).

**Botones disponibles:**
- **Confirmar Recepción:** Único botón de acción. Cambia el estatus del documento de Pendiente/Leído a "Confirmación de Recibido". Es obligatorio confirmarlo.
- **Ver PDF / Descargar PDF:** Para ver o descargar el documento en formato PDF oficial.

### 6.4 Consulta de Bandeja de Entrada (Lógica técnica)

El query del inbox (método `load_real_communications()`) busca comunicados donde:
1. El usuario es destinatario en `comunicado_destinatario` (`usuario_id = $userId`).
2. Y cumple una de estas condiciones:
   - El remitente del comunicado NO es el propio usuario (`remitente_id != $userId`), O
   - Existe un pivot del usuario con `motivo_id = 7` (le devolvieron un documento para corregir).
3. Dentro del rango de fechas seleccionado.

---

## 7. Panel del Analista

**Archivo backend:** `app/Livewire/GestionCorrespondencia/CorrespondenciaAnalista.php`
**Archivo vista:** `resources/views/livewire/gestion-correspondencia/correspondencia-analista.blade.php`

### 7.1 Dashboard

El dashboard del Analista muestra las siguientes tarjetas:

| Tarjeta | Qué muestra | Consulta |
|---------|-------------|----------|
| **Semáforo de Urgencia** | Documentos pendientes agrupados por tiempo transcurrido | Filtra documentos recibidos con `estatus_id` en [1,2,3] (Pendiente, Leído, Confirmado) |
| **Menos de 24 horas** | Cantidad de documentos recibidos hace menos de 24h que aún no han sido remitidos | `created_at > now() - 24h` y `estatus_id` en [1,2,3] |
| **24 a 48 horas** | Documentos pendientes entre 24 y 48 horas | `created_at` entre 24h y 48h atrás |
| **Más de 48 horas** | Documentos pendientes hace más de 48h (requieren atención urgente) | `created_at < now() - 48h` |
| **Total Documentos Creados** | Cantidad total de documentos creados por el analista en el rango de fechas | Cuenta comunicados donde `remitente_id = $userId` |
| **Distribución por Tipo** | Gráfico/tabla que muestra cuántos documentos de cada tipo ha creado | Agrupa por campo `tipo` |
| **Rastreo de Indicaciones** | Lista de documentos tipo "OTRO" (indicaciones/texto libre) que ha enviado | Filtra por `tipo = 'OTRO'` |
| **Actividad Reciente** | Lista de los últimos documentos enviados por el analista | Comunicados donde `remitente_id = $userId` ordenados por fecha |

### 7.2 Bandeja de Entrada

Igual que el Usuario General pero con filtros adicionales de prioridad y estatus.

### 7.3 Creación de Documentos

**Archivo del formulario:** `resources/views/livewire/gestion-correspondencia/partials/create.blade.php`

Al hacer clic en "Generar Comunicación" (botón en el header), se abre el formulario de creación:

**Paso 1 - Seleccionar tipo de documento:** Dropdown con los 12 tipos disponibles (ver [Sección 11](#11-tipos-de-documentos)). Al seleccionar un tipo, el formulario muestra dinámicamente los campos específicos para ese tipo.

**Paso 2 - Campos comunes:**
- **Remitente:** Se autocompleta con "Yo (Analista)" - no editable.
- **Destinatario:** Campo de búsqueda que permite buscar por nombre o email. Solo muestra usuarios con roles de correspondencia.
- **Asunto:** Campo de texto obligatorio (mínimo 10 caracteres, máximo 255).
- **Prioridad:** Dropdown con opciones Urgente, Alta, Normal.

**Paso 3 - Campos específicos del tipo:** Cada tipo de documento tiene sus propios campos (ver [Sección 11](#11-tipos-de-documentos)).

**Paso 4 - Campos opcionales:**
- **Fecha límite:** Fecha opcional para indicar cuándo se espera respuesta.
- **Adjuntos:** Carga de archivos (PDF, DOC, DOCX, XLS, XLSX, máximo 5MB cada uno).

**Paso 5 - Enviar:** Al presionar "Enviar Comunicación":
1. Se validan todos los campos según las reglas del tipo seleccionado (método `rules()`).
2. Se genera automáticamente un código de control único (ej: "MEM-0001-2026").
3. Se crea el registro en la tabla `comunicados` con `remitente_id = auth()->id()`.
4. Se crea el registro en `comunicado_destinatario` con `usuario_id = destinatario`, `estatus_id = 1 (Pendiente)`.
5. Se guardan los adjuntos si los hay.
6. Se envía una notificación toast de éxito.

### 7.4 Responder Documentos

Al abrir un documento recibido y confirmar su recepción, aparece el botón "Responder con el mismo tipo de comunicado" y "Redactar / Enviar indicaciones":

- **Responder con mismo tipo:** Abre el formulario de creación con el tipo prellenado, el asunto con prefijo "RE:", y el destinatario apuntando al remitente del documento original. El nuevo comunicado se vincula al original mediante `respuesta_comunicado_id`.
- **Redactar / Enviar indicaciones:** Abre un formulario de texto libre (tipo "OTRO") para enviar indicaciones sin formato específico.

### 7.5 Editar Documento Devuelto

Cuando un documento creado por el analista es devuelto para corrección:

1. El documento aparece en la bandeja de entrada del analista (gracias a `motivo_id = 7` en su pivot).
2. Al abrirlo, primero debe **Confirmar Recepción**.
3. Después de confirmar, aparece el bloque amarillo "Documento devuelto para corrección" con:
   - El **motivo de la devolución** (la observación que escribió quien lo devolvió).
   - El botón **"Editar y corregir documento"** (solo si el analista es el autor original).
4. Al hacer clic en "Editar y corregir documento":
   - Se abre el formulario de creación en **modo edición** (`editando_comunicado_id` se establece).
   - Los campos se precargan con los datos actuales del documento.
   - El analista corrige lo necesario y presiona "Enviar".
5. Al guardar:
   - Se actualizan los datos del comunicado existente (no se crea uno nuevo).
   - Se incrementa el contador `correcciones` del comunicado.
   - Se limpia el pivot de devolución (`motivo_id = null`, `estatus_id = 4`) para que el documento desaparezca de la bandeja de entrada.
   - Se reenvía al destinatario seleccionado (crea nuevo pivot con `estatus_id = 1`).

### 7.6 Notificaciones

El contador de notificaciones (ícono de campana en el header) muestra la cantidad de documentos que requieren atención del analista. La consulta cuenta pivots donde:
- `usuario_id = $userId`
- `estatus_id` está en [1, 5, 7] (Pendiente, Remitido, Devuelto)
- No existe un registro posterior donde el usuario haya actuado como emisor (es decir, aún no ha respondido/remitido ese documento).

---

## 8. Panel del Gerente

**Archivo backend:** `app/Livewire/GestionCorrespondencia/CorrespondenciaGerente.php`
**Archivo vista:** `resources/views/livewire/gestion-correspondencia/correspondencia-gerente.blade.php`

### 8.1 Dashboard

Incluye todas las tarjetas del Analista más:

| Tarjeta adicional | Qué muestra |
|--------------------|-------------|
| **Seguimiento de Correcciones** | Documentos que el gerente ha marcado con seguimiento, mostrando el estatus actual y cuántas correcciones han tenido |

### 8.2 Acciones Exclusivas del Gerente

Además de crear, responder y confirmar (como el Analista), el Gerente tiene:

**Botón "Remitir":**
- Aparece después de confirmar recepción de un documento.
- Abre el modal de remisión (`remitir-modal.blade.php`).
- Permite seleccionar un destinatario filtrado por rol (Director u otro Gerente).
- Al remitir, se ejecuta `CorrespondenceFlowService::remitir()`:
  - El estatus del pivot del Gerente cambia a 5 (Remitido).
  - Se crea un nuevo pivot para el destinatario con `estatus_id = 1`, `emisor_id = Gerente`, `motivo_id = 5`.

**Botón "Devolver":**
- Aparece después de confirmar recepción.
- Abre el modal de devolución (`devolver-modal.blade.php`).
- El gerente debe escribir obligatoriamente el motivo de la devolución (mínimo 5 caracteres).
- Al devolver, la lógica busca en la tabla `comunicado_destinatario` el pivot original donde el Gerente recibió el documento (excluyendo devoluciones previas con `motivo_id = 7`), y devuelve al `emisor_id` de ese pivot o al `remitente_id` del comunicado (el creador original).
- El estatus del pivot del Gerente se actualiza a 7 (Devuelto).
- Se crea un nuevo pivot para el destinatario con `estatus_id = 1`, `motivo_id = 7` y la observación.

**Botón "Seguimiento" (ícono de estrella/bandera):**
- Alterna el campo `seguimiento` del comunicado (true/false).
- Los documentos marcados aparecen en la tarjeta "Seguimiento de Correcciones" del dashboard.

**Cuando le devuelven un documento al Gerente (un Director le devolvió algo):**
- El Gerente ve el documento en su bandeja de entrada.
- Debe **Confirmar Recepción** primero.
- Después de confirmar, aparece el bloque "Documento devuelto para corrección" con:
  - El motivo de la devolución.
  - **"Devolver al creador":** Devuelve el documento al analista que lo creó originalmente.
  - **"Responder al remitente":** Abre el formulario de respuesta dirigido al Director que se lo devolvió (para aclarar dudas).
- Una vez que el Gerente devuelve el documento, su estatus cambia a 7 y **ya no ve ningún botón de acción** (no puede volver a confirmar, responder ni remitir ese documento porque ya no está bajo su poder).

---

## 9. Panel del Director

**Archivo backend:** `app/Livewire/GestionCorrespondencia/CorrespondenciaDirector.php`
**Archivo vista:** `resources/views/livewire/gestion-correspondencia/correspondencia-director.blade.php`

### 9.1 Dashboard

Igual que el Gerente con las mismas tarjetas de seguimiento y semáforo.

### 9.2 Acciones del Director

Tiene todas las acciones del Gerente con estas diferencias:

**Remitir:** Puede remitir a Presidente, otro Director o Gerente (rango más amplio que el Gerente).

**Devolver:** Al devolver un documento, el Director tiene la opción de elegir el destino:
- **Al emisor:** Devuelve a quien le remitió el documento (ej: si un Gerente se lo remitió, lo devuelve al Gerente).
- **Al creador:** Devuelve directamente al autor original del comunicado (ej: el Analista que lo creó), saltándose intermediarios.

La lógica de devolución (en `devolver_para_corregir()`) busca en `comunicado_destinatario` el pivot original donde el Director recibió el documento (excluyendo pivots con `motivo_id = 7`) y usa el `emisor_id` de ese registro para determinar a quién devolver. Si el `emisor_id` es null (el creador lo envió directamente), devuelve al `remitente_id` del comunicado.

**Comportamiento después de devolver:** Igual que el Gerente, una vez que devuelve un documento, su pivot se actualiza a `estatus_id = 7` y ya no ve ningún botón de acción sobre ese documento.

---

## 10. Panel del Presidente

**Archivo backend:** `app/Livewire/GestionCorrespondencia/CorrespondenciaPresidente.php`
**Archivo vista:** `resources/views/livewire/gestion-correspondencia/correspondencia-presidente.blade.php`

### 10.1 Dashboard

Incluye tarjetas adicionales exclusivas:

| Tarjeta | Qué muestra |
|---------|-------------|
| **Documentos Pendientes de Firma** | Comunicados que han llegado al Presidente y están esperando su aprobación/firma |
| **Distribución por Tipo** | Cuántos documentos de cada tipo ha procesado |
| **Seguimiento de Instrucciones** | Documentos delegados o marcados para seguimiento, con nivel jerárquico (Presidencia, Director, Gerente, Otro) |

### 10.2 Acciones Exclusivas del Presidente

Además de todas las acciones de roles inferiores, el Presidente tiene 3 acciones exclusivas:

**Botón "Aprobar y Firmar":**
- Aparece después de confirmar recepción.
- Al hacer clic, ejecuta `CorrespondenceFlowService::aprobarYFirmar()`:
  - El pivot del Presidente se actualiza a `estatus_id = 6` (Aprobado / Firmado).
  - Se inyecta en `datos_json`: `firmado = true`, `firmado_por` (nombre), `firmado_por_id` (ID), `fecha_firma`.
  - El documento queda **bloqueado definitivamente** — nadie más puede modificarlo.

**Botón "Delegar Instrucción":**
- Permite enviar el documento a cualquier usuario del sistema con instrucciones.
- Ejecuta `CorrespondenceFlowService::delegar()`:
  - Se inyecta en `datos_json`: `delegado_por_id`, `delegado_por_nombre`, `fecha_delegacion`.
  - Se crea un nuevo pivot para el destinatario con `estatus_id = 1`, `motivo_id = 5`.

**Botón "Negar y Archivar":**
- Cierra el documento permanentemente como rechazado.
- Ejecuta `CorrespondenceFlowService::archivar()`:
  - El pivot del Presidente se actualiza a `estatus_id = 8` (Rechazado / Archivado).
  - Se inyecta en `datos_json`: `archivado = true`, `archivado_por` (nombre), `archivado_por_id`, `fecha_archivo`.
  - El documento queda **cerrado permanentemente** — no se puede realizar ninguna otra acción.

---

## 11. Tipos de Documentos

El sistema soporta 12 tipos de documentos, cada uno con su propio formulario de campos específicos y plantilla PDF:

### 11.1 Agenda al Decisor
**Código:** AGD | **Uso:** Documento para presentar temas a la máxima autoridad.
**Campos específicos:** Número de agenda, código de control, presentante, secuencia (Relación/etc.), texto de asunto, resumen del cuerpo, propuesta, indicación de anexos, nombre del decisor.

### 11.2 MEMORANDO
**Código:** MEM | **Uso:** Comunicación interna formal entre dependencias.
**Campos específicos:** Para (nombre y cargo), De (nombre y cargo), acción (dropdown: se comunica/se informa/etc.), detalle del cuerpo, visado, asunto PDF, providencia (número y fecha).

### 11.3 Circular
**Código:** CIR | **Uso:** Comunicación masiva o normativa que se notifica a todas las dependencias.
**Campos específicos:** Título (se convierte a mayúsculas automáticamente), acción (comunica/informa), contenido, visado, cargo del emisor.

### 11.4 Minuta Horizontal
**Código:** MIN | **Uso:** Acta de reunión con registro de participantes, acuerdos y tareas.
**Campos específicos:** Fecha de la reunión, facilitador, dependencia, presentado a, elaborado por, revisado por, indicación de anexos, puntos de agenda (array dinámico), participantes (tabla con nombre/ubicación/correo/teléfono), planteamientos (array), acuerdos (array), tareas (array con tarea/fecha/responsable).

### 11.5 Oficio - Tipo Carta
**Código:** OFC | **Uso:** Comunicación externa formal en formato carta.
**Campos específicos:** Número de oficio, para (nombre, cargo, atención), acción, contenido, visado.

### 11.6 Oficio - Tipo Oficio
**Código:** OFO | **Uso:** Comunicación externa formal en formato oficio (papel legal).
**Campos específicos:** Mismos campos que Oficio - Tipo Carta pero genera PDF en tamaño oficio.

### 11.7 Punto de Información - Presidencia IPOSTEL
**Código:** PI-IPOSTEL | **Uso:** Informe de datos o situación dirigido a la Presidencia.
**Campos específicos:** Número, presentado por, síntesis, recomendaciones.

### 11.8 Punto de Cuenta - Presidencia IPOSTEL
**Código:** PC-IPOSTEL | **Uso:** Solicitud de decisión o aprobación dirigida a la Presidencia.
**Campos específicos:** Número, presentado por, síntesis, propuesta.

### 11.9 Punto de Cuenta - Directorio
**Código:** PC-DIR | **Uso:** Documento de decisión para el Directorio de IPOSTEL.
**Campos específicos:** Número, presentado por, síntesis, propuesta.

### 11.10 Punto de Información - MPPT
**Código:** PI-MPPT | **Uso:** Informe dirigido al Ministerio (MPPT).
**Campos específicos:** Asunto, argumentación, recomendación.

### 11.11 Punto de Cuenta - MPPT
**Código:** PC-MPPT | **Uso:** Solicitud de decisión dirigida al Ministerio (MPPT).
**Campos específicos:** Asunto, argumentación, propuesta.

### 11.12 Indicaciones (OTRO)
**Código:** N/A | **Uso:** Texto libre sin formato oficial. Para enviar indicaciones, instrucciones o comentarios informales.
**Campos específicos:** Solo el campo de asunto y cuerpo de texto libre. No genera PDF con formato oficial.

---

## 12. Estatus de los Comunicados

Definidos en `database/seeders/EstatusComunicacionSeeder.php`:

| ID | Nombre | Color | Significado |
|----|--------|-------|-------------|
| 1 | **Pendiente** | Rojo | El documento fue enviado pero el destinatario aún no lo ha abierto ni interactuado con él. |
| 2 | **Leído** | Azul | El destinatario abrió el documento (se actualizó automáticamente al ver el detalle) pero no ha tomado ninguna acción. |
| 3 | **Confirmación de Recibido** | Verde | El destinatario presionó "Confirmar Recepción", acusando formalmente que recibió y leyó el documento. |
| 4 | **Respondido** | Naranja | El destinatario creó un documento de respuesta vinculado al original. |
| 5 | **Remitido** | Morado | El documento fue reenviado/remitido a otra persona. Indica que quien lo tenía lo pasó a otro nivel jerárquico. |
| 6 | **Aprobado / Firmado** | Verde azulado | El Presidente aprobó y firmó el documento. Es un estatus final — el documento queda bloqueado. |
| 7 | **Devuelto para corregir** | Amarillo | El documento fue devuelto al paso anterior porque contiene errores. Incluye una observación obligatoria indicando qué corregir. |
| 8 | **Rechazado / Archivado** | Gris | El Presidente negó y archivó el documento. Es un estatus final — el documento queda cerrado permanentemente. |

### Flujo de estatus en el pivot (`comunicado_destinatario.estatus_id`)

```
1 (Pendiente) → 2 (Leído) → 3 (Confirmado) → 4 (Respondido)
                                              → 5 (Remitido)
                                              → 7 (Devuelto)
                                              → 6 (Aprobado) [solo Presidente]
                                              → 8 (Archivado) [solo Presidente]
```

### Diferencia entre `estatus_id` y `motivo_id`

- **`estatus_id`:** El estado ACTUAL del documento para este usuario en este paso.
- **`motivo_id`:** El motivo por el cual se creó este registro pivot (por qué le llegó este documento). Por ejemplo, si `motivo_id = 7`, significa que el documento le llegó porque se lo devolvieron para corregir. Si `motivo_id = 5`, le llegó porque alguien se lo remitió.

---

## 13. Flujo Completo de un Documento

### 13.1 Flujo Normal (sin devoluciones)

```
1. Analista CREA el comunicado
   → Se genera código único (ej: MEM-0001-2026)
   → Pivot: usuario_id=Gerente, estatus_id=1, emisor_id=NULL

2. Gerente RECIBE en bandeja de entrada (estatus: Pendiente)
   → Abre el detalle → estatus cambia a 2 (Leído)
   → Presiona "Confirmar Recepción" → estatus cambia a 3 (Confirmado)

3. Gerente REMITE al Director
   → Pivot del Gerente: estatus_id → 5 (Remitido)
   → Nuevo pivot: usuario_id=Director, estatus_id=1, emisor_id=Gerente, motivo_id=5

4. Director RECIBE → Confirma → REMITE al Presidente
   → Mismo patrón: su pivot → 5, nuevo pivot para Presidente

5. Presidente RECIBE → Confirma → APRUEBA Y FIRMA
   → Su pivot → 6 (Aprobado/Firmado)
   → datos_json se actualiza con: firmado=true, firmado_por, fecha_firma
   → FIN del ciclo
```

### 13.2 Flujo con Devolución

```
1. Analista CREA y envía al Gerente
2. Gerente confirma y REMITE al Director
3. Director confirma y encuentra errores → DEVUELVE al Gerente
   → Pivot del Director: estatus_id → 7
   → Nuevo pivot: usuario_id=Gerente, estatus_id=1, motivo_id=7, observacion="Corregir fecha"

4. Gerente RECIBE la devolución en bandeja
   → Confirma recepción
   → Ve el bloque "Documento devuelto para corrección" con el motivo
   → Decide DEVOLVER AL CREADOR
   → Su pivot → 7
   → Nuevo pivot: usuario_id=Analista, estatus_id=1, motivo_id=7

5. Analista RECIBE la devolución
   → Confirma recepción
   → Ve el bloque "Documento devuelto para corrección"
   → Presiona "Editar y corregir documento"
   → Corrige los campos necesarios → Envía
   → Su pivot de devolución se limpia (motivo_id=null, estatus_id=4)
   → El comunicado desaparece de su bandeja de entrada
   → Nuevo pivot para el Gerente con estatus_id=1

6. Gerente recibe el documento corregido → continúa el flujo normal
```

### 13.3 Flujo con Respuesta

```
1. Director envía un Memorando al Analista
2. Analista confirma recepción
3. Analista presiona "Responder con el mismo tipo de comunicado"
   → Se abre el formulario con:
     - Tipo: MEMORANDO (prellenado)
     - Asunto: "RE: [asunto original]"
     - Destinatario: Director (prellenado)
     - respuesta_comunicado_id: apunta al memorando original
4. Analista llena el contenido de la respuesta y envía
   → Se crea un NUEVO comunicado vinculado al original
   → El pivot del Analista en el documento original → estatus 4 (Respondido)
```

---

## 14. Sistema de Notificaciones

### Cómo se calcula el contador de notificaciones

Cada componente tiene un método `refresh_notifications()` que ejecuta la siguiente consulta:

```
Contar registros en comunicado_destinatario donde:
  - usuario_id = usuario actual
  - estatus_id está en [1, 5, 7] (Pendiente, Remitido, o Devuelto)
  - NO existe otro registro posterior donde el usuario ya haya actuado
    como emisor (es decir, aún no ha respondido/remitido ese documento)
```

Esto garantiza que solo se cuentan documentos que **realmente requieren acción** del usuario. Si el usuario ya remitió o respondió un documento, ese documento ya no cuenta como notificación pendiente.

El contador se actualiza automáticamente después de cada acción (confirmar, responder, remitir, devolver, etc.).

---

## 15. Generación de PDF

Cada tipo de documento tiene su propia plantilla PDF ubicada en `resources/views/pdf/`:

| Tipo de Documento | Vista PDF | Tamaño de Papel |
|-------------------|-----------|-----------------|
| Agenda al Decisor | `pdf.agenda-decisor` | Carta |
| MEMORANDO | `pdf.memorando` | Carta |
| Circular | `pdf.circular` | Carta |
| Oficio - Tipo Carta | `pdf.oficio-carta` | Carta |
| Oficio - Tipo Oficio | `pdf.oficio-oficio` | Oficio (Legal) |
| Punto de Información - Presidencia | `pdf.punto-informacion` | Carta |
| Punto de Cuenta - Presidencia | `pdf.punto-de-cuenta` | Carta |
| Punto de Cuenta - MPPT | `pdf.punto-de-cuenta-mppt` | Carta |
| Punto de Información - MPPT | `pdf.punto-de-informacion-mppt` | Carta |
| Punto de Cuenta - Directorio | `pdf.punto-de-cuenta-directorio` | Carta |
| Minuta Horizontal | `pdf.minuta` | Carta |
| Indicaciones (OTRO) | No genera PDF | N/A (solo texto) |

Los PDFs incluyen:
- Encabezado institucional con logo de IPOSTEL.
- Código de control del documento.
- Todos los campos específicos del tipo de documento.
- Fecha en formato largo (ej: "Caracas, 08 de abril de 2026").
- Información de firma si el documento fue aprobado (firmado_por, fecha_firma).
- Lista de adjuntos si los hay.

La generación se realiza mediante los métodos `view_pdf($id)` (previsualización) y `download_pdf($id)` (descarga) presentes en cada componente Livewire.

---

## 16. Servicio de Flujo de Correspondencia

**Archivo:** `app/Services/CorrespondenceFlowService.php`

Este servicio centraliza toda la lógica de movimiento de documentos. Ningún componente modifica directamente los pivots de flujo — todos llaman a este servicio.

### 16.1 `remitir($comunicadoId, $destinatarioId, $remitenteId, $estatusId)`

**Propósito:** Reenviar/remitir un documento a otro usuario en la cadena jerárquica.

**Parámetros:**
- `$comunicadoId` — ID del comunicado a remitir.
- `$destinatarioId` — ID del usuario que recibirá el documento.
- `$remitenteId` — ID del usuario que está remitiendo (usuario actual).
- `$estatusId` — Estatus a asignar: 5 (Remitido) para reenvíos normales, 1 para reenvíos de corrección.

**Lógica:**
1. Registra en `datos_json['remitido_por']` un array con: `usuario_id`, `user_name`, `timestamp`, `a_usuario_id`.
2. Si `$estatusId` es 5 o 6: actualiza el pivot más reciente del remitente a ese estatus.
3. Crea un nuevo pivot para el destinatario con `estatus_id = 1` (Pendiente), `emisor_id = $remitenteId`, `motivo_id = $estatusId`.

### 16.2 `devolver($comunicadoId, $destinatarioId, $remitenteId, $observacion)`

**Propósito:** Devolver un documento al paso anterior para corrección.

**Parámetros:**
- `$comunicadoId` — ID del comunicado.
- `$destinatarioId` — ID del usuario que recibirá la devolución.
- `$remitenteId` — ID del usuario que devuelve (usuario actual).
- `$observacion` — Texto obligatorio explicando qué debe corregirse (mínimo 5 caracteres).

**Lógica:**
1. Registra en `datos_json['devuelto_por']` un array con: `usuario_id`, `user_name`, `timestamp`, `a_usuario_id`, `observacion`.
2. Actualiza el pivot más reciente del remitente a `estatus_id = 7` (Devuelto para corregir).
3. Crea un nuevo pivot para el destinatario con `estatus_id = 1` (Pendiente), `motivo_id = 7`, y la observación.

### 16.3 `aprobarYFirmar($comunicadoId, $presidenteId)`

**Propósito:** Aprobar y firmar un documento (exclusivo del Presidente).

**Lógica:**
1. Busca el pivot más reciente del Presidente para este comunicado.
2. Actualiza su `estatus_id` a 6 (Aprobado / Firmado).
3. Inyecta en `datos_json`: `firmado = true`, `firmado_por` (nombre), `firmado_por_id`, `fecha_firma` (fecha actual).
4. El documento queda bloqueado — no se crea ningún nuevo pivot.

### 16.4 `archivar($comunicadoId, $presidenteId)`

**Propósito:** Negar y archivar un documento permanentemente (exclusivo del Presidente).

**Lógica:**
1. Busca el pivot más reciente del Presidente.
2. Actualiza su `estatus_id` a 8 (Rechazado / Archivado).
3. Inyecta en `datos_json`: `archivado = true`, `archivado_por` (nombre), `archivado_por_id`, `fecha_archivo`.
4. El documento queda cerrado — no se permite ninguna acción posterior.

### 16.5 `delegar($comunicadoId, $destinatarioId, $remitenteId)`

**Propósito:** Delegar una instrucción a cualquier usuario (exclusivo del Presidente).

**Lógica:**
1. Inyecta en `datos_json`: `delegado_por_id`, `delegado_por_nombre`, `fecha_delegacion`.
2. Crea un nuevo pivot para el destinatario con `estatus_id = 1`, `motivo_id = 5`.

### 16.6 Métodos Privados Internos

**`mergeDatosJson($comunicado, $patch)`:** Fusiona un array de datos con el `datos_json` existente del comunicado. Elimina claves con valor null para mantener el JSON limpio. Guarda automáticamente.

**`createPivot($comunicadoId, $usuarioId, $estatusId, $emisorId, $observacion, $motivoId)`:** Crea un nuevo registro en `comunicado_destinatario`. Es el método de bajo nivel que usan todos los métodos públicos para crear movimientos en la cadena.
