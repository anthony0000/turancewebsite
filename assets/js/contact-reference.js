document.addEventListener("DOMContentLoaded", function () {
  const alert = document.querySelector("[data-form-alert]");
  const form = document.getElementById("project-enquiry-form");

  if (alert) {
    window.requestAnimationFrame(function () {
      alert.focus({ preventScroll: true });
      alert.scrollIntoView({ behavior: "smooth", block: "center" });
    });
  }

  if (!form) {
    return;
  }

  const button = form.querySelector("button[type='submit']");
  const turnstileRequired = form.dataset.turnstileRequired === "true";

  function setTurnstileState() {
    if (!button) {
      return;
    }

    const responseField = form.querySelector("input[name='cf-turnstile-response']");
    const token = form.dataset.turnstileToken
      || (responseField ? responseField.value : "");
    const verified = !turnstileRequired || Boolean(token && token.trim());

    form.dataset.turnstileVerified = verified ? "true" : "false";
    button.disabled = !verified;
    button.setAttribute("aria-disabled", verified ? "false" : "true");
  }

  form.addEventListener("contact-turnstile-state", setTurnstileState);
  setTurnstileState();

  form.addEventListener("submit", function (event) {
    if (!form.checkValidity()) {
      return;
    }

    if (!button || button.disabled) {
      event.preventDefault();
      return;
    }

    if (turnstileRequired && form.dataset.turnstileVerified !== "true") {
      event.preventDefault();
      setTurnstileState();
      return;
    }

    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      event: "generate_lead",
      lead_topic: form.elements.topic ? form.elements.topic.value : "",
      page_path: window.location.pathname,
    });

    button.disabled = true;
    button.setAttribute("aria-busy", "true");
    const label = button.querySelector(":scope > span");
    if (label) {
      label.textContent = "Sending enquiry";
    }
  });
});
