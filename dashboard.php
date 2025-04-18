<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user info
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$account_login = $user['account_login'];

// Fetch MetaTrader live data from API
$api_url = 'https://apogee.xaltam.com/api/live-data';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$account = $data['tradedata'] ?? [];
$history = $account['history_trades'] ?? [];
$live_trades = $account['live_trades'] ?? [];

function formatNum($num) {
    return number_format((float)$num, 2, '.', ',');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Fundings4u</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <div class="dashboard-header">
        <h2>Welcome, <?= htmlspecialchars($user['name']) ?></h2>
        <a class="logout-link" href="logout.php">Logout</a>
    </div>
    <div class="account-info">
        <h3>Account Info</h3>
        <table class="account-info-table">
            <tr><td>MetaTrader Login</td><td><?= htmlspecialchars($account['account_login'] ?? $account_login) ?></td></tr>
            <tr><td>Balance</td><td>$<?= isset($account['account_balance']) ? formatNum($account['account_balance']) : 'N/A' ?></td></tr>
            <tr><td>Equity</td><td>$<?= isset($account['equity']) ? formatNum($account['equity']) : 'N/A' ?></td></tr>
            <tr><td>Free Margin</td><td>$<?= isset($account['free_margin']) ? formatNum($account['free_margin']) : 'N/A' ?></td></tr>
            <tr><td>Margin Level</td><td><?= isset($account['margin_level']) ? formatNum($account['margin_level']) : 'N/A' ?></td></tr>
            <tr><td>Leverage</td><td><?= isset($account['leverage']) ? htmlspecialchars($account['leverage']) : 'N/A' ?></td></tr>
        </table>
    </div>
    <div class="trading-history">
        <h3>Trading History</h3>
        <table class="trading-history-table">
            <tr>
                <th>Symbol</th>
                <th>Ticket</th>
                <th>Type</th>
                <th>Volume</th>
                <th>Open Price</th>
                <th>Close Price</th>
                <th>Profit</th>
            </tr>
            <?php if ($history): foreach ($history as $trade): ?>
                <tr>
                    <td><?= htmlspecialchars($trade['symbol']) ?></td>
                    <td><?= htmlspecialchars($trade['ticket']) ?></td>
                    <td><?= $trade['type'] == 0 ? 'Buy' : 'Sell' ?></td>
                    <td><?= formatNum($trade['volume']) ?></td>
                    <td><?= formatNum($trade['open_price']) ?></td>
                    <td><?= formatNum($trade['close_price']) ?></td>
                    <td><?= formatNum($trade['profit']) ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="7">No trading history available.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>
</body>
</html>
