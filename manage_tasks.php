<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... [existing form handling code] ...
}

$tasks = $conn->query("
    SELECT tasks.*, machines.name AS machine_name, users.username, 
           machines.serial_number, machines.site
    FROM tasks
    JOIN machines ON tasks.machine_id = machines.id
    JOIN users ON tasks.assigned_to = users.id
");

$machines = $conn->query("SELECT id, name FROM machines");
$users = $conn->query("SELECT id, username FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Tasks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Header Section -->
    <div class="d-flex justify-content-between mb-4">
        <h2>Manage Tasks</h2>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <!-- Form and Error Handling -->
    <!-- ... [existing form code] ... -->

    <!-- Tasks List -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Machine</th>
                <th>Serial</th>
                <th>Site</th>
                <th>Assigned To</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($task = $tasks->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($task['machine_name']) ?></td>
                <td><?= htmlspecialchars($task['serial_number'] ?? '') ?></td>
                <td><?= htmlspecialchars($task['site'] ?? '') ?></td>
                <td><?= htmlspecialchars($task['username']) ?></td>
                <td><?= htmlspecialchars($task['start_date'] ?? '') ?></td>
                <td><?= htmlspecialchars($task['end_date'] ?? '') ?></td>
                <td><?= htmlspecialchars($task['due_date'] ?? '') ?></td>
                <td>
                    <a href="edit_task.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>