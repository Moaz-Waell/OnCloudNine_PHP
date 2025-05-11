<?php
session_start();
include('../php/config.php');

date_default_timezone_set('Africa/Cairo');
$con->query("SET SESSION time_zone = '+02:00'");

// Error display
if (isset($_SESSION['error'])) {
  echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
  unset($_SESSION['error']);
}

// Fetch orders with notes
$query = "
    SELECT 
        o.ORDER_ID,
        u.USERS_Name AS customer_name,
        o.ORDER_ScheduleDate,
        o.ORDER_ScheduleTime,
        m.MEAL_Name,
        m.MEAL_Description,
        od.M_Quantity,
        od.NOTE,
        CASE 
            WHEN TIMEDIFF(o.ORDER_ScheduleTime, CURTIME()) BETWEEN '-00:10:00' AND '00:10:00' THEN 1
            ELSE 0 
        END AS is_priority
    FROM ORDERS o
    JOIN USERS u ON o.USERS_ID = u.USERS_ID
    JOIN ORDER_DETAILS od ON o.ORDER_ID = od.ORDER_ID
    JOIN MEAL m ON od.MEAL_ID = m.MEAL_ID
    WHERE 
        o.ORDER_ScheduleDate = CURDATE() AND
        o.ORDER_Status IN ('pending', 'preparing')
    ORDER BY 
        is_priority DESC,
        o.ORDER_ScheduleTime ASC
";

$result = $con->query($query);
$orders = [];

while ($row = $result->fetch_assoc()) {
  $orderId = $row['ORDER_ID'];
  if (!isset($orders[$orderId])) {
    $orders[$orderId] = [
      'customer' => $row['customer_name'],
      'date' => date('d M Y', strtotime($row['ORDER_ScheduleDate'])),
      'time' => date('h:i A', strtotime($row['ORDER_ScheduleTime'])),
      'priority' => $row['is_priority'],
      'meals' => []
    ];
  }
  $orders[$orderId]['meals'][] = [
    'name' => $row['MEAL_Name'],
    'description' => $row['MEAL_Description'],
    'quantity' => $row['M_Quantity'],
    'note' => $row['NOTE']
  ];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kitchen Dashboard</title>
  <link rel="stylesheet" href="../style/pages/kitchenDashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
  <header class="dashboard-header">
    <h1 class="dashboard-title">Kitchen Orders</h1>
    <div class="dashboard-info">
      <div class="current-time">
        <i class="fas fa-clock"></i>
        <span id="live-clock"><?= date('D, d M Y H:i:s') ?></span>
      </div>
    </div>
  </header>

  <main class="order-list">
    <?php if (empty($orders)): ?>
      <div class="no-orders">No pending orders found for today</div>
    <?php else: ?>
      <?php foreach ($orders as $orderId => $order): ?>
        <div class="order-card <?= $order['priority'] ? 'priority' : '' ?>">
          <div class="order-header">
            <div class="order-meta">
              <h2 class="order-id">Order #<?= $orderId ?></h2>
              <div class="customer-info">
                <i class="fas fa-user"></i>
                <span><?= htmlspecialchars($order['customer']) ?></span>
              </div>
            </div>
            <div class="order-timing">
              <div class="order-date">
                <i class="fas fa-calendar-day"></i>
                <?= $order['date'] ?>
              </div>
              <div class="order-time">
                <i class="fas fa-clock"></i>
                <?= $order['time'] ?>
              </div>
            </div>
          </div>
          <div class="meal-list">
            <?php foreach ($order['meals'] as $meal): ?>
              <div class="meal-item">
                <div class="meal-info">
                  <h3 class="meal-name">
                    <?= htmlspecialchars($meal['name']) ?>
                    <span class="meal-quantity">x<?= $meal['quantity'] ?></span>
                  </h3>

                  <?php if (!empty($meal['description'])): ?>
                    <p class="meal-description">
                      <?= htmlspecialchars($meal['description']) ?>
                    </p>
                  <?php endif; ?>
                  <?php if (!empty($meal['note'])): ?>
                    <div class="meal-note">
                      <strong>Special Note:</strong>
                      <?= htmlspecialchars($meal['note']) ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="order-actions">
            <form method="GET" action="../php/update_order_status.php" class="status-form">
              <input type="hidden" name="order_id" value="<?= $orderId ?>">
              <input type="hidden" name="status" value="Preparing">
              <button type="submit" class="status-btn in-progress">
                <i class="fas fa-spinner"></i> In Progress
              </button>
            </form>
            <form method="GET" action="../php/update_order_status.php" class="status-form">
              <input type="hidden" name="order_id" value="<?= $orderId ?>">
              <input type="hidden" name="status" value="Out For Delivery">
              <button type="submit" class="status-btn out-delivery">
                <i class="fas fa-truck"></i> Out for Delivery
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>

  <script>
    // Live clock update
    function updateClock() {
      const now = new Date();
      const options = {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      };
      document.getElementById('live-clock').textContent = now.toLocaleDateString('en-US', options);
    }
    setInterval(updateClock, 1000);

    // Auto-refresh every 30 seconds
    setInterval(() => {
      window.location.reload();
    }, 5000);
  </script>
</body>

</html>