<?php
session_start();
require_once 'BeautyP_dbconnect.php';

// Ensure user is logged in
if (!isset($_SESSION['client_id'])) {
    header("Location: BP_Login.php");
    exit();
}

$client_id = $_SESSION['client_id'];
$message = "";

// Handle Appointment Cancellation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['btncancel'])) {
    $appointment_id = (int)($_POST['appointment_id'] ?? 0);

    if ($appointment_id > 0) {
     
        $stmt = $conn->prepare("UPDATE appointment SET status = 'Cancelled' WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $appointment_id, $client_id);

        if ($stmt->execute()) {
            $message = "<p style='color: green;'>Appointment cancelled successfully!</p>";
        } else {
            $message = "<p style='color: red;'>Error cancelling appointment: " . htmlspecialchars($stmt->error) . "</p>";
        }
        $stmt->close();
    }
}

// Aquire the active appointments
$stmt = $conn->prepare("SELECT id, appointment_date, stylist, note, status FROM appointment WHERE user_id = ? AND status != 'Cancelled' ORDER BY appointment_date ASC");
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
    
<h2>Cancel Appointment</h2>

    <?php echo $message; ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Date</th>
                <th>Stylist</th>
                <th>Note</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($row['appointment_date']))); ?></td>
                    <td><?php echo htmlspecialchars($row['stylist']); ?></td>
                    <td><?php echo htmlspecialchars($row['note']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                            <input type="hidden" name="appointment_id" value="<?php echo (int)$row['id']; ?>">
                            <button type="submit" name="btn_cancel">Cancel</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No active appointments available to cancel.</p>
    <?php endif; ?>

    <br>
    <p><a href="DashboardAVUCL.php">Back to Dashboard</a></p>
    <p><a href="view_appointments.php">View All Appointments</a></p>

</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
