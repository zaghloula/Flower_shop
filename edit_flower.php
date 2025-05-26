<?php
session_start();
include("connection.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit;
}

$con = Connect();

// Handle edit request
if (isset($_POST['edit']) && isset($_POST['flower_id'])) {
    $flower_id = mysqli_real_escape_string($con, $_POST['flower_id']);
    $color = mysqli_real_escape_string($con, $_POST['color']);
    $cost = floatval($_POST['cost']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $stock = intval($_POST['stock']);
    $image_path = '';

    // Handle image upload if new image is provided
    if (isset($_FILES['flower_image']) && $_FILES['flower_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['flower_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = 'images/flowers/' . $new_filename;

            if (move_uploaded_file($_FILES['flower_image']['tmp_name'], $upload_path)) {
                $image_path = $upload_path;
                // Update with new image
                $update_query = "UPDATE flower SET color = ?, cost_per_day = ?, status = ?, stock = ?, image = ? WHERE flower_id = ?";
                $stmt = mysqli_prepare($con, $update_query);
                mysqli_stmt_bind_param($stmt, "sdsssi", $color, $cost, $status, $stock, $image_path, $flower_id);
            }
        }
    } else {
        // Update without changing image
        $update_query = "UPDATE flower SET color = ?, cost_per_day = ?, status = ?, stock = ? WHERE flower_id = ?";
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "sdssi", $color, $cost, $status, $stock, $flower_id);
    }

    if (isset($stmt) && mysqli_stmt_execute($stmt)) {
        header("Location: edit_flower.php?success=1");
        exit();
    }
    mysqli_stmt_close($stmt);
}

// Handle delete request
if (isset($_POST['delete']) && isset($_POST['flower_id'])) {
    $flower_id = mysqli_real_escape_string($con, $_POST['flower_id']);

    // Get image path before deleting
    $image_query = "SELECT image FROM flower WHERE flower_id = ?";
    $stmt = mysqli_prepare($con, $image_query);
    mysqli_stmt_bind_param($stmt, "i", $flower_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    $delete_query = "DELETE FROM flower WHERE flower_id = ?";
    $stmt = mysqli_prepare($con, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $flower_id);

    if (mysqli_stmt_execute($stmt)) {
        // Delete image file if exists
        if (!empty($row['image']) && file_exists($row['image'])) {
            unlink($row['image']);
        }
        header("Location: edit_flower.php?success=2");
        exit();
    }
    mysqli_stmt_close($stmt);
}

// Fetch flower data
$query = "SELECT flower_id, color, cost_per_day, status, image, stock FROM flower";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Flowers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h2>Manage Flowers</h2>
        </div>
        <div class="card-body">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_GET['success'] == 1 ? "Flower updated successfully!" : "Flower deleted successfully!"; ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Color</th>
                            <th>Cost (LE)</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['flower_id']) ?></td>
                                <td>
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="<?= htmlspecialchars($row['image']) ?>" style="max-width: 100px;">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td><input type="text" class="form-control" id="color_<?= $row['flower_id'] ?>" value="<?= htmlspecialchars($row['color']) ?>"></td>
                                <td><input type="number" class="form-control" id="cost_<?= $row['flower_id'] ?>" value="<?= htmlspecialchars($row['cost_per_day']) ?>" min="0" step="0.01"></td>
                                <td><input type="number" class="form-control" id="stock_<?= $row['flower_id'] ?>" value="<?= htmlspecialchars($row['stock']) ?>" min="0"></td>
                                <td>
                                    <select class="form-control" id="status_<?= $row['flower_id'] ?>">
                                        <option value="Active" <?= $row['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                                        <option value="Out of service" <?= $row['status'] == 'Out of service' ? 'selected' : '' ?>>Out of service</option>
                                        <option value="Rented" <?= $row['status'] == 'Rented' ? 'selected' : '' ?>>Rented</option>
                                    </select>
                                </td>
                                <td>
                                    <!-- ✅ تم إضافة enctype هنا -->
                                    <form method="post" enctype="multipart/form-data" class="d-inline"
                                          onsubmit="return updateFlower(this, <?= $row['flower_id'] ?>)">
                                        <input type="hidden" name="flower_id" value="<?= $row['flower_id'] ?>">
                                        <input type="file" name="flower_image" class="d-none" id="image_<?= $row['flower_id'] ?>" accept="image/*">
                                        <button type="button" class="btn btn-secondary btn-sm"
                                                onclick="document.getElementById('image_<?= $row['flower_id'] ?>').click()">
                                            Change Image
                                        </button>
                                        <button type="submit" name="edit" class="btn btn-primary btn-sm">Update</button>
                                    </form>
                                    <form method="post" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this flower?');">
                                        <input type="hidden" name="flower_id" value="<?= $row['flower_id'] ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center">No flowers found</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-3">
                <a href="flower.php" class="btn btn-success">Add New Flower</a>
                <a href="admin_page.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<script>
function updateFlower(form, flowerId) {
    const color = document.getElementById('color_' + flowerId).value;
    const cost = document.getElementById('cost_' + flowerId).value;
    const status = document.getElementById('status_' + flowerId).value;
    const stock = document.getElementById('stock_' + flowerId).value;

    form.appendChild(createHiddenInput('color', color));
    form.appendChild(createHiddenInput('cost', cost));
    form.appendChild(createHiddenInput('status', status));
    form.appendChild(createHiddenInput('stock', stock));

    return true;
}

function createHiddenInput(name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    return input;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php mysqli_close($con); ?>
