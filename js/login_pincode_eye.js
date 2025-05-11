document.addEventListener("DOMContentLoaded", function () {
  // Add event listeners for password input interactions
  document.querySelectorAll('input[type="password"]').forEach((input) => {
    // Show/hide toggle on input/focus
    input.addEventListener("input", updateToggleVisibility);
    input.addEventListener("focus", updateToggleVisibility);
    input.addEventListener("blur", updateToggleVisibility);
  });

  function updateToggleVisibility(e) {
    const input = e.target;
    const toggle = input
      .closest(".input-group")
      .querySelector(".password-toggle");
    toggle.style.display =
      input.value || document.activeElement === input ? "block" : "none";
  }

  // Existing toggle functionality
  document.querySelectorAll(".password-toggle").forEach((toggle) => {
    toggle.addEventListener("click", (e) => {
      const input = e.target.closest(".input-group").querySelector("input");
      const type = input.type === "password" ? "text" : "password";
      input.type = type;
      e.target.classList.toggle("fa-eye");
      e.target.classList.toggle("fa-eye-slash");
    });
  });
});
