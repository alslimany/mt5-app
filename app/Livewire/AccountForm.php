<?php

namespace App\Livewire;

use App\Models\Mt5Account;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AccountForm extends Component
{
    public $account_name = '';
    public $account_number = '';
    public $password = '';
    public $investor_password = '';
    public $broker = '';
    public $server = '';
    public $availableServers = [];
    public $testingConnection = false;
    public $connectionMessage = '';

    protected $rules = [
        'account_name' => 'required|string|max:255',
        'account_number' => 'required|string|unique:mt5_accounts,account_number',
        'password' => 'required|string',
        'investor_password' => 'nullable|string',
        'broker' => 'required|string',
        'server' => 'required|string',
    ];

    public function mount()
    {
        // Set default broker servers
        if ($this->broker) {
            $this->updateAvailableServers();
        }
    }

    public function updatedBroker()
    {
        $this->updateAvailableServers();
        $this->server = ''; // Reset server when broker changes
    }

    protected function updateAvailableServers()
    {
        $brokers = config('mt5_brokers.brokers', []);
        $this->availableServers = $brokers[$this->broker]['servers'] ?? [];
    }

    public function testConnection()
    {
        $this->validate([
            'account_number' => 'required|string',
            'password' => 'required|string',
            'broker' => 'required|string',
            'server' => 'required|string',
        ]);

        $this->testingConnection = true;
        $this->connectionMessage = '';

        try {
            $mt5Service = app(\App\Services\Mt5Service::class);
            $result = $mt5Service->testConnection(
                $this->account_number,
                $this->password,
                $this->server,
                $this->broker
            );

            if ($result['success']) {
                $this->connectionMessage = 'Connection successful! Account data retrieved.';
                // You can pre-fill other fields here if needed
            } else {
                $this->connectionMessage = 'Connection failed: ' . ($result['message'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            $this->connectionMessage = 'Error: ' . $e->getMessage();
        }

        $this->testingConnection = false;
    }

    public function save()
    {
        $this->validate();

        try {
            // Test connection before saving
            $mt5Service = app(\App\Services\Mt5Service::class);
            $connectionResult = $mt5Service->testConnection(
                $this->account_number,
                $this->password,
                $this->server,
                $this->broker
            );

            if (!$connectionResult['success']) {
                session()->flash('error', 'Failed to connect to MT5 account: ' . ($connectionResult['message'] ?? 'Unknown error'));
                return;
            }

            // Create account with retrieved data
            $accountData = $connectionResult['data'] ?? [];
            
            Mt5Account::create([
                'user_id' => Auth::id(),
                'account_name' => $this->account_name,
                'account_number' => $this->account_number,
                'password' => $this->password,
                'investor_password' => $this->investor_password,
                'broker' => $this->broker,
                'server' => $this->server,
                'balance' => $accountData['balance'] ?? 0,
                'equity' => $accountData['equity'] ?? 0,
                'margin' => $accountData['margin'] ?? 0,
                'free_margin' => $accountData['free_margin'] ?? 0,
                'profit' => $accountData['profit'] ?? 0,
                'credit' => $accountData['credit'] ?? 0,
                'currency' => $accountData['currency'] ?? 'USD',
                'leverage' => $accountData['leverage'] ?? 100,
            ]);

            $this->reset();
            $this->dispatch('accountAdded');
            session()->flash('message', 'MT5 Account added and synced successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error adding account: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $brokers = array_keys(config('mt5_brokers.brokers', []));
        
        return view('livewire.account-form', [
            'brokers' => $brokers,
        ]);
    }
}
