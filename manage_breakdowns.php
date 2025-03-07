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

// Fetch breakdowns with machine details
$query = "
    SELECT 
        breakdowns.id AS breakdown_id,
        breakdowns.date_reported,
        breakdowns.description,
        breakdowns.status,
        machines.name AS machine_name,
        machines.serial_number,
        machines.site
    FROM 
        breakdowns
    INNER JOIN 
        machines ON breakdowns.machine_id = machines.id
";
$result = $conn->query($query);

// Fetch machines for dropdown
$machines = $conn->query("SELECT id, name FROM machines");

// Handle form submission for adding new breakdown
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $machine_id = $_POST['machine_id'];
    $date_reported = $_POST['date_reported'];
    $description = $_POST['description'];

    // Prepare query to insert breakdown
    $stmt = $conn->prepare("INSERT INTO breakdowns (machine_id, date_reported, description, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iss", $machine_id, $date_reported, $description);

    if ($stmt->execute()) {
        $success = "Breakdown reported successfully!";
    } else {
        $error = "Error reporting breakdown: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Breakdowns</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <h2>Manage Breakdowns</h2>
    </div>

    <!-- Success/Error Messages -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Report Breakdown Form -->
    <form method="POST">
        <div class="mb-3">
            <label for="machine_id" class="form-label">Machine:</label>
            <select class="form-control" id="machine_id" name="machine_id" required>
                <option value="">Select Machine</option>
                <?php while ($row = $machines->fetch_assoc()) { ?>
                <option value="<?php echo htmlspecialchars($row['id']); ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="date_reported" class="form-label">Date Reported:</label>
            <input type="date" class="form-control" id="date_reported" name="date_reported" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Report Breakdown</button>
    </form>

    <!-- List of Breakdowns -->
    <h3 class="mt-5">Reported Breakdowns:</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Machine Name</th>
                <th>Serial Number</th>
                <th>Site</th>
                <th>Date Reported</th>
                <th>Status</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <!-- Machine Details -->
                <td><?php echo htmlspecialchars($row['machine_name']); ?></td>
                <td><?php echo htmlspecialchars($row['serial_number']); ?></td>
                <td><?php echo htmlspecialchars($row['site']); ?></td>

                <!-- Breakdown Details -->
                <td><?php echo htmlspecialchars($row['date_reported']); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>

                <!-- Actions -->
                <td>
                    <!-- Edit Breakdown -->
                    <a href="edit_breakdown.php?id=<?php echo htmlspecialchars($row['breakdown_id']); ?>" class='btn btn-sm btn-primary'>Edit</a>

                    <!-- Delete Breakdown -->
                    <a href="delete_breakdown.php?id=<?php echo htmlspecialchars($row['breakdown_id']); ?>" onclick='return confirm("Are you sure you want to delete this breakdown?");' class='btn btn-sm btn-danger'>Delete</a>
                </td>
            </tr>

            <?php } ?>
        </tbody>
    </table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
$conn->close();
?>

</body>
</html>
