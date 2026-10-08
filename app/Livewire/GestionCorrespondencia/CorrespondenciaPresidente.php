<?php

namespace App\Livewire\GestionCorrespondencia;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\GestionCorrespondencia\Concerns\HasPaginacion;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Comunicado;
use App\Models\User;
use App\Models\ComunicadoDestinatario;
use App\Services\CorrespondenceFlowService;

class CorrespondenciaPresidente extends Component
{
    use WithFileUploads, HasPaginacion;

    // --- Estado de la UI ---
    public $current_view = 'dashboard';
    public $current_role = 'Presidente';
    
    // --- Gestión de Comunicación ---
    public $selected_doc_type = '';
    public bool $tipo_bloqueado = false;
    public $destinatario_search = '';
    public $destinatarios_lista = [];
    public $filtro_rol = '';

    // --- Datos del Formulario Dinámico ---
    public $form_data = [
        'remitente' => '',
        'destinatario' => '',
        'destinatario_final_id' => '',
        'asunto' => '',
        'cuerpo' => '',
        'prioridad' => 'Normal',
        'fecha_limite' => '',
        // Campos específicos Agenda al Decisor
        'codigo_control' => '',
        'agenda_numero' => '',
        'presentante' => '',
        'secuencia' => '',
        'cuerpo_resumen' => '',
        'cuerpo_propuesta' => '',
        'texto_asunto' => '',
        'agenda_has_anexo' => 'No',
        'pi_has_anexo' => 'No',
        'pi_asunto_pdf' => '',
        'pc_has_anexo' => 'No',
        'pc_asunto_pdf' => '',
        'pcd_has_anexo' => 'No',
        'pcd_asunto_pdf' => '',
        'pcd_presentado_por_cargo' => '',
        'presentante_cargo' => '',
        // MEMORANDO
        'memo_para_nombre' => '',
        'memo_para_cargo' => '',
        'memo_de_nombre' => '',
        'memo_de_cargo' => '',
        'memo_accion' => 'Solicitar',
        'memo_cuerpo_detalle' => '',
        'memo_visado' => '',
        'memo_asunto_pdf' => '',
        // Firmante común (Memorando, Circular, Oficios)
        'firmante_nombre' => '',
        'firmante_cargo' => '',
        // Circular
        'circular_titulo' => '',
        'circular_accion' => 'comunica',
        'circular_contenido' => '',
        'circular_visado' => '',
        'circular_cargo' => '',
        // Oficio - Tipo Carta
        'oficio_carta_numero' => '',
        'oficio_carta_para_nombre' => '',
        'oficio_carta_para_cargo' => '',
        'oficio_carta_atencion' => '',
        'oficio_carta_accion' => 'notificarle',
        'oficio_carta_contenido' => '',
        'oficio_carta_visado_part' => 'OP/XX',
        'oficio_carta_cc' => '',
        'pi_presentado_por' => '',
        // MINUTA
        'minuta_fecha' => '',
        'minuta_facilitador' => '',
        'minuta_dependencia' => '',
        'minuta_presentado_a' => '',
        'minuta_presentado_a_cargo' => '',
        'minuta_elaborado' => '',
        'minuta_elaborado_cargo' => '',
        'minuta_revisado' => '',
        'minuta_revisado_cargo' => '',
        'minuta_anexos' => 'NO',
        'minuta_puntos' => ['', ''],
        'minuta_participantes' => [
            ['nombre' => '', 'ubicacion' => '', 'correo' => '', 'telefono' => ''],
            ['nombre' => '', 'ubicacion' => '', 'correo' => '', 'telefono' => ''],
            ['nombre' => '', 'ubicacion' => '', 'correo' => '', 'telefono' => ''],
            ['nombre' => '', 'ubicacion' => '', 'correo' => '', 'telefono' => ''],
            ['nombre' => '', 'ubicacion' => '', 'correo' => '', 'telefono' => ''],
        ],
        'minuta_planteamientos' => ['', '', '', ''],
        'minuta_acuerdos' => ['', '', '', ''],
        'minuta_tareas' => [
            ['tarea' => '', 'fecha' => '', 'responsable' => ''],
            ['tarea' => '', 'fecha' => '', 'responsable' => ''],
            ['tarea' => '', 'fecha' => '', 'responsable' => ''],
            ['tarea' => '', 'fecha' => '', 'responsable' => ''],
        ]
    ];

    public $memo_usuarios = [];
    public $usuariosFinales = [];
    public $memoPara = '';
    public $memoDe = '';
    public $agendaPresentante = '';
    public $pcdPresentadoPor = '';
    public $pcdRevisadoSel = '';
    public $pcdAprobadoSel = '';
    public $agendaParaSel = '';
    public $agendaPresentadoSel = '';
    public $agendaVerificadoSel = '';
    public $agendaAprobadoSel = '';
    public $minutaElaboradoSel = '';
    public $minutaRevisadoSel = '';
    public $minutaPresentadoASel = '';
    public $oficioPara = '';
    public $firmanteMemSel = '';
    public $firmanteCircSel = '';
    public $firmanteOficioSel = '';

    public $tipo_destino = 'Individual';
    public $destinatario_email = '';

    public $respuesta_a = null;
    public $adjuntos = [];

    // --- Devolver para corregir ---
    public string $observacion_devolucion = '';
    public ?string $devolver_comunicado_id = null;
    public bool $show_devolver_form = false;

    // --- Datos Simulados ---
    public $comunicaciones = [];
    public $comunicaciones_recientes = [];
    public $comunicaciones_seguimiento = [];
    public $notifications_count = 0;
    public $active_doc = null;

    // --- Dashboard ---
    public $dashboard_stats = [];

    // --- Filtros de Bandeja de Entrada ---
    public $filtro_prioridad = '';
    public $filtro_estatus = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';

    // --- PDF Handling ---
    public $show_pdf_preview = false;
    public $pdf_url = '';

    // --- Navegación de Referencias ---
    public $previous_doc_id = null;
    public $previous_view = null;

    // --- Flujo de Correspondencia ---
    public string $destinatario_delegacion = '';

    // Tipos con destinatario_final_id obligatorio y fijo (solo 1 destinatario, no editable por Presidente)
    const TIPOS_DESTINATARIO_FIJO = ['MEMORANDO', 'Minuta Horizontal', 'Agenda al Decisor'];

    // Tipos que finalizan al firmar sin envío posterior
    const TIPOS_FINALIZAN_AL_FIRMAR = [
        'Punto de Información - Presidencia IPOSTEL',
        'Punto de Cuenta - Presidencia IPOSTEL',
        'Punto de Cuenta - Directorio',
        'Punto-de-Cuenta-MPPT',
        'Punto-de-Informacion-MPPT',
        'Oficio - Tipo Carta',
        'Oficio - Tipo Oficio',
    ];

    // --- Modal Enviar al Destinatario Final (un solo destinatario) ---
    public bool $show_enviar_final_modal = false;
    public ?int $enviar_final_db_id = null;
    public string $enviar_final_nombre = '';
    public string $enviar_final_email = '';
    public string $enviar_final_seleccionado_id = '';
    public bool $enviar_final_fijo = false; // true = destinatario no editable

    // --- Modal Enviar Circular (múltiples destinatarios) ---
    public bool $show_enviar_circular_modal = false;
    public ?int $enviar_circular_db_id = null;
    public array $circular_destinatarios_seleccionados = [];

    public function view_pdf($id = null)
    {
        $doc = $id ? (array) collect($this->comunicaciones)->firstWhere('id', $id) : $this->active_doc;
        if (!$doc) return;
        
        $this->pdf_url = $this->generate_pdf_url($doc);
        $this->show_pdf_preview = true;
    }

