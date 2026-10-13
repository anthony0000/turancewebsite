(() => {
  const robot = document.querySelector('[data-hero-robot]');
  const button = robot?.querySelector('[data-robot-gesture]');
  if (!button) return;

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    button.disabled = true;
    button.setAttribute('aria-label', 'Robot character');
    return;
  }

  let reactionTimer;
  button.addEventListener('click', () => {
    window.clearTimeout(reactionTimer);
    button.classList.remove('is-reacting');
    void button.offsetWidth;
    button.classList.add('is-reacting');
    reactionTimer = window.setTimeout(() => button.classList.remove('is-reacting'), 1800);
  });
})();
