(() => {
  const robot = document.querySelector('[data-hero-robot]');
  if (!robot) return;
  const button = robot.querySelector('[data-robot-gesture]');
  const frames = [...robot.querySelectorAll('[data-robot-pose]')];
  const hero = robot.closest('section');
  if (!button || !hero || frames.length !== 3) return;

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let current = 'wave';
  let nextFunny = 'thumbs';
  let autoTimer;
  let resetTimer;
  let visible = false;
  let lastGesture = 0;

  const showPose = (pose) => {
    current = pose;
    robot.dataset.gesture = pose;
    frames.forEach((frame) => frame.classList.toggle('is-active', frame.dataset.robotPose === pose));
  };

  const scheduleGesture = () => {
    clearTimeout(autoTimer);
    if (reducedMotion || !visible || document.hidden) return;
    autoTimer = window.setTimeout(() => react(), 5800 + Math.random() * 3600);
  };

  const react = () => {
    clearTimeout(autoTimer);
    clearTimeout(resetTimer);
    const pose = nextFunny;
    nextFunny = pose === 'thumbs' ? 'shrug' : 'thumbs';
    showPose(pose);
    lastGesture = Date.now();

    if (reducedMotion) return;
    button.classList.remove('is-reacting');
    void button.offsetWidth;
    button.classList.add('is-reacting');
    resetTimer = window.setTimeout(() => {
      button.classList.remove('is-reacting');
      showPose('wave');
      scheduleGesture();
    }, 1950);
  };

  button.addEventListener('click', () => {
    if (Date.now() - lastGesture > 450) react();
  });
  button.addEventListener('pointerenter', (event) => {
    if (event.pointerType === 'mouse' && !reducedMotion && Date.now() - lastGesture > 2200) react();
  });

  if (reducedMotion) return;

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

  if ('IntersectionObserver' in window) {
    new IntersectionObserver(([entry]) => {
      visible = entry.isIntersecting;
      if (visible) scheduleGesture();
      else {
        clearTimeout(autoTimer);
        clearTimeout(resetTimer);
        showPose('wave');
      }
    }, { threshold: .2 }).observe(robot);
  } else {
    visible = true;
    scheduleGesture();
  }

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      clearTimeout(autoTimer);
      clearTimeout(resetTimer);
      showPose('wave');
    } else scheduleGesture();
  });
})();
