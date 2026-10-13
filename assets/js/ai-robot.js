(() => {
  const robot = document.querySelector('[data-hero-robot]');
  if (!robot) return;

  const button = robot.querySelector('[data-robot-gesture]');
  if (!button) return;

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    button.disabled = true;
    button.setAttribute('aria-label', 'Robot character');
    return;
  }

  let danceTimer;
  button.addEventListener('click', () => {
    clearTimeout(danceTimer);
    button.classList.remove('is-reacting');
    void button.offsetWidth;
    button.classList.add('is-reacting');
    danceTimer = window.setTimeout(() => button.classList.remove('is-reacting'), 1600);
  });
})();
