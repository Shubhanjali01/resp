<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "post") {
    // Trim input to avoid accidental spaces
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Database connection
    $con = new mysqli("localhost", "root", "", "test");

    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $con->prepare("SELECT * FROM registration WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt_result = $stmt->get_result();


        if ($stmt_result->num_rows > 0) {
            $data = $stmt_result->fetch_assoc();

            // Verify password using hashed value
            if (password_verify($password, $data['password'])) {
                // Session handling
                $_SESSION['email'] = $email;

                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
// 
//                 // Optional: redirect to dashboard or home page
//                 header("Location: dashboard.php");
                exit();
            } else {
                // Password mismatch
                $_SESSION['error_message'] = "Invalid Email or Password";
                header("Location: login.html");
                exit();
            }
        } else {
            // User not found
            $_SESSION['error_message'] = "Invalid Email or Password";
            header("Location: login.html");
            exit();
        }
    }
} else {
    // If accessed without POST, redirect to login form
    header("Location: login.html");
    exit();
}
?>
