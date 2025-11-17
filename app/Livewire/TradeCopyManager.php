<?php

namespace App\Livewire;

use App\Models\Mt5Account;
use App\Models\TradeCopyRule;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TradeCopyManager extends Component
{
    public $source_account_id;
    public $destination_account_id;
    public $lot_multiplier = 1.00;
    public $copy_stop_loss = true;
    public $copy_take_profit = true;
    public $symbol_filter = '';
    public $showForm = false;

    protected $rules = [
        'source_account_id' => 'required|exists:mt5_accounts,id',
        'destination_account_id' => 'required|exists:mt5_accounts,id|different:source_account_id',
        'lot_multiplier' => 'required|numeric|min:0.01|max:100',
        'copy_stop_loss' => 'boolean',
        'copy_take_profit' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        // Parse symbol filter
        $symbols = $this->symbol_filter 
            ? array_map('trim', explode(',', $this->symbol_filter))
            : null;

        TradeCopyRule::create([
            'user_id' => Auth::id(),
            'source_account_id' => $this->source_account_id,
            'destination_account_id' => $this->destination_account_id,
            'lot_multiplier' => $this->lot_multiplier,
            'copy_stop_loss' => $this->copy_stop_loss,
            'copy_take_profit' => $this->copy_take_profit,
            'symbol_filter' => $symbols,
        ]);

        $this->reset(['source_account_id', 'destination_account_id', 'symbol_filter']);
        $this->lot_multiplier = 1.00;
        $this->showForm = false;
        session()->flash('message', 'Trade copy rule created successfully!');
    }

    public function toggleRule($ruleId)
    {
        $rule = TradeCopyRule::findOrFail($ruleId);
        
        if ($rule->user_id !== Auth::id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $rule->update(['is_active' => !$rule->is_active]);
        session()->flash('message', 'Rule status updated!');
    }

    public function deleteRule($ruleId)
    {
        $rule = TradeCopyRule::findOrFail($ruleId);
        
        if ($rule->user_id !== Auth::id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $rule->delete();
        session()->flash('message', 'Rule deleted successfully!');
    }

    public function render()
    {
        $accounts = Auth::user()->mt5Accounts()->where('is_active', true)->get();
        $rules = TradeCopyRule::where('user_id', Auth::id())
            ->with(['sourceAccount', 'destinationAccount'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.trade-copy-manager', [
            'accounts' => $accounts,
            'rules' => $rules,
        ])->layout('layouts.app');
    }
}
