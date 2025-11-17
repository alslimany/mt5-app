<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Trade Copy Manager</h1>
                <p class="mt-2 text-sm text-gray-600">Set up rules to copy trades between your accounts</p>
            </div>
        <button wire:click="$set('showForm', true)" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Copy Rule
        </button>
    </div>

    @if($showForm)
        <div class="mb-6 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Create New Copy Rule</h2>
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="source_account_id" class="block text-sm font-medium text-gray-700">Source Account</label>
                        <select id="source_account_id" wire:model="source_account_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select source account</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->account_name }} ({{ $account->account_number }})</option>
                            @endforeach
                        </select>
                        @error('source_account_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="destination_account_id" class="block text-sm font-medium text-gray-700">Destination Account</label>
                        <select id="destination_account_id" wire:model="destination_account_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select destination account</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->account_name }} ({{ $account->account_number }})</option>
                            @endforeach
                        </select>
                        @error('destination_account_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="lot_multiplier" class="block text-sm font-medium text-gray-700">Lot Multiplier</label>
                        <input type="number" step="0.01" id="lot_multiplier" wire:model="lot_multiplier" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">1.0 = same lot size, 0.5 = half, 2.0 = double</p>
                        @error('lot_multiplier') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="symbol_filter" class="block text-sm font-medium text-gray-700">Symbol Filter (Optional)</label>
                        <input type="text" id="symbol_filter" wire:model="symbol_filter" placeholder="e.g., EURUSD,GBPUSD" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">Leave empty to copy all symbols, or enter comma-separated symbols</p>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="copy_stop_loss" wire:model="copy_stop_loss" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="copy_stop_loss" class="ml-2 block text-sm text-gray-900">Copy Stop Loss</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="copy_take_profit" wire:model="copy_take_profit" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="copy_take_profit" class="ml-2 block text-sm text-gray-900">Copy Take Profit</label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="$set('showForm', false)" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Create Rule
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($rules->isEmpty())
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No copy rules</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating your first trade copy rule.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destination Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lot Multiplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Settings</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rules as $rule)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $rule->sourceAccount->account_name }}</div>
                                <div class="text-sm text-gray-500">{{ $rule->sourceAccount->account_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $rule->destinationAccount->account_name }}</div>
                                <div class="text-sm text-gray-500">{{ $rule->destinationAccount->account_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $rule->lot_multiplier }}x
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>SL: {{ $rule->copy_stop_loss ? 'Yes' : 'No' }}</div>
                                <div>TP: {{ $rule->copy_take_profit ? 'Yes' : 'No' }}</div>
                                @if($rule->symbol_filter)
                                    <div class="text-xs">Symbols: {{ implode(', ', $rule->symbol_filter) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rule->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="toggleRule({{ $rule->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    {{ $rule->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <button wire:click="deleteRule({{ $rule->id }})" wire:confirm="Are you sure you want to delete this rule?" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    </div>
</div>
