// PLAYWRIGHT_MODULE may point to an existing Playwright install. No production dependency.
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const base = process.env.AI_FACE_URL || 'http://127.0.0.1:8093';
const output = 'lara_pro/storage/app/ai-face-review';
fs.mkdirSync(output, { recursive: true });

(async () => {
  const browser = await chromium.launch({ headless: true, args: ['--enable-unsafe-swiftshader'] });
  try {
    const errors = [];
    const page = await browser.newPage({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1 });
    page.on('pageerror', e => errors.push(e.message));
    page.on('console', m => { if (m.type() === 'error' && !m.text().startsWith('Failed to load resource:')) errors.push(m.text()); });
    page.on('response', response => {
      if (response.status() >= 400) {
        if (response.url().startsWith(base)) errors.push(`${response.status()} ${response.url()}`);
        else console.log('External resource:', response.status(), response.url());
      }
    });
    if (process.argv.includes('--capture-fallback')) {
      await page.route(`${base}/__ai-face-capture`, route => route.fulfill({ contentType: 'text/html', body:
        '<!doctype html><style>html,body{margin:0;background:transparent}[data-ai-face]{position:relative;width:960px;height:960px}canvas{display:block}</style>' +
        '<div data-ai-face data-model="/assets/models/ai-face/female-head.bin" data-color="#c5ad7c" data-motion-intensity="0"></div>' +
        '<script type="module" src="/assets/js/ai-face.js?v=6"></script>' }));
      await page.goto(`${base}/__ai-face-capture`);
      await page.locator('[data-state="ready"]').waitFor({ timeout: 60000 });
      await page.locator('canvas').screenshot({ path: 'assets/img/hero/turance-ai-face.png', omitBackground: true });
    }
    await page.goto(base);
    await page.locator('[data-ai-face].is-ready').waitFor({ timeout: 60000 });
    await page.locator('[data-formation="complete"].is-ready').waitFor({ timeout: 20000 });
    const stats = () => page.evaluate(async () => {
      const { instances } = await import('/assets/js/ai-face.js?v=6');
      return instances.get(document.querySelector('[data-ai-face]')).getStats();
    });
    const initial = await stats();
    const face = page.locator('[data-ai-face]');
    const box = await face.boundingBox();
    await page.screenshot({ path: `${output}/desktop-1440.png` });
    for (const [x, y] of [[1, 1], [1438, 998], [box.x + box.width * .55, box.y + box.height * .5]]) {
      await page.mouse.move(x, y, { steps: 15 });
      await page.waitForTimeout(700);
      const s = await stats();
      assert(Math.abs(s.yaw) <= 12.01 && Math.abs(s.pitch) <= 8.01, 'Head exceeds rotation limits');
      if (x > box.x + box.width) assert(s.yaw > 0, 'Face does not turn toward a pointer on the right');
      if (x < box.x) assert(s.yaw < 0, 'Face does not turn toward a pointer on the left');
    }
    const hovered = await stats();
    assert(hovered.shimmer > .3, 'Artwork hover does not shimmer');
    assert(hovered.smile > .3, 'Hover does not produce a welcoming expression');
    assert(Math.abs(hovered.eyeYaw) <= 5, 'Eye tracking is too exaggerated');
    await page.screenshot({ path: `${output}/pointer-hover.png` });
    await page.waitForTimeout(4800);
    const resting = await stats();
    assert(Math.abs(resting.yaw + 8) < .7 && Math.abs(resting.pitch) < .15, 'Inactivity did not restore pose');
    assert(resting.shimmer < .05, 'Shimmer did not settle');
    assert(resting.smile < .05, 'Smile did not relax after inactivity');
    await page.waitForFunction(async () => {
      const { instances } = await import('/assets/js/ai-face.js?v=6');
      return instances.get(document.querySelector('[data-ai-face]')).getStats().blinkCount > 0;
    }, null, { timeout: 20000 });
    const quote = page.locator('[data-conversion="home_hero_quote"]');
    const approvalBefore = (await stats()).approvalCount;
    await quote.hover();
    await page.waitForTimeout(650);
    const salesHover = await stats();
    assert(salesHover.salesActive && salesHover.smile > .4, 'Sales CTA does not produce a smile');
    assert.equal(salesHover.approvalCount, approvalBefore + 1, 'Sales CTA does not trigger one approval nod');
    assert(salesHover.approvalNod > 0 && salesHover.approvalNod <= 3.25, 'Approval nod is missing or excessive');
    await quote.locator('svg').hover();
    assert.equal((await stats()).approvalCount, salesHover.approvalCount, 'Button icon retriggers approval');
    await page.screenshot({ path: `${output}/sales-approval.png` });
    await page.locator('.tt-hero__secondary').hover();
    assert((await stats()).salesActive, 'Pricing link does not trigger approval');
    await page.locator('[data-conversion="header_quote"]').hover();
    assert((await stats()).salesActive, 'Header quote does not trigger approval');
    const approvalAfter = (await stats()).approvalCount;
    await page.locator('.tt-header__nav a').first().hover();
    assert(!(await stats()).salesActive, 'Non-sales navigation incorrectly triggers approval');
    assert.equal((await stats()).approvalCount, approvalAfter);
    await page.waitForFunction(async () => {
      const { instances } = await import('/assets/js/ai-face.js?v=6');
      return instances.get(document.querySelector('[data-ai-face]')).getStats().smile < .05;
    }, null, { timeout: 15000 });
    await quote.focus();
    await page.keyboard.press('Tab');
    await page.waitForTimeout(650);
    const salesFocus = await stats();
    assert(salesFocus.salesActive && salesFocus.smile > .4, 'Keyboard-focused pricing link does not get approval');
    await page.evaluate(() => document.activeElement.blur());
    await page.evaluate(() => window.scrollTo({ top: 1600, behavior: 'instant' }));
    await page.waitForFunction(async () => {
      const { instances } = await import('/assets/js/ai-face.js?v=6');
      return !instances.get(document.querySelector('[data-ai-face]')).getStats().visible;
    });
    // Drain viewport/intersection notifications before measuring a paused interval.
    await page.waitForTimeout(700);
    const offscreen = await stats();
    assert.equal(offscreen.visible, false);
    assert.equal(offscreen.running, false);
    await page.waitForTimeout(500);
    assert.equal((await stats()).frames, offscreen.frames, 'Offscreen renderer keeps running');
    await page.evaluate(() => window.scrollTo({ top: 0, behavior: 'instant' }));
    await page.waitForTimeout(500);
    assert((await stats()).frames > offscreen.frames, 'Renderer did not resume');
    await page.emulateMedia({ reducedMotion: 'reduce' });
    await page.waitForTimeout(500);
    const still = await stats();
    await quote.hover();
    await page.mouse.move(1400, 40);
    await page.waitForTimeout(500);
    assert.equal((await stats()).frames, still.frames, 'Reduced motion still animates');
    assert.equal(still.blink, 0, 'Reduced motion leaves an eye partially closed');
    assert.equal(still.smile, 0, 'Reduced motion does not restore the neutral expression');
    assert.equal((await stats()).approvalCount, still.approvalCount, 'Reduced motion triggers sales nods');
    await page.screenshot({ path: `${output}/reduced-motion.png` });
    assert.equal(await face.evaluate(el => getComputedStyle(el.querySelector('canvas')).pointerEvents), 'none');
    await page.locator('[data-conversion="home_hero_quote"]').click();
    await page.waitForURL('**/contact');
    assert.equal(errors.length, 0, errors.join('\n'));

    const mobile = await browser.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 2, isMobile: true, hasTouch: true });
    const phone = await mobile.newPage();
    await phone.goto(base);
    await phone.locator('[data-ai-face].is-ready').waitFor({ timeout: 60000 });
    await phone.waitForTimeout(1000);
    const layout = await phone.evaluate(() => {
      const face = document.querySelector('[data-ai-face]').getBoundingClientRect();
      const content = document.querySelector('.tt-hero__content').getBoundingClientRect();
      return { overflow: document.documentElement.scrollWidth > innerWidth, overlap: face.top < content.bottom && face.bottom > content.top,
        canvas: document.querySelector('[data-ai-face] canvas').width, width: face.width };
    });
    assert(!layout.overflow && !layout.overlap, `Mobile layout overlaps or overflows: ${JSON.stringify(layout)}`);
    assert(layout.canvas <= layout.width * 1.26, 'Mobile pixel ratio is not capped');
    await phone.screenshot({ path: `${output}/mobile-390.png`, fullPage: false });
    await phone.locator('[data-menu-open]').click();
    assert.equal(await phone.locator('[data-menu-open]').getAttribute('aria-expanded'), 'true');
    await phone.locator('[data-menu-close]').click();
    await phone.locator('[data-conversion="home_hero_quote"]').click();
    await phone.waitForURL('**/contact');
    await mobile.close();

    const fallback = await browser.newPage({ viewport: { width: 1440, height: 1000 } });
    await fallback.addInitScript(() => {
      const original = HTMLCanvasElement.prototype.getContext;
      HTMLCanvasElement.prototype.getContext = function(type, ...args) {
        return type.startsWith('webgl') ? null : original.call(this, type, ...args);
      };
    });
    await fallback.goto(base);
    await fallback.locator('[data-state="static"]').waitFor();
    const image = fallback.locator('.tt-ai-face__fallback');
    assert(await image.evaluate(img => img.complete && img.naturalWidth > 0), 'Static artwork missing');
    assert.equal(await image.evaluate(img => getComputedStyle(img).opacity), '1');
    await fallback.screenshot({ path: `${output}/webgl-fallback.png` });
    console.log(JSON.stringify({ passed: true, initial, hovered, resting, salesHover, salesFocus, offscreen, reducedMotion: still, mobile: layout, errors, screenshots: output }, null, 2));
  } finally { await browser.close(); }
})().catch(e => { console.error(e); process.exitCode = 1; });
