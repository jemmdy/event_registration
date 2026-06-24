<?php
require '../config.php';

// ✅ Restrict access to participants only
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'participant') {
    header('Location: ../login.php');
    exit;
}

$user_name = $_SESSION['user_name'] ?? 'Participant';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Participant Dashboard</title>
<style>
/* ===== General Page Style ===== */
body{
    font-family: Arial, sans-serif;
    background:#f5f7fa;
    margin:0;
    color:#333;
}
header{
    background:#10b981;
    color:white;
    padding:1rem;
    text-align:center;
}
.container{
    max-width:900px;
    margin:2rem auto;
    background:white;
    border-radius:10px;
    padding:2rem;
    box-shadow:0 5px 10px rgba(0,0,0,0.1);
}
h2{text-align:center;color:#10b981;}
p{font-size:1rem;line-height:1.6}

/* ===== Navigation Bar ===== */
.navbar{
    display:flex;
    justify-content:center;
    align-items:center;
    background:#0f766e;
    padding:0.7rem;
}
.navbar a{
    color:white;
    text-decoration:none;
    margin:0 1rem;
    padding:0.5rem 1rem;
    border-radius:6px;
    transition:background 0.3s;
}
.navbar a:hover{
    background:#0d9488;
}
.navbar a.active{
    background:#047857;
}

/* ===== Dashboard Cards ===== */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:1rem;
    margin-top:2rem;
}
.card{
    background:#f9fafb;
    border:1px solid #e5e7eb;
    border-radius:10px;
    padding:1.5rem;
    text-align:center;
    transition:transform 0.2s, box-shadow 0.2s;
}
.card:hover{
    transform:translateY(-3px);
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}
.card h3{color:#2563eb;margin-bottom:0.5rem;}
.card a{
    display:inline-block;
    background:#2563eb;
    color:white;
    padding:0.6rem 1rem;
    border-radius:6px;
    text-decoration:none;
    margin-top:0.8rem;
}
.card a:hover{background:#1d4ed8;}
footer{
    text-align:center;
    color:#555;
    padding:1rem;
    font-size:0.9rem;
    margin-top:3rem;
}
</style>
</head>
<body>

<!-- ===== Navigation Bar ===== -->
<div class="navbar">
    <a href="dashboard.php" class="active">🏠 Dashboard</a>
    <a href="events.php">📅 View Events</a>
    <a href="my_registrations.php">🧾 My Registrations</a>
    <a href="../logout.php">🚪 Logout</a>
</div>

<!-- ===== Header ===== -->
<header>
    <h1>Welcome, <?= htmlspecialchars($user_name) ?>!</h1>
    <p>Your personal event registration dashboard</p>
</header>

<!-- ===== Main Dashboard Section ===== -->
<main class="container">
    <h2>Quick Access</h2>
    <div class="cards">
        <div class="card">
            <h3>📅 View All Events</h3>
            <p>Explore available events and register easily.</p>
            <a href="events.php">Go to Events</a>
        </div>
        <div class="card">
            <h3>🧾 My Registered Events</h3>
            <p>View a list of all the events you’ve registered for.</p>
            <a href="my_registrations.php">View Registrations</a>
        </div>
        <div class="card">
            <h3>🚪 Logout</h3>
            <p>Log out of your account safely.</p>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
</main>

<footer>
    &copy; <?= date('Y') ?> Event Registration System — Participant Portal
</footer>
</body>
</html>
