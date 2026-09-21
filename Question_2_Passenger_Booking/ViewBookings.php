<?php
// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=booking system;charset=utf8mb4", "root", "mysql");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch all bookings
$stmt = $pdo->query("SELECT `ID`, `Passenger Name`, `Destination`, `Fare` FROM `booking` ORDER BY `ID` ASC");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the fare  (Average & Highest Fare)
$summaryStmt = $pdo->query("SELECT AVG(`Fare`) AS avg_fare, MAX(`Fare`) AS max_fare, COUNT(*) AS total_bookings FROM `booking`");
$summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

$totalBookings = (int) ($summary['total_bookings'] ?? 0);
$avgFare = (float) ($summary['avg_fare'] ?? 0);
$maxFare = (float) ($summary['max_fare'] ?? 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings & Summary Report</title>
</head>
<body>

<h2>All Passenger Bookings</h2>

<?php if ($totalBookings > 0): ?>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Passenger Name</th>
                <th>Destination</th>
                <th>Fare</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['ID']) ?></td>
                    <td><?= htmlspecialchars($row['Passenger Name']) ?></td>
                    <td><?= htmlspecialchars($row['Destination']) ?></td>
                    <td>R<?= number_format((float)$row['Fare'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>

    <h3>Summary Report</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Total Bookings:</th>
            <td><?= $totalBookings ?></td>
        </tr>
        <tr>
            <th>Average Fare:</th>
            <td>R<?= number_format($avgFare, 2) ?></td>
        </tr>
        <tr>
            <th>Highest Fare:</th>
            <td>R<?= number_format($maxFare, 2) ?></td>
        </tr>
    </table>

<?php else: ?>
    <p>No bookings found in the database.</p>
<?php endif; ?>


</body>
</html>
