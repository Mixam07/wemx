@if(Cache::get('pageplus::editor', 'summernote') == 'summernote')
    @section('css_libraries')
        <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.css')) }}"/>
    @endsection
    @section('js_libraries')
        <script src="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.js')) }}"></script>
    @endsection
@else
    <script src="https://cdn.tiny.cloud/1/@settings('tinymce::key', '')/tinymce/7/tinymce.min.js"
            referrerpolicy="origin"></script>
    @php($plugins = 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion')
    @php($toolbar = 'undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl')
    @php($menubar = 'file edit view insert format tools table help')

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            tinymce.init({
                selector: 'textarea',
                language: @json(app()->getLocale()),
                menubar: @json($menubar),
                plugins: @json($plugins),
                toolbar: @json($toolbar),
                skin: 'oxide-dark',
                content_css: 'dark',
                toolbar_sticky: true,
                imagetools_cors_hosts: ['picsum.photos'],
                editimage_cors_hosts: ['picsum.photos'],
                autosave_ask_before_unload: true,
                autosave_interval: "30s",
                autosave_restore_when_empty: false,
                autosave_retention: "2m",
                image_advtab: true,
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
            });
        } else {
            tinymce.init({
                selector: 'textarea',
                language: @json(app()->getLocale()),
                menubar: @json($menubar),
                plugins: @json($plugins),
                toolbar: @json($toolbar),
                toolbar_sticky: true,
                imagetools_cors_hosts: ['picsum.photos'],
                editimage_cors_hosts: ['picsum.photos'],
                autosave_ask_before_unload: true,
                autosave_interval: "30s",
                autosave_restore_when_empty: false,
                autosave_retention: "2m",
                image_advtab: true,
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
            });
        }

    </script>
@endif


<div class="form-group">
    <div class="d-flex justify-content-between">
        <label for="content">{{ $label }}</label>
        <div class="form-group">
            <label class="custom-switch mt-2">
                <div class="control-label mr-2">Tinymce</div>
                <input type="checkbox" name="editor" class="custom-switch-input" value="1" onchange="window.location.href = '{{ route('admin.pageplus.toggle_editor') }}'"
                       @if(Cache::get('pageplus::editor', 'summernote') == 'tinymce') checked @endif>
                <span class="custom-switch-indicator"></span>
            </label>
        </div>
    </div>
    <textarea class="summernote form-control" id="content" name="{{ $name }}">{{ $content }}</textarea>
</div>

