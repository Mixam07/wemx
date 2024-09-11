@extends(Theme::path('orders.master'))
@section('title', 'Schedulers | ' . $order->name)

@if(settings('encrypted::pterodactyl::api_admin_key', false))

    @section('content')
        <div class="container mx-auto px-4 py-6 dark:bg-gray-800 dark:text-gray-200 rounded">
            <!-- Task header -->
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-xl font-bold">{{ $schedule['name'] }}
                        @if($schedule['is_active'])
                            <span
                                class="bg-primary-100 text-primary-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                            {{ __('client.active') }}
                        </span>
                        @else
                            <span
                                class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                {{ __('client.inactive') }}
                            </span>
                        @endif
                    </h2>
                    <p class="text-sm mt-2">
                        {!! __('client.last_run_at') !!}
                        <span
                            class="bg-primary-100 text-primary-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                            @if($schedule['last_run_at'])
                                {{ now()::parse($schedule['last_run_at'])->translatedFormat('d M Y H:i:s') }}
                            @else
                                n/a
                            @endif

                        </span>
                        |
                        {!! __('client.next_run_at') !!}
                        <span
                            class="bg-primary-100 text-primary-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                            {{ now()::parse($schedule['next_run_at'])->translatedFormat('d M Y H:i:s') ?? 'n/a' }}
                        </span>
                    </p>
                </div>
                <div class="flex space-x-2">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 text-sm rounded"
                            data-modal-target="edit-modal" data-modal-toggle="edit-modal" type="button">
                        {!! __('client.edit') !!}
                    </button>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 text-sm rounded"
                            data-modal-target="create-task-modal" data-modal-toggle="create-task-modal" type="button">
                        {!! __('client.new_task') !!}
                    </button>
                </div>
            </div>

            <!-- Schedule -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <input class="bg-gray-700 rounded px-4 py-2" type="text" disabled
                       value="{{ $schedule['cron']['minute'] }}">
                <input class="bg-gray-700 rounded px-4 py-2" type="text" disabled
                       value="{{ $schedule['cron']['hour'] }}">
                <input class="bg-gray-700 rounded px-4 py-2" type="text" disabled
                       value="{{ $schedule['cron']['day_of_month'] }}">
                <input class="bg-gray-700 rounded px-4 py-2" type="text" disabled
                       value="{{ $schedule['cron']['month'] }}">
                <input class="bg-gray-700 rounded px-4 py-2" type="text" disabled
                       value="{{ $schedule['cron']['day_of_week'] }}">
            </div>

            <!-- Task Actions -->
            <div class="space-y-4 mb-6">
                @foreach($schedule['relationships']['tasks']['data'] as $task)
                    <div class="bg-gray-700 p-4 rounded flex justify-between items-center">
                        <div>
                            <span>{{ Str::upper($task['attributes']['action']) }}</span>
                            @if($task['attributes']['payload'])
                                <p class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $task['attributes']['payload'] }}</p>
                            @else
                                <p class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{!! __('client.create_backup') !!}</p>
                            @endif

                        </div>

                        <div class="flex items-center">
                            @if($task['attributes']['continue_on_failure'])
                                <span
                                    class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">{!! __('client.continues_on_failure') !!}</span>
                            @endif
                            <i class="bx bxs-edit text-blue-400 mr-2"
                               data-modal-target="edit-task-modal{{ $task['attributes']['id'] }}"
                               data-modal-toggle="edit-task-modal{{ $task['attributes']['id'] }}"></i>
                            <i class="bx bxs-trash text-red-400"
                               data-modal-target="confirm-delete-task{{ $task['attributes']['id'] }}"
                               data-modal-toggle="confirm-delete-task{{ $task['attributes']['id'] }}"></i>
                        </div>
                    </div>
                    @include(Theme::serviceView('pterodactyl', 'modals.edit-task', ['task' => $task]))
                    @include(Theme::serviceView('pterodactyl', 'modals.confirm-delete-task', ['task' => $task]))
                @endforeach
            </div>

            <!-- Task footer -->
            <div class="flex justify-end space-x-2">
                <button data-modal-target="confirm-modal" data-modal-toggle="confirm-modal" type="button"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 text-sm rounded">
                    {{ __('client.delete') }}
                </button>
                @if(count($schedule['relationships']['tasks']['data']))
                    <a href="{{ route('pterodactyl.schedules.execute', ['server' => $server, 'order' => $order->id, 'schedule' => $schedule['id']]) }}"
                       class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-0.5 px-2 text-sm rounded">
                        {!! __('client.run_now') !!}
                    </a>
                @endif

            </div>
        </div>

        @include(Theme::serviceView('pterodactyl', 'modals.edit-schedule'))
        @include(Theme::serviceView('pterodactyl', 'modals.create-task'))
        @include(Theme::serviceView('pterodactyl', 'modals.confirm-delete-schedule'))

        <script>
            function togglePayload(taskId, action) {
                let payloadTextarea = document.getElementById('payload-textarea' + taskId);
                let payloadSelect = document.getElementById('payload-select' + taskId);

                if (action === 'power') {
                    payloadTextarea.style.display = 'none';
                    payloadSelect.style.display = 'block';
                    payloadSelect.setAttribute('name', 'task[payload]');
                    payloadTextarea.removeAttribute('name');
                } else {
                    payloadTextarea.style.display = 'block';
                    payloadSelect.style.display = 'none';
                    payloadTextarea.setAttribute('name', 'task[payload]');
                    payloadSelect.removeAttribute('name');
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[id^="action"]').forEach(actionSelect => {
                    let taskId = actionSelect.id.replace('action', '');
                    togglePayload(taskId, actionSelect.value);
                    actionSelect.addEventListener('change', function() {
                        togglePayload(taskId, this.value);
                    });
                });
            });
        </script>


    @endsection

@endif
