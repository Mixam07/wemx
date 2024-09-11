@extends(AdminTheme::wrapper(), ['title' => __('admin.payments'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('css_libraries')
    <link rel="stylesheet"
          href="{{ asset(AdminTheme::assets('modules/bootstrap-daterangepicker/daterangepicker.css')) }}">
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.css')) }}"/>
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/select2/dist/css/select2.min.css')) }}">
@endsection

@section('js_libraries')
    <script src="{{ asset(AdminTheme::assets('modules/bootstrap-daterangepicker/daterangepicker.js')) }}"></script>
    <script src="{{ asset(AdminTheme::assets('modules/select2/dist/js/select2.full.min.js')) }}"></script>
    <script src="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.js')) }}"></script>
@endsection

@section('container')
    <section class="section">
        <div class="section-body">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-info">
                                <i class="fas fa-solid fa-link"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>{{ __('affiliates::general.referral_code') }}</h4>
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('affiliates.edit', $affiliate->id) }}">{{ $affiliate->code }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fas fa-solid fa-users"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>{{ __('affiliates::general.total_clicks') }}</h4>
                                </div>
                                <div class="card-body">
                                    {{ $affiliate->clicks }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                                <i class="fas fa-solid fa-handshake"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>{{ __('affiliates::general.commission') }}</h4>
                                </div>
                                <div class="card-body">
                                    {{ $affiliate->commission }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="fas fa-solid fa-dollar-sign"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>{{ __('affiliates::general.balance') }}</h4>
                                </div>
                                <div class="card-body">
                                    {{ currency('symbol') }}{{ number_format($affiliate->balance, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-12 col-lg-8">
                <form action="{{ route('affiliates.payouts.update', $payout->id) }}" method="POST">
                    @csrf
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('affiliates::general.update_payout') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12 col-12" style="display: flex;align-items: center;">
                                <img class="mr-3 rounded-circle" width="50" src="{{ $affiliate->user->avatar() }}" alt="avatar">
                                <div class="media-body">
                                    <h6 class="media-title"><a href="{{ route('users.edit', $affiliate->user->id) }}">{{ $affiliate->user->first_name }} {{ $affiliate->user->last_name }}</a></h6>
                                    <div class="text-small text-muted">
                                        {{ $affiliate->user->email }}
                                        <div class="bullet"></div>
                                        <span class="text-primary">{{ __('affiliates::general.joined') }} {{ $affiliate->user->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group col-md-6 col-12">
                                <label for="balance">{{ __('affiliates::general.payout_amount') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            {{ currency('symbol') }}
                                        </div>
                                    </div>
                                    <input type="number" class="form-control" name="amount"
                                    step="0.01" value="{{ number_format($payout->amount, 2) }}" required="">
                                </div>
                            </div>

                            <div class="form-group col-md-6 col-12">
                                <label for="user">{{ __('affiliates::general.status') }}</label>
                                <select class="form-control select2 select2-hidden-accessible" name="status" tabindex="-1"
                                        aria-hidden="true">
                                        <option value="pending" @if($payout->status == 'pending') selected="" @endif>
                                            {{ __('affiliates::general.pending') }}
                                        </option>
                                        <option value="cancelled" @if($payout->status == 'cancelled') selected="" @endif>
                                            {{ __('affiliates::general.cancelled') }}
                                        </option>
                                        <option value="completed" @if($payout->status == 'completed') selected="" @endif>
                                            {{ __('affiliates::general.completed') }}
                                        </option>
                                </select>
                            </div>

                            <div class="form-group col-md-12 col-12">
                                <label>{{ __('affiliates::general.payout_gateway') }}</label>
                                <input type="text" class="form-control" name="gateway" value="{{ $payout->gateway }}" required="">
                            </div>

                            <div class="form-group col-md-12 col-12">
                                <label>{{ __('affiliates::general.paypal_bitcoin_iban') }}</label>
                                <input type="text" class="form-control" name="address" value="{{ $payout->address }}">
                            </div>

                            <div class="form-group col-md-12 col-12">
                                <label>{{ __('affiliates::general.transaction_id_optional') }}</label>
                                <input type="text" class="form-control" name="transaction_id" value="{{ $payout->transaction_id }}">
                            </div>

                            <div class="form-group col-md-12 col-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="email_user" value="1" checked id="customCheck1" />
                                    <label class="custom-control-label" for="customCheck1">{{ __('affiliates::general.email_user__changes') }}</label>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="card-footer text-right">
                        <button class="btn btn-dark" type="submit">{!! __('admin.update') !!}</button>
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header">
                            <h6>{{ __('affiliates::general.pavouts') }}</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>{{ __('affiliates::general.gateway') }}</th>
                                        <th>{{ __('affiliates::general.amount') }}</th>
                                        <th>{{ __('affiliates::general.status') }}</th>
                                        <th>{{ __('affiliates::general.action') }}</th>
                                    </tr>
                                    @foreach($affiliate->payouts()->latest()->paginate(5) as $payout)
                                    <tr>
                                        <td>{{ ucfirst($payout->gateway) }}</td>
                                        <td>{{ currency('symbol') }}{{ number_format( $payout->amount,2)}}</td>
                                        <td>
                                            @if($payout->status == 'completed')
                                                <div class="badge badge-success">{{ $payout->status }}</div>
                                            @else
                                                <div class="badge badge-warning">{{ $payout->status }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('affiliates.payouts.edit', $payout->id) }}" class="btn btn-primary"><i class="fas fa-solid fa-pen"></i></a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $affiliate->payouts()->latest()->paginate(5)->links(AdminTheme::pagination()) }}
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="summary">
                            <div class="summary-item">
                                <h6>
                                    {{ __('affiliates::general.invited_users') }}
                                    <span class="text-muted">({{ $affiliate->invites()->count() }} {{ __('affiliates::general.items') }})</span>
                                </h6>
                                <ul class="list-unstyled list-unstyled-border">
                                    @foreach($affiliate->invites()->latest()->paginate(5) as $invite)
                                    <li class="media">
                                        <div class="media-body">
                                            <div class="media-title"><a href="#">{{ currency('symbol') }}{{ number_format($invite->total_earned, 2) }}</a></div>
                                            <div class="text-muted text-small">
                                                @if($invite->status !== 'pending')
                                                    <span class="text-success">{{ $invite->status }}</span>
                                                @else
                                                    <span class="text-warning">{{ $invite->status }}</span>
                                                @endif
                                                <div class="bullet"></div>
                                                @isset($invite->user) <a href="{{ route('users.edit', $invite->user->id) }}">{{ $invite->user->email }}</a> <div class="bullet"></div>@endisset
                                                {{ $invite->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                                {{ $affiliate->invites()->latest()->paginate(5)->links(AdminTheme::pagination()) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
