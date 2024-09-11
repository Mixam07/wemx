@extends(Theme::wrapper())
@section('title', __('client.dashboard'))

{{-- Keywords for search engines --}}
@section('keywords', 'WemX Dashboard, WemX Panel')

@section('container')
    <div class="flex flex-wrap ">
        <div class="lg:w-1/4 pr-4 pl-4 md:w-1/3 pr-4 pl-4 sm:w-1/2 pr-4 pl-4 w-full">
            <div class="p-6 mb-6 dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-lg bg-white p-3 leading-6 text-slate-700 shadow-xl shadow-black/5 ring-1 ring-slate-700/10">
                <div class="text-center text-gray-500 dark:text-gray-400">
                    <div class="leading-none text-gray-900 dark:text-gray-200 mb-4" style="display: flex; justify-content: space-between; align-items: center;">
                        {{ __('affiliates::general.total_earned') }}
                        <span class="bg-gray-100 text-gray-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                            {{ currency('symbol') }}{{ number_format($affiliate->balance, 2) }}
                        </span>
                    </div>
                    <div class="leading-none text-gray-900 dark:text-gray-200 mb-4" style="display: flex; justify-content: space-between; align-items: center;">
                        {{ __('affiliates::general.referral_code') }}
                        <span class="bg-gray-100 text-gray-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                            {{ $affiliate->code }}
                        </span>
                    </div>
                    <div class="leading-none text-gray-900 dark:text-gray-200 mb-4" style="display: flex; justify-content: space-between; align-items: center;">
                        {{ __('affiliates::general.created_at') }}
                        <span class="bg-gray-100 text-gray-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                            {{ $affiliate->created_at->format(settings('date_format', 'd M, Y')) }}
                        </span>
                    </div>
                </div>
                    <a
                        href="#"
                        data-modal-target="defaultModal" data-modal-toggle="defaultModal"
                        class="inline-flex items-center justify-center w-full py-2.5 px-5 my-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                    >
                        {{ __('affiliates::general.cashout') }}
                    </a>
            </div>

            <!-- Main modal -->
            <div id="defaultModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative w-full max-w-2xl max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <!-- Modal header -->
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ __('affiliates::general.select_payout_option') }}
                            </h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="defaultModal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">{{ __('affiliates::general.close_modal') }}</span>
                            </button>
                        </div>
                        <form action="{{ route('affiliates.payout') }}" method="POST">
                            @csrf
                            <!-- Modal body -->
                            <div class="p-6 space-y-6">
                                <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                                        @if(in_array('balance', json_decode(settings('affiliates::gateways', '["balance", "paypal", "bitcoin"]'))))
                                        <li class="mr-2" role="presentation">
                                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-tab" data-tabs-target="#profile" onclick="setGateway('balance')" type="button" role="tab" aria-controls="profile" aria-selected="false">
                                                {{ __('affiliates::general.balance') }}
                                            </button>
                                        </li>
                                        @endif
                                        @if(in_array('paypal', json_decode(settings('affiliates::gateways', '["balance", "paypal", "bitcoin"]'))))
                                        <li class="mr-2" role="presentation">
                                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" onclick="setGateway('paypal')" id="dashboard-tab" data-tabs-target="#dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">
                                                {!! __('PayPal') !!}
                                            </button>
                                        </li>
                                        @endif
                                        @if(in_array('bitcoin', json_decode(settings('affiliates::gateways', '["balance", "paypal", "bitcoin"]'))))
                                        <li class="mr-2" role="presentation">
                                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" onclick="setGateway('bitcoin')" id="settings-tab" data-tabs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">
                                                {!! __('Bitcoin') !!}
                                            </button>
                                        </li>
                                        @endif
                                        @if($affiliate->payouts->count() !== 0)
                                        <li class="mr-2" role="presentation">
                                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="payouts-tab" data-tabs-target="#payouts" type="button" role="tab" aria-controls="payouts" aria-selected="false">
                                                {{ __('affiliates::general.my_payouts') }}
                                            </button>
                                        </li>
                                        @endif
                                    </ul>
                                </div>

                                <input type="text" id="gateway" name="gateway" class="hidden" value="balance">

                                <div id="myTabContent">
                                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {!! __('affiliates::general.funds_are_deposited_directly_to_your_account_balan') !!}
                                        </p>
                                    </div>
                                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {!! __('affiliates::general.you_may_request_a_withdrawal_to_your_paypal_accoun') !!}
                                    </p>
                                    <div class="relative mt-4 mb-2">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                        <i class='bx bxl-paypal text-gray-500 dark:text-gray-400 bx-xs' ></i>
                                    </div>
                                    <input type="email" id="input-group-1" name="paypal_email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="example@gmail.com">
                                    </div>
                                </div>
                                <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {!! __('affiliates::general.you_may_request_a_withdrawal_to_your_bitcoin_walle') !!}
                                </p>
                                <div class="relative mt-4 mb-2">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <i class='bx bxl-bitcoin text-gray-500 dark:text-gray-400 bx-xs' ></i>
                                </div>
                                <input type="text" id="input-group-1" name="btc_address" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="">
                                </div>
                            </div>
                            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="payouts" role="tabpanel" aria-labelledby="payouts-tab">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">{{ __('affiliates::general.my_payouts') }}</h5>
                                   </div>
                                   <div class="flow-root">
                                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($affiliate->payouts as $payout)
                                            <li class="py-3 sm:py-4">
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white uppercase">
                                                            (#{{ $payout->id }}) {{ $payout->gateway }} {{ $payout->created_at->format(settings('date_format', 'd M, Y')) }}
                                                        </p>
                                                        <p class="text-sm text-gray-500 truncate dark:text-gray-400 mt-2">
                                                            {{ $payout->address }} <span class="bg-primary-100 text-primary-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full dark:bg-primary-900 dark:text-primary-300 capitalize">{{ $payout->status }}</span>
                                                        </p>
                                                    </div>
                                                    <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                                        {{ currency('symbol') . number_format($payout->amount,2) }}
                                                    </div>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                   </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- Modal footer -->
                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{ __('affiliates::general.payout') }}</button>
                        <button data-modal-hide="defaultModal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">{{ __('affiliates::general.cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="flex items-center p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
        <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <span class="sr-only">{{ __('affiliates::general.info') }}</span>
        <div>
         {!! __('affiliates::general.the_minimum_payout_threshold_is_to_withdraw', ['currency' => currency('symbol'), 'format_settings_2' => number_format(settings('affiliates::minimum_payout', '10'), 2)]) !!}
                </div>
              </div>
        </div>
        <div class="lg:w-3/4 pr-4 pl-4 md:w-2/3 pr-4 pl-4 sm:w-1/2 pr-4 pl-4 w-full">

            <div class="columns-3">
                <div
                    class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex flex-col items-center justify-center">
                        <dt class="mb-2 text-3xl font-extrabold dark:text-gray-200">{{ $affiliate->clicks ?? '0' }}
                        </dt>
                        <dd class="text-gray-500 dark:text-gray-400">{{ __('affiliates::general.clicks') }}</dd>
                    </div>
                </div>

                <div
                    class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex flex-col items-center justify-center">
                        <dt class="mb-2 text-3xl font-extrabold dark:text-gray-200">{{ $affiliate->commission }}%
                        </dt>
                        <dd class="text-gray-500 dark:text-gray-400">{{ __('affiliates::general.comission') }}</dd>
                    </div>
                </div>

                <div class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex flex-col items-center justify-center">
                        <dt class="mb-2 text-3xl font-extrabold dark:text-gray-200">
                            {{ currency('symbol') }}{{ number_format($affiliate->balance, 2) }}
                        </dt>
                        <dd class="text-gray-500 dark:text-gray-400">{!! __('client.balance') !!}</dd>
                    </div>
                </div>
            </div>

            <section class="dark:bg-gray-900 py-3 sm:py-5">

                <div class="chart mb-6">
                    <div class="w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
                    <div class="flex justify-between">
                        <div class="grid gap-4 grid-cols-2">
                            <div>
                              <h5 class="inline-flex items-center text-gray-500 dark:text-gray-400 leading-none font-normal mb-2">{{ __('affiliates::general.clicks') }}
                              </h5>
                              <p class="text-gray-900 dark:text-white text-2xl leading-none font-bold">{{ array_sum($invites) }}</p>
                            </div>
                            <div>
                              <h5 class="inline-flex items-center text-gray-500 dark:text-gray-400 leading-none font-normal mb-2">{{ __('affiliates::general.conversion') }}
                              <svg data-popover-target="cpc-info" data-popover-placement="bottom" class="w-3 h-3 text-gray-400 hover:text-gray-900 dark:hover:text-white cursor-pointer ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                  <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"></path>
                                </svg>
                                <div data-popover="" id="cpc-info" role="tooltip" class="absolute z-10 invisible inline-block text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 w-72 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400" data-popper-placement="bottom" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(203px, 72px);">
                                    <div class="p-3 space-y-2">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('affiliates::general.conversion_rate') }}</h3>
                                        {{ __('affiliates::general.the_conversion_rate_is_the_amount_of_users_invited') }}
                                    </div>
                                    <div data-popper-arrow="" style="position: absolute; left: 0px; transform: translate(139px, 0px);"></div>
                                </div>
                              </h5>
                              <p class="text-gray-900 dark:text-white text-2xl leading-none font-bold">@if(array_sum($invites) !== 0) {{ number_format((array_sum($registrations) / array_sum($invites)) * 100, 1) }}% @else 0.0% @endif</p>
                            </div>
                          </div>
                    </div>
                    <div id="data-series-chart"></div>

                    <div class="mt-4">
                        <label for="search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">{{ __('affiliates::general.search') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class='bx bx-link bx-sm dark:text-gray-200 mr-2'></i>
                            </div>
                            <input type="text" class="block w-full p-4 pl-12 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" id="affiliate-url" disabled value="{{ route('affiliate', $affiliate->code) }}" required>
                            <button type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" id="affiliate-button" onclick="copyAffiliateURL()">{{ __('affiliates::general.copy') }}</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between mt-5">
                        <div class="flex justify-between items-center pt-5">
                        <!-- Button -->
                        <button
                            id="dropdownDefaultButton"
                            data-dropdown-toggle="lastDaysdropdown"
                            data-dropdown-placement="bottom"
                            class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 text-center inline-flex items-center dark:hover:text-white"
                            type="button">
                            {{ __('affiliates::general.last') }} {{ session('affiliates_show_days', 6) + 1 }} {{ __('affiliates::general.days') }}
                            <svg class="w-2.5 m-2.5 ml-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                            </svg>
                        </button>
                            <!-- Dropdown menu -->
                            <div id="lastDaysdropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
                                    <li>
                                        <a href="{{ route('affiliates.days', 2) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">{{ __('affiliates::general.today') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('affiliates.days', 7) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">{{ __('affiliates::general.last_7_days') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('affiliates.days', 30) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                            {{ __('affiliates::general.last_30_days') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('affiliates.days', 90) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                            {{ __('affiliates::general.last_90_days') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    </div>
                    <script>
                    // ApexCharts options and config
                    window.addEventListener("load", function() {
                        let options = {
                        // add data series via arrays, learn more here: https://apexcharts.com/docs/series/
                        series: [
                            {
                            name: "Invites",
                            data: @json($invites),  // Number of invites sent per day/week/month
                            color: "#1A56DB",
                            },
                            {
                            name: "Sign ups",
                            data: @json($registrations),  // Number of people who signed up after being invited per day/week/month
                            color: "#7E3BF2",
                            },
                            {
                            name: "Purchases",
                            data: @json($purchases),  // Number of purchases made by those who signed up per day/week/month
                            color: "#059669",
                            },

                        ],
                        chart: {
                            height: "100%",
                            maxWidth: "100%",
                            type: "area",
                            fontFamily: "Inter, sans-serif",
                            dropShadow: {
                            enabled: false,
                            },
                            toolbar: {
                            show: false,
                            },
                        },
                        tooltip: {
                            enabled: true,
                            x: {
                            show: false,
                            },
                        },
                        legend: {
                            show: true
                        },
                        fill: {
                            type: "gradient",
                            gradient: {
                            opacityFrom: 0.55,
                            opacityTo: 0,
                            shade: "#1C64F2",
                            gradientToColors: ["#1C64F2"],
                            },
                        },
                        dataLabels: {
                            enabled: false,
                        },
                        stroke: {
                            width: 6,
                        },
                        grid: {
                            show: false,
                            strokeDashArray: 4,
                            padding: {
                            left: 2,
                            right: 2,
                            top: 0
                            },
                        },
                        xaxis: {
                            categories: @json($days),
                            labels: {
                            show: false,
                            },
                            axisBorder: {
                            show: false,
                            },
                            axisTicks: {
                            show: false,
                            },
                        },
                        yaxis: {
                            show: false,
                            labels: {
                            formatter: function (value) {
                                return '' + value;
                            }
                            }
                        },

                        }

                        if (document.getElementById("data-series-chart") && typeof ApexCharts !== 'undefined') {
                        const chart = new ApexCharts(document.getElementById("data-series-chart"), options);
                        chart.render();
                        }
                    });

                    function setGateway(gateway) {
                        document.getElementById('gateway').value = gateway;
                    }

                    async function copyAffiliateURL() {
                        const copyText = document.getElementById("affiliate-url").value;
                        navigator.clipboard.writeText(copyText);
                        document.getElementById("affiliate-button").innerHTML = 'Copied';
                    }
                    </script>

                </div>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <div
                        class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4 dark:bg-gray-800">
                        <ul class="flex-wrap hidden text-sm font-medium text-center text-gray-500 md:flex dark:text-gray-400">

                        </ul>
                    </div>
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    {{ __('affiliates::general.status') }}
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    {{ __('affiliates::general.comission_earned') }}
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    {!! __('client.date') !!}
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach (AffiliateInvite::where('affiliate_id', $affiliate->id)->paginate(5) as $invite)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4">
                                    @if($invite->status == 'completed')
                                        <span class="bg-primary-100 text-primary-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full dark:bg-primary-900 dark:text-primary-300">{{ $invite->status }}</span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full dark:bg-yellow-900 dark:text-yellow-300">{{ $invite->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    {{ currency('symbol') }}{{ number_format($invite->affiliate_earnings, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="pl-3">
                                        <div
                                            class="text-base font-semibold text-sm">{{$invite->created_at->translatedFormat('d M Y') }}</div>
                                        <div
                                            class="font-normal text-gray-500">{{ $invite->created_at->diffForHumans() }}</div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ AffiliateInvite::where('affiliate_id', $affiliate->id)->paginate(5)->links(Theme::pagination()) }}
                    </div>
                </div>

                @if(AffiliateInvite::where('affiliate_id', $affiliate->id)->paginate(5)->count() == 0)
                    @include(Theme::path('empty-state'), [ 'title' => 'You haven\'t invited anyone',
                    'description' => 'Invites will appear here after someone has opened your affiliate link'])
                @endif
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection
