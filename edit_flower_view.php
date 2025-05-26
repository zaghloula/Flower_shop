<?php
session_start();

echo '<title>Edit Flower</title>';

include("connection.php");
include("admin_function.php");
$admin_data = check_login($con);

// استقبل flower_id من GET
if (!isset($_GET['flower_id'])) {
    die("Missing flower ID");
}

$flower_id = $_GET['flower_id'];

// Handle delete request
if (isset($_POST['delete'])) {
    $delete_query = "DELETE FROM flower WHERE flower_id = ?";
    $stmt = mysqli_prepare($con, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $flower_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var modal = document.getElementById('customModal');
                    var modalIcon = document.getElementById('modalIcon');
                    var modalMessage = document.getElementById('modalMessage');
                    modalIcon.innerHTML = '✔';
                    modalIcon.classList.remove('error');
                    modalIcon.classList.add('success');
                    modalMessage.textContent = 'Flower deleted successfully!';
                    modal.style.display = 'flex';
                    setTimeout(() => { window.location.href = 'edit_flower.php'; }, 2000);
                });
              </script>";
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var modal = document.getElementById('customModal');
                    var modalIcon = document.getElementById('modalIcon');
                    var modalMessage = document.getElementById('modalMessage');
                    modalIcon.innerHTML = '✖';
                    modalIcon.classList.remove('success');
                    modalIcon.classList.add('error');
                    modalMessage.textContent = 'Error deleting flower: " . mysqli_error($con) . "';
                    modal.style.display = 'flex';
                });
              </script>";
    }
    mysqli_stmt_close($stmt);
    exit;
}

// Fetch flower data
$query = "SELECT color, cost_per_day, status FROM flower WHERE flower_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $flower_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Flower</title>
    <link rel="stylesheet" href="flower.css">
    <link rel="stylesheet" href="modal.css">
    <script>
        function validateForm() {
            return true; // No validation needed for delete
        }
    </script>
</head>
<body>
    <nav>
        <a href="admin_logout.php">LOGOUT</a>
    </nav>
    <?php if (isset($row)) { ?>
        <main>
            <div class='edit'>
                <form method="POST" name="myForm" onsubmit="return validateForm()">
                    <p>Color</p>
                    <input type="text" name="color" value="<?php echo htmlspecialchars($row['color']); ?>" readonly><br>
                    <p>Cost per day</p>
                    <input type="number" name="costperday" value="<?php echo htmlspecialchars($row['cost_per_day']); ?>" readonly><br>
                    <p>Status</p>
                    <input type="text" name="status" value="<?php echo htmlspecialchars($row['status']); ?>" readonly><br>
                    <input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure you want to delete this flower?');" style="background-color: #dc3545; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;"><br>
                </form>
            </div>
        </main>
    <?php } else { ?>
        <p style='color: red; text-align: center;'>Flower not found!</p>
    <?php } ?>

    <!-- Custom Modal -->
    <div id="customModal" class="modal">
        <div class="modal-content">
            <div id="modalIcon" class="modal-icon"></div>
            <p id="modalMessage"></p>
            <button id="modalOkButton" class="modal-button">OK</button>
        </div>
    </div>

    <script>
        document.getElementById('modalOkButton').addEventListener('click', function() {
            document.getElementById('customModal').style.display = 'none';
            window.location.href = 'edit_flower.php'; // Redirect on OK
        });
    </script>
</body>
</html>