<script src="https://cdn.tiny.cloud/1/gtpqk4bhodntkjtjhc1iggqd0om3gkf8l1opje9m2uhjk0k8/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
@php($plugins = 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons')
@php($toolbar = 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl')
@php($menubar = 'file edit view insert format tools table help')
<script>
    @if(Cache::get('admin_theme_mode_'.auth()->user()->id, 'light') == 'dark')
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
            autosave_ask_before_unload: true,
            autosave_interval: "30s",
            autosave_restore_when_empty: false,
            autosave_retention: "2m",
            image_advtab: true,
            max_chars: 1000,
        });
    @else
        tinymce.init({
            selector: 'textarea',
            language: @json(app()->getLocale()),
            menubar: @json($menubar),
            plugins: @json($plugins),
            toolbar: @json($toolbar),
            toolbar_sticky: true,
            imagetools_cors_hosts: ['picsum.photos'],
            autosave_ask_before_unload: true,
            autosave_interval: "30s",
            autosave_restore_when_empty: false,
            autosave_retention: "2m",
            image_advtab: true,
            max_chars: 1000,
        });
    @endif
</script>