<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = '$user_id'");
$user = $result->fetch_assoc();

// Fetch machine count
$machineCount = $conn->query("SELECT COUNT(*) as count FROM machines")->fetch_assoc()['count'];

// Fetch task count
$taskCount = $conn->query("SELECT COUNT(*) as count FROM tasks")->fetch_assoc()['count'];

// Fetch breakdown count from breakdowns table
$breakdownCount = $conn->query("SELECT COUNT(*) as count FROM breakdowns")->fetch_assoc()['count'];

// Fetch all notifications with machine details
$notificationsQuery = "
    SELECT 
        notifications.id AS notification_id,
        notifications.due_date,
        notifications.status,
        tasks.id AS task_id,
        machines.name AS machine_name,
        machines.serial_number,
        machines.site
    FROM 
        notifications
    INNER JOIN 
        tasks ON notifications.task_id = tasks.id
    INNER JOIN 
        machines ON tasks.machine_id = machines.id
";
$notifications = $conn->query($notificationsQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technician Digibook Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .card {
            margin-bottom: 20px;
        }
        .logout-btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <a href="logout.php" class="btn btn-danger logout-btn">Logout</a>
    </div>

    <!-- Manage Notifications Link -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manage Notifications</h5>
                    <a href="manage_notifications.php" class="btn btn-primary w-100">Manage Notifications</a>
                </div>
            </div>
        </div>
    </div>

    <!-- All Notifications Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">All Notifications</h5>
                    <?php if ($notifications->num_rows > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Machine Name</th>
                                    <th>Serial Number</th>
                                    <th>Site</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($notification = $notifications->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($notification['machine_name']); ?></td>
                                    <td><?php echo htmlspecialchars($notification['serial_number']); ?></td>
                                    <td><?php echo htmlspecialchars($notification['site']); ?></td>
                                    <td><?php echo htmlspecialchars($notification['due_date']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($notification['status'])); ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No notifications available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row">
        <!-- Total Machines -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Machines</h5>
                    <h2><?php echo $machineCount; ?></h2>
                    <a href="manage_machines.php" class="btn btn-primary">Manage Machines</a>
                </div>
            </div>
        </div>

        <!-- Scheduled Tasks -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Scheduled Tasks</h5>
                    <h2><?php echo $taskCount; ?></h2>
                    <a href="manage_tasks.php" class="btn btn-primary">Manage Tasks</a>
                </div>
            </div>
        </div>

        <!-- Breakdown Reports -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Breakdown Reports</h5>
                    <h2><?php echo $breakdownCount; ?></h2>
                    <a href="manage_breakdowns.php" class="btn btn-primary">Manage Breakdowns</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Role-Specific Features -->
    <?php if ($user['role'] == 'super_admin'): ?>
        <!-- Super Admin Section -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Admin Management</h5>
                        <a href="manage_users.php" class="btn btn-primary">Manage Users</a>
                        <a href="manage_roles.php" class="btn btn-primary">Manage Roles</a>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($user['role'] == 'manager'): ?>
        <!-- Manager Section -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Manager Tools</h5>
                        <a href="assign_tasks.php" class="btn btn-primary">Assign Tasks</a>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<!-- Footer -->
<footer style='margin-top:30px;'class='text-center'>
    &copy;2023 Technician Digibook. All Rights Reserved.
</footer>

<?php
// Close connection at the end
$conn->close();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>