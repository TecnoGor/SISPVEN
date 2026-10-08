<?php

namespace App\Livewire\GestionCorrespondencia\Concerns;

trait HasPaginacion
{
    public int $pagina_inbox   = 1;
    public int $por_pagina_inbox   = 10;
    public int $pagina_reciente    = 1;
    public int $por_pagina_reciente = 10;
    public string $busqueda_actividad = '';

    public function updatedPorPaginaInbox(): void
    {
        $this->pagina_inbox = 1;
    }

    public function updatedBusquedaActividad(): void
    {
        $this->pagina_reciente = 1;
    }

    public function updatedPorPaginaReciente(): void
    {
        $this->pagina_reciente = 1;
    }

    public function inbox_pagina(int $pagina): void
    {
        $total = count($this->comunicaciones ?? []);
        $total_paginas = max(1, (int) ceil($total / $this->por_pagina_inbox));
        $this->pagina_inbox = max(1, min($pagina, $total_paginas));
    }

    public function reciente_pagina(int $pagina): void
    {
        $total = count($this->comunicaciones_recientes ?? []);
        $total_paginas = max(1, (int) ceil($total / $this->por_pagina_reciente));
        $this->pagina_reciente = max(1, min($pagina, $total_paginas));
    }

    protected function resetPaginaInbox(): void
    {
        $this->pagina_inbox = 1;
    }

    protected function resetPaginaReciente(): void
    {
        $this->pagina_reciente = 1;
    }
}
