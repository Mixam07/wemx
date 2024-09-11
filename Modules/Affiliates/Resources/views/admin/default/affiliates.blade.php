@extends(AdminTheme::wrapper(), ['title' => __('admin.payments'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <section class="section">
        <div class="section-body">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('affiliates::general.affiliates') }}</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <tbody>
                                <tr>
                                    <th>{!! __('admin.code') !!}</th>
                                    <th>{!! __('admin.user') !!}</th>
                                    <th>{!! __('admin.balance') !!}</th>
                                    <th>{!! __('admin.clicks') !!}</th>
                                    <th>{!! __('admin.create_at') !!}</th>
                                    <th class="text-right">{!! __('admin.actions') !!}</th>
                                </tr>

                                @foreach($affiliates as $affiliate)
                                    <tr>
                                        <td>{{ $affiliate->code }}</td>
                                        <td>
                                            <a href="{{ route('users.edit', ['user' => $affiliate->user->id]) }}"
                                               style="display: flex; color: #6c757d">
                                                <img alt="{!! _('image') !!}" src="{{ $affiliate->user->avatar() }}"
                                                     class="rounded-circle mr-1 mt-1" width="32px" height="32px"
                                                     data-toggle="tooltip" title=""
                                                     data-original-title="{{ $affiliate->user->first_name }} {{ $affiliate->user->last_name }}">
                                                <div class="flex">
                                                    {{ $affiliate->user->username }} <br>
                                                    <small>{{ $affiliate->user->email }}</small>
                                                </div>
                                            </a>
                                        </td>

                                        <td>{{ currency('symbol') }}{{ number_format($affiliate->balance, 2) }}</td>

                                        <td>{{ $affiliate->clicks }} {{ __('affiliates::general.clicks') }}</td>

                                        <td>{{ $affiliate->created_at->translatedFormat(settings('date_format', 'd M Y')) }}</td>

                                        <td class="text-right">
                                            <a href="{{ route('affiliates.edit', ['affiliate' => $affiliate->id]) }}"
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
                        {{ $affiliates->links(AdminTheme::pagination()) }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
