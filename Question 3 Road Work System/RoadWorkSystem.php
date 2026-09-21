<?php
require_once 'Dbconnection.php';

$sql = "SELECT customer_code, name FROM customers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<h2>Earthmoving Booking Form</h2>

<form method="post"action="ProjectCostBreakdown.php">

<table>
    <tr>
        <td><label for="customer">Customer</label></td>
        <td>
            <select name="customer" id="customer" required>
                <option value="">Select Customer</option>
                <option value=""></option>
                <?php 
                if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($row['customer_code']) . '">' 
                 . htmlspecialchars($row['customer_code']) . ' - ' . htmlspecialchars($row['name']) 
                 . '</option>';
                            }
                        }
                        ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><label for="distance">Distance (Km)</label></td>
        <td><input type="number" value="0" min="1" name="distance" id="distance" required></td>
    </tr>
    <tr>
        <td><button type="submit" name="btncalculate">Calculate Project Cost</button></td>
    </tr>

</table>

</form>

</body>
</html>
<?php $conn->close(); ?>
