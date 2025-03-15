<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch counts
$machineCount = $conn->query("SELECT COUNT(*) FROM machines")->fetch_row()[0];
$taskCount = $conn->query("SELECT COUNT(*) FROM tasks")->fetch_row()[0];
$breakdownCount = $conn->query("SELECT COUNT(*) FROM breakdowns")->fetch_row()[0];
$userCount = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$roleCount = $conn->query("SELECT COUNT(*) FROM roles")->fetch_row()[0];

// Fetch notifications
$notifications = $conn->query("
    SELECT notifications.*, machines.name AS machine_name 
    FROM notifications
    JOIN tasks ON notifications.task_id = tasks.id
    JOIN machines ON tasks.machine_id = machines.id
    ORDER BY notifications.due_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-machine { background: #4e73df; }
        .card-task { background: #1cc88a; }
        .card-breakdown { background: #e74a3b; }
        .card-user { background: #f6c23e; }
        .card-role { background: #36b9cc; }
        .btn-manage { background: white; color: black !important; border: 1px solid #ddd; }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-4">
        <h2>Dashboard</h2>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- Cards Section -->
    <div class="row g-4 mb-4">
        <div class="col-md-2">
            <div class="card card-machine text-white">
                <div class="card-body">
                    <h5>Machines</h5>
                    <h2><?= $machineCount ?></h2>
                    <a href="manage_machines.php" class="btn btn-manage btn-sm">Manage</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="card card-task text-white">
                <div class="card-body">
                    <h5>Tasks</h5>
                    <h2><?= $taskCount ?></h2>
                    <a href="manage_tasks.php" class="btn btn-manage btn-sm">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card card-breakdown text-white">
                <div class="card-body">
                    <h5>Breakdowns</h5>
                    <h2><?= $breakdownCount ?></h2>
                    <a href="manage_breakdowns.php" class="btn btn-manage btn-sm">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card card-user text-white">
                <div class="card-body">
                    <h5>Users</h5>
                    <h2><?= $userCount ?></h2>
                    <a href="manage_users.php" class="btn btn-manage btn-sm">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card card-role text-white">
                <div class="card-body">
                    <h5>Roles</h5>
                    <h2><?= $roleCount ?></h2>
                    <a href="manage_roles.php" class="btn btn-manage btn-sm">Manage</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Section -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Notifications</h5>
            <a href="manage_notifications.php" class="btn btn-manage">Manage Notifications</a>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Machine</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($note = $notifications->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($note['machine_name']) ?></td>
                        <td><?= htmlspecialchars($note['due_date']) ?></td>
                        <td><span class="badge bg-<?= $note['status'] === 'pending' ? 'warning' : 'success' ?>">
                            <?= ucfirst($note['status']) ?>
                        </span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Breakdown Report -->
    <div class="card">
        <div class="card-header">
            <h5>Breakdown Report</h5>
        </div>
        <div class="card-body">
            <?php include 'breakdowns_report.php'; ?>
        </div>
    </div>
</div>
</body>
</html>