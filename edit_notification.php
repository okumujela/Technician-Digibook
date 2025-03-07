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

// Fetch notification details
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM notifications WHERE id = '$id'");
$notification = $result->fetch_assoc();

// Handle form submission for editing notification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];

    // Prepare query to update notification
    $stmt = $conn->prepare("UPDATE notifications SET message = ? WHERE id = ?");
    $stmt->bind_param("si", $message, $id);

    if ($stmt->execute()) {
        $success = "Notification updated successfully!";
    } else {
        $error = "Error updating notification: " . $stmt->error;
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
    <title>Edit Notification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="manage_notifications.php" class="btn btn-secondary">Back to Notifications</a>
        <h2 class="text-center">Edit Notification</h2>
    </div>

    <!-- Display Errors or Success Messages -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Form to edit notification -->
    <form method="POST">
        <div class="mb-3">
            <label for="message" class="form-label">Notification Message:</label>
            <textarea class="form-control" id="message" name="message" required><?php echo htmlspecialchars($notification['message']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Update Notification</button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>