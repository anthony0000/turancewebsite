// From the repo root; reuse the Playwright install selected by PLAYWRIGHT_MODULE.
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const base = process.env.AI_FACE_URL || 'http://127.0.0.1:8093';
const output = 'lara_pro/storage/app/ai-face-review';
fs.mkdirSync(output, { recursive: true });
const stats = page => page.evaluate(async () => {
  const { instances } = await import('/assets/js/ai-face.js?v=6');
  return instances.get(document.querySelector('[data-ai-face]')).getStats();
});
const opacity = (page, selector) => page.locator(selector).evaluate(el => getComputedStyle(el).opacity);

(async () => {
  const browser = await chromium.launch({ headless: true, args: ['--enable-unsafe-swiftshader'] });
  try {
    const context = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
    const page = await context.newPage();
    const errors = [];
    page.on('pageerror', e => errors.push(e.message));
    let releaseModel;
    const modelGate = new Promise(resolve => { releaseModel = resolve; });
    await page.route('**/female-head.bin', async route => { await modelGate; await route.continue(); });
    await page.goto(base, { waitUntil: 'domcontentloaded' });
    assert.equal(await page.locator('[data-ai-face]').getAttribute('data-formation'), 'pending');
    assert.equal(await opacity(page, '.tt-ai-face__cloud'), '1', 'Inline loading cloud is missing');
    assert.equal(await opacity(page, '.tt-ai-face__fallback'), '0', 'Full face flashes before formation');
    assert(await page.locator('.tt-ai-face__cloud circle').count() > 200);
    await page.screenshot({ path: `${output}/loading-cloud.png` });
    // Record rendered progress in the browser, independent of automation round trips.
    await page.evaluate(async () => {
      const { instances } = await import('/assets/js/ai-face.js?v=6');
      window.formationSamples = [];
      function sample(now) {
        const s = instances.get(document.querySelector('[data-ai-face]'))?.getStats();
        if (s?.loaded) window.formationSamples.push({ at: now, progress: s.formation });
        if (!s?.loaded || s.formation < 1) requestAnimationFrame(sample);
      }
      requestAnimationFrame(sample);
    });
    releaseModel();
    await page.locator('[data-ai-face].is-ready').waitFor({ timeout: 60000 });
    await page.screenshot({ path: `${output}/formation-start.png` });
    await page.waitForTimeout(700);
    await page.screenshot({ path: `${output}/formation-resolving.png` });
    await page.locator('[data-formation="complete"].is-ready').waitFor({ timeout: 15000 });
    await page.waitForFunction(() => window.formationSamples.at(-1)?.progress === 1);
    const samples = await page.evaluate(() => window.formationSamples);
    const duration = samples.at(-1).at - samples[0].at;
    assert(samples[0].progress < .2, 'Formation starts with a completed face');
    assert(samples.some(s => s.progress > .25 && s.progress < .8), 'Formation skipped the silhouette stage');
    assert(duration >= 2300 && duration < 3800, `Formation duration: ${duration}ms`);
    await page.screenshot({ path: `${output}/formation-complete.png` });
    assert.equal((await stats(page)).waveStrength, 1);
    const box = await page.locator('[data-ai-face]').boundingBox();
    await page.mouse.move(box.x + box.width * .55, box.y + box.height * .5);
    await page.waitForTimeout(800);
    assert((await stats(page)).shimmer > .2);
    assert.equal((await stats(page)).formation, 1, 'Pointer dissolves the face');
    await page.waitForFunction(async () => {
      const { instances } = await import('/assets/js/ai-face.js?v=6');
      return instances.get(document.querySelector('[data-ai-face]')).getStats().shimmer < .05;
    }, null, { timeout: 20000 });
    await page.reload({ waitUntil: 'domcontentloaded' });
    assert.equal(await page.locator('[data-ai-face]').getAttribute('data-formation'), 'complete', 'Repeat visit replays formation');
    await page.locator('[data-ai-face].is-ready').waitFor({ timeout: 60000 });
    assert.equal((await stats(page)).formation, 1);
    await page.emulateMedia({ reducedMotion: 'reduce' });
    await page.waitForTimeout(500);
    const still = await stats(page);
    await page.waitForTimeout(500);
    assert.equal((await stats(page)).frames, still.frames);
    assert.equal(still.waveStrength, 0);
    assert.equal(still.formation, 1);
    assert.equal(await page.locator('[data-particle-network]').evaluate(el => el.aiPulse), null);

    const reduced = await browser.newPage({ reducedMotion: 'reduce', viewport: { width: 1440, height: 1000 } });
    await reduced.route('**/female-head.bin', route => route.abort());
    await reduced.goto(base);
    assert.equal(await opacity(reduced, '.tt-ai-face__fallback'), '1');
    assert.equal(await opacity(reduced, '.tt-ai-face__cloud'), '0');
    await reduced.screenshot({ path: `${output}/reduced-first-visit.png` });

    const slow = await browser.newPage();
    let releaseSlow;
    const slowGate = new Promise(resolve => { releaseSlow = resolve; });
    await slow.route('**/ai-face.js*', async route => { await slowGate; await route.abort(); });
    await slow.goto(base, { waitUntil: 'commit' });
    await slow.locator('[data-formation="pending"]').waitFor();
    assert.equal(await opacity(slow, '.tt-ai-face__cloud'), '1');
    await slow.locator('[data-formation="complete"]').waitFor({ timeout: 12000 });
    assert.equal(await opacity(slow, '.tt-ai-face__fallback'), '1', 'Failed module strands the loading cloud');
    releaseSlow();

    const noJS = await browser.newPage({ javaScriptEnabled: false });
    await noJS.goto(base);
    assert.equal(await opacity(noJS, '.tt-ai-face__fallback'), '1');
    assert(await noJS.locator('.tt-ai-face__fallback').evaluate(img => img.complete && img.naturalWidth > 0));
    assert.deepEqual(errors, []);
    console.log(JSON.stringify({ passed: true, durationMs: Math.round(duration), samples: samples.length,
      reducedMotion: still, screenshots: output }, null, 2));
  } finally { await browser.close(); }
})().catch(e => { console.error(e); process.exitCode = 1; });
