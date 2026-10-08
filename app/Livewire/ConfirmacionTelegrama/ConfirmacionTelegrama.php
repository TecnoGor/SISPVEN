<?php

namespace App\Livewire\ConfirmacionTelegrama;
use App\Models\Envio;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\AvisoTelegrama;
use Livewire\Attributes\Layout;
use App\Exports\TelegramasExport;
use App\Models\TelegramaRecibido;
use App\Models\UsuarioSeguimiento;
use Illuminate\Notifications\DatabaseNotification;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf; // Asegúrate de tener barryvdh/laravel-dompdf instalado

#[Layout('layouts.app')]
class ConfirmacionTelegrama extends Component
{
    use WithPagination;

    public $usuario = [];
    public $desde; 
    public $hasta;
    public $search;
    public $seccion = 1;
    public $page = 10;
    

    public function mount()
    {
        $this->usuario = auth()->user();
    }

    public function cambiar_seccion()
    {
        if($this->seccion == 1){
            $this->seccion = 2;
        }else{
            $this->seccion = 1;
        }
    }

    public function generar_aviso($telegrama)
    {
        $avisos_total = AvisoTelegrama::where('envio_id', $telegrama)->count();

        if($avisos_total >= 10){
            $this->dispatch('alertSuccess2', message: 'Ya se ha llegado al limite de avisos!');
            return;
        }

        AvisoTelegrama::create([
        'oficina_id' => $this->usuario['oficina_id'],
        'usuario_id' => $this->usuario['id'],
        'envio_id' => $telegrama
        ]);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'create',
            'descripcion' => "Se generó aviso de telegrama del envío ({$telegrama})",
        ]);

        $this->dispatch('alertSuccess', message: 'Aviso generado exitosamente!');
    }

    public function confirmar(TelegramaRecibido $telegrama)
    {
        $telegrama->recibido = true;
        $telegrama->save();

        // Marcar leída la notificación del badge correspondiente a este telegrama
        // (identificada por envio_id en su payload), para que el badge quede
        // sincronizado con recibido=true aunque el usuario no haya hecho clic en ella.
        $this->marcarNotificacionLeida($telegrama->envio_id);

        UsuarioSeguimiento::create([
            'usuario_id'  => auth()->user()->id,
            'accion'      => 'update',
            'descripcion' => "Se confirmó la recepción del telegrama del envío ({$telegrama->envio_id})",
        ]);

        $this->dispatch('alertSuccess', message: 'Telegrama confirmado exitosamente!');

        // Avisa al contador de notificaciones (header) para que se reste al instante.
        $this->dispatch('telegrama-confirmado');
    }

    /**
     * Marca como leída la notificación de tipo telegrama asociada a un envío.
     * La notificación pertenece a la oficina destino; se localiza por el envio_id
     * guardado en su payload. El filtrado del payload se hace en PHP porque la
     * columna `data` es `text` en PostgreSQL (no admite operadores JSON directos).
     */
    private function marcarNotificacionLeida($envioId): void
    {
        DatabaseNotification::whereNull('read_at')
            ->get()
            ->filter(function ($notif) use ($envioId) {
                return ($notif->data['tipo'] ?? null) === 'telegrama'
                    && (int) ($notif->data['envio_id'] ?? 0) === (int) $envioId;
            })
            ->each->markAsRead();
    }

    public function reporte_excel()
    {
        if ($this->seccion == 1) {
        $query = Envio::where('servicio_id', 3)
            ->where('oficina_dest_id', $this->usuario['oficina_id']);
            $file = 'telegramas_recibidos_' . now()->format('Y-m-d') . '.xlsx';
    } elseif ($this->seccion == 2) {
        $query = Envio::where('servicio_id', 3)
            ->where('oficina_id', $this->usuario['oficina_id']);
            $file = 'telegramas_emitidos_' . now()->format('Y-m-d') . '.xlsx';
    } else {
        return back()->with('error', 'No hay datos que exportar');
    }

    $telegramas = $query
        ->when($this->desde, function ($q) {
            $q->whereDate('created_at', '>=', $this->desde);
        })
        ->when($this->hasta, function ($q) {
            $q->whereDate('created_at', '<=', $this->hasta);
        })
        ->when($this->search, function ($q) {
            $q->where(function ($subquery) {
                $subquery->where('documento_rem', 'like', '%' . $this->search . '%')
                         ->orWhere('documento_dest', 'like', '%' . $this->search . '%')
                         ->orWhere('codigo_envio', 'like', '%' . $this->search . '%');
            });
        })
        ->orderBy('created_at', 'desc') // igual que en render
        ->get();
    
        return \Maatwebsite\Excel\Facades\Excel::download(new TelegramasExport($telegramas, $this->seccion), $file);
    }

    public function reporte_pdf()
    {
        if ($this->seccion == 1) {
            $query = Envio::where('servicio_id', 3)
                ->where('oficina_dest_id', $this->usuario['oficina_id']);
        } elseif ($this->seccion == 2) {
            $query = Envio::where('servicio_id', 3)
                ->where('oficina_id', $this->usuario['oficina_id']);
        } else {
            $telegramas = collect();
        }

        $telegramas = $query
            ->when($this->desde, function ($q) {
                $q->whereDate('created_at', '>=', $this->desde);
            })
            ->when($this->hasta, function ($q) {
                $q->whereDate('created_at', '<=', $this->hasta);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($subquery) {
                    $subquery->where('documento_rem', 'like', '%' . $this->search . '%')
                             ->orWhere('documento_dest', 'like', '%' . $this->search . '%')
                             ->orWhere('codigo_envio', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $seccion = $this->seccion;

        $pdf = Pdf::loadView('pdf.telegramas', compact('telegramas', 'seccion'));
        return response()->streamDownload(
            fn () => print($pdf->stream()),
            'reporte_telegramas.pdf'
        );
    }

    public function render()
    {
        if ($this->seccion == 1) {
            $query = Envio::where('servicio_id', 3)
                ->where('oficina_dest_id', $this->usuario['oficina_id']);
        } elseif ($this->seccion == 2) {
            $query = Envio::where('servicio_id', 3)
                ->where('oficina_id', $this->usuario['oficina_id']);
        } else {
            $telegramas = collect();
            return view('livewire.confirmacion-telegrama.confirmacion-telegrama', compact('telegramas')); 
        }

        $telegramas = $query
            ->when($this->desde, function ($q) {
                $q->whereDate('created_at', '>=', $this->desde);
            })
            ->when($this->hasta, function ($q) {
                $q->whereDate('created_at', '<=', $this->hasta);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($subquery) {
                    $subquery->where('documento_rem', 'like', '%' . $this->search . '%')
                            ->orWhere('documento_dest', 'like', '%' . $this->search . '%')
                            ->orWhere('codigo_envio', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->page);

        return view('livewire.confirmacion-telegrama.confirmacion-telegrama', compact('telegramas'));
    }
}
