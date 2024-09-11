<?php

namespace Modules\Tickets\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TicketResponder extends Model
{
    use HasFactory;
    protected $table = 'ticket_responders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'template',
        'is_enabled',
    ];

    protected static function booted()
    {
        static::addGlobalScope('enabled', function (Builder $builder) {
            $builder->where('is_enabled', true);
        });
    }

    public function keywords()
    {
        return $this->hasMany(TicketResponderKeyword::class, 'responder_id');
    }

    public function addKeyword(string $keyword, string $method)
    {
        TicketResponderKeyword::create([
            'responder_id' => $this->id,
            'keywords' => explode(',', strtolower($keyword)),
            'method' => $method,
        ]);
    }

}