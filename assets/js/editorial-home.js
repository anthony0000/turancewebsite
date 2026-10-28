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
  const hero = site.querySelector(".ed-hero");
  const heroPicture = hero?.querySelector(".ed-hero__art picture");
  const finePointer = window.matchMedia("(hover: hover) and (pointer: fine) and (min-width: 651px)");
  if (hero && heroPicture && !reducedMotion.matches && finePointer.matches) {
    let currentX = 0;
    let currentY = 0;
    let targetX = 0;
    let targetY = 0;
    let frame = null;

    const animatePointer = () => {
      currentX += (targetX - currentX) * 0.1;
      currentY += (targetY - currentY) * 0.1;
      if (Math.abs(targetX - currentX) < 0.05) currentX = targetX;
      if (Math.abs(targetY - currentY) < 0.05) currentY = targetY;
      heroPicture.style.setProperty("--ed-pointer-x", `${currentX.toFixed(2)}px`);
      heroPicture.style.setProperty("--ed-pointer-y", `${currentY.toFixed(2)}px`);
      frame = currentX === targetX && currentY === targetY ? null : window.requestAnimationFrame(animatePointer);
    };
    const schedulePointer = () => {
      if (frame === null) frame = window.requestAnimationFrame(animatePointer);
    };
    hero.addEventListener("pointermove", (event) => {
      const bounds = hero.getBoundingClientRect();
      targetX = ((event.clientX - bounds.left) / bounds.width - 0.5) * 28;
      targetY = ((event.clientY - bounds.top) / bounds.height - 0.5) * 18;
      schedulePointer();
    }, { passive: true });
    hero.addEventListener("pointerleave", () => {
      targetX = 0;
      targetY = 0;
      schedulePointer();
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
