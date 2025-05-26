<?php
session_start(); // Ensure session is started explicitly
@include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit();
}

// Handle product deletion
if (isset($_POST['delete_product']) && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $delete_query = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: cart.php"); // Refresh page after deletion
    exit();
}

// Clear cart if requested
if (isset($_POST['clear_cart'])) {
    $user_id = $_SESSION['user_id'];
    $delete_query = "DELETE FROM cart WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: cart.php");
    exit();
}

// Fetch cart items for the user
$user_id = $_SESSION['user_id'];
$query = "SELECT product_id, product_name, product_price FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cart_items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $cart_items[] = $row;
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cart-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-table th,
        .cart-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .cart-table th {
            background: #ff4081;
            color: white;
        }

        .btn-delete {
            background: #f44336;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .btn-delete:hover {
            background: #d32f2f;
        }

        .btn-buy {
            background: #4caf50;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
            text-decoration: none;
        }

        .btn-buy:hover {
            background: #45a049;
        }

        .btn-clear {
            background: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-clear:hover {
            background: #d32f2f;
        }

        .btn-back {
            background: #f06292;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #ec407a;
        }

        .btn-buy-all {
            background: #4caf50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin-left: 10px;
        }

        .btn-buy-all:hover {
            background: #45a049;
        }
    </style>
</head>

<body>
    <div class="cart-container">
        <h2>Your Cart</h2>
        <?php if (!empty($cart_items)): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Action</th>
                        <th>Buy</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_id']); ?></td>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['product_price']); ?> LE</td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="product_id"
                                        value="<?php echo htmlspecialchars($item['product_id']); ?>">
                                    <button type="submit" name="delete_product" class="btn-delete">Delete</button>
                                </form>
                            </td>
                            <td>
                                <a href="cart_payment.php" class="btn-buy">Buy</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form method="post" style="margin-top: 20px;">
                <button type="submit" name="clear_cart" class="btn-clear">Clear Cart</button>
            </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
        <a href="gifts.php" class="btn-back" style="margin-top: 20px; display: inline-block;">Back to Products</a>
        <a href="cart_payment.php" class="btn-buy-all" style="margin-top: 20px; display: inline-block;">Buy All</a>
    </div>
</body>

</html>

<?php
mysqli_close($conn);
?>