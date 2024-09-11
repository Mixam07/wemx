@php
    use Modules\Locales\Models\Manager;
    $manager = new Manager();
@endphp
<span class="hidden mx-2 w-px h-5 bg-gray-200 dark:bg-gray-600 md:inline"></span>

<button type="button" data-dropdown-toggle="language-dropdown"
        class="inline-flex items-center text-gray-800 dark:text-gray-300 hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-2.5 lg:px-5 py-2.5 mr-2 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800">
    <img alt="{{$manager->getCountryCode(app()->getLocale())}}"
         class="mr-2"
         src="https://flagsapi.com/{{$manager->getCountryCode(app()->getLocale())}}/flat/64.png"
         style="width: 16px;">
    {{ $manager->getCountryName(app()->getLocale()) }}
</button>
<div class="hidden z-50 my-4 w-48 text-base list-none rounded divide-y divide-gray-100 shadow dark:bg-gray-700"
     id="language-dropdown" data-popper-placement="bottom"
     style="position: absolute; inset: 0 auto auto 0; margin: 0; transform: translate(1255px, 60px);">
    <ul class="py-1" role="none">
        @foreach ($manager->getInstalled() as $key => $lang)
            <li>
                <a href="{{ route('toggle.lang', ['lang' => $key]) }}"
                   class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white"
                   role="menuitem">
                    <div class="inline-flex items-center">
                        <img alt="{{$manager->getCountryCode($key)}}" class="mr-2"
                             src="https://flagsapi.com/{{$manager->getCountryCode($key)}}/flat/64.png"
                             style="width: 16px;">
                        {{ $manager->getCountryName($key) }}
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
</div>

<span class="hidden w-px h-5 bg-gray-200 dark:bg-gray-600 md:inline"></span>
