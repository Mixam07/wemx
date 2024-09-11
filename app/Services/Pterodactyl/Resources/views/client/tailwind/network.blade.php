@extends(Theme::path('orders.master'))
@section('title', 'Network | ' . $order->name)

@if(settings('encrypted::pterodactyl::api_admin_key', false))
    @php($create_allowed = count($allocations) < $server['feature_limits']['allocations'])
    @section('content')

        <div class="container mx-auto ">
            <div class="relative overflow-x-auto overflow-y-auto shadow-md sm:rounded-lg" style="height: 80vh;">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">{!! __('client.ip') !!} {{ count($allocations) }}/{{ $server['feature_limits']['allocations'] }}</th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">{!! __('client.port') !!}</th>
                        <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300 text-end whitespace-nowrap">
                            <a class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-0.5 px-2 text-sm rounded cursor-pointer"
                                    @if($create_allowed) href="{{ route('pterodactyl.network.assign', ['order' => $order->id,'server' => $server['identifier']]) }}" @endif>
                                {!! __('admin.create') !!}
                            </a>
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-600">
                    @foreach($allocations as $allocation)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">{{ ptero()::node($server['node'])['ip'] ?? $allocation['attributes']['ip_alias'] ??  $allocation['attributes']['ip']}}</td>
                            <td class="px-4 py-2 dark:text-gray-300 whitespace-nowrap">{{ $allocation['attributes']['port'] }}</td>
                            <td class="px-4 py-2 text-end">
                                <a href="{{ route('pterodactyl.network.primary', ['order' => $order->id,'server' => $server['identifier'], 'allocation' => $allocation['attributes']['id']]) }}"
                                   class="font-bold text-white py-1 px-4 text-sm rounded mr-1 inline-flex items-center justify-center
                                   @if($allocation['attributes']['is_default']) bg-yellow-500 hover:bg-yellow-700 @else bg-blue-500 hover:bg-blue-700 @endif">
                                    @if($allocation['attributes']['is_default'])
                                        {!! __('client.primary') !!}
                                    @else
                                       {!! __('client.make_primary') !!}
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endsection
@endif
