<?php
session_start();

// Entering login page always starts a fresh challenge state.
if (isset($_SESSION['user']) || isset($_SESSION['cart']) || isset($_SESSION['inventory'])) {
    session_unset();
    session_destroy();
    session_start();
}

$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTF Challenge - Login</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; background: #f5f5f5; }
        .card { max-width: 420px; margin: 60px auto; background: #fff; padding: 20px; border-radius: 8px; }
        label { display: block; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; }
        button {
            margin-top: 14px;
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
        .error { background: #ffe7e7; border: 1px solid #df8a8a; padding: 8px; margin-top: 10px; }
        .hint { margin-top: 12px; color: #555; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Challenge Login</h1>
        <form method="post" action="process_login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Sign in</button>
        </form>

        <?php if ($error !== ''): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <p class="hint">Account: guest / guest</p>
    </div>
</body>
</html>
