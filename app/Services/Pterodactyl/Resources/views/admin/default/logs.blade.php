@extends(AdminTheme::wrapper(), ['title' => __('admin.logs'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-6">
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end">

                        <a href="{{ route('pterodactyl.logs.clear') }}" class="btn btn-danger mb-3">
                            {!! __('admin.clear_all') !!}
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{!! __('admin.severity') !!}</th>
                                <th>{!! __('admin.source') !!}</th>
                                <th>{!! __('admin.message') !!}</th>
                                <th>{!! __('admin.date') !!}</th>
                                <th class="text-right">{!! __('admin.actions') !!}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($logs as $log)
                                <tr>
                                    <td><span class="badge badge-secondary">{{ $log->severity }}</span></td>
                                    <td>{{ $log->source }}</td>
                                    <td>
                                        {!! Str::limit($log->message, 50) !!}
                                        <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1"
                                             role="dialog"
                                             aria-labelledby="logModalLabel{{ $log->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="logModalLabel{{ $log->id }}">
                                                            {{ $log->source }}
                                                        </h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="{{ __('admin.close') }}">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <strong> {!! nl2br($log->message)  !!} </strong>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">{!! __('admin.close') !!}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{$log->created_at->format('d M Y') }}
                                        ({{ $log->created_at->diffForHumans() }})
                                    </td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-target="#logModal{{ $log->id }}">
                                            {!! __('admin.view') !!}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $logs->links(AdminTheme::pagination()) }}
                </div>
            </div>
        </div>
    </div>
@endsection
