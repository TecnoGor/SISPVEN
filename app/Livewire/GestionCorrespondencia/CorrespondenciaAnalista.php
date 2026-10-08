<?php

namespace App\Livewire\GestionCorrespondencia;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Livewire\GestionCorrespondencia\Concerns\HasPaginacion;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Comunicado;
use App\Models\ComunicadoDestinatario;
use App\Models\AsignacionComunicado;
use App\Models\User;
use App\Services\CorrespondenceFlowService;

class CorrespondenciaAnalista extends Component
{
    use WithFileUploads, HasPaginacion;

    public $current_view = 'dashboard';
    public $current_role = 'Analista';

    public ?int $asignacion_id = null;
    
    public $selected_doc_type = ''; 
    public $destinatario_search = ''; // Para vincular con el input de búsqueda
    public $destinatarios_lista = [];
    public $filtro_rol = '';
    
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
        // MEMORANDO
        'memo_para_nombre' => '',
        'memo_para_cargo' => '',
        'memo_de_nombre' => '',
        'memo_de_cargo' => '',
        'memo_accion' => 'Solicitar',
        'memo_cuerpo_detalle' => '',
        'memo_visado' => '',
        'memo_asunto_pdf' => '',
        'memo_providencia_n' => '',
        'memo_providencia_fecha' => '',
        // Firmante común (Memorando, Circular, Oficios)
        'firmante_nombre' => '',
        'firmante_cargo' => '',
        // Circular
        'circular_titulo' => '',
        'circular_accion' => 'comunica',
        'circular_contenido' => '',
        'circular_visado' => '',
        'circular_cargo' => '',
        // Punto de Cuenta MPPT
        'pcmppt_asunto' => '',
        'pcmppt_argumentacion' => '',
        'pcmppt_propuesta' => '',
        // Punto de Información MPPT
        'pimppt_asunto' => '',
        'pimppt_argumentacion' => '',
        'pimppt_recomendacion' => '',
        // Oficio - Tipo Carta
        'oficio_carta_numero' => '',
        'oficio_carta_para_nombre' => '',
        'oficio_carta_para_cargo' => '',
        'oficio_carta_atencion' => '',
        'oficio_carta_accion' => 'notificarle',
        'oficio_carta_contenido' => '',
        // MINUTA
        'minuta_fecha' => '',
        'minuta_facilitador' => '',
        'minuta_dependencia' => '',
        'minuta_presentado_a' => '',
        'minuta_elaborado' => '',
        'minuta_revisado' => '',
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

    // --- Modo edición para corrección ---
    public ?int $editando_comunicado_id = null;

    public $comunicaciones = [];
    public $comunicaciones_recientes = [];
    public $notifications_count = 0;
    public $active_doc = null;

