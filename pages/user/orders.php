<?php
session_start();
include('../../php/config.php');

// Check authentication
if (!isset($_SESSION['user_id'])) {
  header("Location: ../../pages/aast/uniUserLogin.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Get active orders (not delivered or canceled)
$active_orders = [];
$stmt = $con->prepare("SELECT * FROM ORDERS 
                      WHERE USERS_ID = ? 
                      AND ORDER_Status NOT IN ('Delivered', 'Cancelled')
                      ORDER BY ORDER_ScheduleDate DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  $active_orders[] = $row;
}

// Get order history
$order_history = [];
$stmt = $con->prepare("SELECT * FROM ORDERS 
                      WHERE USERS_ID = ? 
                      AND ORDER_Status IN ('Delivered', 'Cancelled')
                      ORDER BY ORDER_ID DESC"); // Changed from ORDER_ScheduleDate DESC to ORDER_ID DESC
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  $order_history[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Orders</title>
  <link rel="stylesheet" href="../../style/pages/user/orders.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <div class="container">
    <?php include('../../components/sideNav.php'); ?>
    <main class="main-content">
      <h2 class="heading-secondary text-center">Current Orders</h2>

      <!-- Active Orders Table -->
      <table class="orders-table">
        <thead>
          <tr class="heading-secondary">
            <th>Order ID</th>
            <th>Date</th>
            <th>Time</th>
            <th>Total</th>
            <th>Details</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($active_orders as $order): ?>
            <tr>
              <td>#<?= htmlspecialchars($order['ORDER_ID']) ?></td>
              <td><?= date('M j', strtotime($order['ORDER_ScheduleDate'])) ?></td>
              <td><?= date('h:i A', strtotime($order['ORDER_ScheduleTime'])) ?></td>
              <td>$<?= number_format($order['ORDER_Amount'], 2) ?></td>
              <td>
                <a href="viewOrderDetails.php?order_id=<?= $order['ORDER_ID'] ?>" class="view-details">
                  View Details
                </a>
              </td>
              <td>
                <button
                  class="btn btn--status ordersstatus <?= strtolower(str_replace(' ', '-', $order['ORDER_Status'])) ?>">
                  <?= htmlspecialchars($order['ORDER_Status']) ?>
                </button>
              </td>
              <td>
                <?php if ($order['ORDER_Status'] === 'Pending' || $order['ORDER_Status'] === 'In Progress' || $order['ORDER_Status'] === 'Out For Delivery' || $order['ORDER_Status'] === 'Preparing'): ?>
                  <form method="POST" action="../../php/cancelOrder.php">
                    <input type="hidden" name="order_id" value="<?= $order['ORDER_ID'] ?>">
                    <button type="submit" class="btn btn--order-again">Cancel Order</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <h2 class="heading-secondary text-center">Order History</h2>

      <!-- Order History Table -->
      <table class="orders-table">
        <thead>
          <tr class="heading-secondary">
            <th>Order ID</th>
            <th>Date</th>
            <th>Total</th>
            <th>Details</th>
            <th>Status</th>
            <th>Action</th>
            <th>Feedback</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($order_history as $order): ?>
            <tr>
              <td>#<?= htmlspecialchars($order['ORDER_ID']) ?></td>
              <td><?= date('M j, Y', strtotime($order['ORDER_ScheduleDate'])) ?></td>
              <td>$<?= number_format($order['ORDER_Amount'], 2) ?></td>
              <td>
                <a href="viewOrderDetails.php?order_id=<?= $order['ORDER_ID'] ?>" class="view-details">
                  View Details
                </a>
              </td>
              <td>
                <button class="btn btn--status ordersstatus <?= strtolower($order['ORDER_Status']) ?>">
                  <?= htmlspecialchars($order['ORDER_Status']) ?>
                </button>
              </td>
              <td>
                <form method="POST" action="../../php/reorder.php">
                  <input type="hidden" name="order_id" value="<?= $order['ORDER_ID'] ?>">
                  <button type="submit" class="btn btn--order-again">Reorder</button>
                </form>
              </td>
              <td>
                <?php if ($order['ORDER_Status'] === 'Delivered' && empty($order['ORDER_Feedback'])): ?>
                  <button class="btn btn--order-again open-feedback" data-order-id="<?= $order['ORDER_ID'] ?>">
                    Give Feedback
                  </button>
                <?php elseif ($order['ORDER_Feedback']): ?>
                  <div class="rating-stars">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                      <span class="star <?= $i < $order['ORDER_Feedback'] ? 'filled' : '' ?>">★</span>
                    <?php endfor; ?>
                  </div>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Feedback Popup -->
      <div id="feedbackPopup" class="popup">
        <div class="popup-content">
          <span class="close-btn">&times;</span>
          <form method="POST" action="../../php/submitFeedback.php">
            <input type="hidden" name="order_id" id="feedbackOrderId">
            <p class="question">How do you feel about your order?</p>
            <div class="rating">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <label>
                  <input type="radio" name="rating" value="<?= $i ?>">
                  <span class="rating-number"><?= $i ?></span>
                </label>
              <?php endfor; ?>
            </div>
            <button type="submit" class="next-btn">Submit</button>
          </form>
        </div>
      </div>
    </main>
  </div>

  <script>
    // Feedback popup handling
    document.querySelectorAll('.open-feedback').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        document.getElementById('feedbackOrderId').value = btn.dataset.orderId;
        document.getElementById('feedbackPopup').style.display = 'block';
      });
    });

    document.querySelector('.close-btn').addEventListener('click', (e) => {
      e.preventDefault();
      document.getElementById('feedbackPopup').style.display = 'none';
    });
  </script>
</body>

</html>