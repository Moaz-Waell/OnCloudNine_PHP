<?php
session_start();
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
} else {
    $error = '';
}
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AASTMT Student Portal</title>
  <link rel="stylesheet" href="../../style/pages/aast/uniUserLogin.css" />
</head>

<body>
  <div class="portal-container">
    <div class="background-section">
      <div class="overlay">
        <h1>AASTMT Student Portal</h1>
        <p>
          AASTMT Student Portal is an online gateway where students can log in
          to access important program information. Student Portal contain
          information on courses, transcripts, timetables, exam schedules and
          department contact numbers.
        </p>
      </div>
    </div>
    <div class="uni-login-section">
      <div class="logo-container">
        <img src="../../img/aast_imgs/AAST-LOGO-BLUE.png" alt="College Logo" class="college-logo" />
        <h2>Student Portal</h2>
      </div>

      <div class="form-section">
        <h3>Registration</h3>
        <button class="register-btn">Open Registration</button>

        <h3>Login</h3>

        <!-- Error Message Display -->
        <?php if (!empty($error)): ?>
          <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>

        <form action="../../php/user_login_validation.php" method="post">
          <input type="text" name="ID" placeholder="Registration Number" required />
          <input type="password" name="password" placeholder="Pin Code" required />

          <div class="remember-me">
            <input type="checkbox" id="remember" />
            <label for="remember">Remember Me</label>
          </div>

          <button type="submit" class="login-btn">Login</button>
        </form>

        <p class="forgot-password">
          Can't log in or Forgot Password? Click <a href="#">here</a> to send
          your password.
        </p>
      </div>
    </div>
  </div>
</body>

</html>