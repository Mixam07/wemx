<?php

namespace App\Services\Pterodactyl\Http\Controllers;

use App\Facades\Theme;
use App\Models\Order;
use Illuminate\Routing\Controller;

class BackupsController extends Controller
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

    public function backups(Order $order)
    {
        $server = ptero()::server($order->id);
        OrderServer::savePermission($order->id, $server['identifier']);
        $backups = ptero()->clientApi()->backups->all($server['identifier'])['data'];
        return view(Theme::serviceView('pterodactyl', 'backups'), compact('order', 'server', 'backups'));
    }

    public function create(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'name' => 'required|string',
            'ignored' => 'nullable|string',
        ]);
        $resp = ptero()->clientApi()->backups->create($server, $data['name'], $data['ignored'] ?? '');
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.backup_create_successfully'));
    }

    public function lockToggle(Order $order, $server, $backup)
    {
        OrderServer::checkPermission($order->id, $server);
        $resp = ptero()->clientApi()->backups->lockToggle($server, $backup);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.backup_lock_successfully'));

    }

    public function download(Order $order, $server, $backup)
    {
        OrderServer::checkPermission($order->id, $server);
        $file = ptero()->clientApi()->backups->download($server, $backup);
        if (is_array($file) and isset($file['error'])) {
            return redirect()->back()->with('error', $file['response']->json()['errors'][0]['detail']);
        }
        return redirect()->intended($file['attributes']['url']);
    }

    public function delete(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'backup' => 'required|string',
        ]);
        $resp = ptero()->clientApi()->backups->delete($server, $data['backup']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.backup_delete_successfully'));
    }

    public function restore(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'backup' => 'required|string',
        ]);
        $resp = ptero()->clientApi()->backups->restore($server, $data['backup']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->route('service', ['order' => $order->id, 'page' => 'manage'])->with('success', __('responses.backup_restore_successfully'));
    }
}
