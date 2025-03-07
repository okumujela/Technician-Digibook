<?php
session_start();
include 'config.php';

// Fetch task details
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM tasks WHERE id = '$id'");
$row = $result->fetch_assoc();

// Handle form submission for editing task
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $machine_id = $_POST['machine_id'];
    $assigned_to = $_POST['assigned_to'];
    $start_datetime = $_POST['start_datetime'];
    $end_datetime = $_POST['end_datetime'];
    $next_due_date = $_POST['next_due_date'];

    // Prepare query to update task
    $stmt = $conn->prepare("UPDATE tasks SET machine_id = ?, assigned_to = ?, start_datetime = ?, end_datetime = ?, next_due_date = ? WHERE id = ?");
    $stmt->bind_param("iisssi", $machine_id, $assigned_to, $start_datetime, $end_datetime, $next_due_date, $id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Task updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
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
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Edit Task</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="machine_id" class="form-label">Machine:</label>
            <input type="text" class="form-control" id="machine_id" name="machine_id" value="<?php echo $row['machine_id']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="assigned_to" class="form-label">Assigned To:</label>
            <input type="text" class="form-control" id="assigned_to" name="assigned_to" value="<?php echo $row['assigned_to']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="start_datetime" class="form-label">Start Date & Time:</label>
            <input type="datetime-local" class="form-control" id="start_datetime" name="start_datetime" value="<?php echo $row['start_datetime']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="end_datetime" class="form-label">End Date & Time:</label>
            <input type="datetime-local" class="form-control" id="end_datetime" name="end_datetime" value="<?php echo $row['end_datetime']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="next_due_date" class="form-label">Next Due Date:</label>
            <input type="date" class="form-control" id="next_due_date" name="next_due_date" value="<?php echo $row['next_due_date']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Update Task</button>
    </form>
    <p class="text-center mt-3"><a href="manage_tasks.php">Back to Tasks</a></p>
</div>
</body>
</html>