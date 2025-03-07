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

// Fetch all machines
$result = $conn->query("SELECT * FROM machines");

// Handle form submission for adding new machine
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $site = $_POST['site'];
    $serial_number = $_POST['serial_number'];
    $manufacturer = $_POST['manufacturer'];
    $machine_type = $_POST['machine_type'];

    // Prepare query to insert machine
    $stmt = $conn->prepare("INSERT INTO machines (name, description, site, serial_number, manufacturer, machine_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $description, $site, $serial_number, $manufacturer, $machine_type);

    if ($stmt->execute()) {
        $success = "Machine added successfully!";
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
    <title>Manage Machines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <h2 class="text-center">Manage Machines</h2>
    </div>

    <!-- Display Errors or Success Messages -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Form to add new machine -->
    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Machine Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="mb-3">
            <label for="site" class="form-label">Site:</label>
            <input type="text" class="form-control" id="site" name="site" required>
        </div>
        <div class="mb-3">
            <label for="serial_number" class="form-label">Serial Number:</label>
            <input type="text" class="form-control" id="serial_number" name="serial_number" required>
        </div>
        <div class="mb-3">
            <label for="manufacturer" class="form-label">Manufacturer:</label>
            <input type="text" class="form-control" id="manufacturer" name="manufacturer" required>
        </div>
        <div class="mb-3">
            <label for="machine_type" class="form-label">Machine Type:</label>
            <input type="text" class="form-control" id="machine_type" name="machine_type" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Add Machine</button>
    </form>

    <!-- List of existing machines -->
    <h3 class="mt-5">Existing Machines:</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Machine Name</th>
                <th>Description</th>
                <th>Site</th>
                <th>Serial Number</th>
                <th>Manufacturer</th>
                <th>Machine Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['site']); ?></td>
                <td><?php echo htmlspecialchars($row['serial_number']); ?></td>
                <td><?php echo htmlspecialchars($row['manufacturer']); ?></td>
                <td><?php echo htmlspecialchars($row['machine_type']); ?></td>
                <td>
                    <a href="edit_machine.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_machine.php?id=<?php echo $row['id']; ?>" onclick='return confirm("Are you sure you want to delete this machine?");' class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>