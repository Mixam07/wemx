<?php

namespace Modules\Tickets\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Modules\Tickets\Entities\TicketDepartment as Department;
use Modules\Tickets\Entities\TicketMember as Member;
use Modules\Tickets\Entities\Ticket;
use Modules\Tickets\Jobs\TicketCreateWebhook;
use Illuminate\Http\Request;
use App\Facades\Theme;
use App\Models\User;

class TicketsController extends Controller
{
    public function tickets()
    {
        $tickets = Ticket::where('user_id', auth()->user()->id);
        return view(Theme::moduleView('tickets','tickets'), compact('tickets'));
    }

    public function createTicket()
    {
        $departments = Department::get();
        return view(Theme::moduleView('tickets','create'), compact('departments'));
    }

    public function storeTicket(Request $request)
    {
        $validatedData = $request->validate([
            'subject' => 'required|max:255',
            'order' => 'nullable|numeric',
            'department' => 'required|numeric',
            'message' => 'required',
        ]);
        
        if(str_word_count(strip_tags($request->input('message'))) > 500) {
            return redirect()->back()->withError('The maximum amount of words is 500');
        }

        $ticket = Ticket::create([
            'user_id' => auth()->user()->id,
            'order_id' => $request->input('order', null),
            'department_id' => $request->input('department'),
            'subject' => $request->input('subject'),
            'is_subscribed' => true,
        ]);

        $ticket->createMessage([ 
            'user_id' => auth()->user()->id,
            'message' => $request->input('message'),
        ]);

        if(isset($ticket->department->auto_response_template)) {
            $ticket->botMessage($ticket->department->auto_response_template);
        }

        if(settings('tickets::discord_sync', false)) {
            TicketCreateWebhook::dispatch($ticket, strip_tags($request->input('message')));
        }

        return redirect()->route('tickets.view', $ticket->id);
    }

    public function view(Ticket $ticket)
    {
        $departments = Department::get();

        return view(Theme::moduleView('tickets','view'), compact('ticket', 'departments'));
    }

    public function update(Request $request, Ticket $ticket) 
    {
        $validatedData = $request->validate([
            'subject' => 'required|max:255',
            'order' => 'nullable|numeric',
            'department' => 'sometimes|numeric',
        ]);

        if($request->input('subject') !== $ticket->subject) {
            $ticket->updateTimeline([
                'type' => 'subject_changed',
                'content' => "Ticket subject was changed to {$ticket->subject}",
            ]);
        }

        if(isset($ticket->order) AND $request->input('order') != $ticket->order->id ?? '') {
            $ticket->updateTimeline([
                'type' => 'order_changed',
                'content' => "Ticket order was changed",
            ]);
        }

        if($request->input('department')) {
            if($request->input('department') != $ticket->department->id ?? '') {
                $ticket->updateTimeline([
                    'type' => 'department_changed',
                    'content' => "Ticket was moved to a different department",
                ]);
            }
        }

        $ticket->update([
            'order_id' => $request->input('order', isset($ticket->order) ? $ticket->order->id : null),
            'department_id' => $request->input('department', $ticket->department->id),
            'subject' => $request->input('subject'),
        ]);

        return redirect()->back();
    }

    public function createMessage(Request $request, Ticket $ticket) 
    {
        $request->validate([
            'message' => 'required',
        ]);

        if($ticket->is_locked OR !$ticket->is_open) {
            return redirect()->back()->withError('Ticket has been locked or is closed');
        }

        if(str_word_count(strip_tags($request->input('message'))) > 500) {
            return redirect()->back()->withError('The maximum amount of words is 500');
        }

        $ticket->createMessage([ 
            'user_id' => auth()->user()->id,
            'message' => $request->input('message'),
        ]);

        if($request->input('close_with_comment', false)) {
            sleep(1);
            $ticket->close();
        }

        return redirect()->back();
    }

    public function subscribe(Ticket $ticket) 
    {
        if($ticket->is_locked) {
            return redirect()->back()->withError('Ticket has been locked');
        }

        if(!$ticket->is_subscribed) {
            $ticket->update(['is_subscribed' => true]);
            $ticket->updateTimeline([
                'type' => 'subscribed',
                'content' => 'subscribed to ticket',
            ]);
        } else {
            $ticket->update(['is_subscribed' => false]);
            $ticket->updateTimeline([
                'type' => 'unsubscribed',
                'content' => 'unsubscribed to ticket',
            ]);
        }


        return redirect()->back();
    }

    public function close(Ticket $ticket) 
    {
        if($ticket->is_locked) {
            return redirect()->back()->withError('Ticket has been locked');
        }

        $ticket->closeOrOpen();
        return redirect()->back();
    }

    public function lock(Ticket $ticket) 
    {
        if(!auth()->user()->is_admin()) {
            return redirect()->back()->withError('You dont have access to this resource');
        }

        $ticket->lockOrUnlock();
        return redirect()->back();
    }

    public function delete(Ticket $ticket) 
    {
        if(!auth()->user()->is_admin()) {
            return redirect()->back()->withError('You dont have access to this resource');
        }

        $ticket->delete();
        return redirect()->route('tickets.index')->withSuccess('Ticket has been deleted');
    }

    public function createMember(Request $request, Ticket $ticket) 
    {
        $request->validate([
            'email' => 'email',
        ]);

        $user = User::where('email', $request->input('email'))->first();
        if(!$user) {
            return redirect()->back()->withError("User with email {$request->input('email')} does not exists");
        }

        if($user->id == $ticket->user->id OR $ticket->members()->where('id', $user->id)->exists()) {
            return redirect()->back()->withError("User already has access to this ticket.");
        }

        $ticket->members()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id
        ]);

        return redirect()->back()->withSuccess("{$user->username} was added to the ticket");
    }

    public function deleteMember(Ticket $ticket, Member $member) 
    {
        $member->delete();
        return redirect()->back()->withSuccess("{$member->user->username} was removed from this ticket");
    }
}
