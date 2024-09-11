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
                    <form action="{{ route('locales.translate.save', ['code' => $code]) }}" method="POST">
                        @csrf
                        <input type="hidden" value="{{ $file }}" name="file--path">
                        @php($contentArr = array_merge($contentArr, array_diff_key($source, $contentArr)))
                        @foreach ($contentArr as $k => $v)
                            @include('locales::admin.default.elements.trans-form', [
                                'arr_key' => $k,
                                'arr_value' => $v,
                                'arr_source' => $source[$k] ?? null,
                            ])
                        @endforeach
                        <div class="fixed-bottom d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning m-5"><i class="fas fa-save"
                                                                                 style="font-size: 3em;"></i></button>
                        </div>
                    </form>
                    <hr>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function trans(id) {
            var text = document.getElementById('source-' + id).value;
            const lang = 'en|{{ $code }}';

            $.ajax({
                type: 'POST',
                url: '{{ route("locales.translate.api") }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'text': text,
                    'lang': lang,
                },
                success: function (response) {
                    document.getElementById(id).value = response.translation
                }
            });
        }
    </script>
@endsection
