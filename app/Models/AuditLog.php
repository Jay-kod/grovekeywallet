<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'actor_id',
        'event_type',
        'outcome',
        'context',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
        ];
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
