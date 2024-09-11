@extends(Theme::path('orders.master'))
@section('title', 'Backups | ' . $order->name)

@if(settings('encrypted::pterodactyl::api_admin_key', false))
    @php($create_allowed = count($backups) < $server['feature_limits']['backups'])
    @section('content')

        <div class="container mx-auto ">
            <div class="relative overflow-x-auto overflow-y-auto shadow-md sm:rounded-lg" style="height: 80vh;">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {!! __('client.backups') !!} {{ count($backups) }}/{{ $server['feature_limits']['backups'] }}</th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">{!! __('client.status') !!}</th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">{!! __('client.size') !!}</th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">{!! __('client.date') !!}</th>
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
                    @foreach($backups as $backup)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">{{ $backup['attributes']['name'] }}</td>
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">
                                @if($backup['attributes']['is_successful'])
                                    <span
                                            class="bg-primary-100 text-primary-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">
                                   {!! __('client.successful') !!}
                                </span>
                                @else
                                    <span
                                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                    {!! __('client.failed') !!}
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">{{ bytesToHuman($backup['attributes']['bytes']) }}</td>
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">{{ now()::parse($backup['attributes']['completed_at'])->diffForHumans() }}</td>
                            <td class="px-4 py-2 text-end">
                                <a href="{{ route('pterodactyl.backups.lock', ['order' => $order->id,'server' => $server['identifier'], 'backup' => $backup['attributes']['uuid']]) }}"
                                   class="font-bold text-white py-2 px-3 text-sm rounded mr-1 inline-flex items-center justify-center
                                   @if($backup['attributes']['is_locked']) bg-yellow-500 hover:bg-yellow-700 @else bg-primary-500 hover:bg-primary-700 @endif">
                                    @if($backup['attributes']['is_locked'])
                                        <i class="bx bxs-lock"></i>
                                    @else
                                        <i class="bx bxs-lock-open"></i>
                                    @endif
                                </a>
                                <a href="{{ route('pterodactyl.backups.download', ['order' => $order->id,'server' => $server['identifier'], 'backup' => $backup['attributes']['uuid']]) }}"
                                   class="bg-blue-500 hover:bg-blue-700 font-bold text-white py-2 px-3 text-sm rounded mr-1 inline-flex items-center justify-center">
                                    <i class="bx bxs-download"></i>
                                </a>
                                <a data-modal-target="confirm-restore{{ $backup['attributes']['uuid'] }}"
                                   data-modal-toggle="confirm-restore{{ $backup['attributes']['uuid'] }}"
                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-3 text-sm rounded mr-1 inline-flex items-center justify-center">
                                    <i class="bx bxs-archive-out"></i>
                                </a>
                                <a data-modal-target="confirm-modal{{ $backup['attributes']['uuid'] }}"
                                   @if(!$backup['attributes']['is_locked']) disabled
                                   data-modal-toggle="confirm-modal{{ $backup['attributes']['uuid'] }}"
                                   @endif
                                   class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-3 text-sm rounded inline-flex items-center justify-center">
                                    <i class="bx bxs-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @include(Theme::serviceView('pterodactyl','modals.confirm-restore-backup'), ['backup' => $backup])
                        @include(Theme::serviceView('pterodactyl','modals.confirm-delete-backup'), ['backup' => $backup])
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @include(Theme::serviceView('pterodactyl','modals.create-backup'))

    @endsection
@endif
