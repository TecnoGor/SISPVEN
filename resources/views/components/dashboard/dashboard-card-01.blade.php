<div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-6 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
    <div class="px-5 pt-5">
        <header class="flex justify-between items-start mb-2">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Cantidad de Envíos</h2>
        </header>
        <div class="grid grid-cols-1 sm:grid-cols-3 place-content-between">
        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1">Total </div>
        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1 sm:justify-self-end">Nacional</div>
        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1 sm:justify-self-end">Internacional</div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 place-content-between">
            <div class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-100 mr-2 break-words">{{ number_format($dataFeed[0], 0, ',', '.') }}</div>
            <div class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-100 mr-2 sm:justify-self-end break-words">{{ number_format($dataFeed[1], 0, ',', '.') }}</div>
            <div class="text-lg sm:text-xl font-bold text-gray-800 dark:text-gray-100 mr-2 sm:justify-self-end break-words">{{ number_format($dataFeed[2], 0, ',', '.') }}</div>
        </div>
        <div id="dashboard-card-01-legend" class="grow mb-1">
                <ul class="flex flex-wrap gap-x-4 sm:justify-end"></ul>
        </div>
    </div>
    <!-- Chart built with Chart.js 3 -->
    <!-- Check out src/js/components/dashboard-card-01.js for config -->
    <div class="grow max-sm:max-h-[128px] xl:max-h-[128px]">
        <!-- Change the height attribute to adjust the chart height -->
        <canvas id="dashboard-card-01" width="389" height="128"></canvas>
    </div>
</div>
