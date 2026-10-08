<?php

namespace App\Livewire\Oficinas;

use App\Livewire\Forms\Oficinas\Createform2;
use App\Models\Envio;
use App\Models\EnvioExpedicion;
use App\Models\EnvioSaca;
use App\Models\Estado;
use App\Models\Oficina;
use App\Models\Saca;
use App\Models\TipoSaca;
use App\Models\UsuarioSeguimiento;
use App\Services\DespachoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class DetallesSacas extends Component
{
    // En CPC los envíos sugeridos para cargar en valijas vienen de Expedición.
    const OFICINA_CON_EXPEDICION = 10;

    public $saca_id;
    public $saca;
    public $enviosSaca;
    public $envios;
    // Sugeridos: null = aún no cargados (se cargan lazy vía wire:init para no
    // penalizar el render inicial); Collection = ya calculados.
    public $enviosSugeridos = null;
    public $search = '';
    public $perPage = 5;
    public $sortBy = 'envio_id';
    public $sortDir = 'ASC';
    public Createform2 $createForm;
    public $totalPeso;
    public $codigosEnvios = '';
    public $enviosEncontrados = [];
    public $enviosNoEncontrados = [];
    public $enviosYaEnSaca = [];
    public $editandoPrecinto = false;
    public $nuevoPrecinto;
    public $pesoFinal;

    // Edición de la valija (tipo y oficina de destino) antes de cerrarla.
    public $editandoValija = false;
    public $editTipoSaca;
    public $editEstadoDest;
    public $editOficinaDest;
    public $oficinasEdit = [];


    /**
     * ¿La valija es de carga por peso? Estos tipos (ej. Valija de Peso) se
     * despachan indicando solo su peso: no admiten envíos y se cierran vacías.
     *
     * La condición vive en `tipos_sacas.cargar_por_peso`, configurable desde
     * Parámetros de Valijas. Antes era un id de tipo fijo en el código, que no
     * existía en todas las bases de datos.
     */
    private function esCargaPorPeso(): bool
    {
        return (bool) ($this->saca->tipoSaca->cargar_por_peso ?? false);
    }

    /**
     * Indica si la valija no admite agregar envíos: está cerrada o es un tipo
     * de carga por peso (Valija de Peso). Centraliza el guard de los métodos
     * que agregan envíos.
     */
    private function noAdmiteEnvios(): bool
    {
        return $this->saca->cerrado || $this->esCargaPorPeso();
    }

    /**
     * ¿Está la valija físicamente en la oficina del usuario?
     *
     * La ubicación se deriva del último encaminamiento, no de `sacas.oficina_id`
     * (que solo dice dónde nació). Una valija despachada ya no está en su
     * oficina de origen, y una recibida en un COP está allí aunque la creara otra.
     *
     * Queda fuera la valija EN TRÁNSITO (no está en ninguna oficina) y la
     * ABIERTA (llegó a una OPT, liberó sus envíos y no se vuelve a usar).
     */
    private function sacaEnMiOficina(): bool
    {
        return Saca::query()
            ->ubicadaEn(auth()->user()->oficina_id)
            ->where('sacas.saca_id', $this->saca_id)
            ->exists();
    }

    /**
     * Guard de las acciones que modifican la valija. Devuelve true (y avisa al
     * usuario) cuando la valija NO está en su oficina.
     *
     * La vista ya oculta estos controles, pero eso solo es cosmético: una
     * petición Livewire manipulada puede invocar cualquier método público del
     * componente, así que la comprobación tiene que estar en el servidor.
     */
    private function bloqueadoPorUbicacion(): bool
    {
        if ($this->sacaEnMiOficina()) {
            return false;
        }

        $this->dispatch('alertSuccess2', message: 'Esta valija no se encuentra en su oficina, no puede modificarla.');

        return true;
    }

    public function eliminarEnvio($envioId)
    {
        if ($this->bloqueadoPorUbicacion()) {
            return;
        }

        // Buscar el envío dentro de ESTA valija (no en cualquier saca).
        $envio = EnvioSaca::where('saca_id', $this->saca_id)
            ->where('envio_id', $envioId)
            ->first();

        // Si ya no está (doble clic / eliminaciones rápidas encimadas), resincronizar
        // la tabla con la BD y salir sin ruido: la fila desaparece del DOM.
        if (!$envio) {
            $this->actualizarDatosSaca();
            return;
        }

        $envio->delete();

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'delete',
            'descripcion' => "Se descartó el envío ({$envioId}) de la valija ({$this->saca_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Envio descartado correctamente');
        $this->actualizarDatosSaca();
        $this->refrescarSugeridosSiCargados();
    }

    public function guardarPrecinto()
    {
        if ($this->bloqueadoPorUbicacion()) {
            return;
        }

        $this->validate([
            'nuevoPrecinto' => 'nullable|string|max:10',
        ]);

        $this->saca->update(['numero_precinto' => $this->nuevoPrecinto]);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se actualizó el número de precinto de la valija ({$this->saca_id}) a '{$this->nuevoPrecinto}'",
        ]);

        $this->editandoPrecinto = false;
        $this->dispatch('alertSuccess', message: 'Numero de Precinto Actualizado!');
    }

    /**
     * Una valija "salió" de su oficina cuando tiene algún movimiento de
     * encaminamiento posterior a su creación (tránsito o recibida). Se basa en
     * sacas_encaminamiento, por lo que funciona incluso para valijas sin envíos.
     * Tras la salida ya no debe poder editarse, aunque se reabra.
     */
    private function sacaYaSalio(): bool
    {
        return \App\Models\SacaEncaminamiento::where('saca_id', $this->saca_id)
            ->where('saca_estatus_id', '!=', Saca::ESTATUS_CREADA)
            ->exists();
    }

    public function abrirEdicionValija()
    {
        if ($this->bloqueadoPorUbicacion()) {
            return;
        }

        if ($this->sacaYaSalio()) {
            $this->dispatch('alertSuccess2', message: 'La valija ya salió de la oficina, no se puede editar.');
            return;
        }

        if ($this->saca->cerrado) {
            $this->dispatch('alertSuccess2', message: 'La valija está cerrada, no se puede editar.');
            return;
        }

        // Precargar los valores actuales de la valija.
        $this->editTipoSaca = $this->saca->tipo_saca_id;
        $oficinaDest = Oficina::find($this->saca->oficina_destino_id);
        $this->editEstadoDest = $oficinaDest?->estado_id;
        $this->editOficinaDest = $this->saca->oficina_destino_id;
        $this->cargarOficinasEdit();

        $this->editandoValija = true;
    }

    public function cerrarEdicionValija()
    {
        $this->editandoValija = false;
        $this->resetValidation();
    }

    public function updatedEditEstadoDest()
    {
        // Al cambiar el estado, recargar oficinas y limpiar la selección previa.
        $this->editOficinaDest = null;
        $this->cargarOficinasEdit();
    }

    private function cargarOficinasEdit()
    {
        if (empty($this->editEstadoDest)) {
            $this->oficinasEdit = [];
            return;
        }

        $this->oficinasEdit = Oficina::where('estado_id', $this->editEstadoDest)
            ->where('estatus_id', 1)
            ->whereNot('externa', true)
            ->get();
    }

    public function actualizarValija()
    {
        if ($this->bloqueadoPorUbicacion()) {
            return;
        }

        if ($this->sacaYaSalio()) {
            $this->dispatch('alertSuccess2', message: 'La valija ya salió de la oficina, no se puede editar.');
            return;
        }

        if ($this->saca->cerrado) {
            $this->dispatch('alertSuccess2', message: 'La valija está cerrada, no se puede editar.');
            return;
        }

        $this->validate([
            'editTipoSaca'    => 'required',
            'editOficinaDest' => 'required',
        ], [
            'editTipoSaca.required'    => 'Debe seleccionar un tipo de valija.',
            'editOficinaDest.required' => 'Debe seleccionar una oficina de destino.',
        ]);

        // El tipo solo puede cambiarse si la valija no tiene envíos (evita dejar
        // envíos que no corresponden al nuevo tipo).
        $tieneEnvios = EnvioSaca::where('saca_id', $this->saca_id)->exists();
        if ($tieneEnvios && (int) $this->editTipoSaca !== (int) $this->saca->tipo_saca_id) {
            $this->dispatch('alertSuccess2', message: 'No se puede cambiar el tipo de una valija que ya tiene envíos.');
            return;
        }

        // Si cambia el destino, la valija debe reasignarse al despacho del NUEVO par
        // (origen -> nuevo destino), con la misma lógica de correlativo que al crear.
        // Si no se reasigna, la valija queda colgada de un despacho cuyo destino ya no
        // es el suyo. Ver docs/notas/problematica-despachos-destino-null.md
        $cambioDestino = (int) $this->editOficinaDest !== (int) $this->saca->oficina_destino_id;

        DB::transaction(function () use ($cambioDestino) {
            $datos = [
                'tipo_saca_id'       => $this->editTipoSaca,
                'oficina_destino_id' => $this->editOficinaDest,
            ];

            if ($cambioDestino) {
                $datos['numero_despacho_id'] = app(DespachoService::class)
                    ->resolverDespachoActivo(
                        (int) $this->saca->oficina_id,
                        (int) $this->editOficinaDest
                    );
            }

            $this->saca->update($datos);

            UsuarioSeguimiento::create([
                'usuario_id'  => auth()->user()->id,
                'accion'      => 'update',
                'descripcion' => "Se editó la valija ({$this->saca_id}): tipo={$this->editTipoSaca}, oficina_destino={$this->editOficinaDest}",
            ]);
        });

        $this->saca->refresh();
        $this->editandoValija = false;
        $this->dispatch('alertSuccess', message: 'Valija actualizada correctamente.');
    }

    public function buscarEnvios()
    {
        // No está en la lista original de guards, pero es la antesala de store():
        // sin este chequeo se podría sondear qué envíos existen desde una oficina
        // que ni siquiera tiene la valija.
        if ($this->bloqueadoPorUbicacion()) {
            return;
        }

        if ($this->noAdmiteEnvios()) {
            $this->dispatch('alertSuccess2', message: 'Esta valija no permite agregar envíos.');
            return;
        }

        $this->validate([
            'codigosEnvios' => 'required|string|min:1',
        ], [
            'codigosEnvios.required' => 'Debe ingresar al menos un código de envío.',
            'codigosEnvios.min' => 'Debe ingresar al menos un código de envío.',
        ]);

        $this->enviosEncontrados = [];
        $this->enviosNoEncontrados = [];
        $this->enviosYaEnSaca = [];

        if (empty(trim($this->codigosEnvios))) {
            return;
        }

        // Dividir los códigos por líneas y limpiar espacios
        $codigos = array_filter(
            array_map('trim', explode("\n", $this->codigosEnvios)),
            function ($codigo) {
                return !empty($codigo);
            }
        );

        if (empty($codigos)) {
            return;
        }

        // Obtener envíos que están en cualquier saca activa (no despachada)
        $enviosEnSacaIds = EnvioSaca::where('activo', true)
            ->pluck('envio_id')
            ->toArray();

        // Buscar todos los envíos con los códigos proporcionados.
        // Validar que el envío pertenezca a uno de los servicios permitidos por el tipo de valija.
        // En CPC, además validar que el envío esté en Expedición de esa oficina.
        $oficinaSacaId = (int) ($this->saca->oficina_id ?? 0);

        // Servicios permitidos por el tipo de saca (vía pivote tipo_saca_servicio).
        // Fallback a servicio_id legacy si la pivote está vacía (compatibilidad).
        $serviciosValija = $this->saca->tipoSaca?->servicios->pluck('servicio_id')->toArray() ?? [];
        if (empty($serviciosValija) && $this->saca->tipoSaca?->servicio_id) {
            $serviciosValija = [(int) $this->saca->tipoSaca->servicio_id];
        }

        $envioQuery = Envio::whereIn('codigo_envio', $codigos);

        if ($oficinaSacaId === self::OFICINA_CON_EXPEDICION) {
            // En CPC un envío es ensacable si está en Expedición O en Distribución
            // de Paquetes/Muestras, las mismas dos fuentes que alimentan los
            // sugeridos (ver cargarSugeridos). Cada fuente conserva su propio
            // criterio: Expedición filtra por servicio de la valija, Distribución
            // por tipo de valija del envío.
            $envioQuery->where(function ($q) use ($oficinaSacaId, $serviciosValija) {
                $q->where(function ($sub) use ($oficinaSacaId, $serviciosValija) {
                    $sub->whereHas('expedicion_actual', fn($e) => $e->where('oficina_id', $oficinaSacaId));
                    if (!empty($serviciosValija)) {
                        $sub->whereIn('servicio_id', $serviciosValija);
                    }
                })->orWhere(function ($sub) use ($oficinaSacaId) {
                    $sub->whereHas(
                        'distribucion_actual',
                        fn($d) => $d->where('oficina_id', $oficinaSacaId)
                    )->where('tipo_saca_id', $this->saca->tipo_saca_id);
                });
            });
        } elseif (!empty($serviciosValija)) {
            $envioQuery->whereIn('servicio_id', $serviciosValija);
        }


        $enviosEncontrados = $envioQuery->get();
        $codigosEncontrados = $enviosEncontrados->pluck('codigo_envio')->toArray();

        // Separar envíos válidos, no encontrados y ya en alguna saca activa
        foreach ($enviosEncontrados as $envio) {
            if (in_array($envio->envio_id, $enviosEnSacaIds)) {
                $this->enviosYaEnSaca[] = $envio;
            } else {
                $this->enviosEncontrados[] = $envio;
            }
        }

        // Códigos no encontrados
        $this->enviosNoEncontrados = array_diff($codigos, $codigosEncontrados);
    }

    public function mount($saca_id)
    {
        $this->saca_id = $saca_id;
        // tipoSaca se usa en cada guard (carga por peso), así que se trae de una vez.
        $this->saca = Saca::with('tipoSaca')->find($this->saca_id);

        if (!$this->saca) {
            session()->flash('error', 'No se encontró la saca con el ID proporcionado.');
            return;
        }

        // Acceso directo por URL: se permite ver los detalles de una valija que
        // esta oficina creó aunque ya no la tenga (para rastrearla desde la
        // pestaña "Creadas aquí"), pero no de una valija ajena.
        //
        // Que la valija esté aquí o solo haya nacido aquí determina además si se
        // puede modificar; de eso se encarga bloqueadoPorUbicacion() en cada
        // método que muta.
        $esDeMiOficina = $this->sacaEnMiOficina()
            || (int) $this->saca->oficina_id === (int) auth()->user()->oficina_id;

        if (!$esDeMiOficina) {
            abort(403, 'Esta valija no pertenece a su oficina.');
        }

        $this->actualizarDatosSaca();
    }

    public function actualizarDatosSaca()
    {
        $this->enviosSaca = EnvioSaca::where('saca_id', $this->saca_id)->get();
        $envioIds = $this->enviosSaca->pluck('envio_id')->toArray();

        $this->envios = Envio::whereIn('envio_id', $envioIds)
            ->where('codigo_envio', 'like', '%' . $this->search . '%')
            ->get();

        $this->totalPeso = $this->envios->sum('peso');
    }

    public function create()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->createForm->create();
    }

    public function resetForm()
    {
        $this->codigosEnvios = '';
        $this->enviosEncontrados = [];
        $this->enviosNoEncontrados = [];
        $this->enviosYaEnSaca = [];
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->createForm->open = false;
    }

    public function store()
    {
        if ($this->bloqueadoPorUbicacion()) {
            return;
        }

        if ($this->noAdmiteEnvios()) {
            $this->dispatch('alertSuccess2', message: 'Esta valija no permite agregar envíos.');
            return;
        }

        if (empty($this->enviosEncontrados)) {
            $this->dispatch('alertSuccess', message: 'No hay envíos válidos para agregar.');
            return;
        }

        $agregados = 0;
        $errores = 0;

        foreach ($this->enviosEncontrados as $envio) {
            try {
                // Verificar que no esté en ninguna saca activa (no despachada)
                $yaExiste = EnvioSaca::where('activo', true)
                    ->where('envio_id', $envio->envio_id)
                    ->exists();

                if (!$yaExiste) {
                    EnvioSaca::create([
                        'envio_id' => $envio->envio_id,
                        'saca_id' => $this->saca_id,
                        'activo' => true
                    ]);
                    $agregados++;
                }
            } catch (\Exception $e) {
                $errores++;
            }
        }

        // Limpiar formulario
        $this->codigosEnvios = '';
        $this->enviosEncontrados = [];
        $this->enviosNoEncontrados = [];
        $this->enviosYaEnSaca = [];
        $this->createForm->open = false;

        // Mostrar mensaje de resultado
        if ($agregados > 0) {
            $mensaje = "Se agregaron {$agregados} envíos exitosamente.";
            if ($errores > 0) {
                $mensaje .= " {$errores} envíos no pudieron ser agregados.";
            }
            $this->dispatch('alertSuccess', message: $mensaje);
        } else {
            $this->dispatch('alertSuccess', message: 'No se pudieron agregar envíos.');
        }

        $this->actualizarDatosSaca();
        $this->refrescarSugeridosSiCargados();
    }

    public function updatedSearch()
    {
        $this->envios = Envio::whereIn('envio_id', $this->enviosSaca->pluck('envio_id')->toArray())
            ->where('codigo_envio', 'like', '%' . $this->search . '%')
            ->get();
    }

    public function closeSaca()
    {
        if ($this->bloqueadoPorUbicacion()) {
            return;
        }

        // Salvo las de carga por peso (ej. Valija de Peso), no se cierra una valija vacía.
        if ($this->envios->isEmpty() && !$this->esCargaPorPeso()) {
            $this->dispatch('alertSuccess2', message: 'No se puede cerrar una valija sin envíos.');
            return;
        }

        $this->validate([
            'pesoFinal' => 'required|integer|min:0',
        ], [
            'pesoFinal.required' => 'El peso es obligatorio para cerrar la valija.',
            'pesoFinal.integer' => 'El peso debe ser un número entero.',
            'pesoFinal.min' => 'El peso no puede ser negativo.',
        ]);

        $sacaid = $this->saca->saca_id;
        if ($this->saca) {
            $this->saca->update(['cerrado' => true]);
            $this->saca->update(['peso' => $this->pesoFinal]);

            $this->dispatch('alertSuccess', message: 'Saca Cerrada exitosamente!');
            return redirect()->route('saca-termica', $sacaid);
        }
    }

    public function agregarEnvioSugerido($envioId)
    {
        if ($this->bloqueadoPorUbicacion()) {
            return;
        }

        if ($this->noAdmiteEnvios()) {
            $this->dispatch('alertSuccess2', message: 'Esta valija no permite agregar envíos.');
            return;
        }

        // Verifica que el envío no esté en ninguna saca activa (no despachada)
        $sacaExistente = EnvioSaca::where('activo', true)
            ->where('envio_id', $envioId)
            ->first();

        if ($sacaExistente) {
            $sacaCodigo = Saca::where('saca_id', $sacaExistente->saca_id)->value('codigo_saca');
            $this->dispatch('alertError', message: "El envío ya está en la valija {$sacaCodigo}.");
            return;
        }

        // Agrega el envío a la saca
        EnvioSaca::create([
            'saca_id' => $this->saca_id,
            'envio_id' => $envioId,
            'activo' => true,
        ]);

        $this->dispatch('alertSuccess', message: 'Envío añadido a la valija.');
        $this->actualizarDatosSaca();
        $this->refrescarSugeridosSiCargados();
    }

    /**
     * Calcula los envíos sugeridos para agregar a esta valija y los guarda en
     * la propiedad $enviosSugeridos. Se invoca lazy desde el Blade (wire:init)
     * para no penalizar el render inicial: estas consultas costaban ~50ms y
     * solo importan cuando el operador va a cargar envíos.
     *
     * Sugeridos = mismo servicio (vía pivote), misma oficina, estatus true, y
     * que NO estén ya en una saca activa. En CPC la fuente es envios_expedicion
     * (+ distribución de paquetes/muestras); en el resto, envios_almacen.
     */
    public function cargarSugeridos()
    {
        // Una valija que no admite envíos (cerrada o de carga por peso) no tiene
        // sugeridos que ofrecer: mostrarlos invitaría a una acción que el guard
        // de agregarEnvioSugerido() va a rechazar. Se evitan además las consultas.
        if ($this->noAdmiteEnvios()) {
            $this->enviosSugeridos = collect();
            return;
        }

        // IDs de envíos ya en cualquier saca activa (no despachada)
        $enviosEnSacaIds = EnvioSaca::where('activo', true)
            ->pluck('envio_id')
            ->toArray();

        $oficinaSacaId = (int) $this->saca->oficina_id;

        // Servicios permitidos por el tipo de saca (vía pivote tipo_saca_servicio).
        // Fallback a servicio_id legacy si la pivote está vacía (compatibilidad).
        $serviciosValija = $this->saca->tipoSaca?->servicios->pluck('servicio_id')->toArray() ?? [];
        if (empty($serviciosValija) && $this->saca->tipoSaca?->servicio_id) {
            $serviciosValija = [(int) $this->saca->tipoSaca->servicio_id];
        }

        if ($oficinaSacaId === self::OFICINA_CON_EXPEDICION) {
            // En CPC los sugeridos vienen de dos fuentes: Expedición y Distribución de Paquetes/Muestras.
            $enviosSugeridos = EnvioExpedicion::with('envio')
                ->where('estatus', true)
                ->where('oficina_id', $oficinaSacaId)
                ->whereHas('envio', function ($q) use ($serviciosValija) {
                    if (!empty($serviciosValija)) {
                        $q->whereIn('servicio_id', $serviciosValija);
                    }
                })
                ->whereNotIn('envio_id', $enviosEnSacaIds)
                ->get()
                ->map(fn($expedicion) => $expedicion->envio);

            $enviosDistribucion = \App\Models\EnvioDistribucionPaqueteMuestra::with('envio')
                ->where('estatus', true)
                ->where('oficina_id', $oficinaSacaId)
                ->whereHas('envio', function ($q) {
                    $q->where('tipo_saca_id', $this->saca->tipo_saca_id);
                })
                ->whereNotIn('envio_id', $enviosEnSacaIds)
                ->get()
                ->map(fn($distribucion) => $distribucion->envio);

            // Unir ambas fuentes y descartar envíos repetidos.
            $this->enviosSugeridos = $enviosSugeridos
                ->concat($enviosDistribucion)
                ->unique('envio_id')
                ->values();
        } else {
            $this->enviosSugeridos = \App\Models\EnvioAlmacen::with('envio')
                ->where('estatus', true)
                ->where('oficina_id', $oficinaSacaId)
                ->whereHas('envio', function ($q) use ($serviciosValija) {
                    if (!empty($serviciosValija)) {
                        $q->whereIn('servicio_id', $serviciosValija);
                    }
                })
                ->whereNotIn('envio_id', $enviosEnSacaIds)
                ->get()
                ->map(fn($almacen) => $almacen->envio);
        }
    }

    /**
     * Recalcula los sugeridos solo si ya habían sido cargados (el panel está
     * visible). Se llama tras agregar/eliminar envíos para mantener la lista
     * consistente sin forzar la carga si el operador aún no la ha pedido.
     */
    private function refrescarSugeridosSiCargados(): void
    {
        if ($this->enviosSugeridos !== null) {
            $this->cargarSugeridos();
        }
    }

    public function render()
    {
        $usuario = auth()->user();

        // Tipos de valija para el modal de edición (mismo filtro por rol que el listado).
        if ($usuario->hasRole('Clasificador EMS')) {
            $tiposEdit = TipoSaca::where('tipo_saca_id', 12)->where('activo', true)->get();
        } elseif ($usuario->hasRole('Clasificador Bultos')) {
            $tiposEdit = TipoSaca::where('tipo_saca_id', 15)->where('activo', true)->get();
        } else {
            $tiposEdit = TipoSaca::where('activo', true)->get();
        }

        $estadosEdit = Estado::select('estado_id', 'nombre')->get();

        return view('livewire.oficinas.detalles-sacas', [
            'sacas' => $this->saca,
            'enviosSaca' => $this->enviosSaca,
            'envios' => $this->envios,
            'totalPeso' => $this->totalPeso,
            'tiposEdit' => $tiposEdit,
            'estadosEdit' => $estadosEdit,
            'sacaYaSalio' => $this->sacaYaSalio(),
            // Para ocultar los controles de edición cuando la valija no está en
            // esta oficina (se llegó vía "Creadas aquí"). Es solo cosmético: el
            // guard real vive en cada método del componente.
            'sacaEnMiOficina' => $this->sacaEnMiOficina(),
            // Total real de la valija. No se usa $envios->count() porque esa
            // coleccion viene filtrada por el buscador.
            'totalEnvios' => $this->enviosSaca?->count() ?? 0,
        ]);
    }

    public function imprimirRelacion()
    {
        $envioIds = EnvioSaca::where('saca_id', $this->saca_id)->pluck('envio_id');
        $envioSacas = EnvioSaca::whereIn('envio_id', $envioIds)->get()->keyBy('envio_id');
        $sacaIds = $envioSacas->pluck('saca_id')->unique();
        $sacas = Saca::whereIn('saca_id', $sacaIds)->get()->keyBy('saca_id');

        $envios = Envio::whereIn('envio_id', $envioIds)->get();

        $paquetesCertificados = $envios->map(function ($envio) use ($envioSacas, $sacas) {
            $sacaId = $envioSacas[$envio->envio_id]->saca_id ?? null;
            $numeroPrecinto = $sacas[$sacaId]->numero_precinto ?? 'N/A';

            return [
                'tipo' => 'envio',
                'codigo' => $envio->codigo_envio,
                'contenido' => $envio->contenido ?? 'N/A',
                'peso' => $envio->peso,
                'hora' => now()->format('H:i'),
                'fecha' => now()->format('d/m/Y'),
                'precinto' => $numeroPrecinto,
            ];
        });

        $pdf = Pdf::loadView('pdf.relacion-envios-certificado', [
            'paquetesCertificados' => $paquetesCertificados
        ])->setPaper('letter', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'relacion_envios_certificados.pdf');
    }
}
