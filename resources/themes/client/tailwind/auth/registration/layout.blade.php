@extends(Theme::path('auth.wrapper'))

@section('container')
    <section class="bg-white h-screen py-8 dark:bg-gray-900 lg:py-0">
        <div class="mx-auto flex items-center h-full px-4 md:w-[42rem] md:px-8 xl:px-0">
            @yield('content')
        </div>
    </section>
@endsection
