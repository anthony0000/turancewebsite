function anchorOffsetFor(target) {
  if (target && target.id === "contact-form") {
    return 0;
  }

  const header = document.querySelector(".tt-header");
  return header ? header.getBoundingClientRect().height + 18 : 18;
}

function anchorPositionFor(target) {
  if (target && target.id === "contact-form") {
    return document.querySelector(".tt-contact-hero")
      || target;
  }

  return target;
}

function syncHashState() {
  return window.location.hash
    ? document.getElementById(window.location.hash.slice(1))
    : null;
}

document.addEventListener("DOMContentLoaded", function () {
  syncHashState();

  window.addEventListener("hashchange", syncHashState);

  const header = document.querySelector(".tt-header");

  if (!header) {
    return;
  }

  let frameRequested = false;

  function updateHeader() {
    header.classList.toggle("is-scrolled", window.scrollY > 24);
    frameRequested = false;
  }

  function handleScroll() {
    if (frameRequested) {
      return;
    }

    frameRequested = true;
    window.requestAnimationFrame(updateHeader);
  }

  updateHeader();
  window.addEventListener("scroll", handleScroll, { passive: true });
});

document.addEventListener("DOMContentLoaded", function () {
  const openButton = document.querySelector("[data-menu-open]");
  const closeButton = document.querySelector("[data-menu-close]");
  const navigation = document.querySelector("[data-mobile-navigation]");
  const backdrop = document.querySelector("[data-menu-backdrop]");

  if (!openButton || !closeButton || !navigation || !backdrop) {
    return;
  }

  let previouslyFocused = null;

  const focusableSelector = [
    "a[href]",
    "button:not([disabled])",
    "[tabindex]:not([tabindex='-1'])",
  ].join(",");

  function setMenuState(isOpen) {
    if (isOpen) {
      const rect = openButton.getBoundingClientRect();
      const navigationRect = navigation.getBoundingClientRect();
      const originX = rect.left + rect.width / 2 - navigationRect.left;
      const originY = rect.top + rect.height / 2 - navigationRect.top;
      navigation.style.setProperty("--menu-origin-x", originX.toFixed(1) + "px");
      navigation.style.setProperty("--menu-origin-y", originY.toFixed(1) + "px");
    }

    navigation.classList.toggle("is-open", isOpen);
    navigation.setAttribute("aria-hidden", String(!isOpen));
    navigation.inert = !isOpen;
    openButton.setAttribute("aria-expanded", String(isOpen));
    openButton.setAttribute("aria-label", isOpen ? "Close navigation menu" : "Open navigation menu");
    backdrop.hidden = !isOpen;
    document.body.classList.toggle("tt-menu-open", isOpen);

    if (isOpen) {
      previouslyFocused = document.activeElement;
      window.requestAnimationFrame(function () {
        closeButton.focus();
      });
      return;
    }

    if (previouslyFocused instanceof HTMLElement) {
      previouslyFocused.focus();
    }
  }

  function handleKeydown(event) {
    if (!navigation.classList.contains("is-open")) {
      return;
    }

    if (event.key === "Escape") {
      event.preventDefault();
      setMenuState(false);
      return;
    }

    if (event.key !== "Tab") {
      return;
    }

    const focusable = Array.from(navigation.querySelectorAll(focusableSelector));
    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  }

  openButton.addEventListener("click", function () {
    setMenuState(openButton.getAttribute("aria-expanded") !== "true");
  });

  closeButton.addEventListener("click", function () {
    setMenuState(false);
  });

  backdrop.addEventListener("click", function () {
    setMenuState(false);
  });

  navigation.querySelectorAll("a").forEach(function (link) {
    link.addEventListener("click", function () {
      setMenuState(false);
    });
  });

  document.addEventListener("keydown", handleKeydown);
});