    private function generate_pdf_url($doc)
    {
        $docData = (array) json_decode(json_encode($doc), true);

        $pdfData = [
            'id' => $docData['id'],
            'type' => $docData['type'],
            'subject' => $docData['subject'] ?? 'S/N',
            'sender' => $docData['sender'] ?? 'S/N',
            'sender_name' => $docData['sender_name'] ?? null,
            'sender_cargo' => $docData['sender_cargo'] ?? null,
            'destinatario' => $docData['destinatario'] ?? 'S/N',
            'cuerpo' => $docData['cuerpo'] ?? 'Sin contenido disponible.',
            'date' => $this->get_fecha_letras($docData['date'] ?? now()->format('d/m/Y')),
            // Campos Agenda al Decisor
            'codigo_control' => $docData['codigo_control'] ?? null,
            'agenda_numero' => $docData['agenda_numero'] ?? null,
            'presentante' => $docData['presentante'] ?? null,
            'secuencia' => $docData['secuencia'] ?? null,
            'cuerpo_resumen' => $docData['cuerpo_resumen'] ?? null,
            'cuerpo_propuesta' => $docData['cuerpo_propuesta'] ?? null,
            'texto_asunto' => $docData['texto_asunto'] ?? null,
            'agenda_para_nombre' => $docData['agenda_para_nombre'] ?? null,
            'agenda_para_cargo' => $docData['agenda_para_cargo'] ?? null,
            'agenda_verificado_nombre' => $docData['agenda_verificado_nombre'] ?? null,
            'agenda_verificado_cargo' => $docData['agenda_verificado_cargo'] ?? null,
            'agenda_aprobado_nombre' => $docData['agenda_aprobado_nombre'] ?? null,
            'agenda_aprobado_cargo' => $docData['agenda_aprobado_cargo'] ?? null,
            'presentante_cargo' => $docData['presentante_cargo'] ?? null,
            // Memorando
            'memo_correlativo' => $docData['memo_correlativo'] ?? null,
            'memo_para_nombre' => $docData['memo_para_nombre'] ?? null,
            'memo_para_cargo' => $docData['memo_para_cargo'] ?? null,
            'memo_de_nombre' => $docData['memo_de_nombre'] ?? null,
            'memo_de_cargo' => $docData['memo_de_cargo'] ?? null,
            'memo_accion' => $docData['memo_accion'] ?? null,
            'memo_cuerpo_detalle' => $docData['memo_cuerpo_detalle'] ?? null,
            'memo_visado' => $docData['memo_visado'] ?? null,
            'memo_asunto_pdf' => $docData['memo_asunto_pdf'] ?? null,
            'memo_anexos_lista' => $this->get_anexos_string($docData),
            'agenda_has_anexo' => $docData['agenda_has_anexo'] ?? 'No',
            'agenda_para_nombre' => $docData['agenda_para_nombre'] ?? null,
            // Circular
            'circular_numero' => $docData['circular_numero'] ?? null,
            'circular_titulo' => $docData['circular_titulo'] ?? null,
            'circular_accion' => $docData['circular_accion'] ?? null,
            'circular_contenido' => $docData['circular_contenido'] ?? null,
            'circular_visado' => $docData['circular_visado'] ?? null,
            'circular_cargo' => $docData['circular_cargo'] ?? null,
            // Oficio - Tipo Carta / Oficio
            'oficio_carta_numero' => $docData['oficio_carta_numero'] ?? null,
            'oficio_carta_para_nombre' => $docData['oficio_carta_para_nombre'] ?? null,
            'oficio_carta_para_cargo' => $docData['oficio_carta_para_cargo'] ?? null,
            'oficio_carta_entidad' => $docData['oficio_carta_entidad'] ?? null,
            'oficio_carta_atencion' => $docData['oficio_carta_atencion'] ?? null,
            'oficio_carta_accion' => $docData['oficio_carta_accion'] ?? 'notificarle',
            'oficio_carta_contenido' => $docData['oficio_carta_contenido'] ?? null,
            'oficio_carta_visado_part' => $docData['oficio_carta_visado_part'] ?? 'OP/XX',
            'oficio_carta_anexos_lista' => $this->get_anexos_string($docData),
            'oficio_carta_cc' => $docData['oficio_carta_cc'] ?? null,
            // Punto de Información
            'pi_numero' => $docData['pi_numero'] ?? null,
            'pi_presentado_por' => $docData['pi_presentado_por'] ?? null,
            'pi_sintesis' => $docData['pi_sintesis'] ?? null,
            'pi_recomendaciones' => $docData['pi_recomendaciones'] ?? null,
            'pi_has_anexo' => $docData['pi_has_anexo'] ?? 'No',
            'pi_asunto_pdf' => $docData['pi_asunto_pdf'] ?? null,
            // Punto de Cuenta
            'pc_numero' => $docData['pc_numero'] ?? null,
            'pc_presentado_por' => $docData['pc_presentado_por'] ?? null,
            'pc_sintesis' => $docData['pc_sintesis'] ?? null,
            'pc_propuesta' => $docData['pc_propuesta'] ?? null,
            'pc_has_anexo' => $docData['pc_has_anexo'] ?? 'No',
            'pc_asunto_pdf' => $docData['pc_asunto_pdf'] ?? null,
            // Punto de Información MPPT
            'pimppt_numero' => $docData['pimppt_numero'] ?? null,
            'pimppt_numero_corto' => $docData['pimppt_numero_corto'] ?? null,
            'pimppt_asunto' => $docData['pimppt_asunto'] ?? null,
            'pimppt_argumentacion' => $docData['pimppt_argumentacion'] ?? null,
            'pimppt_recomendacion' => $docData['pimppt_recomendacion'] ?? null,
            'pimppt_codigo_pie' => $docData['pimppt_codigo_pie'] ?? null,
            // Punto de Cuenta MPPT
            'pcmppt_numero' => $docData['pcmppt_numero'] ?? null,
            'pcmppt_numero_corto' => $docData['pcmppt_numero_corto'] ?? null,
            'pcmppt_asunto' => $docData['pcmppt_asunto'] ?? null,
            'pcmppt_argumentacion' => $docData['pcmppt_argumentacion'] ?? null,
            'pcmppt_propuesta' => $docData['pcmppt_propuesta'] ?? null,
            'pcmppt_codigo_pie' => $docData['pcmppt_codigo_pie'] ?? null,
            // Punto de Cuenta Directorio
            'pcd_numero' => $docData['pcd_numero'] ?? null,
            'pcd_presentado_por' => $docData['pcd_presentado_por'] ?? null,
            'pcd_presentado_por_cargo' => $docData['pcd_presentado_por_cargo'] ?? null,
            'pcd_sintesis' => $docData['pcd_sintesis'] ?? null,
            'pcd_propuesta' => $docData['pcd_propuesta'] ?? null,
            'pcd_has_anexo' => $docData['pcd_has_anexo'] ?? 'No',
            'pcd_asunto_pdf' => $docData['pcd_asunto_pdf'] ?? null,
            'pcd_revisado_nombre' => $docData['pcd_revisado_nombre'] ?? null,
            'pcd_revisado_cargo' => $docData['pcd_revisado_cargo'] ?? null,
            'pcd_aprobado_nombre' => $docData['pcd_aprobado_nombre'] ?? null,
            'pcd_aprobado_cargo' => $docData['pcd_aprobado_cargo'] ?? null,
            'presentante_cargo' => $docData['presentante_cargo'] ?? null,
            // Minuta
            'minuta_fecha' => $docData['minuta_fecha'] ?? null,
            'minuta_facilitador' => $docData['minuta_facilitador'] ?? null,
            'minuta_dependencia' => $docData['minuta_dependencia'] ?? null,
            'minuta_presentado_a' => $docData['minuta_presentado_a'] ?? null,
            'minuta_presentado_a_cargo' => $docData['minuta_presentado_a_cargo'] ?? null,
            'minuta_elaborado' => $docData['minuta_elaborado'] ?? null,
            'minuta_elaborado_cargo' => $docData['minuta_elaborado_cargo'] ?? null,
            'minuta_revisado' => $docData['minuta_revisado'] ?? null,
            'minuta_revisado_cargo' => $docData['minuta_revisado_cargo'] ?? null,
            'minuta_anexos' => $docData['minuta_anexos'] ?? 'NO',
            'minuta_puntos' => $docData['minuta_puntos'] ?? [],
            'minuta_participantes' => $docData['minuta_participantes'] ?? [],
            'minuta_planteamientos' => $docData['minuta_planteamientos'] ?? [],
            'minuta_acuerdos' => $docData['minuta_acuerdos'] ?? [],
            'minuta_tareas' => $docData['minuta_tareas'] ?? [],
            'firmante_nombre' => $docData['firmante_nombre'] ?? null,
            'firmante_cargo' => $docData['firmante_cargo'] ?? null,
        ];

        $pdfView = match($docData['type']) {
            'Agenda al Decisor' => 'pdf.agenda-decisor',
            'MEMORANDO' => 'pdf.memorando',
            'Circular' => 'pdf.circular',
            'Oficio - Tipo Carta' => 'pdf.oficio-carta',
            'Oficio - Tipo Oficio' => 'pdf.oficio-oficio',
            'Punto de Información - Presidencia IPOSTEL' => 'pdf.punto-informacion',
            'Punto de Cuenta - Presidencia IPOSTEL' => 'pdf.punto-de-cuenta',
            'Punto-de-Cuenta-MPPT' => 'pdf.punto-de-cuenta-mppt',
            'Punto-de-Informacion-MPPT' => 'pdf.punto-de-informacion-mppt',
            'Punto de Cuenta - Directorio' => 'pdf.punto-de-cuenta-directorio',
            'Minuta Horizontal' => 'pdf.minuta',
            default => 'pdf.agenda-decisor'
        };

        $paper = ($docData['type'] === 'Oficio - Tipo Oficio') ? 'legal' : 'letter';
        $pdfContent = Pdf::loadView($pdfView, ['data' => $pdfData])->setPaper($paper)->output();
        return 'data:application/pdf;base64,' . base64_encode($pdfContent);
    }

    public function close_pdf_preview()
    {
        $this->show_pdf_preview = false;
        $this->pdf_url = '';
    }

    private function findDocByDbId(int $dbId): ?array
    {
        $doc = collect($this->comunicaciones)->first(fn($c) => ($c['db_id'] ?? null) == $dbId)
            ?? collect($this->comunicaciones_recientes)->first(fn($c) => ($c['db_id'] ?? null) == $dbId)
            ?? collect($this->comunicaciones_seguimiento)->first(fn($c) => ($c['db_id'] ?? null) == $dbId);

        if (!$doc && !empty($this->active_doc['db_id']) && $this->active_doc['db_id'] == $dbId) {
            $doc = $this->active_doc;
        }

        return $doc ? (array) $doc : null;
    }

    private function get_fecha_letras($date)
    {
        try {
            $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            $dObj = Carbon::createFromFormat('d/m/Y', $date);
            return $dObj->format('d') . ' de ' . $meses[$dObj->format('n') - 1] . ' de ' . $dObj->format('Y');
        } catch (\Exception $e) {
            return $date;
        }
    }

    private function get_anexos_string($docData)
    {
        $nombres = [];
        
        // 1. Si ya tiene adjuntos reales (de la base de datos)
        if (!empty($docData['adjuntos'])) {
            foreach ($docData['adjuntos'] as $adj) {
                $nombres[] = is_array($adj) ? ($adj['nombre_original'] ?? 'Adjunto') : $adj->nombre_original;
            }
        }
        
        // 2. Si tiene adjuntos en sesión (temporales de Livewire)
        if (!empty($this->adjuntos)) {
            foreach ($this->adjuntos as $adj) {
                $nombres[] = $adj->getClientOriginalName();
            }
        }

        return !empty($nombres) ? implode(', ', $nombres) : 'N/A';
    }

