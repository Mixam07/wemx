@extends(AdminTheme::wrapper(), ['title' => 'Dashboard', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('css_libraries')
<link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.css')) }}" />
<link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/select2/dist/css/select2.min.css')) }}">

@endsection

@section('js_libraries')
<script src="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.js')) }}"></script>
<script src="{{ asset(AdminTheme::assets('modules/select2/dist/js/select2.full.min.js')) }}"></script>
@endsection

@section('container')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form action="{{ route('admin.settings.store') }}" method="POST">
            <div class="card-header">
              <h4>{{ __('affiliates::general.affiliate_settings') }}</h4>
            </div>
            <div class="card-body">
                @csrf
              <div class="row">

                <div class="form-group col-6">
                  <label>{{ __('affiliates::general.minimum_payout_amount') }}</label>
                  <input type="number" min="0" class="form-control" name="affiliates::minimum_payout" id="minimum_payout" value="@settings('affiliates::minimum_payout', 10)" required="">
                  <small class="form-text text-muted">
                      {{ __('affiliates::general.minimum_payout_amount_desc') }}
                  </small>
                </div>

                <div class="form-group col-6">
                  <label for="gateways">{{ __('affiliates::general.payout_gateways') }}</label>
                  <div class="input-group mb-2">
                      <select name="affiliates::gateways[]" id="gateways" class="form-control select2 select2-hidden-accessible"
                          multiple="" tabindex="-1" aria-hidden="true">
                              <option value="balance" @if(in_array('balance', json_decode(settings('affiliates::gateways', '["balance", "paypal", "bitcoin"]')))) selected @endif>
                                  {{ __('affiliates::general.account_balance') }}</option>
                              <option value="paypal" @if(in_array('paypal', json_decode(settings('affiliates::gateways', '["balance", "paypal", "bitcoin"]')))) selected @endif>PayPal</option>
                              <option value="bitcoin" @if(in_array('bitcoin', json_decode(settings('affiliates::gateways', '["balance", "paypal", "bitcoin"]')))) selected @endif>Bitcoin</option>
                      </select>
                      <small class="form-text text-muted"></small>
                  </div>
              </div>

                <div class="form-group col-6">
                    <label>{{ __('affiliates::general.default_comission') }}</label>
                    <input type="number" min="0" max="100" class="form-control" name="affiliates::default_comission" id="default_comission" value="@settings('affiliates::default_comission', 10)" required="">
                    <small class="form-text text-muted">
                        {{ __('affiliates::general.default_comission_desc') }}
                    </small>
                </div>

                <div class="form-group col-6">
                  <label>{{ __('affiliates::general.default_discount') }}</label>
                  <input type="number" min="0" max="100" class="form-control" name="affiliates::default_discount" id="default_discount" value="@settings('affiliates::default_discount', 15)" required="">
                  <small class="form-text text-muted">
                      {{ __('affiliates::general.default_discount_desc') }}
                  </small>
                </div>

              </div>
            </div>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary">{{ __('affiliates::general.submit') }}</button>
            </div>
          </div>
        </form>
    </div>
</div>
@endsection
