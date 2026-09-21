<?php
// Database connection 
try {
    $pdo = new PDO("mysql:host=localhost;dbname=booking system;charset=utf8mb4", "root", "mysql");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle Export to CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    
    if (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="bookings_' . date('Y-m-d') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    fputcsv($output, ['Passenger Name', 'Destination', 'Fare']);

    $stmt = $pdo->query("SELECT `Passenger Name`, `Destination`, `Fare` FROM `booking`");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit; 
}


// Handles Add Booking Form Submission and Add button functions
if (isset($_POST['btnadd'])) {
    $passenger_name = trim($_POST['passenger_names']);
    $destination = trim($_POST['destination']);
    $fare = trim($_POST['fare']);

    if (!empty($passenger_name) && !empty($destination) && is_numeric($fare)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO `booking` (`Passenger Name`, `Destination`, `Fare`) VALUES (?, ?, ?)");
            $stmt->execute([$passenger_name, $destination, $fare]);
            
            $message = "<p style='color:green;'>Booking added successfully!</p>";
        } catch (PDOException $e) {
            $message = "<p style='color:red;'>Error saving booking: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        $message = "<p style='color:red;'>Please fill in all fields with valid data.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Booking Form</title>
</head>
<body>
    
<?= $message ?>

<h2>Passenger Booking Form</h2>

<form method="post">
    <table>
        <tr>
            <td><label for="passenger_names">Passenger Names:</label></td>
            <td><input type="text" name="passenger_names" id="passenger_names" required></td>
        </tr>
        <tr>
            <td><label for="destination">Destination:</label></td>
            <td><input type="text" name="destination" id="destination" required></td>
        </tr>
        <tr>
            <td><label for="fare">Fare</label></td>
            <td><input type="number" step="0.01" min="0" name="fare" id="fare" required></td>
        </tr>
        <tr>
            <td><button type="submit" name="btnadd">Add Booking</button></td>
        </tr>
    </table>
</form>

<br>
<a href="SearchBookings.php">Search by Destination</a><br>
<a href="ViewBookings.php">View All Bookings</a><br>
<a href="?export=csv">Export to CSV</a><br>

</body>
</html>
