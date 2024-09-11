@extends(AdminTheme::wrapper(), ['title' => __('admin.tickets'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
<section class="section">
    <div class="section-body">
        <div class="col-12">
            @includeIf(AdminTheme::path('users.user_nav'))
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body">

                @includeIf(AdminTheme::path('empty-state'), ['title' => __('admin.no_tickets') , 'description' => __('admin.no_tickets_desc')])

            </div>
        </div>
    </div>
</section>
@endsection
