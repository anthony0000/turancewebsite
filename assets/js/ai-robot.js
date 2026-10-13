(() => {
  const robot = document.querySelector('[data-hero-robot]');
  if (!robot) return;

  const button = robot.querySelector('[data-robot-gesture]');
  const hero = robot.closest('section');
  if (!button || !hero) return;

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

  hero.addEventListener('pointermove', (event) => {
    if (event.pointerType === 'touch') return;
    const box = hero.getBoundingClientRect();
    const x = Math.max(-1, Math.min(1, (event.clientX - box.left) / box.width * 2 - 1));
    const y = Math.max(-1, Math.min(1, (event.clientY - box.top) / box.height * 2 - 1));
    robot.style.setProperty('--robot-x', `${(x * 10).toFixed(1)}px`);
    robot.style.setProperty('--robot-y', `${(y * 7).toFixed(1)}px`);
    robot.style.setProperty('--robot-tilt', `${(x * 2.5).toFixed(1)}deg`);
  }, { passive: true });

  hero.addEventListener('pointerleave', () => {
    robot.style.removeProperty('--robot-x');
    robot.style.removeProperty('--robot-y');
    robot.style.removeProperty('--robot-tilt');
  });
})();
