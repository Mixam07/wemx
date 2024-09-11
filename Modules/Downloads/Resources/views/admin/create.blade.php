@extends(AdminTheme::wrapper(), ['title' => __('Downloads'), 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('css_libraries')
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.css')) }}" />
    <link rel="stylesheet" href="{{ asset(AdminTheme::assets('modules/select2/dist/css/select2.min.css')) }}">
@endsection

@section('js_libraries')
    <script src="{{ asset(AdminTheme::assets('modules/summernote/summernote-bs4.js')) }}"></script>
    <script src="{{ asset(AdminTheme::assets('modules/select2/dist/js/select2.full.min.js')) }}"></script>
@endsection

@section('container')
    <section class="section">
        <div class="section-body">
            <div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Create Download') }}</h4>

                    </div>
                    <div class="card-body">
                        <form action="{{ route('downloads.store') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    value="{{ old('name') }}" required>
                                <small class="form-text text-muted">Downloads name</small>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" required>{{ old('description') }}</textarea>
                                <small class="form-text text-muted">Description about downloads</small>
                            </div>

                            <div class="form-group">
                                <label for="package">Required Package</label>
                                <div class="input-group mb-2">
                                    <select name="package[]" id="package"
                                        class="form-control select2 select2-hidden-accessible" multiple="" tabindex="-1"
                                        aria-hidden="true">
                                        @foreach (Package::get() as $package)
                                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                                        @endforeach

                                    </select>
                                    <small class="form-text text-muted"></small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="file">File</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file" name="file"
                                        accept=".zip">
                                    <label class="custom-file-label" for="file">Choose file</label>
                                    <small class="form-text text-muted">Please upload a ZIP file</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="allow_guest">Allow Guest</label>
                                <select name="allow_guest" id="allow_guest" class="form-control" required>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>

                            </div>

                            <div class="col-md-12">
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
