<?php
@include 'config.php';

// تضمين ملف إعداد الجلسات الآمنة
require_once 'session_config.php';

session_unset();
session_destroy();

header('location:login_form.php');
exit;
?>