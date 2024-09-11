@extends(AdminTheme::wrapper(), ['title' => 'Artisan', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Env {!! __('client.backups') !!}</h4>
                    <div class="card-header-action">
                        <a href="{{ route('artisan.env-editor') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i> </a>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{ __('admin.name') }}</th>
                                <th>{{ __('admin.path') }}</th>
                                <th>{!! __('admin.created_at') !!}</th>
                                <th class="text-right">{{ __('admin.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($backups as $backup)
                                <tr>
                                    <td>{{ $backup->getFileName() }}</td>
                                    <td>{{ $backup->getPath() }}</td>
                                    <td>{{ date('Y-m-d H:i:s', $backup->getMTime()) }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('artisan.env-backup-download', $backup->getFileName()) }}"
                                           class="btn btn-primary">{{ __('admin.download') }}</a>
                                        <a href="{{ route('artisan.env-backup-restore', $backup->getFileName()) }}" onclick="return confirm('{!! __('admin.you_sure') !!}')"
                                           class="btn btn-warning">{{ __('client.restore') }}</a>
                                        <a href="{{ route('artisan.env-backup-delete', $backup->getFileName()) }}" onclick="return confirm('{!! __('admin.you_sure') !!}')"
                                           class="btn btn-danger">{{ __('admin.delete') }}</a>
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
@endsection


