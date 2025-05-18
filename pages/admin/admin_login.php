<?php
session_start();
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <link rel="stylesheet" href="../../style/Components/login.css" />
  <script defer src="../../js/login_pincode_eye.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body>
  <main>
    <section class="section-login">
      <div class="container">
        <div class="grid grid-2-cols">
          <img src="../../img/logo/onCloudNine.svg" alt="Login illustration" class="login-image" />
          <div class="form-container">
            <h2 class="heading-secondary">Admin Login</h2>
            <?php if (!empty($error)): ?>
              <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
              </div>
            <?php endif; ?>
            <form action="../../php/admin_login_validation.php" method="post" class="login-form">
              <div class="input-group">
                <input type="text" id="ID" name="ID" placeholder="Admin ID" required />
              </div>
              <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Enter your PIN Code" required />
                <i class="password-toggle fas fa-eye-slash"></i>
              </div>
              <button type="submit" class="btn btn-primary">Login</button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>
</body>

</html>