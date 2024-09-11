@extends(Theme::wrapper())
@section('title', 'Tickets')

{{-- Keywords for search engines --}}
@section('keywords', 'WemX Dashboard, WemX Panel')

@section('container')

@if(!request()->get('department_id'))

<div class="mb-6">
    <h2 class="mb-2 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Choose Department...</h2>
    <p class="font-light text-gray-500 dark:text-gray-400 sm:text-xl">Please select the department that best fits your issue</p>
</div>

@foreach($departments as $department)
<div class="p-6 mb-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
    <a href="{{ route('tickets.create', ['department_id' => $department->id]) }}">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $department->name }}</h5>
    </a>
    <div class="flex justify-between items-center">
        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">{!! $department->description !!}</p>
        <a href="{{ route('tickets.create', ['department_id' => $department->id]) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
            Get Started
            <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
            </svg>
        </a>
    </div>
</div>
@endforeach

@else 
<div class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
  <div class="">
      <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Create new ticket</h2>
      <form action="{{ route('tickets.store') }}" method="POST">
        @csrf
          <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
              <div class="sm:col-span-2">
                  <label for="subject" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subject</label>
                  <input type="text" name="subject" id="subject" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required="">
              </div>
              <div>
                <label for="order" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Order</label>
                <select id="order" name="order" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                    @foreach(auth()->user()->orders()->get() as $order)
                      <option value="{{ $order->id }}" @if($order->id == request()->get('order_id')) selected @endif>{{ $order->package->name }} ({{ $order->status }})</option>
                    @endforeach
                    <option value="">None</option>
                </select>
            </div>
              <div>
                  <label for="department" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Department</label>
                  <select id="department" name="department" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                      @foreach($departments as $department)
                        <option value="{{ $department->id }}" @if($department->id == request()->get('department_id')) selected @endif>{{ $department->name }}</option>
                      @endforeach
                  </select>
              </div>
              @includeIf(Theme::moduleView('tickets', 'components.editor'))
              <div class="sm:col-span-2 mb-6">
                <label for="department" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Comment</label>
                @if($selected_department = $departments->where('id', request()->get('department_id', 0))->first())
                <textarea name="message" id="message" rows="3"
                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">{!! $selected_department->template !!}</textarea>
                @else 
                <textarea name="message" id="message" rows="3"
                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"></textarea>
                @endif 
              </div>
          </div>
          <div class="text-right">
            <button type="submit" class="inline-flex items-center px-5 py-2.5 mt-4 sm:mt-6 text-sm font-medium text-center text-white bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800">
                Open Ticket
            </button>
          </div>
      </form>
  </div>
</div>
@endif 

@endsection