<!-- drawer component -->
<div id="drawer-example"
     class="fixed left-0 top-0 z-40 h-screen w-80 -translate-x-full overflow-y-auto bg-white p-4 transition-transform dark:bg-gray-800"
     tabindex="-1" aria-labelledby="drawer-label">
    <h5 id="drawer-label"
        class="mb-4 inline-flex items-center text-base font-semibold text-gray-500 dark:text-gray-400">
        <svg
            class="mr-2 h-5 w-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                  clip-rule="evenodd"></path>
        </svg>{!! __('client.info') !!}</h5>
    <button type="button" data-drawer-hide="drawer-example" aria-controls="drawer-example"
            class="absolute right-2.5 top-2.5 inline-flex items-center rounded-lg bg-transparent p-1.5 text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900 dark:hover:bg-gray-600 dark:hover:text-white">
        <svg aria-hidden="true" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
             xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"></path>
        </svg>
        <span class="sr-only">{!! __('client.close_menu') !!}</span>
    </button>
    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
        {!! __('client.balance_sidebar_desc') !!}
    </p>

    <form action="{{ route('balance.add') }}" method="POST">
        @csrf
        <div class="mb-4">
            <h3 class="mb-5 text-lg font-medium text-gray-900 dark:text-white">{!! __('client.balance_how_add') !!}</h3>
            <ul class="grid w-full gap-6 md:grid-cols-2">
                <li>
                    <input type="radio" id="5" value="5" onclick="preset(5)" name="preselected_amount"
                           class="peer hidden"
                           checked="">
                    <label for="5"
                           class="inline-flex w-full cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-600 peer-checked:border-blue-600 peer-checked:text-blue-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:peer-checked:text-blue-500">
                        <div class="block">
                            <div class="w-full text-lg font-semibold">{{ currency('symbol') }}5.00</div>
                        </div>
                    </label>
                </li>
                <li>
                    <input type="radio" id="10" value="10" onclick="preset(10)" name="preselected_amount"
                           class="peer hidden">
                    <label for="10"
                           class="inline-flex w-full cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-600 peer-checked:border-blue-600 peer-checked:text-blue-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:peer-checked:text-blue-500">
                        <div class="block">
                            <div class="w-full text-lg font-semibold">{{ currency('symbol') }}10.00</div>
                        </div>
                    </label>
                </li>
                <li>
                    <input type="radio" id="20" value="20" onclick="preset(20)" name="preselected_amount"
                           class="peer hidden">
                    <label for="20"
                           class="inline-flex w-full cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-600 peer-checked:border-blue-600 peer-checked:text-blue-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:peer-checked:text-blue-500">
                        <div class="block">
                            <div class="w-full text-lg font-semibold">{{ currency('symbol') }}20.00</div>
                        </div>
                    </label>
                </li>
                <li>
                    <input type="radio" id="50" value="50" onclick="preset(50)" name="preselected_amount"
                           class="peer hidden">
                    <label for="50"
                           class="inline-flex w-full cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-600 peer-checked:border-blue-600 peer-checked:text-blue-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:peer-checked:text-blue-500">
                        <div class="block">
                            <div class="w-full text-lg font-semibold">{{ currency('symbol') }}50.00</div>
                        </div>
                    </label>
                </li>
            </ul>

            <div class="relative mb-6 mt-6">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2"
                         stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1 -1.8 -1"/>
                        <path d="M12 6v2m0 8v2"/>
                    </svg>
                </div>
                <input type="number" id="amount" name="amount"
                       class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pl-10 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                       value="5">
            </div>

            <div class="">
                <select
                    class="mb-6 block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                    name="gateway" tabindex="-1" aria-hidden="true" required>
                    @foreach (App\Models\Gateways\Gateway::getActive() as $gateway)
                        @if ($gateway->driver == 'Balance')
                            @continue
                        @endif
                        <option value="{{ $gateway->id }}">{{ $gateway->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="">
            <button type="submit"
                    class="bg-primary-700 hover:bg-primary-800 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 mb-2 mr-2 w-full rounded-lg px-5 py-2.5 text-sm font-medium text-white focus:outline-none focus:ring-4">{!! __('client.pay_now') !!}</button>
        </div>
    </form>
</div>
<!-- drawer component -->

{{-- User Profile popup --}}
<div data-popover id="popover-user-profile" role="tooltip"
     class="invisible absolute z-10 inline-block w-64 rounded-lg border border-gray-200 bg-white text-sm text-gray-500 opacity-0 shadow-sm transition-opacity duration-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400">
    <div class="p-3">
        <div class="mb-2 flex items-center justify-between">
            <a href="#">
                {{--                <img class="w-10 h-10 rounded-full" src="/docs/images/people/profile-picture-1.jpg" alt="Jese Leos"> --}}
            </a>
            <div>
                <button type="button"
                        class="rounded-lg bg-blue-700 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{ __('client.follow') }}</button>
            </div>
        </div>
        <p class="text-base font-semibold leading-none text-gray-900 dark:text-white">
            <a href="#">Jese Leos</a>
        </p>
        <p class="mb-3 text-sm font-normal">
            <a href="#" class="hover:underline">@jeseleos</a>
        </p>
        <p class="mb-4 text-sm">{!! __('client.opensource_contributor_building_flowbitecom') !!}</p>
        <ul class="flex text-sm">
            <li class="mr-2">
                <a href="#" class="hover:underline">
                    <span class="font-semibold text-gray-900 dark:text-white">799</span>
                    <span>{{ __('client.following') }}</span>
                </a>
            </li>
            <li>
                <a href="#" class="hover:underline">
                    <span class="font-semibold text-gray-900 dark:text-white">3,758</span>
                    <span>{{ __('client.followers') }}</span>
                </a>
            </li>
        </ul>
    </div>
    <div data-popper-arrow></div>
</div>

<script>
    function preset(amount) {
        element = document.getElementById("amount").value = amount;
    }
</script>

@include(Theme::path('layouts.cookie'))

{{-- footer --}}
<footer class="background-gray p-2 mt-auto">
    <div class="container mx-auto">
        <div class="flex justify-between items-center flex-col-reverse md:flex-row">
            <a href="/Terms_and_conditions" class="text-center text-slate-400 text-decoration-none fs-6">
                {!! __('client.terms_n_conditions') !!}
            </a>
            <p class="text-center text-slate-400 fs-6">
                {!! __('client.copyright') !!}
            </p>
            <a href="/Privacy_policy" class="text-center text-slate-400 text-decoration-none fs-6">
                {!! __('client.privacy_policy') !!}
            </a>
        </div>
    </div>
</footer>
{{-- end footer --}}

<!-- Place your JavaScript code here -->
@if (session('code'))
    {!! session('code') !!}
@endif

<script>
    function toggleDarkmode() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        }
    }
</script>

<script>
    async function copyToClipboard(button) {
        try {
            let textToCopy = button.textContent.trim();
            let tempInput = document.createElement("input");
            document.body.appendChild(tempInput);
            tempInput.value = textToCopy;
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
            button.innerText = '{!! __('client.copied') !!}';
            setTimeout(function () {
                button.innerText = textToCopy;
            }, 3000);
            console.log('Text copied to clipboard');
        } catch (err) {
            console.error('Error in copying text: ', err);
        }
    }
</script>
