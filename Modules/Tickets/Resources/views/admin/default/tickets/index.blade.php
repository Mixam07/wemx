@extends(AdminTheme::wrapper(), ['title' => 'Tickets', 'keywords' => 'WemX Dashboard, WemX Panel'])

@section('container')

<div class="col-12 mb-4">
    <div class="card mb-0">
        <div class="card-body">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link @if($nav == 'index') active @endif" href="{{ route('admin.tickets') }}">
                        All Tickets
                        @if($nav == 'index')<span class="badge badge-white">{{$tickets->count()}}</span>@endif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($nav == 'open') active @endif" href="{{ route('admin.tickets.open') }}">
                        Open
                        @if($nav == 'open')<span class="badge badge-white">{{$tickets->count()}}</span>@endif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($nav == 'closed') active @endif" href="{{ route('admin.tickets.closed') }}">
                        Closed
                        @if($nav == 'closed')<span class="badge badge-white">{{$tickets->count()}}</span>@endif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($nav == 'locked') active @endif" href="{{ route('admin.tickets.locked') }}">
                        Locked
                        @if($nav == 'locked')<span class="badge badge-white">{{$tickets->count()}}</span>@endif</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="col-12 col-md-12 col-lg-12">
    <div class="dropdown d-inline mr-2">
        <button class="btn btn-success dropdown-toggle mb-3 " type="button" id="dropdownMenuButton3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Department
        </button>
        <div class="dropdown-menu">
        @foreach($departments as $department) 
            <a class="dropdown-item" href="{{ route('admin.tickets', ['department' => $department->id]) }}">{{ $department->name }}</a>
        @endforeach
        </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h4>Tickets</h4>
        <div class="card-header-action">
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-md">
            <tbody><tr>
              <th class="text-left">User</th>
              <th class="text-left">Subject</th>
              <th>Department</th>
              <th>Status</th>
              <th class="text-right">Last Message by</th>
              <th class="text-right">Last Updated</th>
              <th class="text-right">Actions</th>
            </tr>
            @if($tickets->count() == 0) 
              @include(AdminTheme::path('empty-state'), ['title' => 'No tickets found', 'description' => 'There are no new tickets'])
            @endif
            @foreach($tickets as $ticket)
            <tr>
              <td class="text-left">
                <a href="{{ $ticket->user->avatar() }}" style="display: flex">
                    <img alt="image" src="https://imgur.com/koz9j8a.png" class="rounded-circle mr-2 mt-1" width="32px" height="32px" data-toggle="tooltip" title="" data-original-title="{{ $ticket->user->username }}">
                    <div class="flex">
                      {{ $ticket->user->username }} <br>
                        <small>{{ $ticket->user->email }}</small>
                    </div>
                </a>
              </td>
              <td class="text-left">
                {{ $ticket->subject }}
              </td>
              <td>{{ $ticket->department->name }}</td>
              <td>
                @if($ticket->is_locked)
                <div class="badge badge-warning">
                    Locked
                </div>
                @elseif($ticket->is_open)
                <div class="badge badge-success">
                    Open
                </div>
                @else 
                <div class="badge badge-danger">
                    Closed
                </div>
                @endif
              </td>
              <td class="text-left">
                @php 
                  $last_messanger = $ticket->getMessages()->latest()->first();
                @endphp
                <a href="{{ $last_messanger->user->avatar() }}" style="display: flex;justify-content: flex-end;">
                    <img alt="image" src="https://imgur.com/koz9j8a.png" class="rounded-circle mr-2 mt-1" width="32px" height="32px" data-toggle="tooltip" title="" data-original-title="{{ $last_messanger->user->username }}">
                    <div class="flex">
                      {{ $last_messanger->user->username }} <br>
                        <small>{{ $last_messanger->user->email }}</small>
                    </div>
                </a>
              </td>
              <td class="text-right">{{ $ticket->updated_at->diffForHumans() }}</td>
              <td class="text-right">
                <a href="{{ route('tickets.view', $ticket->id) }}" target="_blank" class="btn btn-success">Open <i class="fas fa-external-link-alt"></i></a>
              </td>
            </tr>
            @endforeach
          </tbody></table>
        </div>
      </div>
    </div>
</div>
@endsection