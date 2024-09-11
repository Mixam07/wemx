@if(settings('encrypted::pterodactyl::api_admin_key', false))
    @php($permissions = collect($order->package->data('permissions', [])))

    <div class="px-3 rounded py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800 mb-6">
        <ul class="space-y-2 font-medium">
            @if($permissions->get('pterodactyl.console', 0) == 1)
                <li>
                    <a href="{{ route('pterodactyl.console', $order->id) }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 {{ is_active('pterodactyl.console', [], 'bg-gray-100 dark:bg-gray-700') }}">
                        <span
                            class="flex-shrink-0 flex w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            style="font-size: 23px;"> <i class="bx bx-terminal"></i> </span>
                        <span class="flex-1 ml-3 whitespace-nowrap">{!! __('client.console') !!}</span>
                    </a>
                </li>
            @endif
            @if($permissions->get('pterodactyl.files', 0) == 1)
                <li>
                    <a href="{{ route('pterodactyl.files', $order->id) }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 {{ is_active('pterodactyl.files', [], 'bg-gray-100 dark:bg-gray-700') }}">
                        <span
                            class="flex-shrink-0 flex w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            style="font-size: 23px;"> <i class='bx bxs-file'></i> </span>
                        <span class="flex-1 ml-3 whitespace-nowrap">{!! __('client.files') !!}</span>
                    </a>
                </li>
            @endif
            @if($permissions->get('pterodactyl.databases', 0) == 1)
                <li>
                    <a href="{{ route('pterodactyl.databases', $order->id) }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 {{ is_active('pterodactyl.databases', [], 'bg-gray-100 dark:bg-gray-700') }}">
                        <span
                            class="flex-shrink-0 flex w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            style="font-size: 23px;"> <i class='bx bxs-data'></i> </span>
                        <span class="flex-1 ml-3 whitespace-nowrap">{!! __('client.database') !!}</span>
                    </a>
                </li>
            @endif
            @if($permissions->get('pterodactyl.schedules', 0) == 1)
                <li>
                    <a href="{{ route('pterodactyl.schedules', $order->id) }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700
               {{ is_active('pterodactyl.schedules', [], 'bg-gray-100 dark:bg-gray-700') }}
               {{ is_active('pterodactyl.schedules.get', [], 'bg-gray-100 dark:bg-gray-700') }}">
                        <span
                            class="flex-shrink-0 flex w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            style="font-size: 23px;"> <i class='bx bx-calendar'></i> </span>
                        <span class="flex-1 ml-3 whitespace-nowrap">{!! __('client.schedules') !!}</span>
                    </a>
                </li>
            @endif
            @if($permissions->get('pterodactyl.backups', 0) == 1)
                <li>
                    <a href="{{ route('pterodactyl.backups', $order->id) }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 {{ is_active('pterodactyl.backups', [], 'bg-gray-100 dark:bg-gray-700') }}">
                        <span
                            class="flex-shrink-0 flex w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            style="font-size: 23px;"> <i class='bx bx-cloud-download'></i> </span>
                        <span class="flex-1 ml-3 whitespace-nowrap">{!! __('client.backups') !!}</span>
                    </a>
                </li>
            @endif
            @if($permissions->get('pterodactyl.network', 0) == 1)
                <li>
                    <a href="{{ route('pterodactyl.network', $order->id) }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 {{ is_active('pterodactyl.network', [], 'bg-gray-100 dark:bg-gray-700') }}">
                        <span
                            class="flex-shrink-0 flex w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            style="font-size: 23px;"> <i class='bx bx-network-chart'></i> </span>
                        <span class="flex-1 ml-3 whitespace-nowrap">{!! __('client.network') !!}</span>
                    </a>
                </li>
            @endif
            @if($permissions->get('pterodactyl.settings', 0) == 1)
                <li>
                    <a href="{{ route('pterodactyl.settings', $order->id) }}"
                       class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 {{ is_active('pterodactyl.settings', [], 'bg-gray-100 dark:bg-gray-700') }}">
                        <span
                            class="flex-shrink-0 flex w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            style="font-size: 23px;"> <i class='bx bx-cog'></i> </span>
                        <span class="flex-1 ml-3 whitespace-nowrap">{!! __('client.settings') !!}</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>

    @if(auth()->user()->id == $order->user_id)
        <div class="px-3 rounded py-4 overflow-y-auto bg-gray-50 dark:bg-gray-800 mb-6">
            <ul class="space-y-2 font-medium">
                <li>
                    <a id="mega-menu-full-dropdown-button" data-collapse-toggle="mega-menu-full-dropdown"
                       class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 bg-gray-100 dark:bg-gray-700 cursor-pointer">
                <span
                    class="flex-shrink-0 flex w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                    style="font-size: 23px;"> <i class="bx bx-server"></i> </span>
                        <span
                            class="flex-1 ml-3 whitespace-nowrap">{{ Cache::get('server_name_' .  $order->id,  $order->name) }}</span>
                    </a>
                </li>
                <div id="mega-menu-full-dropdown" class="hidden">

                    @foreach($order::whereStatus('active')->whereService('pterodactyl')->where('user_id', $order->user->id)->get() as $orderItem)
                        <li class="py-1">
                            <a href="{{ route('pterodactyl.console', $orderItem->id) }}"
                               class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 @if($orderItem->id == $order->id) bg-gray-100 dark:bg-gray-700 @endif">
                        <span
                            class="flex-shrink-0 flex w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            style="font-size: 23px;"> <i class="bx bx-server"></i> </span>
                                <span
                                    class="flex-1 ml-3 whitespace-nowrap">{{ Cache::get('server_name_' .  $orderItem->id,  $orderItem->name) }}</span>
                            </a>
                        </li>
                    @endforeach


                </div>
            </ul>
        </div>
    @endif
@endif
