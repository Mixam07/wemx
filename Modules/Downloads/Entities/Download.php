<?php

namespace Modules\Downloads\Entities;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $table = 'downloads';
    protected $primaryKey = 'id';

    protected $casts = [
        'package' => 'array',
    ];

    public function canDownload(): bool
    {
        $authUser = auth()->user();
    
         // If the user is not logged in, disallow the download
         if (!$authUser) {
            return false;
        }

        // If the user is an admin, allow the download
        if ($authUser && $authUser->is_admin()) {
            return true;
        }
    
        // If download allows guests, allow the download
        if ($this->allow_guest) {
            return true;
        }       
    
        // If the download requires the user to be logged in and has no package requirements, allow the download
        if (empty($this->package)) {
            return true;
        }
    
        // If the download requires a specific package, check if the user has it
        if (!empty($this->package) && $authUser->orders()->whereIn('package_id', $this->package)->exists()) {
            return true;
        }
    
        // If none of the above conditions are met, disallow the download
        return false;
    }
}
