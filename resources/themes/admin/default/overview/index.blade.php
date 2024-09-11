@extends(AdminTheme::wrapper(), ['title' => __('admin.overview'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('css_libraries')
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.css')) }}"/>
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/select2/dist/css/select2.min.css')) }}">
@endsection

@section('js_libraries')
    <script src="{{ Theme::get('Default')->assets }}assets/modules/jquery.sparkline.min.js"></script>
    <script src="{{ Theme::get('Default')->assets }}assets/modules/chart.min.js"></script>

    <script>
        "use strict";

        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($paid_dates),
                datasets: [{
                    label: 'Unpaid Payments',
                    data: @json($unpaid_amounts),
                    borderWidth: 2,
                    backgroundColor: 'rgba(63,82,227,.8)',
                    borderWidth: 0,
                    borderColor: 'transparent',
                    pointBorderWidth: 0,
                    pointRadius: 3.5,
                    pointBackgroundColor: 'transparent',
                    pointHoverBackgroundColor: 'rgba(63,82,227,.8)',
                },
                    {
                        label: 'Paid Payments',
                        data: @json($paid_amounts),
                        borderWidth: 2,
                        backgroundColor: 'rgba(254,86,83,.7)',
                        borderWidth: 0,
                        borderColor: 'transparent',
                        pointBorderWidth: 0,
                        pointRadius: 3.5,
                        pointBackgroundColor: 'transparent',
                        pointHoverBackgroundColor: 'rgba(254,86,83,.8)',
                    }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            // display: false,
                            drawBorder: false,
                            color: '#f2f2f2',
                        },
                        ticks: {
                            beginAtZero: true,
                            stepSize: {{ collect($unpaid_amounts)->sum() }},
                            callback: function (value, index, values) {
                                return '{{ currency('symbol') }}' + parseFloat(value).toFixed(1);
                            }
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false,
                            tickMarkLength: 15,
                        }
                    }]
                },
            }
        });

        var balance_chart = document.getElementById("balance-chart").getContext('2d');

        var balance_chart_bg_color = balance_chart.createLinearGradient(0, 0, 0, 70);
        balance_chart_bg_color.addColorStop(0, 'rgba(63,82,227,.2)');
        balance_chart_bg_color.addColorStop(1, 'rgba(63,82,227,0)');

        var myChart = new Chart(balance_chart, {
            type: 'line',
            data: {
                labels: @json($paid_dates),
                datasets: [{
                    label: 'Balance',
                    data: @json($paid_amounts),
                    backgroundColor: balance_chart_bg_color,
                    borderWidth: 3,
                    borderColor: 'rgba(63,82,227,1)',
                    pointBorderWidth: 0,
                    pointBorderColor: 'transparent',
                    pointRadius: 3,
                    pointBackgroundColor: 'transparent',
                    pointHoverBackgroundColor: 'rgba(63,82,227,1)',
                }]
            },
            options: {
                layout: {
                    padding: {
                        bottom: -1,
                        left: -1
                    }
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            beginAtZero: true,
                            display: false
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            drawBorder: false,
                            display: false,
                        },
                        ticks: {
                            display: false
                        }
                    }]
                },
            }
        });

        var subscription_chart = document.getElementById("subscription-chart").getContext('2d');

        var subscription_chart_bg_color = subscription_chart.createLinearGradient(0, 0, 0, 70);
        subscription_chart_bg_color.addColorStop(0, 'rgba(63,82,227,.2)');
        subscription_chart_bg_color.addColorStop(1, 'rgba(63,82,227,0)');

        var myChart = new Chart(subscription_chart, {
            type: 'line',
            data: {
                labels: @json($paid_dates_sub),
                datasets: [{
                    label: 'Subscriptions',
                    data: @json($paid_amounts_sub),
                    backgroundColor: subscription_chart_bg_color,
                    borderWidth: 3,
                    borderColor: 'rgba(63,82,227,1)',
                    pointBorderWidth: 0,
                    pointBorderColor: 'transparent',
                    pointRadius: 3,
                    pointBackgroundColor: 'transparent',
                    pointHoverBackgroundColor: 'rgba(63,82,227,1)',
                }]
            },
            options: {
                layout: {
                    padding: {
                        bottom: -1,
                        left: -1
                    }
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            beginAtZero: true,
                            display: false
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            drawBorder: false,
                            display: false,
                        },
                        ticks: {
                            display: false
                        }
                    }]
                },
            }
        });

        var sales_chart = document.getElementById("sales-chart").getContext('2d');

        var sales_chart_bg_color = sales_chart.createLinearGradient(0, 0, 0, 80);
        balance_chart_bg_color.addColorStop(0, 'rgba(63,82,227,.2)');
        balance_chart_bg_color.addColorStop(1, 'rgba(63,82,227,0)');

        var myChart = new Chart(sales_chart, {
            type: 'line',
            data: {
                labels: @json($registration_dates),
                datasets: [{
                    label: 'users',
                    data: @json($registration_counts),
                    borderWidth: 2,
                    backgroundColor: balance_chart_bg_color,
                    borderWidth: 3,
                    borderColor: 'rgba(63,82,227,1)',
                    pointBorderWidth: 0,
                    pointBorderColor: 'transparent',
                    pointRadius: 3,
                    pointBackgroundColor: 'transparent',
                    pointHoverBackgroundColor: 'rgba(63,82,227,1)',
                }]
            },
            options: {
                layout: {
                    padding: {
                        bottom: -1,
                        left: -1
                    }
                },
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            beginAtZero: true,
                            display: false
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            drawBorder: false,
                            display: false,
                        },
                        ticks: {
                            display: false
                        }
                    }]
                },
            }
        });

        $("#products-carousel").owlCarousel({
            items: 3,
            margin: 10,
            autoplay: true,
            autoplayTimeout: 5000,
            loop: true,
            responsive: {
                0: {
                    items: 2
                },
                768: {
                    items: 2
                },
                1200: {
                    items: 3
                }
            }
        });
    </script>
