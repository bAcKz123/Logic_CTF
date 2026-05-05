<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : 'unknown';
$rateLimitFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'logic_ctf_login_rate.json';
$windowSeconds = 60;
$maxAttemptsPerWindow = 8;
$lockSeconds = 180;
$now = time();

$rateData = [];
if (is_file($rateLimitFile)) {
    $decoded = json_decode((string) file_get_contents($rateLimitFile), true);
    if (is_array($decoded)) {
        $rateData = $decoded;
    }
}

if (!isset($rateData[$ip]) || !is_array($rateData[$ip])) {
    $rateData[$ip] = ['attempts' => [], 'lock_until' => 0];
}

$ipData = $rateData[$ip];
$ipData['attempts'] = array_values(array_filter(
    isset($ipData['attempts']) && is_array($ipData['attempts']) ? $ipData['attempts'] : [],
    static function ($ts) use ($now, $windowSeconds) {
        return is_int($ts) && ($now - $ts) <= $windowSeconds;
    }
));
$ipData['lock_until'] = isset($ipData['lock_until']) ? (int) $ipData['lock_until'] : 0;

if ($ipData['lock_until'] > $now) {
    $_SESSION['login_error'] = 'Too many login attempts. Please wait and try again.';
    header('Location: login.php');
    exit;
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($username === 'guest' && $password === 'guest') {
    $rateData[$ip] = ['attempts' => [], 'lock_until' => 0];
    file_put_contents($rateLimitFile, json_encode($rateData), LOCK_EX);

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'username' => 'guest',
        'coins' => 5,
    ];

    if (!isset($_SESSION['inventory'])) {
        $_SESSION['inventory'] = [];
    }
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    header('Location: mode_select.php');
    exit;
}

$ipData['attempts'][] = $now;
if (count($ipData['attempts']) >= $maxAttemptsPerWindow) {
    $ipData['lock_until'] = $now + $lockSeconds;
    $ipData['attempts'] = [];
}
$rateData[$ip] = $ipData;
file_put_contents($rateLimitFile, json_encode($rateData), LOCK_EX);

$_SESSION['login_error'] = 'Invalid username or password.';
header('Location: login.php');
exit;
