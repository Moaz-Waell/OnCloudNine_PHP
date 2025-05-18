<?php
session_start();
include('../../php/config.php');

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['add_meal'])) {
    // Add new meal
    $meal_name = $_POST['meal_name'];
    $price = $_POST['price'];
    $category_id = $_POST['category'];
    $image = $_POST['image'];
    $ingredients = $_POST['ingredients'] ?? [];

    // Insert meal
    $stmt = $con->prepare("INSERT INTO MEAL (MEAL_Name, MEAL_Description, MEAL_Price, MEAL_Icon, CATEGORY_ID) 
                             VALUES (?, '', ?, ?, ?)");
    $stmt->bind_param("sdss", $meal_name, $price, $image, $category_id);
    $stmt->execute();
    $meal_id = $stmt->insert_id;

    // Insert ingredients
    if (!empty($ingredients)) {
      $ingredient_stmt = $con->prepare("INSERT INTO MEAL_INGREDIENTS (MEAL_ID, INGREDIENT_ID) VALUES (?, ?)");
      foreach ($ingredients as $ingredient_id) {
        $ingredient_stmt->bind_param("ii", $meal_id, $ingredient_id);
        $ingredient_stmt->execute();
      }
    }
  } elseif (isset($_POST['update_meal'])) {
    // Update existing meal
    $meal_id = $_POST['meal_id'];
    $meal_name = $_POST['meal_name'];
    $price = $_POST['price'];
    $category_id = $_POST['category'];
    $image = $_POST['image'];
    $ingredients = $_POST['ingredients'] ?? [];

    // Update meal
    $stmt = $con->prepare("UPDATE MEAL SET 
                            MEAL_Name = ?, 
                            MEAL_Price = ?, 
                            MEAL_Icon = ?, 
                            CATEGORY_ID = ?
                            WHERE MEAL_ID = ?");
    $stmt->bind_param("sdssi", $meal_name, $price, $image, $category_id, $meal_id);
    $stmt->execute();

    // Update ingredients
    $con->query("DELETE FROM MEAL_INGREDIENTS WHERE MEAL_ID = $meal_id");
    if (!empty($ingredients)) {
      $ingredient_stmt = $con->prepare("INSERT INTO MEAL_INGREDIENTS (MEAL_ID, INGREDIENT_ID) VALUES (?, ?)");
      foreach ($ingredients as $ingredient_id) {
        $ingredient_stmt->bind_param("ii", $meal_id, $ingredient_id);
        $ingredient_stmt->execute();
      }
    }
  }
}

// Handle delete action
if (isset($_GET['delete'])) {
  $meal_id = intval($_GET['delete']);
  $con->query("DELETE FROM MEAL_INGREDIENTS WHERE MEAL_ID = $meal_id");
  $con->query("DELETE FROM MEAL WHERE MEAL_ID = $meal_id");
}

// Get all meals with categories
$meals = $con->query("
    SELECT m.*, c.CATEGORY_Name 
    FROM MEAL m
    JOIN CATEGORY c ON m.CATEGORY_ID = c.CATEGORY_ID
    ORDER BY c.CATEGORY_Name
")->fetch_all(MYSQLI_ASSOC);

// Get all categories
$categories = $con->query("SELECT * FROM CATEGORY")->fetch_all(MYSQLI_ASSOC);

// Get all ingredients
$ingredients = $con->query("SELECT * FROM INGREDIENTS")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Meal Management</title>
  <link rel="stylesheet" href="../../style/pages/admin/CRUD_Meal.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script>
    // Dropdown functionality
    document.addEventListener('DOMContentLoaded', function () {
      const checkLists = document.querySelectorAll('.dropdown-check-list');
      checkLists.forEach(list => {
        list.addEventListener('click', function (evt) {
          this.classList.toggle('visible');
          evt.stopPropagation();
        });
      });

      window.addEventListener('click', function () {
        checkLists.forEach(list => list.classList.remove('visible'));
      });
    });
  </script>
</head>

<body>
  <div class="container">
    <?php include('../../components/admin_sideNav.php'); ?>

    <main class="main-content">
      <div class="category-container">
        <div class="category-header">
          <h2>Meals</h2>
          <a href="#addMealForm" class="CRUD-btn add-meal">Add Meal</a>
        </div>

        <table class="category-table">
          <thead>
            <tr>
              <th>Icon</th>
              <th>Meal Name</th>
              <th>Price</th>
              <th>Category</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($meals as $meal): ?>
              <tr>
                <td>
                  <img src="../../img/meals/<?= strtolower($meal['CATEGORY_Name']) ?>/<?= $meal['MEAL_Icon'] ?>"
                    alt="<?= htmlspecialchars($meal['MEAL_Name']) ?>" class="icon-img">
                </td>
                <td><?= htmlspecialchars($meal['MEAL_Name']) ?></td>
                <td>$<?= number_format($meal['MEAL_Price'], 2) ?></td>
                <td><?= htmlspecialchars($meal['CATEGORY_Name']) ?></td>
                <td>
                  <a href="?delete=<?= $meal['MEAL_ID'] ?>" class="remove_btn">Remove</a>
                  <a href="#updateMealForm-<?= $meal['MEAL_ID'] ?>" class="CRUD-btn">Update</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Add Meal Popup -->
      <div id="addMealForm" class="popup">
        <div class="popup-content">
          <h3 class="popheader">Add Meal</h3>
          <a href="#" class="close-btn">&times;</a>
          <form method="POST">
            <input type="hidden" name="add_meal" value="1">

            <label class="header-lable">Meal name:</label>
            <input type="text" name="meal_name" placeholder="Enter meal name" required>

            <label class="header-lable">Price:</label>
            <input type="text" name="price" placeholder="Enter Price" required>

            <label class="header-lable">Ingredients:</label>
            <div class="dropdown-check-list" tabindex="1">
              <span class="dropdown-title">
                <span>Select Ingredients</span>
                <svg class="dropdown-arrow" width="10" height="6" viewBox="0 0 10 6">
                  <path d="M5 6L0 0H10L5 6Z" fill="currentColor" />
                </svg>
              </span>
              <ul class="items">
                <?php foreach ($ingredients as $ingredient): ?>
                  <li>
                    <label>
                      <input type="checkbox" name="ingredients[]" value="<?= $ingredient['INGREDIENT_ID'] ?>">
                      <?= htmlspecialchars($ingredient['INGREDIENT_NAME']) ?>
                    </label>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>

            <label class="header-lable">Category:</label>
            <select class="category-select" name="category" required>
              <option value="" disabled selected>Select Category</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= $category['CATEGORY_ID'] ?>">
                  <?= htmlspecialchars($category['CATEGORY_Name']) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <label class="header-lable">Image:</label>
            <input type="text" name="image" placeholder="Enter image filename" required>

            <button type="submit" class="CRUD-btn">Submit</button>
          </form>
        </div>
      </div>

      <!-- Update Meal Popups -->
      <?php foreach ($meals as $meal): ?>
        <div id="updateMealForm-<?= $meal['MEAL_ID'] ?>" class="popup">
          <div class="popup-content">
            <div class="popup-header">
              <a href="#" class="close-btn">&times;</a>
            </div>
            <div class="popup-body">
              <h4 class="popheader">Update Meal</h4>
              <form method="POST">
                <input type="hidden" name="update_meal" value="1">
                <input type="hidden" name="meal_id" value="<?= $meal['MEAL_ID'] ?>">

                <label class="header-lable">Meal name:</label>
                <input type="text" name="meal_name" value="<?= htmlspecialchars($meal['MEAL_Name']) ?>" required>

                <label class="header-lable">Price:</label>
                <input type="text" name="price" value="<?= $meal['MEAL_Price'] ?>" required>

                <label class="header-lable">Ingredients:</label>
                <div class="dropdown-check-list" tabindex="1">
                  <span class="dropdown-title">
                    <span>Select Ingredients</span>
                    <svg class="dropdown-arrow" width="10" height="6" viewBox="0 0 10 6">
                      <path d="M5 6L0 0H10L5 6Z" fill="currentColor" />
                    </svg>
                  </span>
                  <ul class="items">
                    <?php
                    $meal_ingredients = $con->query("
                                    SELECT INGREDIENT_ID 
                                    FROM MEAL_INGREDIENTS 
                                    WHERE MEAL_ID = {$meal['MEAL_ID']}
                                ")->fetch_all(MYSQLI_ASSOC);
                    $current_ingredients = array_column($meal_ingredients, 'INGREDIENT_ID');

                    foreach ($ingredients as $ingredient):
                      ?>
                      <li>
                        <label>
                          <input type="checkbox" name="ingredients[]" value="<?= $ingredient['INGREDIENT_ID'] ?>"
                            <?= in_array($ingredient['INGREDIENT_ID'], $current_ingredients) ? 'checked' : '' ?>>
                          <?= htmlspecialchars($ingredient['INGREDIENT_NAME']) ?>
                        </label>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>

                <label class="header-lable">Category:</label>
                <select class="category-select" name="category" required>
                  <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['CATEGORY_ID'] ?>" <?= $category['CATEGORY_ID'] == $meal['CATEGORY_ID'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($category['CATEGORY_Name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>

                <label class="header-lable">Image:</label>
                <input type="text" name="image" value="<?= htmlspecialchars($meal['MEAL_Icon']) ?>" required>

                <button type="submit" class="CRUD-btn">Update</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </main>
  </div>
</body>

</html>