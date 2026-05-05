<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$products = [
    'shirt' => ['name' => 'Shirt', 'price' => 2, 'image' => 'assets/shirt.png', 'desc' => 'Basic blue shirt for new players.'],
    'hat' => ['name' => 'Hat', 'price' => 3, 'image' => 'assets/hat.png', 'desc' => 'Navy hat with a bright stripe.'],
    'flag' => ['name' => 'Flag', 'price' => 10, 'image' => 'assets/flag.svg', 'desc' => 'Rare red flag for elite collectors.'],
];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTF Challenge - Shop</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; background: #f5f5f5; }
        .card { max-width: 980px; margin: 0 auto; background: #fff; padding: 24px; border-radius: 8px; }
        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin: 18px 0;
        }
        .product-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            background: #fafafa;
        }
        .product-item > a > img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
            background: #fff;
            border: 1px solid #eee;
            margin-bottom: 10px;
        }
        .name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .price {
            color: #222;
            margin-bottom: 10px;
        }
        input[type="number"] { width: 100%; padding: 7px; box-sizing: border-box; }
        button { margin-top: 12px; padding: 8px 12px; }
        .top { margin-bottom: 12px; }
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
        <h1>CTF Coin Shop</h1>
        <div class="top">
            User: <b><?= htmlspecialchars($_SESSION['user']['username']) ?></b> |
            Coins: <b><?= (int) $_SESSION['user']['coins'] ?><img class="coin-icon" src="assets/coin.svg" alt="coin"></b> |
            <a class="nav-btn" href="mode_select.php">Mode Select</a>
            <a class="nav-btn" href="buff_coin.php">Buff Coin</a>
            <a class="nav-btn" href="cart.php">Go to Cart</a>
            <a class="nav-btn" href="logout.php">Logout</a>
        </div>

        <div class="products">
            <?php foreach ($products as $code => $product): ?>
                <div class="product-item">
                    <a href="product.php?code=<?= urlencode($code) ?>">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </a>
                    <div class="name">
                        <a href="product.php?code=<?= urlencode($code) ?>"><?= htmlspecialchars($product['name']) ?></a>
                    </div>
                    <div class="price"><?= (int) $product['price'] ?><img class="coin-icon" src="assets/coin.svg" alt="coin"></div>
                    <p><?= htmlspecialchars($product['desc']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</body>
</html>
