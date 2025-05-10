<?php
session_start();
require_once '../../php/config.php';

if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit();
}

// Get active orders
$active_orders = [];
$stmt = $con->prepare("SELECT o.*, u.USERS_Name 
                      FROM ORDERS o
                      JOIN USERS u ON o.USERS_ID = u.USERS_ID
                      WHERE ORDER_Status NOT IN ('Delivered', 'Cancelled')
                      ORDER BY ORDER_ScheduleDate DESC");
$stmt->execute();
$active_orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get order history with feedback
$order_history = [];
$stmt = $con->prepare("SELECT o.*, u.USERS_Name 
                      FROM ORDERS o
                      JOIN USERS u ON o.USERS_ID = u.USERS_ID
                      WHERE ORDER_Status IN ('Delivered', 'Cancelled')
                      ORDER BY ORDER_ScheduleDate DESC");
$stmt->execute();
$order_history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Analytics
$analytics_stmt = $con->prepare("SELECT 
                                COUNT(*) as total_orders,
                                SUM(ORDER_Amount) as total_revenue 
                                FROM ORDERS 
                                WHERE ORDER_Status = 'Delivered'");
$analytics_stmt->execute();
$analytics = $analytics_stmt->get_result()->fetch_assoc();

// Messages
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OCN Admin Dashboard</title>
  <link rel="stylesheet" href="../../style/pages/admin/admin_landing.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <div class="container">
    <?php include('../../components/admin_sideNav.php'); ?>

    <main class="main-content">
      <header class="header flex-space-between">
        <div class="header-title">
          <h1>Dashboard</h1>
        </div>
        <div class="header-title">
          <h1>Welcome, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></h1>
        </div>
      </header>

      <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="success-message"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <div class="top">
        <section class="analysis-section">
          <h2 class="heading-secondary text-center">Analysis</h2>
          <div class="analysis-flex">
            <div class="analysis-item">
              <p class="margin-bottom-1rem"><b>Total Orders:</b></p>
              <p><?= $analytics['total_orders'] ?? 0 ?></p>
            </div>
            <div class="analysis-item">
              <p class="margin-bottom-1rem"><b>Total Revenue:</b></p>
              <p>$<?= number_format($analytics['total_revenue'] ?? 0, 2) ?></p>
            </div>
          </div>
        </section>

        <section class="analysis-section">
          <h2 class="heading-secondary text-center">Vouchers</h2>
          <div class="analysis-flex">
            <form method="POST" action="../../php/sendVouchers.php">
              <button type="submit" class="btn btn--order-again analysis-item">
                Send Vouchers
              </button>
            </form>
          </div>
        </section>
      </div>
      <hr />

      <!-- Current Orders -->
      <section class="margin-top-2rem">
        <h2 class="heading-secondary text-center">Current Orders</h2>
        <table class="orders-table">
          <thead>
            <tr class="heading-secondary">
              <th>Order ID</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Time</th>
              <th>Total</th>
              <th>Details</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($active_orders as $order): ?>
              <tr>
                <td>#<?= htmlspecialchars($order['ORDER_ID']) ?></td>
                <td><?= htmlspecialchars($order['USERS_Name']) ?></td>
                <td><?= date('M j', strtotime($order['ORDER_ScheduleDate'])) ?></td>
                <td><?= date('h:i A', strtotime($order['ORDER_ScheduleTime'])) ?></td>
                <td>$<?= number_format($order['ORDER_Amount'], 2) ?></td>
                <td>
                  <a href="viewOrderDetails.php?order_id=<?= $order['ORDER_ID'] ?>" class="view-details">
                    View Details
                  </a>
                </td>
                <td>
                  <span class="btn btn--status ordersstatus <?= strtolower($order['ORDER_Status']) ?>">
                    <?= htmlspecialchars($order['ORDER_Status']) ?>
                  </span>
                </td>
                <td>
                  <?php if ($order['ORDER_Status'] === 'Pending' || $order['ORDER_Status'] === 'In Progress'): ?>
                    <form method="POST" action="../../php/cancelOrder.php" class="inline-form">
                      <input type="hidden" name="order_id" value="<?= $order['ORDER_ID'] ?>">
                      <button type="submit" class="btn btn--order-again">Cancel</button>
                    </form>
                    <form method="POST" action="../../php/deliverOrder.php" class="inline-form">
                      <input type="hidden" name="order_id" value="<?= $order['ORDER_ID'] ?>">
                      <button type="submit" class="btn btn--order-again">Deliver</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>

      <!-- Order History with Feedback -->
      <section class="margin-top-2rem">
        <h2 class="heading-secondary text-center">Order History</h2>
        <table class="orders-table">
          <thead>
            <tr class="heading-secondary">
              <th>Order ID</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Total</th>
              <th>Status</th>
              <th>Details</th>
              <th>Feedback</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($order_history as $order): ?>
              <tr>
                <td>#<?= htmlspecialchars($order['ORDER_ID']) ?></td>
                <td><?= htmlspecialchars($order['USERS_Name']) ?></td>
                <td><?= date('M j, Y', strtotime($order['ORDER_ScheduleDate'])) ?></td>
                <td>$<?= number_format($order['ORDER_Amount'], 2) ?></td>
                <td>
                  <span class="btn btn--status ordersstatus <?= strtolower($order['ORDER_Status']) ?>">
                    <?= htmlspecialchars($order['ORDER_Status']) ?>
                  </span>
                </td>
                <td>
                  <a href="viewOrderDetails.php?order_id=<?= $order['ORDER_ID'] ?>" class="view-details">
                    View Details
                  </a>
                </td>
                <td>
                  <?php if ($order['ORDER_Status'] === 'Delivered' && !empty($order['ORDER_Feedback'])): ?>
                    <div class="rating-stars">
                      <?php for ($i = 0; $i < 5; $i++): ?>
                        <span class="star <?= $i < $order['ORDER_Feedback'] ? 'filled' : '' ?>">★</span>
                      <?php endfor; ?>
                    </div>
                  <?php else: ?>
                    <span class="no-feedback">N/A</span>
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