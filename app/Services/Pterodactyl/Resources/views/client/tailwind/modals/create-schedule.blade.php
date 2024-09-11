<!-- Create modal -->
<div id="create-modal" tabindex="-1" aria-hidden="true"
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {!! __('client.create_schedule') !!}
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
                    <form
                        action="{{ route('pterodactyl.schedules.create', ['server' => $server['identifier'], 'order' => $order->id]) }}"
                        method="POST">
                        @csrf
                        <div class="col-span-2 mb-3">
                            <label for="name"
                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('client.name') }}</label>
                            <input required type="text" value="" id="name" name="name"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 w-full">
                        </div>
                        <div class="col-span-2 mb-3">
                            <select onchange="generateCron(this.value)"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option value="every-minute">{{ __('client.every_minute') }}</option>
                                <option value="every-5-minutes">{{ __('client.every_5_minutes') }}</option>
                                <option value="every-hour">{{ __('client.every_hour') }}</option>
                                <option value="every-day">{{ __('client.every_day') }}</option>
                                <option value="every-week">{{ __('client.every_week') }}</option>
                                <option value="every-month">{{ __('client.every_month') }}</option>
                                <option value="15th-of-month">{{ __('client.15th_of_month') }}</option>
                                <option value="every-year">{{ __('client.every_year') }}</option>
                                <option value="new-year">{{ __('client.new_year') }}</option>
                            </select>
                        </div>
                        <div class="mb-3 grid grid-cols-5 gap-4">
                            <div class="col-span-1 mb-3">
                                <label for="minute"
                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{!! __('client.minute') !!}</label>
                                <input required type="text" value="*/5" id="minute" name="minute"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 w-full">
                            </div>
                            <div class="col-span-1 mb-3">
                                <label for="hour"
                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{!! __('client.hour') !!}</label>
                                <input required type="text" value="*" id="hour" name="hour"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 w-full">
                            </div>
                            <div class="col-span-1 mb-3">
                                <label for="day_of_month"
                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{!! __('client.day_of_month') !!}</label>
                                <input required type="text" value="*" id="day_of_month" name="day_of_month"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 w-full">
                            </div>
                            <div class="col-span-1 mb-3">
                                <label for="month"
                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{!! __('client.month') !!}</label>
                                <input required type="text" value="*" id="month" name="month"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 w-full">
                            </div>
                            <div class="col-span-1 mb-3">
                                <label for="day_of_week"
                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{!! __('client.day_of_week') !!}</label>
                                <input required type="text" value="*" id="day_of_week" name="day_of_week"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 w-full">
                            </div>
                        </div>
                        <div class="col-span-1 mb-3">
                            <label for="is_active"
                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                {!! __('client.active') !!}?
                            </label>
                            <select id="is_active" name="is_active"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option value="1">{{ __('admin.yes') }}</option>
                                <option value="0">{{ __('admin.no') }}</option>
                            </select>
                        </div>

                        <div class="col-span-1 mb-3">
                            <label for="only_when_online"
                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                {!! __('client.only_when_online') !!}?
                            </label>
                            <select id="only_when_online" name="only_when_online"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option value="1">{{ __('admin.yes') }}</option>
                                <option value="0">{{ __('admin.no') }}</option>
                            </select>
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


<script src="{{ Module::asset('pterodactyl:js/script.js') }}"></script>
