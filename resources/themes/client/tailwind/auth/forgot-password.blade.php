@extends(Theme::path('auth.wrapper'))

@section('title', __('auth.forgot_password'))

@section('container')
    <section class="bg-white dark:bg-gray-900">
        <div class="lg:h-screen">
            <div class="flex w-full h-full items-center justify-center px-4 py-6 sm:px-0 lg:py-0">
                <form method="POST" action="{{ route('forgot-password.send-email') }}" class="w-full max-w-md space-y-4 md:space-y-6 xl:max-w-xl">
                    @csrf
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{!! __('auth.forgot_password') !!}</h1>

                    {{-- include alerts --}}
                    @include(Theme::path('layouts.alerts'))

                    <div>
                        <label for="email"
                            class="mb-2 block text-sm font-medium text-gray-900 dark:text-gray-300">{!! __('auth.email') !!}</label>
                        <input type="email" name="email" id="email"
                            class="focus:ring-primary-500 focus:border-primary-500 block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                            placeholder="{!! __('auth.enter_email') !!}" required="">
                    </div>

                    <button type="submit"
                        class="bg-primary-600 hover:bg-primary-700 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 w-full rounded-lg px-5 py-2.5 text-center text-sm font-medium text-white focus:outline-none focus:ring-4">
                        {!! __('auth.request_reset_password') !!}
                    </button>
                    <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                        {!! __('auth.remember_password') !!}
                        <a href="{{ route('login') }}" class="text-primary-600 dark:text-primary-500 font-medium hover:underline">
                            {!! __('auth.sign_in') !!}
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </section>
@endsection
