@extends(Theme::path('orders.master'))
@section('title', 'Databases | ' . $order->name)

@if(settings('encrypted::pterodactyl::api_admin_key', false))
    @php($create_allowed = count($databases) < $server['feature_limits']['databases'])
    @section('content')
        <div class="container mx-auto ">
            <div class="relative overflow-x-auto overflow-y-auto shadow-md sm:rounded-lg" style="height: 80vh;">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {!! __('client.databases') !!} {{ count($databases) }}/{{ $server['feature_limits']['databases'] }}</th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">{!! __('client.endpoint') !!}</th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 text-end whitespace-nowrap">
                            <button type="submit" data-modal-target="create-modal" data-modal-toggle="create-modal"
                                    class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-0.5 px-2 text-sm rounded"
                                    @if(!$create_allowed) disabled @endif>
                                {!! __('admin.create') !!}
                            </button>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-600">
                    @foreach($databases as $database)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">{{ $database['attributes']['name'] }}</td>
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">{{ $database['attributes']['host']['address'].':'.$database['attributes']['host']['port'] }}</td>
                            <td class="px-4 py-2 text-end">
                                <button data-modal-target="view-modal{{ $database['attributes']['id'] }}"
                                        data-modal-toggle="view-modal{{ $database['attributes']['id'] }}"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 text-sm rounded mr-1">
                                    {!! __('admin.view') !!}
                                </button>
                                <button data-modal-target="confirm-modal{{ $database['attributes']['id'] }}"
                                        data-modal-toggle="confirm-modal{{ $database['attributes']['id'] }}"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 text-sm rounded">
                                    {!! __('admin.delete') !!}
                                </button>
                            </td>
                        </tr>
                        @include(Theme::serviceView('pterodactyl','modals.view-database'), ['database' => $database])
                        @include(Theme::serviceView('pterodactyl','modals.confirm-delete-database'), ['database' => $database])
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @include(Theme::serviceView('pterodactyl','modals.create-database'))
    @endsection
@endif
