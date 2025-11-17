<?php

namespace App\Livewire;

use App\Models\Mt5Account;
use App\Services\Mt5Service;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AccountList extends Component
{
    public $showForm = false;

    protected $listeners = ['accountAdded' => '$refresh'];

    public function syncAccount($accountId)
    {
        $account = Mt5Account::findOrFail($accountId);
        
        if ($account->user_id !== Auth::id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $mt5Service = new Mt5Service();
        
        if ($mt5Service->syncAccount($account)) {
            session()->flash('message', 'Account synced successfully!');
        } else {
            session()->flash('error', 'Failed to sync account.');
        }
    }

    public function toggleStatus($accountId)
    {
        $account = Mt5Account::findOrFail($accountId);
        
        if ($account->user_id !== Auth::id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $account->update(['is_active' => !$account->is_active]);
        session()->flash('message', 'Account status updated!');
    }

    public function deleteAccount($accountId)
    {
        $account = Mt5Account::findOrFail($accountId);
        
        if ($account->user_id !== Auth::id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $account->delete();
        session()->flash('message', 'Account deleted successfully!');
    }

    public function render()
    {
        $accounts = Auth::user()->mt5Accounts()->orderBy('created_at', 'desc')->get();
        
        return view('livewire.account-list', [
            'accounts' => $accounts,
        ])->layout('layouts.app');
    }
}