@endsection

@section('container')
    <section class="section">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title">{!! __('admin.orders', ['default' => 'Orders']) !!}
                        </div>
                        <div class="card-stats-items">
                            <div class="card-stats-item">
                                <div class="card-stats-item-count">{{ Order::whereStatus('cancelled')->count() }}</div>
                                <div
                                    class="card-stats-item-label">{!! __('admin.orders_cancelled', ['default' => 'Cancelled']) !!}</div>
                            </div>
                            <div class="card-stats-item">
                                <div class="card-stats-item-count">{{ Order::whereStatus('suspended')->count() }}</div>
                                <div
                                    class="card-stats-item-label">{!! __('admin.orders_suspended', ['default' => 'Suspended']) !!}</div>
                            </div>
                            <div class="card-stats-item">
                                <div class="card-stats-item-count">{{ Order::whereStatus('terminated')->count() }}</div>
                                <div
                                    class="card-stats-item-label">{!! __('admin.orders_terminated', ['default' => 'Terminated']) !!}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{!! __('admin.orders_total_active', ['default' => 'Total Active Orders']) !!}</h4>
                        </div>
                        <div class="card-body">
                            {{ Order::whereStatus('active')->count() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-chart">
                        <div class="chartjs-size-monitor"
                             style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                            <div class="chartjs-size-monitor-expand"
                                 style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink"
                                 style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                            </div>
                        </div>
                        <canvas id="balance-chart" height="80" width="339"
                                style="display: block; width: 339px; height: 80px;"
                                class="chartjs-render-monitor"></canvas>
                    </div>
                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{!! __('admin.revenue') !!}</h4>
                        </div>
                        <div class="card-body">
                            {{ currency('symbol') }}{{ number_format(collect($paid_amounts)->sum(), 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-chart">
                        <div class="chartjs-size-monitor"
                             style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                            <div class="chartjs-size-monitor-expand"
                                 style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink"
                                 style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                            </div>
                        </div>
                        <canvas id="subscription-chart" height="80" width="339"
                                style="display: block; width: 339px; height: 80px;"
                                class="chartjs-render-monitor"></canvas>
                    </div>
                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{!! __('client.subscription') !!}</h4>
                        </div>
                        <div class="card-body">
                            {{ currency('symbol') }}{{ $incomeSubscriptions }} / {{request()->input('period', 30)}} {!! __('admin.days') !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-chart">
                        <div class="chartjs-size-monitor"
                             style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                            <div class="chartjs-size-monitor-expand"
                                 style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink"
                                 style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                            </div>
                        </div>
                        <canvas id="sales-chart" height="80" width="339"
                                style="display: block; width: 339px; height: 80px;"
                                class="chartjs-render-monitor"></canvas>
                    </div>
                    <div class="card-icon shadow-primary bg-primary">
                        <i class="fas fa-solid fa-user"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{!! __('admin.new_users', ['default' => 'New Users']) !!}</h4>
                        </div>
                        <div class="card-body">
                            {{ collect($registration_counts)->sum() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div
                            class="card-stats-title">{!! __('admin.order_statistics', ['default' => 'Order Statistics -']) !!}
                            <div class="dropdown d-inline">
                                <a class="font-weight-600 dropdown-toggle" data-toggle="dropdown" href="#"
                                   id="orders-month">{!! __('admin.statistics_period_days', ['default' => 'Last :days days', 'days' => request()->input('period', 30)]) !!}</a>
                                <ul class="dropdown-menu dropdown-menu-sm">
                                    <li class="dropdown-title">{!!  __('admin.statistics_select_period', ['default' => 'Select Period']) !!}
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.view', ['period' => 7]) }}"
                                           class="dropdown-item @if(request()->input('period', 30) == 7) active @endif">
                                            {!! __('admin.statistics_period_days', ['default' => 'Last :days days', 'days' => '7']) !!}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.view', ['period' => 30]) }}"
                                           class="dropdown-item @if(request()->input('period', 30) == 30) active @endif">
                                            {!! __('admin.statistics_period_days', ['default' => 'Last :days days', 'days' => '30']) !!}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.view', ['period' => 60]) }}"
                                           class="dropdown-item @if(request()->input('period', 30) == 60) active @endif">
                                            {!! __('admin.statistics_period_days', ['default' => 'Last :days days', 'days' => '60']) !!}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.view', ['period' => 90]) }}"
                                           class="dropdown-item @if(request()->input('period', 30) == 90) active @endif">
                                            {!! __('admin.statistics_period_days', ['default' => 'Last :days days', 'days' => '90']) !!}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.view', ['period' => 365]) }}"
                                           class="dropdown-item @if(request()->input('period', 30) == 365) active @endif">
                                            {!! __('admin.statistics_period_days', ['default' => 'Last :days days', 'days' => '365']) !!}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.view', ['period' => 2000]) }}"
                                           class="dropdown-item @if(request()->input('period', 30) == 2000) active @endif">
                                            {!! __('admin.statistics_all_time', ['default' => 'All Time']) !!}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chartjs-size-monitor"
                             style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;">
                            <div class="chartjs-size-monitor-expand"
                                 style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div>
                            </div>
                            <div class="chartjs-size-monitor-shrink"
                                 style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;">
                                <div style="position:absolute;width:200%;height:200%;left:0; top:0"></div>
                            </div>
                        </div>
                        <canvas id="myChart" height="336" style="display: block; width: 638px; height: 336px;"
                                width="638" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4><a href="{{ route('users.index', ['sort' => 'online']) }}">{!! __('admin.online_users') !!}</a></h4>
                        <div class="card-header-action">

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="summary">
                            <div class="summary-item">
                                <ul class="list-unstyled list-unstyled-border">
                                    @foreach(User::getOnlineUsers()->paginate(5) as $user)
                                        <li class="media">
                                            <a href="{{ route('users.edit', ['user' => $user->id]) }}">
                                                <img class="mr-3 rounded" width="50" src="{{ $user->avatar() }}"
                                                     alt="{{ __('admin.avatar') }}">
                                            </a>
                                            <div class="media-body">
                                                {{-- <div class="media-right">
                                                    <span
                                                        class="text-primary">
                                                        {!! __('client.online') !!}
                                                    </span>
                                                </div> --}}
                                                <div class="media-title"><a
                                                        href="{{ route('users.edit', ['user' => $user->id]) }}">{{ $user->username }}</a>
                                                </div>
                                                <div class="text-muted text-small"><a
                                                        href="{{ route('users.edit', ['user' => $user->id]) }}">{{ $user->first_name }} {{ $user->last_name }}</a>
                                                    <div
                                                        class="bullet"></div> {{ $user->last_seen_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>{!! __('admin.recent_registrations', ['default' => 'Recent Registrations']) !!}</h4>
                        <div class="card-header-action">

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="summary">
                            <div class="summary-item">
                                <ul class="list-unstyled list-unstyled-border">
                                    @foreach(User::latest()->paginate(5) as $user)
                                        <li class="media">
                                            <a href="{{ route('users.edit', ['user' => $user->id]) }}">
                                                <img class="mr-3 rounded" width="50" src="{{ $user->avatar() }}"
                                                     alt="{{ __('admin.avatar') }}">
                                            </a>
                                            <div class="media-body">
                                                <div class="media-right">
                                                    <span
                                                        class="@if($user->status == 'pending') text-warning
                                                        @elseif($user->status == 'suspended') text-danger
                                                        @else text-primary @endif">
                                                        {!! __('admin.' . $user->status) !!}
                                                    </span>
                                                </div>
                                                <div class="media-title"><a
                                                        href="{{ route('users.edit', ['user' => $user->id]) }}">{{ $user->username }}</a>
                                                </div>
                                                <div class="text-muted text-small"><a
                                                        href="{{ route('users.edit', ['user' => $user->id]) }}">{{ $user->first_name }} {{ $user->last_name }}</a>
                                                    <div
                                                        class="bullet"></div> {{ $user->created_at->translatedFormat(settings('date_format', 'd M Y')) }}
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
        </div>
    </section>
@endsection
