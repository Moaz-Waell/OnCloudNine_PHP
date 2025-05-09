<?php
session_start();
include('../../php/config.php');

// Check if cookies exist
if (!isset($_COOKIE['user_id']) || !isset($_COOKIE['username']) || !isset($_COOKIE['attendance']) || !isset($_COOKIE['phone'])) {
  header("Location: ../../pages/aast/uniUserLogin.php");
  exit();
}

$user_id = $_COOKIE['user_id'];
$username = $_COOKIE['username'];
$attendance = $_COOKIE['attendance'];
$phone = $_COOKIE['phone'];

$_SESSION['user_id'] = $user_id;
$_SESSION['username'] = $username;
$_SESSION['attendance'] = $attendance;
$_SESSION['phone'] = $phone;

// Insert user into USERS table if not exists
$checkUser = $con->prepare("SELECT USERS_ID FROM USERS WHERE USERS_ID = ?");
$checkUser->bind_param("i", $user_id);
$checkUser->execute();
if ($checkUser->get_result()->num_rows == 0) {
  $insertUser = $con->prepare("INSERT INTO USERS (USERS_ID, USERS_Phnumber, USERS_Name, USERS_Attendance) VALUES (?, ?, ?, ?)");
  $insertUser->bind_param("iisi", $user_id, $phone, $username, $attendance);
  $insertUser->execute();
  $insertUser->close();
}
$checkUser->close();

// Check if allergy form submitted
$allergyCheck = $con->prepare("SELECT USERS_ID FROM USER_ALLERGIES WHERE USERS_ID = ?");
$allergyCheck->bind_param("i", $user_id);
$allergyCheck->execute();
$hasSubmitted = $allergyCheck->get_result()->num_rows > 0;
$allergyCheck->close();

// Fetch allergies from database
$allergies = [];
$allergyResult = $con->query("SELECT * FROM ALLERGY");
if ($allergyResult) {
  while ($row = $allergyResult->fetch_assoc()) {
    if (trim($row['ALLERGY_Name']) !== 'No Allergies') {
      $allergies[] = $row;
    }
  }
}

$con->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OCN Food Dashboard</title>
  <link rel="stylesheet" href="../../style/pages/user/home.css" />
  <link rel="stylesheet" href="../../style/pages/user/allergies.css">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <?php if (!$hasSubmitted): ?>
    <div class="allergy-overlay">
      <div class="basic-container">
        <form method="POST" action="../../php/submit_allergies.php" class="form-container">
          <h1 class="h1 heading-primary head-allergies">Allergies Form</h1>
          <div class="checkbox-group label">
            <?php foreach ($allergies as $allergy): ?>
              <label class="checkbox-group-hover">
                <input type="checkbox" name="allergies[]" value="<?php echo $allergy['ALLERGY_ID']; ?>">
                <?php echo htmlspecialchars($allergy['ALLERGY_Name']); ?>
              </label>
            <?php endforeach; ?>
          </div>
          <div class="button-group">
            <button type="submit" name="submit" class="btn btn--full">Submit</button>
            <button type="submit" name="no_allergies" class="btn btn--full">No Allergies</button>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>
  <div class="container">
    <!-- Sidebar -->
    <?php include('../../components/sideNav.html'); ?>
    <!-- Main Content -->
    <main class="main-content">
      <!-- Header -->
      <header class="header">
        <div class="header-title">
          <h1>Menu</h1>
        </div>

        <div class="search-bar">
          <i class="fas fa-search"></i>
          <input type="search" placeholder="Search" />
        </div>

        <div class="header-actions">
          <div class="user-greeting">
            <p>Hi, <?php echo htmlspecialchars($username); ?></p>
          </div>
        </div>
      </header>
      <!-- Category Section -->
      <section class="category-section">
        <div class="section-header">
          <h2>Category</h2>
        </div>

        <div class="category-flex">
          <?php
          include('../../php/config.php');
          $query = "SELECT * FROM category";
          $result = $con->query($query);
          if ($result->num_rows > 0) {
            while ($category = $result->fetch_assoc()) {
              $image_path = "../../img/category/" . $category['CATEGORY_Icon'];
              ?>
              <div class="category-item">
                <a href="categoryMeals.php?id=<?php echo $category['CATEGORY_ID']; ?>">
                  <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($category['CATEGORY_Name']); ?>">
                  <p><?php echo htmlspecialchars($category['CATEGORY_Name']); ?></p>
                </a>
              </div>
              <?php
            }
          } else {
            echo "<p>No categories found</p>";
          }
          ?>
        </div>
      </section>
      <!-- Best Seller Section -->
      <section class="meal-section">
        <div class="section-header">
          <h2>Best Seller</h2>
        </div>

        <div class="menu-grid">
          <div class="menu-item">
            <div class="menu-image">
              <img src="https://images.unsplash.com/photo-1604382354936-07c5d9983bd3" alt="Pizza" />
            </div>
            <div class="menu-details">
              <h3>Pepperoni Pizza</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
              <span class="price">$5.59</span>
              <a href="#" class="view-details">View Details</a>
            </div>
          </div>

          <div class="menu-item">
            <div class="menu-image">
              <img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624" alt="Ramen" />
            </div>
            <div class="menu-details">
              <h3>Japanese Ramen</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
              <span class="price">$5.59</span>
            </div>
          </div>

          <div class="menu-item">
            <div class="menu-image">
              <img src="https://images.unsplash.com/photo-1603133872878-684f208fb84b" alt="Fried Rice" />
            </div>
            <div class="menu-details">
              <h3>Fried Rice</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
              <span class="price">$5.59</span>
            </div>
          </div>

          <div class="menu-item">
            <div class="menu-image">
              <img src="https://images.unsplash.com/photo-1604382354936-07c5d9983bd3" alt="Vegan Pizza" />
            </div>
            <div class="menu-details">
              <h3>Vegan Pizza</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
              <span class="price">$5.59</span>
            </div>
          </div>

          <div class="menu-item">
            <div class="menu-image">
              <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd" alt="Beef Burger" />
            </div>
            <div class="menu-details">
              <h3>Beef Burger</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
              <span class="price">$5.59</span>
            </div>
          </div>

          <div class="menu-item">
            <div class="menu-image">
              <img src="https://images.unsplash.com/photo-1565299507177-b0ac66763828" alt="Fish Burger" />
            </div>
            <div class="menu-details">
              <h3>Fish Burger</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
              <span class="price">$5.59</span>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</body>

</html>