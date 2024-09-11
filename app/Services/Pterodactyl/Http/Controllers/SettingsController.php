<?php

namespace App\Services\Pterodactyl\Http\Controllers;

use App\Facades\Theme;
use App\Models\Order;
use App\Services\Pterodactyl\Service;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
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

    public function settings(Order $order)
    {
        $server = ptero()::server($order->id, true);
        $user = ptero()->user()->get($order->user);
        OrderServer::savePermission($order->id, $server['identifier']);
        $data = ptero()->clientApi()->startup->variables($server['identifier']);
        return view(Theme::serviceView('pterodactyl', 'settings'), compact('order', 'server', 'data', 'user'));
    }

    public function update(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'variable' => 'required|string',
            'value' => 'required|string',
        ]);
        $resp = ptero()->clientApi()->startup->update($server, $data);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.variable_update_successfully'));
    }

    public function rename(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'name' => 'required|string',
        ]);
        $resp = ptero()->clientApi()->settings->rename($server, $data['name']);
        Cache::put('server_name_' . $order->id, $data['name']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        $order->update(['name' => $data['name']]);
        return redirect()->back()->with('success', __('responses.server_rename_successfully'));
    }

    public function reinstall(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $resp = ptero()->clientApi()->settings->reinstall($server);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->route('service', ['order' => $order->id, 'page' => 'manage'])->with('success', __('responses.server_reinstall_successfully'));
    }

    public function setDockerImage(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $data = request()->validate([
            'docker_image' => 'required|string',
        ]);
        $resp = ptero()->clientApi()->settings->setDockerImage($server, $data['docker_image']);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.docker_image_successfully'));
    }

    public function changePassword(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $validated = request()->validate([
            'password' => ['required', 'confirmed'],
        ]);
        return ptero()->user()->changePassword($order, $validated['password']);

    }

    public function updateVariable(Order $order, $server)
    {
        OrderServer::checkPermission($order->id, $server);
        $permissions = collect($order->package->data('permissions', []));
        if (!$permissions->get('pterodactyl.variables', 0) == 1) {
            return redirect()->back()->with('error', __('responses.no_permission'));
        }
        $variables = ptero()->clientApi()->startup->variables($server);
        $variables = collect($variables['data'])->map(fn($item) => $item['attributes']);
        // Check if the variable is editable
        $variable = $variables->firstWhere('env_variable', request()->input('var_name'));
        if (!$variable['is_editable']) {
            return redirect()->back()->with('error', __('responses.no_permission'));
        }
        $resp = ptero()->clientApi()->startup->update($server, ['key' => request()->input('var_name'), 'value' => request()->input('var_value')]);
        if (is_array($resp) and isset($resp['error'])) {
            return redirect()->back()->with('error', $resp['response']->json()['errors'][0]['detail']);
        }
        return redirect()->back()->with('success', __('responses.variable_update_successfully'));

    }
}
