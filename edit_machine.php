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

// Fetch machine details
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM machines WHERE id = '$id'");
$machine = $result->fetch_assoc();

// Handle form submission for editing machine
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $serial_number = $_POST['serial_number'];
    $machine_type = $_POST['machine_type'];
    $manufacturer = $_POST['manufacturer'];
    $site = $_POST['site'];
    $description = $_POST['description'];

    // Prepare query to update machine
    $stmt = $conn->prepare("UPDATE machines SET name = ?, serial_number = ?, machine_type = ?, manufacturer = ?, site = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $name, $serial_number, $machine_type, $manufacturer, $site, $description, $id);

    if ($stmt->execute()) {
        $success = "Machine updated successfully!";
    } else {
        $error = "Error updating machine: " . $stmt->error;
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
    <title>Edit Machine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="manage_machines.php" class="btn btn-secondary">Back to Machines</a>
        <h2 class="text-center">Edit Machine</h2>
    </div>

    <!-- Display Errors or Success Messages -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Form to edit machine -->
    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Machine Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($machine['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="serial_number" class="form-label">Serial Number:</label>
            <input type="text" class="form-control" id="serial_number" name="serial_number" value="<?php echo htmlspecialchars($machine['serial_number']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="machine_type" class="form-label">Machine Type:</label>
            <input type="text" class="form-control" id="machine_type" name="machine_type" value="<?php echo htmlspecialchars($machine['machine_type']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="manufacturer" class="form-label">Manufacturer:</label>
            <input type="text" class="form-control" id="manufacturer" name="manufacturer" value="<?php echo htmlspecialchars($machine['manufacturer']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="site" class="form-label">Site:</label>
            <input type="text" class="form-control" id="site" name="site" value="<?php echo htmlspecialchars($machine['site']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($machine['description']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Update Machine</button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>