    public $dashboard_stats = [];
    public $ultimo_correlativo = '';
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
            'date_raw' => $docData['date'] ?? now()->format('d/m/Y'),
            // Campos Agenda al Decisor
            'codigo_control' => $docData['codigo_control'] ?? null,
            'agenda_numero' => $docData['agenda_numero'] ?? null,
            'presentante' => $docData['presentante'] ?? null,
            'secuencia' => $docData['secuencia'] ?? null,
            'cuerpo_resumen' => $docData['cuerpo_resumen'] ?? null,
            'cuerpo_propuesta' => $docData['cuerpo_propuesta'] ?? null,
            'texto_asunto' => $docData['texto_asunto'] ?? null,
            'agenda_has_anexo' => $docData['agenda_has_anexo'] ?? 'No',
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
            'memo_providencia_n' => $docData['memo_providencia_n'] ?? null,
            'memo_providencia_fecha' => $docData['memo_providencia_fecha'] ?? null,
            'memo_anexos_lista' => $this->get_anexos_string($docData),
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
            'oficio_carta_accion' => $docData['oficio_carta_accion'] ?? null,
            'oficio_carta_contenido' => $docData['oficio_carta_contenido'] ?? null,
            // Punto de Información
            'pi_numero' => $docData['pi_numero'] ?? null,
            'pi_presentado_por' => $docData['pi_presentado_por'] ?? null,
            'pi_sintesis' => $docData['pi_sintesis'] ?? null,
            'pi_recomendaciones' => $docData['pi_recomendaciones'] ?? null,
            // Punto de Cuenta
            'pc_numero' => $docData['pc_numero'] ?? null,
            'pc_presentado_por' => $docData['pc_presentado_por'] ?? null,
            'pc_sintesis' => $docData['pc_sintesis'] ?? null,
            'pc_propuesta' => $docData['pc_propuesta'] ?? null,
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
            'pcd_revisado_nombre' => $docData['pcd_revisado_nombre'] ?? null,
            'pcd_revisado_cargo' => $docData['pcd_revisado_cargo'] ?? null,
            'pcd_aprobado_nombre' => $docData['pcd_aprobado_nombre'] ?? null,
            'pcd_aprobado_cargo' => $docData['pcd_aprobado_cargo'] ?? null,
            // Minuta
            'minuta_fecha' => $docData['minuta_fecha'] ?? null,
            'minuta_facilitador' => $docData['minuta_facilitador'] ?? null,
            'minuta_dependencia' => $docData['minuta_dependencia'] ?? null,
            'minuta_presentado_a' => $docData['minuta_presentado_a'] ?? null,
            'minuta_elaborado' => $docData['minuta_elaborado'] ?? null,
            'minuta_revisado' => $docData['minuta_revisado'] ?? null,
            'minuta_anexos' => $docData['minuta_anexos'] ?? 'NO',
            'minuta_puntos' => $docData['minuta_puntos'] ?? [],
            'minuta_participantes' => $docData['minuta_participantes'] ?? [],
            'minuta_planteamientos' => $docData['minuta_planteamientos'] ?? [],
            'minuta_acuerdos' => $docData['minuta_acuerdos'] ?? [],
            'minuta_tareas' => $docData['minuta_tareas'] ?? [],
            // Firmante
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
        ];

        $pdfView = match($pdfData['type']) {
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
            default => 'pdf.agenda-decisor'
        };
    }

    public function mount()
    {
        $this->load_destinatarios();
        $this->load_memo_usuarios();
        $this->load_real_communications();
        $this->refresh_notifications();
        $this->refresh_dashboard_stats();

        if ($asignacionId = request()->query('asignacion')) {
            $this->atender_asignacion((int) $asignacionId);
        }
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
        
        // Jerarquía: Analista envía a Gerente (Arriba), Usuario (Base) y otros Analistas (Pares)
        $allowedRoles = [
            'Gerente Correspondencia',
            'Analista Correspondencia',
            'Usuario Correspondencia'
        ];

        // Se excluyen explícitamente los roles superiores para evitar visibilidad indebida
        $forbiddenRoles = [
            'Presidente Correspondencia',
            'Director Correspondencia'
        ];

        $query = User::role($allowedRoles)
            ->whereDoesntHave('roles', function($q) use ($forbiddenRoles) {
                $q->whereIn('name', $forbiddenRoles);
            })
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

        // 2. Recent Activity: Solo mensajes donde soy el remitente
        $sent_docs = Comunicado::where('remitente_id', $userId)
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->orderBy('created_at', 'desc')
            ->get();

        $this->comunicaciones_recientes = $sent_docs->map(function ($doc) use ($userId) {
            return $doc->toComponentArray($userId);
        })->toArray();
    }

    public function updatedFiltroPrioridad() { $this->resetPaginaInbox(); $this->load_real_communications(); }
    public function updatedFiltroEstatus() { $this->resetPaginaInbox(); $this->load_real_communications(); }
    public function updatedFechaInicio() { $this->resetPaginaInbox(); $this->resetPaginaReciente(); $this->load_real_communications(); $this->refresh_dashboard_stats(); }
    public function updatedFechaFin() { $this->resetPaginaInbox(); $this->resetPaginaReciente(); $this->load_real_communications(); $this->refresh_dashboard_stats(); }

    public function limpiar_filtros()
    {
        $this->reset(['filtro_prioridad', 'filtro_estatus', 'fecha_inicio', 'fecha_fin']);
        $this->load_real_communications();
    }

    public function updated_selected_doc_type($value) {}

    #[On('atender-asignacion')]
    public function atender_asignacion(int $asignacion_id, ?string $tipo_documento_esperado = null, ?string $asunto_sugerido = null): void
    {
        $asignacion = AsignacionComunicado::where('id', $asignacion_id)
            ->where('analista_id', auth()->id())
            ->where('estatus', 'Pendiente')
            ->first();

        if (!$asignacion) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Instrucción no disponible.']);
            return;
        }

        $this->open_create();
        $this->asignacion_id = $asignacion->id;

        $tipo = $tipo_documento_esperado ?? $asignacion->tipo_documento_esperado;
        $asunto = $asunto_sugerido ?? $asignacion->asunto_instruccion;

        if ($tipo) {
            $this->selected_doc_type = $tipo;
        }
        if ($asunto) {
            $this->form_data['asunto'] = $asunto;
        }
    }

    public function open_create()
    {
        $this->current_view = 'create';
        $this->selected_doc_type = '';
        $this->respuesta_a = null;
        $this->asignacion_id = null;
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
            'fecha_limite' => '',
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
            'agenda_para_nombre' => 'MSC. OLGA Y. PEREIRA J.',
            'agenda_para_cargo' => 'Presidenta (E) de IPOSTEL',
            'agenda_verificado_nombre' => '',
            'agenda_verificado_cargo' => '',
            'agenda_aprobado_nombre' => '',
            'agenda_aprobado_cargo' => '',
            'pcd_revisado_nombre' => '',
            'pcd_revisado_cargo' => '',
            'pcd_aprobado_nombre' => '',
            'pcd_aprobado_cargo' => '',
        ];
    }

    public function open_edit_correccion($id)
    {
        $doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
        if (!$doc || empty($doc['es_real'])) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Documento no encontrado.']);
            return;
        }

        // Solo se puede editar si el motivo es "Devuelto" (7) y soy el creador
        if (($doc['motivo_id'] ?? 0) != 7) {
            $this->dispatch('toastr', ['type' => 'warning', 'message' => 'Este documento no está marcado para corrección.']);
            return;
        }

        $comunicado = Comunicado::find($doc['db_id']);
        if (!$comunicado || $comunicado->remitente_id != auth()->id()) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Solo el creador original puede editar este documento.']);
            return;
        }

        $this->editando_comunicado_id = $comunicado->comunicado_id;
        $this->selected_doc_type = $comunicado->tipo;
        $this->current_view = 'create';
        $this->respuesta_a = null;
        $this->tipo_destino = 'Individual';

        // Pre-cargar form_data desde datos_json
        $datos = $comunicado->datos_json ?? [];
        foreach ($this->form_data as $key => $default) {
            if (isset($datos[$key])) {
                $this->form_data[$key] = $datos[$key];
            }
        }

        $this->form_data['asunto'] = $comunicado->asunto;
        $this->form_data['prioridad'] = $comunicado->prioridad;
        $this->form_data['fecha_limite'] = $comunicado->fecha_limite?->format('Y-m-d') ?? '';

        // No pre-cargar destinatario: el analista debe elegir el gerente
        // respetando la jerarquía (Analista → Gerente → Director)
        $this->destinatario_email = '';
        $this->destinatario_search = '';
    }

    public function open_inbox()
    {
        $this->resetPaginaInbox();
        $this->load_real_communications();
        $this->current_view = 'inbox';
        $this->previous_doc_id = null;
        $this->previous_view = null;
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
                if ($com['es_real'] ?? false) {
                    $pivot = \App\Models\ComunicadoDestinatario::where('comunicado_id', $com['db_id'])
                        ->where('usuario_id', auth()->id())
                        ->latest('id')
                        ->first();
                    if ($pivot && $pivot->estatus_id == 1) {
                        $pivot->update(['estatus_id' => 2]);
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
        // Buscar en ambas listas (Recibidos y Enviados) para asegurar que se encuentre
        $doc = collect($this->comunicaciones)->firstWhere('id', $id) 
            ?? collect($this->comunicaciones_recientes)->firstWhere('id', $id);

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

    public function change_status($id, $new_status)
    {
        foreach ($this->comunicaciones as $key => $com) {
            if ($com['id'] === $id) {
                $this->comunicaciones[$key]['status'] = $new_status;
                if ($this->active_doc && $this->active_doc['id'] === $id) {
                    $this->active_doc['status'] = $new_status;
                }
                break;
            }
        }
        $this->refresh_notifications();
        $this->refresh_dashboard_stats();
        session()->flash('success', 'Estatus actualizado a: ' . $new_status);
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
                $this->dispatch('toastr', ['type' => 'success', 'message' => 'Confirmación de recibido enviada.']);
            }
        }
    }

    public function open_reply($id)
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
            $this->selected_doc_type = $original['type'];
            $this->respuesta_a = $original;
            $this->form_data['destinatario'] = $original['sender'];
            $this->form_data['asunto'] = 'RE: ' . $original['subject'];
            $this->form_data['prioridad'] = $original['priority'];

            if (isset($original['remitente_id']) && $original['remitente_id'] != auth()->id()) {
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

        $tiposDestinatarioFijoRequerido = ['MEMORANDO', 'Minuta Horizontal'];
        if (in_array($this->selected_doc_type, $tiposDestinatarioFijoRequerido)) {
            $rules['form_data.destinatario_final_id'] = 'required|integer|exists:users,id';
        }

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
            $rules['form_data.memo_providencia_n'] = 'nullable';
            $rules['form_data.memo_providencia_fecha'] = 'nullable';
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
            $rules['form_data.firmante_nombre'] = ['required', $nameRegex];
        } elseif ($this->selected_doc_type === 'Punto de Información - Presidencia IPOSTEL') {
            $rules['form_data.pi_presentado_por'] = ['required', $nameRegex];
            $rules['form_data.pi_sintesis'] = 'required|min:10';
            $rules['form_data.pi_recomendaciones'] = 'required|min:10';
        } elseif ($this->selected_doc_type === 'Punto de Cuenta - Presidencia IPOSTEL') {
            $rules['form_data.pc_presentado_por'] = ['required', $nameRegex];
            $rules['form_data.pc_sintesis'] = 'required|min:10';
            $rules['form_data.pc_propuesta'] = 'required|min:10';
        } elseif ($this->selected_doc_type === 'Punto-de-Informacion-MPPT') {
            $rules['form_data.pimppt_asunto'] = 'required|min:10';
            $rules['form_data.pimppt_argumentacion'] = 'required|min:10';
            $rules['form_data.pimppt_recomendacion'] = 'required|min:10';
        } elseif ($this->selected_doc_type === 'Punto-de-Cuenta-MPPT') {
            $rules['form_data.pcmppt_asunto'] = 'required|min:10';
            $rules['form_data.pcmppt_argumentacion'] = 'required|min:10';
            $rules['form_data.pcmppt_propuesta'] = 'required|min:10';
        } elseif ($this->selected_doc_type === 'Punto de Cuenta - Directorio') {
            $rules['form_data.pcd_presentado_por'] = ['required', $nameRegex];
            $rules['form_data.pcd_sintesis'] = 'required|min:10';
            $rules['form_data.pcd_propuesta'] = 'required|min:10';
        } elseif ($this->selected_doc_type === 'Minuta Horizontal') {
            $rules['form_data.minuta_fecha'] = 'required|date';
            $rules['form_data.minuta_facilitador'] = ['required', $nameRegex];
            $rules['form_data.minuta_dependencia'] = 'required';
            $rules['form_data.minuta_presentado_a'] = ['required', $nameRegex];
            $rules['form_data.minuta_elaborado'] = ['required', $nameRegex];
            $rules['form_data.minuta_revisado'] = ['required', $nameRegex];
            
            foreach ($this->form_data['minuta_puntos'] as $i => $value) {
                $rules["form_data.minuta_puntos.{$i}"] = 'required';
            }
            
            foreach ($this->form_data['minuta_participantes'] as $i => $p) {
                $rules["form_data.minuta_participantes.{$i}.nombre"] = ['required', $nameRegex];
                $rules["form_data.minuta_participantes.{$i}.ubicacion"] = 'required';
                $rules["form_data.minuta_participantes.{$i}.correo"] = 'required|email';
                $rules["form_data.minuta_participantes.{$i}.telefono"] = 'required';
            }
            
            foreach ($this->form_data['minuta_planteamientos'] as $i => $value) {
                $rules["form_data.minuta_planteamientos.{$i}"] = 'required';
            }
            
            foreach ($this->form_data['minuta_acuerdos'] as $i => $value) {
                $rules["form_data.minuta_acuerdos.{$i}"] = 'required';
            }
            
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
            'form_data.destinatario_final_id' => 'Destinatario Final',
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
            'form_data.pcmppt_asunto' => 'Asunto (MPPT)',
            'form_data.pcmppt_argumentacion' => 'Argumentación (MPPT)',
            'form_data.pcmppt_propuesta' => 'Propuesta (MPPT)',
            'form_data.pimppt_asunto' => 'Asunto (PI-MPPT)',
            'form_data.pimppt_argumentacion' => 'Argumentación (PI-MPPT)',
            'form_data.pimppt_recomendacion' => 'Recomendación (PI-MPPT)',
            'form_data.minuta_facilitador' => 'Facilitador',
            'form_data.minuta_presentado_a' => 'Presentado a',
            'form_data.minuta_elaborado' => 'Elaborado por',
            'form_data.minuta_revisado' => 'Revisado por',
            'form_data.agenda_para_nombre' => 'Para (Nombre)',
            'form_data.agenda_verificado_nombre' => 'Verificado por',
            'form_data.agenda_aprobado_nombre' => 'Aprobado por',

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

        if (!$destinatario_id && $this->tipo_destino === 'Individual' && !$this->editando_comunicado_id) {
             $this->dispatch('toastr', ['type' => 'error', 'message' => 'Debe seleccionar un destinatario para enviar el documento.']);
             return;
        }

        // ── MODO EDICIÓN: Actualizar documento existente (corrección) ──
        if ($this->editando_comunicado_id) {
            $comunicado = Comunicado::find($this->editando_comunicado_id);
            if (!$comunicado || $comunicado->remitente_id != auth()->id()) {
                $this->dispatch('toastr', ['type' => 'error', 'message' => 'No tiene permiso para editar este documento.']);
                return;
            }

            $datos_json = $comunicado->datos_json ?? [];

            $fields = [
                'asunto', 'cuerpo', 'prioridad', 'fecha_limite',
                'agenda_numero', 'presentante', 'presentante_cargo', 'secuencia', 'cuerpo_resumen', 'cuerpo_propuesta', 'texto_asunto', 'agenda_has_anexo',
                'agenda_para_nombre', 'agenda_para_cargo', 'agenda_verificado_nombre', 'agenda_verificado_cargo', 'agenda_aprobado_nombre', 'agenda_aprobado_cargo',
                'memo_para_nombre', 'memo_para_cargo', 'memo_de_nombre', 'memo_de_cargo', 'memo_accion', 'memo_cuerpo_detalle',
                'memo_visado', 'memo_asunto_pdf', 'memo_providencia_n', 'memo_providencia_fecha',
                'circular_titulo', 'circular_accion', 'circular_contenido', 'circular_visado', 'circular_cargo',
                'oficio_carta_para_nombre', 'oficio_carta_para_cargo', 'oficio_carta_atencion', 'oficio_carta_accion', 'oficio_carta_contenido',
                'firmante_nombre', 'firmante_cargo',
                'pi_presentado_por', 'pi_sintesis', 'pi_recomendaciones',
                'pc_presentado_por', 'pc_sintesis', 'pc_propuesta',
                'pcmppt_asunto', 'pcmppt_argumentacion', 'pcmppt_propuesta',
                'pimppt_asunto', 'pimppt_argumentacion', 'pimppt_recomendacion',
                'pcd_presentado_por', 'pcd_presentado_por_cargo', 'pcd_sintesis', 'pcd_propuesta',
                'pcd_revisado_nombre', 'pcd_revisado_cargo', 'pcd_aprobado_nombre', 'pcd_aprobado_cargo',
                'minuta_fecha', 'minuta_facilitador', 'minuta_dependencia', 'minuta_presentado_a', 'minuta_presentado_a_cargo', 'minuta_elaborado', 'minuta_elaborado_cargo', 'minuta_revisado', 'minuta_revisado_cargo', 'minuta_anexos',
                'minuta_puntos', 'minuta_participantes', 'minuta_planteamientos', 'minuta_acuerdos', 'minuta_tareas'
            ];

            foreach ($fields as $field) {
                $datos_json[$field] = $this->form_data[$field] ?? null;
            }

            if (isset($datos_json['circular_titulo'])) {
                $datos_json['circular_titulo'] = mb_strtoupper($datos_json['circular_titulo']);
            }

            $comunicado->update([
                'asunto' => $this->form_data['asunto'],
                'prioridad' => $this->form_data['prioridad'],
                'datos_json' => $datos_json,
                'fecha_limite' => !empty($this->form_data['fecha_limite']) ? Carbon::parse($this->form_data['fecha_limite']) : null,
            ]);

            // Incrementar contador de correcciones
            $comunicado->increment('correcciones');

            // Marcar todos los pivotes de devolución del analista como atendidos
            // para que el comunicado no aparezca más en su bandeja de entrada
            ComunicadoDestinatario::where('comunicado_id', $comunicado->comunicado_id)
                ->where('usuario_id', auth()->id())
                ->where('motivo_id', 7)
                ->update(['estatus_id' => 4, 'motivo_id' => null]);

            // Re-enviar con estatus 1 (Pendiente) para que entre como nuevo en la bandeja
            if ($destinatario_id) {
                (new CorrespondenceFlowService())->remitir(
                    $comunicado->comunicado_id,
                    $destinatario_id,
                    auth()->id(),
                    1
                );
            }

            $this->editando_comunicado_id = null;
            $this->load_real_communications();
            $this->refresh_notifications();
            $this->refresh_dashboard_stats();
            $this->current_view = 'dashboard';
            $this->selected_doc_type = '';
            $this->dispatch('toastr', ['type' => 'success', 'message' => 'Documento corregido y re-enviado exitosamente.']);
            return;
        }

        // ── MODO CREACIÓN: Crear documento nuevo ──

        // --- Generar código de control automático ---
        $controlData = Comunicado::generateControlCode($this->selected_doc_type);
        $new_codigo = $controlData['n_control'];

        $datos_json = [
            'sender' => 'Analista de Correspondencia IPOSTEL',
            'sender_name' => auth()->user()->name,
            'sender_cargo' => 'Analista de Correspondencia',
            'referencia' => $this->respuesta_a ? ($this->respuesta_a['id'] ?? null) : null,
            'destinatario_final_id' => !empty($this->form_data['destinatario_final_id']) ? $this->form_data['destinatario_final_id'] : null,
            'historial' => [
                [
                    'paso' => 'Creado y Enviado',
                    'actor' => 'Yo (' . $this->current_role . ')',
                    'email' => auth()->user()->email,
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
            'memo_visado', 'memo_asunto_pdf', 'memo_providencia_n', 'memo_providencia_fecha',
            'circular_titulo', 'circular_accion', 'circular_contenido', 'circular_visado', 'circular_cargo',
            'oficio_carta_para_nombre', 'oficio_carta_para_cargo', 'oficio_carta_entidad', 'oficio_carta_accion', 'oficio_carta_contenido',
            'firmante_nombre', 'firmante_cargo',
            'pi_presentado_por', 'pi_sintesis', 'pi_recomendaciones',
            'pc_presentado_por', 'pc_sintesis', 'pc_propuesta',
            'pcmppt_asunto', 'pcmppt_argumentacion', 'pcmppt_propuesta',
            'pimppt_asunto', 'pimppt_argumentacion', 'pimppt_recomendacion',
            'pcd_presentado_por', 'pcd_presentado_por_cargo', 'pcd_sintesis', 'pcd_propuesta',
            'pcd_revisado_nombre', 'pcd_revisado_cargo', 'pcd_aprobado_nombre', 'pcd_aprobado_cargo',
            'firmante_nombre', 'firmante_cargo',
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
        $datos_json['pcmppt_numero'] = $controlData['n_control'];
        $datos_json['pimppt_numero'] = $controlData['n_control'];
        $datos_json['pcd_numero'] = $controlData['n_control'];

        // Para MPPT: extraer número corto (3 dígitos) y código pie
        if ($this->selected_doc_type === 'Punto-de-Cuenta-MPPT') {
            $datos_json['pcmppt_numero_corto'] = str_pad($controlData['correlativo_secuencial'], 3, '0', STR_PAD_LEFT);
            $datos_json['pcmppt_codigo_pie'] = 'MPPT-DM-CE-' . str_pad($controlData['correlativo_secuencial'], 3, '0', STR_PAD_LEFT) . '-' . now()->format('d/m/Y');
        }

        // Para PI-MPPT: extraer número corto (3 dígitos) y código pie
        if ($this->selected_doc_type === 'Punto-de-Informacion-MPPT') {
            $datos_json['pimppt_numero_corto'] = str_pad($controlData['correlativo_secuencial'], 3, '0', STR_PAD_LEFT);
            $datos_json['pimppt_codigo_pie'] = 'MPPT-DM-CE-' . str_pad($controlData['correlativo_secuencial'], 3, '0', STR_PAD_LEFT) . '-' . now()->format('d/m/Y');
        }

        $respuesta_id = null;
        if ($this->respuesta_a && isset($this->respuesta_a['db_id'])) {
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
            \App\Models\ComunicadoDestinatario::create([
                'comunicado_id' => $comunicado->comunicado_id,
                'usuario_id' => $destinatario_id,
                'estatus_id' => 1,
            ]);
        } elseif ($this->tipo_destino === 'Todos los Gerentes') {
            $destinos = \App\Models\User::role(['Gerente Correspondencia', 'Usuario Correspondencia'])->get();
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
            $pivot_original = \App\Models\ComunicadoDestinatario::where('comunicado_id', $respuesta_id)
                ->where('usuario_id', auth()->id())
                ->latest('id')
                ->first();
            if ($pivot_original) {
                $pivot_original->update(['estatus_id' => 4]);
            }
        }

        if ($this->asignacion_id) {
            AsignacionComunicado::where('id', $this->asignacion_id)
                ->where('analista_id', auth()->id())
                ->update([
                    'estatus' => 'Completado',
                    'comunicado_generado_id' => $comunicado->comunicado_id,
                ]);
            $this->asignacion_id = null;
            $this->dispatch('asignacion-completada');
        }

        $this->load_real_communications();
        $this->refresh_notifications();
        $this->refresh_dashboard_stats();
        $this->current_view = 'dashboard';
        $this->selected_doc_type = ''; $this->respuesta_a = null;
        $this->dispatch('toastr', ['type' => 'success', 'message' => 'Comunicación enviada exitosamente.']);
    }

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

    private function refresh_dashboard_stats()
    {
        $userId = auth()->id();
        $enviados = collect($this->comunicaciones_recientes);
        $recibidos = collect($this->comunicaciones);

        // 1. Devueltos para corregir (estatus 7)
        $devueltos = $recibidos->filter(fn($c) => ($c['estatus_id'] ?? 0) == 7)->values()->toArray();

        // 2. Rastreador de envíos: últimos documentos enviados y su estado actual
        $rastreo_envios = $enviados->take(10)->map(function ($doc) use ($userId) {
            $ultimo = ComunicadoDestinatario::where('comunicado_id', $doc['db_id'])
                ->with(['usuario.roles', 'estatus'])
                ->latest('id')
                ->first();

            $nivel = 'Desconocido';
            $quien = $ultimo?->usuario?->name ?? 'N/A';
            if ($ultimo && $ultimo->usuario) {
                if ($ultimo->usuario_id == $userId) {
                    $nivel = 'Mi bandeja';
                    $quien = 'Yo';
                } else {
                    $roles = $ultimo->usuario->getRoleNames();
                    if ($roles->contains('Presidente Correspondencia')) $nivel = 'Presidencia';
                    elseif ($roles->contains('Director Correspondencia')) $nivel = 'Director';
                    elseif ($roles->contains('Gerente Correspondencia')) $nivel = 'Gerente';
                    else $nivel = 'Otro';
                }
            }

            return [
                'id' => $doc['id'],
                'db_id' => $doc['db_id'],
                'asunto' => $doc['subject'],
                'tipo' => $doc['type'],
                'fecha' => $doc['date'],
                'nivel' => $nivel,
                'quien' => $quien,
                'estatus' => $ultimo?->estatus?->nombre ?? 'Pendiente',
                'estatus_id' => $ultimo?->estatus_id ?? 1,
            ];
        })->toArray();

        // 3. Documentos creados hoy
        $correlativos_hoy = $enviados->filter(function ($doc) {
            try {
                return Carbon::createFromFormat('d/m/Y', $doc['date'])->isToday();
            } catch (\Exception $e) {
                return false;
            }
        })->count();

        $this->dashboard_stats = [
            'devueltos' => $devueltos,
            'rastreo_envios' => $rastreo_envios,
            'correlativos_hoy' => $correlativos_hoy,
        ];
    }


    public function render()
    {
        return view('livewire.gestion-correspondencia.correspondencia-analista');
    }
}
