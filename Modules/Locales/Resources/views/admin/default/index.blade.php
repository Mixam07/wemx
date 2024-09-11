@extends(AdminTheme::wrapper())

@section('title')
    {{ __('locales::general.title') }}
@endsection

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('locales::general.header') }}</div>
{{--                @if(env('APP_DEBUG'))--}}
{{--                    @lang('locales::general.develop_info')--}}
{{--                @endif--}}
                <div class="card-body">
                    <button class="btn btn-sm btn-primary" data-toggle="modal"
                            data-target="#generateLang">{{ __('locales::general.generate_new') }}</button>

                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <th>{{ __('locales::general.locale_code') }}</th>
                                <th>{{ __('locales::general.locale_name') }}</th>
                                <th>{{ __('locales::general.locale_path') }}</th>
                                <th class="text-right">{{ __('locales::general.locale_actions') }}</th>
                            </tr>
                            @foreach ($list as $key => $lang)
                                @if (isset($lang))
                                    <tr>
                                        <td><code>{{ $key }}</code></td>
                                        <td>{{ $lang }}</td>
                                        <td><code>{{ resource_path('lang') }}/{{ $key }}/</code>
                                        </td>
                                        <td class="text-right">
                                            <a class="btn btn-primary btn-sm"
                                               href="{{ route('locales.translate', ['code' => $key]) }}">{{ __('locales::general.translate') }}</a>
                                            <a onclick="deleteItem(event)"
                                               href="{{ route('locales.remove', ['code' => $key]) }}"
                                               class="btn btn-primary btn-sm">{{ __('locales::general.remove') }}</a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>
    </div>




    {{-- Modals --}}
    <div class="modal fade" id="generateLang" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('locales.generate') }}" method="POST">
                    <div class="modal-header">
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="lang_code"
                                       class="form-label">{{ __('locales::general.select_localisation') }}</label>
                                <select class="form-control" required name="lang_code" id="lang_code">
                                    @foreach ($localizations as $key => $lang)
                                        <option value="{{ $key }}">{{ $lang }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {!! csrf_field() !!}
                        <button type="button" class="btn btn-default btn-sm pull-left"
                                data-dismiss="modal">{{ __('locales::general.cancel') }}</button>
                        <button type="submit"
                                class="btn btn-success btn-sm">{{ __('locales::general.generate') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function confirm_delete() {
            return confirm('Are you sure?');
        }
    </script>
@endsection
