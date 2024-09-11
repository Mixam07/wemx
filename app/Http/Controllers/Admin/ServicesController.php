<?php

namespace App\Http\Controllers\Admin;

use App\Entities\ResourceApiClient;
use App\Http\Controllers\Controller;
use App\Facades\AdminTheme as Theme;
use Nwidart\Modules\Facades\Module;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Facades\Service;

class ServicesController extends Controller
{

    // return login page view
    public function index()
    {
        $api = new ResourceApiClient;
        $marketplace = $api->getAllResources('Services');
        if (array_key_exists('error', $marketplace)) {
            $marketplace = [];
        }
        return Theme::view('services.index', compact('marketplace'));
    }

    public function config($service)
    {
        $service = Service::findOrFail($service);

        return Theme::view('services.config', ['service' => $service]);
    }

    public function store(Request $request, $service)
    {
        $service = Service::findOrFail($service);
        $validated = $request->validate($service->getConfigRules());

        // store the data
        Settings::store($request);

        return redirect()->back();
    }

    public function testConnection($service)
    {
        $service = Service::findOrFail($service);
        if(!$service->canTestConnection()) {
            return redirect()->back();
        }

        return $service->class->testConnection();
    }
}
