<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">MT5 Accounts</h1>
                <p class="mt-2 text-sm text-gray-600">Manage your MetaTrader 5 accounts</p>
            </div>
        <button wire:click="$set('showForm', true)" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Account
        </button>
    </div>

    @if($showForm)
        <div class="mb-6 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Add New Account</h2>
            <livewire:account-form />
        </div>
    @endif

    @if($accounts->isEmpty())
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No accounts</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding your first MT5 account.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($accounts as $account)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $account->account_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $account->account_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $account->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Broker:</span>
                                <span class="font-medium">{{ $account->broker }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Server:</span>
                                <span class="font-medium">{{ $account->server }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Balance:</span>
                                <span class="font-medium">{{ $account->currency }} {{ number_format($account->balance, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Equity:</span>
                                <span class="font-medium">{{ $account->currency }} {{ number_format($account->equity, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Profit:</span>
                                <span class="font-medium {{ $account->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $account->currency }} {{ number_format($account->profit, 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Last Sync:</span>
                                <span class="font-medium">{{ $account->last_sync_at ? $account->last_sync_at->diffForHumans() : 'Never' }}</span>
                            </div>
                        </div>

                        <div class="flex space-x-2">
                            <button wire:click="syncAccount({{ $account->id }})" class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                Sync
                            </button>
                            <button wire:click="toggleStatus({{ $account->id }})" class="flex-1 px-3 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                                {{ $account->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                            <button wire:click="deleteAccount({{ $account->id }})" wire:confirm="Are you sure you want to delete this account?" class="px-3 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    </div>
</div>
