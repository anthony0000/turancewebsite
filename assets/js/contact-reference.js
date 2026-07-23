document.addEventListener("DOMContentLoaded", function () {
  const alert = document.querySelector("[data-form-alert]");
  const form = document.getElementById("contact-form");

  if (alert) {
    window.requestAnimationFrame(function () {
      alert.focus({ preventScroll: true });
      alert.scrollIntoView({ behavior: "smooth", block: "center" });
    });
  }

  if (!form) {
    return;
  }

  form.addEventListener("submit", function (event) {
    if (!form.checkValidity()) {
      return;
    }

    const button = form.querySelector("button[type='submit']");
    if (!button || button.disabled) {
      event.preventDefault();
      return;
    }

    button.disabled = true;
    button.setAttribute("aria-busy", "true");
    const label = button.querySelector(":scope > span");
    if (label) {
      label.textContent = "Sending enquiry";
    }
  });
});
