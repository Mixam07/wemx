@extends(Theme::wrapper())
@section('title', 'Tickets')

{{-- Keywords for search engines --}}
@section('keywords', 'WemX Dashboard, WemX Panel')

@section('container')
<section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
    <div class="">
        <!-- Start coding here -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden mb-4">
            <div class="flex flex-col md:flex-row items-center justify-end space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                    <a href="{{ route('tickets.create') }}" class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                        </svg>
                        New Ticket
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">Ticket</th>
                            <th scope="col" class="px-4 py-3">Department</th>
                            <th scope="col" class="px-4 py-3">Order</th>
                            <th scope="col" class="px-4 py-3">Members</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Last Updated</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets->paginate(10) as $ticket)
                        <tr class="border-b dark:border-gray-700">
                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"><a class="hover:text-primary-400 dark:hover:text-primary-400 font-bold" href="{{ route('tickets.view', $ticket->id) }}">{{ $ticket->subject }}</a></th>
                            <td class="px-4 py-3">{{ $ticket->department->name ?? 'none' }}</td>
                            <td class="px-4 py-3"><a @isset($ticket->order->id) href="{{ route('service', ['order' => $ticket->order->id, 'page' => 'manage']) }}" @else href="#" @endif class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">{{ $ticket->order->package->name ?? 'none' }}</a></td>
                            <td class="px-4 py-3">
                                <div class="flex -space-x-4 rtl:space-x-reverse">
                                    @if($ticket->user->avatar)
                                        <img class="w-10 h-10 border-2 border-white rounded-full dark:border-gray-800" src="{{ $ticket->user->avatar() }}" alt="">
                                    @else
                                        <div class="relative inline-flex border border-gray-500 items-center justify-center mt-0.5 w-9 h-9 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                            <span class="font-medium text-gray-600 dark:text-gray-300">{{ substr($ticket->user->first_name, 0, 1) . substr($ticket->user->last_name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    @foreach($ticket->members()->get() as $member)
                                        @if($member->user->avatar ?? false)
                                            <img class="w-10 h-10 @if($loop->last) z-10 @endif  border-2 border-white rounded-full dark:border-gray-800" src="{{ $member->user->avatar() }}" alt="">
                                        @else
                                            <div class="relative inline-flex border border-gray-500 items-center justify-center mt-0.5 w-9 h-9 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                                <span class="font-medium text-gray-600 dark:text-gray-300">{{ substr($member->user->first_name, 0, 1) . substr($member->user->last_name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>                             
                            </td>
                            <td class="px-4 py-3">
                                @if($ticket->is_open) 
                                    <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Open</span>
                                @else 
                                    <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Closed</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $ticket->updated_at->diffForHumans() ?? 'Never' }}</td>
                        </tr>
                        @endforeach
                        @foreach(\Modules\Tickets\Entities\TicketMember::where('user_id', auth()->user()->id)->get() as $ticket)
                        @php 
                            $ticket = $ticket->ticket;
                        @endphp
                        <tr class="border-b dark:border-gray-700">
                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"><a class="hover:text-primary-400 dark:hover:text-primary-400 font-bold" href="{{ route('tickets.view', $ticket->id) }}">{{ $ticket->subject }}</a></th>
                            <td class="px-4 py-3">{{ $ticket->department->name ?? 'none' }}</td>
                            <td class="px-4 py-3"><a @isset($ticket->order->id) href="{{ route('service', ['order' => $ticket->order->id, 'page' => 'manage']) }}" @else href="#" @endif class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">{{ $ticket->order->package->name ?? 'none' }}</a></td>
                            <td class="px-4 py-3">
                                <div class="flex -space-x-4 rtl:space-x-reverse">
                                    @if($ticket->user->avatar)
                                        <img class="w-10 h-10 border-2 border-white rounded-full dark:border-gray-800" src="{{ $ticket->user->avatar() }}" alt="">
                                    @else
                                        <div class="relative inline-flex border border-gray-500 items-center justify-center mt-0.5 w-9 h-9 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                            <span class="font-medium text-gray-600 dark:text-gray-300">{{ substr($ticket->user->first_name, 0, 1) . substr($ticket->user->last_name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    @foreach($ticket->members()->get() as $member)
                                        @if($member->user->avatar ?? false)
                                            <img class="w-10 h-10 @if($loop->last) z-10 @endif  border-2 border-white rounded-full dark:border-gray-800" src="{{ $member->user->avatar() }}" alt="">
                                        @else
                                            <div class="relative inline-flex border border-gray-500 items-center justify-center mt-0.5 w-9 h-9 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600">
                                                <span class="font-medium text-gray-600 dark:text-gray-300">{{ substr($member->user->first_name, 0, 1) . substr($member->user->last_name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>                             
                            </td>
                            <td class="px-4 py-3">
                                @if($ticket->is_open) 
                                    <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Open</span>
                                @else 
                                    <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Closed</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $ticket->updated_at->diffForHumans() ?? 'Never' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-2 mb-6 flex items-center justify-end">
                {{ $tickets->paginate(10)->links(Theme::pagination()) }}
            </div>
        </div>
        @if($tickets->count() == 0)
            @include(Theme::path('empty-state'), ['title' => 'No tickets found', 'description' => 'You haven\'t created any tickets yet']);
        @endif
    </div>
</section>
@endsection