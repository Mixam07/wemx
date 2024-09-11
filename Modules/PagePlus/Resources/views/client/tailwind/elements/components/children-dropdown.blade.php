@foreach($pages as $page)
    <li>
        @if(!empty(optional($parent)->getTranslation()->content))
            <a href="{{ !empty($parent->getMeta('redirect')) ? $parent->getMeta('redirect') : route($parent->slug) }}"
               class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
               role="menuitem">
                <div class="inline-flex items-center">
                    <div class="mr-2">{!! $parent->getMeta('icon') !!}</div>
                    {{ $parent->getTranslation()->title }}
                </div>
            </a>
            @php($parent = null)
            <hr class="my-2 h-0 border border-t-0 border-solid border-neutral-100 dark:border-white/10"/>
        @endif
        @if($page->childrenCached()->isNotEmpty())
            <button id="dropdownSub-{{ $page->id }}" data-dropdown-toggle="dropdown-{{ $page->id }}"
                    data-dropdown-placement="left-start" type="button"
                    class="flex items-center justify-between w-full block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                <div class="inline-flex items-center">
                    <div class="mr-2">{!! $page->getMeta('icon') !!}</div>
                    {{ $page->getTranslation()->title }}
                </div>
                <i class='bx bx-chevron-left'></i>
            </button>
            <div id="dropdown-{{ $page->id }}"
                 class="hidden z-50 my-4 w-48 text-base list-none bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700">
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownSub-{{ $page->id }}">
                    @includeIf(Theme::moduleView('pageplus', 'elements.components.children-dropdown'), ['parent'=> $page, 'pages' => $page->childrenCached()])
                </ul>
            </div>
        @else
            <a href="{{ !empty($page->getMeta('redirect')) ? $page->getMeta('redirect') : route($page->slug) }}"
               class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
               role="menuitem">
                <div class="inline-flex items-center">
                    <div class="mr-2">{!! $page->getMeta('icon') !!}</div>
                    {{ $page->getTranslation()->title }}
                </div>
            </a>
        @endif
    </li>
@endforeach

