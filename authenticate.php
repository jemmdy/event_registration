<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// CSRF check
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: login.php');
    exit;
}

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    $_SESSION['error'] = 'Please enter your email and password.';
    header('Location: login.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Invalid email or password.';
        header('Location: login.php');
        exit;
    }

    if (!$user['is_active']) {
        $_SESSION['error'] = 'Account disabled. Contact admin.';
        header('Location: login.php');
        exit;
    }

    // success
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];

    if ($user['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: participant/dashboard.php');
    }
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = 'Server error.';
    header('Location: login.php');
    exit;
}
