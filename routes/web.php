<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ConfirmacionTelegramaController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\GraphController;
use App\Http\Controllers\GuiaDespachoController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReciboConsignacionController;
use App\Http\Controllers\ReporteMantenimientosController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VenezuelaController;
use App\Livewire\Aduana\Aduana;
use App\Livewire\AduanaAlmacen;
use App\Livewire\AlianzaRecaduacion\AlianzaRecaudacion as AlianzaRecaduacionAlianzaRecaudacion;
use App\Livewire\AlianzaRecaudacion\AlianzaRecaudacion;
use App\Livewire\Almacenamiento\Almacenamiento;
use App\Livewire\AlmacenOficinas\AlmacenOficinas;
use App\Livewire\ApartadoPostal\ApartadoPostal;
use App\Livewire\ApartadoPostal\CodigosApartadoPostal;
use App\Livewire\ApartadosBeneficiarios;
use App\Livewire\ApartadosCNC\ApartadosCNC;
use App\Livewire\Apis\VerApis;
use App\Livewire\Apostilla\Apostilla;
use App\Livewire\ArrendamientoOficina\ArrendamientoOficina;
use App\Livewire\AsignacionJefeOPT\AsignacionJefeOPT;
use App\Livewire\AsignacionRoles\AsignacionGerenteEstado;
use App\Livewire\AsignacionRoles\AsignacionPresidente;
use App\Livewire\AuditoriaIndex;
use App\Livewire\CargaMasiva\CargaMasiva;
use App\Livewire\ClientesCorporativos\ClientesCorporativos;
use App\Livewire\CompraFilatelia\CompraFilatelia;
use App\Livewire\ConfirmacionTelegrama\ConfirmacionTelegrama;
use App\Livewire\ContratosDetalles\ContratosDetalles;
use App\Livewire\ControlFlota\ConsumoCombustible;
use App\Livewire\ControlFlota\ControlFlota;
use App\Livewire\ControlFlota\Mantenimientos;
use App\Livewire\ControlFlota\MantenimientosHoy;
use App\Livewire\ControlFlota\MantenimientosPorServicios;
use App\Livewire\CorporativoAutorizados\CorporativoAutorizados;
use App\Livewire\CreacionJudicialTribunal\CreacionJudicialTribunal;
use App\Livewire\Cuentas\OficinasIntegrantes;
use App\Livewire\Devoluciones\Devoluciones;
use App\Livewire\DistribucionPaquetesMuestras\DistribucionPaquetesMuestras;
use App\Livewire\Encaminamiento\Encaminamiento;
use App\Livewire\Encaminamiento\EncaminamientoDetalles;
use App\Livewire\Encaminamiento\EncaminamientoExterno;
use App\Livewire\Encaminamiento\EncaminamientoExternoDetalles;
use App\Livewire\EncaminamientoValija\EncaminamientoValija;
use App\Livewire\EntradaAduana;
use App\Livewire\Envios\CierreCaja;
use App\Livewire\Envios\CierreCajaGeneral;
use App\Livewire\Envios\ConsultaEnvios;
use App\Livewire\Envios\DetallesEnvios;
use App\Livewire\Envios\EntregasIntgernacionalesDetalle;
use App\Livewire\Envios\EntregasNacionales;
use App\Livewire\Envios\EntregasNacionalesDetalle;
use App\Livewire\Envios\EnviosForm;
use App\Livewire\Envios\ListadoVentas;
use App\Livewire\Envios\ListadoVentasInternacionales;
use App\Livewire\Envios\ListadoVentasNacionales;
use App\Livewire\EnviosAsignados\EnviosAsignados;
use App\Livewire\EnviosCorporativos\EnviosCorporativos;
use App\Livewire\EnviosInternacionales\EnviosInternacionales;
use App\Livewire\EnviosLotes\EnviosLotes;
use App\Livewire\EnviosPorConfirmar\EnviosPorConfirmar;
use App\Livewire\Expedicion\Expedicion;
use App\Livewire\ExportaFacil\ExportaFacil;
use App\Livewire\GastosOperativos\GastosOperativos;
use App\Livewire\GastosServicios\GastosServicios;
use App\Livewire\GenerarFacturacion\GenerarFacturacion;
use App\Livewire\GenerarTermica\GenerarTermica;
use App\Livewire\GenerarTermicaInternacional\GenerarTermicaInternacional;
use App\Livewire\GestionAlianzas\GestionAlianzas;
use App\Livewire\GestionClientes\GestionClientes;
use App\Livewire\GestionCorrespondencia\AsignacionesAnalista;
use App\Livewire\GestionCorrespondencia\AsignacionesEmisor;
use App\Livewire\GestionCorrespondencia\CorrespondenciaAdmin;
use App\Livewire\GestionCorrespondencia\CorrespondenciaAnalista;
use App\Livewire\GestionCorrespondencia\CorrespondenciaDirector;
use App\Livewire\GestionCorrespondencia\CorrespondenciaGerente;
use App\Livewire\GestionCorrespondencia\CorrespondenciaMain;
use App\Livewire\GestionCorrespondencia\CorrespondenciaPresidente;
use App\Livewire\GestionCorrespondencia\CorrespondenciaUsuario;
use App\Livewire\GestionDespachos\RegistrarEntradaCOP;
use App\Livewire\GestionDespachos\RegistrarEntradaOPT;
use App\Livewire\GestionDespachos\RegistrarSalidaCOP;
use App\Livewire\GestionDespachos\RegistrarSalidaOPT;
use App\Livewire\Imprenta\Imprenta;
use App\Livewire\Incidencia\IncidenciasWeb;
use App\Livewire\Inventario\Inventario;
use App\Livewire\Inventario\InventarioGeneral;
use App\Livewire\InventarioPromotor;
use App\Livewire\Iposplus\Iposplus;
use App\Livewire\Manifiestos\GuiaDespacho;
use App\Livewire\Mapa\VenezuelaMapa;
use App\Livewire\Oficina\PaquetesManifiestos;
use App\Livewire\Oficina\PaquetesPorOficina;
use App\Livewire\Oficina\PaquetesPorOficinaDetalle;
use App\Livewire\OficinaExterna\OficinaExterna;
use App\Livewire\OficinaOperativa\OficinaOperativa;
use App\Livewire\Oficinas\Almacen;
use App\Livewire\Oficinas\AlmacenRezago;
use App\Livewire\Oficinas\CrearOficinas;
use App\Livewire\Oficinas\Detalles;
use App\Livewire\Oficinas\DetallesSacas;
use App\Livewire\Oficinas\Envios;
use App\Livewire\Oficinas\Integrantes;
use App\Livewire\Oficinas\MiOficina;
use App\Livewire\Oficinas\MostrarOficinas;
use App\Livewire\Oficinas\RegistroEntrega;
use App\Livewire\Oficinas\Roles;
use App\Livewire\Oficinas\Sacas;
use App\Livewire\Oficinas\TermicaSaca;
use App\Livewire\Oficinas\VerRepartidores;
use App\Livewire\PaisesExportaFacil\PaisesExportaFacil;
use App\Livewire\ParametrosValijas\ParametrosValijas;
use App\Livewire\PedidosImprenta\PedidosImprenta;
use App\Livewire\PreciosInsumos\PreciosInsumos;
use App\Livewire\Proveedores\Detalles as ProveedoresDetalles;
use App\Livewire\Proveedores\Proveedores;
use App\Livewire\Prueba;
use App\Livewire\ReciboConsignacion\ReciboConsignacion;
use App\Livewire\RegistroNuevoIngreso\RegistroNuevoIngreso;
use App\Livewire\ReporteComunicados;
use App\Livewire\ReportesPresidencia\ReportesPresidencia;
use App\Livewire\Roles\MostrarRoles;
use App\Livewire\Rutas\Rutas;
use App\Livewire\Rutas\RutasLocales;
use App\Livewire\Rutas\RutasNacionales;
use App\Livewire\SalidaAduana;
use App\Livewire\SemaforoPostal\SemaforoPostal;
use App\Livewire\SemaforoPostalDetalles\SemaforoPostalDetalles;
use App\Livewire\SeriesYSellos\SeriesYSellos;
use App\Livewire\ServiciosFlota\ServiciosFlota;
use App\Livewire\TarifaIposplus\TarifaIposplus;
use App\Livewire\Tarifas\MostrarTarifas;
use App\Livewire\Tarifas\MostrarTarifasInternacionales;
use App\Livewire\Tarifas\VerTarifas;
use App\Livewire\Tarifas\VerTarifasConceptos;
use App\Livewire\Tarifas\VerTarifasInternacionales;
use App\Livewire\TarjetasPostales\TarjetasPostales;
use App\Livewire\Tasas\Tasa;
use App\Livewire\Telegrama\Telegrama;
use App\Livewire\UnidadAnalisisDevolucion\UnidadAnalisisDevolucion;
use App\Livewire\Usuarios\MostrarUsuarios;
use App\Livewire\Usuarios\UsuariosMostrar;
use App\Livewire\Vehiculos\CrearVehiculos;
use App\Livewire\VehiculosOficina\VerVehiculos;
use App\Livewire\Viajes\MostrarViajes;
use App\Livewire\Viajes\MostrarViajesLocales;
use App\Livewire\Viajes\MostrarViajesNacionales;
use App\Models\ServicioFlota;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', 'login');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // Route for the getting the data feed
    Route::get('/json-data-feed', [DataFeedController::class, 'getDataFeed'])->name('json_data_feed');

    // Graficas Dashboard
    Route::get('/graph1', [DashboardController::class, 'totalEnvios'])->name('graph1');
    Route::get('/graph2', [DashboardController::class, 'ingresosEnvios'])->name('graph2');
    Route::get('/graph3', [DashboardController::class, 'totalServicios'])->name('graph3');
    Route::get('/graph4', [DashboardController::class, 'ingresosServicios'])->name('graph4');
    Route::get('/graph5', [GraphController::class, 'totalOficinas'])->name('graph5');
    Route::get('/graph6', [DashboardController::class, 'tasaDevoluiciones'])->name('graph6');
    Route::get('/graph7', [GraphController::class, 'enviodiarosmes'])->name('graph7');
    Route::get('/graph8', [GraphController::class, 'tipeOficina'])->name('graph8');
    Route::get('/graph9', [DashboardController::class, 'margenBeneficio'])->name('graph9');
    Route::get('/mapaOficinas/{code}', [VenezuelaController::class, 'oficinaMapa'])->name('mapa');
    Route::get('/mapaColores', [VenezuelaController::class, 'mapaColores'])->name('mapaColores');
    Route::get('/oficinatabla', [VenezuelaController::class, 'oficinatabla'])->name('oficinatabla');
    //dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //Cuentas
    Route::get('/cuentas', [UserController::class, 'index'])->name('cuentas')->middleware('can:Ver usuarios');
    Route::get('/integrantes', OficinasIntegrantes::class)->name('integrantes-gestion')->middleware('can:Gestionar Integrantes');

    //Roles
    Route::get('/roles', MostrarRoles::class)->name('roles-mostrar')->middleware('can:Ver Roles');

    // ROLES + PERMISOS
    Route::get('/roles/permisos/{rol}', [RoleController::class, 'permisos'])->name('roles.permisos')->middleware('can:Editar Permisos');

    //Oficinas
    Route::get('/oficinas', MostrarOficinas::class)->name('oficinas-mostrar')->middleware('can:Ver Oficinas-admin');
    Route::get('/oficina-detalle/{oficina_id}', Detalles::class)->name('oficina-detalles')->middleware('can:Ver Oficinas-admin');
    Route::get('/mi-oficina', MiOficina::class)->name('mi-oficina')->middleware('can:Ver Mi oficina');
    Route::get('/sacas', Sacas::class)->name('mostrar-sacas');
    Route::get('/saca-detalle/{saca_id}', DetallesSacas::class)->name('sacas-detalles');
    Route::get('/saca-termica/{saca_id}', TermicaSaca::class)->name('saca-termica');
    Route::get('/ver-almacen', Almacen::class)->name('ver-almacen');
    Route::get('/almacen-rezago', AlmacenRezago::class)->name('almacen-rezago');
    Route::get('/oficina-detalle/integrantes/{oficina_id}', Integrantes::class)->name('integrantes');
    Route::get('/oficina-detalle/vehiculos/{oficina_id}', VerVehiculos::class)->name('ver-vehiculos');
    Route::get('/oficina-roles/roles/{oficina_id}', Roles::class)->name('ver-roles-oficinas');
    Route::get('/oficinas/ver-repartidores', VerRepartidores::class)->name('repartidores.index');

    //Clientes Corporativos
    Route::get('/clientes-corporativos', ClientesCorporativos::class)->name('clientes-corporativos')->middleware('can:Clientes corporativos');

    //Corporativos Autorizados
    Route::get('/corporativo-autorizados', CorporativoAutorizados::class)->name('corporativo-autorizados')->middleware('can:Clientes corporativos');

    //Contratos Detalles
    Route::get('/contratos-detalles', ContratosDetalles::class)->name('contratos-detalles')->middleware('can:Clientes corporativos');

    //Envios
    Route::get('/envios', EnviosForm::class)->name('envios-form')->middleware(['can:Crear envios', 'oficina-operativa']);
    Route::get('/envios-lotes', EnviosLotes::class)->name('envios-lotes')->middleware(['can:Crear envios', 'oficina-operativa']);
    Route::get('/consulta-envios/{servicio_id}', ConsultaEnvios::class)->name('envios.consulta-envios')->middleware(['can:Ver envios', 'oficina-operativa']);
    Route::get('/detalles-envios/{servicio_id}/{envio}', DetallesEnvios::class)->name('envios.detalles-envios')->middleware(['can:Ver envios', 'oficina-operativa']);
    Route::get('/envios-confirmar', EnviosPorConfirmar::class)->name('confirmar-envios')->middleware(['can:Ver envios', 'oficina-operativa']);
    Route::get('/entrega-nacionales', EntregasNacionales::class)->name('entrega-nacionales')->middleware(['can:Ver envios', 'oficina-operativa']);
    Route::get('/entrega-nacionales-detalle/{servicio_id}/{desde?}/{hasta?}', EntregasNacionalesDetalle::class)->name('entrega-nacionales-detalles')->middleware(['can:Ver envios', 'oficina-operativa']);
    Route::get('/entrega-internacionales', EntregasIntgernacionalesDetalle::class)->name('entrega-internacionales')->middleware(['can:Ver envios', 'oficina-operativa']);
    Route::get('/listado-ventas', ListadoVentas::class)->name('envios.listado-ventas')->middleware(['can:Ver envios', 'oficina-operativa']);
    Route::get('/listado-ventas-nacionales', ListadoVentasNacionales::class)->name('envios.listado-ventas-nacionales')->middleware(['can:Ver envios', 'oficina-operativa']);
    Route::get('/listado-ventas-internacionales', ListadoVentasInternacionales::class)->name('envios.listado-ventas-internacionales')->middleware(['can:Ver envios', 'oficina-operativa']);
    Route::get('/cierre-caja/{servicio_id}/{desde?}/{hasta?}/{oficina_id?}/{usuario_id?}', CierreCaja::class)->name('envios.cierre-caja')->middleware(['can:Ver envios', 'oficina-operativa']);
    Route::get('/reporte-cierre/{desde?}/{hasta?}/{oficina_id?}/{usuario_id?}', CierreCajaGeneral::class)->name('envios.cierre-caja-general')->middleware(['can:Ver envios', 'oficina-operativa']);

    //Rastreo y Seguimiento
    Route::get('/encaminamiento', Encaminamiento::class)->name('encaminamiento.encaminamiento')->middleware('can:Ver envios');
    Route::get('/encaminamiento/{id}', EncaminamientoDetalles::class)->name('encaminamiento.encaminamiento-detalles')->middleware('can:Ver envios');
    Route::get('/encaminamiento-valija', EncaminamientoValija::class)->name('encaminamiento-valija')->middleware('can:Ver envios');

    //Rastreo y Seguimiento Externo
    Route::get('/tracking', EncaminamientoExterno::class)->name('encaminamiento.encaminamientoext')->middleware('can:Ver envios');
    Route::get('/tracking/{id}', EncaminamientoExternoDetalles::class)->name('encaminamiento.encaminamientoext-detalles')->middleware('can:Ver envios');

    //Generar Termica
    Route::get('/termica/{envio}', GenerarTermica::class)->name('generar-termica');
    Route::get('/termica-internacional/{envio}', GenerarTermicaInternacional::class)->name('generar-termica-internacional');

    //Devoluciones
    Route::get('/devolucion-incidencia', Devoluciones::class)->name('devolucion-incidencia')->middleware('oficina-operativa');

    //Apartado Postal
    Route::get('/apartado', ApartadoPostal::class)->name('apartado-postal')->middleware('can:Crear envios');
    Route::get('/codigo-apartado', CodigosApartadoPostal::class)->name('codigo-apartado');
    Route::get('/apartados-c-n-c', ApartadosCNC::class)->name('apartados-c-n-c');
    Route::get('/apartado-beneficiarios', ApartadosBeneficiarios::class)->name('apartados-beneficiarios');

    // Nueva ruta para exportar PDF desde Livewire
    Route::get('/codigo-apartado/exportar-pdf', [CodigosApartadoPostal::class, 'exportarPdf'])->name('codigo-apartado.exportar-pdf');
    Route::get('/apartados-cnc/exportar-pdf', ApartadosCNC::class . '@exportarPdf')->name('codigo-apartado-cnc.exportar-pdf');

    //Tarjetas Postales
    Route::get('/tarjetas-postales', TarjetasPostales::class)->name('tarjetas-postales')->middleware(['can:Crear envios', 'oficina-operativa']);

    //Almacenamiento
    Route::get('/almacenamiento', Almacenamiento::class)->name('almacenamiento');

    //Imprenta
    Route::get('/imprenta', Imprenta::class)->name('imprenta');
    Route::get('/pedidos-imprenta', PedidosImprenta::class)->name('pedidos-imprenta');

    //Telegrama
    Route::get('/telegrama', Telegrama::class)->name('telegrama')->middleware(['can:Ver telegramas', 'oficina-operativa']);

    //Confirmacion de Telegrama
    Route::get('/confirmacion-telegrama', ConfirmacionTelegrama::class)->name('confirmacion-telegrama')->middleware(['can:Ver telegramas', 'oficina-operativa']);

    //Exporta Facil
    Route::get('/exporta-facil', ExportaFacil::class)->name('exporta-facil')->middleware(['can:Crear envios', 'oficina-operativa']);

    //IposPlus
    Route::get('/iposplus', Iposplus::class)->name('iposplus')->middleware(['can:Crear envios', 'oficina-operativa']);

    //Recolectas (solicitudes desde la app de clientes)
    Route::get('/recolectas-oficina', \App\Livewire\Recolectas\RecolectasOficina::class)->name('recolectas-oficina')->middleware(['can:Ver recolectas', 'oficina-operativa']);

    //Apostilla
    Route::get('/apostilla', Apostilla::class)->name('apostilla')->middleware(['can:Ver Apostilla', 'oficina-operativa']);

    //Envios Internacionales
    Route::get('/envios-internacionales', EnviosInternacionales::class)->name('envios-internacionales')->middleware(['oficina-operativa', 'can:Aperturar Envios']);

    //Envios Internacionales
    Route::get('/envios-corporativos', EnviosCorporativos::class)->name('envios-corporativos')->middleware(['crear-envios', 'oficina-operativa']);

    // Carga Masiva
    Route::get('/carga-masiva', CargaMasiva::class)->name('carga-masiva')->middleware(['can:Crear envios']);

    //Reportes Presidencia
    Route::get('/reportes-presidencia', ReportesPresidencia::class)->name('reportes-presidencia')->middleware(['can:Ver Reportes Presidencia', 'oficina-operativa']);

    //Gastos Operativos
    Route::get('/gastos-operativos', GastosOperativos::class)->name('gastos-operativos')->middleware(['can:Ver Gastos Operativos', 'oficina-operativa']);

    //Gastos Servicios
    Route::get('/gastos-servicios', GastosServicios::class)->name('gastos-servicios')->middleware(['oficina-operativa']);

    //Gastos de Arrendamiento
    Route::get('/arrendamiento-oficina', ArrendamientoOficina::class)->name('arrendamiento-oficina')->middleware(['can:Ver Gastos Arrendamiento', 'oficina-arrendada']);

    //Registro Nuevo Ingreso
    Route::get('/registro-empleado', RegistroNuevoIngreso::class)->name('registro-empleado')->middleware(['can:Ver Registro de Empleados', 'oficina-operativa']);

    //Asigancion de jefe en opts
    Route::get('/asignacion-jefe-opt', AsignacionJefeOPT::class)->name('asignacion-jefe-opt')->middleware(['can:Ver Asignacion de Jefe en Opts', 'oficina-operativa']);

    //Parametros Valijas
    Route::get('/parametros-valijas', ParametrosValijas::class)->name('parametros-valijas');



    //TARIFAS
    Route::get('/servicios', MostrarTarifas::class)->name('tarifas')->middleware('can:Ver servicios');
    Route::get('/servicios-internacionales', MostrarTarifasInternacionales::class)->name('tarifas-internacionales');
    Route::get('/ver-tarifas/{servicio_id}', VerTarifas::class)->name('ver-tarifas');
    Route::get('/ver-tarifas-conceptos', VerTarifasConceptos::class)->name('tarifas-conceptos');
    Route::get('/ver-tarifas-internacionales/{servicio_id}', VerTarifasInternacionales::class)->name('ver-tarifas-internacionales');

    Route::get('/ver-tasas', Tasa::class)->name('ver-tasa')->middleware('can:Ver tasas');
    Route::get('/servicios-flota', ServiciosFlota::class)->name('servicios-flota');
    Route::get('/precios-insumos', PreciosInsumos::class)->name('precios-insumos');
    Route::get('/países-exporta-facil', PaisesExportaFacil::class)->name('paises-exporta-facil');


    Route::get('/ver-proveedores', Proveedores::class)->name('ver-proveedores');
    Route::get('/detalle-proveedor/{proveedor_id}', ProveedoresDetalles::class)->name('detalle-proveedor');
    Route::get('/crear-vehiculo/{proveedor_id}', CrearVehiculos::class)->name('crear-vehiculo');

    Route::get('/rutas', Rutas::class)->name('ver-rutas');
    Route::get('/viajes', MostrarViajes::class)->name('ver-viajes');

    Route::get('/rutas-locales', RutasLocales::class)->name('ver-rutas-locales');
    Route::get('/viajes-locales', MostrarViajesLocales::class)->name('ver-viajes-locales');

    Route::get('/rutas-nacionales', RutasNacionales::class)->name('ver-rutas-nacionales');
    Route::get('/viajes-nacionales', MostrarViajesNacionales::class)->name('ver-viajes-nacionales');
    Route::get('/Guias-Despacho', GuiaDespacho::class)->name('manifiestos');
    Route::get('/Guias-Despacho/{numero_despacho_id}', PaquetesManifiestos::class)->name('manifiestos_paquetes');
    // Guia historica: despachos anteriores al modelo actual (sin oficina_destino_id).
    // Su informacion real vive en el manifiesto, no en el despacho.
    Route::get('/Guias-Despacho/historica/{manifiesto_id}', \App\Livewire\Manifiestos\GuiaManifiesto::class)->name('guia-manifiesto');
    //Control de Flota
    Route::get('/Control-Flota', ControlFlota::class)->name('flota');
    Route::get('/Control-Flota/Mantenimientos-Hoy', MantenimientosHoy::class)->name('mantenimientos-hoy');
    Route::get('/Control-Flota/Mantenimientos-Por-Servicios', MantenimientosPorServicios::class)->name('mantenimientos-servicios');
    Route::get('/Control-Flota/{vehiculo_id}', Mantenimientos::class)->name('mantenimiento');
    Route::get('/Control-flota/consumo-combustible', ConsumoCombustible::class)->name('consumo-combustible');
    Route::get('/control-flota/historial-combustible/{vehiculoId}', \App\Livewire\ControlFlota\HistorialCombustibleVehiculo::class)->name('historial-combustible-vehiculo');
    Route::get('/Registrar-Entrada', RegistrarEntradaOPT::class)->name('entradaOPT');
    Route::get('/Registrar-Salida', RegistrarSalidaOPT::class)->name('salidaOPT');

    Route::get('/Registrar-Entrada-Cop', RegistrarEntradaCOP::class)->name('entradaCOP');
    Route::get('/Registrar-salida-Cop', RegistrarSalidaCOP::class)->name('salidaCOP');
    Route::get('/Incidencias', IncidenciasWeb::class)->name('Incidencias');
    Route::get('/Registrar_entrega/{envio_id}', RegistroEntrega::class)->name('entrega');
    // Entrega múltiple: varios envíos en un solo pago. Los IDs van por query string (?ids=1,2,3).
    Route::get('/Registrar_entrega_multiple', RegistroEntrega::class)->name('entrega-multiple');
    Route::get('/almacen-aduana', AduanaAlmacen::class)->name('almacen-aduana');
    Route::get('/entrada-aduana', EntradaAduana::class)->name('entrada-aduana');
    Route::get('/salida-aduana', SalidaAduana::class)->name('salida-aduana');
    Route::get('/recibo-consignacion', ReciboConsignacion::class)->name('recibo-consignacion');
    Route::get('/recibo-consignacion/preview/{envioId}', [ReciboConsignacionController::class, 'preview'])->name('recibo-consignacion.preview');
    Route::get('/recibo-consignacion/pdf/{envioId}', [ReciboConsignacionController::class, 'pdf'])->name('recibo-consignacion.pdf');


    Route::get('/ver-apis', VerApis::class)->name('ver-apis');

    Route::get('/mapa-venezuela', VenezuelaMapa::class)->name('mapavenezuela');

    // Paquetes por oficina (vista pública dentro del área autenticada)
    Route::get('/paquetes-por-oficina', PaquetesPorOficina::class)->name('paquetes-por-oficina');
    Route::get('/paquetes-por-oficina/{oficina_id}', PaquetesPorOficinaDetalle::class)->name('paquetes-por-oficina.detalle');

    //Inventario
    Route::get('/inventario', Inventario::class)->name('inventario');
    Route::get('/inventario-general', InventarioGeneral::class)->name('inventario-general');
    Route::get('/inventario-promotor', InventarioPromotor::class)->name('inventario-promotor');

    //Oficina no Operativa
    Route::get('/oficina-operativa', OficinaOperativa::class)->name('oficina-operativa');

    //oficina externa
    Route::get('/oficina-externa', OficinaExterna::class)->name('oficina-externa')->middleware(['can:Crear Oficinas Aliadas']);

    //Almacen de Oficinas
    Route::get('/almacen-de-oficinas', AlmacenOficinas::class)->name('almacen-de-oficinas')->middleware('can:Ver Informacion de Envíos');

    //Semaforo Postal
    Route::get('/semaforo-postal', SemaforoPostal::class)->name('semaforo-postal');
    Route::get('/semaforo-postal-detalles', SemaforoPostalDetalles::class)->name('semaforo-postal-detalles');

    //Series y sellos
    Route::get('/series-y-sellos', SeriesYSellos::class)->name('series-y-sellos');

    //Filatelia
    Route::get('/filatelia', CompraFilatelia::class)->name('filatelia');

    //Alianza y Recaudacion
    Route::get('/alianza-recaudacion', AlianzaRecaudacion::class)->name('alianza-recaudacion');

    //Alianza y Recaudacion
    Route::get('/envios-asignados', EnviosAsignados::class)->name('envios-asignados');

    // //Facturar envios
    // Route::get('/generar-facturacion', GenerarFacturacion::class)->name('generar-facturacion');

    //Crear Oficinas
    Route::get('/crear-oficinas', CrearOficinas::class)->name('crear-oficinas');

    // Asignar Gerente de Estado: SuperAdmin y Presidente
    Route::get('/asignar-gerente-estado', AsignacionGerenteEstado::class)->name('asignar-gerente-estado');

    // Asignar Presidente: solo SuperAdmin
    Route::get('/asignar-presidente', AsignacionPresidente::class)->name('asignar-presidente');

    // Unidad de Analisis de Devolucion
    Route::get('/unidad-analisis-devolucion', UnidadAnalisisDevolucion::class)->name('unidad-analisis-devolucion');

    //Aduana
    Route::get('/aduana', Aduana::class)->name('aduana');

    //Expedición
    Route::get('/expedicion', Expedicion::class)->name('expedicion');

    //Distribución Paquetes Muestras
    Route::get('/distribucion-paquetes-muestras', DistribucionPaquetesMuestras::class)->name('distribucion-paquetes-muestras');

    Route::get('/onboarding-01', function () {
        return view('pages/onboarding-01');
    })->name('onboarding-01');
    Route::get('/onboarding-02', function () {
        return view('pages/onboarding-02');
    })->name('onboarding-02');
    Route::get('/onboarding-03', function () {
        return view('pages/onboarding-03');
    })->name('onboarding-03');
    Route::get('/onboarding-04', function () {
        return view('pages/onboarding-04');
    })->name('onboarding-04');
    Route::get('/component/button', function () {
        return view('pages/component/button-page');
    })->name('button-page');
    Route::get('/component/form', function () {
        return view('pages/component/form-page');
    })->name('form-page');
    Route::get('/component/dropdown', function () {
        return view('pages/component/dropdown-page');
    })->name('dropdown-page');
    Route::get('/component/alert', function () {
        return view('pages/component/alert-page');
    })->name('alert-page');
    Route::get('/component/modal', function () {
        return view('pages/component/modal-page');
    })->name('modal-page');
    Route::get('/component/pagination', function () {
        return view('pages/component/pagination-page');
    })->name('pagination-page');
    Route::get('/component/tabs', function () {
        return view('pages/component/tabs-page');
    })->name('tabs-page');
    Route::get('/component/breadcrumb', function () {
        return view('pages/component/breadcrumb-page');
    })->name('breadcrumb-page');
    Route::get('/component/badge', function () {
        return view('pages/component/badge-page');
    })->name('badge-page');
    Route::get('/component/avatar', function () {
        return view('pages/component/avatar-page');
    })->name('avatar-page');
    Route::get('/component/tooltip', function () {
        return view('pages/component/tooltip-page');
    })->name('tooltip-page');
    Route::get('/component/accordion', function () {
        return view('pages/component/accordion-page');
    })->name('accordion-page');
    Route::get('/component/icons', function () {
        return view('pages/component/icons-page');
    })->name('icons-page');

    Route::fallback(function () {
        return view('pages/utility/404');
    });

    Route::get('/confirmacion-telegrama/reporte-pdf', [ConfirmacionTelegramaController::class, 'reportePdf'])
        ->name('confirmacion-telegrama.pdf')
        ->middleware('auth');

    Route::get('/confirmacion-telegrama/envio-pdf/{envio}', [ConfirmacionTelegramaController::class, 'envioPdf'])
        ->name('confirmacion-telegrama.envio-pdf')
        ->middleware('auth');

    Route::get('/gestion-clientes', GestionClientes::class)->name('clientes.index');

    Route::get('/gestion-alianzas', GestionAlianzas::class)
        ->middleware('auth')
        ->name('gestion.alianzas');

    Route::get('/tarifas-iposplus', TarifaIposplus::class)->name('tarifas.index');

    //Gestion Correspondencia
    Route::get('/correspondencia', CorrespondenciaMain::class)
        ->name('correspondencia')
        ->middleware('can:Acceso Modulo Correspondencia');

    Route::get('/correspondencia-presidente', CorrespondenciaPresidente::class)
        ->name('correspondencia.presidente')
        ->middleware('can:Ver Correspondencia-Presidente');

    Route::get('/correspondencia-director', CorrespondenciaDirector::class)
        ->name('correspondencia.director')
        ->middleware('can:Ver Correspondencia-Director');

    Route::get('/correspondencia-gerente', CorrespondenciaGerente::class)
        ->name('correspondencia.gerente')
        ->middleware('can:Ver Correspondencia-Gerente');

    Route::get('/correspondencia-analista', CorrespondenciaAnalista::class)
        ->name('correspondencia.analista')
        ->middleware('can:Ver Correspondencia-Analista');

    Route::get('/correspondencia-usuario', CorrespondenciaUsuario::class)
        ->name('correspondencia.usuario')
        ->middleware('can:Ver Correspondencia-Usuario');

    Route::get('/correspondencia-admin', CorrespondenciaAdmin::class)
        ->name('correspondencia.admin')
        ->middleware('can:Ver Correspondencia-Admin');

    Route::get('/correspondencia/asignaciones-emisor', AsignacionesEmisor::class)
        ->name('correspondencia.asignaciones-emisor')
        ->middleware('can:Asignar Comunicados');

    Route::get('/correspondencia/asignaciones-analista', AsignacionesAnalista::class)
        ->name('correspondencia.asignaciones-analista')
        ->middleware('can:Atender Asignaciones');

    Route::get('/correspondencia/reportes', ReporteComunicados::class)
        ->name('correspondencia.reportes')
        ->middleware('can:Ver Reportes Correspondencia');

    Route::get('/correspondencia/auditoria', AuditoriaIndex::class)
        ->name('correspondencia.auditoria')
        ->middleware('can:Ver Auditoria Correspondencia');

    Route::post('/reporte-mantenimientos/pdf', [ReporteMantenimientosController::class, 'pdf']);
    Route::post('/reporte-mantenimientos/excel', [ReporteMantenimientosController::class, 'excel']);
    Route::get('/reporte-mantenimientos/pdf/{vehiculo}', [ReporteMantenimientosController::class, 'pdfConFiltros']);
    Route::get('/reporte-mantenimientos/excel/{vehiculo}', [ReporteMantenimientosController::class, 'excelConFiltros']);
    Route::get('/gestion/circuitos-tribunales', CreacionJudicialTribunal::class)->name('gestion.circuitos_tribunales');
});
