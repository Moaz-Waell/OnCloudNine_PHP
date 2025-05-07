<?php
session_start();
include('../../php/config.php');

// Check authentication
if (!isset($_COOKIE['user_id'])) {
  header("Location: ../../pages/aast/uniUserLogin.php");
  exit();
}

$user_id = $_COOKIE['user_id'];
$error = '';
$subtotal = 0;
$discount = 0;
$discount_percent = 0;

// Restore discount from session
if (isset($_SESSION['applied_discount'])) {
  $discount = $_SESSION['applied_discount'];
  $discount_percent = $_SESSION['discount_percent'];
}

// Get cart items and calculate subtotal
$cart_items = [];
$stmt = $con->prepare("SELECT c.*, m.MEAL_Price, m.MEAL_Name, m.MEAL_Icon, cat.CATEGORY_Name 
                      FROM CART c
                      JOIN MEAL m ON c.MEAL_ID = m.MEAL_ID
                      JOIN CATEGORY cat ON m.CATEGORY_ID = cat.CATEGORY_ID
                      WHERE c.USERS_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $cart_items[] = $row;
  $subtotal += $row['MEAL_Price'] * $row['QUANTITY'];
}

// Handle voucher application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_voucher'])) {
  $voucher_id = intval($_POST['apply_voucher']);

  $stmt = $con->prepare("SELECT v.*, uv.VOUCHER_StartDate, uv.VOUCHER_EndDate 
                          FROM VOUCHER v
                          JOIN USER_VOUCHERS uv ON v.VOUCHER_ID = uv.VOUCHER_ID
                          WHERE v.VOUCHER_ID = ? 
                          AND uv.USERS_ID = ?
                          AND CURDATE() BETWEEN uv.VOUCHER_StartDate AND uv.VOUCHER_EndDate");
  $stmt->bind_param("ii", $voucher_id, $user_id);
  $stmt->execute();
  $voucher = $stmt->get_result()->fetch_assoc();

  if ($voucher) {
    $discount_percent = $voucher['VOUCHER_Percentage'];
    $discount = $subtotal * ($discount_percent / 100);
    $_SESSION['applied_voucher'] = $voucher['VOUCHER_ID'];
    $_SESSION['discount_percent'] = $discount_percent;
    $_SESSION['applied_discount'] = $discount;
  } else {
    $error = "Invalid or expired voucher";
  }
}

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
  try {
    $con->begin_transaction();

    // Recalculate discount based on current subtotal
    if (isset($_SESSION['applied_voucher'])) {
      $discount_percent = $_SESSION['discount_percent'];
      $discount = $subtotal * ($discount_percent / 100);
    }

    // Get order details
    $payment_type = $_POST['payment_method'];
    $schedule_date = $_POST['Schedule_date'];
    $schedule_time = $_POST['Schedule_time'];

    if (isset($_POST['deliver_now'])) {
      $schedule_date = date('Y-m-d');
      $schedule_time = date('H:i:s');
    }

    $total = $subtotal - $discount;

    // Create order
    $order_stmt = $con->prepare("INSERT INTO ORDERS 
                  (ORDER_Status, ORDER_ScheduleDate, ORDER_ScheduleTime, ORDER_Amount, ORDER_PaymentType, USERS_ID)
                  VALUES ('Pending', ?, ?, ?, ?, ?)");
    $order_stmt->bind_param(
      "ssdsi",
      $schedule_date,
      $schedule_time,
      $total,
      $payment_type,
      $user_id
    );
    $order_stmt->execute();

    // Clear cart
    $delete_cart = $con->prepare("DELETE FROM CART WHERE USERS_ID = ?");
    $delete_cart->bind_param("i", $user_id);
    $delete_cart->execute();

    // Remove used voucher
    if (isset($_SESSION['applied_voucher'])) {
      $delete_voucher = $con->prepare("DELETE FROM USER_VOUCHERS 
                                            WHERE USERS_ID = ? AND VOUCHER_ID = ?");
      $delete_voucher->bind_param("ii", $user_id, $_SESSION['applied_voucher']);
      $delete_voucher->execute();

      // Clear voucher session data
      unset($_SESSION['applied_voucher']);
      unset($_SESSION['discount_percent']);
      unset($_SESSION['applied_discount']);
    }

    $con->commit();
    header("Location: orders.php");
    exit();
  } catch (Exception $e) {
    $con->rollback();
    $error = "Error placing order: " . $e->getMessage();
  }
}

// Get available vouchers
$vouchers = [];
$stmt = $con->prepare("SELECT 
                        v.VOUCHER_ID,
                        v.VOUCHER_Percentage,
                        uv.VOUCHER_StartDate,
                        uv.VOUCHER_EndDate 
                      FROM USER_VOUCHERS uv
                      JOIN VOUCHER v ON uv.VOUCHER_ID = v.VOUCHER_ID
                      WHERE uv.USERS_ID = ? 
                      AND CURDATE() BETWEEN uv.VOUCHER_StartDate AND uv.VOUCHER_EndDate");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  $vouchers[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Checkout</title>
  <link rel="stylesheet" href="../../style/pages/user/checkout.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <div class="container">
    <?php include('../../components/sideNav.html'); ?>
    <main class="main-content">
      <div class="checkout container">
        <?php if ($error): ?>
          <div class="error-message">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form method="POST">
          <div class="left_side">
            <h2 class="heading-primary">Checkout</h2>

            <!-- Delivery Options -->
            <label class="checkbox-container margin-top-2rem">
              <input type="checkbox" name="deliver_now" id="deliver_now">
              <span class="checkmark"></span>
              Deliver Now
            </label>
            <div class="heading-secondary">
              <label for="Schedule_date">Schedule Delivery</label>
              <input type="date" name="Schedule_date" id="Schedule_date" min="<?= date('Y-m-d') ?>" required
                value="<?= isset($_POST['Schedule_date']) ? htmlspecialchars($_POST['Schedule_date']) : '' ?>">
            </div>
            <div class="heading-secondary">
              <label for="Schedule_time">Schedule Time</label>
              <input type="time" name="Schedule_time" id="Schedule_time" min="<?= date('H:i') ?>" required value="
                    <?= isset($_POST['Schedule_time']) ? htmlspecialchars($_POST['Schedule_time']) : '' ?>">
            </div>

            <!-- Vouchers Section -->
            <div class="coupon_section">
              <h3 class="heading-secondary">Vouchers</h3>
              <?php if (empty($vouchers)): ?>
                <div class="no-vouchers">
                  <p>No vouchers available</p>
                </div>
              <?php else: ?>
                <?php foreach ($vouchers as $voucher): ?>
                  <div class="coupon">
                    <div class="coupon-details">
                      <span class="badge">
                        <?= htmlspecialchars($voucher['VOUCHER_Percentage']) ?>% Off
                      </span>
                      <span class="end-date">
                        Valid until
                        <?= htmlspecialchars($voucher['VOUCHER_EndDate']) ?>
                      </span>
                    </div>
                    <button type="submit" name="apply_voucher" value="<?= $voucher['VOUCHER_ID'] ?>" class="apply-btn">
                      <?= (isset($_SESSION['applied_voucher']) && $_SESSION['applied_voucher'] == $voucher['VOUCHER_ID']) ? 'Applied' : 'Apply' ?>
                    </button>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>

            <!-- Order Summary -->
            <div class="total_price">
              <h3 class="description">Subtotal: $
                <?= number_format($subtotal, 2) ?>
              </h3>
              <h3 class="description">Discount: $
                <?= number_format($discount, 2) ?>
              </h3>
              <h3 class="heading-secondary">Total Price: $
                <?= number_format($subtotal - $discount, 2) ?>
              </h3>
            </div>
          </div>

          <!-- Payment Section -->
          <div class="left_right_wrapper">
            <div class="right_side">
              <h2 class="heading-primary">Payment Method</h2>
              <div class="form form-inline">
                <input type="radio" name="payment_method" id="cash" value="Cash" checked>
                <label for="cash">Cash</label>
              </div>
              <div class="form form-inline">
                <input type="radio" name="payment_method" id="card" value="Card">
                <label for="card">Credit Card</label>
              </div>

              <div id="card-details" style="display: none;">
                <!-- Card details fields remain same -->
              </div>

              <button type="submit" name="place_order" class="btn btn--full place_order_btn">
                Place Order
              </button>
            </div>
          </div>
        </form>
      </div>
    </main>
  </div>

  <script>
    // Show/hide card details
    document.getElementById('card').addEventListener('change', function () {
      document.getElementById('card-details').style.display = this.checked ? 'block' : 'none';
    });

    // Auto-fill deliver now
    document.getElementById('deliver_now').addEventListener('change', function () {
      const dateField = document.getElementById('Schedule_date');
      const timeField = document.getElementById('Schedule_time');

      if (this.checked) {
        const now = new Date();
        dateField.value = now.toISOString().split('T')[0];
        timeField.value = now.toTimeString().substring(0, 5);
        dateField.readOnly = true;
        timeField.readOnly = true;
      } else {
        dateField.readOnly = false;
        timeField.readOnly = false;
        dateField.value = '';
        timeField.value = '';
      }
    });
  </script>
</body>

</html>