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
  if (isset($_POST['add_category'])) {
    // Add new category
    $name = $_POST['name'];
    $icon = $_POST['icon'];

    $stmt = $con->prepare("INSERT INTO CATEGORY (CATEGORY_Name, CATEGORY_Icon) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $icon);
    $stmt->execute();
  } elseif (isset($_POST['update_category'])) {
    // Update existing category
    $id = $_POST['category_id'];
    $name = $_POST['name'];
    $icon = $_POST['icon'];

    $stmt = $con->prepare("UPDATE CATEGORY SET CATEGORY_Name = ?, CATEGORY_Icon = ? WHERE CATEGORY_ID = ?");
    $stmt->bind_param("ssi", $name, $icon, $id);
    $stmt->execute();
  }
}

// Handle delete action
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $stmt = $con->prepare("DELETE FROM CATEGORY WHERE CATEGORY_ID = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
}

// Get all categories
$categories = [];
$result = $con->query("SELECT * FROM CATEGORY");
while ($row = $result->fetch_assoc()) {
  $categories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Category Management</title>
  <link rel="stylesheet" href="../../style/pages/admin/CRUD_Category.css" />
</head>

<body>
  <div class="container">
    <?php include('../../components/admin_sideNav.php'); ?>

    <main class="main-content">
      <div class="category-container">
        <div class="category-header">
          <h2>Category</h2>
          <a href="#popupForm" class="CRUD-btn">Add category</a>
        </div>

        <table class="category-table">
          <thead>
            <tr>
              <th>Icon</th>
              <th>Category Name</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $category): ?>
              <tr>
                <td>
                  <img src="../../img/category/<?= htmlspecialchars($category['CATEGORY_Icon']) ?>"
                    alt="<?= htmlspecialchars($category['CATEGORY_Name']) ?>" class="icon-img" />
                </td>
                <td><?= htmlspecialchars($category['CATEGORY_Name']) ?></td>
                <td>
                  <form method="GET" style="display: inline;">
                    <input type="hidden" name="delete" value="<?= $category['CATEGORY_ID'] ?>">
                    <a href="?delete=<?= $category['CATEGORY_ID'] ?>" class="CRUD-btn">Remove</a>
                  </form>
                  <a href="#popup_<?= $category['CATEGORY_ID'] ?>" class="CRUD-btn">Update</a>

                  <!-- Update Popup -->
                  <div id="popup_<?= $category['CATEGORY_ID'] ?>" class="popup">
                    <div class="popup-content">
                      <div class="popup-header">
                        <a href="#" class="close-btn">&times;</a>
                      </div>
                      <div class="popup-body">
                        <h4 class="popheader">Update Category</h4>
                        <form method="POST">
                          <input type="hidden" name="category_id" value="<?= $category['CATEGORY_ID'] ?>">
                          <div class="form-group">
                            <label class="header-lable">New name:</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($category['CATEGORY_Name']) ?>"
                              required />
                          </div>
                          <div class="form-group">
                            <label class="header-lable">New icon:</label>
                            <input type="text" name="icon" value="<?= htmlspecialchars($category['CATEGORY_Icon']) ?>"
                              required />
                          </div>
                          <button type="submit" name="update_category">Submit</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <!-- Add Category Popup -->
      <div id="popupForm" class="popup">
        <div class="popup-content">
          <h3 class="popheader">Add Category</h3>
          <a href="#" class="close-btn">&times;</a>
          <form method="POST">
            <div class="form-group">
              <label class="header-lable">Category name:</label>
              <input type="text" name="name" placeholder="Enter name" required />
            </div>
            <div class="form-group">
              <label class="header-lable">Icon:</label>
              <input type="text" name="icon" placeholder="Image Name" required />
            </div>
            <button type="submit" name="add_category">Submit</button>
          </form>
        </div>
      </div>
    </main>
  </div>
</body>

</html>