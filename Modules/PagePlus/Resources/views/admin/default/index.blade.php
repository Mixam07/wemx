@extends(AdminTheme::wrapper(), ['title' => __('pageplus::messages.pages'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">{!! __('pageplus::messages.pages') !!}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.pageplus.create') }}" class="btn btn-primary btn-sm">
                            {!!  __('pageplus::messages.create_page') !!}
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th scope="col">{!!  __('pageplus::messages.title') !!}</th>
                            <th scope="col">{!!  __('pageplus::messages.slug') !!}</th>
                            <th scope="col">{!!  __('pageplus::messages.order') !!}</th>
                            <th scope="col" class="text-right">{!! __('pageplus::messages.actions') !!}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <style>
                            tr {
                                cursor: pointer;
                            }

                            tr.parent-page {
                                border-top: 1px solid #dee2e6;
                            }
                        </style>
                        @include(AdminTheme::moduleView('pageplus', 'page_children'), ['pages' => $pages, 'depth' => 0, 'prefix' => '', 'collapse' => false])
                        </tbody>
                    </table>
                    {{ $pages->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
