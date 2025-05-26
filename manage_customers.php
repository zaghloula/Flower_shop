<?php
session_start();
@include 'config.php';

if (!isset($_SESSION['admin_name'])) {
    header("Location: login_form.php");
    exit;
}

// معالجة حذف العميل
if (isset($_POST['delete']) && isset($_POST['customer_id'])) {
    $customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $delete_query = "DELETE FROM user_form WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    if (mysqli_stmt_execute($stmt)) {
        // إعادة التوجيه بعد الحذف لتحديث الصفحة
        header("Location: manage_customers.php");
        exit;
    } else {
        $error = "Error deleting customer.";
    }
    mysqli_stmt_close($stmt);
}

// جلب بيانات العملاء مع الطلبات السابقة باستخدام الإيميل
$query = "SELECT u.id, u.name, u.email, 
          GROUP_CONCAT(o.order_id SEPARATOR ', ') as order_ids, 
          GROUP_CONCAT(o.order_date SEPARATOR ', ') as order_dates, 
          GROUP_CONCAT(o.details SEPARATOR ', ') as order_details 
          FROM user_form u 
          LEFT JOIN orders o ON u.email = o.customer_email 
          GROUP BY u.id, u.name, u.email";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* التصميم العام للصفحة */
        body {
            background: linear-gradient(135deg, #fff0f5, #ffffff);
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* حاوية الصفحة */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* العنوان */
        h2 {
            color: #d81b60;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
            font-family: 'Georgia', serif;
        }

        /* تصميم الجدول */
        .customer-table {
            margin-top: 20px;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background: #ff4081;
            color: #fff;
            font-weight: bold;
            padding: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .table td {
            padding: 15px;
            color: #333;
            border-bottom: 1px solid #f1f1f1;
        }

        .table tbody tr:nth-child(even) {
            background: #ffebee;
        }

        .table tbody tr:hover {
            background: #f8bbd0;
            transition: background 0.3s ease;
        }

        /* تصميم الأزرار */
        .btn-delete {
            background: #f44336;
            border: none;
            padding: 5px 10px;
            font-size: 0.9rem;
            border-radius: 20px;
            transition: all 0.3s ease;
            color: #fff;
        }

        .btn-delete:hover {
            background: #d32f2f;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: #f06292;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-primary:hover {
            background: #ec407a;
            transform: translateY(-2px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* تنسيق النصوص */
        td {
            vertical-align: middle;
            font-size: 0.95rem;
        }

        /* تحسين العرض على الشاشات الصغيرة */
        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 15px;
            }

            h2 {
                font-size: 1.8rem;
            }

            .table th, .table td {
                padding: 10px;
                font-size: 0.85rem;
            }

            .btn-delete, .btn-primary {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Customers</h2>
        <?php if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>
        <div class="table-responsive customer-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Previous Orders</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            $orders = !empty($row['order_ids']) ? "Order IDs: " . htmlspecialchars($row['order_ids']) . "<br>Date: " . htmlspecialchars($row['order_dates']) . "<br>Details: " . htmlspecialchars($row['order_details']) : "No orders";
                            echo "<td>" . $orders . "</td>";
                            echo "<td>";
                            echo "<form method='post' action='' onsubmit='return confirm(\"Are you sure you want to delete this customer?\");'>";
                            echo "<input type='hidden' name='customer_id' value='" . htmlspecialchars($row['id']) . "'>";
                            echo "<button type='submit' name='delete' class='btn-delete'>Delete</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No customers found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="text-center">
            <a href="admin_page.php" class="btn btn-primary mt-3">Back to Dashboard</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
mysqli_close($conn);
?>