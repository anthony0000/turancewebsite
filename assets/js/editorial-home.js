document.addEventListener("DOMContentLoaded", () => {
  const site = document.querySelector(".ed-site");
  if (!site) return;

  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const header = site.querySelector("[data-header]");
  if (header) {
    const updateHeader = () => header.classList.toggle("is-scrolled", window.scrollY > 96);
    updateHeader();
    window.addEventListener("scroll", updateHeader, { passive: true });
  }
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
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add("is-visible");
        revealObserver.unobserve(entry.target);
      });
    }, { threshold: 0.08, rootMargin: "0px 0px -6% 0px" });
    site.querySelectorAll("[data-reveal]").forEach((target) => {
      if (target.getBoundingClientRect().top <= window.innerHeight + 40) return;
      const siblings = [...target.parentElement.children].filter((child) => child.hasAttribute("data-reveal"));
      if (siblings.length > 1) {
        target.style.setProperty("--ed-reveal-delay", `${Math.min(siblings.indexOf(target), 3) * 85}ms`);
      }
      target.classList.add("ed-reveal-pending");
      revealObserver.observe(target);
    });
  }

  if ("IntersectionObserver" in window) {
    const ambientObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        entry.target.classList.toggle("is-active", entry.isIntersecting);
      });
    }, { rootMargin: "-10% 0px -10% 0px" });
    site.querySelectorAll(".ed-hero, .ed-section").forEach((section) => {
      ambientObserver.observe(section);
    });
    site.classList.add("ed-ambient-ready");
    site.classList.toggle("ed-ambient-paused", document.hidden);
    document.addEventListener("visibilitychange", () => {
      site.classList.toggle("ed-ambient-paused", document.hidden);
    });
  }

  site.querySelectorAll(".ed-faq__items details").forEach((details) => {
    const summary = details.querySelector("summary");
    const answer = details.querySelector("p");
    if (!summary || !answer || typeof answer.animate !== "function") return;
    let animation = null;
    let finishTimer = null;

    summary.addEventListener("click", (event) => {
      if (reducedMotion.matches) return;
      event.preventDefault();
      const opening = !details.open;
      const startHeight = details.open ? answer.getBoundingClientRect().height : 0;
      const startOpacity = details.open ? getComputedStyle(answer).opacity : 0;
      const startMargin = details.open ? getComputedStyle(answer).marginBottom : "0px";
      window.clearTimeout(finishTimer);
      animation?.cancel();
      if (opening) details.open = true;
      const endHeight = opening ? answer.scrollHeight : 0;
      answer.style.overflow = "hidden";
      animation = answer.animate([
        { height: `${startHeight}px`, opacity: startOpacity, marginBottom: startMargin },
        { height: `${endHeight}px`, opacity: opening ? 1 : 0, marginBottom: opening ? "24px" : "0px" },
      ], { duration: 360, easing: "cubic-bezier(.2,.7,.2,1)" });
      const currentAnimation = animation;
      const settle = () => {
        if (animation !== currentAnimation) return;
        window.clearTimeout(finishTimer);
        currentAnimation.cancel();
        if (!opening) details.open = false;
        answer.style.overflow = "";
        animation = null;
      };
      currentAnimation.onfinish = settle;
      finishTimer = window.setTimeout(settle, 430);
    });
  });

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
