<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mt5Trade extends Model
{
    protected $fillable = [
        'mt5_account_id',
        'ticket',
        'symbol',
        'type',
        'volume',
        'open_price',
        'close_price',
        'stop_loss',
        'take_profit',
        'profit',
        'commission',
        'swap',
        'open_time',
        'close_time',
        'comment',
        'status',
    ];

    protected $casts = [
        'volume' => 'decimal:2',
        'open_price' => 'decimal:5',
        'close_price' => 'decimal:5',
        'stop_loss' => 'decimal:5',
        'take_profit' => 'decimal:5',
        'profit' => 'decimal:2',
        'commission' => 'decimal:2',
        'swap' => 'decimal:2',
        'open_time' => 'datetime',
        'close_time' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Mt5Account::class, 'mt5_account_id');
    }
}
