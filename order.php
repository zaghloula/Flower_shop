<?php


include 'config.php';

session_start();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$amount = $_POST['amount'];
$product = $_POST['product'];

// SQL query to insert payment details into database
$sql = "INSERT INTO payments (name, email, amount, product) VALUES ('$name', '$email', '$amount', '$product')";

if ($conn->query($sql) === TRUE) {
    echo "Payment processed successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
