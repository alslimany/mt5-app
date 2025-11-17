<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeCopyRule extends Model
{
    protected $fillable = [
        'user_id',
        'source_account_id',
        'destination_account_id',
        'is_active',
        'lot_multiplier',
        'copy_stop_loss',
        'copy_take_profit',
        'symbol_filter',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'lot_multiplier' => 'decimal:2',
        'copy_stop_loss' => 'boolean',
        'copy_take_profit' => 'boolean',
        'symbol_filter' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(Mt5Account::class, 'source_account_id');
    }

    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(Mt5Account::class, 'destination_account_id');
    }
}
