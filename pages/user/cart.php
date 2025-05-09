<?php
session_start();
include('../../php/config.php');

// Check authentication
if (!isset($_SESSION['user_id'])) {
  header("Location: ../../pages/aast/uniUserLogin.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Handle quantity updates
if (isset($_GET['action']) && isset($_GET['cart_id'])) {
  $cart_id = intval($_GET['cart_id']);

  if ($_GET['action'] === 'decrement') {
    $stmt = $con->prepare("SELECT QUANTITY FROM CART WHERE CART_ID = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $quantity = $stmt->get_result()->fetch_assoc()['QUANTITY'];

    if ($quantity > 1) {
      $update = $con->prepare("UPDATE CART SET QUANTITY = QUANTITY - 1 WHERE CART_ID = ?");
      $update->bind_param("i", $cart_id);
      $update->execute();
    } else {
      $delete = $con->prepare("DELETE FROM CART WHERE CART_ID = ?");
      $delete->bind_param("i", $cart_id);
      $delete->execute();
    }
    header("Location: cart.php");
    exit();
  }
}

// Get cart items with descriptions
$cart_items = [];
$total = 0;
$stmt = $con->prepare("SELECT c.*, m.MEAL_Price, m.MEAL_Name, m.MEAL_Icon, m.MEAL_Description, cat.CATEGORY_Name 
                      FROM CART c 
                      JOIN MEAL m ON c.MEAL_ID = m.MEAL_ID 
                      JOIN CATEGORY cat ON m.CATEGORY_ID = cat.CATEGORY_ID 
                      WHERE c.USERS_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $cart_items[] = $row;
  $total += $row['MEAL_Price'] * $row['QUANTITY'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cart</title>
  <link rel="stylesheet" href="../../style/pages/user/cart.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <div class="container">
    <?php include('../../components/sideNav.html'); ?>
    <main class="main-content">
      <form action="checkout.php" method="POST">
        <div class="meal-cart-container">
          <?php if (empty($cart_items)): ?>
            <div class="empty-cart-message">
              <i class="fas fa-shopping-cart fa-3x"></i>
              <h2 class="heading-secondary ">Your Cart is Empty</h2>
              <p class="description">Looks like you haven't added any meals yet!</p>
              <a href="home.php" class="btn btn--full margin-top-2rem">Browse Meals
              </a>
            </div>
          <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
              <div class="meal-item">
                <div class="meal-item__info">
                  <img class="meal-item__photo"
                    src="../../img/meals/<?= strtolower(htmlspecialchars($item['CATEGORY_Name'])) ?>/<?= htmlspecialchars($item['MEAL_Icon']) ?>"
                    alt="<?= htmlspecialchars($item['MEAL_Name']) ?>">
                  <div class="meal-item__content">
                    <div class="heading-secondary"><?= htmlspecialchars($item['MEAL_Name']) ?></div>
                    <div class="description">
                      <?= !empty($item['NOTE'])
                        ? htmlspecialchars($item['NOTE'])
                        : htmlspecialchars($item['MEAL_Description']) ?>
                    </div>
                    <div class="description">
                      Quantity: <?= htmlspecialchars($item['QUANTITY']) ?>
                    </div>
                    <div class="meal-item__price">
                      $<?= number_format($item['MEAL_Price'], 2) ?>
                    </div>
                  </div>
                  <a href="cart.php?action=decrement&cart_id=<?= $item['CART_ID'] ?>" class="meal-item__remove-btn">
                    <i class="fas fa-minus"></i>
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <?php if (!empty($cart_items)): ?>
          <div class="cart-footer-wrapper">
            <div class="cart-footer">
              <div class="total-amount">
                <i class="fas fa-receipt"></i>
                Total Amount: $<span id="total-amount"><?= number_format($total, 2) ?></span>
              </div>
              <button type="submit" class="btn btn--full btn--checkout" <?= empty($cart_items) ? 'disabled' : '' ?>>
                <i class="fas fa-credit-card"></i> Proceed to Checkout
              </button>
            </div>
          </div>
        <?php endif; ?>
      </form>
    </main>
  </div>
</body>

</html>