document.addEventListener("DOMContentLoaded", function () {
  const hero = document.querySelector(".tt-hero");
  const glow = document.querySelector("[data-cursor-glow]");
  const tilt = document.querySelector("[data-tilt]");
  const sheen = document.querySelector("[data-sheen]");
  const parallaxLayers = Array.from(document.querySelectorAll("[data-parallax-layer]"));

  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const hasFinePointer = window.matchMedia("(pointer: fine)").matches;

  if (hero && !prefersReducedMotion && hasFinePointer) {
    const depth = {
      curves: { x: 12, y: 8 },
      visual: { x: -20, y: -14 },
    };

    let targetX = 0;
    let targetY = 0;
    let currentX = 0;
    let currentY = 0;
    let glowTargetX = 0;
    let glowTargetY = 0;
    let glowX = 0;
    let glowY = 0;
    let rafId = null;

    function tick() {
      currentX += (targetX - currentX) * 0.07;
      currentY += (targetY - currentY) * 0.07;
      glowX += (glowTargetX - glowX) * 0.12;
      glowY += (glowTargetY - glowY) * 0.12;

      parallaxLayers.forEach(function (layer) {
        const layerName = layer.getAttribute("data-parallax-layer");
        const factor = depth[layerName];
        if (!factor) {
          return;
        }
        layer.style.transform = "translate3d(" + (currentX * factor.x).toFixed(2) + "px, " + (currentY * factor.y).toFixed(2) + "px, 0)";
      });

      if (glow) {
        glow.style.transform = "translate3d(" + glowX.toFixed(2) + "px, " + glowY.toFixed(2) + "px, 0)";
      }

      if (tilt) {
        const rotateY = currentX * 16;
        const rotateX = currentY * -12;
        tilt.style.transform = "rotateX(" + rotateX.toFixed(2) + "deg) rotateY(" + rotateY.toFixed(2) + "deg)";
      }

      if (sheen) {
        sheen.style.transform = "translate3d(" + (currentX * 32).toFixed(1) + "%, " + (currentY * 32).toFixed(1) + "%, 0)";
      }

      const settled =
        Math.abs(targetX - currentX) < 0.001 &&
        Math.abs(targetY - currentY) < 0.001 &&
        Math.abs(glowTargetX - glowX) < 0.5 &&
        Math.abs(glowTargetY - glowY) < 0.5;

      rafId = settled ? null : window.requestAnimationFrame(tick);
    }

    hero.addEventListener("pointermove", function (event) {
      const rect = hero.getBoundingClientRect();
      targetX = ((event.clientX - rect.left) / rect.width) * 2 - 1;
      targetY = ((event.clientY - rect.top) / rect.height) * 2 - 1;
      glowTargetX = event.clientX - rect.left;
      glowTargetY = event.clientY - rect.top;

      if (glow) {
        glow.classList.add("is-active");
      }

      if (!rafId) {
        rafId = window.requestAnimationFrame(tick);
      }
    });

    hero.addEventListener("pointerleave", function () {
      targetX = 0;
      targetY = 0;

      if (glow) {
        glow.classList.remove("is-active");
      }

      if (!rafId) {
        rafId = window.requestAnimationFrame(tick);
      }
    });
  }

  if (!prefersReducedMotion && hasFinePointer) {
    const magneticStrength = { "tt-primary-button": 0.22, "tt-menu-button": 0.3 };

    document.querySelectorAll("[data-magnetic]").forEach(function (el) {
      const strength = magneticStrength[el.classList[0]] || 0.2;

      el.addEventListener("pointermove", function (event) {
        const rect = el.getBoundingClientRect();
        const relX = event.clientX - rect.left - rect.width / 2;
        const relY = event.clientY - rect.top - rect.height / 2;

        el.style.transition = "box-shadow 240ms ease";
        el.style.transform = "translate3d(" + (relX * strength).toFixed(2) + "px, " + (relY * strength).toFixed(2) + "px, 0)";
      });

      el.addEventListener("pointerleave", function () {
        el.style.transition = "";
        el.style.transform = "translate3d(0, 0, 0)";
      });
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const hero = document.querySelector(".tt-hero");
  const canvas = document.querySelector("[data-particle-network]");
  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (!hero || !canvas || prefersReducedMotion) {
    return;
  }

  const ctx = canvas.getContext("2d");
  const dpr = Math.min(window.devicePixelRatio || 1, 2);
  const maxLinkDistance = 130;
  const cursorLinkDistance = 190;
  const cursorRepelDistance = 90;

  let width = 0;
  let height = 0;
  let particles = [];
  const pointer = { x: null, y: null };
  let rafId = null;
  let resizeTimer = null;

  function random(min, max) {
    return Math.random() * (max - min) + min;
  }

  function createParticles() {
    const count = Math.max(28, Math.min(90, Math.floor((width * height) / 9000)));
    particles = [];
    for (let i = 0; i < count; i++) {
      particles.push({
        x: random(0, width),
        y: random(0, height),
        vx: random(-0.25, 0.25),
        vy: random(-0.25, 0.25),
        r: random(1, 2.2),
      });
    }
  }

  function resize() {
    const rect = hero.getBoundingClientRect();
    width = rect.width;
    height = rect.height;
    canvas.style.width = width + "px";
    canvas.style.height = height + "px";
    canvas.width = Math.round(width * dpr);
    canvas.height = Math.round(height * dpr);
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    createParticles();
  }

  function step() {
    ctx.clearRect(0, 0, width, height);

    for (let i = 0; i < particles.length; i++) {
      const p = particles[i];

      p.x += p.vx;
      p.y += p.vy;

      if (p.x < 0 || p.x > width) {
        p.vx *= -1;
        p.x = Math.max(0, Math.min(width, p.x));
      }

      if (p.y < 0 || p.y > height) {
        p.vy *= -1;
        p.y = Math.max(0, Math.min(height, p.y));
      }

      if (pointer.x !== null) {
        const dx = p.x - pointer.x;
        const dy = p.y - pointer.y;
        const dist = Math.hypot(dx, dy);
        if (dist < cursorRepelDistance && dist > 0.01) {
          const push = (1 - dist / cursorRepelDistance) * 3;
          p.x += (dx / dist) * push;
          p.y += (dy / dist) * push;
        }
      }

      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
      ctx.fillStyle = "rgba(201, 147, 33, 0.55)";
      ctx.fill();
    }

    for (let i = 0; i < particles.length; i++) {
      for (let j = i + 1; j < particles.length; j++) {
        const a = particles[i];
        const b = particles[j];
        const dist = Math.hypot(a.x - b.x, a.y - b.y);
        if (dist < maxLinkDistance) {
          const alpha = (1 - dist / maxLinkDistance) * 0.35;
          ctx.beginPath();
          ctx.moveTo(a.x, a.y);
          ctx.lineTo(b.x, b.y);
          ctx.strokeStyle = "rgba(201, 147, 33, " + alpha.toFixed(3) + ")";
          ctx.lineWidth = 1;
          ctx.stroke();
        }
      }
    }

    if (pointer.x !== null) {
      for (let i = 0; i < particles.length; i++) {
        const p = particles[i];
        const dist = Math.hypot(p.x - pointer.x, p.y - pointer.y);
        if (dist < cursorLinkDistance) {
          const alpha = (1 - dist / cursorLinkDistance) * 0.55;
          ctx.beginPath();
          ctx.moveTo(p.x, p.y);
          ctx.lineTo(pointer.x, pointer.y);
          ctx.strokeStyle = "rgba(233, 196, 106, " + alpha.toFixed(3) + ")";
          ctx.lineWidth = 1;
          ctx.stroke();
        }
      }
    }

    rafId = window.requestAnimationFrame(step);
  }

  hero.addEventListener("pointermove", function (event) {
    const rect = hero.getBoundingClientRect();
    pointer.x = event.clientX - rect.left;
    pointer.y = event.clientY - rect.top;
  });

  hero.addEventListener("pointerleave", function () {
    pointer.x = null;
    pointer.y = null;
  });

  window.addEventListener("resize", function () {
    window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(resize, 150);
  });

  document.addEventListener("visibilitychange", function () {
    if (document.hidden) {
      if (rafId) {
        window.cancelAnimationFrame(rafId);
        rafId = null;
      }
      return;
    }
    if (!rafId) {
      rafId = window.requestAnimationFrame(step);
    }
  });

  resize();
  rafId = window.requestAnimationFrame(step);
});

document.addEventListener("DOMContentLoaded", function () {
  const targets = document.querySelectorAll(".tt-hero__title [data-scramble]");

  if (!targets.length) {
    return;
  }

  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (prefersReducedMotion) {
    return;
  }

  const charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!<>-_\\/[]{}=+*^?#";

  function scrambleText(el, finalText, duration) {
    const chars = finalText.split("");
    const revealDelays = chars.map(function (_, index) {
      return (index / chars.length) * duration * 0.7 + Math.random() * duration * 0.3;
    });
    let start = null;

    function frame(timestamp) {
      if (start === null) {
        start = timestamp;
      }
      const elapsed = timestamp - start;
      let output = "";

      for (let i = 0; i < chars.length; i++) {
        if (chars[i] === " ") {
          output += " ";
        } else if (elapsed >= revealDelays[i]) {
          output += chars[i];
        } else {
          output += charset[Math.floor(Math.random() * charset.length)];
        }
      }

      el.textContent = output;

      if (elapsed < duration) {
        window.requestAnimationFrame(frame);
      } else {
        el.textContent = finalText;
      }
    }

    window.requestAnimationFrame(frame);
  }

  targets.forEach(function (el, index) {
    const finalText = el.textContent;
    el.textContent = "";
    window.setTimeout(function () {
      scrambleText(el, finalText, 850);
    }, 200 + index * 260);
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const hasFinePointer = window.matchMedia("(pointer: fine)").matches;

  if (
    prefersReducedMotion ||
    !hasFinePointer ||
    document.querySelector(".tt-detail, .tt-services-overview")
  ) {
    return;
  }

  const ring = document.createElement("div");
  ring.className = "tt-cursor";
  ring.setAttribute("aria-hidden", "true");
  ring.innerHTML =
    '<span class="tt-cursor__scale">' +
    '<svg viewBox="0 0 40 40">' +
    '<circle cx="20" cy="20" r="17" fill="none" stroke="currentColor" stroke-width="1"></circle>' +
    '<path d="M20 1v7M20 32v7M1 20h7M32 20h7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"></path>' +
    "</svg>" +
    "</span>";

  const dot = document.createElement("span");
  dot.className = "tt-cursor-dot";
  dot.setAttribute("aria-hidden", "true");

  document.body.appendChild(ring);
  document.body.appendChild(dot);
  document.body.classList.add("tt-custom-cursor-active");

  let ringX = 0;
  let ringY = 0;
  let targetX = 0;
  let targetY = 0;
  let rafId = null;

  function tick() {
    ringX += (targetX - ringX) * 0.22;
    ringY += (targetY - ringY) * 0.22;

    ring.style.transform = "translate3d(" + ringX.toFixed(2) + "px, " + ringY.toFixed(2) + "px, 0)";

    const settled = Math.abs(targetX - ringX) < 0.05 && Math.abs(targetY - ringY) < 0.05;
    rafId = settled ? null : window.requestAnimationFrame(tick);
  }

  document.addEventListener("pointermove", function (event) {
    targetX = event.clientX;
    targetY = event.clientY;

    dot.style.transform = "translate3d(" + targetX + "px, " + targetY + "px, 0)";

    ring.classList.add("is-visible");
    dot.classList.add("is-visible");

    if (!rafId) {
      rafId = window.requestAnimationFrame(tick);
    }
  });

  document.documentElement.addEventListener("mouseleave", function () {
    ring.classList.remove("is-visible");
    dot.classList.remove("is-visible");
  });

  const interactiveTargets = document.querySelectorAll(".tt-reference-home a, .tt-reference-home button");

  interactiveTargets.forEach(function (el) {
    el.addEventListener("mouseenter", function () {
      ring.classList.add("is-active");
      dot.classList.add("is-active");
    });
    el.addEventListener("mouseleave", function () {
      ring.classList.remove("is-active");
      dot.classList.remove("is-active");
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const ring = document.querySelector("[data-orbit-ring]");
  const dots = Array.from(document.querySelectorAll("[data-orbit-dot]"));
  const spark = document.querySelector("[data-orbit-spark]");
  const wobble = document.querySelector("[data-tilt-wobble]");
  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (!ring || dots.length < 2 || prefersReducedMotion) {
    return;
  }

  const orbits = [
    { speed: 16, startAngle: 0, radiusFactor: 1 },
    { speed: -37, startAngle: 180, radiusFactor: 0.89 },
  ];
  const collisionThresholdDeg = 7;
  const collisionCooldownMs = 2600;
  const pullDurationMs = 560;
  // Phases of the hit, as a fraction of pullDurationMs.
  const phaseContact = 0.22; // rush together
  const phaseHold = 0.3; // hit-stop: freeze at the moment of contact
  const phaseRecoil = 0.68; // bounce apart past the normal orbit position
  const contactPeak = 1.06; // dots interpenetrate slightly for a solid-looking hit
  const recoilPeak = 0.4; // outward kick after the hit

  let radius = 0;
  let centerX = 0;
  let centerY = 0;
  let lastCollisionAt = -Infinity;
  let pullStart = -Infinity;
  let pullMidX = 0;
  let pullMidY = 0;

  function measure() {
    const rect = ring.getBoundingClientRect();
    centerX = rect.width / 2;
    centerY = rect.height / 2;
    radius = rect.width / 2;
  }

  function triggerImpact(posA, posB) {
    ring.classList.add("is-colliding");
    window.setTimeout(function () {
      ring.classList.remove("is-colliding");
    }, 220);

    dots.forEach(function (dot) {
      dot.classList.remove("is-impact");
      void dot.offsetWidth;
      dot.classList.add("is-impact");
      window.setTimeout(function () {
        dot.classList.remove("is-impact");
      }, 600);
    });

    if (spark) {
      const x = (posA.x + posB.x) / 2;
      const y = (posA.y + posB.y) / 2;

      spark.style.setProperty("--spark-x", x.toFixed(2) + "px");
      spark.style.setProperty("--spark-y", y.toFixed(2) + "px");
      spark.classList.remove("is-bursting");
      void spark.offsetWidth;
      spark.classList.add("is-bursting");
    }

    if (wobble) {
      wobble.classList.remove("is-wobbling");
      void wobble.offsetWidth;
      wobble.classList.add("is-wobbling");
    }
  }

  // Pull amount as a fraction of the distance to the impact midpoint:
  // rush in (ease-in), freeze at contact, recoil past zero, then settle.
  function pullAmount(progress) {
    if (progress < phaseContact) {
      const p = progress / phaseContact;
      return contactPeak * p * p;
    }
    if (progress < phaseHold) {
      return contactPeak;
    }
    if (progress < phaseRecoil) {
      const p = (progress - phaseHold) / (phaseRecoil - phaseHold);
      const eased = Math.sin((p * Math.PI) / 2);
      return contactPeak + (-recoilPeak - contactPeak) * eased;
    }
    const p = (progress - phaseRecoil) / (1 - phaseRecoil);
    const eased = 1 - Math.pow(1 - p, 3);
    return -recoilPeak + recoilPeak * eased;
  }

  function step(timestamp) {
    const t = timestamp / 1000;

    const positions = orbits.map(function (orbit) {
      let angle = (orbit.startAngle + orbit.speed * t) % 360;
      if (angle < 0) {
        angle += 360;
      }
      const rad = (angle * Math.PI) / 180;
      const r = radius * orbit.radiusFactor;
      return {
        angle: angle,
        x: centerX + r * Math.cos(rad),
        y: centerY + r * Math.sin(rad),
      };
    });

    let diff = Math.abs(positions[0].angle - positions[1].angle);
    diff = Math.min(diff, 360 - diff);

    if (diff < collisionThresholdDeg && timestamp - lastCollisionAt > collisionCooldownMs) {
      lastCollisionAt = timestamp;
      pullStart = timestamp;
      pullMidX = (positions[0].x + positions[1].x) / 2;
      pullMidY = (positions[0].y + positions[1].y) / 2;

      window.setTimeout(function () {
        triggerImpact(positions[0], positions[1]);
      }, pullDurationMs * phaseContact);
    }

    const sincePull = timestamp - pullStart;
    let renderPositions = positions;

    if (sincePull >= 0 && sincePull < pullDurationMs) {
      const pull = pullAmount(sincePull / pullDurationMs);

      renderPositions = positions.map(function (p) {
        return {
          x: p.x + (pullMidX - p.x) * pull,
          y: p.y + (pullMidY - p.y) * pull,
        };
      });
    }

    dots.forEach(function (dot, index) {
      const p = renderPositions[index];
      dot.style.transform = "translate3d(" + p.x.toFixed(2) + "px, " + p.y.toFixed(2) + "px, 0)";
    });

    window.requestAnimationFrame(step);
  }

  measure();
  window.addEventListener("resize", measure);
  window.requestAnimationFrame(step);
});

document.addEventListener("DOMContentLoaded", function () {
  const revealTargets = Array.from(document.querySelectorAll("[data-reveal]"));
  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (!revealTargets.length) {
    return;
  }

  if (prefersReducedMotion || !("IntersectionObserver" in window)) {
    revealTargets.forEach(function (target) {
      target.classList.add("is-visible");
    });
    return;
  }

  const observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) {
          return;
        }

        const siblings = Array.from(entry.target.parentElement.children).filter(function (child) {
          return child.hasAttribute && child.hasAttribute("data-reveal");
        });
        const index = Math.max(0, siblings.indexOf(entry.target));
        entry.target.style.transitionDelay = Math.min(index * 70, 280) + "ms";
        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target);
      });
    },
    { threshold: 0.14, rootMargin: "0px 0px -8%" }
  );

  revealTargets.forEach(function (target) {
    observer.observe(target);
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const links = Array.from(document.querySelectorAll("[data-nav-link]"));
  const sections = ["about", "services", "work", "insights", "contact"]
    .map(function (id) {
      return document.getElementById(id);
    })
    .filter(Boolean);

  if (!links.length || !sections.length) {
    return;
  }

  let frameRequested = false;

  function setActiveLink() {
    const marker = window.scrollY + Math.min(window.innerHeight * 0.38, 320);
    let activeId = "";

    sections.forEach(function (section) {
      if (section.offsetTop <= marker) {
        activeId = section.id;
      }
    });

    links.forEach(function (link) {
      const isActive = activeId && link.getAttribute("href") === "#" + activeId;
      link.classList.toggle("is-active", Boolean(isActive));
      if (isActive) {
        link.setAttribute("aria-current", "location");
      } else {
        link.removeAttribute("aria-current");
      }
    });

    frameRequested = false;
  }

  window.addEventListener(
    "scroll",
    function () {
      if (!frameRequested) {
        frameRequested = true;
        window.requestAnimationFrame(setActiveLink);
      }
    },
    { passive: true }
  );

  setActiveLink();
});

document.addEventListener("DOMContentLoaded", function () {
  const accordion = document.querySelector("[data-accordion]");

  if (!accordion) {
    return;
  }

  const items = Array.from(accordion.querySelectorAll(".tt-faq__item"));

  function closeItem(item) {
    const button = item.querySelector("button[aria-expanded]");
    const answer = item.querySelector(".tt-faq__answer");

    if (!button || !answer || button.getAttribute("aria-expanded") === "false") {
      return;
    }

    button.setAttribute("aria-expanded", "false");
    item.classList.remove("is-open");

    const onTransitionEnd = function (event) {
      if (event.propertyName !== "grid-template-rows" || item.classList.contains("is-open")) {
        return;
      }
      answer.hidden = true;
      answer.removeEventListener("transitionend", onTransitionEnd);
    };

    answer.addEventListener("transitionend", onTransitionEnd);
    window.setTimeout(function () {
      if (!item.classList.contains("is-open")) {
        answer.hidden = true;
      }
    }, 500);
  }

  function openItem(item) {
    const button = item.querySelector("button[aria-expanded]");
    const answer = item.querySelector(".tt-faq__answer");

    if (!button || !answer) {
      return;
    }

    answer.hidden = false;
    button.setAttribute("aria-expanded", "true");
    window.requestAnimationFrame(function () {
      item.classList.add("is-open");
    });
  }

  items.forEach(function (item) {
    const button = item.querySelector("button[aria-expanded]");
    if (!button) {
      return;
    }

    button.addEventListener("click", function () {
      const isOpen = button.getAttribute("aria-expanded") === "true";
      items.forEach(function (otherItem) {
        if (otherItem !== item) {
          closeItem(otherItem);
        }
      });

      if (isOpen) {
        closeItem(item);
      } else {
        openItem(item);
      }
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const timeline = document.querySelector("[data-process-timeline]");
  const progress = document.querySelector("[data-process-progress]");

  if (!timeline || !progress || window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    return;
  }

  let frameRequested = false;

  function updateProgress() {
    const rect = timeline.getBoundingClientRect();
    const start = window.innerHeight * 0.78;
    const end = window.innerHeight * 0.28;
    const progressValue = Math.max(0, Math.min(1, (start - rect.top) / (start - end + rect.height * 0.25)));
    timeline.style.setProperty("--process-progress", (progressValue * 100).toFixed(2) + "%");
    frameRequested = false;
  }

  window.addEventListener(
    "scroll",
    function () {
      if (!frameRequested) {
        frameRequested = true;
        window.requestAnimationFrame(updateProgress);
      }
    },
    { passive: true }
  );

  updateProgress();
});

document.addEventListener("DOMContentLoaded", function () {
  const carousel = document.querySelector("[data-testimonial-carousel]");

  if (!carousel) {
    return;
  }

  const slides = Array.from(carousel.querySelectorAll("[data-testimonial-slide]"));
  const previousButton = carousel.querySelector("[data-testimonial-previous]");
  const nextButton = carousel.querySelector("[data-testimonial-next]");
  const currentLabel = carousel.querySelector("[data-testimonial-current]");

  if (!slides.length || !previousButton || !nextButton || !currentLabel) {
    return;
  }

  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const decodeCharset = "01ABCDEFGHIJKLMNOPQRSTUVWXYZ#$%&*+-/<>";

  function decodeText(el, duration) {
    if (!el) {
      return;
    }

    if (!el.dataset.finalText) {
      el.dataset.finalText = el.textContent;
    }

    const finalText = el.dataset.finalText;

    if (prefersReducedMotion) {
      el.textContent = finalText;
      return;
    }

    const chars = finalText.split("");
    const revealDelays = chars.map(function (_, index) {
      return (index / chars.length) * duration * 0.75 + Math.random() * duration * 0.25;
    });
    let start = null;

    el.classList.add("is-decoding");

    function frame(timestamp) {
      if (start === null) {
        start = timestamp;
      }
      const elapsed = timestamp - start;
      let output = "";

      for (let i = 0; i < chars.length; i++) {
        if (chars[i] === " ") {
          output += " ";
        } else if (elapsed >= revealDelays[i]) {
          output += chars[i];
        } else {
          output += decodeCharset[Math.floor(Math.random() * decodeCharset.length)];
        }
      }

      el.textContent = output;

      if (elapsed < duration) {
        window.requestAnimationFrame(frame);
      } else {
        el.textContent = finalText;
        el.classList.remove("is-decoding");
      }
    }

    window.requestAnimationFrame(frame);
  }

  function decodeSlide(slide) {
    decodeText(slide.querySelector("blockquote [data-decode]"), 560);
    decodeText(slide.querySelector("footer strong [data-decode]"), 420);
  }

  let currentIndex = 0;
  let touchStartX = null;
  let leaveTimer = null;

  function showSlide(nextIndex) {
    const total = slides.length;
    const targetIndex = (nextIndex + total) % total;

    if (targetIndex === currentIndex) {
      return;
    }

    let diff = targetIndex - currentIndex;
    if (diff > total / 2) diff -= total;
    if (diff < -total / 2) diff += total;
    const forward = diff >= 0;

    carousel.classList.toggle("tt-dir-next", forward);
    carousel.classList.toggle("tt-dir-prev", !forward);

    const outgoing = slides[currentIndex];
    const incoming = slides[targetIndex];

    if (leaveTimer !== null) {
      window.clearTimeout(leaveTimer);
      slides.forEach(function (slide) {
        slide.classList.remove("is-leaving");
      });
    }

    outgoing.classList.remove("is-active");
    outgoing.classList.add("is-leaving");
    outgoing.setAttribute("aria-hidden", "true");
    outgoing.inert = true;

    incoming.classList.remove("is-leaving");
    incoming.classList.add("is-active");
    incoming.setAttribute("aria-hidden", "false");
    incoming.inert = false;

    currentIndex = targetIndex;
    currentLabel.textContent = String(currentIndex + 1).padStart(2, "0");

    decodeSlide(incoming);

    leaveTimer = window.setTimeout(function () {
      outgoing.classList.remove("is-leaving");
      leaveTimer = null;
    }, 650);
  }

  slides.forEach(function (slide, index) {
    slide.inert = index !== currentIndex;
  });

  previousButton.addEventListener("click", function () {
    showSlide(currentIndex - 1);
  });

  nextButton.addEventListener("click", function () {
    showSlide(currentIndex + 1);
  });

  carousel.addEventListener("keydown", function (event) {
    if (event.key !== "ArrowLeft" && event.key !== "ArrowRight") {
      return;
    }

    event.preventDefault();
    showSlide(currentIndex + (event.key === "ArrowRight" ? 1 : -1));
  });

  carousel.addEventListener(
    "touchstart",
    function (event) {
      touchStartX = event.changedTouches[0].clientX;
    },
    { passive: true }
  );

  carousel.addEventListener(
    "touchend",
    function (event) {
      if (touchStartX === null) {
        return;
      }

      const distance = event.changedTouches[0].clientX - touchStartX;
      touchStartX = null;

      if (Math.abs(distance) >= 45) {
        showSlide(currentIndex + (distance < 0 ? 1 : -1));
      }
    },
    { passive: true }
  );
});

document.addEventListener("DOMContentLoaded", function () {
  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const interruptionKeys = new Set(["ArrowUp", "ArrowDown", "PageUp", "PageDown", "Home", "End", " "]);
  let animationFrame = null;

  function cancelScroll() {
    if (animationFrame !== null) {
      window.cancelAnimationFrame(animationFrame);
      animationFrame = null;
      document.documentElement.style.scrollBehavior = "";
    }
  }

  function easeInOutCubic(progress) {
    return progress < 0.5
      ? 4 * progress * progress * progress
      : 1 - Math.pow(-2 * progress + 2, 3) / 2;
  }

  function scrollToTarget(target, hash) {
    cancelScroll();
    window.history.pushState(null, "", hash);
    syncHashState();

    const startY = window.scrollY;
    const documentHeight = document.documentElement.scrollHeight;
    const positionTarget = anchorPositionFor(target);
    const destination = Math.max(
      0,
      Math.min(
        positionTarget.getBoundingClientRect().top + startY - anchorOffsetFor(target),
        documentHeight - window.innerHeight
      )
    );
    const distance = destination - startY;

    if (prefersReducedMotion.matches || Math.abs(distance) < 2) {
      window.scrollTo(0, destination);
      return;
    }

    const duration = Math.max(480, Math.min(920, 420 + Math.abs(distance) * 0.16));
    const startTime = performance.now();
    document.documentElement.style.scrollBehavior = "auto";

    function step(now) {
      const progress = Math.min(1, (now - startTime) / duration);
      window.scrollTo(0, startY + distance * easeInOutCubic(progress));

      if (progress < 1) {
        animationFrame = window.requestAnimationFrame(step);
        return;
      }

      animationFrame = null;
      document.documentElement.style.scrollBehavior = "";
    }

    animationFrame = window.requestAnimationFrame(step);
  }

  document.addEventListener("click", function (event) {
    const link = event.target.closest("a[href*='#']");
    if (!link || link.classList.contains("tt-skip-link") || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
      return;
    }

    const url = new URL(link.href, window.location.href);
    const sameDocument = url.origin === window.location.origin && url.pathname === window.location.pathname;
    if (!sameDocument || !url.hash || url.hash === "#") {
      return;
    }

    let target;
    try {
      target = document.querySelector(url.hash);
    } catch (error) {
      return;
    }

    if (!target) {
      return;
    }

    event.preventDefault();
    scrollToTarget(target, url.hash);
  });

  window.addEventListener("wheel", cancelScroll, { passive: true });
  window.addEventListener("touchstart", cancelScroll, { passive: true });
  document.addEventListener("keydown", function (event) {
    if (interruptionKeys.has(event.key)) {
      cancelScroll();
    }
  });
});

window.addEventListener("load", function () {
  function applyHashTarget() {
    const target = syncHashState();
    if (!target) {
      return;
    }

    const previousBehaviour = document.documentElement.style.scrollBehavior;
    document.documentElement.style.scrollBehavior = "auto";
    const positionTarget = anchorPositionFor(target);
    const targetTop = positionTarget.getBoundingClientRect().top + window.scrollY;
    window.scrollTo(0, Math.max(0, targetTop - anchorOffsetFor(target)));
    const revealRoot = target.closest("[data-reveal]");
    [target, revealRoot].concat(Array.from(target.querySelectorAll("[data-reveal]"))).filter(Boolean).forEach(function (element) {
      if (element.hasAttribute && element.hasAttribute("data-reveal")) {
        element.style.transitionDelay = "0ms";
        element.classList.add("is-visible");
      }
    });
    window.requestAnimationFrame(function () {
      document.documentElement.style.scrollBehavior = previousBehaviour;
    });
  }

  applyHashTarget();

  // Browsers can restore the native hash position after the load event. Re-apply
  // once after that restoration so a fixed header never covers the contact hero.
  window.setTimeout(applyHashTarget, 80);
  window.setTimeout(applyHashTarget, 260);
});

document.addEventListener("DOMContentLoaded", function () {
  const mobileSalesBar = document.querySelector("[data-mobile-sales-bar]");

  if (mobileSalesBar) {
    const syncSalesBar = function () {
      mobileSalesBar.classList.toggle("is-visible", window.scrollY > 420);
    };

    syncSalesBar();
    window.addEventListener("scroll", syncSalesBar, { passive: true });
  }

  document.addEventListener("click", function (event) {
    const conversionLink = event.target.closest("[data-conversion]");
    if (!conversionLink) {
      return;
    }

    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      event: "conversion_intent",
      conversion_name: conversionLink.getAttribute("data-conversion"),
      destination: conversionLink.getAttribute("href") || "",
      page_path: window.location.pathname,
    });
  });
});
