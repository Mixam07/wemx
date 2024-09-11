<?php

namespace App\Services\Pterodactyl;

use App\Models\Order;
use App\Models\Package;
use App\Services\Pterodactyl\Entities\Node;
use App\Services\Pterodactyl\Entities\Placeholder;
use App\Services\Pterodactyl\Entities\Server;
use App\Services\ServiceInterface;
use Exception;
use Illuminate\Support\Arr;

class Service implements ServiceInterface
{
    public static string $key = 'pterodactyl';
    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getDisplayName(): string
    {
        return settings('pterodactyl::display_name', 'Pterodactyl');
    }

    // Service Info & Config
    public static function metaData(): object
    {
        return (object)
        [
            'display_name' => 'Pterodactyl',
            'author' => 'GIGABAIT',
            'version' => '2.1.3',
            'wemx_version' => ['>=2.0.0'],
        ];
    }

    public static function setConfig(): array
    {
        return [
            [
                'key' => 'encrypted::pterodactyl::api_url',
                'name' => 'Api Url',
                'description' => 'Pterodactyl api url',
                'type' => 'text',
                'rules' => ['required', 'url']
            ],

            [
                'key' => 'encrypted::pterodactyl::api_key',
                'name' => 'Api Key',
                'description' => 'Pterodactyl api key',
                'type' => 'password',
                'rules' => ['required']
            ],
            [
                'key' => 'encrypted::pterodactyl::api_admin_key',
                'name' => 'Admin private API key',
                'description' => 'Pterodactyl administrator api client key',
                'type' => 'password',
                'rules' => ['nullable']
            ],
            [
                'key' => 'encrypted::pterodactyl::sso_secret',
                'name' => 'Sso Key',
                'description' => 'Pterodactyl sso key',
                'type' => 'password',
                'rules' => ['required']
            ],
            [
                'key' => 'pterodactyl::display_name',
                'name' => 'Display Name',
                'default_value' => 'Pterodactyl',
                'description' => 'The name that will be displayed to the user',
                'type' => 'text',
                'rules' => ['required']
            ],
            [
                'key' => 'pterodactyl::short_location_name',
                'name' => 'Short location name',
                'description' => 'Show short location name',
                'type' => 'select',
                'options' => ['1' => 'Yes', '0' => 'No'],
                'default_value' => '0',
                'rules' => ['nullable']
            ],
            [
                'key' => 'pterodactyl::login_to_panel',
                'name' => 'Login to panel',
                'description' => 'Allow users to automatically log into the pterodactyl panel',
                'type' => 'bool',
                'default_value' => true,
                'rules' => ['nullable']
            ],
            [
                'key' => 'pterodactyl::console_button',
                'name' => 'Console button',
                'description' => 'Show console button',
                'type' => 'bool',
                'default_value' => true,
                'rules' => ['nullable']
            ],
            [
                'key' => 'pterodactyl::ip_button',
                'name' => 'Ip button',
                'description' => 'Show ip button',
                'type' => 'bool',
                'default_value' => true,
                'rules' => ['nullable']
            ],
        ];
    }

    public static function setPackageConfig(Package $package): array
    {
        $egg_id = '';
        $location_ids = [];
        $variablesBtn = [];
        $serverBtn = [];
        $placeholders = Placeholder::PLACEHOLDERS;
        if (array_key_exists('egg', $package->data ?? [])) {
            $egg_id = is_numeric($package->data['egg']) ? $package->data['egg'] : json_decode($package->data['egg'], true)['id'];
            $variablesBtn = serviceHelper()::variablesToOptions($egg_id);
            $serverBtn = serviceHelper()::getServerParamsOptions($egg_id);
        }

        if (array_key_exists('locations', $package->data ?? [])) {
            $location_ids = $package->data['locations'];
        }

        $placeholdersInfo = [
            'key' => 'content',
            'type' => 'content',
            'label' => __("admin.available_placeholders"),
            'description' => Arr::join($placeholders, ', '),
            'rules' => ['nullable'],
        ];

        $buttons = array_merge([
            [
                'key' => 'locations[]',
                'name' => 'Locations',
                'description' => 'Select the locations that the user is able to select to deploy their server on',
                'type' => 'select',
                'multiple' => true,
                "options" => serviceHelper()->locationsOptions(),
                'default_value' => $location_ids,
                'rules' => ['required', 'integer'],
                'required' => true
            ],
            [
                'key' => 'egg',
                'name' => 'Egg',
                'description' => 'Select the Nest that this server will be grouped under.',
                'type' => 'select',
                'save_on_change' => true,
                'options' => serviceHelper()::eggsOptions(),
                'default_value' => $egg_id,
                'rules' => ['required'],
                'required' => true
            ],
        ], $serverBtn, [$placeholdersInfo], $variablesBtn);

        // Remove rules if placeholder exist
        foreach ($buttons as $key => $button) {
            if (request()->has('environment') and isset($button['env_variable'])) {
                if (in_array(request()->input('environment')[$button['env_variable']], $placeholders)) {
                    $buttons[$key]['rules'] = [];
                }
            }
        }
        return array_merge($buttons, [
            serviceHelper()::getExcludeOptions($variablesBtn)
        ], serviceHelper()::getPermissionsOptions(self::permissions()));
    }

