@extends(Theme::wrapper())
@section('title', 'Tickets')

{{-- Keywords for search engines --}}
@section('keywords', 'WemX Dashboard, WemX Panel')

@section('header')
    <link rel="stylesheet" href="{{ Theme::get('Default')->assets }}assets/css/typography.min.css">
@endsection

@section('container')
<header class="mb-4 lg:mb-6 not-format">
   <h1 class="mb-4 text-2xl font-extrabold leading-tight text-gray-900 lg:mb-6 lg:text-4xl dark:text-white">{{ $ticket->subject }}</h1>
   <div class="flex items-center mb-4">
        <svg class="mr-2 w-3 h-3 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm3.982 13.982a1 1 0 0 1-1.414 0l-3.274-3.274A1.012 1.012 0 0 1 9 10V6a1 1 0 0 1 2 0v3.586l2.982 2.982a1 1 0 0 1 0 1.414Z"></path></svg>
        <time class="font-normal text-gray-500 dark:text-gray-400" pubdate="" datetime="2022-03-08" title="August 3rd, 2022">{{ $ticket->created_at->format(settings('date_format', 'd M Y')) }}</time>
    </div>
   <div class="flex justify-between items-center py-6 mb-6 border-t border-b border-gray-200 dark:border-gray-700 not-format">
    <span class="text-sm font-bold text-gray-900 lg:mb-0 dark:text-white">{{ $ticket->getMessages()->count() }} Posts</span>
    <div class="flex items-center">
        <span class="mr-2 text-xs font-semibold text-gray-900 uppercase dark:text-white">Sort by</span>
        <button id="dropdownSortingButton" data-dropdown-toggle="dropdownSorting" class="flex items-center py-1 px-2 text-sm font-medium text-gray-500 rounded-full hover:text-primary-600 dark:hover:text-primary-500 md:mr-0 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400" type="button">
            <span class="sr-only">Open user menu</span>
            @if(request()->get('sort', 'desc') == 'asc')
                Oldest
            @else 
                Latest
            @endif
            <svg class="ml-1.5 w-2.5 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"></path></svg>
        </button>
        <!-- Dropdown menu -->
        <div id="dropdownSorting" class="z-10 w-36 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 hidden" data-popper-placement="bottom" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(862px, 363px);">
            <ul class="py-1 text-sm list-none text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefault">
                <li>
                    <a href="{{ route('tickets.view', ['ticket' => $ticket->id,'sort' => 'desc']) }}" class="block py-2 px-4 w-full hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Latest</a>
                </li>
                <li>
                    <a href="{{ route('tickets.view', ['ticket' => $ticket->id,'sort' => 'asc']) }}" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Oldest</a>
                </li>
            </ul>
        </div> 
    </div>
</div>
</header>

