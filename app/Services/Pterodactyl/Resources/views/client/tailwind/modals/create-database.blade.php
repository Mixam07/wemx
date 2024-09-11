@if($create_allowed)
    <!-- Create modal -->
    <div id="create-modal" tabindex="-1" aria-hidden="true"
         class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {!! __('client.create_database') !!}
                    </h3>
                    <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-toggle="create-modal">
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
                    <div class="grid gap-4 mb-4 grid-cols-1">
                        <form action="{{ route('pterodactyl.databases.create', ['server' => $server['identifier'], 'order' => $order->id]) }}"
                              method="POST">
                            @csrf
                            <div class="col-span-2">
                                <label for="database"
                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('client.name') }}</label>
                                <input required type="text" value="" id="database" name="database"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 w-full">
                            </div>
                            <div class="col-span-2 mt-4">
                                <label for="remote"
                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{!! __('client.connection_from') !!}</label>
                                <input required type="text" name="remote" value="%" id="remote"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 w-full">
                            </div>
                            <button type="submit"
                                    class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-1 px-2 text-sm rounded mt-5">
                                {{ __('client.create') }}
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endif
