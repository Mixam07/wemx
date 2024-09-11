<button id="dropdownSubNav-{{ $parent->id }}" data-dropdown-toggle="dropdown-nav-{{ $parent->id }}"
        data-dropdown-placement="right-start" type="button"
        class="flex items-center justify-between w-full block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
    <div class="inline-flex items-center">
        <div class="mr-2">{!! $parent->getMeta('icon') !!}</div>
        {{ $parent->getTranslation()->title }}
    </div>
    <i class='bx bx-chevron-right'></i>
</button>


<div id="dropdown-nav-{{ $parent->id }}"
     class="hidden z-50 my-4 w-48 text-base list-none bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700">
    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownSubNav-{{ $parent->id }}">
        @foreach($parent->childrenCached() as $page)
            @if(!empty(optional($parent)->getTranslation()->content))
                <a href="{{ !empty($parent->getMeta('redirect')) ? $parent->getMeta('redirect') : route($parent->slug) }}"
                   class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                   role="menuitem">
                    <div class="inline-flex items-center">
                        <div class="mr-2">{!! $parent->getMeta('icon') !!}</div>
                        {{ $parent->getTranslation()->title }}
                    </div>
                </a>
                <hr class="my-2 h-0 border border-t-0 border-solid border-neutral-100 dark:border-white/10"/>
                @php($parent = null)
            @endif
            @if($page->childrenCached()->isNotEmpty())
                @include(Theme::moduleView('pageplus', 'elements.components.nav-children-dropdown'), ['parent' => $page])
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
        @endforeach
    </ul>
</div>


