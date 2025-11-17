<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mt5AccountStat extends Model
{
    protected $fillable = [
        'mt5_account_id',
        'date',
        'balance',
        'equity',
        'profit',
        'drawdown',
        'drawdown_percent',
        'total_trades',
        'winning_trades',
        'losing_trades',
        'gross_profit',
        'gross_loss',
    ];

    protected $casts = [
        'date' => 'date',
        'balance' => 'decimal:2',
        'equity' => 'decimal:2',
        'profit' => 'decimal:2',
        'drawdown' => 'decimal:2',
        'drawdown_percent' => 'decimal:2',
        'total_trades' => 'integer',
        'winning_trades' => 'integer',
        'losing_trades' => 'integer',
        'gross_profit' => 'decimal:2',
        'gross_loss' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Mt5Account::class, 'mt5_account_id');
    }
}
