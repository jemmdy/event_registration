<?php 
require 'config.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Event Registration</title>
<style>
/* ===== Page Styling ===== */
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(135deg, #10b981, #2563eb);
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* ===== Login Card ===== */
.login-container {
    background: #fff;
    padding: 2.5rem;
    border-radius: 16px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    animation: fadeIn 0.6s ease-in-out;
}

/* ===== Animations ===== */
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-10px);}
    to {opacity: 1; transform: translateY(0);}
}

/* ===== Titles & Text ===== */
h1 {
    text-align: center;
    color: #111827;
    margin-bottom: 1rem;
}
p {
    text-align: center;
    color: #6b7280;
    margin-bottom: 1.5rem;
}

/* ===== Input Fields ===== */
label {
    display: block;
    margin-top: 1rem;
    font-weight: 600;
    color: #374151;
}
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    margin-top: 0.3rem;
    font-size: 1rem;
    transition: 0.3s;
}
input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 5px rgba(37,99,235,0.3);
    outline: none;
}

/* ===== Buttons ===== */
button {
    width: 100%;
    margin-top: 1.2rem;
    background: #2563eb;
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
    background: #1e4fd3;
}

.register-btn {
    width: 100%;
    margin-top: 0.8rem;
    background: #10b981;
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
.register-btn:hover {
    background: #0f766e;
}

/* ===== Error Message ===== */
.msg.error {
    background: #fde8e8;
    color: #a10000;
    padding: 0.7rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    text-align: center;
    border: 1px solid #f5bebe;
}
</style>
</head>
<body>

<main class="login-container">
    <form action="authenticate.php" method="post">
        <h1>Welcome Back</h1>
        <p>Login to manage your event registrations</p>

        <?php if ($error): ?>
            <div class="msg error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" placeholder="Enter your email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Enter your password" required minlength="6">

        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <button type="submit">Login</button>
    </form>

    <a href="register.php" class="register-btn">Create New Account</a>
</main>

</body>
</html>
