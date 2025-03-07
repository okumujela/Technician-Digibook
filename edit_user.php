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

// Fetch user details
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE id = '$id'");
$user = $result->fetch_assoc();

// Handle form submission for editing user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Prepare query to update user
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $email, $role, $id);

    if ($stmt->execute()) {
        $success = "User updated successfully!";
    } else {
        $error = "Error: " . $stmt->error;
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
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="manage_users.php" class="btn btn-secondary">Back to Users</a>
        <h2 class="text-center">Edit User</h2>
    </div>

    <!-- Display Errors or Success Messages -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Form to edit user -->
    <div class="manage-users-form">
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role:</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="super_admin" <?php if ($user['role'] == 'super_admin') echo 'selected'; ?>>Super Admin</option>
                    <option value="manager" <?php if ($user['role'] == 'manager') echo 'selected'; ?>>Manager</option>
                    <option value="technician" <?php if ($user['role'] == 'technician') echo 'selected'; ?>>Technician</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update User</button>
        </form>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>