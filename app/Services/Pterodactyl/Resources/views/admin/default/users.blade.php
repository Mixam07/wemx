@extends(AdminTheme::wrapper(), ['title' => __('admin.users'), 'keywords' => 'WemX Dashboard, WemX Panel'])

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
                    {!! __('admin.users_created_panel') !!}
                </div>

                <div class="card-body">

                    <hr>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{!! __('admin.id') !!}</th>
                                <th>{!! __('admin.username') !!}</th>
                                <th>{!! __('admin.email') !!}</th>
                                <th class="text-right">{!! __('admin.actions') !!}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user['attributes']['id'] }}</td>
                                    <td>{{ $user['attributes']['username'] }}</td>
                                    <td>{{ $user['attributes']['email'] }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('users.edit', str_replace('wmx-', '', $user['attributes']['external_id'])) }}"
                                           class="btn btn-primary">
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
    {{ $users->links(AdminTheme::pagination()) }}

@endsection
