<div class="px-4 py-3">
    <div class="flex justify-end items-center gap-6">
        <div class="text-right">
            <span class="font-semibold text-gray-600 dark:text-gray-300">Total Pagado:</span>
            <span class="ml-2 font-bold text-gray-800 dark:text-white">
                S/ {{ $this->getTotalVentas()['total'] }}
            </span>
        </div>
        <div class="text-right">
            <span class="font-semibold text-gray-600 dark:text-gray-300">10% del Total:</span>
            <span class="ml-2 font-bold text-emerald-600">
                S/ {{ $this->getTotalVentas()['porcentaje'] }}
            </span>
        </div>
    </div>
</div>