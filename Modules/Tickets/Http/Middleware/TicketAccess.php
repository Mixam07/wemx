<?php 

namespace Modules\Tickets\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Order;

class TicketAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the 'ticket' parameter is present in the route.
        $ticket = $request->route('ticket');
        $authUser = auth()->user();

        // Return 404 if no authenticated user or no ticket found
        if (!$authUser || !$ticket) {
            return abort(404);
        }

        // Check if the authenticated user is a member of the ticket
        $isMember = $ticket->members()->where('user_id', $authUser->id)->exists();

        // Check if the authenticated user is the owner of the ticket
        $isOwner = $authUser->id == $ticket->user_id; // Assuming 'user_id' is the owner field in the ticket

        // Check if the authenticated user is an admin
        $isAdmin = $authUser->is_admin();

        // Allow access if the user is a member, or the owner, or an admin
        if ($isMember || $isOwner || $isAdmin) {
            return $next($request);
        }

        // Otherwise, abort with a 404 error
        return abort(404);
    }
}