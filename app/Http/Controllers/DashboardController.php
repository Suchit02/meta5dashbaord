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
        return view('dashboard', compact('user', 'account', 'history'));
    }
}
