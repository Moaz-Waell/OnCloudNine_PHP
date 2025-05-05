<?php
include('../../php/config.php');

// Check for category ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: home.php");
  exit();
}

$category_id = intval($_GET['id']);

// Fetch category details
$stmt = $con->prepare("SELECT CATEGORY_Name, CATEGORY_Icon FROM category WHERE CATEGORY_ID = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
  header("Location: home.php");
  exit();
}

$category = $result->fetch_assoc();
$category_name = htmlspecialchars($category['CATEGORY_Name']);

// Fetch meals in the category
$stmt_meals = $con->prepare("SELECT * FROM meal WHERE CATEGORY_ID = ?");
$stmt_meals->bind_param("i", $category_id);
$stmt_meals->execute();
$meals_result = $stmt_meals->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo $category_name; ?> Menu</title>
  <link rel="stylesheet" href="../../style/pages/user/categoryMeals.css" />
</head>

<body>
  <!-- Sticky Back Button -->
  <a href="home.php" class="back-button" aria-label="Back to Main Menu">
    <span class="back-arrow">←</span>
  </a>

  <!-- Category Section -->
  <div class="menu-category">
    <h1 class="category-title">
      <?php echo $category_name; ?>
    </h1>
    <div class="menu-grid">
      <?php
      if ($meals_result->num_rows > 0) {
        while ($meal = $meals_result->fetch_assoc()) {
          $meal_image = htmlspecialchars($meal['MEAL_Icon']);
          $meal_name = htmlspecialchars($meal['MEAL_Name']);
          // $meal_desc = htmlspecialchars($meal['MEAL_Description']);
          $meal_price = number_format($meal['MEAL_Price'], 2);
          ?>
          <div class="menu-item">
            <div class="menu-image">
              <img src="../../img/meals/<?php echo strtolower($category_name); ?>/<?php echo $meal_image; ?>"
                alt="<?php echo $meal_name; ?>">
            </div>
            <div class="menu-details">
              <h3><?php echo $meal_name; ?></h3>
              <p>testing</p>
              <span class="price">$<?php echo $meal_price; ?></span>
              <a href="viewMealDetails.php?id=<?php echo $meal['MEAL_ID']; ?>" class="view-details">View Details</a>
            </div>
          </div>
          <?php
        }
      } else {
        echo "<p>No meals found in this category.</p>";
      }
      ?>
    </div>
  </div>
</body>

</html>