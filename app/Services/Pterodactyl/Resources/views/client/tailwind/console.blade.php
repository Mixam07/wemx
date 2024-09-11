@extends(Theme::path('orders.master'))
@section('title', 'Console | ' . $order->name)

@if(settings('encrypted::pterodactyl::api_admin_key', false))
    @section('content')
        <div class="container mx-auto">
            <div class="relative overflow-x-auto overflow-y-auto shadow-md" style="height: 80vh;">
                @includeIf(Theme::serviceView('pterodactyl', 'stats_bar'))

                <div class="dark:bg-gray-800 rounded p-2">
                    <div
                        class="overflow-y-auto h-96 p-3 text-xs  bg-gray-50 border text-gray-900 dark:text-white dark:bg-gray-700 dark:border-gray-600  font-semibold"
                        id="console-output">
                        <!-- Console output will be here -->
                    </div>
                    <div class="mt-2 relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <i class='bx bxs-chevrons-right text-gray-900 dark:text-white'></i>
                        </div>
                        <label>
                            <input type="text" id="commandInput"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                   placeholder="{!! __('client.type_command') !!}">
                        </label>
                    </div>
                </div>
            </div>

            <input type="hidden" id="totalMemory" value="{{ $server["limits"]["memory"] }}">
            <input type="hidden" id="totalDisk" value="{{ $server["limits"]["disk"] }}">
            <input type="hidden" id="totalCPU" value="{{ $server["limits"]["cpu"] }}">
            <input type="hidden" id="orderId" value="{{ $order->id }}">
            <input type="hidden" id="socketUrl"
                   value="{{ route("pterodactyl.console.socket", ["order" => $order->id]) }}">

            <div class="hidden">
                <div id="translate-running">{{ Str::upper(__('client.running')) }}</div>
                <div id="translate-starting">{{ Str::upper(__('client.starting')) }}</div>
                <div id="translate-stopping">{{ Str::upper(__('client.stopping')) }}</div>
                <div id="translate-offline">{{ Str::upper(__('client.offline')) }}</div>
                <div id="translate-installing">{{ Str::upper(__('client.installing')) }}</div>
                <div id="translate-suspended">{{ Str::upper(__('admin.suspended')) }}</div>
                <div id="translate-updating">{{ Str::upper(__('client.updating')) }}</div>
            </div>
        </div>
        <script src="{{ Module::asset('pterodactyl:js/ansi_up.js') }}"></script>
        <script src="{{ Module::asset('pterodactyl:js/console.js') }}"></script>

    @endsection
@endif
