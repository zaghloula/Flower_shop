<?php
// session_start();
require_once 'config.php';
require_once 'session_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items with flower details
$query = "SELECT c.*, f.stock, f.image, f.flower_id 
          FROM cart c 
          LEFT JOIN flower f ON c.product_id = f.flower_id 
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_amount = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_amount += $row['product_price'];
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_submit'])) {
    $conn->begin_transaction();
    $error = null;

    try {
        // Check stock for all items
        foreach ($cart_items as $item) {
            $check_stock = $conn->prepare("SELECT stock FROM flower WHERE flower_id = ? AND stock > 0 FOR UPDATE");
            $check_stock->bind_param("i", $item['flower_id']);
            $check_stock->execute();
            $current_stock = $check_stock->get_result()->fetch_assoc();

            if (!$current_stock || $current_stock['stock'] <= 0) {
                throw new Exception("Item {$item['product_name']} is out of stock");
            }
        }

        // Update stock and record payment
        foreach ($cart_items as $item) {
            // Update stock
            $update_stock = $conn->prepare("UPDATE flower SET stock = stock - 1 WHERE flower_id = ?");
            $update_stock->bind_param("i", $item['flower_id']);
            $update_stock->execute();

            // Record payment
            $payment_stmt = $conn->prepare("INSERT INTO payments (name, email, amount, product) VALUES (?, ?, ?, ?)");
            $name = $_SESSION['user_name'];
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $amount = $item['product_price'];
            $product = $item['product_name'];

            $payment_stmt->bind_param("ssds", $name, $email, $amount, $product);
            if (!$payment_stmt->execute()) {
                throw new Exception("Failed to record payment");
            }
            $last_payment_id = $conn->insert_id;
        }

        // Clear cart
        $clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $clear_cart->bind_param("i", $user_id);
        $clear_cart->execute();

        $conn->commit();
        header("Location: success.php?payment_id=" . $last_payment_id);
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .payment-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }

        .cart-item {
            background-color: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .cart-item img {
            max-width: 100px;
            border-radius: 5px;
        }

        .total-amount {
            font-size: 1.5rem;
            color: #ff4081;
            font-weight: bold;
        }

        .payment-form {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container payment-container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (empty($cart_items)): ?>
            <div class="alert alert-info">Your cart is empty.</div>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        <?php else: ?>
            <div class="row">
                <div class="col-md-7">
                    <h3 class="mb-4">Cart Items</h3>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item d-flex align-items-center">
                            <img src="<?php echo htmlspecialchars($item['image'] ?? 'images/default.jpg'); ?>"
                                alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="me-3">
                            <div>
                                <h5><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                <p class="mb-0">Price: <?php echo htmlspecialchars($item['product_price']); ?> LE</p>
                                <small class="text-muted">Stock: <?php echo htmlspecialchars($item['stock']); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="mt-4">
                        <h4>Total Amount: <span class="total-amount"><?php echo number_format($total_amount, 2); ?>
                                LE</span></h4>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="payment-form">
                        <h3 class="mb-4">Payment Details</h3>
                        <form method="POST" id="payment-form">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name"
                                    value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="card-number" class="form-label">Card Number</label>
                                <input type="text" class="form-control" id="card-number" required
                                    placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expiry" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="expiry" required placeholder="MM/YY"
                                        maxlength="5">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" required placeholder="123"
                                        maxlength="4">
                                </div>
                            </div>
                            <button type="submit" name="payment_submit" class="btn btn-primary w-100">
                                Pay <?php echo number_format($total_amount, 2); ?> LE
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('payment-form')?.addEventListener('submit', function (e) {
            const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
            const expiry = document.getElementById('expiry').value;
            const cvv = document.getElementById('cvv').value;

            // Validate card number (16 digits)
            if (!/^\d{16}$/.test(cardNumber)) {
                alert('Please enter a valid 16-digit card number');
                e.preventDefault();
                return;
            }

            // Validate expiry date (MM/YY format)
            if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
                alert('Please enter a valid expiry date (MM/YY)');
                e.preventDefault();
                return;
            }

            // Validate CVV (3-4 digits)
            if (!/^\d{3,4}$/.test(cvv)) {
                alert('Please enter a valid CVV');
                e.preventDefault();
                return;
            }
        });

        // Format card number with spaces
        document.getElementById('card-number')?.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove all non-digits
            if (value.length > 16) value = value.substr(0, 16);
            // Add spaces after every 4 digits
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            e.target.value = value;
        });

        // Format expiry date
        document.getElementById('expiry')?.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4) value = value.substr(0, 4);
            if (value.length > 2) {
                value = value.substr(0, 2) + '/' + value.substr(2);
            }
            e.target.value = value;
        });

        // Only allow numbers in CVV
        document.getElementById('cvv')?.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4) value = value.substr(0, 4);
            e.target.value = value;
        });
    </script>
</body>

</html>