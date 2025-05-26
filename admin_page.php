<?php
// تضمين ملف الإعدادات
@include 'config.php';

// تضمين ملف إعداد الجلسات الآمنة
require_once 'session_config.php';

// التحقق من وجود جلسة المشرف
if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <style>
        .bg {
            background-color: black !important;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="content text-center">
            <h3>Hi, <span>Admin</span></h3>
            <h1>Welcome <span><?php echo htmlspecialchars(explode('@', $_SESSION['admin_name'])[0], ENT_QUOTES, 'UTF-8'); ?></span></h1>
            <p>This is an admin page</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="flower.php" class="bg btn btn-primary">Add Flower</a>
                <a href="edit_flower.php" class="bg btn btn-primary">Review Flower</a>
                <a href="manage_customers.php" class="bg btn btn-primary">Manage Customers</a>
                <a href="logout.php" class=" bg btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>