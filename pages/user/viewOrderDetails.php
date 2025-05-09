<?php
session_start();
require_once '../../php/config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
  header("Location: ../../pages/aast/uniUserLogin.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_GET['order_id']);

// Get order details
$order_stmt = $con->prepare("SELECT o.*, u.USERS_Name 
                            FROM ORDERS o
                            JOIN USERS u ON o.USERS_ID = u.USERS_ID
                            WHERE o.ORDER_ID = ? 
                            AND o.USERS_ID = ?");
$order_stmt->bind_param("ii", $order_id, $user_id);
$order_stmt->execute();
$order = $order_stmt->get_result()->fetch_assoc();

// Get order items
$items_stmt = $con->prepare("SELECT od.*, m.MEAL_Name, m.MEAL_Price, m.MEAL_Icon, cat.CATEGORY_Name 
                            FROM ORDER_DETAILS od
                            JOIN MEAL m ON od.MEAL_ID = m.MEAL_ID
                            JOIN CATEGORY cat ON m.CATEGORY_ID = cat.CATEGORY_ID
                            WHERE od.ORDER_ID = ?");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Details</title>
  <link rel="stylesheet" href="../../style/pages/user/viewOrderDetails.css" />
</head>

<body>
  <a href="orders.php" class="back-button" aria-label="Back to Orders">
    <span class="back-arrow">←</span>
  </a>

  <div class="order-header">
    <h1 class="category-title">Order #<?= $order_id ?></h1>
    <span class="btn btn--status order-status ordersstatus <?= strtolower(str_replace(' ', '-', $order['ORDER_Status'])) ?>">
      <?= htmlspecialchars($order['ORDER_Status']) ?>
    </span>
  </div>

  <div class="order-summary">
    <div class="summary-item">
      <span class="label">Order Date:</span>
      <span class="value">
        <?= date('Y-m-d H:i', strtotime($order['ORDER_ScheduleDate'] . ' ' . $order['ORDER_ScheduleTime'])) ?>
      </span>
    </div>
    <div class="summary-item">
      <span class="label">Customer:</span>
      <span class="value"><?= htmlspecialchars($order['USERS_Name']) ?></span>
    </div>
  </div>

  <div class="order-items">
    <h2 class="section-title">Items</h2>
    <?php foreach ($items as $item): ?>
      <div class="order-item">
        <div class="item-image">
          <img src="../../img/meals/<?= strtolower($item['CATEGORY_Name']) ?>/<?= htmlspecialchars($item['MEAL_Icon']) ?>"
            alt="<?= htmlspecialchars($item['MEAL_Name']) ?>">
        </div>
        <div class="item-details">
          <h3><?= htmlspecialchars($item['MEAL_Name']) ?></h3>
          <?php if (!empty($item['NOTE'])): ?>
            <p class="item-note">Note: <?= htmlspecialchars($item['NOTE']) ?></p>
          <?php endif; ?>
        </div>
        <div class="item-price">
          <span class="quantity">x<?= $item['M_Quantity'] ?></span>
          <span class="unit-price">$<?= number_format($item['MEAL_Price'], 2) ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="order-total">
    <div class="total-row final-total">
      <span class="label">Total Amount:</span>
      <span class="value">$<?= number_format($order['ORDER_Amount'], 2) ?></span>
    </div>
  </div>
</body>

</html>