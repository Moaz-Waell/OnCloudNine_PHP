<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: user_login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OCN Food Dashboard</title>
  <link rel="stylesheet" href="../style/pages/home.css" />
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <div class="container">
    <!-- Sidebar -->
    <?php include('../components/nav.html'); ?>
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
            <p>Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
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
          include('../php/config.php');
          $query = "SELECT * FROM category";
          $result = $con->query($query);
          if ($result->num_rows > 0) {
            while ($category = $result->fetch_assoc()) {
              $image_path = "../img/category/" . $category['C_Icon'];
              ?>
              <div class="category-item">
                <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($category['C_Name']); ?>">
                <p><?php echo htmlspecialchars($category['C_Name']); ?></p>
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