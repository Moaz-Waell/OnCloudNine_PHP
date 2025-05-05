<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../style/pages/checkout.css" />


  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <div class="container">
    <?php include('../components/nav.html'); ?>
    <main class="main-content">
      <div class="checkout container">
        <div class="left_side">
          <h2 class="heading-primary">Checkout</h2>
          <h3 class="heading-secondary"><u>Order number:</u> <span>123</span></h3>
          <div class="deliver_now">
            <input type="checkbox" name="deliver_now" id="deliver_now" value="deliver_now" class="deliver_now"/>
            <label for="deliver_now">Deliver Now</label>  
          </div>
          <div class="heading-secondary">
            <label for="Schedule_date" class="Schedule_date">Schedule Delivery</label>
            <input type="date" name="Schedule_date" id="Schedule_date" placeholder="MM/YY" />
          </div>
          <div class="heading-secondary">
            <label for="Schedule_time" class="Schedule_time">Schedule Time</label>
            <input type="time" name="Schedule_time" id="Schedule_time"/>
          </div>
          <div class="coupon_section">
            <div class="coupon">
              <div class="coupon-details">
                <span class="badge">5% Off</span>
              </div>
              <button class="apply-btn">Apply</button>
            </div>
          </div>
          <div class="coupon_section">
            <div class="coupon">
              <div class="coupon-details">
                <span class="badge">30% Off</span>
              </div>
              <button class="apply-btn">Apply</button>
            </div>
          </div>
          <div class="coupon_section">
            <div class="coupon">
              <div class="coupon-details">
                <span class="badge">15% Off</span>
              </div>
              <button class="apply-btn">Apply</button>
            </div>
          </div>
          <div class="total_price">
            <h3 class="description">Subtotal: <span>123</span></h3>
            <h3 class="description">Discount: <span>100</span></h3>
            <h3 class="heading-secondary">Total Price: <span>1293</span></h3>
          </div>
        </div>

        <div class="left_right_wrapper">
          <div class="right_side">
            <h2 class="heading-primary">Payment method</h2>
            <form action="" method="POST" class="card_details">
              <div class="form form-inline">
                <input type="radio" name="payment_method" id="cash" value="cash" />
                <label for="cash">Cash</label>
              </div>
              <div class="form form-inline">
                <input type="radio" name="payment_method" id="card" value="card" />
                <label for="card">Card</label>
              </div>
              <div class="form">
                <label for="cardholder_name">Cardholder Name</label>
                <input type="text" name="cardholder_name" id="cardholder_name" placeholder="Cardholder Name" />
              </div>
              <div class="form">
                <label for="card_number">Card Number</label>
                <input type="text" min="0" name="card_number" id="card_number" placeholder="Card Number" />
              </div>
              <div class="form">
                <label for="expiry_date">Expiry Date</label>
                <input type="date" name="expiry_date" id="expiry_date" placeholder="MM/YY" />
              </div>
              <div class="buttons">
                <button type="submit" class="btn btn--full place_order_btn">
                  Place Order
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>

</html>