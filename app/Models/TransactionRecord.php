<?php

namespace App\Models;

use Database\Factories\TransactionRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRecord extends Model
{
    /** @use HasFactory<TransactionRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'transaction_hash',
        'from_address',
        'to_address',
        'amount_wei',
        'network_name',
        'chain_id',
        'gas_limit',
        'gas_price_wei',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'chain_id' => 'integer',
            'submitted_at' => 'datetime',
        ];
    }

    public function walletProfile()
    {
        return $this->belongsTo(WalletProfile::class);
    }
}
