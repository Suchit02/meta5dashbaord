<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $apiUrl = 'https://apogee.xaltam.com/api/live-data';
        $response = Http::get($apiUrl);
        $data = $response->json();
        $account = $data['tradedata'] ?? [];
        $history = $account['history_trades'] ?? [];

        // Prepare chart data: profit/loss over time
        $chartLabels = [];
        $chartData = [];
        $totalProfit = 0;
        foreach ($history as $trade) {
            $chartLabels[] = date('d M H:i', $trade['close_time']);
            $chartData[] = round($trade['profit'], 2);
            $totalProfit += $trade['profit'];
        }
        $totalProfit = round($totalProfit, 2);

        // Determine current status (profit/loss)
        $currentStatus = $totalProfit > 0 ? 'Profit' : ($totalProfit < 0 ? 'Loss' : 'Break Even');

        return view('dashboard', [
            'user' => $user,
            'account' => $account,
            'history' => $history,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'totalProfit' => $totalProfit,
            'currentStatus' => $currentStatus,
        ]);
    }
}
