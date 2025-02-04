<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Order;

class CanViewOrder
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
        // Check if the 'order' parameter is present in the route.
        $order = $request->route('order');

        if ($order) {

            if (!$order instanceof Order) {
                // Attempt to find the order with the given ID.
                $order = Order::findOrFail($order);
            }

            // Check if the authenticated user can edit the order.
            if (!$order->canViewOrder()) {
                return redirect()->back()->withError(__('client.no_access_email_download'));
            }
        }

        // If the order parameter is not set, proceed with the next middleware or request handling.
        return $next($request);
    }
}

