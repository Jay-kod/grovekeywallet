<?php

namespace App\Models;

use Database\Factories\WalletProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletProfile extends Model
{
    /** @use HasFactory<WalletProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'address',
        'network_name',
        'chain_id',
        'label',
    ];

    protected function casts(): array
    {
        return [
            'chain_id' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
