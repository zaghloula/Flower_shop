<?php
include 'config.php';

session_start();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
        die("Please fill in all required fields (name, email, message)");
    }

    // Sanitize and validate input
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $number = isset($_POST['number']) ? htmlspecialchars(trim($_POST['number'])) : '';
    $message = htmlspecialchars(trim($_POST['message']));

    if ($email === false) {
        die("Invalid email address");
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, number, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $number, $message);

    if ($stmt->execute()) {
        echo "Message sent successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request method";
}

$conn->close();
?>