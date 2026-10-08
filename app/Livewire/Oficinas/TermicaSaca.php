<?php

namespace App\Livewire\Oficinas;

use App\Models\EnvioSaca;
use App\Models\Saca;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Picqer\Barcode\BarcodeGeneratorPNG;

#[Layout('layouts.app')]
class TermicaSaca extends Component
{
    public $saca_id;
    public $saca;
    public $envios;
    public $barcodeImage; // Para almacenar el código de barras generado

    public function mount($saca_id)
    {
        $this->saca_id = $saca_id;
        $this->saca = Saca::find($this->saca_id);
        $this->envios = EnvioSaca::where('saca_id', $this->saca_id)->count();

        if ($this->saca) {
            // Generar el código de barras cuando se encuentra la saca
            $this->generateBarcode($this->saca->codigo_saca);
        }
    }

    // Método para generar el código de barras
    public function generateBarcode($codigo_saca)
    {
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($codigo_saca, $generator::TYPE_CODE_128);
        $this->barcodeImage = base64_encode($barcode); // Convertir el código de barras a base64
    }

    public function render()
    {
        return view('livewire.oficinas.termica-saca', [
            'saca' => $this->saca,
            'barcodeImage' => $this->barcodeImage, // Pasamos la imagen del código de barras a la vista
            'envios' => $this->envios,
        ]);
    }
}

