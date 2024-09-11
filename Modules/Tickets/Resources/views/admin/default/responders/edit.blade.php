@extends(AdminTheme::wrapper(), ['title' => 'Edit Responder', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')
<div class="col-12 col-md-12 col-lg-12">
  <form action="{{ route('tickets.responders.update', $responder->id) }}" method="POST">
    @csrf
    <div class="card">
      <div class="card-header">
        <h4>
          Edit Responder
        </h4>
      </div>
      <div class="card-body p-0">

        <div class="form-group col-12">
          <label>Name</label>
          <input type="text" name="name" value="{{ $responder->name }}" required class="form-control">
        </div>

        @includeIf(AdminTheme::moduleView('tickets', 'editor'))
        <div class="form-group col-12">
          <label>Template</label>
          <textarea name="template" class="form-control">{!! $responder->template !!}</textarea>
          <small class="form-text text-muted">
            This template will be presented when a user tries to create a new ticket in this department
          </small>
        </div>

      </div>
      <div class="card-footer" style="display: flex; justify-content: space-between">
        <a href="{{ route('tickets.responders.delete', $responder->id) }}" class="btn btn-danger">Delete</a>
        <button type="submit" class="btn btn-success">Update</button>
      </div>
    </div>
  </form>

    <div class="card">
      <div class="card-header">
        <h4>
          Keywords
        </h4>
      </div>
      <div class="card-body">
        <form action="{{ route('tickets.responders.keyword.store', $responder->id) }}" method="POST">
          @csrf
        <div class="row">
          <div class="form-group col-8">
            <label>Keyword</label>
            <input type="text" name="keywords" placeholder="Enter something" required class="form-control">
            <small class="form-text text-muted">
                Enter the keywords the responder should look for i.e "refund". If you want to look for multiple keywords at once, separate them with a comma i.e "talk, human"
            </small>
          </div>
          <div class="form-group col-4">
            <label>Method</label>
            <select name="method" class="form-control">
              <option value="contains">Contains one or more keywords</option>
              <option value="containsAll">Contains all keywords</option>
            </select>
          </div>
          <div class="form-group col-12" style="display: flex;align-items: flex-end; justify-content: end;">
            <td class="text-left"><button type="submit" class="btn btn-success">Add Keyword</button></td>
          </div>
        </div>
      </form>

        <div class="table-responsive">
          <table class="table table-striped table-md">
            <tbody><tr>
              <th>#</th>
              <th>Keyword</th>
              <th>Method</th>
              <th class="text-right">Action</th>
            </tr>
            @foreach($responder->keywords as $keyword)
            <tr>
              <td>{{ $keyword->id }}</td>
              <td>{{ $keyword->keywordsToString() }}</td>
              <td>{{ $keyword->method }}</td>
              <td class="text-right"><a href="{{ route('tickets.keywords.delete', $keyword->id) }}" class="btn btn-danger">Delete</a></td>
            </tr>
            @endforeach
          </tbody></table>
        </div>

      </div>
    </div>

</div>
@endsection