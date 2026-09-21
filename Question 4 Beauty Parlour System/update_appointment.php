<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'BeautyP_dbconnect.php';

// Checks user Authentication
if (!isset($_SESSION['client_id'])) {
    header("Location: BP_Login.php");
    exit();
}

$client_id = $_SESSION['client_id'];
$message = "";
$appointment_to_edit = null;

try {
    //Manages the Update Form Submission
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['btn_update'])) {
        $appointment_id   = (int)($_POST['appointment_id'] ?? 0);
        $appointment_date = trim($_POST['date'] ?? '');
        $stylist          = trim($_POST['stylist'] ?? '');
        $note             = trim($_POST['note'] ?? '');

        if (empty($appointment_date) || empty($stylist) || empty($note)) {
            $message = "<p style='color: red;'>Please fill in all fields.</p>";
        } else {
            $update_stmt = $conn->prepare("UPDATE appointment SET appointment_date = ?, stylist = ?, note = ? WHERE id = ? AND user_id = ?");
            $update_stmt->bind_param("sssii", $appointment_date, $stylist, $note, $appointment_id, $client_id);

            if ($update_stmt->execute()) {
                $message = "<p style='color: green;'>Appointment updated successfully!</p>";
            } else {
                $message = "<p style='color: red;'>Error updating appointment: " . htmlspecialchars($update_stmt->error) . "</p>";
            }
            $update_stmt->close();
        }
    }

    // Fetch specific record if the variable edit_id is passed in the URL
    if (isset($_GET['edit_id'])) {
        $edit_id = (int)$_GET['edit_id'];
        $edit_stmt = $conn->prepare("SELECT id, appointment_date, stylist, note FROM appointment WHERE id = ? AND user_id = ?");
        $edit_stmt->bind_param("ii", $edit_id, $client_id);
        $edit_stmt->execute();
        $res = $edit_stmt->get_result();
        $appointment_to_edit = $res->fetch_assoc();
        $edit_stmt->close();
    }

    //Fetch active appointments for listing
    $list_stmt = $conn->prepare("SELECT id, appointment_date, stylist, note, status FROM appointment WHERE user_id = ? AND status != 'Cancelled' ORDER BY appointment_date ASC");
    $list_stmt->bind_param("i", $client_id);
    $list_stmt->execute();
    $all_appointments = $list_stmt->get_result();

} catch (Exception $e) {
    die("<h3>Database Error:</h3> " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Appointment</title>
</head>
<body>

    <h2>Update Appointment</h2>

    <?php echo $message; ?>

    <!-- Edit Form -->
    <?php if (!empty($appointment_to_edit)): ?>
        <?php
            $raw_date = $appointment_to_edit['appointment_date'];
            $clean_date = substr($raw_date, 0, 10);
        ?>
        <h3>Edit Appointment Details</h3>
        <form method="POST" action="update_appointment.php">
            <input type="hidden" name="appointment_id" value="<?php echo (int)$appointment_to_edit['id']; ?>">
            <table>
                <tr>
                    <td><label for="date">Date:</label></td>
                    <td><input type="date" name="date" id="date" value="<?php echo htmlspecialchars($clean_date); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="stylist">Stylist:</label></td>
                    <td><input type="text" name="stylist" id="stylist" value="<?php echo htmlspecialchars($appointment_to_edit['stylist']); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="note">Note:</label></td>
                    <td><input type="text" name="note" id="note" value="<?php echo htmlspecialchars($appointment_to_edit['note']); ?>" required></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <br>
                        <button type="submit" name="btn_update">Save Changes</button>
                        <a href="update_appointment.php" style="margin-left: 10px;">Cancel</a>
                    </td>
                </tr>
            </table>
        </form>
        <hr>
    <?php endif; ?>

    <h3>Select an Appointment to Edit</h3>
    <?php if ($all_appointments && $all_appointments->num_rows > 0): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Date</th>
                <th>Stylist</th>
                <th>Note</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $all_appointments->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars(substr($row['appointment_date'], 0, 10)); ?></td>
                    <td><?php echo htmlspecialchars($row['stylist']); ?></td>
                    <td><?php echo htmlspecialchars($row['note']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <a href="update_appointment.php?edit_id=<?php echo (int)$row['id']; ?>">Edit</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No active appointments found to update.</p>
    <?php endif; ?>

    <br>
    <p><a href="DashboardAVUCL.php">Back to Dashboard</a></p>

</body>
</html>
<?php
if (isset($list_stmt)) {
    $list_stmt->close();
}
$conn->close();
?>
