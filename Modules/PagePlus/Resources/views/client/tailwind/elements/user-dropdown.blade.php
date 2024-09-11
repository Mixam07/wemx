@php($pages = pages_by_location('user-dropdown'))
@if(count($pages))
    @foreach($pages as $page)
        <li>
            @if($page->childrenCached()->isNotEmpty())
                <button type="button" data-dropdown-toggle="pageplus-dropdown-{{ $page->id }}"
                        data-dropdown-placement="right-start"
                        class="flex w-full items-center justify-between px-4 py-2 text-sm hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                    <span class="flex items-center">
                       <span class="mr-1">{!! $page->getMeta('icon') !!}</span> {{ $page->getTranslation()->title }}
                    </span>
                    <svg aria-hidden="true" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                              clip-rule="evenodd"></path>
                    </svg>
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
                   class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                    <span class="mr-2 text-xl text-gray-400">
                        {!! $page->getMeta('icon') !!}
                    </span>
                    {{ $page->getTranslation()->title }}
                </a>
            @endif
        </li>
    @endforeach
@endif

