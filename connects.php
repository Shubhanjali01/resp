<?php
// Retrieve form data
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$gender = $_POST['gender'];
$email = $_POST['email'];
$password = $_POST['password'];
$number = $_POST['number'];

// Hash the password before storing it
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'test');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
} else {
    // Check if the email already exists
    $stmt = $conn->prepare("SELECT email FROM registration WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email already exists, prompt the user to choose a different one
        echo "This email is already registered. Please use a different email.";
    } else {
        // Prepare the SQL statement for inserting a new record
        $stmt = $conn->prepare("INSERT INTO registration (firstName, lastName, gender, email, password, number) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $firstName, $lastName, $gender, $email, $hashedPassword, $number);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            echo "Registration successful! You can now <a href='login.html'>Login</a>";
        } else {
            echo "Something went wrong: " . $stmt->error;
        }
    }

    // Close the prepared statement and connection
    $stmt->close();
    $conn->close();
}
?>
