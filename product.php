<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$products = [
    'shirt' => [
        'name' => 'Shirt',
        'price' => 2,
        'image' => 'assets/shirt.png',
        'desc' => 'Basic blue shirt for new players.',
    ],
    'hat' => [
        'name' => 'Hat',
        'price' => 3,
        'image' => 'assets/hat.png',
        'desc' => 'Navy hat with a bright stripe.',
    ],
    'flag' => [
        'name' => 'Flag',
        'price' => 10,
        'image' => 'assets/flag.svg',
        'desc' => 'Rare red flag for elite collectors.',
    ],
];

$code = isset($_GET['code']) ? (string) $_GET['code'] : '';
if (!isset($products[$code])) {
    header('Location: index.php');
    exit;
}

$product = $products[$code];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTF Challenge - Product Detail</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; background: #f5f5f5; }
        .card { max-width: 720px; margin: 0 auto; background: #fff; padding: 24px; border-radius: 8px; }
        .product-image { width: 100%; max-width: 360px; display: block; margin: 0 auto 14px; background: #fff; border: 1px solid #eee; border-radius: 8px; }
        .price { font-size: 20px; font-weight: 700; margin: 10px 0; }
        .meta { margin-bottom: 14px; color: #333; }
        .coin-icon { width: 16px; height: 16px; margin-left: 4px; vertical-align: -2px; }
        input[type="number"] { width: 100%; max-width: 220px; padding: 8px; box-sizing: border-box; }
        button {
            margin-top: 12px;
            padding: 9px 14px;
            border: 1px solid #1d4ed8;
            border-radius: 8px;
            background: #2563eb;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
            transition: background 0.2s ease, transform 0.05s ease;
        }
        button:hover { background: #1d4ed8; }
        button:active { transform: translateY(1px); }
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
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <p>
            User: <b><?= htmlspecialchars($_SESSION['user']['username']) ?></b> |
            Coins: <b><?= (int) $_SESSION['user']['coins'] ?><img class="coin-icon" src="assets/coin.svg" alt="coin"></b> |
            <a class="nav-btn" href="mode_select.php">Mode Select</a>
            <a class="nav-btn" href="buff_coin.php">Buff Coin</a>
            <a class="nav-btn" href="logout.php">Logout</a>
        </p>

        <img class="product-image" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        <p class="price"><?= (int) $product['price'] ?> <img class="coin-icon" src="assets/coin.svg" alt="coin"></p>
        <p class="meta"><?= htmlspecialchars($product['desc']) ?></p>

        <form method="post" action="cart.php">
            <input type="hidden" name="product_code" value="<?= htmlspecialchars($code) ?>">
            <label for="qty">Quantity</label><br>
            <input id="qty" type="number" name="qty" value="1" min="0" step="1">
            <br>
            <button type="submit" name="add_to_cart_single">Add to Cart</button>
        </form>

        <p><a class="nav-btn" href="index.php">Back to Shop</a> <a class="nav-btn" href="cart.php">Go to Cart</a> <a class="nav-btn" href="mode_select.php">Mode Select</a></p>
    </div>
</body>
</html>
