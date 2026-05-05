<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['buff_streak'])) {
    $_SESSION['buff_streak'] = 0;
}
if (!isset($_SESSION['buff_csrf'])) {
    $_SESSION['buff_csrf'] = bin2hex(random_bytes(32));
}

$message = '';
$messageClass = 'ok';
$rolled = null;
$bet = 1;
$guessNumber = 10;

if (isset($_SESSION['buff_flash']) && is_array($_SESSION['buff_flash'])) {
    $flash = $_SESSION['buff_flash'];
    $message = isset($flash['message']) ? (string) $flash['message'] : '';
    $messageClass = isset($flash['class']) ? (string) $flash['class'] : 'ok';
    unset($_SESSION['buff_flash']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['predict'])) {
    $csrf = isset($_POST['buff_csrf']) ? (string) $_POST['buff_csrf'] : '';
    $betRaw = isset($_POST['bet']) ? trim((string) $_POST['bet']) : '';
    $guessRaw = isset($_POST['guess_number']) ? trim((string) $_POST['guess_number']) : '';
    $playerCoins = (int) $_SESSION['user']['coins'];

    if (!hash_equals($_SESSION['buff_csrf'], $csrf)) {
        $messageClass = 'error';
        $message = 'Invalid request token.';
    } elseif (!ctype_digit($betRaw)) {
        $messageClass = 'error';
        $message = 'Bet must be greater than 0 coin.';
    } else {
        $validatedBet = filter_var(
            $betRaw,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => $playerCoins]]
        );
        if ($validatedBet === false) {
            $messageClass = 'error';
            $message = 'Not enough coins to place this bet.';
        } else {
            $bet = $validatedBet;
        }
    }

    if ($message === '') {
        $validatedGuess = filter_var(
            $guessRaw,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 20]]
        );
        if ($validatedGuess === false) {
            $messageClass = 'error';
            $message = 'Guess number must be between 1 and 20.';
        } else {
            $guessNumber = $validatedGuess;
        }
    }

    if ($message === '') {
        if ($bet > $playerCoins) {
            $messageClass = 'error';
            $message = 'Not enough coins to place this bet.';
        }
    }

    if ($message === '') {
        // Force losing outcome: roll any number except the guessed one.
        do {
            $rolled = random_int(1, 20);
        } while ($rolled === $guessNumber);

        $_SESSION['buff_streak'] += 1;
        $_SESSION['user']['coins'] -= $bet;
        if ($_SESSION['user']['coins'] < 0) {
            $_SESSION['user']['coins'] = 0;
        }
        $messageClass = 'error';
        $message = "Rolled {$rolled}, you guessed {$guessNumber}. You lost {$bet} coin(s).";
    }

    $_SESSION['buff_flash'] = [
        'message' => $message,
        'class' => $messageClass,
    ];
    header('Location: buff_coin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTF Challenge - Buff Coin</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; background: #f5f5f5; }
        .card { max-width: 720px; margin: 40px auto; background: #fff; padding: 20px; border-radius: 8px; }
        .coin-icon { width: 16px; height: 16px; margin-left: 4px; vertical-align: -2px; }
        .game-box {
            margin-top: 16px;
            padding: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fafafa;
        }
        .ok { background: #e8f7e8; border: 1px solid #80c080; padding: 10px; color: #14532d; margin-bottom: 10px; }
        .error { background: #fee2e2; border: 1px solid #ef4444; padding: 10px; color: #b91c1c; margin-bottom: 10px; }
        label { display: block; margin-top: 10px; font-weight: 700; }
        input[type="number"] { width: 100%; max-width: 220px; padding: 8px; margin-top: 4px; box-sizing: border-box; }
        button {
            margin-top: 12px;
            padding: 10px 14px;
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
        .muted { color: #666; }
        .result { margin-top: 10px; }
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
        <h1>Buff Coin Mini Game</h1>
        <p>
            User: <b><?= htmlspecialchars($_SESSION['user']['username']) ?></b> |
            Coins: <b><?= (int) $_SESSION['user']['coins'] ?><img class="coin-icon" src="assets/coin.svg" alt="coin"></b> |
            <a class="nav-btn" href="logout.php">Logout</a>
        </p>

        <div class="game-box">
            <?php if ($message !== ''): ?>
                <div class="<?= htmlspecialchars($messageClass) ?> result"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <p>Predict exact number (1-20). Win pays x2 bet (but this game is cursed).</p>
            <form method="post">
                <input type="hidden" name="buff_csrf" value="<?= htmlspecialchars($_SESSION['buff_csrf']) ?>">
                <label for="bet">Bet coins</label>
                <input id="bet" type="number" name="bet" min="1" step="1" value="<?= (int) $bet ?>">

                <label for="guess_number">Predict number (1-20)</label>
                <input id="guess_number" type="number" name="guess_number" min="1" max="20" step="1" value="<?= (int) $guessNumber ?>">

                <button type="submit" name="predict" value="1">Predict</button>
            </form>
        </div>

        <p>
            <a class="nav-btn" href="mode_select.php">Back to Mode Select</a>
            <a class="nav-btn" href="index.php">Go to Coin Shop</a>
        </p>
    </div>
</body>
</html>
