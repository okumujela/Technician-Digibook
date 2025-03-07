<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch breakdown details
$id = $_GET['id'];

// Prepare query to delete breakdown
$stmt = $conn->prepare("DELETE FROM breakdowns WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<div class='alert alert-success'>Breakdown deleted successfully!</div>";
} else {
    echo "<div class='alert alert-danger'>Error deleting breakdown: " . $stmt->error . "</div>";
}

// Close connection
$conn->close();

// Redirect back to manage breakdowns page
header('Location: manage_breakdowns.php');
exit;
?>