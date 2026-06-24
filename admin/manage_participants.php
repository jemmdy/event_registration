<?php 
require '../config.php';

// ✅ Only allow logged-in admin users
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// ✅ Handle deletion request
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'participant'");
        $stmt->execute([$id]);
        $_SESSION['msg'] = "🗑️ Participant deleted successfully.";
    } catch (Exception $e) {
        $_SESSION['msg'] = "❌ Error deleting participant.";
    }
    header('Location: manage_participants.php');
    exit;
}

// ✅ Handle activation/deactivation
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    try {
        $pdo->query("UPDATE users SET is_active = 1 - is_active WHERE id = $id AND role='participant'");
        $_SESSION['msg'] = "✅ Participant status updated successfully.";
    } catch (Exception $e) {
        $_SESSION['msg'] = "❌ Could not update status.";
    }
    header('Location: manage_participants.php');
    exit;
}

// ✅ Fetch all participants
$stmt = $pdo->query("SELECT id, full_name, email, is_active, created_at FROM users WHERE role = 'participant' ORDER BY created_at DESC");
$participants = $stmt->fetchAll();

$msg = $_SESSION['msg'] ?? null;
unset($_SESSION['msg']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Participants - Admin Panel</title>
    <style>
        /* ===== General Page Style ===== */
        body { font-family: Arial, sans-serif; background: #f5f7fa; margin: 0; color: #333; }

        /* ===== Navigation Bar ===== */
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
            transition: background 0.3s;
        }
        .navbar a:hover { background: #1d4ed8; }
        .navbar a.active { background: #1e3a8a; }

        /* ===== Header & Layout ===== */
        header {
            background: #2563eb;
            color: white;
            text-align: center;
            padding: 1rem;
        }
        main {
            padding: 2rem;
            max-width: 950px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }

        /* ===== Table ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.8rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #2563eb;
            color: white;
        }
        tr:hover { background: #f9fafb; }

        /* ===== Buttons ===== */
        a.btn {
            padding: 0.4rem 0.7rem;
            border-radius: 5px;
            color: #fff;
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.2s;
        }
        a.btn.toggle { background: #2563eb; }
        a.btn.toggle:hover { background: #1d4ed8; }
        a.btn.delete { background: #dc2626; }
        a.btn.delete:hover { background: #b91c1c; }

        /* ===== Message Box ===== */
        .msg {
            background: #e0f2fe;
            color: #0369a1;
            padding: 0.8rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            text-align: center;
        }

        /* ===== Footer ===== */
        footer {
            text-align: center;
            color: #555;
            padding: 1rem;
            font-size: 0.9rem;
            margin-top: 3rem;
        }
    </style>
</head>
<body>

<!-- ===== Navigation Bar ===== -->
<div class="navbar">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="manage_events.php">📅 Manage Events</a>
    <a href="view_registrations.php">🧾 View Registrations</a>
    <a href="manage_participants.php" class="active">👥 Participants</a>
    <a href="../logout.php">🚪 Logout</a>
</div>

<!-- ===== Header ===== -->
<header>
    <h2>Manage Participants</h2>
</header>

<!-- ===== Main Content ===== -->
<main>
    <?php if ($msg): ?>
        <div class="msg"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (empty($participants)): ?>
        <p>No participants have registered yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Date Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participants as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['id']) ?></td>
                        <td><?= htmlspecialchars($p['full_name']) ?></td>
                        <td><?= htmlspecialchars($p['email']) ?></td>
                        <td><?= $p['is_active'] ? '🟢 Active' : '🔴 Inactive' ?></td>
                        <td><?= htmlspecialchars($p['created_at']) ?></td>
                        <td>
                            <a href="?toggle=<?= $p['id'] ?>" class="btn toggle">
                                <?= $p['is_active'] ? 'Deactivate' : 'Activate' ?>
                            </a>
                            <a href="?delete=<?= $p['id'] ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this participant?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<footer>
    &copy; <?= date('Y') ?> Event Registration System — Admin Panel
</footer>

</body>
</html>
