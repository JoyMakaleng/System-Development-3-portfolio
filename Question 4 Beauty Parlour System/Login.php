<?php
session_start();
require_once 'BeautyP_dbconnect.php';

$error_message = "";

// Check if the user is already logged in 
if (isset($_SESSION['client_id'])) {
    header("Location: DashboardAVUCL.php");
    exit();
} elseif (isset($_COOKIE['remember_client'])) {
    $_SESSION['client_id'] = $_COOKIE['remember_client'];
    header("Location: DashboardAVUCL.php");
    exit();
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = ($_POST['password'] ?? '');
    $remember = isset($_POST['remember_me']);

    // Error handling for missing fields
    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password.";
    } else {
        // Retrieve user from database
        $stmt = $conn->prepare("SELECT Client_id, email, Client_password FROM clients WHERE email = ?");
        
        if (!$stmt) {
            die("Query error: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($client = $result->fetch_assoc()) {
            // Verifies the hashed password
            if (password_verify($password, $client['Client_password']) || $password === $client['Client_password']) {
                
                // Start Session
                $_SESSION['client_id'] = $client['Client_id'];
                $_SESSION['email'] = $client['email'];

                
                if ($remember) {
                    setcookie("remember_client", $client['Client_id'], time() + (86400 * 30), "/");
                }

                header("Location: DashboardAVUCL.php");
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "No account found with that email.";
        }
        $stmt->close();
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
    
<h2>Beauty Parlour Login</h2>

<?php if (!empty($error_message)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
<?php endif; ?>

<form method="Post" action="">
<table>
<tr>
    <td><label for="email">EMAIL:</label></td>
    <td><input type="email" name="email" id="email" required></td>
</tr>
<tr>
    <td><label for="password">PASSWORD:</label></td>
    <td><input type="password" name="password" id="password" required></td>
</tr>
<tr>
    <td colspan="2"><input type="checkbox" name="remember_me" id="remember_me"><label for="remember_me">Remember Me</label></td>
</tr>
<tr>
    <td colspan="2"><button type="submit">Login</button></td>
</tr>
</table>

</form>
</body>
</html>
