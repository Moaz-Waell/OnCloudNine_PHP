<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add to Cart</title>
  <link rel="stylesheet" href="../../style/pages/user/cart.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

</head>

<body>
  <div class="container">

    <?php include('../../components/sideNav.html'); ?>
    <main class="main-content">
      <!-- Navigation Bar -->
      <nav class="navbar">
        <h2 class="heading-secondary ">Cart</h2>
      </nav>

      <!-- Meal Cart Container -->
      <div class="meal-cart-container">
        <div class="meal-cart">

          <!-- Meal Item 1 -->
          <div class="meal-item">
            <div class="meal-item__info">
              <div class="meal-item__photo">
                <img src="data:image/jpeg;base64,...base64string..." alt="Meal Photo">
              </div>
              <div class="meal-item__content">
                <div class="heading-secondary meal-item__name">Falafel</div>
                <div class="description meal-item__description">Falafel, tomato, cucumber, pickles, tahini, baladi
                  bread.</div>
                <div class="meal-item__price">$5.59</div>
              </div>
              <button class="meal-item__remove-btn">-</button>
            </div>
          </div>

          <!-- Meal Item 2 -->
          <div class="meal-item">
            <div class="meal-item__info">
              <div class="meal-item__photo">
                <img src="data:image/jpeg;base64,...base64string..." alt="Meal Photo">
              </div>
              <div class="meal-item__content">
                <div class="heading-secondary meal-item__name">Red Bull</div>
                <div class="description meal-item__description">The Spring Edition 250ml</div>
                <div class="meal-item__price">$2.99</div>
              </div>
              <button class="meal-item__remove-btn">-</button>
            </div>
          </div>

          <!-- Meal Item 1 -->
          <div class="meal-item">
            <div class="meal-item__info">
              <div class="meal-item__photo">
                <img src="data:image/jpeg;base64,...base64string..." alt="Meal Photo">
              </div>
              <div class="meal-item__content">
                <div class="heading-secondary meal-item__name">Falafel</div>
                <div class="description meal-item__description">Falafel, tomato, cucumber, pickles, tahini, baladi
                  bread.</div>
                <div class="meal-item__price">$5.59</div>
              </div>
              <button class="meal-item__remove-btn">-</button>
            </div>
          </div>

          <!-- Meal Item 1 -->
          <div class="meal-item">
            <div class="meal-item__info">
              <div class="meal-item__photo">
                <img src="data:image/jpeg;base64,...base64string..." alt="Meal Photo">
              </div>
              <div class="meal-item__content">
                <div class="heading-secondary meal-item__name">Falafel</div>
                <div class="description meal-item__description">Falafel, tomato, cucumber, pickles, tahini, baladi
                  bread.</div>
                <div class="meal-item__price">$5.59</div>
              </div>
              <button class="meal-item__remove-btn">-</button>
            </div>
          </div>

          <!-- Meal Item 1 -->
          <div class="meal-item">
            <div class="meal-item__info">
              <div class="meal-item__photo">
                <img src="data:image/jpeg;base64,...base64string..." alt="Meal Photo">
              </div>
              <div class="meal-item__content">
                <div class="heading-secondary meal-item__name">Falafel</div>
                <div class="description meal-item__description">Falafel, tomato, cucumber, pickles, tahini, baladi
                  bread.</div>
                <div class="meal-item__price">$5.59</div>
              </div>
              <button class="meal-item__remove-btn">-</button>
            </div>
          </div>

          <!-- Meal Item 1 -->
          <div class="meal-item">
            <div class="meal-item__info">
              <div class="meal-item__photo">
                <img src="data:image/jpeg;base64,...base64string..." alt="Meal Photo">
              </div>
              <div class="meal-item__content">
                <div class="heading-secondary meal-item__name">Falafel</div>
                <div class="description meal-item__description">Falafel, tomato, cucumber, pickles, tahini, baladi
                  bread.</div>
                <div class="meal-item__price">$5.59</div>
              </div>
              <button class="meal-item__remove-btn">-</button>
            </div>
          </div>

          <!-- Meal Item 1 -->
          <div class="meal-item">
            <div class="meal-item__info">
              <div class="meal-item__photo">
                <img src="data:image/jpeg;base64,...base64string..." alt="Meal Photo">
              </div>
              <div class="meal-item__content">
                <div class="heading-secondary meal-item__name">Falafel</div>
                <div class="description meal-item__description">Falafel, tomato, cucumber, pickles, tahini, baladi
                  bread.</div>
                <div class="meal-item__price">$5.59</div>
              </div>
              <button class="meal-item__remove-btn">-</button>
            </div>
          </div>

        </div>
      </div>
      <!-- Cart Footer Wrapper -->
      <div class="cart-footer-wrapper">
        <div class="cart-footer">
          <div class="total-amount">
            Total Amount: $<span id="total-amount">0.00</span>
          </div>
          <button class="btn btn--full btn--checkout">Checkout</button>
        </div>
      </div>

    </main>
  </div>
</body>

</html>