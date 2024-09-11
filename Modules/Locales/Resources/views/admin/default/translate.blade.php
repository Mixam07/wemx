@extends(AdminTheme::wrapper())

@section('title')
    {{ __('locales::general.title') }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('locales::general.translation') }}</div>

                <div class="card-body">
                    <table class="table">
                        <thead>

                            <tr>
                                <th>{{ __('locales::general.files') }}</th>
                                <th class="text-right">{{ __('locales::general.locale_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <th class="text-center" colspan="2">{{ __('locales::general.general_files') }}</th>
                            @foreach ($files as $file)
                                <tr>
                                    <td><code>{{ $file->getRealPath() }}</code></td>
                                    <td class="text-right">
                                        <form method="POST" action="{{ route('locales.translate.file', $code) }}">
                                            @csrf
                                            <input type="hidden" name="file" value="{{ $file->getRealPath() }}">
                                            <button class="btn btn-sm btn-primary"
                                                type="submit">{{ __('locales::general.translate') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach (Module::collections() as $module)
                                @php($path = $module->getExtraPath('Resources/lang/' . $code))
                                @if (File::isDirectory($path))
                                    @if (!empty(File::files($path)))
                                        <th class="text-center" colspan="2">
                                            {{ __('locales::general.module_files', ['module' => $module->getName()]) }}
                                        </th>
                                    @endif

                                    @foreach (File::allFiles($path) as $file)
                                        <tr>
                                            <td><code>{{ $file->getRealPath() }}</code></td>
                                            <td class="text-right">
                                                <form method="POST" action="{{ route('locales.translate.file', $code) }}">
                                                    @csrf
                                                    <input type="hidden" name="file"
                                                        value="{{ $file->getRealPath() }}">
                                                    <button class="btn btn-sm btn-primary"
                                                        type="submit">{{ __('locales::general.translate') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
