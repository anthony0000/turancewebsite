document.addEventListener("DOMContentLoaded", () => {
  const trigger = document.querySelector("[data-menu-open]");
  const closeButton = document.querySelector("[data-menu-close]");
  const panel = document.querySelector("[data-mobile-navigation]");
  const backdrop = document.querySelector("[data-menu-backdrop]");
  const curtain = document.querySelector("[data-menu-curtain]");
  if (!trigger || !closeButton || !panel || !backdrop || !curtain) return;

  const focusableSelector = "a[href], button:not([disabled]), [tabindex]:not([tabindex='-1'])";
  let previousFocus = null;

  function setOpen(open, restoreFocus = true) {
    if (open === panel.classList.contains("is-open")) return;

    if (open) previousFocus = document.activeElement;
    backdrop.classList.toggle("is-open", open);
    curtain.classList.toggle("is-open", open);
    panel.classList.toggle("is-open", open);
    panel.inert = !open;
    panel.setAttribute("aria-hidden", String(!open));
    trigger.setAttribute("aria-expanded", String(open));
    trigger.setAttribute("aria-label", open ? "Close navigation menu" : "Open navigation menu");
    document.body.classList.toggle("tt-menu-open", open);

    if (open) {
      closeButton.focus({ preventScroll: true });
    } else if (restoreFocus && previousFocus instanceof HTMLElement) {
      previousFocus.focus({ preventScroll: true });
    }
  }

  trigger.addEventListener("click", () => setOpen(!panel.classList.contains("is-open")));
  closeButton.addEventListener("click", () => setOpen(false));
  backdrop.addEventListener("click", () => setOpen(false));
  panel.querySelectorAll("a").forEach((link) => link.addEventListener("click", () => setOpen(false, false)));

  document.addEventListener("keydown", (event) => {
    if (!panel.classList.contains("is-open")) return;
    if (event.key === "Escape") {
      event.preventDefault();
      setOpen(false);
      return;
    }
    if (event.key !== "Tab") return;

    const focusable = [...panel.querySelectorAll(focusableSelector)];
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  });

  window.addEventListener("resize", () => {
    if (document.querySelector(".ed-header") && window.innerWidth > 900) setOpen(false);
  });
});
