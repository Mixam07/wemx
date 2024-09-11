<?php

namespace App\Services\Pterodactyl\Http\Controllers;

use App\Facades\AdminTheme;
use App\Models\ErrorLog;
use App\Models\Order;
use App\Services\Pterodactyl\Api\Pterodactyl;
use App\Services\Pterodactyl\Entities\Node;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    public function clearCache(){
        ptero()::clearCache();
        return redirect()->back()->with('success', __('responses.cleared_api_cache'));
    }
    public function nodes()
    {
        try {
            ptero()::clearCache();
            $nodes = Node::all();
        } catch (Exception $e){
            $nodes = [];
            request()->session()->flash('error', $e->getMessage());
        }
        return view(AdminTheme::serviceView('pterodactyl', 'nodes'), compact('nodes'));
    }

    public function storeNode()
    {
        $data = request()->validate([
            'ports_range' => 'required',
            'location_id' => 'required',
            'node_id' => 'required',
            'ip' => 'required',
        ]);
        DB::table('pterodactyl_nodes')->updateOrInsert(['node_id' => $data['node_id']], $data);
        ptero()::clearCache();
        return redirect()->back()->with('success', __('admin.node_has_been_stored'));
    }

    public function wemxUsers()
    {
        $page = request()->input('page', 1);
        $perPage = 20;
        $users = ptero()->api()->users->all("?page=$page&per_page=$perPage&filter[external_id]=wmx-");
        $total = $users['meta']['pagination']['total'];
        $usersCollection = collect($users['data']);
        $users = new LengthAwarePaginator($usersCollection, $total, $perPage, $page,
            [
                'path' => request()->url(),
                'query' => request()->query()
            ]
        );
        return view(AdminTheme::serviceView('pterodactyl', 'users'), compact('users'));
    }

    public function wemxServers(){
        $page = request()->input('page', 1);
        $perPage = 20;
        $params = ['filter[external_id]' => 'wmx-', 'page' => $page, 'per_page' => $perPage];
        $servers = ptero()->api()->servers->all($params);
        $total = $servers['meta']['pagination']['total'];
        $usersCollection = collect($servers['data']);
        $servers = new LengthAwarePaginator($usersCollection, $total, $perPage, $page,
            [
                'path' => request()->url(),
                'query' => request()->query()
            ]
        );
        return view(AdminTheme::serviceView('pterodactyl', 'servers'), compact('servers'));
    }

    public function assignServerOrder()
    {
        $data = request()->validate([
            'server_uuid' => 'required',
            'order_id' => 'required|integer',
        ]);
        $order = Order::query()->find($data['order_id']);

        if (empty($order) or !empty($order->getExternalId())){
            return redirect()->back()->with('error', __('admin.assign_order_error'));
        }
        $params = ['filter[uuidShort]' => $data['server_uuid']];
        $server = ptero()->api()->servers->all($params);
        if (is_array($server) and isset($server['data'][0])){
            $server = $server['data'][0]['attributes'];
            if (!empty($server['external_id'])){
                return redirect()->back()->with('error', __('admin.server_has_been_assigned_error'));
            }
            $params = ['name' => $server['name'], 'user' => $server['user'], 'external_id' => 'wmx-'.$data['order_id']];
            try {
                ptero()->api()->servers->update($server['id'], $params);
                $order->setExternalId($server['uuidShort']);
            } catch (Exception $e){
                return redirect()->back()->with('error', $e->getMessage());
            }
            return redirect()->back()->with('success', __('admin.server_has_been_assigned'));
        }
        return redirect()->back()->with('error', __('admin.server_not_found'));
    }

    public function logs()
    {
        $logs = ErrorLog::query()->latest()->where('source', 'like', 'pterodactyl%')->paginate(25);
        return view(AdminTheme::serviceView('pterodactyl', 'logs'), compact('logs'));
    }

    public function clearLogs()
    {
        ErrorLog::query()->where('source', 'like', 'pterodactyl%')->delete();
        return redirect()->back();
    }

    public function debug()
    {
        $forceHttps = config('env.FORCE_HTTPS', 'false');
        $nodes = Node::all();
        $nodesIps = collect($nodes)->pluck('ip')->toArray();
        return view(AdminTheme::serviceView('pterodactyl', 'tests'), compact('nodesIps', 'forceHttps', 'nodes'));
    }

    public function checkOpenPort()
    {
        $port = request()->input('port');
        $host = request()->input('host');
        $error = null;
        $connection = @fsockopen($host, $port, $errno, $error, 2);
        if (is_resource($connection)){
            fclose($connection);
            return response()->json(['success' => 'Port is error']);
        }
        return response()->json(['error' => $error]);
    }

    public function checkApiConnection()
    {
        $url = settings('encrypted::pterodactyl::api_url', '');
        $results = [
            'url_available' => false,
            'sso_authorized' => false,
            'client_api_available' => false,
            'application_api_available' => false,
        ];

        // Check URL accessibility
        try {
            $results['url_available'] = Http::head($url)->successful();
        } catch (Exception $e) {
            $results['url_available'] = false;
        }

        // SSO
        try {
            $results['sso_authorized'] = self::checkSsoAuthorization($url, settings('encrypted::pterodactyl::sso_secret', ''));
        } catch (Exception $e) {
            $results['sso_authorized'] = false;
        }
        // Client API
        try {
            $client = new Pterodactyl(settings('encrypted::pterodactyl::api_admin_key', ''), $url);
            $results['client_api_available'] = $client->checkAuthorizationClient();
        } catch (Exception $e) {
            $results['client_api_available'] = false;
        }
        // Application API
        try {
            $api = new Pterodactyl(settings('encrypted::pterodactyl::api_key', ''), $url);
            $results['application_api_available'] = $api->checkAuthorization();
        } catch (Exception $e) {
            $results['application_api_available'] = false;
        }
        return response()->json($results);
    }

    private static function checkSsoAuthorization($url, $secret): bool
    {
        $sso = Http::get($url . '/sso-wemx', [
            'sso_secret' => $secret,
            'user_id' => 1
        ]);

        if ($sso->successful() and is_array($sso->json())) {
            return true;
        } elseif ($sso->getStatusCode() == 501) {
            return $sso->json()['message'] == 'You cannot automatically login to admin accounts.';
        }
        return false;
    }

}


