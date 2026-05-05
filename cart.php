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

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$maxQtyPerItem = 200;
$shouldRedirect = false;
$cartNotice = isset($_SESSION['cart_notice']) ? (string) $_SESSION['cart_notice'] : '';
unset($_SESSION['cart_notice']);

function parseQtyValue($rawValue)
{
    $raw = trim((string) $rawValue);
    if (!preg_match('/^-?\d+$/', $raw)) {
        return null;
    }

    $negative = $raw[0] === '-';
    $digits = $negative ? substr($raw, 1) : $raw;
    $digits = ltrim($digits, '0');
    if ($digits === '') {
        return 0;
    }

    $maxInt = (string) PHP_INT_MAX;
    if (strlen($digits) > strlen($maxInt)) {
        return null;
    }
    if (strlen($digits) === strlen($maxInt) && strcmp($digits, $maxInt) > 0) {
        return null;
    }

    $value = (int) $digits;
    return $negative ? -$value : $value;
}

function safeAddInt($left, $right)
{
    $a = (int) $left;
    $b = (int) $right;
    if ($b > 0 && $a > PHP_INT_MAX - $b) {
        return null;
    }
    if ($b < 0 && $a < -PHP_INT_MAX - $b) {
        return null;
    }
    return $a + $b;
}

if (isset($_POST['add_to_cart_single'])) {
    $code = isset($_POST['product_code']) ? (string) $_POST['product_code'] : '';
    $qty = isset($_POST['qty']) ? parseQtyValue($_POST['qty']) : 0;

    if ($qty === null) {
        $_SESSION['cart_notice'] = 'Quantity is too large or invalid.';
    } elseif (isset($products[$code]) && $qty !== 0) {
        if (!isset($_SESSION['cart'][$code])) {
            $_SESSION['cart'][$code] = 0;
        }
        $nextQty = safeAddInt($_SESSION['cart'][$code], $qty);
        if ($nextQty === null) {
            $_SESSION['cart_notice'] = 'Quantity overflow blocked by server.';
        } else {
            if ($nextQty > $maxQtyPerItem) {
                $nextQty = $maxQtyPerItem;
                $_SESSION['cart_notice'] = "Max quantity per item is {$maxQtyPerItem}.";
            }
            $_SESSION['cart'][$code] = $nextQty;
        }
    }
    $shouldRedirect = true;
}

if (isset($_POST['add_to_cart']) && isset($_POST['qty'])) {
    $_SESSION['cart'] = [];
    foreach ($products as $code => $product) {
        $q = isset($_POST['qty'][$code]) ? parseQtyValue($_POST['qty'][$code]) : 0;
        if ($q === null) {
            $_SESSION['cart_notice'] = 'One or more quantities are too large or invalid.';
            continue;
        }
        if ($q !== 0) {
            if ($q > $maxQtyPerItem) {
                $q = $maxQtyPerItem;
                $_SESSION['cart_notice'] = "Max quantity per item is {$maxQtyPerItem}.";
            }
            $_SESSION['cart'][$code] = $q;
        }
    }
    $shouldRedirect = true;
}

if (isset($_POST['cart_action'])) {
    $code = isset($_POST['product_code']) ? (string) $_POST['product_code'] : '';
    $action = (string) $_POST['cart_action'];

    if (isset($products[$code]) && isset($_SESSION['cart'][$code])) {
        if ($action === 'inc') {
            if ($_SESSION['cart'][$code] >= $maxQtyPerItem) {
                $_SESSION['cart_notice'] = "Max quantity per item is {$maxQtyPerItem}.";
            } elseif ($_SESSION['cart'][$code] < PHP_INT_MAX) {
                $_SESSION['cart'][$code] += 1;
            }
        } elseif ($action === 'dec') {
            if ($_SESSION['cart'][$code] > -PHP_INT_MAX) {
                $_SESSION['cart'][$code] -= 1;
            }
            if ($_SESSION['cart'][$code] <= 0) {
                unset($_SESSION['cart'][$code]);
            }
        } elseif ($action === 'remove') {
            unset($_SESSION['cart'][$code]);
        }
    }
    $shouldRedirect = true;
}

if ($shouldRedirect) {
    header('Location: cart.php');
    exit;
}

$cartTotal = 0;
foreach ($_SESSION['cart'] as $code => $q) {
    if (isset($products[$code])) {
        $cartTotal += ((int) $q) * $products[$code]['price'];
    }
}

// One-time checkout token helps prevent replay/double-submit races.
$_SESSION['checkout_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTF Challenge - Cart</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; background: #f5f5f5; }
        .card { max-width: 720px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; border-bottom: 1px solid #ddd; padding: 8px; }
        button {
            padding: 7px 11px;
            margin-right: 6px;
            border: 1px solid #1d4ed8;
            border-radius: 7px;
            background: #2563eb;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
            transition: background 0.2s ease, transform 0.05s ease;
        }
        button:hover { background: #1d4ed8; }
        button:active { transform: translateY(1px); }
        .actions form { display: inline-block; margin: 0; }
        .coin-icon { width: 16px; height: 16px; margin-left: 4px; vertical-align: -2px; }
        .total { margin-top: 14px; font-size: 18px; font-weight: 700; }
        .footer-actions { margin-top: 10px; display: flex; align-items: center; gap: 10px; }
        .checkout-form { margin: 0; display: inline-block; }
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
        <h1>Cart Review</h1>
        <?php if ($cartNotice !== ''): ?>
            <p style="background:#fff4ce;border:1px solid #d9b650;padding:8px;border-radius:6px;"><?= htmlspecialchars($cartNotice) ?></p>
        <?php endif; ?>
        <p>
            Current coins: <b><?= (int) $_SESSION['user']['coins'] ?><img class="coin-icon" src="assets/coin.svg" alt="coin"></b> |
            User: <b><?= htmlspecialchars($_SESSION['user']['username']) ?></b> |
            <a class="nav-btn" href="mode_select.php">Mode Select</a>
            <a class="nav-btn" href="buff_coin.php">Buff Coin</a>
            <a class="nav-btn" href="logout.php">Logout</a>
        </p>

        <?php if (empty($_SESSION['cart'])): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $code => $q): ?>
                        <tr>
                            <td><?= htmlspecialchars($products[$code]['name']) ?></td>
                            <td><?= (int) $products[$code]['price'] ?> <img class="coin-icon" src="assets/coin.svg" alt="coin"></td>
                            <td><?= (int) $q ?></td>
                            <td class="actions">
                                <form method="post">
                                    <input type="hidden" name="product_code" value="<?= htmlspecialchars($code) ?>">
                                    <button type="submit" name="cart_action" value="dec">-</button>
                                </form>
                                <form method="post">
                                    <input type="hidden" name="product_code" value="<?= htmlspecialchars($code) ?>">
                                    <button type="submit" name="cart_action" value="inc">+</button>
                                </form>
                                <form method="post">
                                    <input type="hidden" name="product_code" value="<?= htmlspecialchars($code) ?>">
                                    <button type="submit" name="cart_action" value="remove">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p class="total">Total: <?= (int) $cartTotal ?> <img class="coin-icon" src="assets/coin.svg" alt="coin"></p>

        <div class="footer-actions">
            <a class="nav-btn" href="index.php">Back to Shop</a>
            <form class="checkout-form" method="post" action="checkout.php">
                <input type="hidden" name="checkout_token" value="<?= htmlspecialchars($_SESSION['checkout_token']) ?>">
                <button type="submit" name="checkout">Checkout</button>
            </form>
        </div>
    </div>
</body>
</html>
