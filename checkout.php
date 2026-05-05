<?php
session_start();

$products = [
    'shirt' => ['name' => 'Shirt', 'price' => 2],
    'hat' => ['name' => 'Hat', 'price' => 3],
    'flag' => ['name' => 'Flag', 'price' => 10],
];

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !isset($_POST['checkout'])
) {
    header('Location: cart.php');
    exit;
}

if (
    !isset($_SESSION['checkout_token'], $_POST['checkout_token']) ||
    !hash_equals($_SESSION['checkout_token'], (string) $_POST['checkout_token'])
) {
    header('Location: cart.php');
    exit;
}
unset($_SESSION['checkout_token']);

if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = [];
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$total = 0;
foreach ($_SESSION['cart'] as $code => $q) {
    if (isset($products[$code])) {
        // Vulnerability: no validation for negative quantity.
        $total += ((int) $q) * $products[$code]['price'];
    }
}

if ($total > 0 && $total <= $_SESSION['user']['coins']) {
    $_SESSION['user']['coins'] -= $total;

    foreach ($_SESSION['cart'] as $code => $q) {
        if (!isset($_SESSION['inventory'][$code])) {
            $_SESSION['inventory'][$code] = 0;
        }
        $_SESSION['inventory'][$code] += (int) $q;
    }

    $message = "Payment successful. Order total: {$total} coins.";
    $messageClass = 'ok';
    $_SESSION['cart'] = [];
} else {
    $message = 'Payment failed';
    $messageClass = 'error';
}

$capturedFlag = isset($_SESSION['inventory']['flag']) && (int) $_SESSION['inventory']['flag'] > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTF Challenge - Checkout</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; background: #f5f5f5; }
        .card { max-width: 720px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; }
        .ok { background: #e8f7e8; border: 1px solid #80c080; padding: 10px; color: #14532d; }
        .error { background: #fee2e2; border: 1px solid #ef4444; padding: 10px; color: #b91c1c; font-weight: 700; }
        ul { margin-top: 8px; }
        .coin-icon { width: 16px; height: 16px; margin-left: 4px; vertical-align: -2px; }
        .nav-btn {
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
        .nav-btn:hover { background: #1d4ed8; }
        .nav-btn:active { transform: translateY(1px); }
    </style>
</head>
<body>
    <div class="card">
        <h1>Order Status</h1>
        <p class="<?= htmlspecialchars($messageClass) ?>"><?= htmlspecialchars($message) ?></p>

        <p>Remaining coins: <b><?= (int) $_SESSION['user']['coins'] ?><img class="coin-icon" src="assets/coin.svg" alt="coin"></b></p>
        <h3>Inventory</h3>
        <?php if (empty($_SESSION['inventory'])): ?>
            <p>(empty)</p>
        <?php else: ?>
            <ul>
                <?php foreach ($_SESSION['inventory'] as $code => $q): ?>
                    <li><?= htmlspecialchars($products[$code]['name']) ?>: <?= (int) $q ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <p><a class="nav-btn" href="index.php">Back to Shop</a> <a class="nav-btn" href="cart.php">Back to Cart</a> <a class="nav-btn" href="buff_coin.php">Buff Coin</a> <a class="nav-btn" href="mode_select.php">Mode Select</a></p>

        <?php if ($capturedFlag): ?>
            <p class="ok"><b>FFF{logic_bug_negative_quantity}</b></p>
        <?php endif; ?>

        <hr>
    </div>
</body>
</html>
