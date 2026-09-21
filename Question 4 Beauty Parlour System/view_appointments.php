<?php
session_start();
require_once 'BeautyP_dbconnect.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: BP_Login.php");
    exit();
}

$client_id = $_SESSION['client_id'];


$stmt = $conn->prepare("SELECT id, appointment_date, stylist, note, status FROM appointment WHERE user_id = ? ORDER BY appointment_date ASC");

if (!$stmt) {
    die("Query error: " . $conn->error);
}

$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <h2>Your Appointments</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <ul>
            <?php 
        
            while ($row = $result->fetch_assoc()): 
                $date    = htmlspecialchars($row['appointment_date']);
                $stylist = htmlspecialchars($row['stylist']);
                $notes   = htmlspecialchars($row['note']);
                $status  = htmlspecialchars($row['status']);
            ?>
                <li>
                    <strong><?php echo $date; ?></strong> with <?php echo $stylist; ?>: <?php echo $notes; ?> 
                    <em>(Status: <?php echo $status; ?>)</em>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No appointments found.</p>
    <?php endif; ?>

    <br>
    <p><a href="DashboardAVUCL.php">Back to Dashboard</a></p>
    <p><a href="add_appointment.php">Add a NEW Appointment</a></p>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
