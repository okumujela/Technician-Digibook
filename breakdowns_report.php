<?php
include 'config.php';

$report = $conn->query("
    SELECT breakdowns.*,
           machines.name AS machine_name,
           machines.machine_type,
           machines.serial_number,
           machines.site
    FROM breakdowns
    JOIN machines ON breakdowns.machine_id = machines.id
    ORDER BY machines.serial_number, breakdowns.date_reported DESC
");

echo '<table class="table table-hover">
        <thead>
            <tr>
                <th>Machine</th>
                <th>Type</th>
                <th>Serial</th>
                <th>Site</th>
                <th>Date Reported</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>';

while($row = $report->fetch_assoc()) {
    echo "<tr>
            <td>".htmlspecialchars($row['machine_name'])."</td>
            <td>".htmlspecialchars($row['machine_type'])."</td>
            <td>".htmlspecialchars($row['serial_number'])."</td>
            <td>".htmlspecialchars($row['site'])."</td>
            <td>".htmlspecialchars($row['date_reported'])."</td>
            <td>".htmlspecialchars($row['description'])."</td>
          </tr>";
}

echo '</tbody></table>';
$conn->close();
?>