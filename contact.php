<?php
include 'config.php';

session_start();
if ($conn->connect_error) {
    die("Connection failed: ". $conn->connect_error);
}

// Sanitize and validate input
$name = htmlspecialchars($_POST['name']);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$number = htmlspecialchars($_POST['number']);
$message = htmlspecialchars($_POST['message']);

$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
$email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) : '';
$number = isset($_POST['number']) ? htmlspecialchars($_POST['number']) : '';
$message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';


$sql = "INSERT INTO contacts (name, email, number, message)
VALUES ('$name', '$email', '$number', '$message')";

if ($conn->query($sql) === TRUE) {
    echo "Message sent successfully!";
} else {
    echo "Error: ". $sql. "<br>". $conn->error;
}

$conn->close();
?>
