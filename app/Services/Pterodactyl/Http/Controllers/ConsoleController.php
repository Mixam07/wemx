<?php

namespace App\Services\Pterodactyl\Http\Controllers;

use App\Facades\Theme;
use App\Models\Order;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ConsoleController extends Controller
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
    public function loginPanel(Order $order)
    {
        $url = settings('encrypted::pterodactyl::api_url') . '/sso-wemx';
        $secret = settings('encrypted::pterodactyl::sso_secret');

        $response = Http::get($url, [
            'sso_secret' => $secret,
            'user_id' => ptero()->user()->get($order->user)['id']
        ]);

        if (!$response->successful()) {
            $message = __('admin.panel_login_mess');
            try {
                if (is_array($response->json()) && array_key_exists('message', $response->json())) {
                    $message = $response->json()['message'];
                }
            } catch (Exception $e) {
                ErrorLog('pterodactyl::loginPanel', $e->getMessage());
            }
            return redirect()->back()->with('error', $message);
        }
        if (!isset($response['redirect'])) {
            return redirect()->back()->withError(__('responses.ptero_failed_connect'));
        }
        return redirect()->intended($response['redirect']);
    }

    public function powerAction(Order $order, string $action)
    {
        $admin_api_key = settings('encrypted::pterodactyl::api_admin_key', false);
        if ($admin_api_key) {
            if (!in_array($action, ['start', 'stop', 'restart', 'kill'])) {
                return redirect()->back()->with("error", __('responses.power_action_not supported'));
            }
            try {
                $server = ptero()::server($order->id);
                ptero()->clientApi()->server->power($server['identifier'], $action);
                sleep(2);
                return redirect()->back()->with("success", __('responses.power_action_has_sent'));
            } catch (Exception $e) {
                ErrorLog('pterodactyl::powerAction', $e->getMessage());
                return redirect()->back()->with("error", __('auth.oauth_callback_error'));
            }
        } else {
            return redirect()->back()->with("error", __('responses.not_configured_function'));
        }

    }

    public function console(Order $order)
    {
        try {
            $server = ptero()::server($order->id);
        } catch (Exception $e) {
            ErrorLog('pterodactyl::console::order_id=' . $order->id, $e->getMessage());
            return redirect()->back()->with("error", __('responses.find_server_error', ['order_id' => $order->id]));
        }
        Cache::put('server_name_' . $order->id, $server['name']);
        return view(Theme::serviceView('pterodactyl', 'console'), compact('order', 'server'));
    }

    public function websocket(Order $order)
    {
        try {
            $websocketData = $this->getWebsocketData(ptero()::server($order->id)['identifier']);
            return response()->json($websocketData);
        } catch (Exception $e) {
            ErrorLog('pterodactyl::websocket', $e->getMessage());
            return response()->json(['error' => 'Error retrieving websocket data'], 500);
        }
    }

    private function getWebsocketData($uuidShort)
    {
        return ptero()->clientApi()->server->websocket($uuidShort)['data'];
    }
}
