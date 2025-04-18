@extends('layouts.app')
@section('content')
<div class="min-h-screen flex bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg flex flex-col items-center py-8">
        <div class="flex items-center mb-8">
            <div class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center mr-2">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24"><rect width="24" height="24" rx="6" fill="#6C3EF4"/><text x="50%" y="55%" text-anchor="middle" fill="#fff" font-size="14" font-weight="bold" dy=".3em">F4U</text></svg>
            </div>
            <span class="text-xl font-bold text-violet-700">Fundings4u</span>
        </div>
        <nav class="flex-1 w-full">
            <a href="#" class="block px-6 py-3 text-violet-700 font-semibold bg-violet-50 rounded-lg mx-3 mb-2">Dashboard</a>
            <!-- Add more nav links if needed -->
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="w-full px-6 mt-8">
            @csrf
            <button type="submit" class="w-full py-2 bg-red-50 text-red-600 font-semibold rounded-lg hover:bg-red-100 transition">Logout</button>
        </form>
    </aside>
    <!-- Main Content -->
    <main class="flex-1 p-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Welcome, {{ $user->name }}</h2>
                <div class="text-sm text-gray-400">MetaTrader Login: {{ $account['account_login'] ?? $user->account_login }}</div>
            </div>
            <div class="flex items-center gap-4 flex-wrap">
                <div class="bg-violet-100 text-violet-700 px-4 py-2 rounded-lg font-semibold">
                    Balance: ${{ number_format($account['account_balance'] ?? 0, 2) }}
                </div>
                <div class="bg-green-100 text-green-700 px-4 py-2 rounded-lg font-semibold">
                    Equity: ${{ number_format($account['equity'] ?? 0, 2) }}
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-gray-700">Status:</span>
                    @if($currentStatus === 'Profit')
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Profit</span>
                    @elseif($currentStatus === 'Loss')
                        <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold">Loss</span>
                    @else
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-bold">Break Even</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-gray-700">Total Profit:</span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $totalProfit > 0 ? 'bg-green-100 text-green-700' : ($totalProfit < 0 ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-700') }}">
                        ${{ number_format($totalProfit, 2) }}
                    </span>
                </div>
            </div>
        </div>
        <!-- Chart: Profit & Loss -->
        <div class="bg-white rounded-lg shadow mb-8 p-6 flex flex-col items-center min-h-[250px]">
            <div class="w-full flex justify-between items-center mb-2">
                <span class="font-semibold text-gray-700">Profit & Loss</span>
                <span class="text-gray-400 text-xs">(Live Data)</span>
            </div>
            <div class="w-full">
                <canvas id="profitLossChart" height="70"></canvas>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var ctx = document.getElementById('profitLossChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'Profit/Loss',
                            data: @json($chartData),
                            fill: true,
                            borderColor: '#6C3EF4',
                            backgroundColor: 'rgba(108,62,244,0.08)',
                            tension: 0.4,
                            pointRadius: 3,
                            pointBackgroundColor: '#6C3EF4',
                            pointBorderColor: '#fff',
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            x: {
                                display: true,
                                ticks: { color: '#a3a3c2', font: { size: 11 } }
                            },
                            y: {
                                display: true,
                                ticks: { color: '#6C3EF4', font: { size: 11 } }
                            }
                        }
                    }
                });
            });
        </script>
        <!-- Account Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-500">Free Margin</div>
                <div class="text-xl font-bold text-gray-900">${{ number_format($account['free_margin'] ?? 0, 2) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-500">Margin Level</div>
                <div class="text-xl font-bold text-gray-900">{{ $account['margin_level'] ?? 'N/A' }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-500">Leverage</div>
                <div class="text-xl font-bold text-gray-900">{{ $account['leverage'] ?? 'N/A' }}</div>
            </div>
        </div>
        <!-- Trading Objectives (Progress Bars) -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Trading Objectives</h3>
            <div class="mb-4">
                <div class="flex justify-between mb-1 text-xs">
                    <span class="font-semibold text-blue-700">Daily Loss Limit</span>
                    <span class="font-semibold text-red-600">$0.00 Left</span>
                </div>
                <div class="w-full bg-red-100 rounded-full h-3 mb-2">
                    <div class="bg-red-500 h-3 rounded-full" style="width: 100%"></div>
                </div>
            </div>
            <div class="mb-4">
                <div class="flex justify-between mb-1 text-xs">
                    <span class="font-semibold text-blue-700">Max Loss Limit</span>
                    <span class="font-semibold text-yellow-600">$10,430 Left</span>
                </div>
                <div class="w-full bg-yellow-100 rounded-full h-3 mb-2">
                    <div class="bg-yellow-500 h-3 rounded-full" style="width: 98%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-1 text-xs">
                    <span class="font-semibold text-blue-700">Profit Target</span>
                    <span class="font-semibold text-green-600">$589.70 Left</span>
                </div>
                <div class="w-full bg-green-100 rounded-full h-3 mb-2">
                    <div class="bg-green-500 h-3 rounded-full" style="width: 0%"></div>
                </div>
            </div>
        </div>
        <!-- Trading History Table -->
        <div class="bg-white rounded-lg shadow p-6 mb-8 overflow-x-auto">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Trading History</h3>
            <table class="min-w-full text-xs text-left">
                <thead class="bg-violet-50 text-violet-700">
                    <tr>
                        <th class="px-4 py-2">Symbol</th>
                        <th class="px-4 py-2">Ticket</th>
                        <th class="px-4 py-2">Type</th>
                        <th class="px-4 py-2">Volume</th>
                        <th class="px-4 py-2">Open Price</th>
                        <th class="px-4 py-2">Close Price</th>
                        <th class="px-4 py-2">Profit</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($history as $trade)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $trade['symbol'] }}</td>
                        <td class="px-4 py-2">{{ $trade['ticket'] }}</td>
                        <td class="px-4 py-2">{{ $trade['type'] == 0 ? 'Buy' : 'Sell' }}</td>
                        <td class="px-4 py-2">{{ number_format($trade['volume'], 2) }}</td>
                        <td class="px-4 py-2">{{ number_format($trade['open_price'], 2) }}</td>
                        <td class="px-4 py-2">{{ number_format($trade['close_price'], 2) }}</td>
                        <td class="px-4 py-2 {{ $trade['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($trade['profit'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-2 text-center text-gray-400">No trading history available.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <!-- Disclaimer -->
        <div class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg p-4 mt-6">
            <b>DISCLAIMER</b><br>
            TRADING RESULTS ON THIS DASHBOARD ARE INFORMATIVE ONLY. REAL-TIME TRADING UPDATES CAN BE MONITORED THROUGH THE TRADING PLATFORM. THE ABOVE ACCOUNT STATISTICS ARE AUTOMATICALLY UPDATED PERIODICALLY WHEN YOU HAVE ACTIVE TRADES/INCASE OF ANY DISCREPANCIES, PLEASE BE PATIENT FOR THE DATA TO SYNC. IF THE ISSUE STILL PERSISTS, PLEASE CONTACT US THROUGH HELP CENTER.
        </div>
    </main>
</div>
@endsection
