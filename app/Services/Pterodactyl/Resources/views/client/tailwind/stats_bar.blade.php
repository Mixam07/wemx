<div class="flex justify-between items-center">
    <div>
        <span class="flex items-center text-1xl uppercase font-medium text-gray-900 dark:text-white mb-4">
            <span class="bg-gray-100 text-gray-800 font-semibold inline-flex items-center px-2.5 py-0.5 rounded me-2 dark:bg-gray-800 dark:text-white border border-gray-500 dark:border-gray-700">
                <i class='bx bx-signal-5 flex w-4 h-4 rounded-full mr-1.5 flex-shrink-0 text-green-500'></i>
                <span class="cursor-pointer" onclick="copyToClipboard(this)">{{ ptero()::serverIP($order->id) }}</span>
            </span>
        </span>
    </div>
    <div>
        <span id="serverStatus" class="flex items-center text-1xl uppercase font-medium text-gray-900 dark:text-white mb-4">
            <span class="bg-gray-100 text-gray-800 font-semibold inline-flex items-center px-2.5 py-0.5 rounded me-2 dark:bg-gray-800 dark:text-white border border-gray-500 dark:border-gray-700">
                <span id="statusIndicator" class="flex w-4 h-4 bg-red-600 rounded-full mr-1.5 flex-shrink-0"></span>
                <span id="statusText">{!! __('client.offline') !!}</span>
            </span>
        </span>
    </div>
</div>

<div id="resourceUsage" class="flex flex-wrap">
    <!-- CPU Usage -->
    <div class="w-full md:w-1/3 pr-2 mb-4">
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 cpu-usage-info">
            <h5 class="mb-2 text-lg font-bold tracking-tight text-gray-900 dark:text-white">{!! __('client.cup_usage') !!}</h5>
            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400 cpu-usage">{!! __('client.offline') !!}</p>
            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                <div class="bg-primary-600 h-2.5 rounded-full cpu-usage-bar" style="width: 1%"></div>
            </div>
        </div>
    </div>
    <!-- Memory Usage -->
    <div class="w-full md:w-1/3 pl-2 pr-2 mb-4">
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 memory-usage-info">
            <h5 class="mb-2 text-lg font-bold tracking-tight text-gray-900 dark:text-white">{!! __('client.memory_usage') !!}</h5>
            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400 memory-usage">{!! __('client.offline') !!}</p>
            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                <div class="bg-primary-600 h-2.5 rounded-full memory-usage-bar" style="width: 1%"></div>
            </div>
        </div>
    </div>
    <!-- Disk Usage -->
    <div class="w-full md:w-1/3 pl-2 mb-4">
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 disk-usage-info">
            <h5 class="mb-2 text-lg font-bold tracking-tight text-gray-900 dark:text-white">{!! __('client.disk_usage') !!}</h5>
            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400 disk-usage">{!! __('client.offline') !!}</p>
            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                <div class="bg-primary-600 h-2.5 rounded-full disk-usage-bar" style="width: 1%"></div>
            </div>
        </div>
    </div>
</div>

{{-- Buttons --}}
<div class="flex space-x-4 font-semibold text-sm text-white">
    <button
        class="flex-1 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 rounded-lg px-5 py-2.5 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
        id="start">{!! __('client.start') !!}
    </button>
    <button
        class="flex-1 focus:outline-none bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 rounded-lg px-5 py-2.5 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
        id="restart">{!! __('client.restart') !!}
    </button>
    <button
        class="flex-1 focus:outline-none bg-[#FF9119] hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 rounded-lg px-5 py-2.5 mb-2 dark:focus:ring-yellow-900"
        id="stop">{!! __('client.stop') !!}
    </button>
    <button
        class="flex-1 focus:outline-none bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 rounded-lg px-5 py-2.5 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
        id="kill">{!! __('client.kill') !!}
    </button>
</div>
