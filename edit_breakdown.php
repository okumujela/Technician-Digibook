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

// Fetch breakdown details
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM breakdowns WHERE id = '$id'");
$breakdown = $result->fetch_assoc();

// Fetch machines for dropdown
$machines = $conn->query("SELECT id, name FROM machines");

// Handle form submission for editing breakdown
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date_reported = $_POST['date_reported'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Prepare query to update breakdown
    $stmt = $conn->prepare("UPDATE breakdowns SET date_reported = ?, description = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssi", $date_reported, $description, $status, $id);

    if ($stmt->execute()) {
        $success = "Breakdown updated successfully!";
    } else {
        $error = "Error updating breakdown: " . $stmt->error;
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
    <title>Edit Breakdown</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="manage_breakdowns.php" class="btn btn-secondary">Back to Breakdowns</a>
        <h2 class="text-center">Edit Breakdown</h2>
    </div>

    <!-- Display Errors or Success Messages -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Form to edit breakdown -->
    <form method="POST">
        <div class="mb-3">
            <label for="date_reported" class="form-label">Date Reported:</label>
            <input type="date" class="form-control" id="date_reported" name="date_reported" value="<?php echo htmlspecialchars($breakdown['date_reported']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($breakdown['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status:</label>
            <select class="form-control" id="status" name="status" required>
                <option value="">Select Status</option>
                <option value="pending" <?php if ($breakdown['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                <option value="resolved" <?php if ($breakdown['status'] == 'resolved') echo 'selected'; ?>>Resolved</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Update Breakdown</button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>