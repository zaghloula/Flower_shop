<?php
   @include 'config.php';
   require_once 'session_config.php';

   // إنشاء رمز CSRF إذا مش موجود
   if (!isset($_SESSION['csrf_token'])) {
       $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
   }

   // Rate limiting based on IP
   $ip_address = $_SERVER['REMOTE_ADDR'];
   $max_attempts = 5;
   $lockout_time = 900;

   if (!isset($_SESSION['login_attempts'][$ip_address])) {
       $_SESSION['login_attempts'][$ip_address] = ['count' => 0, 'last_attempt' => time()];
   } else {
       $current_time = time();
       if (($current_time - $_SESSION['login_attempts'][$ip_address]['last_attempt']) >= $lockout_time) {
           $_SESSION['login_attempts'][$ip_address] = ['count' => 0, 'last_attempt' => $current_time];
       }
   }

   if (isset($_POST['submit'])) {
       $error = [];

       // فحص CSRF
       if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
           $error[] = 'invalid csrf token!';
       } else {
           $current_time = time();
           if ($_SESSION['login_attempts'][$ip_address]['count'] >= $max_attempts && ($current_time - $_SESSION['login_attempts'][$ip_address]['last_attempt']) < $lockout_time) {
               $error[] = 'too many login attempts! please try again after 15 minutes.';
           } else {
               // reCAPTCHA
               $secretKey = "6LfNwjgrAAAAAE8dBehcl7Bjd0zYPopNW-8Z_FtP"; // secret key
               $response = $_POST['g-recaptcha-response'];
               $remoteIp = $_SERVER['REMOTE_ADDR'];
               $verify = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$response&remoteip=$remoteIp"));

               if (!$verify->success) {
                   $error[] = 'please complete the captcha!';
               } else {
                   // تنظيف الإيميل (بدون تحويل إلى أحرف صغيرة)
                   $email = mysqli_real_escape_string($conn, trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)));
                   $pass = trim($_POST['password']);

                   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                       $error[] = 'invalid email format!';
                   } else {
                       // ======== تحقق من admin ========
                       $stmt_admin = mysqli_prepare($conn, "SELECT email, password FROM admin WHERE email = ?");
                       mysqli_stmt_bind_param($stmt_admin, "s", $email);
                       mysqli_stmt_execute($stmt_admin);
                       $result_admin = mysqli_stmt_get_result($stmt_admin);
                       $admin = mysqli_fetch_assoc($result_admin);
                       mysqli_stmt_close($stmt_admin);

                       if ($admin && password_verify($pass, $admin['password'])) {
                           $_SESSION['admin_name'] = $admin['email'];
                           $_SESSION['login_attempts'][$ip_address] = ['count' => 0, 'last_attempt' => $current_time];
                           header("Location: admin_page.php");
                           exit;
                       }

                       // ======== تحقق من user ========
                       $stmt_user = mysqli_prepare($conn, "SELECT id, name, password FROM user_form WHERE email = ?");
                       mysqli_stmt_bind_param($stmt_user, "s", $email);
                       mysqli_stmt_execute($stmt_user);
                       $result_user = mysqli_stmt_get_result($stmt_user);
                       $user = mysqli_fetch_assoc($result_user);
                       mysqli_stmt_close($stmt_user);

                       if ($user && password_verify($pass, $user['password'])) {
                           $_SESSION['user_id'] = $user['id'];
                           $_SESSION['user_name'] = $user['name'];
                           $_SESSION['login_attempts'][$ip_address] = ['count' => 0, 'last_attempt' => $current_time];
                           header("Location: index.php");
                           exit;
                       }

                       // لو الاثنين غلط
                       $_SESSION['login_attempts'][$ip_address]['count']++;
                       $_SESSION['login_attempts'][$ip_address]['last_attempt'] = $current_time;
                       $error[] = 'incorrect email or password!';
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
       <title>login form</title>
       <link rel="stylesheet" href="style.css">
       <script src="https://www.google.com/recaptcha/api.js" async defer></script>
   </head>
   <body>
   <div class="form-container">
       <form action="" method="post">
           <h3>login now</h3>
           <?php
           if (!empty($error)) {
               foreach ($error as $err) {
                   echo '<span class="error-msg">' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</span>';
               }
           }
           ?>
           <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
           <input type="email" name="email" required placeholder="enter your email">
           <input type="password" name="password" required placeholder="enter your password">
           <div class="g-recaptcha" data-sitekey="6LfNwjgrAAAAAM9MC7ITB3KAVbUd8zd4gafAevCt"></div>
           <input type="submit" name="submit" value="login now" class="form-btn">
           <p>don't have an account? <a href="register_form.php">register now</a></p>
       </form>
   </div>
   </body>
   </html>