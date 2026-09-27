// Freeze only the pulse uniforms to inspect rendered phases without GPU timing drift.
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const base = process.env.AI_FACE_URL || 'http://127.0.0.1:8093';
const output = 'lara_pro/storage/app/ai-face-review';
fs.mkdirSync(output, { recursive: true });

(async () => {
  const browser = await chromium.launch({ headless: true, args: ['--enable-unsafe-swiftshader'] });
  try {
    const page = await browser.newPage({ reducedMotion: 'reduce' });
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    page.on('console', m => { if (m.type() === 'error' && /shader|WebGL/i.test(m.text())) errors.push(m.text()); });
    await page.route('**/ai-face.js?v=6', async route => {
      const response = await route.fetch();
      const body = (await response.text())
        .replace('pulseTime: animation.time, pulseStrength: animation.strength',
          'pulseTime: { get value() { return window.pulseTime || 0; } }, pulseStrength: { get value() { return window.pulseStrength || 0; } }')
        .replace('loaded = true; renderer.render(scene, camera);',
          'loaded = true; window.renderPulse = () => renderer.render(scene, camera); renderer.render(scene, camera);');
      await route.fulfill({ response, body });
    });
    const results = [];
    for (const width of [1440, 1024, 768, 390, 320]) {
      await page.setViewportSize({ width, height: 1000 });
      await page.goto(base);
      await page.locator('[data-ai-face].is-ready').waitFor({ timeout: 60000 });
      const layout = await page.evaluate(() => {
        const a = document.querySelector('[data-ai-face]').getBoundingClientRect();
        const b = document.querySelector('.tt-hero__content').getBoundingClientRect();
        return { artwork: a.toJSON(), content: b.toJSON(), overlap: a.left < b.right && a.right > b.left && a.top < b.bottom && a.bottom > b.top,
          overflow: document.documentElement.scrollWidth > innerWidth };
      });
      assert(!layout.overlap && !layout.overflow, `Artwork overlaps copy or overflows at ${width}: ${JSON.stringify(layout)}`);
      const frames = await page.evaluate(async () => {
        const canvas = document.querySelector('[data-ai-face] canvas');
        async function capture(time, strength) {
          window.pulseTime = time; window.pulseStrength = strength; window.renderPulse();
          const bitmap = await createImageBitmap(canvas);
          const copy = document.createElement('canvas'); copy.width = canvas.width; copy.height = canvas.height;
          const ctx = copy.getContext('2d'); ctx.drawImage(bitmap, 0, 0); bitmap.close();
          return ctx.getImageData(0, 0, copy.width, copy.height).data;
        }
        const baseline = await capture(0, 0), counts = [];
        for (const time of [0, 1, 2, 8.98, 9.02]) {
          const pixels = await capture(time, 1);
          let changed = 0, face = 0, edge = 0;
          for (let i = 0; i < pixels.length; i += 4) {
            if (Math.max(...[0, 1, 2, 3].map(c => Math.abs(pixels[i + c] - baseline[i + c]))) < 3) continue;
            const x = (i / 4 % canvas.width) / canvas.width, y = Math.floor(i / 4 / canvas.width) / canvas.height;
            changed++;
            if (x > .36 && x < .62 && y > .23 && y < .70) face++;
            if (x < .025 || x > .975 || y < .025 || y > .975) edge++;
          }
          counts.push({ time, changed, face, edge });
        }
        return counts;
      });
      assert(frames.every(f => f.changed > 50), `Pulse disappears at ${width}: ${JSON.stringify(frames)}`);
      assert(frames.every(f => f.face === 0 && f.edge === 0), `Pulse touches face or boundary at ${width}: ${JSON.stringify(frames)}`);
      await page.screenshot({ path: `${output}/pulse-${width}.png` });
      results.push({ width, layout, frames });
    }
    assert.deepEqual(errors, []);
    console.log(JSON.stringify({ passed: true, results }, null, 2));
  } finally { await browser.close(); }
})().catch(e => { console.error(e); process.exitCode = 1; });
