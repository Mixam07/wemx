@extends(AdminTheme::wrapper(), ['title' => __('Downloads'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <section class="section">
        <div class="section-body">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Downloads') }}</h4>
                        <div class="card-header-action">
                            <a href="{{ route('downloads.create') }}" class="btn btn-icon icon-left btn-primary">
                                <i class="fas fa-solid fa-download"></i>
                                {{ __('Add New Downloads') }}
                            </a>
                        </div>

                    </div>


                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <tbody>
                                    <tr>
                                        <th class="text-center">{!! __('Downloads Name') !!}</th>
                                        <th class="text-center">{!! __('Description') !!}</th>
                                        <th class="text-center">{!! __('File') !!}</th>
                                        <th class="text-center">{!! __('Allow Guest') !!}</th>
                                        <th class="text-center">{!! __('Actions') !!}</th>
                                    </tr>

                                    @foreach ($downloads as $download)
                                        <tr>
                                            <td class="text-center">{{ $download->name }}</td>
                                            <td class="text-center">{{ $download->description }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('downloads.download', $download->id) }}">
                                                    <i class="fas fa-file-archive"></i> Download ZIP
                                                </a>
                                            </td>

                                            <td class="text-center">
                                                @if ($download->allow_guest)
                                                    <span class="badge badge-success">Yes</span>
                                                @else
                                                    <span class="badge badge-danger">No</span>
                                                @endif
                                            </td>


                                            <td class="text-center">
                                                <a href="{{ route('downloads.download', $download->id) }}"
                                                    class="btn btn-primary mr-2" title="{!! __('Download') !!}">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <a href="{{ route('downloads.edit', $download->id) }}"
                                                    class="btn btn-warning mr-2" title="{!! __('Edit') !!}">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('downloads.destroy', $download->id) }}"
                                                    method="post" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        title="{!! __('Delete') !!}"
                                                        onclick="return confirm('Are you sure you want to delete this download?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        {{ $downloads->links(AdminTheme::pagination()) }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css_libraries')
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.css')) }}" />
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/select2/dist/css/select2.min.css')) }}">
@endsection

@section('js_libraries')
    <script src="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.js')) }}"></script>
    <script src="{{ asset(AdminTheme::assets('modules/select2/dist/js/select2.full.min.js')) }}"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
@endsection
