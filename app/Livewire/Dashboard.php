<?php

namespace App\Livewire;

use App\Models\Mt5Account;
use App\Models\Mt5AccountStat;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $selectedAccountId;
    public $dateRange = 30;

    public function mount()
    {
        $firstAccount = Auth::user()->mt5Accounts()->first();
        $this->selectedAccountId = $firstAccount?->id;
    }

    public function render()
    {
        $accounts = Auth::user()->mt5Accounts()->get();
        
        $selectedAccount = null;
        $stats = collect();
        $recentTrades = collect();
        $chartData = [
            'labels' => [],
            'balance' => [],
            'equity' => [],
            'profit' => [],
            'drawdown' => [],
        ];

        if ($this->selectedAccountId) {
            $selectedAccount = Mt5Account::find($this->selectedAccountId);
            
            // Get stats for the date range
            $stats = Mt5AccountStat::where('mt5_account_id', $this->selectedAccountId)
                ->where('date', '>=', Carbon::now()->subDays($this->dateRange))
                ->orderBy('date', 'asc')
                ->get();

            // Prepare chart data
            foreach ($stats as $stat) {
                $chartData['labels'][] = $stat->date->format('M d');
                $chartData['balance'][] = (float) $stat->balance;
                $chartData['equity'][] = (float) $stat->equity;
                $chartData['profit'][] = (float) $stat->profit;
                $chartData['drawdown'][] = (float) $stat->drawdown;
            }

            // Get recent trades
            $recentTrades = $selectedAccount->trades()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        return view('livewire.dashboard', [
            'accounts' => $accounts,
            'selectedAccount' => $selectedAccount,
            'stats' => $stats,
            'recentTrades' => $recentTrades,
            'chartData' => $chartData,
        ])->layout('layouts.app');
    }
}
