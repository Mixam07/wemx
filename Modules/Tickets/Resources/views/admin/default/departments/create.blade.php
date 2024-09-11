@extends(AdminTheme::wrapper(), ['title' => 'Departments', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
<div class="col-12 col-md-12 col-lg-12">
  <form action="{{ route('tickets.departments.store') }}" method="POST">
    @csrf
    <div class="card">
      <div class="card-header">
        <h4>Create Department</h4>
      </div>
      <div class="card-body p-0">

        <div class="form-group col-12">
          <label>Name</label>
          <input type="text" name="name" placeholder="Name" required class="form-control">
        </div>

        <div class="form-group col-12">
          <label>Description</label>
          <input type="text" name="description" placeholder="Description" class="form-control">
        </div>

        @includeIf(AdminTheme::moduleView('tickets', 'editor'))
        <div class="form-group col-12">
          <label>Template</label>
          <textarea name="template" class="form-control"></textarea>
          <small class="form-text text-muted">
            This template will be presented when a user tries to create a new ticket in this department
          </small>
        </div>

        <div class="form-group col-12">
          <label>Auto Response</label>
          <textarea name="auto_response_template" class="form-control"></textarea>
          <small class="form-text text-muted">
            The bot will automatically respond to the user after their ticket has been created
          </small>
        </div>

        <div class="form-group col-12">
          <label>Auto close after hour</label>
          <input type="number" name="auto_close_after" value="0" required class="form-control">
          <small class="form-text text-muted">
            After how many hours of inactivity should the ticket automatically be closed. Set "0" not to close
          </small>
        </div>

        <div class="form-group col-12">
          <label>Auto lock after hour</label>
          <input type="number" name="auto_lock_after" value="0" required class="form-control">
          <small class="form-text text-muted">
            After how many hours of inactivity should the ticket automatically be locked. Set "0" not to lock
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