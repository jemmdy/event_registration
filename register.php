<?php 
require 'config.php';

// ✅ Redirect logged-in users
if (!empty($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: participant/dashboard.php');
    }
    exit;
}

// ✅ Messages
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);

// ✅ CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - Event Registration</title>
<style>
/* ===== Page Style ===== */
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(135deg, #10b981, #2563eb);
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* ===== Registration Card ===== */
.register-container {
    background: #fff;
    padding: 2.5rem;
    border-radius: 16px;
    width: 100%;
    max-width: 440px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    animation: fadeIn 0.6s ease-in-out;
}

/* ===== Animations ===== */
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-10px);}
    to {opacity: 1; transform: translateY(0);}
}

/* ===== Text & Titles ===== */
h1 {
    text-align: center;
    color: #111827;
    margin-bottom: 0.5rem;
}
p.subtitle {
    text-align: center;
    color: #6b7280;
    margin-bottom: 1.5rem;
}

/* ===== Inputs ===== */
label {
    display: block;
    margin-top: 1rem;
    font-weight: 600;
    color: #374151;
}
input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    margin-top: 0.3rem;
    font-size: 1rem;
    transition: border-color 0.3s, box-shadow 0.3s;
}
input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 6px rgba(37,99,235,0.3);
    outline: none;
}

/* ===== Buttons ===== */
button {
    width: 100%;
    margin-top: 1.2rem;
    background: #10b981;
    color: #fff;
    border: none;
    padding: 0.9rem;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s ease;
}
button:hover {
    background: #0f766e;
}

.login-btn {
    width: 100%;
    margin-top: 0.8rem;
    background: #2563eb;
    color: #fff;
    border: none;
    padding: 0.9rem;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}
.login-btn:hover {
    background: #1e4fd3;
}

/* ===== Message Boxes ===== */
.msg {
    padding: 0.7rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    text-align: center;
    font-weight: 500;
}
.msg.error {
    background: #fde8e8;
    color: #a10000;
    border: 1px solid #f5bebe;
}
.msg.success {
    background: #e7f9ee;
    color: #0f5132;
    border: 1px solid #c5e6cd;
}

/* ===== Footer Text ===== */
p.footer {
    text-align: center;
    color: #6b7280;
    margin-top: 1rem;
}
p.footer a {
    color: #2563eb;
    text-decoration: none;
}
p.footer a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<main class="register-container">
    <form action="register_action.php" method="post">
        <h1>Create an Account</h1>
        <p class="subtitle">Register to join and manage your event participation</p>

        <?php if ($error): ?>
            <div class="msg error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="msg success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" placeholder="Enter your full name" required>

        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" placeholder="Enter your email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Create a strong password" required minlength="6">

        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <button type="submit">Register</button>
    </form>

    <a href="login.php" class="login-btn">Already have an account? Login</a>
</main>

</body>
</html>
