<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserOauth extends Model
{
    protected $table = 'user_oauths';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'driver',
        'email',
        'data',
        'external_profile',
        'display_on_profile',
    ];

    protected $casts = [
        'data' => 'object',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}