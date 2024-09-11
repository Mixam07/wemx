@extends(AdminTheme::wrapper(), ['title' => 'Responders', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
<div class="col-12 col-md-12 col-lg-12">

  <div class="alert alert-info" role="alert">
    ("Responders") is an algorithm that can help automate repetitive responses. <br> <br>

    Example: A client contacts you asking you how to setup a domain for their server. <br>
    You can give them an automated response by setting up the keywords that users may use i.e ("domain, server"). Once the alogirthm detects the combination of these words, it will automatically respond with your template response
  </div>

    <div class="card">
      <div class="card-header">
        <h4>Responders</h4>
        <div class="card-header-action">
          <a href="{{ route('tickets.responders.create') }}" class="btn btn-icon icon-left btn-primary">
              Create Responder
          </a>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-md">
            <tbody><tr>
              <th>Name</th>
              <th>Description</th>
              <th class="text-right">Status</th>
              <th class="text-right">Actions</th>
            </tr>
            @if($responders->count() == 0) 
              @include(AdminTheme::path('empty-state'), ['title' => 'No responders found', 'description' => 'You have not created any responders yet'])
            @endif
            @foreach($responders as $responder)
            <tr>
              <td>{{ $responder->name }}</td>
              <td>Keywords</td>
              <td>Enabled</td>
              <td class="text-right">
                <a href="{{ route('tickets.responders.edit', $responder->id) }}" class="btn btn-success">Edit</a>
              </td>
            </tr>
            @endforeach
          </tbody></table>
        </div>
      </div>
    </div>
</div>
@endsection