    public function download_pdf($id)
    {
        $doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
        if (!$doc) return;
        $doc = (array) $doc;

        $this->dispatch('toastr', ['type' => 'info', 'message' => 'Generando y descargando documento ' . $id]);
        
        $pdfData = [
            'id' => $doc['id'],
            'type' => $doc['type'],
            'subject' => $doc['subject'] ?? 'S/N',
            'sender' => $doc['sender'] ?? 'S/N',
            'destinatario' => $doc['destinatario'] ?? 'S/N',
            'cuerpo' => $doc['cuerpo'] ?? 'Sin contenido disponible.',
            'date' => $doc['date'] ?? now()->format('d/m/Y'),
            // Campos Agenda al Decisor
            'codigo_control' => $doc['codigo_control'] ?? null,
            'agenda_numero' => $doc['agenda_numero'] ?? null,
            'presentante' => $doc['presentante'] ?? null,
            'secuencia' => $doc['secuencia'] ?? null,
            'cuerpo_resumen' => $doc['cuerpo_resumen'] ?? null,
            'cuerpo_propuesta' => $doc['cuerpo_propuesta'] ?? null,
            'texto_asunto' => $doc['texto_asunto'] ?? null,
            // Memorando
            'memo_correlativo' => $doc['memo_correlativo'] ?? null,
            'memo_para_nombre' => $doc['memo_para_nombre'] ?? null,
            'memo_para_cargo' => $doc['memo_para_cargo'] ?? null,
            'memo_de_nombre' => $doc['memo_de_nombre'] ?? null,
            'memo_de_cargo' => $doc['memo_de_cargo'] ?? null,
            'memo_accion' => $doc['memo_accion'] ?? null,
            'memo_cuerpo_detalle' => $doc['memo_cuerpo_detalle'] ?? null,
            // Circular
            'circular_numero' => $doc['circular_numero'] ?? null,
            'circular_titulo' => $doc['circular_titulo'] ?? null,
            'circular_accion' => $doc['circular_accion'] ?? null,
            'circular_contenido' => $doc['circular_contenido'] ?? null,
            'circular_visado' => $doc['circular_visado'] ?? null,
            'circular_cargo' => $doc['circular_cargo'] ?? null,
            // Oficio - Tipo Carta / Oficio
            'oficio_carta_numero' => $doc['oficio_carta_numero'] ?? null,
            'oficio_carta_para_nombre' => $doc['oficio_carta_para_nombre'] ?? null,
            'oficio_carta_para_cargo' => $doc['oficio_carta_para_cargo'] ?? null,
            'oficio_carta_atencion' => $doc['oficio_carta_atencion'] ?? null,
            'oficio_carta_accion' => $doc['oficio_carta_accion'] ?? 'notificarle',
            'oficio_carta_contenido' => $doc['oficio_carta_contenido'] ?? null,
            'oficio_carta_visado_part' => $doc['oficio_carta_visado_part'] ?? 'OP/XX',
            'oficio_carta_anexos_lista' => $this->get_anexos_string($doc),
            'oficio_carta_cc' => $doc['oficio_carta_cc'] ?? null,
            'memo_anexos_lista' => $this->get_anexos_string($doc),
            // Punto de Información
            'pi_numero' => $doc['pi_numero'] ?? null,
            'pi_presentado_por' => $doc['pi_presentado_por'] ?? null,
            'pi_sintesis' => $doc['pi_sintesis'] ?? null,
            'pi_recomendaciones' => $doc['pi_recomendaciones'] ?? null,
            'pi_has_anexo' => $doc['pi_has_anexo'] ?? 'No',
            'pi_asunto_pdf' => $doc['pi_asunto_pdf'] ?? null,
            // Punto de Cuenta
            'pc_numero' => $doc['pc_numero'] ?? null,
            'pc_presentado_por' => $doc['pc_presentado_por'] ?? null,
            'pc_sintesis' => $doc['pc_sintesis'] ?? null,
            'pc_propuesta' => $doc['pc_propuesta'] ?? null,
            'pc_has_anexo' => $doc['pc_has_anexo'] ?? 'No',
            'pc_asunto_pdf' => $doc['pc_asunto_pdf'] ?? null,
            // Punto de Información MPPT
            'pimppt_numero' => $doc['pimppt_numero'] ?? null,
            'pimppt_numero_corto' => $doc['pimppt_numero_corto'] ?? null,
            'pimppt_asunto' => $doc['pimppt_asunto'] ?? null,
            'pimppt_argumentacion' => $doc['pimppt_argumentacion'] ?? null,
            'pimppt_recomendacion' => $doc['pimppt_recomendacion'] ?? null,
            'pimppt_codigo_pie' => $doc['pimppt_codigo_pie'] ?? null,
            // Punto de Cuenta MPPT
            'pcmppt_numero' => $doc['pcmppt_numero'] ?? null,
            'pcmppt_numero_corto' => $doc['pcmppt_numero_corto'] ?? null,
            'pcmppt_asunto' => $doc['pcmppt_asunto'] ?? null,
            'pcmppt_argumentacion' => $doc['pcmppt_argumentacion'] ?? null,
            'pcmppt_propuesta' => $doc['pcmppt_propuesta'] ?? null,
            'pcmppt_codigo_pie' => $doc['pcmppt_codigo_pie'] ?? null,
            // Punto de Cuenta Directorio
            'pcd_numero' => $doc['pcd_numero'] ?? null,
            'pcd_presentado_por' => $doc['pcd_presentado_por'] ?? null,
            'pcd_presentado_por_cargo' => $doc['pcd_presentado_por_cargo'] ?? null,
            'pcd_sintesis' => $doc['pcd_sintesis'] ?? null,
            'pcd_propuesta' => $doc['pcd_propuesta'] ?? null,
            'pcd_has_anexo' => $doc['pcd_has_anexo'] ?? 'No',
            'pcd_asunto_pdf' => $doc['pcd_asunto_pdf'] ?? null,
            'presentante_cargo' => $doc['presentante_cargo'] ?? null,
            // Minuta
            'minuta_fecha' => $doc['minuta_fecha'] ?? null,
            'minuta_facilitador' => $doc['minuta_facilitador'] ?? null,
            'minuta_dependencia' => $doc['minuta_dependencia'] ?? null,
            'minuta_presentado_a' => $doc['minuta_presentado_a'] ?? null,
            'minuta_presentado_a_cargo' => $doc['minuta_presentado_a_cargo'] ?? null,
            'minuta_elaborado' => $doc['minuta_elaborado'] ?? null,
            'minuta_elaborado_cargo' => $doc['minuta_elaborado_cargo'] ?? null,
            'minuta_revisado' => $doc['minuta_revisado'] ?? null,
            'minuta_revisado_cargo' => $doc['minuta_revisado_cargo'] ?? null,
            'minuta_anexos' => $doc['minuta_anexos'] ?? 'NO',
            'minuta_puntos' => $doc['minuta_puntos'] ?? [],
            'minuta_participantes' => $doc['minuta_participantes'] ?? [],
            'minuta_planteamientos' => $doc['minuta_planteamientos'] ?? [],
            'minuta_acuerdos' => $doc['minuta_acuerdos'] ?? [],
            'minuta_tareas' => $doc['minuta_tareas'] ?? [],
            'firmante_nombre' => $doc['firmante_nombre'] ?? null,
            'firmante_cargo' => $doc['firmante_cargo'] ?? null,
        ];

        $pdfView = match($doc['type']) {
            'Agenda al Decisor' => 'pdf.agenda-decisor',
            'MEMORANDO' => 'pdf.memorando',
            'Circular' => 'pdf.circular',
            'Oficio - Tipo Carta' => 'pdf.oficio-carta',
            'Oficio - Tipo Oficio' => 'pdf.oficio-oficio',
            'Punto de Información - Presidencia IPOSTEL' => 'pdf.punto-informacion',
            'Punto de Cuenta - Presidencia IPOSTEL' => 'pdf.punto-de-cuenta',
            'Punto-de-Cuenta-MPPT' => 'pdf.punto-de-cuenta-mppt',
            'Punto-de-Informacion-MPPT' => 'pdf.punto-de-informacion-mppt',
            'Punto de Cuenta - Directorio' => 'pdf.punto-de-cuenta-directorio',
            'Minuta Horizontal' => 'pdf.minuta',
            default => 'pdf.agenda-decisor'
        };


        $paper = ($doc['type'] === 'Oficio - Tipo Oficio') ? 'legal' : 'letter';
        return response()->streamDownload(function () use ($pdfData, $pdfView, $paper) {
            echo Pdf::loadView($pdfView, ['data' => $pdfData])->setPaper($paper)->output();
        }, str_replace(['/', '\\', ' ', '°', '–', 'º'], ['_', '_', '_', '', '-', ''], $pdfData['id']) . ".pdf");
    }

    public function mount()
    {
        $this->load_destinatarios();
        $this->load_memo_usuarios();
        $this->load_real_communications();
        $this->refresh_notifications();
        $this->refresh_dashboard_stats();
    }

    public function updatedFiltroRol()
    {
        $this->load_destinatarios();
    }

    public function updatedDestinatarioSearch()
    {
        $this->load_destinatarios();
    }