<div class="flex flex-wrap mt-6">
    <div class="w-3/4 md:w-3/4 pr-4 pl-4 sm:w-full pr-4 pl-4 mb-8">

        <ol class="relative border-s border-gray-200 dark:border-gray-700">
            
            @foreach($ticket->timeline()->orderBy('created_at', request()->get('sort', 'desc'))->paginate(8) as $timeline)

                @if($timeline->type == 'message')
                <li class="mb-10 ms-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                        <img class="rounded-full shadow-lg" src="{{ $timeline->user->avatar() }}" alt="{{ $timeline->user->username }}"/>
                    </span>
                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        <div class="items-center justify-between mb-3 sm:flex">
                            <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{ $timeline->created_at->diffForHumans() }}</time>
                            <div class="text-sm font-normal text-gray-800 lex dark:text-gray-300 capitalize"><strong>{{ $timeline->user->username }}</strong> @if($timeline->user->is_admin()) <span class="bg-emerald-100 text-emerald-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-emerald-900 dark:text-emerald-300">Support Team</span>@endif</div>
                        </div>
                        <div class="">
                            <div class="format min-w-fit format-sm sm:format-base text-sm text-gray-700 dark:text-gray-300 lg:format-sm format-blue dark:format-invert">
                                {!! $timeline->content !!}
                            </div>
                        </div>
                    </div>
                </li>
                @endif

                @if($timeline->type == 'discordMessage')
                <li class="mb-10 ms-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                        <img class="rounded-full shadow-lg" src="{{ $timeline->data->get('avatar_url') }}" alt="Avatar"/>
                    </span>
                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        <div class="items-center justify-between mb-3 sm:flex">
                            <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{ $timeline->created_at->diffForHumans() }}</time>
                            <div class="text-sm font-normal text-gray-800 lex dark:text-gray-300 capitalize"><strong>{{ $timeline->data->get('author') }}</strong> <span class="bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-indigo-900 dark:text-indigo-300">Discord</span></div>
                        </div>
                        <div class="">
                            <div class="format min-w-fit format-sm sm:format-base text-sm text-gray-700 dark:text-gray-300 lg:format-sm format-blue dark:format-invert">
                                {!! Str::markdown($timeline->content) !!}
                            </div>
                        </div>
                    </div>
                </li>
                @endif

                @if($timeline->type == 'subscribed' OR $timeline->type == 'unsubscribed')
                <li class="mb-10 ms-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 -start-3 ring-8 ring-white dark:ring-gray-900 bg-white dark:bg-gray-900">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 21">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 3.464V1.1m0 2.365a5.338 5.338 0 0 1 5.133 5.368v1.8c0 2.386 1.867 2.982 1.867 4.175C17 15.4 17 16 16.462 16H3.538C3 16 3 15.4 3 14.807c0-1.193 1.867-1.789 1.867-4.175v-1.8A5.338 5.338 0 0 1 10 3.464ZM1.866 8.832a8.458 8.458 0 0 1 2.252-5.714m14.016 5.714a8.458 8.458 0 0 0-2.252-5.714M6.54 16a3.48 3.48 0 0 0 6.92 0H6.54Z"/>
                        </svg>
                    </span>
                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        <div class="items-center justify-between sm:flex">
                            <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{ $timeline->created_at->diffForHumans() }}</time>
                            <div class="text-sm font-normal text-gray-500 lex dark:text-gray-300">
                                @if($timeline->type == 'subscribed') 
                                    {{ $timeline->user->username }} subscribed to this ticket
                                @else 
                                    {{ $timeline->user->username }} unsubscribed to this ticket
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
                @endif

                @if($timeline->type == 'closed' OR $timeline->type == 'reopened')
                <li class="mb-10 ms-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 -start-3 ring-8 ring-white dark:ring-gray-900 bg-white dark:bg-gray-900">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </span>
                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        <div class="items-center justify-between sm:flex">
                            <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{ $timeline->created_at->diffForHumans() }}</time>
                            <div class="text-sm font-normal text-gray-500 lex dark:text-gray-300">
                                @if($timeline->type == 'closed') 
                                    {{ $timeline->user->username ?? 'Bot' }} <strong>closed</strong> this ticket
                                @else 
                                    {{ $timeline->user->username ?? 'Bot' }} <strong>reopened</strong> this ticket
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
                @endif

                @if($timeline->type == 'locked' OR $timeline->type == 'unlocked')
                <li class="mb-10 ms-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 -start-3 ring-8 ring-white dark:ring-gray-900 bg-white dark:bg-gray-900">
                        @if($timeline->type == 'locked')
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.5 8V4.5a3.5 3.5 0 1 0-7 0V8M8 12v3M2 8h12a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1Z"/>
                        </svg>
                        @else 
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 8V4.5a3.5 3.5 0 1 0-7 0V8M8 12.167v3M2 8h12a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1Z"/>
                        </svg>
                        @endif
                    </span>
                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        <div class="items-center justify-between sm:flex">
                            <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{ $timeline->created_at->diffForHumans() }}</time>
                            <div class="text-sm font-normal text-gray-500 lex dark:text-gray-300">
                                @if($timeline->type == 'locked') 
                                    {{ $timeline->user->username ?? 'Bot' }} <strong>locked</strong> this ticket
                                @else 
                                    {{ $timeline->user->username ?? 'Bot' }} <strong>unlocked</strong> this ticket
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
                @endif

                @if($timeline->type == 'subject_changed' OR $timeline->type == 'order_changed' OR $timeline->type == 'department_changed')
                <li class="mb-10 ms-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 -start-3 ring-8 ring-white dark:ring-gray-900 bg-white dark:bg-gray-900">
                        @if($timeline->type == 'subject_changed')
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="m13.835 7.578-.005.007-7.137 7.137 2.139 2.138 7.143-7.142-2.14-2.14Zm-10.696 3.59 2.139 2.14 7.138-7.137.007-.005-2.141-2.141-7.143 7.143Zm1.433 4.261L2 12.852.051 18.684a1 1 0 0 0 1.265 1.264L7.147 18l-2.575-2.571Zm14.249-14.25a4.03 4.03 0 0 0-5.693 0L11.7 2.611 17.389 8.3l1.432-1.432a4.029 4.029 0 0 0 0-5.689Z"/>
                        </svg>
                        @elseif($timeline->type == 'order_changed')
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M8 8v1h4V8m4 7H4a1 1 0 0 1-1-1V5h14v9a1 1 0 0 1-1 1ZM2 1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1Z"/>
                        </svg>
                        @elseif($timeline->type == 'department_changed')
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M17 10H4a1 1 0 0 0-1 1v9m14-10a1 1 0 0 1 1 1m-1-1h-5.057M17 10a1 1 0 0 1 1 1m0 0v9m0 0a1 1 0 0 1-1 1m1-1a1 1 0 0 1-1 1m0 0H4m0 0a1 1 0 0 1-1-1m1 1a1 1 0 0 1-1-1m0 0V7m0 0a1 1 0 0 1 1-1h4.443a1 1 0 0 1 .8.4l2.7 3.6M3 7v3h8.943M18 18h2a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-5.057l-2.7-3.6a1 1 0 0 0-.8-.4H7a1 1 0 0 0-1 1v1"/>
                        </svg>
                        @endif
                    </span>
                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        <div class="items-center justify-between sm:flex">
                            <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{ $timeline->created_at->diffForHumans() }}</time>
                            <div class="text-sm font-normal text-gray-500 lex dark:text-gray-300">
                                @if($timeline->type == 'subject_changed') 
                                    {{ $timeline->user->username }} changed the subject to <strong>{{ $ticket->subject }}</strong>
                                @elseif($timeline->type == 'order_changed')
                                    {{ $timeline->user->username }} changed the order to <strong>{{ $ticket->order->package->name ?? 'None' }}</strong>
                                @elseif($timeline->type == 'department_changed')
                                    {{ $timeline->user->username }} moved the ticket to department <strong>{{ $ticket->department->name ?? 'Other' }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
                @endif

                @if($timeline->type == 'bot_response')
                <li class="mb-10 ms-6">
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                        <img class="rounded-full shadow-lg" src="{{ settings('tickets::bot_avatar', settings('logo')) }}" alt="logo"/>
                    </span>
                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        <div class="items-center justify-between sm:flex mb-3">
                            <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">{{ $timeline->created_at->diffForHumans() }}</time>
                            <div class="text-sm font-normal text-gray-500 lex dark:text-gray-300">
                                {{ settings('app_name') }} <span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">bot</span>
                            </div>
                        </div>
                        <div class="text-sm font-normal text-gray-500 lex dark:text-gray-300">
                            {!! $timeline->content !!}
                        </div>
                    </div>
                </li>
                @endif
                
            @endforeach

        </ol>
        <div class="mt-2 mb-6 flex items-center justify-end">
            {{ $ticket->timeline()->orderBy('created_at', request()->get('sort', 'desc'))->paginate(8)->links(Theme::pagination()) }}
        </div>
        @if($ticket->is_locked)
        <div id="alert-additional-content-5" class="p-4 mb-6 border border-gray-300 rounded-lg bg-gray-50 dark:border-gray-600 dark:bg-gray-800" role="alert">
            <div class="flex items-center">
              <svg class="flex-shrink-0 w-4 h-4 me-2 dark:text-gray-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
              </svg>
              <span class="sr-only">Info</span>
              <h3 class="text-lg font-medium text-gray-800 dark:text-gray-300">This ticket has been locked.</h3>
            </div>
            <div class="mt-2 mb-4 text-sm text-gray-800 dark:text-gray-300">
              This ticket has been locked.
            </div>
        </div>
        @elseif(!$ticket->is_open)
        <div id="alert-additional-content-5" class="p-4 mb-6 mt-6 border border-gray-300 rounded-lg bg-gray-50 dark:border-gray-600 dark:bg-gray-800" role="alert">
            <div class="flex items-center">
              <svg class="flex-shrink-0 w-4 h-4 me-2 dark:text-gray-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
              </svg>
              <span class="sr-only">Info</span>
              <h3 class="text-lg font-medium text-gray-800 dark:text-gray-300">This ticket has been closed.</h3>
            </div>
            <div class="mt-2 mb-4 text-sm text-gray-800 dark:text-gray-300">
              This ticket was closed, this can be if your ticket was resolved, due to inactivity or you closed the ticket.
            </div>
            <div class="flex">
              <a href="{{ route('tickets.close', $ticket->id) }}" class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-xs px-3 py-1.5 me-2 text-center inline-flex items-center dark:bg-gray-600 dark:hover:bg-gray-500 dark:focus:ring-gray-800">
                Reopen Ticket
              </a>
            </div>
        </div>
        @else
        
        <form id="comment-form" action="{{ route('tickets.message.create', $ticket->id) }}" method="POST">
            @csrf
            @includeIf(Theme::moduleView('tickets', 'components.editor'))
            <div class="sm:col-span-2 mb-6">
                <textarea name="message" id="message" rows="3"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">{{old('description')}}</textarea>
            </div>
            <div class="text-right mb-4">
                <button type="button" onclick="closeWithComment()" class="py-2 px-5 me-1 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Close with comment
                </button>
                <button type="submit" id="post_comment" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2 me-2 mb-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">Comment</button>
            </div>
        </form>

        @endif
    </div>
    <div class="w-1/3 md:w-1/4 pr-4 pl-4 sm:w-full pr-4 pl-4">

        <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700 mb-4">
            <div class="flex flex-col pb-3">
                <dt class="mb-1 text-gray-500 md:text-md dark:text-gray-400 mb-2">Members</dt>
                <dd class="text-lg font-semibold flex gap-1">
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
                </dd>
            </div>
            <div class="flex flex-col py-3">
                <dt class="mb-1 text-gray-500 md:text-md dark:text-gray-400">Order</dt>
                <dd class="text-lg font-semibold"><a @isset($ticket->order->id) href="{{ route('service', ['order' => $ticket->order->id, 'page' => 'manage']) }}" @else href="#" @endif class="text-lg font-medium text-primary-600 hover:underline dark:text-primary-500">{{ $ticket->order->package->name ?? 'None' }}</a></dd>
            </div>
            <div class="flex flex-col py-3">
                <dt class="mb-1 text-gray-500 md:text-md dark:text-gray-400">Department</dt>
                <dd class="text-lg font-semibold">{{ $ticket->department->name ?? 'None' }}</dd>
            </div>
            <div class="flex flex-col py-3">
                <dt class="mb-1 text-gray-500 md:text-md dark:text-gray-400">Status</dt>
                <dd class="text-lg font-semibold">
                    @if($ticket->is_open) 
                        Open 
                    @else 
                        Closed 
                    @endif
                </dd>
            </div>
            <div class="flex flex-col pt-3">
                <dt class="mb-1 text-gray-500 md:text-md dark:text-gray-400">Ticket ID</dt>
                <dd class="text-lg font-semibold">#{{ $ticket->id }}</dd>
            </div>
        </dl>
        <a href="{{ route('tickets.subscribe', $ticket->id) }}" class="w-full flex justify-center items-center text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
            <svg class="w-4 h-4 mr-2 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 21">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 3.464V1.1m0 2.365a5.338 5.338 0 0 1 5.133 5.368v1.8c0 2.386 1.867 2.982 1.867 4.175C17 15.4 17 16 16.462 16H3.538C3 16 3 15.4 3 14.807c0-1.193 1.867-1.789 1.867-4.175v-1.8A5.338 5.338 0 0 1 10 3.464ZM1.866 8.832a8.458 8.458 0 0 1 2.252-5.714m14.016 5.714a8.458 8.458 0 0 0-2.252-5.714M6.54 16a3.48 3.48 0 0 0 6.92 0H6.54Z"/>
              </svg>
            @if(!$ticket->is_subscribed)
                Subscribe
            @else 
                Unsubscribe 
            @endif
        </a>
        <p class="text-sm font-light text-gray-500 dark:text-gray-400">
            Subscribe to receive email notifications
        </p>

        <hr class="h-px my-4 bg-gray-200 border-0 dark:bg-gray-700">

        <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Actions</h2>
        <ul class="max-w-md space-y-2 text-gray-500 list-inside dark:text-gray-400">
            <li class="flex items-center mb-2 hover:underline hover:cursor-pointer" data-drawer-target="drawer-right-update-ticket" data-drawer-show="drawer-right-update-ticket" data-drawer-placement="right" aria-controls="drawer-right-update-ticket">
                <span class="text-gray-500 dark:text-gray-400 flex-shrink-0 mr-1">
                    <i class='bx bxs-comment-edit'></i>
                </span>
               Update Ticket
           </li>
            <li class="flex items-center mb-2">
                <a href="{{ route('tickets.close', $ticket->id) }}" class="hover:underline hover:cursor-pointer">
                    <span class="text-gray-500 dark:text-gray-400 flex-shrink-0">
                        <i class='bx bx-block' ></i>
                    </span>
                    @if($ticket->is_open) Close Ticket @else Open Ticket @endif
                </a>
           </li>
            @if(auth()->user()->is_admin())
            <li class="flex items-center mb-2">
                <a href="{{ route('users.edit', $ticket->user->id) }}" target="_blank" class="hover:underline hover:cursor-pointer">
                    <span class="text-gray-500 dark:text-gray-400 flex-shrink-0">
                        <i class='bx bxs-user' ></i>
                    </span>
                    View Profile
                </a>
            </li>
            <li class="flex items-center mb-2 hover:underline hover:cursor-pointer" data-drawer-target="drawer-right-add-member-ticket" data-drawer-show="drawer-right-add-member-ticket" data-drawer-placement="right" aria-controls="drawer-right-add-member-ticket">
                <span class="text-gray-500 dark:text-gray-400 flex-shrink-0 mr-1">
                    <i class='bx bxs-user-plus'></i>
                </span>
               Update Members
            </li>
            <li class="flex items-center mb-2">
                <a href="{{ route('tickets.lock', $ticket->id) }}" class="hover:underline hover:cursor-pointer">
                    <span class="text-gray-500 dark:text-gray-400 flex-shrink-0">
                        <i class='bx bxs-lock' ></i>
                    </span>
                @if(!$ticket->is_locked) Lock Ticket @else Unlock Ticket @endif
                </a>
            </li>
            <li class="flex items-center mb-2">
                <a href="{{ route('tickets.delete', $ticket->id) }}" class="hover:underline hover:cursor-pointer">
                    <span class="text-gray-500 dark:text-gray-400 flex-shrink-0">
                        <i class='bx bxs-trash' ></i>
                    </span>
                    Delete Ticket
                </a>
            </li>
            @endif
        </ul>

    </div>
</div>
 
 <!-- Update drawer component -->
 <div id="drawer-right-update-ticket" class="fixed top-0 right-0 z-40 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white w-80 dark:bg-gray-800" tabindex="-1" aria-labelledby="drawer-update-ticket-label">
     <h5 id="drawer-right-label" class="inline-flex items-center mb-4 text-base font-semibold text-gray-500 dark:text-gray-400"><svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
     <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
   </svg>Update Ticket</h5>
   <form action="{{ route('tickets.update', $ticket->id) }}" method="POST">
        @csrf
    <button type="button" data-drawer-hide="drawer-right-update-ticket" aria-controls="drawer-right-update-ticket" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 end-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white" >
       <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
       </svg>
       <span class="sr-only">Close menu</span>
    </button>
    <div class="mb-6">
        <label for="subject" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subject</label>
        <input type="text" id="subject" value="{{ $ticket->subject }}" name="subject" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
    </div>
    @if(auth()->user()->is_admin())
    <div class="mb-6">
        <div>
            <label for="department" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Department</label>
            <select id="department" name="department" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                @foreach($departments as $department)
                <option value="{{ $department->id }}" 
                    @if(isset($ticket->department) && $department->id == $ticket->department->id) selected @endif>
                    {{ $department->name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="mb-6">
        <div>
            <label for="order" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Order</label>
            <select id="order" name="order" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                @foreach($ticket->user->orders()->get() as $order)
                  <option value="{{ $order->id }}" @if(isset($ticket->order) && $order->id == $ticket->order->id) selected @endif>{{ $order->package->name }} ({{ $order->status }})</option>
                @endforeach
                <option value="" @if(!isset($ticket->order)) selected @endif>None</option>
            </select>
        </div>
    </div>
    @endif
       <button href="#" class="inline-flex w-full justify-center items-center px-4 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Update
     </button>
    </form>
 </div>
 
 @if(auth()->user()->is_admin())
 <!-- add-member drawer component -->
 <div id="drawer-right-add-member-ticket" class="fixed top-0 right-0 z-40 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white w-80 dark:bg-gray-800" tabindex="-1" aria-labelledby="drawer-add-member-ticket-label">
    <h5 id="drawer-right-label" class="inline-flex items-center mb-4 text-base font-semibold text-gray-500 dark:text-gray-400"><svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
  </svg>Add Member</h5>
  <form action="{{ route('tickets.members.create', $ticket->id) }}" method="POST">
       @csrf
   <button type="button" data-drawer-hide="drawer-right-add-member-ticket" aria-controls="drawer-right-add-member-ticket" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 end-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white" >
      <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
         <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
      </svg>
      <span class="sr-only">Close menu</span>
   </button>
        <div class="mb-6">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">User Email</label>
            <input type="email" id="email" value="" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
        </div>
      <button type="submit" class="inline-flex w-full justify-center items-center px-4 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Add Member
    </button>
   </form>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Username
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($ticket->members()->get() as $member)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <a href="{{ route('users.edit', $member->user->id) }}" target="_BLANK" class="underline">{{ $member->user->username }}</a>
                    </th>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('tickets.members.delete', ['ticket' => $ticket->id, 'member' => $member->id])}}" class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endif

<script>
    function closeWithComment() {
        var form = document.getElementById('comment-form');

        // Get the action value of the form
        var actionValue = form.action;
        form.action = actionValue + '?close_with_comment=true';
        form.submit();
    }
</script>

@endsection