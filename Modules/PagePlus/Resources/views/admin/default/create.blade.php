@extends(AdminTheme::wrapper(), ['title' => $page ? __('pageplus::messages.edit_page') : __('pageplus::messages.create_page'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title text-center">{{ $page ? __('pageplus::messages.edit_page') : __('pageplus::messages.create_page') }}</h4>
                    <div class="card-tools text-center">
                        <a href="{{ route('admin.pageplus.index') }}" class="btn btn-primary btn-sm">{!! __('pageplus::messages.back') !!}</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pageplus.store') }}" method="POST">
                        @csrf
                        @if($page)
                            <input type="hidden" name="id" value="{{ $page->id }}">
                        @endif
                        <div class="form-group">
                            <label for="title">{!! __('pageplus::messages.title') !!}</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{ old('title', optional($page)->getTranslation(app()->getLocale())->title ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label for="slug">{!! __('pageplus::messages.slug') !!}</label>
                            <input type="text" class="form-control" id="slug" name="slug" placeholder="Slug" value="{{ old('slug', $page->slug ?? '') }}">
                        </div>
                        <div class="form-group" data-toggle="modal" data-target="#IconModal">
                            <label for="icon">{!! __('pageplus::messages.icon') !!}</label>
                            <input class="form-control" id="icon" name="meta[icon]" value="{{ old('meta.icon', optional($page)->getMeta('icon') ?? '') }}" placeholder="<i class='bx bx-info-circle'></i>">
                        </div>
                        <div class="form-group">
                            <label for="order">{!! __('pageplus::messages.order') !!}</label>
                            <input type="number" class="form-control" id="order" name="order" placeholder="Order" value="{{ old('order', $page->order ?? '') }}">
                        </div>
                        <div class="form-group">
                            <label for="parent_id">{!! __('pageplus::messages.parent_page') !!}</label>
                            <select class="form-control" id="parent_id" name="parent_id">
                                <option value="">No Parent</option>
                                @foreach(optional($page)->availableParents() ?? $pages as $parentPage)
                                    @if(!isset($page) || (isset($page) && $page->id !== $parentPage->id))
                                        <option value="{{ $parentPage->id }}" @if(isset($page) && $page->parent_id == $parentPage->id) selected @endif>{{ $parentPage->slug }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="redirect">{!! __('pageplus::messages.redirect_url') !!}</label>
                            <input class="form-control" id="redirect" name="meta[redirect]" value="{{ old('meta.redirect', optional($page)->getMeta('redirect') ?? '') }}">
                        </div>

                        @include(AdminTheme::moduleView('pageplus', 'editor'), ['content' => old('content', optional($page)->getTranslation(app()->getLocale())->content ?? ''), 'name' => 'content', 'label' => 'Content'])
                        @if(!$page or !$page->parent_id)
                        <div class="form-group">
                            <label for="location">{!! __('pageplus::messages.page_location') !!}</label>
                            <select class="form-control" id="location" name="meta[location]">
                                <option value="none" {{ (old('meta.location', optional($page)->getMeta('location')) == 'none') ? 'selected' : '' }}>{!! __('pageplus::messages.no_location') !!}</option>
                                <option value="main-menu" {{ (old('meta.location', optional($page)->getMeta('location')) == 'main-menu') ? 'selected' : '' }}>{!! __('pageplus::messages.navbar') !!}</option>
                                <option value="navbar-dropdown-right" {{ (old('meta.location', optional($page)->getMeta('location')) == 'navbar-dropdown-right') ? 'selected' : '' }}>{!! __('pageplus::messages.header_right') !!}</option>
                                <option value="navbar-dropdown-left" {{ (old('meta.location', optional($page)->getMeta('location')) == 'navbar-dropdown-left') ? 'selected' : '' }}>{!! __('pageplus::messages.header_left') !!}</option>
                                <option value="user-dropdown" {{ (old('meta.location', optional($page)->getMeta('location')) == 'user-dropdown') ? 'selected' : '' }}>{!! __('pageplus::messages.user_dropdown') !!}</option>
                                <option value="app-dropdown" {{ (old('meta.location', optional($page)->getMeta('location')) == 'app-dropdown') ? 'selected' : '' }}>{!! __('pageplus::messages.app_dropdown') !!}</option>
                                <option value="footer-help" {{ (old('meta.location', optional($page)->getMeta('location')) == 'footer-help') ? 'selected' : '' }}>{!! __('pageplus::messages.help_footer') !!}</option>
                                <option value="footer-legal" {{ (old('meta.location', optional($page)->getMeta('location')) == 'footer-legal') ? 'selected' : '' }}>{!! __('pageplus::messages.legal_footer') !!}</option>
                                <option value="footer-resources" {{ (old('meta.location', optional($page)->getMeta('location')) == 'footer-resources') ? 'selected' : '' }}>{!! __('pageplus::messages.resources_footer') !!}</option>
                            </select>
                        </div>
                        @endif

                        <button type="submit" class="btn btn-primary">{{ $page ? __('pageplus::messages.update_page') : __('pageplus::messages.create_page') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="IconModal" tabindex="-1" role="dialog"
         aria-labelledby="IconModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="IconModalLabel">{{ __('admin.select_icon') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('admin.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @foreach(config('utils.icons') as $icon)
                            <div class="col-1 mb-4">
                                <div class="bx-md d-flex justify-content-center"
                                     style="cursor: pointer;" onclick='setIcon("{{ $icon }}")'>
                                    {!! $icon !!}
                                </div>
                            </div>
                        @endforeach
                        <div class="form-group col-md-12 col-12">
                            <label for="custom-icon">{{ __('admin.icon_font') }}</label>
                            <input type="text" name="description" id="custom-icon" value="" class="form-control" required=""/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('admin.close') }}</button>
                    <button type="button" onclick="setPageIcon()" class="btn btn-primary"
                            data-dismiss="modal">{{ __('admin.use_icon') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setIcon(icon) {
            document.getElementById("custom-icon").value = icon;
        }

        function setPageIcon() {
            document.getElementById("icon").value = document.getElementById("custom-icon").value;
        }
    </script>
@endsection
