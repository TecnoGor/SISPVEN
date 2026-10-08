<div class="flex flex-col col-span-full sm:col-span-6 xl:col-span-6 bg-white dark:bg-gray-800 shadow-sm rounded-xl">
    <div class="px-5 pt-5">
        <header class="flex justify-between items-start mb-2">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Total Devoluciones de envios</h2>
        </header>
        <div class="grid grid-cols-3 place-content-between">
            <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-1">Total</div>
        </div>
        <div class="grid grid-cols-3 place-content-between">
            <div class="text-3xl font-bold text-gray-800 dark:text-gray-100 mr-2">{{ number_format($dataFeed[10], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="grow max-sm:max-h-[128px] xl:max-h-[128px]">
        <canvas id="dashboard-card-07" width="389" height="128"></canvas>
    </div>
</div>
