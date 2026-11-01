document.addEventListener("DOMContentLoaded", () => {
  const scrollToAnchor = (target, behavior = "smooth") => {
    target.scrollIntoView({ behavior, block: "start" });
  };
  const cleanUrl = () => {
    if (window.location.hash) {
      window.history.replaceState(window.history.state, "", window.location.pathname + window.location.search);
    }
  };

  document.addEventListener("click", (event) => {
    if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
    const link = event.target.closest("a[href*='#']");
    if (!link || link.hasAttribute("download") || (link.target && link.target !== "_self")) return;
    const destination = new URL(link.href, window.location.href);
    if (!destination.hash || destination.origin !== window.location.origin || destination.pathname !== window.location.pathname || destination.search !== window.location.search) return;
    const target = document.getElementById(decodeURIComponent(destination.hash.slice(1)));
    if (!target) return;
    event.preventDefault();
    scrollToAnchor(target, window.matchMedia("(prefers-reduced-motion: reduce)").matches ? "auto" : "smooth");
    cleanUrl();
    if (link.classList.contains("ed-skip")) {
      target.focus({ preventScroll: true });
    }
  });

  if (window.location.hash) {
    const target = document.getElementById(decodeURIComponent(window.location.hash.slice(1)));
    if (target) {
      window.requestAnimationFrame(() => {
        scrollToAnchor(target, "auto");
        cleanUrl();
      });
    }
  }

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
