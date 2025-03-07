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

// Fetch all roles
$result = $conn->query("SELECT * FROM roles");

// Fetch all users
$users = $conn->query("SELECT id, username FROM users");

// Handle form submission for adding new role
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role_name = $_POST['role_name'];
    $description = $_POST['description'];
    $user_id = $_POST['user_id'];

    // Prepare query to insert role
    $stmt = $conn->prepare("INSERT INTO roles (role_name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $role_name, $description);

    if ($stmt->execute()) {
        $role_id = $stmt->insert_id;

        // Assign role to user
        $assign_stmt = $conn->prepare("UPDATE users SET role_id = ? WHERE id = ?");
        $assign_stmt->bind_param("ii", $role_id, $user_id);

        if ($assign_stmt->execute()) {
            $success = "Role added and assigned to user successfully!";
        } else {
            $error = "Error assigning role to user: " . $assign_stmt->error;
        }
    } else {
        $error = "Error adding role: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Roles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <h2 class="text-center">Manage Roles</h2>
    </div>

    <!-- Display Errors or Success Messages -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Form to add new role -->
    <form method="POST" class="mb-5">
        <div class="mb-3">
            <label for="role_name" class="form-label">Role Name:</label>
            <input type="text" class="form-control" id="role_name" name="role_name" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="mb-3">
            <label for="user_id" class="form-label">Assign to User:</label>
            <select class="form-control" id="user_id" name="user_id" required>
                <option value="">Select User</option>
                <?php while ($user = $users->fetch_assoc()) { ?>
                <option value="<?php echo htmlspecialchars($user['id']); ?>">
                    <?php echo htmlspecialchars($user['username']); ?>
                </option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Add Role</button>
    </form>

    <!-- List of existing roles -->
    <h3>Existing Roles:</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Role Name</th>
                <th>Description</th>
                <th>Assigned User</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['role_name']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>

                <!-- Fetch assigned user dynamically -->
                <?php 
                    $userQuery = "SELECT username FROM users WHERE role_id = {$row['id']}";
                    $userResult = mysqli_query($conn, $userQuery);
                    if ($userResult && mysqli_num_rows($userResult) > 0) {
                        echo "<td>" . htmlspecialchars(mysqli_fetch_assoc($userResult)['username']) . "</td>";
                    } else {
                        echo "<td>Not assigned</td>";
                    }
                ?>

                <!-- Actions -->
                <td>
                    <a href="edit_role.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_role.php?id=<?php echo $row['id']; ?>" onclick='return confirm("Are you sure you want to delete this role?");' class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Do not close the connection here; let PHP handle it automatically.
?>

</body>
</html>