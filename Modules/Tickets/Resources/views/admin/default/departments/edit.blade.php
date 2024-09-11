@extends(AdminTheme::wrapper(), ['title' => 'Departments', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
<div class="col-12 col-md-12 col-lg-12">
  <form action="{{ route('tickets.departments.update', $department->id) }}" method="POST">
    @csrf
    <div class="card">
      <div class="card-header">
        <h4>
          Edit Department
        </h4>
      </div>
      <div class="card-body p-0">

        <div class="form-group col-12">
          <label>Name</label>
          <input type="text" name="name" value="{{ $department->name }}" required class="form-control">
        </div>

        <div class="form-group col-12">
          <label>Description</label>
          <input type="text" name="description" value="{{ $department->description }}" class="form-control">
        </div>

        @includeIf(AdminTheme::moduleView('tickets', 'editor'))
        <div class="form-group col-12">
          <label>Template</label>
          <textarea name="template" class="form-control">{!! $department->template !!}</textarea>
          <small class="form-text text-muted">
            This template will be presented when a user tries to create a new ticket in this department
          </small>
        </div>

        <div class="form-group col-12">
          <label>Auto Response</label>
          <textarea name="auto_response_template" class="form-control">{!! $department->auto_response_template !!}</textarea>
          <small class="form-text text-muted">
            The bot will automatically respond to the user after their ticket has been created
          </small>
        </div>

        <div class="form-group col-12">
          <label>Auto close after hour</label>
          <input type="number" name="auto_close_after" value="{{ $department->auto_close_after }}" required class="form-control">
          <small class="form-text text-muted">
            After how many hours of inactivity should the ticket automatically be closed. Set "0" not to close
          </small>
        </div>

        <div class="form-group col-12">
          <label>Auto lock after hour</label>
          <input type="number" name="auto_lock_after" value="{{ $department->auto_lock_after }}" required class="form-control">
          <small class="form-text text-muted">
            After how many hours of inactivity should the ticket automatically be locked. Set "0" not to lock
          </small>
        </div>

      </div>
      <div class="card-footer" style="display: flex; justify-content: space-between">
        <a href="{{ route('tickets.departments.delete', $department->id) }}" class="btn btn-danger">Delete</a>
        <button type="submit" class="btn btn-success">Update</button>
      </div>
    </div>
  </form>

</div>
@endsection