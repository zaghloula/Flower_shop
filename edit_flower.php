<?php
session_start();
include("connection.php");
$con = Connect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Flower</title>
    <link rel="stylesheet" href="flower.css">
</head>

<body>
    <div>
        <nav>
            <a href="logout.php">LOGOUT</a>
        </nav><br>
    </div>

    <?php
    $query = "SELECT flower_id, color, cost_per_day, status FROM flower";
    $result = mysqli_query($con, $query);

    echo "<table border='1px'>";

    if ($result->num_rows > 0) {
        echo "<tr>
                <th>ID</th>
                <th>Color</th>
                <th>Cost</th>
                <th>Status</th>
                <th>Edit</th>
              </tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo "<td>{$row['flower_id']}</td>";
            echo "<td>{$row['color']}</td>";
            echo "<td>{$row['cost_per_day']}</td>";
            echo "<td>{$row['status']}</td>";
            echo '<td>
                    <form method="GET" action="edit_flower_view.php">
                        <input type="hidden" name="flower_id" value="' . $row['flower_id'] . '">
                        <input type="submit" name="update" value="Update">
                    </form>
                  </td>';
            echo '</tr>';
        }
        echo "</table>";
    } else {
        echo "No search results";
    }

    mysqli_close($con);
    ?>
</body>
</html>
