<?php

namespace App\Services;

use App\Models\Mt5Account;
use App\Models\Mt5Trade;
use App\Models\Mt5AccountStat;
use Carbon\Carbon;

class Mt5Service
{
    /**
     * Sync account data from MT5 server
     * Note: This is a placeholder implementation. In production, you would integrate
     * with actual MT5 API or use MetaTrader 5 Manager API
     */
    public function syncAccount(Mt5Account $account): bool
    {
        try {
            // Simulate MT5 API call - in production, this would be an actual API call
            $accountData = $this->fetchAccountData($account);
            
            // Update account information
            $account->update([
                'balance' => $accountData['balance'],
                'equity' => $accountData['equity'],
                'margin' => $accountData['margin'],
                'free_margin' => $accountData['free_margin'],
                'profit' => $accountData['profit'],
                'last_sync_at' => now(),
            ]);

            // Sync trades
            $this->syncTrades($account, $accountData['trades']);

            // Calculate and store stats
            $this->calculateStats($account);

            return true;
        } catch (\Exception $e) {
            \Log::error('MT5 Sync Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch account data from MT5 (placeholder)
     */
    protected function fetchAccountData(Mt5Account $account): array
    {
        // This is a placeholder. In production, implement actual MT5 API integration
        // You could use: https://www.mql5.com/en/docs/integration
        // Or MetaTrader Manager API
        
        return [
            'balance' => $account->balance + rand(-100, 100),
            'equity' => $account->equity + rand(-50, 50),
            'margin' => $account->margin,
            'free_margin' => $account->free_margin + rand(-50, 50),
            'profit' => $account->profit + rand(-20, 20),
            'trades' => $this->fetchTrades($account),
        ];
    }

    /**
     * Fetch trades from MT5 (placeholder)
     */
    protected function fetchTrades(Mt5Account $account): array
    {
        // Placeholder for MT5 trades fetch
        return [];
    }

    /**
     * Sync trades for an account
     */
    protected function syncTrades(Mt5Account $account, array $trades): void
    {
        foreach ($trades as $trade) {
            Mt5Trade::updateOrCreate(
                ['ticket' => $trade['ticket']],
                [
                    'mt5_account_id' => $account->id,
                    'symbol' => $trade['symbol'],
                    'type' => $trade['type'],
                    'volume' => $trade['volume'],
                    'open_price' => $trade['open_price'],
                    'close_price' => $trade['close_price'] ?? null,
                    'stop_loss' => $trade['stop_loss'] ?? null,
                    'take_profit' => $trade['take_profit'] ?? null,
                    'profit' => $trade['profit'] ?? 0,
                    'commission' => $trade['commission'] ?? 0,
                    'swap' => $trade['swap'] ?? 0,
                    'open_time' => $trade['open_time'],
                    'close_time' => $trade['close_time'] ?? null,
                    'comment' => $trade['comment'] ?? null,
                    'status' => $trade['status'] ?? 'open',
                ]
            );
        }
    }

    /**
     * Calculate account statistics
     */
    protected function calculateStats(Mt5Account $account): void
    {
        $today = Carbon::today();
        
        // Get all trades for today
        $todayTrades = $account->trades()
            ->whereDate('created_at', $today)
            ->get();

        $totalTrades = $todayTrades->count();
        $winningTrades = $todayTrades->where('profit', '>', 0)->count();
        $losingTrades = $todayTrades->where('profit', '<', 0)->count();
        $grossProfit = $todayTrades->where('profit', '>', 0)->sum('profit');
        $grossLoss = abs($todayTrades->where('profit', '<', 0)->sum('profit'));

        // Calculate drawdown
        $peak = $account->stats()
            ->orderBy('date', 'desc')
            ->value('equity') ?? $account->equity;
        
        $drawdown = max(0, $peak - $account->equity);
        $drawdownPercent = $peak > 0 ? ($drawdown / $peak) * 100 : 0;

        // Store stats
        Mt5AccountStat::updateOrCreate(
            [
                'mt5_account_id' => $account->id,
                'date' => $today,
            ],
            [
                'balance' => $account->balance,
                'equity' => $account->equity,
                'profit' => $account->profit,
                'drawdown' => $drawdown,
                'drawdown_percent' => $drawdownPercent,
                'total_trades' => $totalTrades,
                'winning_trades' => $winningTrades,
                'losing_trades' => $losingTrades,
                'gross_profit' => $grossProfit,
                'gross_loss' => $grossLoss,
            ]
        );
    }

    /**
     * Copy trade from source to destination account
     */
    public function copyTrade(Mt5Trade $sourceTrade, Mt5Account $destinationAccount, float $lotMultiplier = 1.0, bool $copySL = true, bool $copyTP = true): bool
    {
        try {
            // In production, this would execute a trade on the destination MT5 account
            // For now, we'll just create a record
            
            Mt5Trade::create([
                'mt5_account_id' => $destinationAccount->id,
                'ticket' => 'COPY_' . $sourceTrade->ticket . '_' . time(),
                'symbol' => $sourceTrade->symbol,
                'type' => $sourceTrade->type,
                'volume' => $sourceTrade->volume * $lotMultiplier,
                'open_price' => $sourceTrade->open_price,
                'stop_loss' => $copySL ? $sourceTrade->stop_loss : null,
                'take_profit' => $copyTP ? $sourceTrade->take_profit : null,
                'open_time' => now(),
                'comment' => 'Copied from account ' . $sourceTrade->account->account_number,
                'status' => 'open',
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Trade Copy Error: ' . $e->getMessage());
            return false;
        }
    }
}
