@extends(AdminTheme::wrapper(), ['title' => 'Artisan', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading">Warning!</h4>
        <p>Editing the .env file can cause your application to stop working. Please be careful and make sure you have a backup of the .env file before making any changes.</p>
        <hr>
        <p class="mb-0">If you are not sure what you are doing, please ask for help from a professional.</p>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Env</h4>
                    <div class="card-header-action">
                        <a href="{{ route('artisan.env-backups') }}" class="btn btn-primary">{!! __('client.backups') !!}</a>
                        <a href="{{ route('artisan.index') }}" class="btn btn-primary"><i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>

                <div class="card-body p-3">
                    @foreach(explode("\n", $env) as $line)
                        @if($line)
                            @php
                                $key = trim(explode('=', $line)[0] ?? '');
                                $value = trim(explode('=', $line)[1] ?? '');
                                if(empty($key)) {
                                    continue;
                                }
                            @endphp
                            <form action="{{ route('artisan.env-editor-save') }}" method="post">
                                @csrf
                                <div class="form-group row">
                                    <label for="{{ $key }}" class="col-sm-2 col-form-label">{{ $key }}</label>
                                    <div class="col-sm-8">

                                        <input type="hidden" name="key" value="{{ $key }}">
                                        @if(in_array($key, $nonEditableKeys))
                                            <input type="password" class="form-control" name="value" id="{{ $key }}"
                                                   onclick="this.setAttribute('type', 'text');"
                                                   onchange="this.value = '{{ $value }}'"
                                                   value="{{ $value }}">
                                        @else
                                            <input type="text" class="form-control" name="value" id="{{ $key }}"
                                                   value="{{ $value }}">
                                        @endif


                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit"
                                                class="btn btn-primary" {{ in_array($key, $nonEditableKeys) ? 'disabled' : '' }}>{{ __('admin.update') }}</button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>


        </div>
    </div>

@endsection
