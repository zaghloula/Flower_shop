<?php
session_start();
include "connection.php";
$conn = Connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get the posted data
    $color = $_POST['color'];
    $cost = $_POST['cost'];
    $status = $_POST['status'];

    // Prepare the SQL query to check for existing records
    $sql = "SELECT * FROM flower WHERE color = '$color' AND cost_per_day = '$cost' AND status = '$status'";
    $result = mysqli_query($conn, $sql);
    $rowcount = mysqli_num_rows($result);

    if ($rowcount > 0) {
        header("Location: add_flower.html?error=Flower with this combination already exists!");
        exit();
    }

    if (!empty($color) && !empty($cost) && !empty($status)) {
        // Insert data into the database
        $stmt = $conn->prepare("INSERT INTO flower (color, cost_per_day, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $color, $cost, $status);
        
        if ($stmt->execute()) {
            // Redirect to success page
            header("Location: success.php");
            exit();
        } else {
            header("Location: add_flower.html?error=Failed to add flower.");
            exit();
        }
    } else {
        header("Location: add_flower.html?error=Please enter valid information!");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Flower</title>
    <link rel="stylesheet" href="signup.css">
    <script>
        function validateForm() {
            var cost = document.forms["myForm"]["cost"].value;
            if (cost == "" || cost <= 0) {
                alert("Please check flower cost again");
                return false;
            }
            var status = document.forms["myForm"]["status"].value;
            if (status == "") {
                alert("Flower status must be filled out");
                return false;
            }
            if (status != "Active" && status != "Out of service" && status != "Rented") {
                alert("Invalid flower status!!");
                return false;
            }
        }
    </script>
</head>
<body>
    <header>
        <a href="#" class="logo">Floward</a>
    </header>
    <form action="flower.php" method="post" name="myForm" onsubmit="return validateForm()">
        <h2>Add Flower</h2>
        <?php if (isset($_GET['error'])) { ?>
            <p class="error"><?php echo $_GET['error']; ?></p>
        <?php } ?>
        <label>Color</label>
        <input type="text" name="color" placeholder="Enter flower color"><br>
        <label>Cost</label>
        <input type="number" name="cost" placeholder="Enter flower cost"><br>
        <label>Status</label>
        <input type="text" name="status" placeholder="Active/Out of service"><br>
        <button type="submit">Add Flower</button>
    </form>
</body>
</html>