@extends(AdminTheme::wrapper(), ['title' => __('pageplus::messages.translation_page'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title text-center">{!! __('pageplus::messages.translation_page') !!}</h4>
                    <div class="card-tools text-center">
                        <a href="{{ route('admin.pageplus.index') }}" class="btn btn-primary btn-sm">{!! __('pageplus::messages.back') !!}</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pageplus.translate.store', $page->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="locale">{!! __('pageplus::messages.language') !!}</label>
                            <select class="form-control" id="locale" name="locale"
                                    onchange="window.location.href = '{{ route('admin.pageplus.translate', ['page' => $page->id]) }}'+'/'+this.value">
                                @if(Module::isEnabled('locales'))
                                    @foreach(lang_module()->getInstalled() as $key => $lang)
                                        <option @if($locale == $key) selected
                                                @endif value="{{$key}}">{{$lang}}</option>
                                    @endforeach
                                @else
                                    <option value="en">English</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="title">{!! __('pageplus::messages.title') !!}</label>
                            <input type="text" class="form-control" id="title" name="title"
                                   value="{{ optional($page)->getTranslation($locale)->title }}" required>
                        </div>
                        @include(AdminTheme::moduleView('pageplus', 'editor'), ['content' => optional($page)->getTranslation($locale)->content, 'name' => 'content', 'label' => __('pageplus::messages.content')])
                        <button type="submit" class="btn btn-primary">{!! __('pageplus::messages.save_translation') !!}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