    public static function setCheckoutConfig(Package $package): array
    {
        return serviceHelper()::getFrontendOptions($package);
    }

    public static function setServiceButtons(Order $order): array
    {
        $permissions = collect($order->package->data('permissions', []));
        $login_to_panel = $permissions->get('pterodactyl.login', 0) == 1 ??
            settings('pterodactyl::login_to_panel', true) ?
            settings('encrypted::pterodactyl::sso_secret') ? [
                "name" => __('client.login_to_panel'),
                "icon" => '<i class="bx bx-user"></i>',
                "color" => "primary",
                "href" => route('pterodactyl.login', $order->id),
                "target" => "_blank",
            ] : [] : [];

        $console = $permissions->get('pterodactyl.console', 0) == 1 ?? settings('pterodactyl::console_button', true) ? [
            "name" => __('client.console'),
            "color" => "primary",
            "icon" => "<i class='bx bx-terminal' ></i>",
            "href" => route('pterodactyl.console', $order->id)
        ] : [];

        $server_ip = [];
        if (settings('pterodactyl::ip_button', true)) {
            $ip = trim(ptero()::serverIP($order->id));
            if ($ip) {
                $server_ip = [
                    "tag" => 'button',
                    "name" => $ip,
                    "color" => "primary",
                    "onclick" => "copyToClipboard(this)",
                ];
            }
        }
        return [$login_to_panel, $console, $server_ip];
    }

    public static function permissions(): array
    {
        return [
            'pterodactyl.login' => [
                'description' => 'Can this user automatically login to Pterodactyl',
            ],
            'pterodactyl.console' => [
                'description' => 'Full access to the console',
                'contains' => true
            ],
            'pterodactyl.files' => [
                'description' => 'Full access to the file manager',
                'contains' => true
            ],
            'pterodactyl.databases' => [
                'description' => 'Full access to the databases manager',
                'contains' => true
            ],
            'pterodactyl.schedules' => [
                'description' => 'Full access to the schedules manager',
                'contains' => true
            ],
            'pterodactyl.backups' => [
                'description' => 'Full access to the backups manager',
                'contains' => true
            ],
            'pterodactyl.network' => [
                'description' => 'Full access to the network manager',
                'contains' => true
            ],
            'pterodactyl.settings' => [
                'description' => 'Full access to the settings manager',
                'contains' => true
            ],
            'pterodactyl.variables' => [
                'description' => 'Allow user to modify egg variables',
                'contains' => true
            ],
        ];
    }


    // Service Actions & Methods
    public function create(array $data = []): void
    {
        $server = new Server($this->order);
        $data = $server->create();
        if (is_array($data) and array_key_exists('attributes', $data)) {
            $this->order->setExternalId($data['attributes']['identifier']);
        }
    }

    public function upgrade(Package $oldPackage, Package $newPackage): void
    {
        $server = ptero()::server($this->order->id);
        ptero()->api()->servers->build($server['id'], [
            "allocation" => $server['allocation'],
            'memory' => (integer)$newPackage->data('memory_limit', 0),
            'swap' => (integer)$newPackage->data('swap_limit', 0),
            'disk' => (integer)$newPackage->data('disk_limit', 0),
            'io' => (integer)$newPackage->data('block_io_weight', 500),
            'cpu' => (integer)$newPackage->data('cpu_limit', 100),
            "feature_limits" => [
                "databases" => (integer)$newPackage->data('database_limit', 0),
                "backups" => (integer)$newPackage->data('backup_limit', 0),
                "allocations" => (integer)$newPackage->data('allocation_limit', 0),
            ]
        ]);
    }

    public function suspend(array $data = []): void
    {
        try {
            $server = ptero()::server($this->order->id);
            ptero()->api()->servers->suspend($server['id']);
        } catch (Exception $e) {
            request()->session()->flash('error', $e->getMessage());
        }
    }

    public function unsuspend(array $data = []): void
    {
        try {
            $server = ptero()::server($this->order->id);
            ptero()->api()->servers->unsuspend($server['id']);
        } catch (Exception $e) {
            request()->session()->flash('error', $e->getMessage());
        }
    }

    public function terminate(array $data = []): void
    {
        try {
            $server = ptero()::server($this->order->id);
            ptero()->api()->servers->delete($server['id']);
        } catch (Exception $e) {
            request()->session()->flash('error', $e->getMessage());
        }
    }

    // Events & Hooks
    public function eventLoadPackage(Package $package): void
    {
        ptero()::clearCache();
    }

    public function eventCheckout(Package $package): array
    {
        foreach (Node::getByLocationsIds([request()->input('location', 0)]) as $node) {
            if (!empty($node)) {
                $resp = Node::getNodeStatus($node, ['memory' => $package->data('memory_limit', 100), 'disk' => $package->data('disk_limit', 100)]);
                if (!$resp['is_full']) {
                    return ['success' => true];
                }
            }
        }
        ErrorLog('pterodactyl::eventCheckout', "All nodes of the selected location are full. Package: $package->name", 'INFO');
        redirect()->route('store.package', $package->id)->withError(__('responses.all_nodes_full_in_location'))->send();
        return ['error' => __('responses.all_nodes_full_in_location')];
    }

}
