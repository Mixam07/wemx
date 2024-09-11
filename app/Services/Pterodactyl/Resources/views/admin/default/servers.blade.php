@extends(AdminTheme::wrapper(), ['title' => __('admin.servers'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('css_libraries')
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.css')) }}"/>
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/select2/dist/css/select2.min.css')) }}">
@endsection

@section('js_libraries')
    <script src="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.js')) }}"></script>
    <script src="{{ asset(AdminTheme::assets('modules/select2/dist/js/select2.full.min.js')) }}"></script>
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    {!! __('admin.servers_created_panel') !!}
                    <button class="btn btn-info" data-toggle="modal" data-target="#assigning_server">
                        {!! __('admin.assign_server') !!}
                    </button>
                </div>

                <div class="card-body">

                    <hr>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{!! __('admin.id') !!}</th>
                                <th>{!! __('admin.name') !!}</th>
                                <th>{!! __('admin.user') !!}</th>
                                <th class="text-right">{!! __('admin.actions') !!}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($servers as $server)
                                <tr>
                                    <td>{{ $server['attributes']['uuid'] }}</td>
                                    <td>{{ $server['attributes']['name'] }}</td>
                                    <td>
                                        <a href="{{ route('users.edit', str_replace('wmx-', '', $server['attributes']['relationships']['user']['attributes']['external_id'])) }}">
                                            {{ $server['attributes']['relationships']['user']['attributes']['username'] }}
                                        </a>

                                    </td>

                                    <td class="text-right">
                                        <a href="@settings('encrypted::pterodactyl::api_url', false)/server/{{ $server['attributes']['uuid'] }}" target="_blank"
                                           class="btn btn-primary">
                                            View Panel
                                        </a>
                                        <a href="@settings('encrypted::pterodactyl::api_url', false)/admin/servers/view/{{ $server['attributes']['id'] }}" target="_blank"
                                           class="btn btn-primary">
                                            View Admin Panel
                                        </a>
                                        <a href="{{ route('service', ['order' => str_replace('wmx-', '', $server['attributes']['external_id']), 'page' => 'manage']) }}"
                                           class="btn btn-primary" target="_blank">
                                        {!! __('admin.view') !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    {{ $servers->links(AdminTheme::pagination()) }}


    {{-- Modal window for assigning a server to an order--}}
    <div class="modal fade" id="assigning_server" tabindex="-1" role="dialog" aria-labelledby="assigning_server_label"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assigning_server_label">
                        {!! __('admin.assign_server_desc') !!}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('client.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('pterodactyl.server.assign') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group col-md-12 col-12">
                            <label for="order_id">{!! __('admin.order') !!}</label>
                            <select type="text" class="form-control" name="order_id" id="order_id"
                                    required>
                                @foreach(Order::whereService('pterodactyl')->whereExternalId(null)->get() as $order)
                                    <option value="{{ $order['id'] }}">{{ $order['id'] }}: {{ $order->user->username }}
                                        ({{ $order['name'] }}) ({{ $order['status'] }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                {!! __('admin.order_assign_desc') !!}
                            </small>
                        </div>
                        <div class="form-group col-md-12 col-12">
                            <label for="uuidShort">{!! __('admin.server_uuid_short') !!}</label>
                            <input type="text" class="form-control" name="server_uuid" id="uuidShort"
                                   placeholder="8b604519" required>
                            <small class="form-text text-muted">
                                {!! __('admin.server_uuid_assign_desc') !!}
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{!! __('admin.close') !!}
                        </button>
                        <button type="submit"
                                class="btn btn-primary">{!! __('admin.submit') !!}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
