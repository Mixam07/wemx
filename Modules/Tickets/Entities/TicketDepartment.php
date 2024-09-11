<?php

namespace Modules\Tickets\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TicketDepartment extends Model
{
    use HasFactory;
    protected $table = 'ticket_departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_enabled',
        'template',
        'auto_close_after',
        'auto_lock_after',
        'auto_response_template',
    ];

    protected static function booted()
    {
        static::addGlobalScope('enabled', function (Builder $builder) {
            $builder->where('is_enabled', true);
        });
    }

}