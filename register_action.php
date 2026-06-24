<?php 
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

// CSRF check
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: register.php');
    exit;
}

// Collect and validate inputs
$name = trim($_POST['name'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$name || !$email || !$password) {
    $_SESSION['error'] = 'All fields are required.';
    header('Location: register.php');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['error'] = 'Password must be at least 6 characters.';
    header('Location: register.php');
    exit;
}

// Check if email already exists
try {
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        $_SESSION['error'] = 'Email is already registered. Please log in.';
        header('Location: register.php');
        exit;
    }

    // Insert new participant
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role, is_active)
                           VALUES (?, ?, ?, 'participant', 1)");
    $stmt->execute([$name, $email, $hashed]);

    $_SESSION['success'] = 'Registration successful! You can now log in.';
    header('Location: register.php');
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = 'Server error. Please try again.';
    header('Location: register.php');
    exit;
}
