@extends(AdminTheme::wrapper(), ['title' => 'Create Responder', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
<div class="col-12 col-md-12 col-lg-12">
  <form action="{{ route('tickets.responders.store') }}" method="POST">
    @csrf
    <div class="card">
      <div class="card-header">
        <h4>Create Responder</h4>
      </div>
      <div class="card-body p-0">

        <div class="form-group col-12">
          <label>Name</label>
          <input type="text" name="name" placeholder="Name" required class="form-control">
        </div>

        @includeIf(AdminTheme::moduleView('tickets', 'editor'))
        <div class="form-group col-12">
          <label>Template</label>
          <textarea name="template" class="form-control"></textarea>
          <small class="form-text text-muted">
            This template will be presented when a user tries to create a new ticket in this department
          </small>
        </div>

      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-success">Create</button>
      </div>
    </div>
  </form>

</div>
@endsection