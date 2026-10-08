<?php

namespace App\Livewire\GestionCorrespondencia;

use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Comunicado;
use App\Livewire\GestionCorrespondencia\Concerns\HasPaginacion;

class CorrespondenciaUsuario extends Component
{
    use WithFileUploads, HasPaginacion;

    public $current_view = 'dashboard';
    public $current_role = 'Usuario General';

    public $comunicaciones = [];
    public $comunicaciones_recientes = [];
    public $notifications_count = 0;
    public $active_doc = null;

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
            'codigo_control' => $docData['codigo_control'] ?? null,
            'agenda_numero' => $docData['agenda_numero'] ?? null,
            'presentante' => $docData['presentante'] ?? null,
            'secuencia' => $docData['secuencia'] ?? null,
            'cuerpo_resumen' => $docData['cuerpo_resumen'] ?? null,
            'cuerpo_propuesta' => $docData['cuerpo_propuesta'] ?? null,
            'texto_asunto' => $docData['texto_asunto'] ?? null,
            'agenda_has_anexo' => $docData['agenda_has_anexo'] ?? 'No',
            'agenda_para_nombre' => $docData['agenda_para_nombre'] ?? null,
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
            'circular_numero' => $docData['circular_numero'] ?? null,
            'circular_titulo' => $docData['circular_titulo'] ?? null,
            'circular_accion' => $docData['circular_accion'] ?? null,
            'circular_contenido' => $docData['circular_contenido'] ?? null,
            'circular_visado' => $docData['circular_visado'] ?? null,
            'circular_cargo' => $docData['circular_cargo'] ?? null,
            'oficio_carta_numero' => $docData['oficio_carta_numero'] ?? null,
            'oficio_carta_para_nombre' => $docData['oficio_carta_para_nombre'] ?? null,
            'oficio_carta_para_cargo' => $docData['oficio_carta_para_cargo'] ?? null,
            'oficio_carta_entidad' => $docData['oficio_carta_entidad'] ?? null,
            'oficio_carta_atencion' => $docData['oficio_carta_atencion'] ?? null,
            'oficio_carta_accion' => $docData['oficio_carta_accion'] ?? null,
            'oficio_carta_contenido' => $docData['oficio_carta_contenido'] ?? null,
            'pi_numero' => $docData['pi_numero'] ?? null,
            'pi_presentado_por' => $docData['pi_presentado_por'] ?? null,
            'pi_sintesis' => $docData['pi_sintesis'] ?? null,
            'pi_recomendaciones' => $docData['pi_recomendaciones'] ?? null,
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
            'pcd_numero' => $docData['pcd_numero'] ?? null,
            'pcd_presentado_por' => $docData['pcd_presentado_por'] ?? null,
            'pcd_sintesis' => $docData['pcd_sintesis'] ?? null,
            'pcd_propuesta' => $docData['pcd_propuesta'] ?? null,
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
            default => null
        };

        if (!$pdfView) {
            return null;
        }

        $paper = ($docData['type'] === 'Oficio - Tipo Oficio') ? 'legal' : 'letter';
        $pdfContent = Pdf::loadView($pdfView, ['data' => $pdfData])->setPaper($paper)->output();
        return 'data:application/pdf;base64,' . base64_encode($pdfContent);
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
        if (!empty($docData['adjuntos'])) {
            foreach ($docData['adjuntos'] as $adj) {
                $nombres[] = is_array($adj) ? ($adj['nombre_original'] ?? 'Adjunto') : $adj->nombre_original;
            }
        }
        return !empty($nombres) ? implode(', ', $nombres) : 'N/A';
    }

    public function close_pdf_preview()
    {
        $this->show_pdf_preview = false;
        $this->pdf_url = '';
    }

    public function download_pdf($id)
    {
        $doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
        if (!$doc) return;

        $this->dispatch('toastr', ['type' => 'info', 'message' => 'Generando y descargando documento ' . $id]);
        
        $docData = $doc;

        $pdfData = [
            'id' => $docData['id'],
            'type' => $docData['type'],
            'subject' => $docData['subject'] ?? 'S/N',
            'sender' => $docData['sender'] ?? 'S/N',
            'destinatario' => $docData['destinatario'] ?? 'S/N',
            'date' => $docData['date'] ?? now()->format('d/m/Y'),
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
        ];

        $pdfView = match($docData['type']) {
            'Minuta Horizontal' => 'pdf.minuta',
            default => 'pdf.comunicacion-oficial'
        };

        return response()->streamDownload(function () use ($pdfData, $pdfView) {
            echo Pdf::loadView($pdfView, ['data' => $pdfData])->setPaper('letter')->output();
        }, str_replace(['/', '\\', ' ', '°', '–', 'º'], ['_', '_', '_', '', '-', ''], $pdfData['id']) . ".pdf");
    }

    public function mount()
    {
        $this->load_real_communications();
        $this->refresh_notifications();
    }

    private function load_real_communications()
    {
        $userId = auth()->id();
        
        $start = $this->fecha_inicio ? Carbon::parse($this->fecha_inicio)->startOfDay() : null;
        $end   = $this->fecha_fin   ? Carbon::parse($this->fecha_fin)->endOfDay()       : null;
        if ($start && $end && $start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        // Inbox: Solo mensajes donde soy el destinatario (excluye mis propios envíos, excepto devueltos para corregir)
        // El filtro de fecha aplica sobre cuándo llegó el pivote al usuario, no sobre created_at del comunicado.
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

        if ($this->filtro_estatus) {
            $this->comunicaciones = collect($this->comunicaciones)
                ->where('status', $this->filtro_estatus)
                ->values()
                ->toArray();
        }
    }

    // --- Navegación ---

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
    }

    public function open_detail_sent($id)
    {
        $doc = collect($this->comunicaciones)->firstWhere('id', $id);

        if ($doc) {
            $this->active_doc = (array) $doc;
            $this->pdf_url = $this->generate_pdf_url($this->active_doc);
        }
        $this->current_view = 'detail_sent';
    }

    public function open_detail_ref($refCode)
    {
        $userId = auth()->id();
        $doc = \App\Models\Comunicado::where('codigo', $refCode)
            ->whereHas('destinatarios_pivot', fn($q) => $q->where('usuario_id', $userId))
            ->first();

        if (!$doc) return;

        $this->previous_doc_id = $this->active_doc['id'] ?? null;
        $this->previous_view = $this->current_view;
        $this->active_doc = $doc->toComponentArray($userId);
        $this->pdf_url = $this->generate_pdf_url($this->active_doc);
        $this->current_view = 'detail';
    }

    public function open_detail_from_root($rootCode)
    {
        $userId = auth()->id();
        $doc = \App\Models\Comunicado::where('codigo', $rootCode)
            ->whereHas('destinatarios_pivot', fn($q) => $q->where('usuario_id', $userId))
            ->first();

        if (!$doc) return;

        $this->previous_doc_id = $this->active_doc['id'] ?? null;
        $this->previous_view = $this->current_view;
        $this->active_doc = $doc->toComponentArray($userId);
        $this->pdf_url = $this->generate_pdf_url($this->active_doc);
        $this->current_view = 'detail';
    }

    public function go_back_to_previous()
    {
        if (!$this->previous_doc_id) {
            $this->open_inbox();
            return;
        }

        $prevId = $this->previous_doc_id;
        $this->previous_doc_id = null;
        $this->previous_view = null;

        $this->load_real_communications();
        $doc = collect($this->comunicaciones)->firstWhere('id', $prevId);

        if ($doc) {
            $this->active_doc = (array) $doc;
            $this->pdf_url = $this->generate_pdf_url($this->active_doc);
            $this->current_view = 'detail';
        } else {
            $this->open_inbox();
        }
    }


    public function completar_comunicado($id)
    {
        $doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
        if (!$doc || !isset($doc['es_real'])) return;

        $pivot = \App\Models\ComunicadoDestinatario::where('comunicado_id', $doc['db_id'])
            ->where('usuario_id', auth()->id())
            ->latest('id')
            ->first();

        if ($pivot && $pivot->estatus_id == 3) {
            $pivot->update(['estatus_id' => 9]);
            $this->load_real_communications();
            $this->active_doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
            $this->refresh_notifications();
            $this->dispatch('toastr', ['type' => 'success', 'message' => 'Comunicado marcado como completado.']);
        }
    }

    public function confirmar_recepcion($id)
    {
        $doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
        if ($doc && isset($doc['es_real'])) {
            $pivot = \App\Models\ComunicadoDestinatario::where('comunicado_id', $doc['db_id'])
                ->where('usuario_id', auth()->id())
                ->latest('id')
                ->first();
            if ($pivot) {
                $pivot->update(['estatus_id' => 3]);
                $this->load_real_communications();
                $this->active_doc = (array) collect($this->comunicaciones)->firstWhere('id', $id);
                $this->refresh_notifications();
                $this->dispatch('toastr', ['type' => 'success', 'message' => 'Confirmación de recibido enviada.']);
            }
        }
    }

    // --- Filtros ---

    public function updatedFiltroPrioridad() { $this->load_real_communications(); }
    public function updatedFiltroEstatus() { $this->load_real_communications(); }
    public function updatedFechaInicio() { $this->load_real_communications(); }
    public function updatedFechaFin() { $this->load_real_communications(); }

    public function limpiar_filtros()
    {
        $this->reset(['filtro_prioridad', 'filtro_estatus', 'fecha_inicio', 'fecha_fin']);
        $this->load_real_communications();
    }

    public function render()
    {
        return view('livewire.gestion-correspondencia.correspondencia-usuario');
    }

    private function refresh_notifications()
    {
        $userId = auth()->id();
        $this->notifications_count = \App\Models\ComunicadoDestinatario::where('usuario_id', $userId)
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
}
