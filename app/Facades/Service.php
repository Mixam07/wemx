<?php

namespace App\Facades;

use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Collection;

class Service
{

    public static function find($service)
    {
        return new ServiceManager($service);
    }

    public static function findOrFail($service)
    {
        try {
            $service = new ServiceManager($service);
            return $service;
        } catch(\Exception $error) {
            abort(404);
        }
    }

    public static function findOrNull($service)
    {
        try {
            $service = new ServiceManager($service);
            return $service;
        } catch(\Exception $error) {
            return null;
        }
    }

    /**
     * Create a collection from a list of items
     *
     * @param callable|null $condition
     * @return Collection
     */
    public static function all(callable $condition = null): Collection
    {
        $services = collect();
        foreach (Module::scan() as $module) {
            if (strpos(Module::getModulePath($module), '/app/Services/')) {
                if ($condition === null || $condition($module)) {
                    $services->push(Service::find($module->getName()));
                }
            }
        }

        return $services;
    }

    /**
     * Create a collection of all enabled services
     *
     * @return Collection
     */
    public static function allEnabled(): Collection
    {
        $services = Service::all(function ($module) {
            return Module::isEnabled($module);
        });

        return $services;
    }

    /**
     * Create a collection of all disabled services
     *
     * @return Collection
     */
    public static function allDisabled(): Collection
    {
        $services = Service::all(function ($module) {
            return Module::isDisabled($module);
        });

        return $services;
    }

    public static function count(): int
    {
        $services = Service::all();
        return $services->count();
    }
}
