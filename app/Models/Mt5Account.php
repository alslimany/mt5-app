<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mt5Account extends Model
{
    protected $fillable = [
        'user_id',
        'account_name',
        'account_number',
        'password',
        'investor_password',
        'server',
        'broker',
        'api_token',
        'api_secret',
        'balance',
        'equity',
        'margin',
        'free_margin',
        'profit',
        'credit',
        'currency',
        'leverage',
        'is_active',
        'last_sync_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'equity' => 'decimal:2',
        'margin' => 'decimal:2',
        'free_margin' => 'decimal:2',
        'profit' => 'decimal:2',
        'credit' => 'decimal:2',
        'leverage' => 'integer',
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
        'password' => 'encrypted',
        'investor_password' => 'encrypted',
    ];

    protected $hidden = [
        'password',
        'investor_password',
        'api_token',
        'api_secret',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trades(): HasMany
    {
        return $this->hasMany(Mt5Trade::class);
    }

    public function stats(): HasMany
    {
        return $this->hasMany(Mt5AccountStat::class);
    }

    public function sourceCopyRules(): HasMany
    {
        return $this->hasMany(TradeCopyRule::class, 'source_account_id');
    }

    public function destinationCopyRules(): HasMany
    {
        return $this->hasMany(TradeCopyRule::class, 'destination_account_id');
    }
}
