<?php
session_start();

echo '<title>Edit flower</title>';

include("connection.php");
include("admin_function.php");
$admin_data = check_login($con);

// استقبل flower_id من GET
if (!isset($_GET['flower_id'])) {
    die("Missing flower ID");
}

$flower_id = $_GET['flower_id'];

// عند الضغط على زر التحديث
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $color = $_POST['color'];
    $cost_per_day = $_POST['costperday'];
    $status = $_POST['status'];

    $query = "UPDATE flower 
              SET color = '$color', cost_per_day = '$cost_per_day', status = '$status' 
              WHERE flower_id = '$flower_id'";

    if (!mysqli_query($con, $query)) {
        echo "Error updating record: " . mysqli_error($con);
    } else {
        header("Location: edit_flower.php");
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
    <title>Edit Flower</title>
    <link rel="stylesheet" href="flower.css">
    <script>
        function validateForm() {
            var color = document.forms["myForm"]["color"].value;
            var cost = document.forms["myForm"]["costperday"].value;
            var status = document.forms["myForm"]["status"].value;

            if (color == "") {
                alert("Flower color must be filled out");
                return false;
            }
            if (cost == "" || cost <= 0) {
                alert("Flower cost must be positive");
                return false;
            }
            if (status == "") {
                alert("Status must be filled out");
                return false;
            }
            if (status != "Active" && status != "Out of service" && status != "Rented") {
                alert("Invalid status");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <nav>
        <a href="admin_logout.php">LOGOUT</a>
    </nav>
    <?php
    $query = "SELECT color, cost_per_day, status FROM flower WHERE flower_id = '$flower_id'";
    $result = mysqli_query($con, $query);
    $row = mysqli_fetch_assoc($result);

    echo "<main>";
    echo "<div class='edit'>";
    echo '<form method="POST" name="myForm" onsubmit="return validateForm()">';
    echo "<p>Color</p>";
    echo "<input type='text' name='color' value='{$row['color']}'><br>";
    echo "<p>Cost per day</p>";
    echo "<input type='number' name='costperday' value='{$row['cost_per_day']}'><br>";
    echo "<p>Status</p>";
    echo "<input type='text' name='status' value='{$row['status']}'><br>";
    echo "<input type='submit' value='Update flower'><br>";
    echo "</form>";
    echo "</div>";
    echo "</main>";
    ?>
</body>
