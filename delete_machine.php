<?php
// Include database connection file
include 'config.php';

// Start session
session_start();

// Check if user is logged in and has super admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header('Location: login.html');
    exit;
}

// Fetch machine details
$id = $_GET['id'];

// Prepare query to delete machine
$stmt = $conn->prepare("DELETE FROM machines WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Machine deleted successfully!";
    header('Location: manage_machines.php');
    exit;
} else {
    echo "Error: " . $stmt->error;
}

// Close connection
$conn->close();
?>