<?php
// session_start();
require_once 'config.php';
require_once 'session_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

// Check if payment_id is provided
if (!isset($_GET['payment_id'])) {
    header('Location: index.php');
    exit();
}

$payment_id = intval($_GET['payment_id']);

// Fetch all payments with the same timestamp as the given payment_id
$stmt = $conn->prepare("
    SELECT p1.* 
    FROM payments p1 
    JOIN payments p2 ON DATE(p1.payment_date) = DATE(p2.payment_date) 
        AND HOUR(p1.payment_date) = HOUR(p2.payment_date) 
        AND MINUTE(p1.payment_date) = MINUTE(p2.payment_date) 
        AND SECOND(p1.payment_date) = SECOND(p2.payment_date) 
    WHERE p2.payment_id = ? AND p1.email = p2.email
");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$payments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (empty($payments)) {
    header('Location: index.php');
    exit();
}

// Calculate total amount
$total_amount = 0;
foreach ($payments as $payment) {
    $total_amount += $payment['amount'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }

        .success-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background-color: #4caf50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .success-icon i {
            font-size: 40px;
            color: white;
        }

        .order-details {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
            text-align: left;
        }

        .order-details h4 {
            color: #666;
            margin-bottom: 1rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
        }

        .btn-continue {
            background-color: #ff4081;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-continue:hover {
            background-color: #f50057;
            color: white;
        }

        .thank-you {
            color: #4caf50;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .confirmation-text {
            color: #666;
            margin-bottom: 2rem;
        }

        .purchased-item {
            padding: 8px 0;
            border-bottom: 1px dashed #dee2e6;
        }

        .purchased-item:last-child {
            border-bottom: none;
        }

        .detail-row .detail-value {
            flex: 1;
        }

        .detail-row {
            align-items: flex-start;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="thank-you">Thank You!</h1>
            <p class="confirmation-text">Your payment was successful and your order has been confirmed.</p>

            <div class="order-details">
                <h4>Order Details</h4>
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">#<?php echo str_pad($payment_id, 6, '0', STR_PAD_LEFT); ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Items:</span>
                    <div class="detail-value">
                        <?php foreach ($payments as $index => $payment): ?>
                            <div class="purchased-item">
                                <?php echo ($index + 1) . ". " . htmlspecialchars($payment['product']); ?> -
                                <?php echo number_format($payment['amount'], 2); ?> LE
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value"><?php echo number_format($total_amount, 2); ?> LE</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span
                        class="detail-value"><?php echo date('F j, Y g:i A', strtotime($payments[0]['payment_date'])); ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($payments[0]['name']); ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($payments[0]['email']); ?></span>
                </div>
            </div>

            <div class="mt-4">
                <a href="index.php" class="btn-continue">Continue Shopping</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>