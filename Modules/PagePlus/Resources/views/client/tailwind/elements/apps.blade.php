@php($pages = pages_by_location('app-dropdown'))
@if(count($pages))
    @foreach($pages as $page)
        <a href="{{ !empty($page->getMeta('redirect')) ? $page->getMeta('redirect') : route($page->slug) }}"
           class="group block rounded-lg p-4 text-center hover:bg-gray-100 dark:hover:bg-gray-600">
            <div class="mx-auto mb-1 text-3xl text-gray-400 group-hover:text-gray-500 dark:text-gray-400 dark:group-hover:text-gray-400">
                {!! $page->getMeta('icon') !!}
            </div>
            <div class="text-sm text-gray-900 dark:text-white"> {{ $page->getTranslation()->title }}</div>
        </a>
    @endforeach
@endif

