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
        header("Location: index.php"); // Stay on index.php
        exit();
    } else {
        header("Location: index.php?error=add_failed");
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
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' media='screen' href='style.css'>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Floward</title>
</head>

<body>
    <header>
        <a href="#" class="logo">Floward</a>

        <nav class="navbar">
            <a href="#home" onclick="navigateTo('home')">Home</a>
            <a href="#products" onclick="navigateTo('products')">Products</a>
            <a href="gifts.php" onclick="navigateTo('gifts')">Gifts</a>
            <a href="#about" onclick="navigateTo('about')">About</a>
            <a href="#contact" onclick="navigateTo('contact')">Contact</a>
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
    </header>

    <section class="home" id="home">
        <div class="content">
            <h3>Floward</h3>
            <span>natural & beautiful flowers</span>
            <p>Let's make beautiful flowers a part of your life!</p>
            <br>
        </div>
    </section>

    <section class="products" id="products">
        <h1 class="heading">latest <span>products</span></h1>
        <div class="box-container">
            <?php
            // Fetch flowers from database
            $query = "SELECT * FROM flower WHERE status = 'Active' ORDER BY flower_id DESC";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $image = !empty($row['image']) ? $row['image'] : 'images/Boq 1.jpg'; // Default image if none set
                    ?>
                    <div class="box">
                        <div class="image">
                            <img src="<?php echo htmlspecialchars($image); ?>"
                                alt="<?php echo htmlspecialchars($row['color']); ?> Flower">
                            <div class="icons">
                                <?php if ($row['stock'] > 0) { ?>
                                    <a href="payment.php?flower_id=<?php echo $row['flower_id']; ?>" class="cart-btn">BUY</a>
                                <?php } else { ?>
                                    <span class="out-of-stock">Out of Stock</span>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="content">
                            <h3><?php echo htmlspecialchars($row['color']); ?> Flower</h3>
                            <div class="price-cart">
                                <div class="price"><?php echo htmlspecialchars($row['cost_per_day']); ?> LE</div>
                                <div class="stock">Stock: <?php echo htmlspecialchars($row['stock']); ?></div>
                                <?php if ($row['stock'] > 0) { ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="product_id" value="<?php echo $row['flower_id']; ?>">
                                        <input type="hidden" name="product_name"
                                            value="<?php echo htmlspecialchars($row['color']); ?> Flower">
                                        <input type="hidden" name="product_price"
                                            value="<?php echo htmlspecialchars($row['cost_per_day']); ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <button type="submit" name="add_to_cart" class="cart-icon-btn">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p class="text-center w-100">No flowers available at the moment.</p>';
            }
            ?>
        </div>
    </section>

    <section class="about" id="about">
        <h1 class="heading"><span>about</span> us</h1>
        <div class="row">
            <div class="content">
                <p>
                <h1>Welcome to our blossoming space on the web! We are a vibrant team of five students from Alexandria
                    National University, united by our passion for nature's most exquisite creations—flowers.</h1>
                </p>
                <br><br>
                <h2>Meet Our Team:</h2>
                <h3>
                    <p>Kerlous Nasser Shehata</p>
                    <p>Mohammed Elsharkawey</p>
                    <p>Mahmoud Amr</p>
                    <p>Abdo Hisham</p>
                </h3>
                <h1>
                    Together, we cultivate not just flowers, but also joy and connections. Each stem, petal, and leaf is
                    handpicked with care to ensure that when you choose our flowers, you're not just picking a product,
                    but a piece of our shared love for flora.
                    <br><br>
                    Thank you for visiting us, and may your days be filled with the fragrance and beauty of our
                    carefully curated blooms.
                    <br><br>
                    Feel free to adjust the description to fit your team's unique qualities and roles. I hope this helps
                    your website flourish!
                </h1>
            </div>
        </div>
    </section>

    <section class="contact" id="contact">
        <h1 class="heading"><span>contact</span> us</h1>
        <div class="row">
            <form id="contactForm" method="POST">
                <input type="text" name="name" placeholder="name" class="box">
                <input type="email" name="email" placeholder="email" class="box">
                <input type="number" name="number" placeholder="number" class="box">
                <textarea name="message" class="box" placeholder="message" id="" cols="30" rows="10"></textarea>
                <button type="submit" class="submit-btn">Send Message</button>
            </form>
            <div class="image">
                <img src="images/contact-img.svg" alt="">
            </div>
        </div>
    </section>

    <section class="footer">
        <div class="box-container">
            <div class="box">
                <h3>Delivery locations</h3>
                <p>Alexandria</p>
                <p>Damanhour</p>
                <p>Cairo</p>
                <p>North Sinai</p>
            </div>
            <div class="box">
                <h3>contact info</h3>
                <a href="tel:+1234567890">+1234567890</a>
                <a href="mailto:floward@example.com">floward@example.com</a>
            </div>
        </div>
    </section>

    <script src='script.js'></script>
</body>

</html>