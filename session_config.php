<?php
  // بدء الجلسة مع إعدادات آمنة
  session_start([
      'cookie_secure' => false, // غيرها لـ true في البرودكشن مع HTTPS
      'cookie_httponly' => true, // منع الوصول عبر JavaScript
      'cookie_samesite' => 'Strict', // حماية من CSRF
      'use_strict_mode' => true // وضع صارم للجلسات
  ]);

  // تجديد معرف الجلسة بعد تسجيل الدخول لمنع Session Fixation
  if (isset($_SESSION['user_id'])) {
      session_regenerate_id(true);
  }
  ?>