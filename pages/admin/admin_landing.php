<?php
session_start();
require_once '../../php/config.php';

// Check authentication
$user_id = $_COOKIE['user_id'];

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
                      ORDER BY ORDER_ScheduleDate DESC");
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OCN Food Dashboard</title>
  <link rel="stylesheet" href="../../style/pages/admin/admin_landing.css" />
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <div class="container">
    <!-- Sidebar -->
    <?php include('../../components/admin_sideNav.html'); ?>
    <!-- Main Content -->
    <main class="main-content">
      <!-- Header -->
      <header class="header">
        <div class="header-title">
          <h1>Dashboard</h1>
        </div>
      </header>
      <!-- top Section -->

      <div class="top">
        <section class="analysis-section">
          <div>
            <h2 class="heading-secondary text-center">Analysis</h2>
          </div>
          <div class="analysis-flex">
            <div class="analysis-item">
              <p class="margin-bottom-1rem"><b>total number of orders:</b></p>
              <p>676</p>
            </div>
            <div class="analysis-item">
              <p class="margin-bottom-1rem"><b>total revenue:</b></p>
              <p>7656</p>
            </div>
          </div>
        </section>
        <section class="analysis-section">
          <div>
            <h2 class="heading-secondary text-center">voucher</h2>
            <span class="underline"></span>
          </div>
          <div class="analysis-flex">
            <div class="btn btn--order-again analysis-item">
              <p>send vouchers</p>
            </div>
          </div>
        </section>

      </div>
      <hr />

      <!-- Active Orders Table -->
      <section class="margin-top-2rem">
        <h2 class="heading-secondary text-center">Current Orders</h2>

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
                  <?php if ($order['ORDER_Status'] === 'Pending' || $order['ORDER_Status'] === 'In Progress'): ?>
                    <form method="POST" action="../../php/cancelOrder.php">
                      <input type="hidden" name="order_id" value="<?= $order['ORDER_ID'] ?>">
                      <button type="submit" class="btn btn--order-again">Cancel Order</button>
                    </form>
                  <?php endif; ?>
                  <?php if ($order['ORDER_Status'] === 'Pending' || $order['ORDER_Status'] === 'In Progress'): ?>
                    <form method="POST" action="../../php/cancelOrder.php">
                      <input type="hidden" name="order_id" value="<?= $order['ORDER_ID'] ?>">
                      <button type="submit" class="btn btn--order-again">Delivered</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>


      <section>
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
      </section>
    </main>
  </div>
</body>

</html>