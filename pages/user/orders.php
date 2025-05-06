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
    <?php include('../../components/sideNav.html'); ?>
    <main class="main-content">
      <!-- Navigation Bar -->
      <h2 class="heading-secondary text-center">Orders</h2>

      <!-- Orders Table -->
      <table class="orders-table">
        <thead>
          <tr class="heading-secondary">
            <th>Order ID</th>
            <th>Date</th>
            <th>Schedule Time</th>
            <th>Total</th>
            <th></th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#123456</td>
            <td>June 1</td>
            <td>12:00 PM</td>
            <td>$5.59</td>
            <td><a href="#" class="view-details">View Details</a></td>
            <td><button class="btn btn--status ordersstatus">In Progress</button></td>
            <td><button class="btn btn--order-again">Cancel Order</button></td>
          </tr>
          <tr>
            <td>#123457</td>
            <td>June 2</td>
            <td>1:00 PM</td>
            <td>$10.99</td>
            <td><a href="#" class="view-details">View Details</a></td>
            <td><button class="btn btn--status ordersstatus">Pending</button></td>
            <td><button class="btn btn--order-again">Cancel Order</button></td>
          </tr>
          <!-- Repeat rows for other orders -->
        </tbody>
      </table>

      <h2 class="heading-secondary padding-left-4rem">Orders History</h2>

      <table class="orders-table">
  <thead>
    <tr class="heading-secondary">
      <th>Order ID</th>
      <th>Time</th>
      <th>Total</th>
      <th></th>
      <th>Status</th>
      <th></th>
      <th>FeedBack</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>#123458</td>
      <td>June 3</td>
      <td>$7.49</td>
      <td><a href="#" class="view-details">View Details</a></td>
      <td><button class="btn btn--status ordersstatus">Cancelled</button></td>
      <td><button class="btn btn--order-again">Order Again</button></td>
      <td>
        <button class="btn btn--order-again">
        <a href="#popupForm">Feed Back</a>
        </button></td>
    </tr>
    <!-- Repeat rows for other orders -->
  </tbody>
</table>

      <!-- Popup Form -->
      <div id="popupForm" class="popup">
        <div class="popup-content">
          <a href="#" class="close-btn">&times;</a>
          <form>
            <p class="question">How do you feel about your order?</p>
            <div class="rating">
              <label>
                <input type="radio" name="rating" value="1">
                <span class="rating-number">1</span>
              </label>
              <label>
                <input type="radio" name="rating" value="2">
                <span class="rating-number">2</span>
              </label>
              <label>
                <input type="radio" name="rating" value="3">
                <span class="rating-number">3</span>
              </label>
              <label>
                <input type="radio" name="rating" value="4">
                <span class="rating-number">4</span>
              </label>
              <label>
                <input type="radio" name="rating" value="5">
                <span class="rating-number">5</span>
              </label>
            </div>
            <button type="submit" class="next-btn">Submit</button>
          </form>
        </div>
      </div>
    </main>
  </div>
</body>

</html>