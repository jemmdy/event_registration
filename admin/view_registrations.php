<?php
require '../config.php';
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$sql = "
SELECT e.title, e.event_date, u.full_name, u.email, r.registered_at
FROM event_registrations r
JOIN users u ON r.user_id = u.id
JOIN events e ON r.event_id = e.id
ORDER BY e.event_date DESC, r.registered_at DESC
";
$data = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Event Registrations</title>
<style>
body{font-family:Arial;background:#f5f7fa;margin:0}
.navbar{display:flex;justify-content:center;align-items:center;background:#1e40af;padding:0.7rem}
.navbar a{color:white;text-decoration:none;margin:0 1rem;padding:0.5rem 1rem;border-radius:6px}
.navbar a:hover{background:#1d4ed8}
.navbar a.active{background:#1e3a8a}
header{background:#2563eb;color:white;text-align:center;padding:1rem}
main{padding:2rem;max-width:900px;margin:auto}
table{width:100%;border-collapse:collapse;background:white;border-radius:8px;overflow:hidden}
th,td{padding:0.8rem;border-bottom:1px solid #ddd;text-align:left}
th{background:#2563eb;color:white}
tr:hover{background:#f9fafb}
</style>
</head>
<body>

<div class="navbar">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="manage_events.php">📅 Manage Events</a>
    <a href="view_registrations.php" class="active">🧾 View Registrations</a>
    <a href="manage_participants.php">👥 Participants</a>
    <a href="../logout.php">🚪 Logout</a>
</div>

<header><h2>Event Registrations</h2></header>
<main>
<?php if (empty($data)): ?>
<p>No registrations yet.</p>
<?php else: ?>
<table>
<thead><tr><th>Event</th><th>Date</th><th>Participant</th><th>Email</th><th>Registered At</th></tr></thead>
<tbody>
<?php foreach ($data as $r): ?>
<tr>
<td><?= htmlspecialchars($r['title']) ?></td>
<td><?= htmlspecialchars($r['event_date']) ?></td>
<td><?= htmlspecialchars($r['full_name']) ?></td>
<td><?= htmlspecialchars($r['email']) ?></td>
<td><?= htmlspecialchars($r['registered_at']) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php endif; ?>
</main>
</body>
</html>
