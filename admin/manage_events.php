<?php 
require '../config.php';

// ✅ Restrict to admin only
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

/* -------------------------------------------
   1️⃣ AUTO-CLOSE PAST EVENTS
-------------------------------------------- */
$pdo->query("UPDATE events SET status='closed' WHERE event_date < CURDATE()");

/* -------------------------------------------
   2️⃣ ADD NEW EVENT
-------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date = trim($_POST['event_date']);
    $capacity = (int)($_POST['capacity'] ?? 0);
    $location = trim($_POST['location']);

    if ($title && $date && $location) {
        $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, capacity, location, status)
                               VALUES (?, ?, ?, ?, ?, 'open')");
        $stmt->execute([$title, $description, $date, $capacity, $location]);
        $_SESSION['msg'] = "✅ Event added successfully!";
    } else {
        $_SESSION['msg'] = "⚠️ Please fill all required fields.";
    }
    header('Location: manage_events.php');
    exit;
}

/* -------------------------------------------
   3️⃣ DELETE EVENT
-------------------------------------------- */
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM events WHERE id = ?")->execute([(int)$_GET['delete']]);
    $_SESSION['msg'] = "🗑️ Event deleted successfully!";
    header('Location: manage_events.php');
    exit;
}

/* -------------------------------------------
   4️⃣ EDIT EVENT
-------------------------------------------- */
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_event = $pdo->query("SELECT * FROM events WHERE id=$id")->fetch();
}

/* -------------------------------------------
   5️⃣ UPDATE EVENT
-------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_event'])) {
    $id = (int)$_POST['event_id'];
    $stmt = $pdo->prepare("UPDATE events 
                           SET title=?, description=?, event_date=?, capacity=?, location=? 
                           WHERE id=?");
    $stmt->execute([
        $_POST['title'], 
        $_POST['description'], 
        $_POST['event_date'], 
        (int)$_POST['capacity'], 
        $_POST['location'], 
        $id
    ]);
    $_SESSION['msg'] = "✅ Event updated successfully!";
    header('Location: manage_events.php');
    exit;
}

/* -------------------------------------------
   6️⃣ FETCH EVENTS
-------------------------------------------- */
$events = $pdo->query("SELECT * FROM events ORDER BY event_date DESC")->fetchAll();
$msg = $_SESSION['msg'] ?? null;
unset($_SESSION['msg']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Events</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f7fa;
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
form {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}
label { font-weight: bold; display: block; margin-top: 1rem; }
input, textarea {
    width: 100%;
    padding: 0.6rem;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-top: 0.3rem;
}
button {
    background: #10b981;
    color: white;
    border: none;
    padding: 0.7rem 1.2rem;
    border-radius: 6px;
    margin-top: 1rem;
    cursor: pointer;
}
button:hover { background: #0a996f; }
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
}
th, td {
    padding: 0.8rem;
    border-bottom: 1px solid #ddd;
    text-align: left;
}
th {
    background: #2563eb;
    color: white;
}
tr:hover { background: #f9fafb; }
a.btn-del, a.btn-edit {
    padding: 0.3rem 0.6rem;
    border-radius: 4px;
    text-decoration: none;
    color: white;
}
a.btn-del { background: #ef4444; }
a.btn-edit { background: #2563eb; }
.msg {
    background: #e0f2fe;
    padding: 0.7rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    text-align: center;
}
.status-open { color: green; font-weight: bold; }
.status-closed { color: red; font-weight: bold; }
</style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="manage_events.php" class="active">📅 Manage Events</a>
    <a href="view_registrations.php">🧾 View Registrations</a>
    <a href="manage_participants.php">👥 Participants</a>
    <a href="../logout.php">🚪 Logout</a>
</div>

<header><h2>Manage Events</h2></header>
<main>
<?php if ($msg): ?><div class="msg"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<form method="post">
    <h3><?= isset($edit_event) ? "Edit Event" : "Add New Event" ?></h3>
    
    <label>Title</label>
    <input type="text" name="title" value="<?= $edit_event['title'] ?? '' ?>" required>

    <label>Description</label>
    <textarea name="description"><?= $edit_event['description'] ?? '' ?></textarea>

    <label>Date</label>
    <input type="date" name="event_date" value="<?= $edit_event['event_date'] ?? '' ?>" required>

    <label>Location</label>
    <input type="text" name="location" value="<?= $edit_event['location'] ?? '' ?>" required>

    <label>Capacity</label>
    <input type="number" name="capacity" min="0" value="<?= $edit_event['capacity'] ?? '' ?>">

    <?php if (isset($edit_event)): ?>
        <input type="hidden" name="event_id" value="<?= $edit_event['id'] ?>">
        <button type="submit" name="update_event">Update Event</button>
        <a href="manage_events.php" style="color:#ef4444;margin-left:10px;">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add_event">Add Event</button>
    <?php endif; ?>
</form>

<h3>Existing Events</h3>
<?php if (empty($events)): ?>
    <p>No events created yet.</p>
<?php else: ?>
<table>
<thead>
<tr>
  <th>Title</th>
  <th>Date</th>
  <th>Location</th>
  <th>Capacity</th>
  <th>Status</th>
  <th>Description</th>
  <th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach($events as $e): ?>
<tr>
  <td><?= htmlspecialchars($e['title']) ?></td>
  <td><?= htmlspecialchars($e['event_date']) ?></td>
  <td><?= htmlspecialchars($e['location']) ?></td>
  <td><?= $e['capacity'] ?: 'Unlimited' ?></td>
  <td>
      <?= $e['status'] === 'closed' 
          ? '<span class="status-closed">Closed</span>' 
          : '<span class="status-open">Open</span>' ?>
  </td>
  <td><?= htmlspecialchars($e['description']) ?></td>
  <td>
      <a href="?edit=<?= $e['id'] ?>" class="btn-edit">Edit</a>
      <a href="?delete=<?= $e['id'] ?>" class="btn-del" onclick="return confirm('Delete this event?');">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>
</main>
</body>
</html>
