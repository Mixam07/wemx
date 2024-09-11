<?php

namespace App\Services\Pterodactyl\Http\Controllers;

use App\Facades\Theme;
use App\Models\Order;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class NetworkController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->order->status != 'active') {
                return redirect()->route('service', ['order' => $request->order->id, 'page' => 'manage']);
            }
            if (!auth()->user()->isRootAdmin()) {
                if ($request->order) {
                    $hasPermission = collect($request->order->package->data('permissions'))
                        ->first(fn($value, $key) => $value == 1 && str_contains($request->route()->getName(), $key));
                    if (!$hasPermission) {
                        return redirect()->route('service', ['order' => $request->order->id, 'page' => 'manage'])
                            ->with('error', __('responses.no_permission'));
                    }
                }
            }
            return $next($request);
        });
    }

    public function network(Order $order)
    {
        $server = ptero()::server($order->id);
        OrderServer::savePermission($order->id, $server['identifier']);
        $allocations = ptero()->clientApi()->network->all($server['identifier'])['data'];
        return view(Theme::serviceView('pterodactyl', 'network'), compact('order', 'server', 'allocations'));
    }

    public function assign(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $resp = ptero()->clientApi()->network->assignAllocation($server);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.allocation_assigned_successfully'));
    }

    public function setNote(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'allocation' => 'required|string',
            'note' => 'required|string',
        ]);
        $resp = ptero()->clientApi()->network->setNote($server, $data['allocation'], $data['note']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.note_set_successfully'));
    }

    public function setPrimary(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'allocation' => 'required|string',
        ]);
        $resp = ptero()->clientApi()->network->setPrimary($server, $data['allocation']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        Cache::forget("server.ip.order.$order->id");
        return redirect()->back()->with('success', __('responses.allocation_primary_successfully'));
    }

    public function delete(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'allocation' => 'required|string',
        ]);
        $resp = ptero()->clientApi()->network->delete($server, $data['allocation']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.allocation_delete_successfully'));
    }
}
