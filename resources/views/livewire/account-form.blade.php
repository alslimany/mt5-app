<div>
    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="account_name" class="block text-sm font-medium text-gray-700">Account Name (Nickname)</label>
                <input type="text" id="account_name" wire:model="account_name" placeholder="e.g., My Main Trading Account" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('account_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="broker" class="block text-sm font-medium text-gray-700">Broker *</label>
                <select id="broker" wire:model.live="broker" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select your broker</option>
                    @foreach($brokers as $brokerName)
                        <option value="{{ $brokerName }}">{{ $brokerName }}</option>
                    @endforeach
                </select>
                @error('broker') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="server" class="block text-sm font-medium text-gray-700">Server *</label>
                <select id="server" wire:model="server" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @if(empty($availableServers)) disabled @endif>
                    <option value="">{{ empty($availableServers) ? 'Select broker first' : 'Select server' }}</option>
                    @foreach($availableServers as $serverName => $serverValue)
                        <option value="{{ $serverValue }}">{{ $serverName }}</option>
                    @endforeach
                </select>
                @error('server') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                <p class="mt-1 text-xs text-gray-500">Select the server shown in your MT5 platform</p>
            </div>

            <div>
                <label for="account_number" class="block text-sm font-medium text-gray-700">Account Number (Login) *</label>
                <input type="text" id="account_number" wire:model="account_number" placeholder="e.g., 10934806" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('account_number') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                <input type="password" id="password" wire:model="password" placeholder="Your MT5 account password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                <p class="mt-1 text-xs text-gray-500">Your MT5 trading password (not investor password)</p>
            </div>

            <div class="md:col-span-2">
                <label for="investor_password" class="block text-sm font-medium text-gray-700">Investor Password (Optional)</label>
                <input type="password" id="investor_password" wire:model="investor_password" placeholder="Optional: For read-only access" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('investor_password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                <p class="mt-1 text-xs text-gray-500">Optional: Investor password for read-only monitoring</p>
            </div>
        </div>

        @if($connectionMessage)
            <div class="rounded-md p-4 {{ str_contains($connectionMessage, 'successful') ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
                <p class="text-sm">{{ $connectionMessage }}</p>
            </div>
        @endif

        <div class="flex justify-between items-center">
            <button type="button" wire:click="testConnection" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" @if(!$broker || !$server || !$account_number || !$password) disabled @endif>
                <svg wire:loading wire:target="testConnection" class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Test Connection
            </button>

            <div class="flex space-x-3">
                <button type="button" wire:click="$dispatch('accountAdded')" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    <span wire:loading.remove wire:target="save">Save & Connect</span>
                    <span wire:loading wire:target="save">Connecting...</span>
                </button>
            </div>
        </div>

        <div class="mt-4 p-4 bg-blue-50 rounded-md">
            <h4 class="text-sm font-medium text-blue-900 mb-2">Where to find your MT5 credentials:</h4>
            <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                <li>Open your MetaTrader 5 platform</li>
                <li>Go to Tools → Options → Server tab</li>
                <li>Your account number (Login) and Server will be displayed there</li>
                <li>Use the password you created when opening the account</li>
            </ul>
        </div>
    </form>
</div>
