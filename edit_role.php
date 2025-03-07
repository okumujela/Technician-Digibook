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

// Fetch role details
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM roles WHERE id = '$id'");
$role = $result->fetch_assoc();

// Fetch all users
$users = $conn->query("SELECT id, username FROM users");

// Handle form submission for editing role
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role_name = $_POST['role_name'];
    $description = $_POST['description'];
    $user_id = $_POST['user_id'];

    // Prepare query to update role
    $stmt = $conn->prepare("UPDATE roles SET role_name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $role_name, $description, $id);

    if ($stmt->execute()) {
        // Update user role assignment
        $assign_stmt = $conn->prepare("UPDATE users SET role_id = ? WHERE role_id = ?");
        $assign_stmt->bind_param("ii", $user_id, $id);

        if ($assign_stmt->execute()) {
            $success = "Role updated and assigned to user successfully!";
        } else {
            $error = "Error assigning role to user: " . $assign_stmt->error;
        }
    } else {
        $error = "Error updating role: " . $stmt->error;
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
    <title>Edit Role</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="manage_roles.php" class="btn btn-secondary">Back to Roles</a>
        <h2 class="text-center">Edit Role</h2>
    </div>

    <!-- Display Errors or Success Messages -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Form to edit role -->
    <div class="manage-roles-form">
        <form method="POST">
            <div class="mb-3">
                <label for="role_name" class="form-label">Role Name:</label>
                <input type="text" class="form-control" id="role_name" name="role_name" value="<?php echo htmlspecialchars($role['role_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($role['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="user_id" class="form-label">Assign to User:</label>
                <select class="form-control" id="user_id" name="user_id" required>
                    <option value="">Select User</option>
                    <?php while ($user = $users->fetch_assoc()) { ?>
                    <option value="<?php echo htmlspecialchars($user['id']); ?>" <?php if ($user['id'] == $role['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($user['username']); ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Role</button>
        </form>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>