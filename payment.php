<?php
// session_start();
require_once 'config.php';
require_once 'session_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login_form.php');
    exit();
}

// Check if flower_id is provided
if (!isset($_GET['flower_id'])) {
    header('Location: index.php');
    exit();
}

$flower_id = intval($_GET['flower_id']);

// Fetch flower details
$stmt = $conn->prepare("SELECT * FROM flower WHERE flower_id = ? AND stock > 0");
$stmt->bind_param("i", $flower_id);
$stmt->execute();
$result = $stmt->get_result();
$flower = $result->fetch_assoc();

if (!$flower) {
    header('Location: index.php?error=flower_not_available');
    exit();
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['payment_submit'])) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Check stock again
            $check_stock = $conn->prepare("SELECT stock FROM flower WHERE flower_id = ? AND stock > 0 FOR UPDATE");
            $check_stock->bind_param("i", $flower_id);
            $check_stock->execute();
            $current_stock = $check_stock->get_result()->fetch_assoc();

            if (!$current_stock) {
                throw new Exception("Flower is out of stock");
            }

            // Update stock
            $update_stock = $conn->prepare("UPDATE flower SET stock = stock - 1 WHERE flower_id = ?");
            $update_stock->bind_param("i", $flower_id);
            $update_stock->execute();

            // Record payment
            $payment_stmt = $conn->prepare("INSERT INTO payments (name, email, amount, product) VALUES (?, ?, ?, ?)");
            $name = $_SESSION['user_name'];
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $amount = $flower['cost_per_day'];
            $product = $flower['color'] . " Flower";

            $payment_stmt->bind_param("ssds", $name, $email, $amount, $product);
            if (!$payment_stmt->execute()) {
                throw new Exception("Failed to record payment");
            }
            $payment_id = $conn->insert_id;

            $conn->commit();
            header("Location: success.php?payment_id=" . $payment_id);
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - <?php echo htmlspecialchars($flower['color']); ?> Flower</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .payment-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .flower-image {
            max-width: 300px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .payment-details {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .price {
            font-size: 1.5rem;
            color: #ff4081;
            font-weight: bold;
        }

        .stock-info {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="container payment-container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo htmlspecialchars($flower['image']); ?>"
                    alt="<?php echo htmlspecialchars($flower['color']); ?> Flower" class="img-fluid flower-image mb-4">
                <div class="flower-info">
                    <h2><?php echo htmlspecialchars($flower['color']); ?> Flower</h2>
                    <p class="price"><?php echo htmlspecialchars($flower['cost_per_day']); ?> LE</p>
                    <p class="stock-info">Available Stock: <?php echo htmlspecialchars($flower['stock']); ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="payment-details">
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
                            <input type="text" class="form-control" id="card-number" name="card-number" required
                                placeholder="1234 5678 9012 3456" maxlength="19">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="expiry" class="form-label">Expiry Date</label>
                                <input type="text" class="form-control" id="expiry" name="expiry" required
                                    placeholder="MM/YY" maxlength="5">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control" id="cvv" name="cvv" required placeholder="123"
                                    maxlength="4">
                            </div>
                        </div>
                        <button type="submit" name="payment_submit" class="btn btn-primary w-100">
                            Pay <?php echo htmlspecialchars($flower['cost_per_day']); ?> LE
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('payment-form').addEventListener('submit', function (e) {
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
        document.getElementById('card-number').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove all non-digits
            if (value.length > 16) value = value.substr(0, 16);
            // Add spaces after every 4 digits
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            e.target.value = value;
        });

        // Format expiry date
        document.getElementById('expiry').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4) value = value.substr(0, 4);
            if (value.length > 2) {
                value = value.substr(0, 2) + '/' + value.substr(2);
            }
            e.target.value = value;
        });

        // Only allow numbers in CVV
        document.getElementById('cvv').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4) value = value.substr(0, 4);
            e.target.value = value;
        });
    </script>
</body>

</html>