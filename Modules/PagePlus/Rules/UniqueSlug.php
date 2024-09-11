<?php

namespace Modules\PagePlus\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\PagePlus\Entities\PagePlus;

class UniqueSlug implements Rule
{
    protected mixed $ignoreId;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value): bool
    {
        // Check if the slug already exists and does not belong to the current record
        $existingPage = PagePlus::where('slug', $value)->first();
        if ($existingPage && $this->ignoreId && $existingPage->id == $this->ignoreId) {
            // If the slug already exists, but it belongs to the current record, ignore the uniqueness check
            return true;
        }

        // Get all routes and check against static URIss
        foreach (Route::getRoutes() as $route) {
            $uriSegments = explode('/', $route->uri());
            $firstSegment = $uriSegments[0] ?? null; // Get the first segment of the URI

            // Remove any dynamic segments from the first segment
            if ($firstSegment && !Str::startsWith($firstSegment, '{')) {
                if ($firstSegment === $value) {
                    return false;
                }
            }
        }
        return true;
    }

    public function message(): string
    {
        return 'The :attribute is already in use.';
    }
}
