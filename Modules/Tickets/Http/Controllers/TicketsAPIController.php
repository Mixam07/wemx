<?php

namespace Modules\Tickets\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Modules\Tickets\Entities\TicketDepartment as Department;
use Modules\Tickets\Entities\TicketResponder as Responder;
use Modules\Tickets\Entities\Ticket;
use Illuminate\Http\Request;

class TicketsAPIController extends Controller
{
    public function tickets()
    {
        return Ticket::paginate(15);
    }

    public function getTicket(Ticket $ticket)
    {
        return $ticket->load(['user', 'department', 'order']);
    }

    public function getTicketMessages(Ticket $ticket)
    {
        return $ticket->getMessages()->with(['user'])->paginate(15);
    }

    public function createDiscordMessage(Request $request, Ticket $ticket) 
    {
        $request->validate([
            'author' => 'required',
            'avatar_url' => 'required',
            'message' => 'required',
        ]);

        $ticket->updateTimeline([
            'user_id' => null,
            'type' => 'discordMessage',
            'content' => $request->input('message'),
            'data' => [
                'author' => $request->input('author'),
                'avatar_url' => $request->input('avatar_url'),
            ]
        ]);

        if($ticket->is_subscribed) {
            $ticket->user->email([
                "subject" => "New Response [{$ticket->subject}]",
                "content" => "Your ticket received a new response. Please click the button below to view your ticket.",
                "button" => [
                    'name' => 'View Ticket',
                    'url' => route('tickets.view', $ticket->id)
                ],
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function getTicketTimeline(Ticket $ticket)
    {
        return $ticket->timeline()->paginate(15);
    }

    public function closeOrOpen(Ticket $ticket) 
    {
        $ticket->closeOrOpen();
        return $ticket;
    }

    public function closeTicket(Ticket $ticket) 
    {
        $ticket->close();
        return $ticket;
    }

    public function reopenTicket(Ticket $ticket) 
    {
        $ticket->open();
        return $ticket;
    }

    public function lockOrUnlock(Ticket $ticket) 
    {
        $ticket->lockOrUnlock();
        return $ticket;
    }

    public function lockTicket(Ticket $ticket) 
    {
        $ticket->lock();
        return $ticket;
    }

    public function unlockTicket(Ticket $ticket) 
    {
        $ticket->unlock();
        return $ticket;
    }

    public function deleteTicket(Ticket $ticket) 
    {
        $ticket->delete();
        return ['success' => true];
    }

    public function departments()
    {
        return Department::paginate(15);
    }

    public function responders()
    {
        return Responder::paginate(15);
    }

    public function syncDiscord()
    {
        return [
            'discord_server' => settings('tickets::discord_server', ''),
            'discord_channel' => settings('tickets::discord_channel_id', ''),
        ];
    }
}
