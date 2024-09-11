<!-- View modal -->
<div id="view-modal{{ $database['attributes']['id'] }}" tabindex="-1" aria-hidden="true"
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {!! __('client.database') !!}: {{ $database['attributes']['name'] }}
                </h3>
                <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="view-modal{{ $database['attributes']['id'] }}">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2"
                              d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">{{ __('client.close') }}</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5">
                <div class="grid gap-4 mb-4 grid-cols-2">
                    <div class="col-span-2">
                        <label for="endpoint"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{!! __('client.endpoint') !!}</label>
                        <input onclick="copyValue(this)" type="text"
                               value="{{ $database['attributes']['host']['address'].':'.$database['attributes']['host']['port'] }}"
                               id="endpoint" readonly
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 cursor-pointer">
                    </div>
                    <div class="col-span-2">
                        <label for="connection"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{!! __('client.connection_from') !!}</label>
                        <input type="text"
                               value="{{ $database['attributes']['connections_from'] }}"
                               id="connection" readonly
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 cursor-pointer">
                    </div>
                    <div class="col-span-2">
                        <label for="username"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('client.username') }}</label>
                        <input onclick="copyValue(this)" type="text" id="username"
                               value="{{ $database['attributes']['username'] }}" readonly
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 cursor-pointer">
                    </div>
                    <div class="col-span-2">
                        <label for="password"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('admin.password') }}</label>
                        <input onclick="copyValue(this)" type="text" id="password"
                               value="{{ $database['attributes']['relationships']['password']['attributes']['password'] }}"
                               readonly
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 cursor-pointer">
                    </div>
                    <form
                        action="{{ route('pterodactyl.databases.reset_password', ['server' => $server['identifier'], 'order' => $order->id]) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="database"
                               value="{{ $database['attributes']['id'] }}">
                        <button type="submit"
                                class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-1 px-2 text-sm rounded">
                            {!! __('auth.reset_password') !!}
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
<script>
    function copyValue(element) {
        if (element && 'value' in element) {
            let text = element.value;
            if (text !== '{{ __('client.copied') }}!') {
                navigator.clipboard.writeText(text).then(function () {
                    element.value = '{{ __('client.copied') }}!';
                    setTimeout(function () {
                        element.value = text;
                    }, 1000);
                }).catch(function (error) {
                    console.error('Could not copy text: ', error);
                });
            }
        }
    }
</script>
