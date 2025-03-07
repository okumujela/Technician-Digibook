<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch notification details
$id = $_GET['id'];

// Prepare query to delete notification
$stmt = $conn->prepare("DELETE FROM notifications WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<div class='alert alert-success'>Notification deleted successfully!</div>";
} else {
    echo "<div class='alert alert-danger'>Error deleting notification: " . $stmt->error . "</div>";
}

// Close connection
$conn->close();

// Redirect back to notifications page
header('Location: manage_notifications.php');
exit;
?>