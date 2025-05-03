let quantity = 1;
      const quantityDisplay = document.getElementById("quantity");
      function changeQuantity(delta) {
        quantity = Math.max(1, quantity + delta);
        quantityDisplay.textContent = quantity;
      }