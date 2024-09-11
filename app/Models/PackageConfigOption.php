<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\Package;

class PackageConfigOption extends Model
{
    use HasFactory;

    protected $table = 'package_config_options';

    protected $fillable = [
        'package_id',
        'name',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
