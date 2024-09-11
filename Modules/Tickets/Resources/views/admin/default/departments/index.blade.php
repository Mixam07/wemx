@extends(AdminTheme::wrapper(), ['title' => 'Departments', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
<div class="col-12 col-md-12 col-lg-12">
    <div class="card">
      <div class="card-header">
        <h4>Departments</h4>
        <div class="card-header-action">
          <a href="{{ route('tickets.departments.create') }}" class="btn btn-icon icon-left btn-primary">
              Create Department
          </a>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-md">
            <tbody><tr>
              <th>Name</th>
              <th>Description</th>
              <th>Auto close after</th>
              <th>Auto lock after</th>
              <th class="text-right">Last Updated</th>
              <th class="text-right">Actions</th>
            </tr>
            @if($departments->count() == 0) 
              @include(AdminTheme::path('empty-state'), ['title' => 'No departments found', 'description' => 'You have not created any departments yet'])
            @endif
            @foreach($departments as $department)
            <tr>
              <td>{{ $department->name }}</td>
              <td>{{ $department->description }}</td>
              <td>{{ $department->auto_close_after }} hours</td>
              <td>{{ $department->auto_lock_after }} hours</td>
              <td class="text-right">{{ $department->updated_at->diffForHumans() }}</td>
              <td class="text-right">
                <a href="{{ route('tickets.departments.edit', $department->id) }}" class="btn btn-success">Edit</a>
              </td>
            </tr>
            @endforeach
          </tbody></table>
        </div>
      </div>
    </div>
</div>
@endsection