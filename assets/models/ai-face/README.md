# Turance nanostructured AI face

`female-head.bin` is a real adult female anatomical head and short neck, derived
from MakeHuman's authored hm08 topology and `caucasian-female-young.target`.
It is not a primitive or an image projected onto a sphere. Only head/neck body
faces are retained; helper geometry and the rest of the body are excluded.
One Catmull-Clark subdivision pass produces 17,307 vertices / 34,512 triangles.

## Source and license

MakeHuman Community revision `a8bc2d54ff0ac92e78ff71431b1023eda42bf482`:

- [base.obj](https://github.com/makehumancommunity/makehuman/blob/a8bc2d54ff0ac92e78ff71431b1023eda42bf482/makehuman/data/3dobjs/base.obj)
- [female target](https://github.com/makehumancommunity/makehuman/blob/a8bc2d54ff0ac92e78ff71431b1023eda42bf482/makehuman/data/targets/macrodetails/caucasian-female-young.target)
- Both source files explicitly carry the September 2020 **CC0 1.0** asset dedication.
  Original credits: Manuel Bastioni (target); Data Collection AB, Joel Palmius,
  Jonas Hauquier / MakeHuman Community. See `MAKEHUMAN-LICENSE.md` section C and
  `LICENSE.ASSETS.md` for the full dedication. No MakeHuman program code is bundled.

Source SHA-256:

```text
base.obj       8e761e6624b8f54536409135d1636da63b32486a90d4897f84e121d144f6fb4c
female.target  118379f6e8ba9266247fdb8788a20e1df40a239f97ced0b9905bcbcc74f6e820
```

Download these two source files, then run from the repository root:

```text
node lara_pro/scripts/build-ai-face.mjs /path/to/base.obj /path/to/female.target
```

The compact binary format is documented in the build script; the shipped file is
552,844 bytes. It needs no runtime loader dependency beyond Three.js. Three.js
0.180.0 is pinned and self-hosted in `assets/js/vendor/three`, with its MIT license.
The fallback PNG is a transparent browser capture of this same geometry/material.
There are no external model, texture, CDN, or API requests at runtime.

## Component and tuning

`<x-home.hero-visual />` retains the homepage's original artwork column and rings.
Blade props: `color` (default `#c5ad7c`), `particle-density` (22,000),
`motion-intensity` (1; use 0 for still), `horizontal-limit` (12 degrees),
`vertical-limit` (8 degrees), and `expression-intensity` (1; use 0 to disable
expressions). Limits include the eight-degree resting turn.
The exported `createAIFace(element, overrides)` also returns a `dispose()` method
and read-only `getStats()` instrumentation. Regenerate the fallback after changing
the default appearance so the loading/static artwork remains consistent.

Particles use two GPU point buffers. Touch / low-capability devices halve surface
density and cap DPR at 1.25; other devices cap DPR at 1.75. Sustained slow rendering
reduces DPR and particle draw count once. Intersection and visibility observers
pause rendering offscreen/in background tabs. Reduced motion renders a still pose.
Context loss, load failure, and missing WebGL retain the fallback. Page lifecycle
handlers and `dispose()` release observers, listeners, materials, buffers and the
environment render target. The decorative wrapper/canvas never receives input.

Expressions use a shared GPU deformation for the face, mesh connections and
surface particles: a soft smile, cheek lift and slight brow raise on hover.
A brief acknowledgement nod is limited to once per three seconds. Eyes lead
the damped head movement with a smaller rotation. Anatomical eyelid regions
close during irregular natural blinks; the eyeballs compress beneath the lids.
Touch retains the quiet idle pose and occasional blink. Reduced motion and
zero motion intensity disable all expression animation as well as tracking.

Sales CTAs receive a warmer held smile and a single downward approval nod
(2.6 degrees at default intensity) on hover or keyboard-visible focus. Quote,
WhatsApp and offer conversion markers plus direct email/phone links are recognized;
other sales-oriented links/buttons opt in with `data-ai-sales`. Services, pricing
and contact entry points on the homepage are marked explicitly. Delegated passive
pointer listeners preserve clicks and scrolling; nested labels/icons do not
retrigger the nod, and a 1.4-second cooldown prevents rapid repeated gestures.
Moving away or blurring focus releases the smile. Offscreen and reduced-motion
states suppress approval animation; touch clicks are never delayed for it.

## Reproducible browser checks

Run the existing Laravel app locally. If its local database session credentials
are unavailable, a temporary server may use `SESSION_DRIVER=array` and
`CACHE_STORE=array` without editing `.env`. Run from the repository root with
Playwright available (`PLAYWRIGHT_MODULE` may name an existing installation):

```text
node lara_pro/scripts/verify-ai-face.cjs
node lara_pro/scripts/verify-ai-face.cjs --capture-fallback
```

Set `AI_FACE_URL` to override `http://127.0.0.1:8093`. The second command also
regenerates the default fallback. Evidence is written to
`lara_pro/storage/app/ai-face-review/`. Checks cover rotation bounds, local shimmer,
inactivity reset, offscreen pause/resume, live reduced-motion changes, mobile
layout/DPR, navigation/CTA usability, WebGL fallback, hover expression/relaxation,
natural blinking, restrained eye tracking, sales hover/keyboard approval,
nested-icon deduplication and non-sales navigation exclusion.
