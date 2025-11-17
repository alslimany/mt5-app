<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600">Monitor your MT5 accounts performance</p>
        </div>

    @if($accounts->isEmpty())
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-600 mb-4">You haven't added any MT5 accounts yet.</p>
            <a href="{{ route('accounts') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Add Your First Account
            </a>
        </div>
    @else
        <!-- Account Selector -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Account</label>
            <select wire:model.live="selectedAccountId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->account_name }} ({{ $account->account_number }})</option>
                @endforeach
            </select>
        </div>

        @if($selectedAccount)
            <!-- Account Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600">Balance</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $selectedAccount->currency }} {{ number_format($selectedAccount->balance, 2) }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600">Equity</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $selectedAccount->currency }} {{ number_format($selectedAccount->equity, 2) }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600">Profit</div>
                    <div class="mt-2 text-3xl font-bold {{ $selectedAccount->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $selectedAccount->currency }} {{ number_format($selectedAccount->profit, 2) }}
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600">Free Margin</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $selectedAccount->currency }} {{ number_format($selectedAccount->free_margin, 2) }}</div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Balance & Equity</h3>
                    <canvas id="balanceChart" height="200"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Drawdown</h3>
                    <canvas id="drawdownChart" height="200"></canvas>
                </div>
            </div>

            <!-- Recent Trades -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Recent Trades</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Symbol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volume</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Open Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentTrades as $trade)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $trade->ticket }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $trade->symbol }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ strtoupper($trade->type) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $trade->volume }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($trade->open_price, 5) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $trade->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($trade->profit, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $trade->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($trade->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No trades found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    @if($selectedAccount)
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:navigated', () => {
                const chartData = @js($chartData);

                // Balance Chart
                const balanceCtx = document.getElementById('balanceChart');
                if (balanceCtx) {
                    new Chart(balanceCtx, {
                        type: 'line',
                        data: {
                            labels: chartData.labels,
                            datasets: [{
                                label: 'Balance',
                                data: chartData.balance,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.1
                            }, {
                                label: 'Equity',
                                data: chartData.equity,
                                borderColor: 'rgb(16, 185, 129)',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }

                // Drawdown Chart
                const drawdownCtx = document.getElementById('drawdownChart');
                if (drawdownCtx) {
                    new Chart(drawdownCtx, {
                        type: 'line',
                        data: {
                            labels: chartData.labels,
                            datasets: [{
                                label: 'Drawdown',
                                data: chartData.drawdown,
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.1,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
            });
        </script>
        @endpush
    @endif
    </div>
</div>
