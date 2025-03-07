<?php
session_start();
include 'config.php';

// Check if user is logged in and has super admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header('Location: login.php');
    exit;
}

// Fetch role details
$id = $_GET['id'];

// Prepare query to delete role
$stmt = $conn->prepare("DELETE FROM roles WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<div class='alert alert-success'>Role deleted successfully!</div>";
} else {
    echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
}

// Close connection
$conn->close();

// Redirect back to roles page
header('Location: manage_roles.php');
exit;
?>