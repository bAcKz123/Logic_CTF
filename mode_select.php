<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTF Challenge - Mode Select</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; background: #f5f5f5; }
        .card { max-width: 860px; margin: 60px auto; background: #fff; padding: 24px; border-radius: 8px; }
        .top { margin-bottom: 16px; }
        .coin-icon { width: 16px; height: 16px; margin-left: 4px; vertical-align: -2px; }
        .modes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        .mode-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 16px;
            background: #fafafa;
        }
        .mode-item h2 { margin-top: 0; }
        .mode-item p { color: #333; min-height: 46px; }
        .btn {
            display: inline-block;
            margin-top: 8px;
            padding: 9px 14px;
            text-decoration: none;
            color: #fff;
            background: #1f4ed8;
            border: 1px solid #1d4ed8;
            border-radius: 6px;
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
            transition: background 0.2s ease, transform 0.05s ease;
        }
        .btn:hover { background: #1d4ed8; }
        .btn:active { transform: translateY(1px); }
        .btn.secondary { background: #15803d; }
        .btn.secondary:hover { background: #166534; }
        .top .nav-btn {
            display: inline-block;
            padding: 6px 10px;
            border: 1px solid #1d4ed8;
            border-radius: 7px;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
            transition: background 0.2s ease, transform 0.05s ease;
        }
        .top .nav-btn:hover { background: #1d4ed8; }
        .top .nav-btn:active { transform: translateY(1px); }
    </style>
</head>
<body>
    <div class="card">
        <h1>Choose Your Mode</h1>
        <div class="top">
            User: <b><?= htmlspecialchars($_SESSION['user']['username']) ?></b> |
            Coins: <b><?= (int) $_SESSION['user']['coins'] ?><img class="coin-icon" src="assets/coin.svg" alt="coin"></b> |
            <a class="nav-btn" href="logout.php">Logout</a>
        </div>

        <div class="modes">
            <div class="mode-item">
                <h2>CTF Coin Shop</h2>
                <p>Buy items with your current coins. This is the original shop flow.</p>
                <a class="btn" href="index.php">Enter Coin Shop</a>
            </div>

            <div class="mode-item">
                <h2>Buff Coin</h2>
                <p>Play mini game and earn extra coins before returning to the shop.</p>
                <a class="btn secondary" href="buff_coin.php">Enter Buff Coin</a>
            </div>
        </div>
    </div>
</body>
</html>
