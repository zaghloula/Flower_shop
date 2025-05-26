<?php
session_start();
include "connection.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit;
}

$conn = Connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get the posted data
    $color = trim(mysqli_real_escape_string($conn, $_POST['color']));
    $cost = floatval($_POST['cost']);
    $status = trim(mysqli_real_escape_string($conn, $_POST['status']));
    $stock = intval($_POST['stock']);
    $image_path = '';

    // Handle image upload
    if (isset($_FILES['flower_image']) && $_FILES['flower_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['flower_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = 'images/flowers/' . $new_filename;

            if (move_uploaded_file($_FILES['flower_image']['tmp_name'], $upload_path)) {
                $image_path = $upload_path;
            } else {
                header("Location: flower.php?error=Failed to upload image");
                exit();
            }
        } else {
            header("Location: flower.php?error=Invalid image format. Allowed: jpg, jpeg, png, webp");
            exit();
        }
    }

    if (!empty($color) && $cost > 0 && !empty($status) && $stock >= 0) {
        // Insert data into the database
        $stmt = $conn->prepare("INSERT INTO flower (color, cost_per_day, status, image, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssi", $color, $cost, $status, $image_path, $stock);

        if ($stmt->execute()) {
            header("Location: edit_flower.php?success=1");
            exit();
        } else {
            header("Location: flower.php?error=Failed to add flower");
            exit();
        }
        $stmt->close();
    } else {
        header("Location: flower.php?error=Please enter valid information!");
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Add New Flower</h2>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['error'])) { ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                        <?php } ?>

                        <form action="flower.php" method="post" enctype="multipart/form-data" name="myForm"
                            onsubmit="return validateForm()">
                            <div class="mb-3">
                                <label class="form-label">Color</label>
                                <input type="text" class="form-control" name="color" required
                                    placeholder="Enter flower color">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Cost (LE)</label>
                                <input type="number" class="form-control" name="cost" required min="0" step="0.01"
                                    placeholder="Enter flower cost">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Stock</label>
                                <input type="number" class="form-control" name="stock" required min="0"
                                    placeholder="Enter stock quantity">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Out of service">Out of service</option>
                                    <option value="Rented">Rented</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Flower Image</label>
                                <input type="file" class="form-control" name="flower_image" accept="image/*" required>
                                <small class="text-muted">Allowed formats: JPG, JPEG, PNG, WEBP</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Add Flower</button>
                                <a href="admin_page.php" class="btn btn-secondary">Back to Dashboard</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            var cost = document.forms["myForm"]["cost"].value;
            var stock = document.forms["myForm"]["stock"].value;
            if (cost <= 0) {
                alert("Cost must be greater than 0");
                return false;
            }
            if (stock < 0) {
                alert("Stock cannot be negative");
                return false;
            }
            return true;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>