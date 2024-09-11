@php($pages = pages_by_location('main-menu'))
@if(count($pages))
    @foreach($pages as $page)
        <li class="mr-2">
            @if($page->childrenCached()->isNotEmpty())
                <button type="button" data-dropdown-toggle="pageplus-dropdown-{{ $page->id }}"
                        class="{{ is_active($page->slug) }} group inline-flex rounded-t-lg border-b-2 border-gray-50 px-4 py-4 text-center text-sm font-medium text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <span class="mr-2">
                       {!! $page->getMeta('icon') !!}
                    </span>
                    {{ $page->getTranslation()->title }}
                </button>
                <div
                    class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700"
                    style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(186px, 44px);"
                    data-popper-placement="right-start" id="pageplus-dropdown-{{ $page->id }}">
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" role="none">
                        @includeIf(Theme::moduleView('pageplus', 'elements.components.children-dropdown'), ['parent'=> $page, 'pages' => $page->childrenCached()])
                    </ul>
                </div>
            @else
                <a href="{{ !empty($page->getMeta('redirect')) ? $page->getMeta('redirect') : route($page->slug) }}"
                   class="{{ is_active($page->slug) }} group inline-flex rounded-t-lg border-b-2 border-gray-50 px-4 py-4 text-center text-sm font-medium text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <span class="mr-2">
                        {!! $page->getMeta('icon') !!}
                    </span>
                    {{ $page->getTranslation()->title }}
                </a>
            @endif
        </li>
    @endforeach
@endif

