<?php
session_start();
include 'config.php';

// Check if user is logged in and has super admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Fetch all notifications with machine details
$result = $conn->query("
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
");

// Handle form submission for updating notification status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Prepare query to update notification status
    $stmt = $conn->prepare("UPDATE notifications SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        $success = "Notification status updated successfully!";
    } else {
        $error = "Error updating notification status: " . $stmt->error;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <h2 class="text-center">Manage Notifications</h2>
    </div>

    <!-- Display Errors or Success Messages -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- List of existing notifications -->
    <h3 class="mt-5">Existing Notifications:</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Machine Name</th>
                <th>Serial Number</th>
                <th>Site</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['machine_name']); ?></td>
                <td><?php echo htmlspecialchars($row['serial_number']); ?></td>
                <td><?php echo htmlspecialchars($row['site']); ?></td>
                <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                <td>
                    <?php if ($row['status'] == 'pending'): ?>
                        <span class="badge bg-warning">Pending</span>
                    <?php elseif ($row['status'] == 'sent'): ?>
                        <span class="badge bg-success">Sent</span>
                    <?php endif; ?>
                    <form method="POST" style="display: inline-block;">
                        <input type="hidden" name="id" value="<?php echo $row['notification_id']; ?>">
                        <select name="status" onchange="this.form.submit()">
                            <option value="">Select Status</option>
                            <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="sent" <?php if ($row['status'] == 'sent') echo 'selected'; ?>>Sent</option>
                        </select>
                    </form>
                </td>
                <td>
                    <a href="edit_notification.php?id=<?php echo $row['notification_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_notification.php?id=<?php echo $row['notification_id']; ?>" onclick='return confirm("Are you sure you want to delete this notification?");' class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>