<?php
// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=booking system;charset=utf8mb4", "root", "mysql");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$searchResults = [];
$searchedDestination = "";
$hasSearched = false;

// Handles Search Request
if (isset($_GET['btnsearch'])) {
    $searchedDestination = trim($_GET['destination'] ?? '');
    $hasSearched = true;

    $stmt = $pdo->prepare("SELECT `ID`, `Passenger Name`, `Destination`, `Fare` 
                           FROM `booking` 
                           WHERE `Destination` LIKE ? 
                           ORDER BY `ID` ASC");
    $stmt->execute(["%$searchedDestination%"]);
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Bookings by Destination</title>
</head>
<body>

<h2>Search Bookings by Destination</h2>

<form method="get" action="searchBookings.php">
    <label for="destination">Destination:</label>
    <input type="text" name="destination" id="destination" 
           value="<?= htmlspecialchars($searchedDestination) ?>" 
           placeholder="Enter destination..." required>
    <button type="submit" name="btnsearch">Search</button>
    <a href="searchBookings.php"><button type="button">Reset</button></a>
</form>

<br>

<?php if ($hasSearched): ?>
    <h3>Results for "<?= htmlspecialchars($searchedDestination) ?>":</h3>

    <?php if (count($searchResults) > 0): ?>
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
                <?php foreach ($searchResults as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['ID']) ?></td>
                        <td><?= htmlspecialchars($row['Passenger Name']) ?></td>
                        <td><?= htmlspecialchars($row['Destination']) ?></td>
                        <td>R<?= number_format((float)$row['Fare'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: red;">No bookings found matching destination: "<?= htmlspecialchars($searchedDestination) ?>"</p>
    <?php endif; ?>
<?php endif; ?>


</body>
</html>
