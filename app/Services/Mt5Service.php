<?php

namespace App\Services;

use App\Models\Mt5Account;
use App\Models\Mt5Trade;
use App\Models\Mt5AccountStat;
use Carbon\Carbon;

class Mt5Service
{
    /**
     * Test connection to MT5 account
     * 
     * @param string $login Account number
     * @param string $password Account password
     * @param string $server MT5 server name
     * @param string $broker Broker name
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public function testConnection(string $login, string $password, string $server, string $broker): array
    {
        try {
            // Call the Python bridge to test MT5 connection
            $result = $this->callMt5PythonBridge('test_connection', [
                'login' => $login,
                'password' => $password,
                'server' => $server,
                'broker' => $broker,
            ]);

            if ($result['success']) {
                return [
                    'success' => true,
                    'message' => 'Connection successful',
                    'data' => $result['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to connect to MT5 server',
            ];
        } catch (\Exception $e) {
            \Log::error('MT5 Connection Test Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sync account data from MT5 server
     */
    public function syncAccount(Mt5Account $account): bool
    {
        try {
            // Fetch account data from MT5
            $accountData = $this->fetchAccountData($account);
            
            if (!isset($accountData['success']) || !$accountData['success']) {
                \Log::error('MT5 Sync Error: Failed to fetch account data');
                return false;
            }

            $data = $accountData['data'] ?? [];
            
            // Update account information
            $account->update([
                'balance' => $data['balance'] ?? $account->balance,
                'equity' => $data['equity'] ?? $account->equity,
                'margin' => $data['margin'] ?? $account->margin,
                'free_margin' => $data['free_margin'] ?? $account->free_margin,
                'profit' => $data['profit'] ?? $account->profit,
                'credit' => $data['credit'] ?? $account->credit,
                'last_sync_at' => now(),
            ]);

            // Sync trades if available
            if (isset($data['trades']) && is_array($data['trades'])) {
                $this->syncTrades($account, $data['trades']);
            }

            // Calculate and store stats
            $this->calculateStats($account);

            return true;
        } catch (\Exception $e) {
            \Log::error('MT5 Sync Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Call the Python bridge for MT5 operations
     * 
     * @param string $action The action to perform (test_connection, get_account_info, get_trades, etc.)
     * @param array $params Parameters for the action
     * @return array Response from the Python bridge
     */
    protected function callMt5PythonBridge(string $action, array $params = []): array
    {
        $pythonScript = base_path('scripts/mt5_bridge.py');
        
        // Check if Python script exists
        if (!file_exists($pythonScript)) {
            \Log::warning('MT5 Python bridge not found. Using simulation mode.');
            return $this->simulateMt5Response($action, $params);
        }

        try {
            // Prepare command
            $command = sprintf(
                'python3 %s %s %s 2>&1',
                escapeshellarg($pythonScript),
                escapeshellarg($action),
                escapeshellarg(json_encode($params))
            );

            // Execute command
            exec($command, $output, $returnCode);
            
            $outputStr = implode("\n", $output);
            
            if ($returnCode !== 0) {
                \Log::error('MT5 Bridge Error: ' . $outputStr);
                return [
                    'success' => false,
                    'message' => 'Failed to execute MT5 bridge: ' . $outputStr,
                ];
            }

            // Parse JSON response
            $result = json_decode($outputStr, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('MT5 Bridge JSON Error: ' . json_last_error_msg());
                return [
                    'success' => false,
                    'message' => 'Invalid response from MT5 bridge',
                ];
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error('MT5 Bridge Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Simulate MT5 response when Python bridge is not available
     * This is for demonstration purposes only
     */
    protected function simulateMt5Response(string $action, array $params): array
    {
        if ($action === 'test_connection') {
            return [
                'success' => true,
                'message' => 'Simulation mode: Connection would be successful',
                'data' => [
                    'balance' => 10000.00,
                    'equity' => 10000.00,
                    'margin' => 0.00,
                    'free_margin' => 10000.00,
                    'profit' => 0.00,
                    'credit' => 0.00,
                    'currency' => 'USD',
                    'leverage' => 100,
                    'server' => $params['server'] ?? 'Demo Server',
                ],
            ];
        }

        return [
            'success' => false,
            'message' => 'Simulation mode: Action not implemented',
        ];
    }

    /**
     * Fetch account data from MT5
     */
    protected function fetchAccountData(Mt5Account $account): array
    {
        return $this->callMt5PythonBridge('get_account_info', [
            'login' => $account->account_number,
            'password' => $account->password,
            'server' => $account->server,
            'broker' => $account->broker,
        ]);
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
