<?php
// session_start(); // Ensure session is started explicitly
@include 'config.php';
require_once 'session_config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.php");
    exit();
}

// Generate CSRF Token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Handle Add to Cart action
if (isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
    // Verify CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF Token!");
    }

    $user_id = $_SESSION['user_id'];
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);

    // Insert the product into the cart table
    $query = "INSERT INTO cart (user_id, product_id, product_name, product_price) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iisd", $user_id, $product_id, $product_name, $product_price);
    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($success) {
        header("Location: gifts.php"); // Stay on gifts.php
        exit();
    } else {
        header("Location: gifts.php?error=add_failed");
        exit();
    }
}

// Fetch cart count for the user
$cart_count_query = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $cart_count_query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cart_count = mysqli_fetch_assoc($result)['count'];
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gifts - Floward</title>
    <link rel="stylesheet" href="gifts.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

</head>

<body>
    <!-- Navbar -->
    <header>
        <a href="#" class="logo">G<span>ifts</span></a>
        <nav class="navbar">
            <a href="index.php">Home</a>
            <a href="#chocolates">Chocolates</a>
            <a href="#gifts">Gifts</a>
            <!-- رابط السلة مع العداد -->
            <div class="user-section">

        </nav>

        <div class="user-section">
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>

            </div>

            <a href="cart.php" class="cart-counter">
                <i class="fas fa-shopping-cart"></i>
                <span class="badge"><?php echo $cart_count; ?></span>
            </a>

            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        <i class="fas fa-bars"></i>
    </header>

    <!-- Hero Section -->
    <section class="home" id="home">
        <div class="content">
            <h3>Thoughtful Gifts</h3>
            <span>for every occasion</span>
            <p>Explore our curated collection of chocolates and gifts to make every moment special.</p>
            <a href="#chocolates" class="btn">Shop Now</a>
        </div>
    </section>

    <!-- Chocolates Section -->
    <section class="products" id="chocolates">
        <h1 class="heading">Our <span>Chocolates</span></h1>
        <div class="box-container">
            <div class="box">
                <div class="image">
                    <img src="images/wm1.webp" alt="White Chocolate" />
                    <div class="icons">
                        <a href="payment.html" class="cart-btn">BUY</a>
                    </div>
                </div>
                <div class="content">
                    <h3>White Chocolate</h3>
                </div>
            </div>


            <div class="box">
                <div class="image">
                    <img src="images/wm3.webp" alt="Fruit Chocolate" />
                    <div class="icons">
                        <a href="payment.html" class="cart-btn">BUY</a>
                    </div>
                </div>
                <div class="content">
                    <h3>Fruit Chocolate</h3>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images/wm4.webp" alt="Nuts Chocolate" />
                    <div class="icons">
                        <a href="payment.html" class="cart-btn">BUY</a>
                    </div>
                </div>
                <div class="content">
                    <h3>Nuts Chocolate</h3>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images/wn5.webp" alt="Milk Chocolate" />
                    <div class="icons">
                        <a href="payment.html" class="cart-btn">BUY</a>
                    </div>
                </div>
                <div class="content">
                    <h3>Milk Chocolate</h3>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images/wn6.webp" alt="Flavour Chocolate" />
                    <div class="icons">
                        <a href="payment.html" class="cart-btn">BUY</a>
                    </div>
                </div>
                <div class="content">
                    <h3>Flavour Chocolate</h3>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images/wm2.webp" alt="Flavour Chocolate" />
                    <div class="icons">
                        <a href="payment.html" class="cart-btn">BUY</a>
                    </div>
                </div>
                <div class="content">
                    <h3>Flavour Chocolate</h3>
                </div>
            </div>
        </div>

    </section>

    <section class="products" id="gifts">
        <h1 class="heading">Our <span>Gifts</span></h1>
        <div class="box-container">

            <div class="box">
                <div class="image">
                    <img src="images\Gifts.jpg" alt="Gifts Box" />
                    <div class="icons">
                        <a href="payment.html" class="cart-btn">BUY</a>
                    </div>
                </div>
                <div class="content">
                    <h3>Gifts Box</h3>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images\6ccaecadb4e906c111d950621806c9ba.jpg" alt="Mugs" />
                    <div class="icons">
                        <a href="payment.html" class="cart-btn">BUY</a>
                    </div>
                </div>
                <div class="content">
                    <h3>Mugs</h3>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images\shirt.jpg" alt="Designed Shirts" />
                    <div class="icons">
                        <a href="payment.html" class="cart-btn">BUY</a>
                    </div>
                </div>
                <div class="content">
                    <h3>Designed Shirts</h3>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images/M4.webp" alt="Balloons" />
                    <div class="icons">
                        <a href="payment.html" class="cart-btn">BUY</a>
                    </div>
                </div>
                <div class="content">
                    <h3>Balloons</h3>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images\toy.jpeg" alt="Flowers" />
                    <div class="icons">
                        <a href="payment.html" class="cart-btn">BUY</a>
                    </div>
                </div>
                <div class="content">
                    <h3>Toy</h3>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images\df6b68232cd9348cfede300e654971ec.jpg" alt="Keychains" />
                    <div class="icons">
                        <a href="payment.html" class="cart-btn">BUY</a>
                    </div>
                </div>
                <div class="content">
                    <h3>Keychains</h3>
                </div>
            </div>

        </div>
    </section>


    <!-- Footer -->
    <section class="footer">
        <div class="box-container">
            <div class="box">
                <h3>Delivery Locations</h3>
                <p>Alexandria</p>
                <p>Damanhour</p>
                <p>Cairo</p>
                <p>North Sinai</p>
            </div>
            <div class="box">
                <h3>Contact Info</h3>
                <a href="tel:+1234567890">+1234567890</a>
                <a href="mailto:floward@example.com">floward@example.com</a>
            </div>
        </div>
    </section>
</body>

</html>

<?php
mysqli_close($conn);
?>