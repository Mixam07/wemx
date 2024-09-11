@extends(Theme::path('orders.master'))
@section('title', 'Settings | ' . $order->name)

@if(settings('encrypted::pterodactyl::api_admin_key', false))
    @php
        $permissions = collect($order->package->data('permissions', []));
        $inputClass = 'mt-1 block w-full h-8 py-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white';
        $selectClass = 'mt-1 block w-full h-8 py-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white';
        $btnClass = 'mt-3 inline-flex justify-center py-1 px-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white';
    @endphp

    @section('content')
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-400 rounded-lg shadow overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <!-- Startup Command Card -->
                    <div class="mb-4 shadow-2xl p-3">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{!! __('client.startup_command') !!}</h3>
                        <textarea id="startup"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                  rows="4" disabled>{{ $data['meta']['startup_command'] }}</textarea>
                    </div>

                    <!-- Reinstall Button -->
                    <div class="mb-4 shadow-2xl p-3">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{!! __('client.reinstall') !!}</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{!! __('client.reinstall_desc') !!}</p>
                        <form method="GET"
                              action="{{ route('pterodactyl.settings.reinstall', ['order' => $order->id, 'server' => $server['identifier']]) }}">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('{{ __('client.reinstall_confirm') }}')"
                                    class=" {{ $btnClass }} bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                {!! __('client.reinstall') !!}
                            </button>
                        </form>
                    </div>

                    <!-- SFTP Details -->
                    <div class="mb-4 shadow-2xl p-3">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{!! __('client.sftp_details') !!}</h3>
                        <div class="mt-2 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="sftp-username"
                                       class="block text-sm font-medium text-gray-700 dark:text-gray-400">{!! __('auth.username') !!}</label>
                                <input type="text" id="sftp-username"
                                       class="{{ $inputClass }}"
                                       value="{{ $user['username'].'.'.$server['identifier'] }}" disabled>
                            </div>
                            <div>
                                <label for="sftp-address"
                                       class="block text-sm font-medium text-gray-700 dark:text-gray-400">{!! __('auth.address') !!}</label>
                                <input type="text" id="sftp-address"
                                       class="{{ $inputClass }}"
                                       value="{{ $server['sftp_details']['ip'].':'.$server['sftp_details']['port'] }}"
                                       disabled>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="mb-4">
                            <span
                                class="ml-3 inline-flex rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800 dark:bg-primary-900 dark:text-primary-300">
                                {!! __('client.launch_sftp_desc') !!}
                            </span>
                            </div>
                            @php($launch_url = 'sftp://'.$user['username'].'.'.$server['identifier'].'@'.$server['sftp_details']['ip'].':'.$server['sftp_details']['port'])
                            <a href="{{ $launch_url }}" target="_blank"
                               class="{{ $btnClass }} bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                {!! __('client.launch_sftp') !!}
                            </a>
                            <button type="button" data-drawer-target="drawer-change-password"
                                    data-drawer-show="drawer-change-password" data-drawer-placement="right"
                                    aria-controls="drawer-change-password"
                                    class="{{ $btnClass }} bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                {{ __('client.change_password') }}
                            </button>
                        </div>
                    </div>

                    @foreach (enabledModules() as $module)
                        @includeIf(Theme::moduleView($module->getLowerName(), 'pterodactyl.settings_block'))
                    @endforeach

                    <!-- Server Name and Docker Image Update -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="shadow-2xl p-3">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('client.name') }}</h3>
                            <form method="POST"
                                  action="{{ route('pterodactyl.settings.rename', ['order' => $order->id, 'server' => $server['identifier']]) }}">
                                @csrf
                                <input type="text" id="server-name" name="name"
                                       class="{{ $inputClass }}"
                                       value="{{ $server['name'] }}">
                                <button type="submit"
                                        class="{{ $btnClass }} bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    {!! __('client.rename') !!}
                                </button>
                            </form>
                        </div>
                        <div class="shadow-2xl p-3">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('client.docker_image') }}</h3>
                            <form method="POST"
                                  action="{{ route('pterodactyl.settings.docker_image', ['order' => $order->id, 'server' => $server['identifier']]) }}">
                                @csrf
                                <select id="docker-image" name="docker_image"
                                        class="{{ $selectClass }}">
                                    @foreach($data['meta']['docker_images'] as $key => $image)
                                        <option value="{{ $image }}"
                                                @if($image == $server['docker_image']) selected @endif>{{ $key }}
                                            ({{ $image }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                        class="{{ $btnClass }} bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    {!! __('admin.update') !!}
                                </button>
                            </form>
                        </div>

                        @if($permissions->get('pterodactyl.variables', 0) == 1)
                            @foreach($data['data'] as $variable)
                                <form method="POST" class="shadow-2xl p-3"
                                      action="{{ route('pterodactyl.settings.update_variable', ['order' => $order->id, 'server' => $server['identifier']]) }}">
                                    @csrf
                                    @php($rules = ptero()::determineType(explode('|', $variable['attributes']['rules'])))
                                    <div class="mb-4">
                                        <label for="{{ $variable['attributes']['env_variable'] }}"
                                               class="block text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $variable['attributes']['name'] }}
                                        </label>
                                        <input type="hidden" name="var_name"
                                               value="{{ $variable['attributes']['env_variable'] }}">
                                        @if($rules['type'] == 'bool')
                                            <select name="var_value" id="{{ $variable['attributes']['env_variable'] }}"
                                                    class="{{ $selectClass }}"
                                                {{ $variable['attributes']['is_editable'] ? '' : 'disabled' }}>
                                            </select>
                                        @elseif($rules['type'] == 'number')
                                            <input type="number" name="var_value"
                                                   id="{{ $variable['attributes']['env_variable'] }}"
                                                   @if(array_key_exists('min', $rules)) min="{{ $rules['min'] ?? '' }}"
                                                   @endif
                                                   @if(array_key_exists('max', $rules)) max="{{ $rules['max'] ?? '' }}"
                                                   @endif
                                                   class="{{ $inputClass }}"
                                                   value="{{ $variable['attributes']['server_value'] }}" {{ $variable['attributes']['is_editable'] ? '' : 'disabled' }}>
                                        @elseif($rules['type'] == 'select')
                                            <select name="var_value" id="{{ $variable['attributes']['env_variable'] }}"
                                                    class="{{ $inputClass }}"
                                                {{ $variable['attributes']['is_editable'] ? '' : 'disabled' }}>
                                                @foreach($rules['options'] as $key => $value)
                                                    <option value="{{ $key }}"
                                                            @if($key == $variable['attributes']['server_value']) selected @endif>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" name="var_value"
                                                   id="{{ $variable['attributes']['env_variable'] }}"
                                                   class="{{ $inputClass }}"
                                                   value="{{ $variable['attributes']['server_value'] }}" {{ $variable['attributes']['is_editable'] ? '' : 'disabled' }}>
                                        @endif
                                    </div>
                                    @if($variable['attributes']['is_editable'])
                                        <button type="submit"
                                                class="{{ $btnClass }} bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                            {!! __('admin.update') !!}
                                        </button>
                                    @else
                                        <span
                                            class="ml-3 inline-flex rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800 dark:bg-primary-900 dark:text-primary-300">
                                        {!! __('responses.no_permission') !!}
                                    </span>
                                    @endif
                                </form>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- Change Password -->
        <div id="drawer-change-password"
             class="fixed top-0 right-0 z-40 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white w-80 dark:bg-gray-800"
             tabindex="-1" aria-labelledby="drawer-change-password-label">
            <h5 id="drawer-change-password-label"
                class="inline-flex items-center mb-4 text-base font-semibold text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>{{ __('client.change_password') }}</h5>
            <button type="button" data-drawer-hide="drawer-change-password"
                    aria-controls="drawer-change-password"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 right-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">{{ __('client.close_menu') }}</span>
            </button>
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">{{ __('client.change_service_password', ['service' => $order->package->service]) }}</p>
            <form
                action="{{ route('pterodactyl.settings.change_password', ['order' => $order->id, 'server' => $server['identifier']]) }}"
                method="POST">
                @csrf
                <div class="mb-6">
                    <label for="password"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('auth.new_password') }}</label>
                    <input type="text" name="password" id="password"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                           placeholder="{{ __('auth.new_password') }}" required>
                </div>

                <div class="mb-6">
                    <label for="password_confirmation"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('auth.confirm_new_password') }}</label>
                    <input type="text" name="password_confirmation" id="password_confirmation"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                           placeholder="{{ __('auth.confirm_new_password') }}" required>
                </div>

                <div class="">
                    <button type="submit" style="width: 100%"
                            class="items-center px-4 py-2 text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        {{ __('client.change_password') }}
                    </button>

                </div>
            </form>
        </div>
    @endsection
@endif
