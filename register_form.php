<?php
@include 'config.php';

// تضمين ملف إعداد الجلسات الآمنة
require_once 'session_config.php';

// إنشاء رمز CSRF إذا لم يكن موجودًا
if (!isset($_SESSION['csrf_token'])) {
   $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['submit'])) {
   // التحقق من رمز CSRF
   if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      $error[] = 'Invalid CSRF token!';
   } else {
      // Verify reCAPTCHA
      $secretKey = "6LfNwjgrAAAAAE8dBehcl7Bjd0zYPopNW-8Z_FtP"; // Your Secret Key
      $response = $_POST['g-recaptcha-response'];
      $remoteIp = $_SERVER['REMOTE_ADDR'];
      $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$response&remoteip=$remoteIp";
      $verify = json_decode(file_get_contents($url));

      if ($verify->success == false) {
         $error[] = 'Please complete the CAPTCHA!';
      } else {
         // Sanitize and validate inputs
         $name = mysqli_real_escape_string($conn, trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING)));
         if (!preg_match("/^[a-zA-Z ]{2,50}$/", $name)) {
            $error[] = 'Name must contain only letters and spaces, 2-50 characters!';
         }

         $email = mysqli_real_escape_string($conn, trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)));
         if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error[] = 'Invalid email format!';
         }

         $pass = trim($_POST['password']);
         $cpass = trim($_POST['cpassword']);

         // Check password strength
         if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $pass)) {
            $error[] = 'Password must be at least 8 characters long, with an uppercase letter, lowercase letter, number, and special character!';
         } else {
            // Hash password using password_hash
            $pass_hashed = password_hash($pass, PASSWORD_DEFAULT);

            $select = "SELECT * FROM user_form WHERE email = '$email'";
            $result = mysqli_query($conn, $select);

            if (mysqli_num_rows($result) > 0) {
               $error[] = 'User already exists!';
            } else {
               if ($pass != $cpass) {
                  $error[] = 'Passwords do not match!';
               } else {
                  $insert = "INSERT INTO user_form(name, email, password) VALUES('$name', '$email', '$pass_hashed')";
                  if (mysqli_query($conn, $insert)) {
                     header('location:login_form.php');
                     exit;
                  } else {
                     $error[] = 'Registration failed! Database error.';
                  }
               }
            }
         }
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register Form</title>
   <link rel="stylesheet" href="style.css">
   <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<div class="form-container">
   <form action="" method="post" onsubmit="return validatePassword()">
      <h3>Register Now</h3>
      <?php
      if (isset($error)) {
         foreach ($error as $error_msg) {
            echo '<span class="error-msg">' . htmlspecialchars($error_msg, ENT_QUOTES, 'UTF-8') . '</span>';
         }
      }
      ?>
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
      <input type="text" name="name" required placeholder="Enter your name">
      <input type="email" name="email" required placeholder="Enter your email">
      <input type="password" name="password" id="password" required placeholder="Enter your password">
      <input type="password" name="cpassword" required placeholder="Confirm your password">
      <div class="g-recaptcha" data-sitekey="6LfNwjgrAAAAAM9MC7ITB3KAVbUd8zd4gafAevCt"></div>
      <input type="submit" name="submit" value="Register Now" class="form-btn">
      <p>Already have an account? <a href="login_form.php">Login now</a></p>
   </form>
</div>

<script>
function validatePassword() {
   let password = document.getElementById("password").value;
   let regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
   if (!regex.test(password)) {
      alert("Password must be at least 8 characters long, with an uppercase letter, lowercase letter, number, and special character!");
      return false;
   }
   return true;
}
</script>

</body>
</html>