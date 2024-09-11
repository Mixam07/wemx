@extends(AdminTheme::wrapper(), ['title' => __('admin.settings'), 'keywords' => 'WemX Dashboard, WemX Panel'])

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
              <h4>Ticket Settings</h4>
            </div>
            <div class="card-body">
                @csrf
              <div class="row">
                <div class="form-group col-12">
                  <a href="{{ route('admin.tickets.view-api-key') }}" class="btn btn-primary">
                    View API Key
                  </a>
                    <a href="{{ route('admin.tickets.create-api-key') }}" class="btn btn-warning">
                        Regenerate API Key
                    </a>
                </div>
                <div class="form-group col-12">
                  <div class="control-label">
                      Enable Discord Sync
                  </div>
                  <label class="custom-switch mt-2" 
                  onclick="location.href = '@if(settings('tickets::discord_sync', false)) /admin/settings/store?tickets::discord_sync=0 @else /admin/settings/store?tickets::discord_sync=1 @endif';">
                      <input type="checkbox" name="tickets::discord_sync" value="1" @if(settings('tickets::discord_sync')) checked @endif class="custom-switch-input">
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description">
                          Enable syncronization between tickets on the website and Discord
                      </span>
                  </label>
              </div>
              @if(settings('tickets::discord_sync', false))
                <div class="form-group col-12">
                    <label>Discord Server</label>
                    <input type="number" name="tickets::discord_server" value="@settings('tickets::discord_server', '')" class="form-control">
                    <small class="text-sm mt-2">The ID of the Discord server where tickets should be created</small>
                </div>
                <div class="form-group col-12">
                  <label>Category ID</label>
                  <input type="number" name="tickets::discord_channel_id" value="@settings('tickets::discord_channel_id', '')" class="form-control">
                  <small class="text-sm mt-2">The ID of the Category where tickets should be created</small>
                </div>
              @endif
                <div class="form-group col-12">
                    <label>Bot Avatar</label>
                    <input type="text" name="tickets::bot_avatar" value="@settings('tickets::bot_avatar', settings('logo', 'https://imgur.com/oJDxg2r.png'))" class="form-control">
                    <div class="gallery gallary mt-3">
                        <div class="gallery-item" data-image="@settings('tickets::bot_avatar', settings('logo', 'https://imgur.com/oJDxg2r.png'))" data-title="Image 1" href="@settings('tickets::bot_avatar', settings('logo', 'https://imgur.com/oJDxg2r.png'))" title="Image 1" style="background-image: url('@settings('tickets::bot_avatar', settings('logo', 'https://imgur.com/oJDxg2r.png'))')');"></div>
                      </div>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary">{!! __('admin.submit') !!}</button>
            </div>
          </div>
        </form>
    </div>
</div>
<style>
    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }
</style>
@endsection