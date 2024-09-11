<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;
use App\Models\Package;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\HigherOrderCollectionProxy;

class PackageFeature extends Model
{
    use HasFactory;

    protected $table = 'package_features';

    public function up()
    {
        $this->increment('order');
    }

    public function down()
    {
        $this->decrement('order');
    }

}
