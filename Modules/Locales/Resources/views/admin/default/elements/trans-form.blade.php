@if (is_array($arr_value))
    @foreach ($arr_value as $key => $value)
        @php
            if (strpos($arr_key, '--') !== false) {
                $source_key = explode('--', $arr_key);
                array_push($source_key, $key);
                $_source = $source;
                foreach ($source_key as $kk) {
                    $_source = $_source[$kk];
                }
                $arr_value = $value;
            } else {
                $_source = $source[$arr_key][$key];
            }
        @endphp

        @include(AdminTheme::moduleView('Locales', 'elements.trans-form'), [
            'arr_key' => $arr_key . '--' . $key,
            'arr_value' => $value,
            'arr_source' => $_source,
        ])
    @endforeach
@else
    <div class="form-group">
        <label for="{{ $arr_key }}">{{ $arr_key }}</label>
        <div class="input-group">

            <input disabled type="text" class="form-control" id="source-{{ $arr_key }}" value="{{ $arr_source }}">
            <input type="text" class="form-control" id="{{ $arr_key }}" name="{{ $arr_key }}"
                value="{{ $arr_value }}">
            <div class="input-group-append">
                <button class="btn btn-primary" onclick="trans('{{ $arr_key }}')"
                    type="button">{{ __('locales::general.translate') }}</button>
            </div>
        </div>
    </div>
@endif
