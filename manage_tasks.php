<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Fetch tasks with machine details and assigned user
$query = "
    SELECT 
        tasks.id AS task_id,
        tasks.machine_id,
        tasks.assigned_to,
        tasks.start_datetime,
        tasks.end_datetime,
        tasks.next_due_date,
        machines.name AS machine_name,
        machines.serial_number,
        machines.machine_type,
        machines.site,
        users.username AS assigned_user
    FROM 
        tasks
    INNER JOIN 
        machines ON tasks.machine_id = machines.id
    INNER JOIN 
        users ON tasks.assigned_to = users.id
";
$result = $conn->query($query);

// Fetch machines for dropdown
$machines = $conn->query("SELECT id, name FROM machines");

// Fetch users for dropdown
$users = $conn->query("SELECT id, username FROM users");

// Handle form submission for adding new task
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $machine_id = $_POST['machine_id'];
    $assigned_to = $_POST['assigned_to'];
    $start_datetime = $_POST['start_datetime'];
    $end_datetime = $_POST['end_datetime'];
    $next_due_date = $_POST['next_due_date'];

    // Prepare query to insert task
    $stmt = $conn->prepare("INSERT INTO tasks (machine_id, assigned_to, start_datetime, end_datetime, next_due_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $machine_id, $assigned_to, $start_datetime, $end_datetime, $next_due_date);

    if ($stmt->execute()) {
        // Create a notification for the task
        $task_id = $stmt->insert_id;
        $notification_stmt = $conn->prepare("INSERT INTO notifications (task_id, due_date, status) VALUES (?, ?, 'pending')");
        $notification_stmt->bind_param("is", $task_id, $next_due_date);
        
        if ($notification_stmt->execute()) {
            $success = "Task and notification created successfully!";
        } else {
            $error = "Error creating notification: " . $notification_stmt->error;
        }
    } else {
        $error = "Error creating task: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tasks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <h2 class="text-center">Manage Tasks</h2>
    </div>

    <!-- Display Errors or Success Messages -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Form to add new task -->
    <form method="POST">
        <div class="mb-3">
            <label for="machine_id" class="form-label">Machine:</label>
            <select class="form-control" id="machine_id" name="machine_id" required>
                <option value="">Select Machine</option>
                <?php while ($row = $machines->fetch_assoc()) { ?>
                <option value="<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="assigned_to" class="form-label">Assigned To:</label>
            <select class="form-control" id="assigned_to" name="assigned_to" required>
                <option value="">Select User</option>
                <?php while ($row = $users->fetch_assoc()) { ?>
                <option value="<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['username']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="start_datetime" class="form-label">Start Date & Time:</label>
            <input type="datetime-local" class="form-control" id="start_datetime" name="start_datetime" required>
        </div>
        <div class="mb-3">
            <label for="end_datetime" class="form-label">End Date & Time:</label>
            <input type="datetime-local" class="form-control" id="end_datetime" name="end_datetime" required>
        </div>
        <div class="mb-3">
            <label for="next_due_date" class="form-label">Next Due Date:</label>
            <input type="date" class="form-control" id="next_due_date" name="next_due_date" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Add Task</button>
    </form>

    <!-- List of existing tasks -->
    <h3 class="mt-5">Existing Tasks:</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Machine Name</th>
                <th>Serial Number</th>
                <th>Machine Type</th>
                <th>Site</th>
                <th>Assigned To</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Next Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['machine_name']); ?></td>
                <td><?php echo htmlspecialchars($row['serial_number']); ?></td>
                <td><?php echo htmlspecialchars($row['machine_type']); ?></td>
                <td><?php echo htmlspecialchars($row['site']); ?></td>

                <!-- Assigned User -->
                <?php 
                    // Display assigned user details dynamically
                    echo "<td>" . htmlspecialchars($row['assigned_user']) . "</td>";
                ?>

                <!-- Task details -->
                <td><?php echo htmlspecialchars($row['start_datetime']); ?></td>
                <td><?php echo htmlspecialchars($row['end_datetime']); ?></td>
                <td><?php echo htmlspecialchars($row['next_due_date']); ?></td>

                <!-- Actions -->
                <td>
                    <a href="edit_task.php?id=<?php echo $row['task_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_task.php?id=<?php echo $row['task_id']; ?>" onclick='return confirm("Are you sure you want to delete this task?");' class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>

            <?php } ?>
        </tbody>
    </table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Close connection at the end
$conn->close();
?>

</body>
</html>
