<?php 
require '../config.php';

// ✅ Only allow logged-in participants
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'participant') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

/* -------------------------------------------
   1️⃣ Handle registration cancellation
-------------------------------------------- */
if (isset($_POST['cancel_registration'])) {
    $event_id = (int) $_POST['event_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM event_registrations WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$user_id, $event_id]);
        $_SESSION['msg'] = "❌ You have successfully cancelled your registration.";
    } catch (Exception $e) {
        $_SESSION['msg'] = "⚠️ Error cancelling registration. Try again.";
    }
    header('Location: my_registrations.php');
    exit;
}

/* -------------------------------------------
   2️⃣ Fetch user's registered events
-------------------------------------------- */
$sql = "SELECT e.id, e.title, e.description, e.event_date, e.location, e.status, r.registered_at
        FROM event_registrations r
        JOIN events e ON r.event_id = e.id
        WHERE r.user_id = ?
        ORDER BY e.event_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$registrations = $stmt->fetchAll();

$msg = $_SESSION['msg'] ?? null;
unset($_SESSION['msg']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Registrations</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f3f4f6;
    margin: 0;
}
.navbar {
    display: flex;
    justify-content: center;
    align-items: center;
    background: #1e40af;
    padding: 0.7rem;
}
.navbar a {
    color: white;
    text-decoration: none;
    margin: 0 1rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
}
.navbar a:hover { background: #1d4ed8; }
.navbar a.active { background: #1e3a8a; }
header {
    background: #2563eb;
    color: white;
    text-align: center;
    padding: 1rem;
}
main {
    padding: 2rem;
    max-width: 900px;
    margin: auto;
}
.msg {
    background: #e0f2fe;
    color: #0369a1;
    padding: 0.7rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    text-align: center;
}
.card {
    background: white;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.card-left {
    flex: 1;
}
.card h3 {
    margin: 0;
    color: #111827;
}
.card p {
    margin: 0.3rem 0;
    color: #555;
}
.status-open { color: green; font-weight: bold; }
.status-closed { color: red; font-weight: bold; }
.cancel-btn {
    background: #ef4444;
    color: white;
    border: none;
    padding: 0.6rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
}
.cancel-btn:hover {
    background: #b91c1c;
}
</style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="events.php">📅 Events</a>
    <a href="my_registrations.php" class="active">🧾 My Registrations</a>
    <a href="../logout.php">🚪 Logout</a>
</div>

<header><h2>My Event Registrations</h2></header>

<main>
<?php if ($msg): ?>
    <div class="msg"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if (empty($registrations)): ?>
    <p>You haven't registered for any events yet.</p>
<?php else: ?>
    <?php foreach ($registrations as $r): ?>
        <div class="card">
            <div class="card-left">
                <h3><?= htmlspecialchars($r['title']) ?></h3>
                <p><strong>Date:</strong> <?= htmlspecialchars($r['event_date']) ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($r['location']) ?></p>
                <p><strong>Status:</strong> 
                    <?= $r['status'] === 'closed' 
                        ? '<span class="status-closed">Closed</span>' 
                        : '<span class="status-open">Open</span>' ?>
                </p>
                <p><small>Registered on <?= htmlspecialchars($r['registered_at']) ?></small></p>
            </div>

            <?php if ($r['status'] === 'open'): ?>
                <form method="post" onsubmit="return confirm('Cancel this registration?');">
                    <input type="hidden" name="event_id" value="<?= $r['id'] ?>">
                    <button type="submit" name="cancel_registration" class="cancel-btn">
                        ❌ Cancel
                    </button>
                </form>
            <?php else: ?>
                <button class="cancel-btn" disabled>Closed</button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</main>
</body>
</html>
