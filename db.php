<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "flower"; // ← غيره حسب اسم قاعدة البيانات

$con = new mysqli($host, $user, $password, $database);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>
