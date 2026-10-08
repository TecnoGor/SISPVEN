@section('titulo')
    Consumo de Combustible
@endsection

<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="w-full mb-4">
            <h1 class="text-lg font-bold text-center text-primary">Gráficas del Mes Actual</h1>
            <p class="text-sm text-center text-gray-600 mt-1" id="infoOficina">
                @if(isset($infoOficina))
                    @if($infoOficina['tipo'] === 'SuperAdmin')
                        Mostrando datos de todas las oficinas ({{ $infoOficina['totalVehiculos'] }} vehículos)
                    @elseif($infoOficina['tipo'] === 'Gerente de Estado')
                        Mostrando datos de {{ $infoOficina['nombre'] }} ({{ $infoOficina['totalVehiculos'] }} vehículos)
                    @else
                        Mostrando datos de {{ $infoOficina['nombre'] }} ({{ $infoOficina['totalVehiculos'] }} vehículos)
                    @endif
                @else
                    Cargando información de la oficina...
                @endif
            </p>
        </div>
        <!-- Dashboard resumen -->
        <div wire:ignore>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Resumen General: Gráfica de flujo -->
            <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                <h2 class="text-xl font-bold text-primary mb-2 mt-10">Resumen General</h2>
                <div class="w-full flex flex-col items-center">
                    <canvas id="resumenGeneralLine" height="180" style="width:100%; mt-20"></canvas>
                </div>
                <div class="flex flex-col md:flex-row justify-center items-center gap-4 mt-4">
                    <div class="text-center">
                        <div class="text-xs text-gray-500 font-semibold uppercase">Litros Totales</div>
                        <div id="litrosTotalesMes" class="text-lg font-bold text-green-700">0</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500 font-semibold uppercase">Costos Totales</div>
                        <div id="costosTotalesMes" class="text-lg font-bold text-primary">0</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs text-gray-500 font-semibold uppercase">Vehículos</div>
                        <div id="totalVehiculos" class="text-lg font-bold text-blue-700">0</div>
                    </div>
                </div>
            </div>
            <!-- Consumo por Vehículo: Gráfica de barras -->
            <div class="bg-white rounded-lg shadow p-6 flex flex-col items-center">
                <h2 class="text-xl font-bold text-primary mb-2">Consumo de Combustible por Vehículo</h2>
                <div class="flex flex-col md:flex-row gap-2 items-center mb-2 w-full justify-between">
                    <div class="flex items-center gap-2">
                        <label for="topVehiculos" class="text-sm font-semibold text-gray-700">Mostrar:</label>
                        <select id="topVehiculos" class="border border-gray-300 rounded px-2 py-1 text-sm">
                            <option value="5">Top 5</option>
                            <option value="10">Top 10</option>
                            <option value="20">Top 20</option>
                            <option value="all">Todos</option>
                        </select>
                    </div>
                    <div class="text-xs text-gray-500" id="mesActual">Mes actual</div>
                </div>
                <div class="w-full">
                    <canvas id="consumoCombustibleChart" height="180"></canvas>
                </div>
            </div>
        </div>
        </div>

        <!-- Pestañas -->
        <div class="flex flex-col md:flex-row">
            <x-tab-link :href="route('flota')" :active="request()->routeIs('flota')" wire:navigate.hover>
                {{ __('Todos los Vehículos') }}
            </x-tab-link>

            <x-tab-link :href="route('mantenimientos-hoy')" :active="request()->routeIs('mantenimientos-hoy')" wire:navigate.hover>
                {{ __('Mantenimientos de Hoy') }}
            </x-tab-link>
            <x-tab-link :href="route('mantenimientos-servicios')" :active="request()->routeIs('mantenimientos-servicios')" wire:navigate.hover>
                {{ __('Por Fecha') }}
            </x-tab-link>
           <x-tab-link :href="route('consumo-combustible')" :active="request()->routeIs('consumo-combustible')" wire:navigate.hover>
                {{ __('Consumo de Combustible') }}
            </x-tab-link>
        </div>

        <!-- Buscador y tabla -->
        <div class="bg-white shadow-md sm:rounded-lg overflow-hidden border border-gray-200">
            <div class="flex flex-col md:flex-row gap-2 items-center justify-between p-4">
                <div class="flex w-full md:w-auto">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500"
                                fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input  type="text" wire:model.live.debounce.300ms="search"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-default focus:border-default block w-full pl-10 p-2 "
                            placeholder="Buscar...">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-default uppercase bg-gray-100">
                        <tr>
                            <th class="px-4 py-3">VEHÍCULO</th>
                            <th class="px-4 py-3">PLACA</th>
                            <th class="px-4 py-3">LITROS TOTALES</th>
                            <th class="px-4 py-3">COSTO TOTAL</th>
                            <th class="px-4 py-3">KM INICIAL</th>
                            <th class="px-4 py-3">KM FINAL</th>
                            <th class="px-4 py-3">KM RECORRIDOS</th>
                            <th class="px-4 py-3">L/100KM</th>
                            <th class="px-4 py-3">KM/L</th>
                            <th class="px-4 py-3">DETALLES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehiculos as $v)
                            <tr class="border-b text-center hover:bg-gray-50 transition">
                                <td class="px-4 py-2 font-semibold">{{ $v['vehiculo']->marca }} {{ $v['vehiculo']->modelo }}</td>
                                <td class="px-4 py-2">{{ $v['vehiculo']->placa }}</td>
                                <td class="px-4 py-2">{{ number_format($v['litrosTotales'], 2) }}</td>
                                <td class="px-4 py-2">{{ number_format($v['costoTotal'], 2) }}</td>
                                <td class="px-4 py-2">{{ $v['kilometrajeInicial'] }}</td>
                                <td class="px-4 py-2">{{ $v['kilometrajeFinal'] }}</td>
                                <td class="px-4 py-2">{{ $v['kmRecorridos'] }}</td>
                                <td class="px-4 py-2">{{ $v['consumoPor100km'] ? number_format($v['consumoPor100km'], 2) : '-' }}</td>
                                <td class="px-4 py-2">{{ $v['kmPorLitro'] ? number_format($v['kmPorLitro'], 2) : '-' }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('historial-combustible-vehiculo', $v['vehiculo']->vehiculo_id) }}" class="text-blue-600 hover:underline font-bold">Ver historial</a>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b text-center">
                                <td colspan="10" class="py-7 text-default text-2xl">No hay datos de consumo de combustible.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="py-4 px-3">
                <div class="flex space-x-4 items-center mb-3">
                    <label class="w-32 text-sm font-medium text-gray-900">Por página</label>
                    <select wire:model.live="perPage"
                        class="md:max-w-36 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block w-full p-2.5 ">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                    </select>
                </div>
                <div>
                    @if($vehiculos->lastPage() > 1)
                        {{ $vehiculos->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// Colores globales para todas las gráficas (declarar solo una vez)
const PRIMARY_COLOR = '#d32f2f'; // Rojo del sistema (costos)
const GREEN_COLOR = 'rgba(34,197,94,0.85)'; // Verde (litros)
const GREEN_COLOR_BORDER = 'rgba(34,197,94,1)';
let resumenLineChart = null;
let barChart = null;

function renderResumenLineChart(litros, costos) {
    const ctx = document.getElementById('resumenGeneralLine');
    if (!ctx) return;
    const context = ctx.getContext('2d');
    if (!context) return;
    if (resumenLineChart) resumenLineChart.destroy();
    
    // Verificar si hay datos válidos
    const hasData = (litros > 0 || costos > 0);
    
    resumenLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Margen de Beneficio', 'Ingresos Totales', 'Costos Totales'],
            datasets: [
                {
                    label: 'Litros Totales',
                    data: [0, litros, 0],
                    borderColor: GREEN_COLOR,
                    backgroundColor: GREEN_COLOR,
                    fill: false,
                    tension: 0.4,
                    pointRadius: hasData ? 3 : 0,
                    pointBackgroundColor: GREEN_COLOR,
                },
                {
                    label: 'Costos Totales',
                    data: [0, costos, 0],
                    borderColor: PRIMARY_COLOR,
                    backgroundColor: PRIMARY_COLOR,
                    fill: false,
                    tension: 0.4,
                    pointRadius: hasData ? 3 : 0,
                    pointBackgroundColor: PRIMARY_COLOR,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                },
                tooltip: {
                    enabled: hasData,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.parsed.y;
                            if (label.includes('Litros')) {
                                return label + ': ' + value.toLocaleString(undefined, {maximumFractionDigits:2}) + ' L';
                            } else {
                                return label + ': ' + value.toLocaleString(undefined, {maximumFractionDigits:2}) + ' Bs';
                            }
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { 
                        color: hasData ? '#888' : '#9ca3af', 
                        font: { weight: 'bold' } 
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#eee' },
                    ticks: { 
                        color: hasData ? '#888' : '#9ca3af',
                        callback: function(value) {
                            if (!hasData) return '';
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function updateBarChart(topN) {
    const ctx = document.getElementById('consumoCombustibleChart');
    if (!ctx) return;
    const context = ctx.getContext('2d');
    if (!context) return;
    if (barChart) barChart.destroy();
    if (!window.allData) return;

    let labels = allData.labels;
    let litros = allData.litros;
    
    // Verificar si hay datos
    if (!labels || labels.length === 0) {
        // Mostrar mensaje de no hay datos
        barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Sin datos'],
                datasets: [{
                    label: 'Litros',
                    data: [0],
                    backgroundColor: '#e5e7eb',
                    borderColor: '#d1d5db',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Vehículo (placa)', font: { size: 14 } },
                        ticks: {
                            color: '#9ca3af'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Litros', font: { size: 14 } },
                        ticks: {
                            color: '#9ca3af'
                        }
                    }
                }
            }
        });
        return;
    }
    
    // Ordenar por litros descendente
    let combined = labels.map((label, i) => ({ label, litros: litros[i] }));
    combined.sort((a, b) => b.litros - a.litros);
    if (topN !== 'all') {
        combined = combined.slice(0, parseInt(topN));
    }

    // Calcular el valor máximo de litros para ajustar la escala
    let maxLitros = Math.max(...combined.map(x => x.litros), 0);
    // Calcular un stepSize adecuado
    let stepSize = 1;
    if (maxLitros > 1000) stepSize = 100;
    else if (maxLitros > 500) stepSize = 50;
    else if (maxLitros > 250) stepSize = 25;
    else if (maxLitros > 100) stepSize = 10;
    else if (maxLitros > 50) stepSize = 5;
    else if (maxLitros > 10) stepSize = 2;
    // El máximo sugerido será el siguiente múltiplo de stepSize por encima del máximo real
    let suggestedMax = Math.ceil(maxLitros / stepSize) * stepSize;
    if (suggestedMax === 0) suggestedMax = 10;

    barChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: combined.map(x => x.label),
            datasets: [{
                label: 'Litros',
                data: combined.map(x => x.litros),
                backgroundColor: PRIMARY_COLOR,
                borderColor: PRIMARY_COLOR,
                borderWidth: 1,
                barPercentage: 0.4,
                categoryPercentage: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { font: { size: 13 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.parsed.y;
                            return label + ': ' + value.toLocaleString(undefined, {maximumFractionDigits:2}) + ' L';
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: { display: true, text: 'Vehículo (placa)', font: { size: 14 } },
                    ticks: {
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 20
                    }
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: suggestedMax,
                    ticks: {
                        stepSize: stepSize
                    },
                    title: { display: true, text: 'Litros', font: { size: 14 } }
                }
            }
        }
    });
}

function mostrarErrorGraficas(mensaje) {
    let errorDiv = document.getElementById('errorGraficasConsumo');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'errorGraficasConsumo';
        errorDiv.style.color = 'red';
        errorDiv.style.fontWeight = 'bold';
        errorDiv.style.marginTop = '10px';
        document.getElementById('resumenGeneralLine').parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = mensaje;
}

function limpiarErrorGraficas() {
    let errorDiv = document.getElementById('errorGraficasConsumo');
    if (errorDiv) errorDiv.remove();
}

function cargarGraficasConsumoCombustible() {
    limpiarErrorGraficas();
    
    // Mostrar información de carga
    const infoOficina = document.getElementById('infoOficina');
    if (infoOficina) {
        infoOficina.textContent = 'Cargando datos de consumo de combustible...';
    }
    
    console.log('Iniciando llamada a la API...');
    
    fetch('/api/consumo-combustible', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin'
    })
        .then(async response => {
            console.log('Respuesta de la API:', response.status, response.statusText);
            
            if (response.status === 429) {
                mostrarErrorGraficas('Demasiadas peticiones. Espera unos segundos y vuelve a intentarlo.');
                throw new Error('429');
            }
            if (!response.ok) {
                console.error('Error HTTP:', response.status, response.statusText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            window.allData = data;
            
            // Debug: Mostrar información en consola para diagnóstico
            if (data.debug) {
                console.log('Debug info:', data.debug);
                console.log('Datos recibidos:', {
                    labels: data.labels,
                    litros: data.litros,
                    costos: data.costos,
                    totLitros: data.totLitros,
                    totCostos: data.totCostos,
                    totalVehiculos: data.totalVehiculos
                });
            }
            
            // Actualizar información de la oficina
            if (infoOficina) {
                if (data.totalVehiculos > 0) {
                    const oficinaInfo = data.oficinaUsuario ? ` (${data.oficinaUsuario})` : '';
                    infoOficina.textContent = `Mostrando datos de ${data.totalVehiculos} vehículos${oficinaInfo} - ${data.mesActual}`;
                } else {
                    const oficinaInfo = data.oficinaUsuario ? ` de ${data.oficinaUsuario}` : '';
                    infoOficina.textContent = `No hay datos de consumo${oficinaInfo} disponibles`;
                }
            }
            
            if (document.getElementById('resumenGeneralLine')) {
                console.log('Renderizando gráfica de resumen con:', data.totLitros, data.totCostos);
                renderResumenLineChart(data.totLitros, data.totCostos);
                document.getElementById('litrosTotalesMes').textContent = data.totLitros.toLocaleString(undefined, {maximumFractionDigits:2}) + ' L';
                document.getElementById('costosTotalesMes').textContent = data.totCostos.toLocaleString(undefined, {maximumFractionDigits:2}) + ' Bs';
                document.getElementById('totalVehiculos').textContent = data.totalVehiculos || 0;
                document.getElementById('mesActual').textContent = `Mes actual: ${data.mesActual}`;
            }
            
            if (typeof updateBarChart === 'function') {
                console.log('Actualizando gráfica de barras con', data.labels.length, 'vehículos');
                // Lee el valor actual del select para sincronizar el top
                let topSelect = document.getElementById('topVehiculos');
                let topN = topSelect ? topSelect.value : '5';
                updateBarChart(topN);
            }
        })
        .catch(e => {
            console.error('Error cargando gráficas:', e);
            if (infoOficina) {
                if (e.message && e.message.includes('429')) {
                    infoOficina.textContent = 'Demasiadas peticiones. Espera unos segundos y vuelve a intentarlo.';
                } else {
                    infoOficina.textContent = 'Error al cargar los datos. Intenta recargar la página.';
                }
            }
            mostrarErrorGraficas('Error al cargar los datos de consumo de combustible.');
        });
}

// Cargar gráficas en los eventos correctos
window.addEventListener('DOMContentLoaded', cargarGraficasConsumoCombustible);
window.addEventListener('livewire:load', cargarGraficasConsumoCombustible);
window.addEventListener('livewire:navigated', cargarGraficasConsumoCombustible);
window.addEventListener('closeModal', function() {
    setTimeout(cargarGraficasConsumoCombustible, 200);
});

// Refuerza el cierre del modal Livewire
Livewire.hook('message.processed', (message, component) => {
    if (!document.querySelector('[wire\:model.defer="historialModalOpen"]') || document.querySelector('[wire\:model.defer="historialModalOpen"]').style.display === 'none') {
        setTimeout(cargarGraficasConsumoCombustible, 200);
    }
});

document.getElementById('topVehiculos').addEventListener('change', function(e) {
    updateBarChart(e.target.value);
});

// MutationObserver para restaurar las gráficas si los canvas son recreados
let lastResumenCanvas = null;
let lastBarCanvas = null;
const observer = new MutationObserver(() => {
    const resumenCanvas = document.getElementById('resumenGeneralLine');
    const barCanvas = document.getElementById('consumoCombustibleChart');
    if ((resumenCanvas && resumenCanvas !== lastResumenCanvas) || (barCanvas && barCanvas !== lastBarCanvas)) {
        lastResumenCanvas = resumenCanvas;
        lastBarCanvas = barCanvas;
        cargarGraficasConsumoCombustible();
    }
});
observer.observe(document.body, { childList: true, subtree: true });

// Función para probar la API directamente
function testAPI() {
    console.log('Probando API...');
    fetch('/api/consumo-combustible-test', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin'
    })
        .then(response => response.json())
        .then(data => {
            console.log('Test API response:', data);
        })
        .catch(error => {
            console.error('Test API error:', error);
        });
}

// Función para probar la API principal
function testMainAPI() {
    console.log('Probando API principal...');
    fetch('/api/consumo-combustible', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin'
    })
        .then(response => response.json())
        .then(data => {
            console.log('Main API response:', data);
        })
        .catch(error => {
            console.error('Main API error:', error);
        });
}

// Hacer las funciones disponibles globalmente para debugging
window.testAPI = testAPI;
window.testMainAPI = testMainAPI;
</script>
@endpush 