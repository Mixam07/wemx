@php($pages = pages_by_location('navbar-dropdown-left'))

@if(count($pages))
    @foreach($pages as $page)
        @if(!$loop->first)
            <span class="hidden w-px h-5 bg-gray-200 dark:bg-gray-600 md:inline"></span>
        @endif

        @php($page_title = $page->getTranslation()->title)
        @if($page->childrenCached()->isNotEmpty())
            <button type="button" data-dropdown-toggle="pageplus-dropdown-{{ $page->id }}"
                    class="mx-2 inline-flex items-center text-gray-800 dark:text-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-2.5 lg:px-5 py-2.5 mr-2 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800">
                <div class="@empty(!$page_title) mr-2 @endempty">{!! $page->getMeta('icon') !!}</div> {{ $page_title }}
            </button>
            <div
                class="hidden z-50 my-4 w-48 text-base list-none bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700"
                id="pageplus-dropdown-{{ $page->id }}" data-popper-placement="bottom"
                style="position: absolute; inset: 0 auto auto 0; margin: 0; transform: translate(1255px, 60px);">
                <ul class="py-1" role="none">
                    @includeIf(Theme::moduleView('pageplus', 'elements.components.children-dropdown'), ['parent'=> $page, 'pages' => $page->childrenCached()])
                </ul>
            </div>
        @else
            <a href="{{ !empty($page->getMeta('redirect')) ? $page->getMeta('redirect') : route($page->slug) }}"
               class="mx-2 inline-flex items-center text-gray-800 dark:text-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-2.5 lg:px-5 py-2.5 mr-2 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800">
                <div class="@empty(!$page_title) mr-2 @endempty">{!! $page->getMeta('icon') !!}</div> {{ $page_title }}
            </a>
        @endif
        @if ($loop->last)
            <span class="hidden mr-3 w-px h-5 bg-gray-200 dark:bg-gray-600 md:inline"></span>
        @endif
    @endforeach

@endif

