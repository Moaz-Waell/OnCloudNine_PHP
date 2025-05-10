<?php
session_start();
require_once '../../php/config.php';

// Clear previous messages
unset($_SESSION['form_success']);
unset($_SESSION['form_error']);

// Check user authentication
if (!isset($_SESSION['user_id'])) {
  header("Location: ../../pages/aast/uniUserLogin.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$error = $_SESSION['form_error'] ?? '';
$success = $_SESSION['form_success'] ?? '';

// Fetch user data
$user_stmt = $con->prepare("SELECT USERS_Name, USERS_Phnumber, USERS_Attendance FROM USERS WHERE USERS_ID = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// Fetch all allergies
$allergies = [];
$allergy_stmt = $con->query("SELECT * FROM ALLERGY");
while ($row = $allergy_stmt->fetch_assoc()) {
  $allergies[] = $row;
}

// Fetch user's allergies
$user_allergies = [];
$user_allergy_stmt = $con->prepare("SELECT A.ALLERGY_ID FROM USER_ALLERGIES UA 
                                  JOIN ALLERGY A ON UA.ALLERGY_ID = A.ALLERGY_ID 
                                  WHERE UA.USERS_ID = ? AND UA.Has_Allergy = 'Yes'");
$user_allergy_stmt->bind_param("i", $user_id);
$user_allergy_stmt->execute();
$user_allergy_result = $user_allergy_stmt->get_result();
while ($row = $user_allergy_result->fetch_assoc()) {
  $user_allergies[] = $row['ALLERGY_ID'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
  $selected_allergies = $_POST['allergies'] ?? [];

  $con->begin_transaction();
  try {
    // Update phone number
    $update_phone = $con->prepare("UPDATE USERS SET USERS_Phnumber = ? WHERE USERS_ID = ?");
    $update_phone->bind_param("si", $phone, $user_id);
    if (!$update_phone->execute()) {
      throw new Exception("Failed to update phone number");
    }

    // Clear existing allergies
    $delete_stmt = $con->prepare("DELETE FROM USER_ALLERGIES WHERE USERS_ID = ?");
    $delete_stmt->bind_param("i", $user_id);
    if (!$delete_stmt->execute()) {
      throw new Exception("Failed to clear existing allergies");
    }

    // Insert new selections
    if (!empty($selected_allergies)) {
      $insert_stmt = $con->prepare("INSERT INTO USER_ALLERGIES (USERS_ID, ALLERGY_ID, Has_Allergy) VALUES (?, ?, 'Yes')");
      foreach ($selected_allergies as $allergy_id) {
        $insert_stmt->bind_param("ii", $user_id, $allergy_id);
        if (!$insert_stmt->execute()) {
          throw new Exception("Failed to insert allergy: $allergy_id");
        }
      }
    } else {
      // Insert "No Allergies"
      $none_stmt = $con->prepare("INSERT INTO USER_ALLERGIES (USERS_ID, ALLERGY_ID, Has_Allergy)
                                      SELECT ?, ALLERGY_ID, 'No' FROM ALLERGY WHERE ALLERGY_Name = 'No Allergies'");
      $none_stmt->bind_param("i", $user_id);
      if (!$none_stmt->execute()) {
        throw new Exception("Failed to set 'No Allergies'");
      }
    }

    $con->commit();
    $_SESSION['form_success'] = "Profile updated successfully!";
  } catch (Exception $e) {
    $con->rollback();
    $_SESSION['form_error'] = "Error: " . $e->getMessage();
  }

  header("Location: profile.php");
  exit();
}

// Clear session messages after displaying
unset($_SESSION['form_success']);
unset($_SESSION['form_error']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Profile</title>
  <link rel="stylesheet" href="../../style/pages/user/profile.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <div class="container">
    <?php include('../../components/sideNav.php'); ?>
    <main class="main-content">
      <div class="profile-card">
        <form method="POST" action="profile.php">
          <div class="profile-header">
            <div class="profile-avatar">
              <img src="../../img/icons/avatar.png" alt="User Avatar" class="avatar-image">
              <i class="fas fa-user"></i>
            </div>
            <div class="user-details">
              <h2 class="user-name"><?= htmlspecialchars($user['USERS_Name']) ?></h2>
              <p class="user-id">ID: <?= htmlspecialchars($user_id) ?></p>
              <div class="input-group">
                <label for="phone-input" class="input-label">Phone:</label>
                <input type="tel" id="phone-input" name="phone" class="input-field"
                  value="<?= htmlspecialchars($user['USERS_Phnumber'] ?? '') ?>">
              </div>
            </div>
          </div>

          <div class="allergies-section">
            <h3 class="section-title">Allergies</h3>
            <div class="allergy-group">
              <?php foreach ($allergies as $allergy):
                if ($allergy['ALLERGY_Name'] === 'No Allergies')
                  continue; ?>
                <label class="checkbox-container">
                  <input type="checkbox" name="allergies[]" value="<?= $allergy['ALLERGY_ID'] ?>"
                    <?= in_array($allergy['ALLERGY_ID'], $user_allergies) ? 'checked' : '' ?>>
                  <span class="checkmark"></span>
                  <?= htmlspecialchars($allergy['ALLERGY_Name']) ?>
                </label>
              <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn--full save-button">Save Changes</button>
          </div>
        </form>
      </div>

      <?php if ($error): ?>
        <div class="error-message"><?= $error ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="success-message"><?= $success ?></div>
      <?php endif; ?>
    </main>
  </div>

  <script>
    // Auto-hide messages after 5 seconds
    setTimeout(() => {
      document.querySelectorAll('.success-message, .error-message').forEach(el => {
        el.style.display = 'none';
      });
    }, 5000);
  </script>
</body>

</html>