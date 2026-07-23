document.addEventListener("DOMContentLoaded", function () {
    var page = document.querySelector(".tt-service-page");

    if (!page || typeof window.gsap === "undefined") {
        return;
    }

    var gsap = window.gsap;
    var prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    var hasHover = window.matchMedia("(hover: hover)").matches;
    var hasScrollTrigger = typeof window.ScrollTrigger !== "undefined";

    if (hasScrollTrigger) {
        gsap.registerPlugin(window.ScrollTrigger);
    }

    var hero = page.querySelector(".tt-service-hero");
    var heroKicker = hero ? hero.querySelector(".tt-service-kicker") : null;
    var heroLead = hero ? hero.querySelector(".tt-service-lead") : null;
    var heroActions = hero ? hero.querySelector(".tt-service-actions") : null;
    var heroStage = hero ? hero.querySelector(".tt-service-hero-stage") : null;
    var heroStatCards = hero ? hero.querySelectorAll(".tt-service-stat-card") : [];
    var heroFloatCards = hero ? hero.querySelectorAll(".tt-service-floating-card") : [];
    var heroHalos = hero ? hero.querySelectorAll(".tt-service-hero-halo") : [];
    var processList = page.querySelector(".tt-service-process-list");
    var processProgress = page.querySelector(".tt-service-process-progress span");
    var counters = page.querySelectorAll("[data-service-count]");
    var revealItems = Array.prototype.slice.call(page.querySelectorAll(".tt-service-reveal")).filter(function (item) {
        return !hero || !hero.contains(item);
    });
    var tiltCards = page.querySelectorAll(".tt-service-tilt-card");

    if (!prefersReducedMotion) {
        if (hero) {
            var heroTimeline = gsap.timeline({
                defaults: {
                    ease: "power3.out"
                }
            });

            heroTimeline
                .from(heroKicker, {
                    y: 22,
                    opacity: 0,
                    duration: 0.7
                })
                .from(heroLead, {
                    y: 28,
                    opacity: 0,
                    duration: 0.9
                }, "-=0.35")
                .from(heroActions, {
                    y: 22,
                    opacity: 0,
                    duration: 0.8
                }, "-=0.45")
                .from(heroStatCards, {
                    y: 26,
                    opacity: 0,
                    duration: 0.8,
                    stagger: 0.12
                }, "-=0.45")
                .from(heroStage, {
                    scale: 0.92,
                    opacity: 0,
                    rotateY: -7,
                    duration: 1.2
                }, "-=0.95")
                .from(heroFloatCards, {
                    y: 18,
                    opacity: 0,
                    duration: 0.75,
                    stagger: 0.1
                }, "-=0.7");
        }

        if (hero && hasHover) {
            var setSpotX = gsap.quickTo(hero, "--tt-spot-x", {
                duration: 0.6,
                ease: "power3.out"
            });
            var setSpotY = gsap.quickTo(hero, "--tt-spot-y", {
                duration: 0.6,
                ease: "power3.out"
            });

            hero.addEventListener("pointermove", function (event) {
                var rect = hero.getBoundingClientRect();
                var x = ((event.clientX - rect.left) / rect.width) * 100;
                var y = ((event.clientY - rect.top) / rect.height) * 100;

                setSpotX(x + "%");
                setSpotY(y + "%");
            });

            hero.addEventListener("pointerleave", function () {
                setSpotX("74%");
                setSpotY("18%");
            });
        }

        heroHalos.forEach(function (halo, index) {
            gsap.to(halo, {
                x: index === 0 ? 28 : -22,
                y: index === 0 ? -22 : 18,
                duration: index === 0 ? 6.8 : 5.6,
                repeat: -1,
                yoyo: true,
                ease: "sine.inOut"
            });
        });

        if (hasScrollTrigger && heroStage) {
            gsap.to(heroStage, {
                y: 34,
                ease: "none",
                scrollTrigger: {
                    trigger: hero,
                    start: "top top",
                    end: "bottom top",
                    scrub: true
                }
            });
        }

        revealItems.forEach(function (item, index) {
            if (!hasScrollTrigger) {
                gsap.from(item, {
                    y: 34,
                    opacity: 0,
                    duration: 0.8,
                    delay: index * 0.04,
                    ease: "power3.out"
                });
                return;
            }

            gsap.from(item, {
                y: 38,
                opacity: 0,
                duration: 1,
                ease: "power3.out",
                scrollTrigger: {
                    trigger: item,
                    start: "top 84%",
                    once: true
                }
            });
        });

        counters.forEach(function (counter) {
            var total = Number(counter.getAttribute("data-service-count") || 0);
            var prefix = counter.getAttribute("data-service-prefix") || "";
            var suffix = counter.getAttribute("data-service-suffix") || "";
            var metric = {
                value: 0
            };

            var render = function () {
                counter.textContent = prefix + Math.round(metric.value) + suffix;
            };

            render();

            gsap.to(metric, {
                value: total,
                duration: 1.8,
                ease: "power2.out",
                snap: {
                    value: 1
                },
                onUpdate: render,
                scrollTrigger: hasScrollTrigger ? {
                    trigger: counter,
                    start: "top 90%",
                    once: true
                } : undefined
            });
        });

        if (hasScrollTrigger && processList && processProgress) {
            gsap.to(processProgress, {
                scaleY: 1,
                ease: "none",
                scrollTrigger: {
                    trigger: processList,
                    start: "top 72%",
                    end: "bottom 72%",
                    scrub: true
                }
            });
        }

        if (hasHover) {
            tiltCards.forEach(function (card) {
                var bounds;

                card.addEventListener("mouseenter", function () {
                    bounds = card.getBoundingClientRect();
                });

                card.addEventListener("mousemove", function (event) {
                    if (!bounds) {
                        bounds = card.getBoundingClientRect();
                    }

                    var percentX = (event.clientX - bounds.left) / bounds.width - 0.5;
                    var percentY = (event.clientY - bounds.top) / bounds.height - 0.5;

                    gsap.to(card, {
                        rotateY: percentX * 10,
                        rotateX: percentY * -10,
                        x: percentX * 6,
                        y: percentY * 6,
                        duration: 0.55,
                        ease: "power2.out",
                        transformPerspective: 1200,
                        transformOrigin: "center center"
                    });
                });

                card.addEventListener("mouseleave", function () {
                    bounds = null;

                    gsap.to(card, {
                        rotateY: 0,
                        rotateX: 0,
                        x: 0,
                        y: 0,
                        duration: 0.8,
                        ease: "power3.out"
                    });
                });
            });
        }
    }

    if (hasScrollTrigger) {
        window.ScrollTrigger.refresh();
    }
});
