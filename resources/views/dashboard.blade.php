@extends('layouts.app')
@section('content')
<div class="min-h-screen flex bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg flex flex-col items-center py-8">
        <div class="flex items-center mb-8">
            <div class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center mr-2 overflow-hidden">
                <img src="/asset/logo/logo.png" alt="Logo" class="object-contain w-8 h-8">
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
        <!-- Trading History Table with Pagination -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center mb-4">
                <span class="text-lg font-semibold text-gray-800 mr-2">Trading History</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs md:text-sm text-left rounded-xl shadow bg-white" id="historyTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 font-bold text-gray-500 uppercase">Symbol</th>
                            <th class="px-4 py-2 font-bold text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-2 font-bold text-gray-500 uppercase">Volume</th>
                            <th class="px-4 py-2 font-bold text-gray-500 uppercase">Open Price</th>
                            <th class="px-4 py-2 font-bold text-gray-500 uppercase">Close Price</th>
                            <th class="px-4 py-2 font-bold text-gray-500 uppercase">Stop Loss</th>
                            <th class="px-4 py-2 font-bold text-gray-500 uppercase">Take Profit</th>
                            <th class="px-4 py-2 font-bold text-gray-500 uppercase">Open Date</th>
                            <th class="px-4 py-2 font-bold text-gray-500 uppercase">Close Date</th>
                            <th class="px-4 py-2 font-bold text-gray-500 uppercase text-right">Profit</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody" class="divide-y divide-gray-100">
                        <!-- Rows will be injected by JS -->
                    </tbody>
                </table>
            </div>
            <!-- Pagination Controls -->
            <div class="flex justify-center items-center mt-4 gap-2">
                <button id="prevPage" class="px-4 py-2 rounded bg-gray-100 text-gray-400 font-semibold" disabled>Previous</button>
                <span id="pageInfo" class="px-4 text-gray-500 text-sm"></span>
                <button id="nextPage" class="px-4 py-2 rounded bg-gray-100 text-gray-400 font-semibold" disabled>Next</button>
            </div>
        </div>
        <style>
            #historyTable tr {
                transition: background 0.2s;
            }
            #historyTable tbody tr:hover {
                background: #f3f4f6;
            }
        </style>
        <script>
            const historyData = @json($history);
            console.log('HISTORY SAMPLE:', historyData[0]);
            const rowsPerPage = 5;
            let currentPage = 1;
            function renderTable() {
                const tbody = document.getElementById('historyTableBody');
                tbody.innerHTML = '';
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const pageData = historyData.slice(start, end);
                if (pageData.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="11" class="text-center py-4 text-gray-400">No trading history available.</td></tr>';
                } else {
                    pageData.forEach(trade => {
                        function safe(val, isDate = false) {
                            if (val === undefined || val === null || val === "") return "-";
                            if (isDate) {
                                // If it's a Unix timestamp (number), convert; otherwise, show as-is
                                if (!isNaN(val) && Number(val) > 10000) {
                                    return new Date(Number(val) * 1000).toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });
                                } else {
                                    return val;
                                }
                            }
                            return val;
                        }
                        tbody.innerHTML += `
                        <tr class="border-b last:border-0">
                            <td class="px-4 py-2 font-bold text-gray-700">${trade.symbol ?? '-'}</td>
                            <td class="px-4 py-2">
                                ${
                                    trade.type === 0 || trade.type === '0' || (typeof trade.type === 'string' && trade.type.toLowerCase() === 'buy')
                                        ? '<span class=\'bg-violet-100 text-violet-700 px-3 py-1 rounded-full text-xs font-bold\'>Buy</span>'
                                        : (trade.type === 1 || trade.type === '1' || (typeof trade.type === 'string' && trade.type.toLowerCase() === 'sell')
                                            ? '<span class=\'bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold\'>Sell</span>'
                                            : '-')
                                }
                            </td>
                            <td class="px-4 py-2">${trade.open_date ?? '-'}</td>
                            <td class="px-4 py-2">${trade.open_time ?? '-'}</td>
                            <td class="px-4 py-2">${trade.open ?? '-'}</td>
                            <td class="px-4 py-2">${trade.close_date ?? '-'}</td>
                            <td class="px-4 py-2">${trade.close_time ?? '-'}</td>
                            <td class="px-4 py-2">${trade.close ?? '-'}</td>
                            <td class="px-4 py-2">${trade.tp ?? '-'}</td>
                            <td class="px-4 py-2">${trade.sl ?? '-'}</td>
                            <td class="px-4 py-2">${trade.lots ?? '-'}</td>
                            <td class="px-4 py-2">${trade.commission ?? '-'}</td>
                            <td class="px-4 py-2">${trade.profit ?? '-'}</td>
                        </tr>
                        `;
                    });
                }
                // Render body for only the 10 columns you want
                tbody.innerHTML = '';
                pageData.forEach(trade => {
                    let row = '<tr class="hover:bg-gray-50">';
                    // Symbol
                    row += `<td class='px-4 py-2 font-bold text-gray-700'>${trade.symbol ?? '-'}</td>`;
                    // Type
                    if (trade.type === 0 || trade.type === '0' || (typeof trade.type === 'string' && trade.type.toLowerCase() === 'buy')) {
                        row += `<td class='px-4 py-2'><span class='bg-violet-100 text-violet-700 px-3 py-1 rounded-full text-xs font-bold'>Buy</span></td>`;
                    } else if (trade.type === 1 || trade.type === '1' || (typeof trade.type === 'string' && trade.type.toLowerCase() === 'sell')) {
                        row += `<td class='px-4 py-2'><span class='bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold'>Sell</span></td>`;
                    } else {
                        row += `<td class='px-4 py-2'>-</td>`;
                    }
                    // Volume
                    row += `<td class='px-4 py-2'>${trade.volume ?? '-'}</td>`;
                    // Open Price
                    row += `<td class='px-4 py-2'>${trade.open_price ?? '-'}</td>`;
                    // Close Price
                    row += `<td class='px-4 py-2'>${trade.close_price ?? '-'}</td>`;
                    // Stop Loss
                    row += `<td class='px-4 py-2'>${trade.stop_loss ?? '-'}</td>`;
                    // Take Profit
                    row += `<td class='px-4 py-2'>${trade.take_profit ?? '-'}</td>`;
                    // Open Date
                    if (trade.open_time && !isNaN(trade.open_time)) {
                        const d = new Date(Number(trade.open_time) * 1000);
                        row += `<td class='px-4 py-2'>${d.toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true })}</td>`;
                    } else {
                        row += `<td class='px-4 py-2'>-</td>`;
                    }
                    // Close Date
                    if (trade.close_time && !isNaN(trade.close_time)) {
                        const d = new Date(Number(trade.close_time) * 1000);
                        row += `<td class='px-4 py-2'>${d.toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true })}</td>`;
                    } else {
                        row += `<td class='px-4 py-2'>-</td>`;
                    }
                    // Profit
                    if (trade.profit !== undefined && trade.profit !== null && trade.profit !== '') {
                        const profit = parseFloat(trade.profit);
                        row += `<td class='px-4 py-2 text-right font-bold ${profit >= 0 ? 'text-green-600' : 'text-red-600'}'>${profit >= 0 ? '+' : ''}$${profit.toFixed(2)}</td>`;
                    } else {
                        row += `<td class='px-4 py-2 text-right'>-</td>`;
                    }
                    row += '</tr>';
                    tbody.innerHTML += row;
                });
                // Update pagination controls
                document.getElementById('prevPage').disabled = currentPage === 1;
                document.getElementById('nextPage').disabled = end >= historyData.length;
                document.getElementById('pageInfo').textContent = `Showing ${pageData.length ? (start + 1) : 0} to ${start + pageData.length} of ${historyData.length}`;
            }
            document.getElementById('prevPage').addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                }
            });
            document.getElementById('nextPage').addEventListener('click', function() {
                if ((currentPage * rowsPerPage) < historyData.length) {
                    currentPage++;
                    renderTable();
                }
            });
            // Initial render
            renderTable();
        </script>
        <!-- Disclaimer -->
        <div class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg p-4 mt-6">
            <b>DISCLAIMER</b><br>
            TRADING RESULTS ON THIS DASHBOARD ARE INFORMATIVE ONLY. REAL-TIME TRADING UPDATES CAN BE MONITORED THROUGH THE TRADING PLATFORM. THE ABOVE ACCOUNT STATISTICS ARE AUTOMATICALLY UPDATED PERIODICALLY WHEN YOU HAVE ACTIVE TRADES/INCASE OF ANY DISCREPANCIES, PLEASE BE PATIENT FOR THE DATA TO SYNC. IF THE ISSUE STILL PERSISTS, PLEASE CONTACT US THROUGH HELP CENTER.
        </div>
    </main>
</div>
@endsection
