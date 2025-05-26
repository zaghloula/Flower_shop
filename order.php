<?php
header('Content-Type: application/json');

include 'config.php';

session_start();

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error); // Log error
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get form data with fallback
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$amount = $_POST['amount'] ?? 0;
$product = $_POST['product'] ?? '';

// Check if required fields are present
if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

// Check if email exists in user_form table
$checkEmail = $conn->prepare("SELECT email FROM user_form WHERE email = ?");
if ($checkEmail === false) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Database query preparation failed']);
    exit;
}
$checkEmail->bind_param("s", $email);
if (!$checkEmail->execute()) {
    error_log("Execute failed: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Database query execution failed']);
    exit;
}
$result = $checkEmail->get_result();

if ($result->num_rows > 0) {
    // Email exists, proceed with payment
    $sql = $conn->prepare("INSERT INTO payments (name, email, amount, product) VALUES (?, ?, ?, ?)");
    if ($sql === false) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Payment preparation failed']);
        exit;
    }
    $sql->bind_param("ssds", $name, $email, $amount, $product);
    if ($sql->execute()) {
        echo json_encode(['success' => true, 'message' => 'Payment processed successfully']);
    } else {
        error_log("Execute failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Error processing payment']);
    }
    $sql->close();
} else {
    echo json_encode(['success' => false, 'message' => 'email not found please signup']);
}

$checkEmail->close();
$conn->close();
?>