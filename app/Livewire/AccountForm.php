<?php

namespace App\Livewire;

use App\Models\Mt5Account;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AccountForm extends Component
{
    public $account_name = '';
    public $account_number = '';
    public $server = '';
    public $broker = '';
    public $api_token = '';
    public $api_secret = '';
    public $balance = 0;
    public $currency = 'USD';
    public $leverage = 100;

    protected $rules = [
        'account_name' => 'required|string|max:255',
        'account_number' => 'required|string|unique:mt5_accounts,account_number',
        'server' => 'required|string|max:255',
        'broker' => 'required|string|max:255',
        'api_token' => 'nullable|string',
        'api_secret' => 'nullable|string',
        'balance' => 'required|numeric|min:0',
        'currency' => 'required|string|max:10',
        'leverage' => 'required|integer|min:1',
    ];

    public function save()
    {
        $this->validate();

        Mt5Account::create([
            'user_id' => Auth::id(),
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'server' => $this->server,
            'broker' => $this->broker,
            'api_token' => $this->api_token,
            'api_secret' => $this->api_secret,
            'balance' => $this->balance,
            'equity' => $this->balance,
            'currency' => $this->currency,
            'leverage' => $this->leverage,
        ]);

        $this->reset();
        $this->dispatch('accountAdded');
        session()->flash('message', 'MT5 Account added successfully!');
    }

    public function render()
    {
        return view('livewire.account-form');
    }
}
