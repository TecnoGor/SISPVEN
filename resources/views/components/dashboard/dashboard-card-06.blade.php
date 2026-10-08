<div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-6 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                <div class="px-5 pt-5">
                    <header class="flex justify-between items-start mb-2">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Total de Oficinas</h2>
                    </header>
                    <div class="grid grid-cols-4 place-content-between">
                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1">Total </div>
                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1 justify-self-end">Activas</div>
                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1 justify-self-end">Inoperativas</div>
                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1 justify-self-end">Inactivas</div>
                    </div>
                    <div class="grid grid-cols-4 place-content-between">
                        <div class="text-3xl font-bold text-gray-800 dark:text-gray-100 mr-2">{{ number_format($dataFeed[6], 0, ',', '.') }}</div>
                        <div class="text-xl font-bold text-gray-800 dark:text-gray-100 mr-2 justify-self-end">{{ number_format($dataFeed[7], 0, ',', '.') }}</div>
                        <div class="text-xl font-bold text-gray-800 dark:text-gray-100 mr-2 justify-self-end">{{ number_format($dataFeed[8], 0, ',', '.') }}</div>
                        <div class="text-xl font-bold text-gray-800 dark:text-gray-100 mr-2 justify-self-end">{{ number_format($dataFeed[9], 0, ',', '.') }}</div>
                    </div>
                    <div id="dashboard-card-06-legend" class="grow mb-1">
                            <ul class="flex flex-wrap gap-x-4 sm:justify-end"></ul>
                    </div>
                </div>

                <div class="grow">
                    <canvas id="dashboard-card-06" width="595" height="248"></canvas>
                </div>
</div>