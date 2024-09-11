@extends(Theme::wrapper())
@section('title', $page->getTranslation()->title)
@section('container')
    <div class="flex flex-col lg:flex-row gap-4">
        @if($page->getTopmostAncestor() and $page->getTopmostAncestor()->childrenCached()->isNotEmpty())
            @php($ancestor = $page->getTopmostAncestor())
            <div class="lg:w-1/6 lg:flex-shrink-0">
                <div class="mb-6 rounded-lg bg-white p-3 p-6 leading-6 text-gray-500 text-slate-700 shadow-xl shadow-black/5 ring-1 ring-slate-700/10 dark:bg-gray-800 dark:text-gray-400">
                    <a href="{{ !empty($ancestor->getMeta('redirect')) ? $ancestor->getMeta('redirect') : route($ancestor->slug) }}"
                       class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                       role="menuitem">
                        <div class="inline-flex items-center">
                            <div class="mr-2">{!! $ancestor->getMeta('icon') !!}</div>
                            {{ $ancestor->getTranslation()->title }}
                        </div>
                    </a>
                    @foreach($ancestor->childrenCached() as $subpage)
                        @if($subpage->childrenCached()->isNotEmpty())
                            @include(Theme::moduleView('pageplus', 'elements.components.nav-children-dropdown'), ['parent' => $subpage])
                        @else
                            <a href="{{ !empty($subpage->getMeta('redirect')) ? $subpage->getMeta('redirect') : route($subpage->slug) }}"
                               class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                               role="menuitem">
                                <div class="inline-flex items-center">
                                    <div class="mr-2">{!! $subpage->getMeta('icon') !!}</div>
                                    {{ $subpage->getTranslation()->title }}
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <div class="w-full min-h-screen">
            <div class="mb-6 rounded-lg bg-white p-3 p-6 leading-6 text-gray-500 text-slate-700 shadow-xl shadow-black/5 ring-1 ring-slate-700/10 dark:bg-gray-800 dark:text-gray-400">
                <div class="text-center text-gray-500 dark:text-gray-400">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ $page->getTranslation()->title }}
                    </h1>
                </div>
            </div>
            <div class="dark:bg-gray-800 rounded-lg text-gray-500 dark:text-gray-400 p-4">
                {!! $page->getTranslation()->content !!}
            </div>
        </div>
    </div>
@endsection

