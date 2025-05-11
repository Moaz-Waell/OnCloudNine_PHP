<?php
session_start();
include('../../php/config.php');

if (!isset($_SESSION['id'])) {
  header("Location: uniUserLogin.php");
  exit();
}

// Fetch user data including phone number
$stmt = $con->prepare("SELECT USERS_ID, USERS_Name, USERS_Attendance, USERS_Phnumber FROM Uni_users WHERE USERS_ID = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

if (!$userData) {
  header("Location: uniUserLogin.php");
  exit();
}

// Set cookies with user data (expires in 6 days)
setcookie('user_id', $userData['USERS_ID'], time() + 6 * 24 * 60 * 60, '/');
setcookie('username', $userData['USERS_Name'], time() + 6 * 24 * 60 * 60, '/');
setcookie('attendance', $userData['USERS_Attendance'], time() + 6 * 24 * 60 * 60, '/');
setcookie('phone', $userData['USERS_Phnumber'], time() + 6 * 24 * 60 * 60, '/');

$con->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Portal</title>
  <link rel="stylesheet" href="../../style/pages/aast/uniUserPortal.css" />
</head>

<body>
  <div class="student-portal">
    <header>
      <div class="logo-container">
        <img src="../../img/aast_imgs/AAST-LOGO-BLUE.png" alt="Student Portal" class="logo-img" />
        <h1>Student Portal</h1>
      </div>
      <nav>
        <ul class="nav-links">
          <li class="user-profile">
            <a href="#">
              <span class="user-icon">👤</span>
              <?php echo htmlspecialchars($userData['USERS_Name']); ?> -
              <?php echo htmlspecialchars($userData['USERS_ID']); ?>
              <span class="dropdown-arrow">▼</span>
            </a>
          </li>
          <li class="logout-btn">
            <a href="../../php/logoutUniUser.php">Logout</a>
          </li>
        </ul>
      </nav>
    </header>
    <div class="title-section">
      <h2>Average Attendance: <?php echo htmlspecialchars($userData['USERS_Attendance']); ?>%</h2>
    </div>
    <main>
      <div class="grid-container">
        <div class="card">
          <img src="https://placehold.co/300x200/9df5ff/333333?text=Student" alt="Student Results" />
          <h3>Student Results</h3>
        </div>
        <div class="card">
          <img src="https://placehold.co/300x200/ffffff/33aa33?text=Calendar" alt="Student Schedule" />
          <h3>Student Schedule</h3>
        </div>
        <div class="card">
          <a href="../user/home.php">
            <img src="../../img/logo/onCloudNine.svg" alt="On Cloud Nine" />
            <h3>On Cloud Nine</h3>
          </a>
        </div>
        <div class="card">
          <img src="https://placehold.co/300x200/e0f0ff/333333?text=Clinic" alt="Clinic Reservation" />
          <h3>Clinic Reservation</h3>
        </div>
        <div class="card">
          <img src="https://placehold.co/300x200/9df5ff/333333?text=Training" alt="Student Training" />
          <h3>Student Training</h3>
        </div>
        <div class="card">
          <img src="https://placehold.co/300x200/ffaa00/ffffff?text=Moodle" alt="Old Moodle" />
          <h3>Old Moodle</h3>
        </div>
        <div class="card">
          <img src="https://placehold.co/300x200/ffaa00/ffffff?text=Moodle" alt="New Moodle" />
          <h3>New Moodle</h3>
        </div>
        <div class="card">
          <img src="https://placehold.co/300x200/fff5f5/333333?text=Unofficial" alt="Unofficial Transcript" />
          <h3>Unofficial Transcript</h3>
        </div>
      </div>
    </main>
  </div>
</body>

</html>