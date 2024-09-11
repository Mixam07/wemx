@extends(Theme::path('orders.master'))
@section('title', 'Schedulers | ' . $order->name)

@if(settings('encrypted::pterodactyl::api_admin_key', false))

    @section('content')
        <div class="container mx-auto ">
            <div class="relative overflow-x-auto overflow-y-auto shadow-md sm:rounded-lg" style="height: 80vh;">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ __('client.name') }}</th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ __('client.cron') }}</th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ __('client.status') }}</th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ __('client.running') }}</th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ __('client.only_when_online') }}</th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 text-end whitespace-nowrap">
                            <button data-modal-target="create-modal" data-modal-toggle="create-modal" type="button"
                                    class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-0.5 px-2 text-sm rounded">
                                {{ __('client.create') }}
                            </button>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-600">

                    @foreach($schedules as $schedule)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">{{ $schedule['attributes']['name'] }}</td>
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">
                                {{ $schedule['attributes']['cron']['minute'] }}
                                {{ $schedule['attributes']['cron']['hour'] }}
                                {{ $schedule['attributes']['cron']['day_of_month'] }}
                                {{ $schedule['attributes']['cron']['month'] }}
                                {{ $schedule['attributes']['cron']['day_of_week'] }}
                            </td>
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">{{ $schedule['attributes']['is_active'] ? __('admin.active') : __('admin.inactive') }}</td>
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">{{ $schedule['attributes']['is_processing'] ? __('admin.yes') : __('admin.no') }}</td>
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">{{ $schedule['attributes']['only_when_online'] ? __('admin.yes') : __('admin.no') }}</td>
                            <td class="px-4 py-2 text-end whitespace-nowrap">
                                <a href="{{ route('pterodactyl.schedules.get', ['order' => $order->id, 'server' => $server['identifier'], 'schedule' => $schedule['attributes']['id']]) }}"
                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 text-sm rounded">
                                    {!! __('admin.view') !!}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @include('pterodactyl::client.tailwind.modals.create-schedule')
    @endsection
@endif
