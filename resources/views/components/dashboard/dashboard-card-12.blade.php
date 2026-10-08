<div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-6 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <div class="px-5 pt-5">
                    <header class="flex justify-between items-start mb-2">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Tipos de Oficina totales</h2>
                    </header>
                    <div class="grid grid-cols-4 place-content-between">
                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1 justify-self-end">Arrendadas</div>
                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1 justify-self-end">Comodato</div>
                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1 justify-self-end">Propia </div>
                    </div>
                    <div class="grid grid-cols-4 place-content-between">
                        <div class="text-xl font-bold text-gray-800 dark:text-gray-100 mr-2 justify-self-end">{{ number_format($dataFeed[12], 0, ',', '.') }}</div>
                        <div class="text-xl font-bold text-gray-800 dark:text-gray-100 mr-2 justify-self-end">{{ number_format($dataFeed[13], 0, ',', '.') }}</div>
                        <div class="text-xl font-bold text-gray-800 dark:text-gray-100 mr-2 justify-self-end">{{ number_format($dataFeed[11], 0, ',', '.') }}</div>
                    </div>
                </div>


                <div class="grow max-sm:max-h-[128px] xl:max-h-[128px]">
                    <canvas id="dashboard-card-12" width="595" height="248"></canvas>
                </div>
                
                <!-- Inyectar datos correctos desde PHP al JS -->
                <script>
                    window.dashboardCard12Data = {
                        arrendadas: {{ $dataFeed[12] }},
                        comodato: {{ $dataFeed[13] }},
                        propia: {{ $dataFeed[11] }}
                    };
                </script>

</div>