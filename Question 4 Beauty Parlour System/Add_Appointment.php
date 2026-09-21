<?php

session_start();
require_once 'BeautyP_dbconnect.php';

//Makes Sure the user is logged in
if(!isset($_SESSION['client_id'])){
header("Location: BP_Login.php");
exit();

}

$client_id = $_SESSION['client_id'];
$message = "";

// Manages the User Submissions
if($_SERVER["REQUEST_METHOD"]=== "POST" && isset($_POST['btnadd'])){
    $appointment_date = trim($_POST['date'] ?? '' );
    $stylist = trim($_POST['stylist'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    //Error Handling
    if(empty($appointment_date) || empty($stylist) || empty($notes)){
        $message = "<p style='color: red;'>Please Fill in ALL Spaces</p>";
   } else {
        // Secure prepared statement insertion
       $stmt = $conn->prepare("INSERT INTO appointment (user_id, appointment_date, stylist, note, status) VALUES (?, ?, ?, ?, 'Scheduled')");
        
        if (!$stmt) {
            $message = "<p style='color: red;'>Query Preparation Error: " . htmlspecialchars($conn->error) . "</p>";
        } else {
            $stmt->bind_param("isss", $client_id, $appointment_date, $stylist, $notes);

            if ($stmt->execute()) {
                $message = "<p style='color: green;'>Appointment successfully added!</p>";
            } else {
                $message = "<p style='color: red;'>Error saving appointment: " . htmlspecialchars($stmt->error) . "</p>";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

     <h2>Add Appointment</h2>

     <?php echo $message; ?>

    <form method="post" action="">
    <table>

    <tr>
    <td><label for="date">Date:</label></td>
    <td><input type="date" name="date" id="date" required></td>
    </tr>

    <tr>
    <td><label for="stylist">Stylist:</label></td>
    <td><input type="text" name="stylist" id="stylist" required></td>
    </tr>

    <tr>
    <td><label for="notes">Notes:</label></td>
    <td><input type="text" name="notes" id="notes" required></td>
    </tr>
    <tr>
    <td colspan="2"><br><button type="submit" name="btnadd">Add Appointment</button></td>
    </tr>
    </table>
</form>

<br>
<p><a href="DashboardAVUCL.php">Back to Dashboard</a></p>

</body>
</html>
<?php $conn->close(); ?>
