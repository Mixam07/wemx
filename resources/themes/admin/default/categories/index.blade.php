@extends(AdminTheme::wrapper(), ['title' => 'Categories', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{!! __('admin.categories') !!}</div>

                <div class="card-body">
                    <a href="{{ route('categories.create') }}" class="btn btn-primary"><i
                            class="fas fa-solid fa-plus"></i> {!! __('admin.create_category') !!}</a>
                    <hr>
                    @if($categories->count() == 0)
                        @include(AdminTheme::path('empty-state'), ['title' => 'We couldn\'t find any categories', 'description' => 'You haven\'t created any categories yet.'])
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{!! __('admin.id') !!}</th>
                                    <th>{!! __('admin.icon') !!}</th>
                                    <th>{!! __('admin.name') !!}</th>
                                    <th>{!! __('admin.link') !!}</th>
                                    <th>{!! __('admin.description') !!}</th>
                                    <th class="text-right">{!! __('admin.actions') !!}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->order ?? 0 }}</td>
                                        <td><img alt="image" src="{{ asset('storage/products/' . $category->icon) }}"
                                                 class="rounded-circle" width="35" data-toggle="tooltip" title=""
                                                 data-original-title="{{ $category->name }}"></td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->link }}</td>
                                        <td>{{ $category->description }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.change-order', ['id' => $category->id, 'model' => 'categories', 'direction' => 'up']) }}"
                                               class="btn btn-primary"><i class="fas fa-solid fa-caret-up"></i></a>
                                            <a href="{{ route('admin.change-order', ['id' => $category->id, 'model' => 'categories', 'direction' => 'down']) }}"
                                               class="btn btn-primary"><i class="fas fa-solid fa-caret-down"></i></a>
                                            <a href="{{ route('categories.edit', $category->id) }}"
                                               class="btn btn-primary">{!! __('admin.edit') !!}</a>

                                            <form action="{{ route('categories.destroy', $category->id) }}"
                                                  method="POST"
                                                  style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick="deleteItem(event)" type="submit"
                                                        class="btn btn-danger">{!! __('admin.delete') !!}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection
