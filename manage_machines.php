<?php
session_start();
include 'config.php';

// ... [existing authentication and form handling code] ...

$machines = $conn->query("
    SELECT *, DATE_FORMAT(date_installed, '%Y-%m-%d') AS install_date 
    FROM machines
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Machines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Header Section -->
    <div class="d-flex justify-content-between mb-4">
        <h2>Manage Machines</h2>
        <a href="dashboard.php" class="btn btn-secondary">Back</a>
    </div>

    <!-- Add Machine Form -->
    <!-- ... [existing form code] ... -->

    <!-- Machines List -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Serial</th>
                <th>Type</th>
                <th>Site</th>
                <th>Install Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($machine = $machines->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($machine['name']) ?></td>
                <td><?= htmlspecialchars($machine['serial_number']) ?></td>
                <td><?= htmlspecialchars($machine['machine_type']) ?></td>
                <td><?= htmlspecialchars($machine['site']) ?></td>
                <td><?= htmlspecialchars($machine['install_date']) ?></td>
                <td>
                    <span class="badge bg-<?= $machine['status'] === 'active' ? 'success' : 'danger' ?>">
                        <?= ucfirst($machine['status']) ?>
                    </span>
                </td>
                <td>
                    <a href="edit_machine.php?id=<?= $machine['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete_machine.php?id=<?= $machine['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>