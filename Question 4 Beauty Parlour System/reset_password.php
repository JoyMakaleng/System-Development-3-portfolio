<?php
require_once 'BeautyP_dbconnect.php';

$email        = 'user@examplecom';
$raw_password = 'password';
$fresh_hash   = password_hash($raw_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE clients SET Client_password = ? WHERE email = ?");
$stmt->bind_param("ss", $fresh_hash, $email);

if ($stmt->execute()) {
    echo "<h2>Password Updated Successfully!</h2>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
    echo "<p><strong>Password:</strong> " . htmlspecialchars($raw_password) . "</p>";
    echo "<p><strong>Generated Hash in DB:</strong> " . htmlspecialchars($fresh_hash) . "</p>";
    echo "<p><a href='BP_Login.php'>Click here to Log In</a></p>";
} else {
    echo "Error updating record: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
