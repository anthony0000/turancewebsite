document.addEventListener("DOMContentLoaded", () => {
  const site = document.querySelector(".ed-site");
  if (!site) return;

  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const menuButton = document.querySelector("[data-menu-button]");
  const menu = document.getElementById("ed-nav");
  let lastFocused = null;

  function closeMenu(restoreFocus = false) {
    if (!menu || !menuButton) return;
    menu.classList.remove("is-open");
    menuButton.setAttribute("aria-expanded", "false");
    menuButton.setAttribute("aria-label", "Open menu");
    if (restoreFocus && lastFocused instanceof HTMLElement) lastFocused.focus();
  }

  if (menu && menuButton) {
    menuButton.addEventListener("click", () => {
      const isOpening = menuButton.getAttribute("aria-expanded") !== "true";
      if (!isOpening) {
        closeMenu();
        return;
      }
      lastFocused = document.activeElement;
      menu.classList.add("is-open");
      menuButton.setAttribute("aria-expanded", "true");
      menuButton.setAttribute("aria-label", "Close menu");
      menu.querySelector("a")?.focus();
    });
    menu.querySelectorAll("a").forEach((link) => link.addEventListener("click", () => closeMenu()));
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && menu.classList.contains("is-open")) closeMenu(true);
      if (event.key !== "Tab" || !menu.classList.contains("is-open")) return;
      const links = [...menu.querySelectorAll("a")];
      if (event.shiftKey && document.activeElement === links[0]) {
        event.preventDefault();
        menuButton.focus();
      } else if (!event.shiftKey && document.activeElement === menuButton) {
        event.preventDefault();
        links[0].focus();
      }
    });
    window.addEventListener("resize", () => {
      if (window.innerWidth > 900) closeMenu();
    });
  }

  if (!reducedMotion.matches && "IntersectionObserver" in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.08, rootMargin: "0px 0px -10px 0px" });
    site.querySelectorAll("[data-reveal]").forEach((target) => {
      if (target.getBoundingClientRect().top <= window.innerHeight + 24) return;
      target.classList.add("ed-reveal-pending");
      observer.observe(target);
    });
  }

  document.addEventListener("click", (event) => {
    const link = event.target.closest("[data-conversion]");
    if (!link) return;
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      event: "conversion_intent",
      conversion_name: link.getAttribute("data-conversion"),
      destination: link.getAttribute("href") || "",
      page_path: window.location.pathname,
    });
  });
});
