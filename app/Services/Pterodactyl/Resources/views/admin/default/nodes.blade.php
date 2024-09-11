@extends(AdminTheme::wrapper(), ['title' => __('admin.nodes'), 'keywords' => 'WemX Dashboard, WemX Panel'])

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
                    {!! __('admin.nodes') !!}
                    <a href="{{ route('pterodactyl.clear_cache') }}" class="btn btn-info"
                       onclick="return confirm('{!! __('admin.clear_cache_desc') !!}')">
                        {!! __('admin.clear_cache') !!}
                    </a>
                </div>

                <div class="card-body">

                    <hr>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{!! __('admin.uuid') !!}</th>
                                <th>{!! __('admin.name') !!}</th>
                                <th>{!! __('admin.ports_range') !!}</th>
                                <th>{!! __('admin.ip') !!}</th>
                                <th>{!! __('admin.location_id') !!}</th>
                                <th>{!! __('admin.status') !!}</th>
                                <th>{!! __('admin.available_disk') !!}</th>
                                <th>{!! __('admin.available_memory') !!}</th>
                                <th class="text-right">{!! __('admin.actions') !!}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($nodes as $node)
                                @php($allocationsCount = 0)
                                @php($allocations = ptero()->api()->allocations->get($node['id']))
                                @if(array_key_exists('data', $allocations))
                                    @php($allocationsCount = count($allocations['data']))
                                @endif
                                @if($allocationsCount <= 1)
                                    @php(ptero()->api()->allocations->create($node['id'], ['ip' => $node['fqdn'], 'ports' => ['25565']]))
                                @endif
                                <tr>
                                    <td>{{ $node['uuid'] }}</td>
                                    <td>{{ $node['name'] }}</td>
                                    <td>{{ $node['ports_range'] ?? '49152-65535' }}</td>
                                    <td>{{ $node['ip'] ?? $node['fqdn'] }}</td>
                                    <td>{{ $node['location_id'] }}</td>
                                    <td>{{ $node['is_full'] ? 'Full' : 'Available'}}</td>
                                    <td>{{ megabytesToGigabytes($node['available_disk']) }} GB</td>
                                    <td>{{ megabytesToGigabytes($node['available_memory']) }} GB</td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-target="#nodeModal{{ $node['id'] }}">
                                            {!! __('admin.manage') !!}
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="nodeModal{{ $node['id'] }}" tabindex="-1"
                                     role="dialog" aria-labelledby="nodeModalLabel{{ $node['id'] }}"
                                     aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="nodeModalLabel{{ $node['id'] }}">
                                                    {!! __('admin.pterodactyl_edit_node') !!}: {{ $node['name'] }}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="{{ __('client.close') }}">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{route('pterodactyl.nodes.store')}}" method="POST">
                                                @csrf
                                                <input type="hidden" name="node_id" value="{{ $node['id'] }}">
                                                <input type="hidden" name="location_id"
                                                       value="{{ $node['location_id'] }}">
                                                <div class="modal-body">
                                                    <div class="form-group col-md-12 col-12">
                                                        <label
                                                            for="ports_range">{!! __('admin.ports_rang_label') !!}</label>
                                                        <input type="text" class="form-control" name="ports_range"
                                                               id="ports_range"
                                                               value="{{ $node['ports_range'] ?? '49152-65535' }}"
                                                               required/>
                                                        <small class="form-text text-muted">
                                                            {!! __('admin.ports_rang_desc') !!}
                                                        </small>
                                                    </div>
                                                    <div class="form-group col-md-12 col-12">
                                                        <label
                                                            for="ip">{!! __('admin.ip') !!}</label>
                                                        <input type="text" class="form-control" name="ip"
                                                               id="ip"
                                                               value="{{ $node['ip'] ?? $node['fqdn'] }}"
                                                               required/>
                                                        <small class="form-text text-muted">
                                                            {!! __('admin.ip_address') !!}
                                                        </small>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{!! __('admin.close') !!}
                                                    </button>
                                                    <button type="submit"
                                                            class="btn btn-primary">{!! __('admin.update') !!}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
