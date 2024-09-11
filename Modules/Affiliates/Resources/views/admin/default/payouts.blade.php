@extends(AdminTheme::wrapper(), ['title' => __('admin.payments'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <section class="section">
        <div class="section-body">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('affiliates::general.pavouts') }}</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <tbody>
                                <tr>
                                    <th>{!! __('admin.code') !!}</th>
                                    <th>{!! __('admin.user') !!}</th>
                                    <th>{!! __('admin.amount') !!}</th>
                                    <th>{!! __('admin.status') !!}</th>
                                    <th>{!! __('admin.create_at') !!}</th>
                                    <th class="text-right">{!! __('admin.actions') !!}</th>
                                </tr>

                                @foreach($payouts as $payout)
                                    <tr>
                                        <td><a href="{{ route('affiliates.edit', $payout->affiliate->id) }}">{{ $payout->affiliate->code }}</a></td>
                                        <td>
                                            <a href="{{ route('users.edit', ['user' => $payout->user->id]) }}"
                                               style="display: flex; color: #6c757d">
                                                <img alt="image" src="{{ $payout->user->avatar() }}"
                                                     class="rounded-circle mr-1 mt-1" width="32px" height="32px"
                                                     data-toggle="tooltip" title=""
                                                     data-original-title="{{ $payout->user->first_name }} {{ $payout->user->last_name }}">
                                                <div class="flex">
                                                    {{ $payout->user->username }} <br>
                                                    <small>{{ $payout->user->email }}</small>
                                                </div>
                                            </a>
                                        </td>

                                        <td>{{ currency('symbol') }}{{ number_format($payout->amount, 2) }}</td>

                                        <td>{{ $payout->status }}</td>

                                        <td>{{ $payout->created_at->translatedFormat(settings('date_format', 'd M Y')) }}</td>

                                        <td class="text-right">
                                            <a href="{{ route('affiliates.payouts.edit', ['payouts' => $payout->id]) }}"
                                               class="btn btn-primary">{!! __('admin.manage') !!}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        {{ $payouts->links(AdminTheme::pagination()) }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
