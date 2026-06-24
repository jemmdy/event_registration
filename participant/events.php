<?php
require '../config.php';

// ✅ Only allow participants
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'participant') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = $_SESSION['msg'] ?? null;
unset($_SESSION['msg']);

// Fetch all available events
$events = $pdo->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();

// Handle registration
if (isset($_GET['register'])) {
    $event_id = (int) $_GET['register'];

    // Check if already registered
    $check = $pdo->prepare("SELECT id FROM event_registrations WHERE event_id = ? AND user_id = ?");
    $check->execute([$event_id, $user_id]);
    if ($check->fetch()) {
        $_SESSION['msg'] = "⚠️ You’re already registered for this event.";
    } else {
        // Register the participant
        $insert = $pdo->prepare("INSERT INTO event_registrations (event_id, user_id) VALUES (?, ?)");
        $insert->execute([$event_id, $user_id]);
        $_SESSION['msg'] = "✅ Successfully registered for the event!";
    }

    header("Location: events.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Available Events</title>
<style>
body{font-family:Arial;background:#f5f7fa;margin:0;color:#333}
.navbar{display:flex;justify-content:center;align-items:center;background:#0f766e;padding:0.7rem}
.navbar a{color:white;text-decoration:none;margin:0 1rem;padding:0.5rem 1rem;border-radius:6px;transition:background 0.3s}
.navbar a:hover{background:#0d9488}
.navbar a.active{background:#047857}
header{background:#10b981;color:white;text-align:center;padding:1rem}
main{padding:2rem;max-width:950px;margin:0 auto}
.msg{background:#e0f2fe;color:#0369a1;padding:0.8rem;border-radius:6px;margin-bottom:1rem;text-align:center}
.event-card{background:white;border:1px solid #e5e7eb;border-radius:10px;padding:1.5rem;margin-bottom:1rem;box-shadow:0 4px 10px rgba(0,0,0,0.05)}
.event-card h3{color:#2563eb;margin-bottom:0.5rem}
.event-card p{margin:0.3rem 0}
.event-card a{display:inline-block;margin-top:0.5rem;padding:0.5rem 1rem;background:#2563eb;color:white;border-radius:6px;text-decoration:none}
.event-card a:hover{background:#1d4ed8}
footer{text-align:center;color:#555;padding:1rem;margin-top:2rem;font-size:0.9rem}
</style>
</head>
<body>

<!-- ===== Navigation Bar ===== -->
<div class="navbar">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="events.php" class="active">📅 Events</a>
    <a href="my_registrations.php">🧾 My Registrations</a>
    <a href="../logout.php">🚪 Logout</a>
</div>

<header>
    <h2>Available Events</h2>
    <p>Browse and register for upcoming events</p>
</header>

<main>
    <?php if ($msg): ?><div class="msg"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

    <?php if (empty($events)): ?>
        <p>No events are currently available.</p>
    <?php else: ?>
        <?php foreach ($events as $e): ?>
            <div class="event-card">
                <h3><?= htmlspecialchars($e['title']) ?></h3>
                <p><strong>Date:</strong> <?= htmlspecialchars($e['event_date']) ?></p>
                <p><strong>Description:</strong> <?= htmlspecialchars($e['description']) ?></p>
                <p><strong>Capacity:</strong> <?= $e['capacity'] ?: 'Unlimited' ?></p>
                <a href="?register=<?= $e['id'] ?>">Register</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<footer>
    &copy; <?= date('Y') ?> Event Registration System — Participant Portal
</footer>
</body>
</html>
