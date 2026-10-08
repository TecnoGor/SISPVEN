@section('titulo')
Termica - Envío
@endsection

<div id="contentToPrint" class="p-6">
    <h2 id="backLink" class="font-semibold text-lg text-primary leading-tight flex gap-3 ml-10 mb-4 text-center">
        <x-return-link :href="route('mostrar-sacas')" wire:navigate.hover />
        Regresar
    </h2>
    <div class="bg-white border border-gray-300 p-5 relative">
        
        <!-- Título centrado con margen superior para evitar colisión con el logo -->
        <div class="text-center mt-10">
            <h1 class="text-lg font-bold mb-1">Instituto Postal Telegrafico</h1>
            <h2 class="text-sm">IPOSTEL</h2>
        </div>

        <!-- Código de barras -->
        <div class="mt-3 flex justify-center">
            <img src="data:image/png;base64,{{ $barcodeImage }}" alt="Código de barras" class="w-full max-w-xs">
        </div>
        <div class="mt-2 text-center">
            <p><strong>Código:</strong> {{ $saca->codigo_saca }}</p>
        </div>

        <!-- Información del envío -->
        <div class="mt-2 text-base">
            <p><strong>Oficina Origen:</strong> {{ $saca->oficinaOrigen->nombre }}</p>
            <p><strong>Oficina Destino:</strong> {{ $saca->oficinaDestino->nombre }}</p>
            <p><strong>Estado:</strong> {{ $saca->cerrado ? 'Cerrada' : 'Abierta' }}</p>
            <p><strong>Peso Total:</strong> {{ $saca->peso }} Gr</p>
            <p><strong>Cantidad de envios: </strong> {{ $envios }}</p>
            <p><strong>Fecha:</strong> {{ $saca->created_at->format('d/m/Y') }}</p>
            <p><strong>Tipo:</strong> {{ $saca->tipoSaca->nombre }}</p>
            <p><strong>Numero de Precinto:</strong> {{ $saca->numero_precinto }}</p>
              <p><strong>Numero de Despacho:</strong> N°{{ $saca->numeroDespacho->numero_despacho }}</p>
        </div>
    </div>

    <!-- Botón para generar PDF -->
    <div class="text-center mt-5">
        <button id="printButton" onclick="printPDF()" class="bg-primary text-white px-4 py-2 rounded shadow hover:bg-primary-dark">
            Imprimir en PDF
        </button>
    </div>
</div>

<!-- Script para generar el PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
    function printPDF() {
        const content = document.getElementById('contentToPrint').cloneNode(true);

        // Eliminar el botón de impresión y el enlace de regreso del contenido a imprimir
        const printButton = content.querySelector('#printButton');
        const backLink = content.querySelector('#backLink');

        if (printButton) {
            printButton.remove();
        }
        if (backLink) {
            backLink.remove();
        }

const options = {
    margin: [0, 0, 0, 0],
    filename: 'envio_termica.pdf',
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'cm', format: [12, 13.5], orientation: 'portrait' } // Ajusta la altura justo al contenido
};

        html2pdf().set(options).from(content).save();
    }
</script>