    public function load_destinatarios()
    {
        $userId = auth()->id();
        
        // Jerarquía: Presidente envía a otros Presidentes (Pares) y niveles inferiores
        $allowedRoles = [
            'Presidente Correspondencia',
            'Director Correspondencia',
            'Gerente Correspondencia',
            'Analista Correspondencia',
            'Usuario Correspondencia'
        ];

        $query = User::role($allowedRoles)
            ->where('id', '!=', $userId);

        if ($this->filtro_rol) {
            $query->role($this->filtro_rol);
        }

        if ($this->destinatario_search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->destinatario_search . '%')
                  ->orWhere('email', 'like', '%' . $this->destinatario_search . '%');
            });
        }

        $this->destinatarios_lista = $query->get()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_name' => $user->getRoleNames()->first()
            ];
        })->toArray();
    }

    public function load_memo_usuarios(): void
    {
        $this->memo_usuarios = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%Correspondencia%');
        })->orderBy('name')->get()->map(function($user) {
            return [
                'id'   => $user->id,
                'name' => $user->name,
                'role' => $user->getRoleNames()->first() ?? '',
            ];
        })->toArray();

        // Usuarios finales (destinatario obligatorio para MEMORANDO, Oficio Carta y Minuta).
        // Se carga una sola vez aqui en lugar de re-consultarse en cada render del formulario.
        $this->usuariosFinales = User::role('Usuario Correspondencia')
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(fn($user) => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ])
            ->toArray();
    }

    public function updatedMemoPara($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        if ($user) {
            $this->form_data['memo_para_nombre'] = $user['name'];
            $this->form_data['memo_para_cargo']  = $user['role'];
            $this->form_data['destinatario_final_id'] = $user['id'];
        } else {
            $this->form_data['memo_para_nombre'] = '';
            $this->form_data['memo_para_cargo']  = '';
            $this->form_data['destinatario_final_id'] = '';
        }
    }

    public function updatedMemoDe($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        if ($user) {
            $this->form_data['memo_de_nombre'] = $user['name'];
            $this->form_data['memo_de_cargo']  = $user['role'];
        } else {
            $this->form_data['memo_de_nombre'] = '';
            $this->form_data['memo_de_cargo']  = '';
        }
    }

    public function updatedAgendaPresentante($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        $this->form_data['presentante'] = $user ? $user['name'] : '';
        $this->form_data['presentante_cargo'] = $user ? $user['role'] : '';
    }

    public function updatedAgendaParaSel($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        $this->form_data['agenda_para_nombre'] = $user ? $user['name'] : '';
        $this->form_data['agenda_para_cargo'] = $user ? $user['role'] : '';
        $this->form_data['destinatario_final_id'] = $user ? $user['id'] : null;
    }

    public function updatedAgendaPresentadoSel($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        $this->form_data['presentante'] = $user ? $user['name'] : '';
        $this->form_data['presentante_cargo'] = $user ? $user['role'] : '';
    }

    public function updatedAgendaVerificadoSel($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        $this->form_data['agenda_verificado_nombre'] = $user ? $user['name'] : '';
        $this->form_data['agenda_verificado_cargo'] = $user ? $user['role'] : '';
    }

    public function updatedAgendaAprobadoSel($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        $this->form_data['agenda_aprobado_nombre'] = $user ? $user['name'] : '';
        $this->form_data['agenda_aprobado_cargo'] = $user ? $user['role'] : '';
    }

    public function updatedPcdPresentadoPor($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        if ($user) {
            $this->form_data['pcd_presentado_por'] = $user['name'];
            $this->form_data['pcd_presentado_por_cargo'] = $user['role'];
        } else {
            $this->form_data['pcd_presentado_por'] = '';
            $this->form_data['pcd_presentado_por_cargo'] = '';
        }
    }

    public function updatedPcdRevisadoSel($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        if ($user) {
            $this->form_data['pcd_revisado_nombre'] = $user['name'];
            $this->form_data['pcd_revisado_cargo'] = $user['role'];
        } else {
            $this->form_data['pcd_revisado_nombre'] = '';
            $this->form_data['pcd_revisado_cargo'] = '';
        }
    }

    public function updatedPcdAprobadoSel($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        if ($user) {
            $this->form_data['pcd_aprobado_nombre'] = $user['name'];
            $this->form_data['pcd_aprobado_cargo'] = $user['role'];
        } else {
            $this->form_data['pcd_aprobado_nombre'] = '';
            $this->form_data['pcd_aprobado_cargo'] = '';
        }
    }

    public function updatedMinutaElaboradoSel($value): void
    {
        // No usado — elaborado por es automático del creador
    }

    public function updatedMinutaRevisadoSel($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        $this->form_data['minuta_revisado'] = $user ? $user['name'] : '';
        $this->form_data['minuta_revisado_cargo'] = $user ? $user['role'] : '';
    }

    public function updatedMinutaPresentadoASel($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        if ($user) {
            $this->form_data['minuta_presentado_a'] = $user['name'];
            $this->form_data['minuta_presentado_a_cargo'] = $user['role'];
            $this->form_data['destinatario_final_id'] = $user['id'];
        } else {
            $this->form_data['minuta_presentado_a'] = '';
            $this->form_data['minuta_presentado_a_cargo'] = '';
            $this->form_data['destinatario_final_id'] = '';
        }
    }

    public function updatedFirmanteMemSel($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        $this->form_data['firmante_nombre'] = $user ? $user['name'] : '';
        $this->form_data['firmante_cargo']  = $user ? $user['role'] : '';
    }

    public function updatedFirmanteCircSel($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        $this->form_data['firmante_nombre'] = $user ? $user['name'] : '';
        $this->form_data['firmante_cargo']  = $user ? $user['role'] : '';
    }

    public function updatedFirmanteOficioSel($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        $this->form_data['firmante_nombre'] = $user ? $user['name'] : '';
        $this->form_data['firmante_cargo']  = $user ? $user['role'] : '';
    }

    public function updatedOficioPara($value): void
    {
        $user = collect($this->memo_usuarios)->firstWhere('id', (int) $value);
        if ($user) {
            $this->form_data['oficio_carta_para_nombre'] = $user['name'];
            $this->form_data['oficio_carta_para_cargo']  = $user['role'];
            $this->form_data['destinatario_final_id']    = $user['id'];
        } else {
            $this->form_data['oficio_carta_para_nombre'] = '';
            $this->form_data['oficio_carta_para_cargo']  = '';
            $this->form_data['destinatario_final_id']    = '';
        }
    }

    public function select_suggested_destinatario($id, $name, $email)
    {
        $this->destinatario_email = (string) $id;
        $this->destinatario_search = "$name ($email)";
    }

    private function load_real_communications()
    {
        $userId = auth()->id();

        // --- Preparar fechas para el filtro ---
        $start = $this->fecha_inicio ? Carbon::parse($this->fecha_inicio)->startOfDay() : null;
        $end   = $this->fecha_fin   ? Carbon::parse($this->fecha_fin)->endOfDay()       : null;
        if ($start && $end && $start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        // 1. Inbox: Mensajes donde soy el destinatario (excluye mis propios envíos, excepto devueltos para corregir)
        $queryInbox = Comunicado::whereHas('destinatarios_pivot', function ($q) use ($userId, $start, $end) {
            $q->where('usuario_id', $userId);
            if ($start && $end) $q->whereBetween('created_at', [$start, $end]);
        })
        ->where(function ($q) use ($userId) {
            $q->where('remitente_id', '!=', $userId)
              ->orWhereHas('destinatarios_pivot', function ($sub) use ($userId) {
                  $sub->where('usuario_id', $userId)->where('motivo_id', 7);
              });
        });

        if ($this->filtro_prioridad) {
            $queryInbox->where('prioridad', $this->filtro_prioridad);
        }

        $inbox_docs = $queryInbox->orderBy('created_at', 'desc')->get();

        $this->comunicaciones = $inbox_docs->map(function ($doc) use ($userId) {
            return $doc->toComponentArray($userId);
        })->toArray();

        // --- Aplicar Filtro de Estatus (Post-procesamiento) ---
        if ($this->filtro_estatus) {
            $this->comunicaciones = collect($this->comunicaciones)
                ->where('status', $this->filtro_estatus)
                ->values()
                ->toArray();
        }

        // 2. Recent Activity: Mensajes donde soy el remitente
        $sent_docs = Comunicado::where('remitente_id', $userId)
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->orderBy('created_at', 'desc')
            ->get();

        $this->comunicaciones_recientes = $sent_docs->map(function ($doc) use ($userId) {
            return $doc->toComponentArray($userId);
        })->toArray();

        // 3. Seguimiento: Mensajes enviados/recibidos marcados para seguimiento
        $seguimiento_docs = Comunicado::where(function($q) use ($userId) {
                $q->where('remitente_id', $userId)
                  ->orWhereHas('destinatarios_pivot', function($sq) use ($userId) {
                      $sq->where('usuario_id', $userId);
                  });
            })
            ->where('seguimiento', true)
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->orderBy('created_at', 'desc')
            ->get();

        $this->comunicaciones_seguimiento = $seguimiento_docs->map(function ($doc) use ($userId) {
            return $doc->toComponentArray($userId);
        })->toArray();
    }

    // --- Acciones de UI ---
    

    public function open_create()
    {
        $this->current_view = 'create';
        $this->selected_doc_type = 'OTRO';
        $this->tipo_bloqueado = true;
        $this->respuesta_a = null;
        $this->tipo_destino = 'Individual';
        $this->destinatario_email = '';
        $this->destinatario_search = '';
        $this->memoPara = '';
        $this->memoDe = '';
        $this->agendaPresentante = '';
        $this->pcdPresentadoPor = '';
        $this->pcdRevisadoSel = '';
        $this->pcdAprobadoSel = '';
        $this->minutaElaboradoSel = '';
        $this->minutaRevisadoSel = '';
        $this->minutaPresentadoASel = '';
        $this->oficioPara = '';
        $this->firmanteMemSel = '';
        $this->firmanteCircSel = '';
        $this->firmanteOficioSel = '';
        $this->form_data = [
            'remitente' => 'Yo (' . $this->current_role . ')',
            'destinatario' => '',
            'asunto' => '',
            'cuerpo' => '',
            'prioridad' => 'Normal',
            'minuta_fecha' => Carbon::now()->format('Y-m-d'),
            'minuta_facilitador' => '',
            'minuta_dependencia' => '',
            'minuta_presentado_a' => '',
            'minuta_presentado_a_cargo' => '',
            'minuta_elaborado' => auth()->user()->name,
            'minuta_elaborado_cargo' => auth()->user()->getRoleNames()->first() ?? '',
            'minuta_revisado' => '',
            'minuta_revisado_cargo' => '',
            'minuta_anexos' => 'NO',
            'minuta_puntos' => ['', ''],
            'minuta_participantes' => array_fill(0, 5, ['nombre' => '', 'ubicacion' => '', 'correo' => '', 'telefono' => '']),
            'minuta_planteamientos' => array_fill(0, 4, ''),
            'minuta_acuerdos' => array_fill(0, 4, ''),
            'minuta_tareas' => array_fill(0, 4, ['tarea' => '', 'fecha' => '', 'responsable' => '']),
            'agenda_numero' => '',
            'pcd_presentado_por' => auth()->user()->name,
            'pcd_presentado_por_cargo' => auth()->user()->getRoleNames()->first() ?? '',
            'presentante' => auth()->user()->name,
            'presentante_cargo' => auth()->user()->getRoleNames()->first() ?? '',
            'secuencia' => 'Relación',
            'texto_asunto' => '',
            'cuerpo_resumen' => '',
            'cuerpo_propuesta' => '',
            'agenda_has_anexo' => 'No',
            'pi_has_anexo' => 'No',
            'pi_asunto_pdf' => '',
            'pc_has_anexo' => 'No',
            'pc_asunto_pdf' => '',
            'pcd_has_anexo' => 'No',
            'pcd_asunto_pdf' => '',
            'agenda_para_nombre' => 'MSC. OLGA Y. PEREIRA J.',
            'agenda_para_cargo' => 'Presidenta (E) de IPOSTEL',
            'agenda_verificado_nombre' => '',
            'agenda_verificado_cargo' => '',
            'agenda_aprobado_nombre' => '',
            'agenda_aprobado_cargo' => '',
            'circular_titulo' => '',
            'circular_accion' => 'comunica',
            'circular_contenido' => '',
            'circular_visado' => '',
            'circular_cargo' => '',
            'oficio_carta_visado_part' => 'OP/XX',
            'oficio_carta_cc' => '',
            'pi_presentado_por' => '',
            'pcd_revisado_nombre' => '',
            'pcd_revisado_cargo' => '',
            'pcd_aprobado_nombre' => '',
            'pcd_aprobado_cargo' => '',
        ];
    }

    public function open_inbox()
    {
        $this->resetPaginaInbox();
        $this->load_real_communications();
        $this->current_view = 'inbox';
        $this->previous_doc_id = null;
        $this->previous_view = null;
    }

    public function updatedFiltroPrioridad() { $this->resetPaginaInbox(); $this->resetPaginaReciente(); $this->load_real_communications(); }
    public function updatedFiltroEstatus() { $this->resetPaginaInbox(); $this->resetPaginaReciente(); $this->load_real_communications(); }
    public function updatedFechaInicio() { $this->resetPaginaInbox(); $this->resetPaginaReciente(); $this->load_real_communications(); $this->refresh_dashboard_stats(); }
    public function updatedFechaFin() { $this->resetPaginaInbox(); $this->resetPaginaReciente(); $this->load_real_communications(); $this->refresh_dashboard_stats(); }

    public function limpiar_filtros()
    {
        $this->reset(['filtro_prioridad', 'filtro_estatus', 'fecha_inicio', 'fecha_fin']);
        $this->load_real_communications();
    }

    public function open_dashboard()
    {
        $this->current_view = 'dashboard';
        $this->active_doc = null;
        $this->previous_doc_id = null;
        $this->previous_view = null;
    }

    public function open_detail($id)
    {
        foreach ($this->comunicaciones as $key => $com) {
            if ($com['id'] === $id) {
                if (isset($com['es_real']) && $com['es_real']) {
                    $pivot = ComunicadoDestinatario::where('comunicado_id', $com['db_id'])
                        ->where('usuario_id', auth()->id())
                        ->latest('id')
                        ->first();
                    if ($pivot && $pivot->estatus_id == 1) {
                        $pivot->update(['estatus_id' => 2]);
                    }
                } else {
                    if ($com['status'] === 'Pendiente') {
                        $this->comunicaciones[$key]['status'] = 'Leído';
                    }
                }
                
                $this->load_real_communications();
                $this->active_doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
                $this->pdf_url = $this->generate_pdf_url($this->active_doc);
                break;
            }
        }
        $this->current_view = 'detail';
        $this->refresh_notifications();
        $this->refresh_dashboard_stats();
    }

    // Abre el detalle sin cambiar el seguimiento (para comunicados enviados por el usuario)
    public function open_detail_sent($id)
    {
        // Buscar en todas las listas (Recibidos, Enviados y Seguimiento) para asegurar que se encuentre
        $doc = collect($this->comunicaciones)->firstWhere('id', $id) 
            ?? collect($this->comunicaciones_recientes)->firstWhere('id', $id)
            ?? collect($this->comunicaciones_seguimiento)->firstWhere('id', $id);

        if ($doc) {
            $this->active_doc = (array) $doc;
            $this->pdf_url = $this->generate_pdf_url($this->active_doc);
        }
        $this->current_view = 'detail_sent';
    }

    public function open_detail_ref($refCode)
    {
        $doc = collect($this->comunicaciones)->firstWhere('id', $refCode)
            ?? collect($this->comunicaciones_recientes)->firstWhere('id', $refCode);

        if ($doc) {
            // Guardar estado previo para permitir volver
            if ($this->active_doc) {
                $this->previous_doc_id = $this->active_doc['id'];
                $this->previous_view = $this->current_view;
            }

            $this->active_doc = (array) $doc;
            $this->pdf_url = $this->generate_pdf_url($this->active_doc);
            $this->current_view = 'detail_sent';
        }
    }

    public function go_back_to_previous()
    {
        if ($this->previous_doc_id && $this->previous_view) {
            $id = $this->previous_doc_id;
            $view = $this->previous_view;

            // Limpiar antes de navegar para evitar bucles o estados inconsistentes
            $this->previous_doc_id = null;
            $this->previous_view = null;

            if ($view === 'detail') {
                $this->open_detail($id);
            } else {
                $this->open_detail_sent($id);
            }
        } else {
            $this->open_dashboard();
        }
    }

    public function confirmar_recepcion($id)
    {
        $doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
        if ($doc && isset($doc['es_real'])) {
            $pivot = ComunicadoDestinatario::where('comunicado_id', $doc['db_id'])
                ->where('usuario_id', auth()->id())
                ->latest('id')
                ->first();
            if ($pivot) {
                $pivot->update(['estatus_id' => 3]);
                $this->load_real_communications();
                $this->active_doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
                $this->refresh_notifications();
                $this->refresh_dashboard_stats();
                session()->flash('success', 'Confirmación de recibido enviada.');
            }
        }
    }

    public function change_status($id, $new_status)
    {
        // Left for static backward compatibility if needed, not used for dynamic statuses
    }

    public function open_reply($id, $tipoOverride = null)
    {
        $original = (array) collect($this->comunicaciones)->firstWhere('id', $id);
        if ($original) {
            $original = (array) $original;

            // Bloqueo de respuesta si no ha confirmado recepción
            if (isset($original['status']) && $original['status'] !== 'Confirmación de Recibido') {
                $this->dispatch('toastr', ['type' => 'warning', 'message' => 'Debe confirmar la recepción del comunicado antes de poder responderlo.']);
                return;
            }

            $this->open_create();
            $this->selected_doc_type = $tipoOverride ?? $original['type'];
            $this->respuesta_a = $original;
            $this->form_data['destinatario'] = $original['sender'];
            $this->form_data['asunto'] = 'RE: ' . $original['subject'];
            $this->form_data['prioridad'] = $original['priority'];

            // Prioridad: emisor_id (quien me lo envió en este paso) > remitente_id (autor original)
            if (!empty($original['emisor_id']) && $original['emisor_id'] != auth()->id()) {
                $this->destinatario_email = (string) $original['emisor_id'];
            } elseif (isset($original['remitente_id']) && $original['remitente_id'] != auth()->id()) {
                $this->destinatario_email = (string) $original['remitente_id'];
            } elseif (isset($original['destinatario_id'])) {
                $this->destinatario_email = (string) $original['destinatario_id'];
            }

            // Sync searchable string for the reply
            $targetUser = User::find($this->destinatario_email);
            if ($targetUser) {
                $this->destinatario_search = "{$targetUser->name} ({$targetUser->email})";
            }
        }
    }

    protected function rules()
    {
        $nameRegex = 'regex:/^[^0-9]+$/u'; // No permite números

        $rules = [
            'selected_doc_type' => 'required',
            'form_data.asunto'  => 'required|min:5|max:200',
            'form_data.prioridad' => 'required|in:Normal,Alta,Urgente',
            'form_data.fecha_limite' => 'nullable|date|after_or_equal:today',
            'destinatario_email' => ($this->tipo_destino === 'Individual' && !in_array($this->selected_doc_type, ['MEMORANDO'])) ? 'required' : 'nullable',
        ];

        if ($this->selected_doc_type === 'Agenda al Decisor') {
            $rules['form_data.agenda_numero'] = 'required';
            $rules['form_data.presentante'] = ['required', $nameRegex];
            $rules['form_data.secuencia'] = 'required';
            $rules['form_data.texto_asunto'] = 'required|min:10';
            $rules['form_data.cuerpo_resumen'] = 'required|min:10';
            $rules['form_data.cuerpo_propuesta'] = 'required|min:5';
            $rules['form_data.agenda_has_anexo'] = 'required|in:Sí,No';
            $rules['form_data.agenda_para_nombre'] = ['required', $nameRegex];
        } elseif ($this->selected_doc_type === 'MEMORANDO') {
            $rules['form_data.memo_para_nombre'] = ['required', $nameRegex];
            $rules['form_data.memo_para_cargo'] = ['required', $nameRegex];
            $rules['form_data.memo_de_nombre'] = ['required', $nameRegex];
            $rules['form_data.memo_de_cargo'] = ['required', $nameRegex];
            $rules['form_data.memo_accion'] = 'required|in:Solicitar,Remitir';
            $rules['form_data.memo_cuerpo_detalle'] = 'required|min:10';
            $rules['form_data.memo_visado'] = ['required', 'regex:/^[A-Z]{1,5}\/[a-záéíóúñ]{2,}$/u'];
            $rules['form_data.memo_asunto_pdf'] = 'required|min:5';
            $rules['form_data.firmante_nombre'] = ['required', $nameRegex];
        } elseif ($this->selected_doc_type === 'Circular') {
            $rules['form_data.circular_titulo'] = 'required|min:5';
            $rules['form_data.circular_accion'] = 'required|in:comunica,notifica,informa';
            $rules['form_data.circular_contenido'] = 'required|min:10';
            $rules['form_data.circular_visado'] = ['required', 'regex:/^[A-Z]{1,5}\/[a-záéíóúñ]{2,}$/u'];
            $rules['form_data.firmante_nombre'] = ['required', $nameRegex];
        } elseif ($this->selected_doc_type === 'Oficio - Tipo Carta' || $this->selected_doc_type === 'Oficio - Tipo Oficio') {
            $rules['form_data.oficio_carta_para_nombre'] = ['required', $nameRegex];
            $rules['form_data.oficio_carta_para_cargo'] = ['required', $nameRegex];
            $rules['form_data.oficio_carta_entidad'] = 'nullable|string';
            $rules['form_data.oficio_carta_accion'] = 'required|in:notificarle,remitirle,solicitarle';
            $rules['form_data.oficio_carta_contenido'] = 'required|min:10';
            $rules['form_data.oficio_carta_visado_part'] = ['required', 'regex:/^[A-Z]{1,5}\/[a-záéíóúñ]{2,}$/u'];
            $rules['form_data.oficio_carta_cc'] = 'nullable|string';
            $rules['form_data.firmante_nombre'] = ['required', $nameRegex];
        } elseif ($this->selected_doc_type === 'Punto de Información - Presidencia IPOSTEL') {
            $rules['form_data.pi_presentado_por'] = ['required', $nameRegex];
            $rules['form_data.pi_sintesis'] = 'required|min:10';
            $rules['form_data.pi_recomendaciones'] = 'required|min:10';
            $rules['form_data.pi_has_anexo'] = 'required|in:Sí,No';
            $rules['form_data.pi_asunto_pdf'] = 'required|min:5';
        } elseif ($this->selected_doc_type === 'Punto de Cuenta - Presidencia IPOSTEL') {
            $rules['form_data.pc_presentado_por'] = ['required', $nameRegex];
            $rules['form_data.pc_sintesis'] = 'required|min:10';
            $rules['form_data.pc_propuesta'] = 'required|min:10';
            $rules['form_data.pc_has_anexo'] = 'required|in:Sí,No';
            $rules['form_data.pc_asunto_pdf'] = 'required|min:5';
        } elseif ($this->selected_doc_type === 'Punto de Cuenta - Directorio') {
            $rules['form_data.pcd_presentado_por'] = ['required', $nameRegex];
            $rules['form_data.pcd_sintesis'] = 'required|min:10';
            $rules['form_data.pcd_propuesta'] = 'required|min:10';
            $rules['form_data.pcd_has_anexo'] = 'required|in:Sí,No';
            $rules['form_data.pcd_asunto_pdf'] = 'required|min:5';
        } elseif ($this->selected_doc_type === 'Minuta Horizontal') {
            $rules['form_data.minuta_fecha'] = 'required|date';
            $rules['form_data.minuta_facilitador'] = ['required', $nameRegex];
            $rules['form_data.minuta_dependencia'] = 'required';
            $rules['form_data.minuta_presentado_a'] = ['required', $nameRegex];
            $rules['form_data.minuta_elaborado'] = ['required', $nameRegex];
            $rules['form_data.minuta_revisado'] = ['required', $nameRegex];
            foreach ($this->form_data['minuta_puntos'] as $i => $v) $rules["form_data.minuta_puntos.{$i}"] = 'required';
            foreach ($this->form_data['minuta_participantes'] as $i => $p) {
                $rules["form_data.minuta_participantes.{$i}.nombre"] = ['required', $nameRegex];
                $rules["form_data.minuta_participantes.{$i}.ubicacion"] = 'required';
                $rules["form_data.minuta_participantes.{$i}.correo"] = 'required|email';
                $rules["form_data.minuta_participantes.{$i}.telefono"] = 'required';
            }
            foreach ($this->form_data['minuta_planteamientos'] as $i => $v) $rules["form_data.minuta_planteamientos.{$i}"] = 'required';
            foreach ($this->form_data['minuta_acuerdos'] as $i => $v) $rules["form_data.minuta_acuerdos.{$i}"] = 'required';
            foreach ($this->form_data['minuta_tareas'] as $i => $t) {
                $rules["form_data.minuta_tareas.{$i}.tarea"] = 'required';
                $rules["form_data.minuta_tareas.{$i}.fecha"] = 'required|date';
                $rules["form_data.minuta_tareas.{$i}.responsable"] = ['required', $nameRegex];
            }
        } else {
            $rules['form_data.cuerpo'] = 'required|min:10';
        }

        return $rules;
    }

    protected function validationAttributes()
    {
        return [
            'selected_doc_type' => 'Tipo de Comunicación',
            'form_data.asunto' => 'Asunto',
            'form_data.prioridad' => 'Prioridad',
            'form_data.fecha_limite' => 'Fecha Límite',
            'destinatario_email' => 'Destinatario',
            'form_data.presentante' => 'Presentante',
            'form_data.agenda_para_nombre' => 'Nombre del destinatario (Agenda)',
            'form_data.memo_para_nombre' => 'Nombre del destinatario',
            'form_data.memo_para_cargo' => 'Cargo del destinatario',
            'form_data.memo_de_nombre' => 'Nombre del remitente',
            'form_data.memo_de_cargo' => 'Cargo del remitente',
            'form_data.memo_visado' => 'Visado',
            'form_data.memo_asunto_pdf' => 'Asunto del PDF',
            'form_data.circular_titulo' => 'Título de la Circular',
            'form_data.oficio_carta_para_nombre' => 'Nombre del destinatario',
            'form_data.oficio_carta_para_cargo' => 'Cargo del destinatario',
            'form_data.oficio_carta_entidad' => 'Entidad',
            'form_data.firmante_nombre' => 'Nombre del Firmante',
            'form_data.firmante_cargo' => 'Cargo del Firmante',
            'form_data.pi_presentado_por' => 'Presentado por',
            'form_data.pc_presentado_por' => 'Presentado por',
            'form_data.pcd_presentado_por' => 'Presentado por',
            'form_data.minuta_facilitador' => 'Facilitador',
            'form_data.minuta_presentado_a' => 'Presentado a',
            'form_data.minuta_elaborado' => 'Elaborado por',
            'form_data.minuta_revisado' => 'Revisado por',

            // Agenda al Decisor
            'form_data.agenda_numero' => 'Agenda N°',
            'form_data.secuencia' => 'Secuencia',
            'form_data.texto_asunto' => 'Texto del Asunto',
            'form_data.cuerpo_resumen' => 'Resumen del Asunto',
            'form_data.cuerpo_propuesta' => 'Propuesta Conclusiva',
            'form_data.agenda_has_anexo' => 'Anexos',

            // Memorando
            'form_data.memo_accion' => 'Acción del Memorando',
            'form_data.memo_cuerpo_detalle' => 'Cuerpo / Detalle Técnico',

            // Circular
            'form_data.circular_accion' => 'Acción de la Circular',
            'form_data.circular_contenido' => 'Contenido de la Circular',
            'form_data.circular_visado' => 'Visado',

            // Oficio
            'form_data.oficio_carta_accion' => 'Acción Principal',
            'form_data.oficio_carta_contenido' => 'Contenido del Asunto',
            'form_data.oficio_carta_visado_part' => 'Visado',
            'form_data.oficio_carta_cc' => 'Con copia a (c.c.)',

            // Punto de Información
            'form_data.pi_sintesis' => 'Síntesis',
            'form_data.pi_recomendaciones' => 'Recomendaciones',
            'form_data.pi_has_anexo' => 'Anexos',
            'form_data.pi_asunto_pdf' => 'Asunto del PDF',

            // Punto de Cuenta - Presidencia
            'form_data.pc_sintesis' => 'Síntesis',
            'form_data.pc_propuesta' => 'Propuesta',
            'form_data.pc_has_anexo' => 'Anexos',
            'form_data.pc_asunto_pdf' => 'Asunto del PDF',

            // Punto de Cuenta - Directorio
            'form_data.pcd_sintesis' => 'Síntesis',
            'form_data.pcd_propuesta' => 'Propuesta',
            'form_data.pcd_has_anexo' => 'Anexos',
            'form_data.pcd_asunto_pdf' => 'Asunto del PDF',

            // Minuta Horizontal
            'form_data.minuta_fecha' => 'Fecha de Reunión',
            'form_data.minuta_dependencia' => 'Dependencia',

            // Cuerpo genérico (Indicaciones)
            'form_data.cuerpo' => 'Cuerpo del Comunicado',
        ];
    }

    protected function messages()
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'max' => 'El campo :attribute no puede exceder los :max caracteres.',
            'regex' => 'El campo :attribute tiene un formato inválido o contiene números no permitidos.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'after_or_equal' => 'La :attribute debe ser hoy o una fecha futura.',
            'email' => 'El campo :attribute debe ser un correo electrónico válido.',
            'selected_doc_type.required' => 'Debe seleccionar un tipo de documento formal.',
        ];
    }

    public function save_document()
    {
        $this->validate();

        $tiposConParaPropio = ['MEMORANDO'];
        $destinatario_id = null;
        if (is_numeric($this->destinatario_email)) {
            $destinatario_id = (int)$this->destinatario_email;
        } elseif (in_array($this->selected_doc_type, $tiposConParaPropio) && !empty($this->form_data['destinatario_final_id'])) {
            $destinatario_id = (int)$this->form_data['destinatario_final_id'];
        }
        
        // Fallback automático para Agenda y PCD si es una respuesta (firma)
        if (!$destinatario_id && in_array($this->selected_doc_type, ['Agenda al Decisor', 'Punto de Cuenta - Directorio']) && $this->respuesta_a) {
            $destinatario_id = $this->respuesta_a['emisor_id'] ?? $this->respuesta_a['remitente_id'] ?? null;
        }

        if (!$destinatario_id && $this->tipo_destino === 'Individual') {
             $this->dispatch('toastr', ['type' => 'error', 'message' => 'Debe seleccionar un destinatario para enviar el documento.']);
             return;
        }

        // --- Generar código de control automático ---
        $controlData = Comunicado::generateControlCode($this->selected_doc_type);

        $new_codigo = $controlData['n_control'];

        $datos_json = [
            'sender' => auth()->user()->name,
            'sender_name' => auth()->user()->name,
            'sender_cargo' => 'Presidente de IPOSTEL',
            'referencia' => $this->respuesta_a ? ($this->respuesta_a['id'] ?? null) : null,
            'destinatario_final_id' => !empty($this->form_data['destinatario_final_id']) ? $this->form_data['destinatario_final_id'] : null,
            'historial' => [
                [
                    'paso' => 'Creado y Enviado',
                    'actor' => 'Yo (' . $this->current_role . ')',
                    'email' => 'presidencia@ipostel.gob.ve',
                    'fecha' => Carbon::now()->format('d/m/Y H:i'),
                    'comentario' => 'Inicio de la comunicación oficial.'
                ]
            ],
        ];

        $fields = [
            'asunto', 'cuerpo', 'prioridad', 'fecha_limite', 'destinatario_final_id',
            'agenda_numero', 'presentante', 'presentante_cargo', 'secuencia', 'cuerpo_resumen', 'cuerpo_propuesta', 'texto_asunto', 'agenda_has_anexo',
            'agenda_para_nombre', 'agenda_para_cargo', 'agenda_verificado_nombre', 'agenda_verificado_cargo', 'agenda_aprobado_nombre', 'agenda_aprobado_cargo',
            'memo_para_nombre', 'memo_para_cargo', 'memo_de_nombre', 'memo_de_cargo', 'memo_accion', 'memo_cuerpo_detalle',
            'memo_visado', 'memo_asunto_pdf',
            'circular_titulo', 'circular_accion', 'circular_contenido', 'circular_visado', 'circular_cargo',
            'oficio_carta_para_nombre', 'oficio_carta_para_cargo', 'oficio_carta_entidad', 'oficio_carta_accion', 'oficio_carta_contenido',
            'oficio_carta_visado_part', 'oficio_carta_cc',
            'firmante_nombre', 'firmante_cargo',
            'pi_presentado_por', 'pi_sintesis', 'pi_recomendaciones', 'pi_has_anexo', 'pi_asunto_pdf',
            'pc_presentado_por', 'pc_sintesis', 'pc_propuesta', 'pc_has_anexo', 'pc_asunto_pdf',
            'pcd_presentado_por', 'pcd_presentado_por_cargo', 'pcd_sintesis', 'pcd_propuesta', 'pcd_has_anexo', 'pcd_asunto_pdf',
            'pcd_revisado_nombre', 'pcd_revisado_cargo', 'pcd_aprobado_nombre', 'pcd_aprobado_cargo',
            'presentante_cargo',
            'minuta_fecha', 'minuta_facilitador', 'minuta_dependencia', 'minuta_presentado_a', 'minuta_presentado_a_cargo', 'minuta_elaborado', 'minuta_elaborado_cargo', 'minuta_revisado', 'minuta_revisado_cargo', 'minuta_anexos',
            'minuta_puntos', 'minuta_participantes', 'minuta_planteamientos', 'minuta_acuerdos', 'minuta_tareas'
        ];

        foreach ($fields as $field) {
            $datos_json[$field] = $this->form_data[$field] ?? null;
        }

        if (isset($datos_json['circular_titulo'])) {
            $datos_json['circular_titulo'] = mb_strtoupper($datos_json['circular_titulo']);
        }

        // Inyectar código de control auto-generado
        $datos_json = array_merge($datos_json, $controlData);

        // Asignar los campos de número para compatibilidad con PDF
        $datos_json['codigo_control'] = $controlData['n_control'];
        $datos_json['circular_numero'] = $controlData['n_control'];
        $datos_json['memo_correlativo'] = $controlData['n_control'];
        $datos_json['oficio_carta_numero'] = $controlData['n_control'];
        $datos_json['pi_numero'] = $controlData['n_control'];
        $datos_json['pc_numero'] = $controlData['n_control'];
        $datos_json['pcd_numero'] = $controlData['n_control'];

        $respuesta_id = null;
        if ($this->respuesta_a && isset($this->respuesta_a['es_real'])) {
            $respuesta_id = $this->respuesta_a['db_id'];
        }

        $comunicado = Comunicado::create([
            'codigo' => $new_codigo,
            'tipo' => $this->selected_doc_type,
            'asunto' => $this->form_data['asunto'],
            'prioridad' => $this->form_data['prioridad'],
            'remitente_id' => auth()->id(),
            'respuesta_comunicado_id' => $respuesta_id,
            'datos_json' => $datos_json,
            'fecha_limite' => !empty($this->form_data['fecha_limite']) ? Carbon::parse($this->form_data['fecha_limite']) : null,
        ]);

        if ($this->tipo_destino === 'Individual' && $destinatario_id) {
            ComunicadoDestinatario::create([
                'comunicado_id' => $comunicado->comunicado_id,
                'usuario_id' => $destinatario_id,
                'estatus_id' => 1,
            ]);
        } elseif ($this->tipo_destino === 'Todos los Roles') {
            $destinos = \App\Models\User::role(['Director Correspondencia', 'Usuario Correspondencia'])->get();
            foreach ($destinos as $user_dest) {
                \App\Models\ComunicadoDestinatario::create([
                    'comunicado_id' => $comunicado->comunicado_id,
                    'usuario_id' => $user_dest->id,
                    'estatus_id' => 1,
                ]);
            }
        }

        // --- Guardar adjuntos ---
        if (!empty($this->adjuntos)) {
            foreach ($this->adjuntos as $adjunto) {
                $path = $adjunto->store('adjuntos', 'public');
                \App\Models\ComunicadoAdjunto::create([
                    'comunicado_id' => $comunicado->comunicado_id,
                    'nombre_original' => $adjunto->getClientOriginalName(),
                    'ruta_archivo' => $path,
                    'tipo_mime' => $adjunto->getMimeType(),
                    'tamano' => $adjunto->getSize(),
                ]);
            }
            $this->adjuntos = [];
        }

        if ($respuesta_id) {
            $pivot_original = ComunicadoDestinatario::where('comunicado_id', $respuesta_id)
                ->where('usuario_id', auth()->id())
                ->latest('id')
                ->first();
            if ($pivot_original) {
                $pivot_original->update(['estatus_id' => 4]);
            }
        }

        $this->load_real_communications();
        $this->refresh_notifications();
        $this->refresh_dashboard_stats();
        $this->current_view = 'dashboard';
        $this->selected_doc_type = '';
        $this->respuesta_a = null;
        $this->dispatch('toastr', ['type' => 'success', 'message' => 'Comunicación enviada exitosamente.']);
    }

    // --- Métodos Auxiliares ---

    private function refresh_notifications()
    {
        $userId = auth()->id();
        $this->notifications_count = ComunicadoDestinatario::where('usuario_id', $userId)
            ->whereIn('estatus_id', [1, 5, 7])
            ->whereNotExists(function ($query) use ($userId) {
                $query->select(\DB::raw(1))
                    ->from('comunicado_destinatario as outgoing')
                    ->whereColumn('outgoing.comunicado_id', 'comunicado_destinatario.comunicado_id')
                    ->where('outgoing.emisor_id', $userId)
                    ->whereColumn('outgoing.id', '>', 'comunicado_destinatario.id');
            })
            ->count();
    }

    public function toggle_seguimiento($dbId)
    {
        $comunicado = Comunicado::find($dbId);
        if ($comunicado && $comunicado->remitente_id === auth()->id()) {
            $comunicado->seguimiento = !$comunicado->seguimiento;
            $comunicado->save();

            $this->load_real_communications();
            $this->refresh_dashboard_stats();

            // Refresh active_doc if currently viewing
            if ($this->active_doc && isset($this->active_doc['db_id']) && $this->active_doc['db_id'] == $dbId) {
                $doc = collect($this->comunicaciones)->firstWhere('db_id', $dbId)
                    ?? collect($this->comunicaciones_recientes)->firstWhere('db_id', $dbId)
                    ?? collect($this->comunicaciones_seguimiento)->firstWhere('db_id', $dbId);
                if ($doc) {
                    $this->active_doc = (array) $doc;
                }
            }

            $msg = $comunicado->seguimiento
                ? 'Comunicado añadido al seguimiento de instrucciones.'
                : 'Comunicado removido del seguimiento de instrucciones.';
            $this->dispatch('toastr', ['type' => 'success', 'message' => $msg]);
        }
    }

    private function refresh_dashboard_stats()
    {
        $userId = auth()->id();
        $enviados = collect($this->comunicaciones_recientes);

        $pendientes  = $enviados->filter(fn($e) => ($e['estatus_id'] ?? 0) == 1)->count();
        $leidos      = $enviados->filter(fn($e) => ($e['estatus_id'] ?? 0) == 2)->count();
        $recibidos   = $enviados->filter(fn($e) => ($e['estatus_id'] ?? 0) == 3)->count();
        $respondidos = $enviados->filter(fn($e) => ($e['estatus_id'] ?? 0) == 4)->count();

        $requieren_firma = collect($this->comunicaciones)->filter(fn($c) => in_array($c['estatus_id'] ?? 0, [1, 5]))->count();

        // Rastrear documentos que el presidente devolvió para corregir
        $devueltos = ComunicadoDestinatario::where('emisor_id', $userId)
            ->where('estatus_id', 7)
            ->with(['comunicado.remitente', 'usuario'])
            ->latest('id')
            ->get()
            ->unique('comunicado_id');

        $seguimiento_correcciones = $devueltos->map(function ($pivot) {
            $comunicado = $pivot->comunicado;
            if (!$comunicado) return null;

            // Buscar el último movimiento de este documento después de la devolución
            $ultimo_pivot = ComunicadoDestinatario::where('comunicado_id', $comunicado->comunicado_id)
                ->where('id', '>', $pivot->id)
                ->with(['usuario', 'estatus'])
                ->latest('id')
                ->first();

            if ($ultimo_pivot) {
                $estado_actual = $ultimo_pivot->estatus?->nombre ?? 'Desconocido';
                $quien_tiene = $ultimo_pivot->usuario?->name ?? 'Desconocido';
                $estatus_id_actual = $ultimo_pivot->estatus_id;
            } else {
                // Nadie ha actuado después de la devolución
                $estado_actual = 'Esperando corrección';
                $quien_tiene = $pivot->usuario?->name ?? 'Desconocido';
                $estatus_id_actual = 7;
            }

            return [
                'db_id' => $comunicado->comunicado_id,
                'codigo' => $comunicado->codigo,
                'asunto' => $comunicado->asunto,
                'tipo' => $comunicado->tipo,
                'creador' => $comunicado->remitente?->name ?? 'Desconocido',
                'devuelto_a' => $pivot->usuario?->name ?? 'Desconocido',
                'fecha_devolucion' => $pivot->created_at?->format('d/m/Y'),
                'observacion' => $pivot->observacion,
                'estado_actual' => $estado_actual,
                'estatus_id_actual' => $estatus_id_actual,
                'quien_tiene' => $quien_tiene,
            ];
        })->filter()->values()->toArray();

        // Distribución por tipo de comunicados recibidos (filtrado por fecha)
        $distribucion_tipos = collect($this->comunicaciones)->groupBy('type')->map(fn($g) => $g->count())->toArray();

        $this->dashboard_stats = [
            'pendientes'       => $pendientes,
            'leidos'           => $leidos,
            'recibidos'        => $recibidos,
            'respondidos'      => $respondidos,
            'requieren_firma'  => $requieren_firma,
            'total_documentos' => $enviados->count(),
            'seguimiento_correcciones' => $seguimiento_correcciones,
            'distribucion_tipos' => $distribucion_tipos,
        ];
        $this->dispatch('chart-refresh');
    }


    public function aprobarYFirmar(int $dbId)
    {
        $doc = $this->findDocByDbId($dbId);

        if (!$doc || empty($doc['db_id'])) {
            $comunicado = \App\Models\Comunicado::find($dbId);
            if (!$comunicado) {
                $this->dispatch('toastr', ['type' => 'error', 'message' => 'Documento no encontrado (ID: ' . $dbId . ').']);
                return;
            }
            $doc = $comunicado->toComponentArray(auth()->id());
        }

        $service = new CorrespondenceFlowService();

        try {
            $service->aprobarYFirmar($doc['db_id'], auth()->id());

            $comunicadoActualizado = \App\Models\Comunicado::find($doc['db_id']);
            if ($comunicadoActualizado) {
                $this->active_doc = $comunicadoActualizado->toComponentArray(auth()->id());
                $this->pdf_url = $this->generate_pdf_url($this->active_doc);
            }

            $this->load_real_communications();
            $this->refresh_notifications();
            $this->refresh_dashboard_stats();

            $tipo = $comunicadoActualizado?->tipo ?? '';
            $datos = $comunicadoActualizado?->datos_json ?? [];

            if (in_array($tipo, self::TIPOS_FINALIZAN_AL_FIRMAR)) {
                // Flujo termina aquí, sin envío posterior
                $this->dispatch('toastr', ['type' => 'success', 'message' => 'Documento firmado correctamente.']);
            } elseif ($tipo === 'Circular') {
                // Abrir modal de selección múltiple
                $this->enviar_circular_db_id = $doc['db_id'];
                $this->circular_destinatarios_seleccionados = [];
                $this->show_enviar_circular_modal = true;
                $this->dispatch('toastr', ['type' => 'success', 'message' => 'Documento firmado. Seleccione los destinatarios de la circular.']);
            } elseif (in_array($tipo, self::TIPOS_DESTINATARIO_FIJO)) {
                // Destinatario fijo — abrir modal de confirmación sin posibilidad de cambiar
                $finalId = $datos['destinatario_final_id'] ?? null;
                if (!$finalId || !($user = User::find($finalId))) {
                    $this->dispatch('toastr', ['type' => 'error', 'message' => 'Este documento no tiene un destinatario final válido asignado.']);
                    return;
                }
                $this->enviar_final_db_id           = $doc['db_id'];
                $this->enviar_final_nombre          = $user->name;
                $this->enviar_final_email           = $user->email;
                $this->enviar_final_seleccionado_id = (string) $user->id;
                $this->enviar_final_fijo            = true;
                $this->show_enviar_final_modal      = true;
                $this->dispatch('toastr', ['type' => 'success', 'message' => 'Documento firmado. Confirme el envío al destinatario final.']);
            } else {
                // Otros tipos: modal editable como antes
                $this->abrirModalEnviarFinal($doc['db_id']);
                $this->dispatch('toastr', ['type' => 'success', 'message' => 'Documento firmado correctamente. Ahora puede enviarlo al destinatario final.']);
            }
        } catch (\Exception $e) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function abrirModalEnviarFinal(int $dbId)
    {
        $comunicado = Comunicado::find($dbId);
        if (!$comunicado) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Documento no encontrado.']);
            return;
        }

        $this->enviar_final_db_id = $dbId;

        $finalId = $comunicado->datos_json['destinatario_final_id'] ?? null;
        if ($finalId && $user = User::find($finalId)) {
            $this->enviar_final_nombre          = $user->name;
            $this->enviar_final_email           = $user->email;
            $this->enviar_final_seleccionado_id = (string) $user->id;
        } else {
            $this->enviar_final_nombre          = '';
            $this->enviar_final_email           = '';
            $this->enviar_final_seleccionado_id = '';
        }

        $this->show_enviar_final_modal = true;
    }

    public function cerrarModalEnviarFinal()
    {
        $this->show_enviar_final_modal      = false;
        $this->enviar_final_db_id           = null;
        $this->enviar_final_nombre          = '';
        $this->enviar_final_email           = '';
        $this->enviar_final_seleccionado_id = '';
        $this->enviar_final_fijo            = false;
    }

    public function cerrarModalCircular()
    {
        $this->show_enviar_circular_modal             = false;
        $this->enviar_circular_db_id                  = null;
        $this->circular_destinatarios_seleccionados   = [];
    }

    public function enviarCircular(int $dbId)
    {
        if (empty($this->circular_destinatarios_seleccionados)) {
            $this->dispatch('toastr', ['type' => 'warning', 'message' => 'Debe seleccionar al menos un destinatario.']);
            return;
        }

        $comunicado = Comunicado::find($dbId);
        if (!$comunicado) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Documento no encontrado.']);
            return;
        }

        $service = new CorrespondenceFlowService();
        $enviados = 0;

        foreach ($this->circular_destinatarios_seleccionados as $userId) {
            $user = User::find($userId);
            if (!$user) continue;
            $service->enviarAlDestinatarioFinalId($dbId, (int) $userId, auth()->id());
            $enviados++;
        }

        $service->mergeDatosJsonPublic($comunicado, ['enviado_a_final' => true]);

        $comunicadoActualizado = Comunicado::find($dbId);
        if ($comunicadoActualizado) {
            $this->active_doc = $comunicadoActualizado->toComponentArray(auth()->id());
        }

        $this->load_real_communications();
        $this->refresh_notifications();
        $this->refresh_dashboard_stats();
        $this->cerrarModalCircular();
        $this->dispatch('toastr', ['type' => 'success', 'message' => "Circular enviada a {$enviados} destinatario(s)."]);
    }

    public function enviarDestinatarioFinal(int $dbId)
    {
        $comunicado = Comunicado::find($dbId);
        if (!$comunicado) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Documento no encontrado.']);
            return;
        }

        // El Presidente puede usar el destinatario original o haber cambiado la selección en el modal
        if (empty($this->enviar_final_seleccionado_id)) {
            $this->dispatch('toastr', ['type' => 'warning', 'message' => 'Debe seleccionar un destinatario final.']);
            return;
        }

        $destinatario = User::find($this->enviar_final_seleccionado_id);
        if (!$destinatario) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'El destinatario seleccionado no existe.']);
            return;
        }

        // Actualizar destinatario_final_id con la selección definitiva del Presidente
        $datos = $comunicado->datos_json ?? [];
        $datos['destinatario_final_id'] = $destinatario->id;
        $comunicado->datos_json = $datos;
        $comunicado->save();

        try {
            (new CorrespondenceFlowService())->enviarAlDestinatarioFinal($dbId, auth()->id());

            $comunicadoActualizado = Comunicado::find($dbId);
            if ($comunicadoActualizado) {
                $this->active_doc = $comunicadoActualizado->toComponentArray(auth()->id());
            }

            $this->load_real_communications();
            $this->refresh_notifications();
            $this->refresh_dashboard_stats();

            $nombre = $destinatario->name;
            $this->cerrarModalEnviarFinal();
            $this->dispatch('toastr', ['type' => 'success', 'message' => 'Documento enviado a ' . $nombre . '.']);
        } catch (\Exception $e) {
            $this->cerrarModalEnviarFinal();
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function delegarInstruccion($id)
    {
        $doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
        if (!$doc || empty($doc['es_real'])) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Documento no encontrado.']);
            return;
        }

        $destinatarioId = (int) $this->destinatario_delegacion;
        if (!$destinatarioId || !User::find($destinatarioId)) {
            $this->dispatch('toastr', ['type' => 'warning', 'message' => 'Seleccione un destinatario válido para delegar.']);
            return;
        }

        try {
            (new CorrespondenceFlowService())->delegar($doc['db_id'], $destinatarioId, auth()->id());
            $this->destinatario_delegacion = '';
            $this->load_real_communications();
            $this->refresh_notifications();
            $this->refresh_dashboard_stats();
            $this->active_doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
            $this->dispatch('toastr', ['type' => 'success', 'message' => 'Instrucción delegada.']);
        } catch (\Exception $e) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Error al delegar: ' . $e->getMessage()]);
        }
    }

    public function negarYArchivar($id)
    {
        $doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
        if (!$doc || empty($doc['es_real'])) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Documento no encontrado.']);
            return;
        }

        try {
            (new CorrespondenceFlowService())->archivar($doc['db_id'], auth()->id());
            $this->load_real_communications();
            $this->refresh_notifications();
            $this->refresh_dashboard_stats();
            $this->active_doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
            $this->dispatch('toastr', ['type' => 'info', 'message' => 'Documento rechazado y archivado.']);
        } catch (\Exception $e) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Error al archivar: ' . $e->getMessage()]);
        }
    }

    public function open_devolver($id)
    {
        $this->devolver_comunicado_id = $id;
        $this->observacion_devolucion = '';
        $this->show_devolver_form = true;
    }

    public function cancel_devolver()
    {
        $this->show_devolver_form = false;
        $this->devolver_comunicado_id = null;
        $this->observacion_devolucion = '';
    }

    public function devolver_para_corregir()
    {
        $this->validate([
            'observacion_devolucion' => 'required|min:5',
        ], [
            'observacion_devolucion.required' => 'Debe indicar el motivo de la devolución.',
            'observacion_devolucion.min' => 'La observación debe tener al menos 5 caracteres.',
        ]);

        $doc = (array) collect($this->comunicaciones)->firstWhere('id', $this->devolver_comunicado_id);
        if (!$doc || empty($doc['es_real'])) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Documento no encontrado.']);
            return;
        }

        // Retroceder en la cadena de remisiones
        $comunicado = Comunicado::find($doc['db_id']);
        if (!$comunicado) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Documento no encontrado.']);
            return;
        }

        $pivot_original = ComunicadoDestinatario::where('comunicado_id', $doc['db_id'])
            ->where('usuario_id', auth()->id())
            ->where(function ($q) {
                $q->whereNull('motivo_id')->orWhere('motivo_id', '!=', 7);
            })
            ->orderBy('id')
            ->first();

        $destinatarioId = $pivot_original?->emisor_id ?? $comunicado->remitente_id;

        if (!$destinatarioId || $destinatarioId == auth()->id()) {
            $destinatarioId = $comunicado->remitente_id;
        }

        if (!$destinatarioId || $destinatarioId == auth()->id()) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'No se pudo determinar a quién devolver el documento.']);
            return;
        }

        try {
            (new CorrespondenceFlowService())->devolver(
                $doc['db_id'],
                $destinatarioId,
                auth()->id(),
                $this->observacion_devolucion
            );

            $this->cancel_devolver();
            $this->load_real_communications();
            $this->refresh_notifications();
            $this->refresh_dashboard_stats();
            $this->active_doc = (array) collect($this->comunicaciones)->firstWhere('id', $this->devolver_comunicado_id)
                ?? $this->active_doc;
            $this->dispatch('toastr', ['type' => 'success', 'message' => 'Documento devuelto para corrección.']);
        } catch (\Exception $e) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function open_detail_from_root($rootCode)
    {
        $comunicado = Comunicado::where('codigo', $rootCode)->first();
        if (!$comunicado) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Documento original no encontrado.']);
            return;
        }

        $this->active_doc = $comunicado->toComponentArray(auth()->id());
        $this->pdf_url = $this->generate_pdf_url($this->active_doc);
        $this->current_view = 'detail';
    }

    public function render()
    {
        return view('livewire.gestion-correspondencia.correspondencia-presidente');
    }
}
