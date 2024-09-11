<!-- Create Task Modal -->
<div id="edit-task-modal{{ $task['attributes']['id'] }}" tabindex="-1" aria-hidden="true"
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {!! __('client.edit_task') !!}
                </h3>
                <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="edit-task-modal{{ $task['attributes']['id'] }}">
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
                        action="{{ route('pterodactyl.schedules.update_task', ['server' => $server, 'order' => $order->id]) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="schedule_id" value="{{ $schedule['id'] }}">
                        <input type="hidden" name="task_id" value="{{ $task['attributes']['id'] }}">
                        <div class="col-span-2 mb-3">
                            <label for="action"
                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{!! __('client.action') !!}</label>
                            <select id="action{{ $task['attributes']['id'] }}" name="task[action]"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option @if($task['attributes']['action'] == 'command') selected @endif value="command">{{ __('client.command') }}</option>
                                <option @if($task['attributes']['action'] == 'power') selected @endif value="power">{{ __('client.power') }}</option>
                                <option @if($task['attributes']['action'] == 'backup') selected @endif value="backup">{{ __('client.backup') }}</option>
                            </select>
                        </div>
                        <div class="col-span-2 mb-3">
                            <label
                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{!! __('client.payload') !!}</label>
                            <textarea id="payload-textarea{{ $task['attributes']['id'] }}" name="task[payload]" rows="3"
                                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                      style="display: none;">{{ $task['attributes']['payload'] }}</textarea>
                            <select id="payload-select{{ $task['attributes']['id'] }}" name="task[payload]"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                    style="display: none;">
                                <option @if($task['attributes']['payload'] == 'start') selected @endif value="start">{!! __('client.start') !!}</option>
                                <option @if($task['attributes']['payload'] == 'restart') selected @endif value="restart">{!! __('client.restart') !!}</option>
                                <option @if($task['attributes']['payload'] == 'stop') selected @endif value="stop">{!! __('client.stop') !!}</option>
                                <option @if($task['attributes']['payload'] == 'kill') selected @endif value="kill">{!! __('client.kill') !!}</option>
                            </select>
                        </div>
                        <div class="col-span-2 mb-3">
                            <label for="continue_on_failure{{ $task['attributes']['id'] }}"
                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                {!! __('client.continue_on_failure') !!}?
                            </label>
                            <select id="continue_on_failure{{ $task['attributes']['id'] }}" name="task[continue_on_failure]"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option @if($task['attributes']['continue_on_failure'] == "1") selected @endif value="1">{{ __('admin.yes') }}</option>
                                <option @if($task['attributes']['continue_on_failure'] == "0") selected @endif value="0">{{ __('admin.no') }}</option>
                            </select>
                        </div>
                        <div class="col-span-2 mb-3">
                            <label for="time_offset{{ $task['attributes']['id'] }}"
                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                {!! __('client.time_offset') !!}
                            </label>
                            <input type="number" id="time_offset{{ $task['attributes']['id'] }}" name="task[time_offset]" value="{{ $task['attributes']['time_offset'] }}"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        </div>
                        <button type="submit"
                                class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-1 px-2 text-sm rounded mt-5">
                            {{ __('client.update') }}
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>



