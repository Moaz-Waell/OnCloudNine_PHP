function changeQuantity(delta) {
  const display = document.getElementById('quantity');
  const input = document.getElementById('quantityInput');
  let current = parseInt(display.textContent);
  
  current = delta === 1 ? current + 1 : Math.max(1, current - 1);
  
  display.textContent = current;
  input.value = current;
}