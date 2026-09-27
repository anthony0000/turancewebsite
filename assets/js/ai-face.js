// Three.js r180, self-hosted. Public component API: createAIFace(element, overrides).
import * as THREE from './vendor/three/three.module.min.js';

const clamp = THREE.MathUtils.clamp;
const radians = THREE.MathUtils.degToRad;
const number = (value, fallback, min, max) => clamp(Number.isFinite(+value) ? +value : fallback, min, max);
const instances = new Map();
const formationKey = 'tt-ai-face-formed-v1';
function hasFormed() {
  try { return window.__ttAIFaceFormed || localStorage.getItem(formationKey) === '1'; }
  catch { return !!window.__ttAIFaceFormed; }
}
function rememberFormation() {
  window.__ttAIFaceFormed = true;
  try { localStorage.setItem(formationKey, '1'); } catch { /* Memory covers this document when storage is unavailable. */ }
}
const salesSelector = '[data-ai-sales], [data-conversion$="_quote"], [data-conversion$="_whatsapp"], [data-conversion*="anniversary_offer"], a[href^="mailto:"], a[href^="tel:"], a[href^="https://wa.me/"]';

// Reproducible particles keep the live artwork and its captured fallback consistent.
function randomGenerator() {
  let seed = 57291;
  return () => { seed = (Math.imul(seed, 1664525) + 1013904223) >>> 0; return seed / 4294967296; };
}

function studioEnvironment(renderer) {
  const width = 256, height = 128, data = new Float32Array(width * height * 4);
  for (let y = 0; y < height; y++) for (let x = 0; x < width; x++) {
    const u = x / width, v = y / height;
    const panel = (cx, cy, sx, sy) => Math.exp(-(((u - cx) / sx) ** 6 + ((v - cy) / sy) ** 6));
    const ivory = panel(.24, .38, .09, .24) * 3.2 + panel(.75, .35, .055, .29) * 2.4;
    const gold = panel(.52, .52, .13, .16) * .75;
    const i = (y * width + x) * 4;
    data[i] = .19 + ivory + gold;
    data[i + 1] = .17 + ivory * .96 + gold * .72;
    data[i + 2] = .13 + ivory * .86 + gold * .36;
    data[i + 3] = 1;
  }
  const texture = new THREE.DataTexture(data, width, height, THREE.RGBAFormat, THREE.FloatType);
  texture.mapping = THREE.EquirectangularReflectionMapping;
  texture.needsUpdate = true;
  const pmrem = new THREE.PMREMGenerator(renderer);
  const target = pmrem.fromEquirectangular(texture);
  texture.dispose(); pmrem.dispose();
  return target;
}

// The same deformation runs on skin, cell edges and particles so expressions
// never separate the nanostructure from the authored facial geometry.
const expressionShader = `
  uniform float faceSmile;
  uniform float faceBlink;
  uniform float faceBrow;
  vec3 expressFace(vec3 p) {
    float front = smoothstep(.60, .78, p.z);
    float corner = exp(-pow((abs(p.x) - .22) / .16, 2.0) - pow((p.y + .50) / .15, 2.0)) * front;
    float cheek = exp(-pow((abs(p.x) - .39) / .20, 2.0) - pow((p.y + .15) / .23, 2.0)) * front;
    float brow = exp(-pow((abs(p.x) - .29) / .20, 2.0) - pow((p.y - .36) / .12, 2.0)) * front;
    float lid = (1.0 - smoothstep(.10, .20, abs(abs(p.x) - .2885)))
      * (1.0 - smoothstep(.075, .18, abs(p.y - .1749))) * front;
    p.y += faceSmile * (corner * .065 + cheek * .018) + faceBrow * brow * .025;
    p.x += sign(p.x) * corner * faceSmile * .018;
    p.y = mix(p.y, .1749, lid * faceBlink);
    return p;
  }
`;

function addExpression(shader, expression) {
  shader.uniforms.faceSmile = expression.smile;
  shader.uniforms.faceBlink = expression.blink;
  shader.uniforms.faceBrow = expression.brow;
  shader.vertexShader = expressionShader + shader.vertexShader;
  shader.vertexShader = shader.vertexShader.replace('#include <begin_vertex>', '#include <begin_vertex>\ntransformed = expressFace(transformed);');
}

function nanoSurface(color, hover, expression, animation) {
  const material = new THREE.MeshStandardMaterial({ color, metalness: .8, roughness: .4 });
  material.onBeforeCompile = shader => {
    shader.uniforms.nanoPointer = hover.point;
    shader.uniforms.nanoHover = hover.strength;
    shader.uniforms.formation = animation.formation;
    shader.vertexShader = 'varying vec3 nanoPosition;\n' + shader.vertexShader;
    shader.vertexShader = shader.vertexShader.replace('#include <begin_vertex>', '#include <begin_vertex>\nnanoPosition = position;');
    addExpression(shader, expression);
    shader.fragmentShader = `varying vec3 nanoPosition;
      uniform vec3 nanoPointer;
      uniform float nanoHover;
      uniform float formation;
      float nanoHash(vec3 p) { return fract(sin(dot(p, vec3(127.1,311.7,74.7))) * 43758.5453); }
    ` + shader.fragmentShader;
    shader.fragmentShader = shader.fragmentShader.replace('#include <color_fragment>', `
      #include <color_fragment>
      vec3 cell = nanoPosition * 95.0;
      vec3 grid = abs(fract(cell) - .5);
      float seam = smoothstep(.445, .495, max(grid.x, grid.y));
      float grain = nanoHash(floor(cell));
      // Outline first, then facial detail, then a continuous central surface.
      float center = (1.0 - smoothstep(.25, .72, abs(nanoPosition.x)))
        * (1.0 - smoothstep(.65, 1.12, abs(nanoPosition.y)));
      float resolved = smoothstep(.26 + center * .20, .65 + center * .27, formation);
      if (formation < .999 && grain >= resolved) discard;
      diffuseColor.rgb *= 1.0 - seam * .24 + (grain - .5) * .2;
      float crown = smoothstep(.88, 1.27, nanoPosition.y);
      float neck = 1.0 - smoothstep(-1.27, -.86, nanoPosition.y);
      float temple = smoothstep(.59, .86, abs(nanoPosition.x)) * smoothstep(.25, .66, nanoPosition.y);
      float erosion = max(max(crown * .86, neck * .96), temple * .65);
      if (grain < erosion) discard;
      float shimmer = exp(-dot(nanoPosition - nanoPointer, nanoPosition - nanoPointer) * 20.0) * nanoHover;
      diffuseColor.rgb += vec3(.19, .14, .06) * shimmer;
    `);
  };
  return material;
}

function particles(geometry, count, hover, color, expression, animation, fringe = false) {
  const random = randomGenerator(), positions = [], normals = [], seeds = [], sizes = [];
  const p = geometry.attributes.position, n = geometry.attributes.normal, idx = geometry.index;
  const a = new THREE.Vector3(), b = new THREE.Vector3(), c = new THREE.Vector3();
  const ab = new THREE.Vector3(), ac = new THREE.Vector3(), normal = new THREE.Vector3();
  const cumulative = [];
  let total = 0;
  for (let i = 0; i < idx.count; i += 3) {
    a.fromBufferAttribute(p, idx.getX(i)); b.fromBufferAttribute(p, idx.getX(i + 1)); c.fromBufferAttribute(p, idx.getX(i + 2));
    total += ab.subVectors(b, a).cross(ac.subVectors(c, a)).length() * .5;
    cumulative.push(total);
  }
  for (let i = 0; i < count;) {
    const target = random() * total;
    let low = 0, high = cumulative.length - 1;
    while (low < high) { const mid = (low + high) >> 1; if (cumulative[mid] < target) low = mid + 1; else high = mid; }
    const ia = idx.getX(low * 3), ib = idx.getX(low * 3 + 1), ic = idx.getX(low * 3 + 2);
    const u = Math.sqrt(random()), v = random(), weights = [1 - u, u * (1 - v), u * v];
    a.set(0, 0, 0); normal.set(0, 0, 0);
    [ia, ib, ic].forEach((j, k) => { a.addScaledVector(b.fromBufferAttribute(p, j), weights[k]); normal.addScaledVector(b.fromBufferAttribute(n, j), weights[k]); });
    normal.normalize();
    if (fringe && !(a.y > .8 || a.y < -.88 || Math.abs(a.x) > .65)) continue;
    const offset = fringe ? .018 + random() ** 2 * .22 : .0035;
    a.addScaledVector(normal, offset);
    positions.push(...a); normals.push(...normal); seeds.push(random());
    sizes.push(fringe ? .7 + random() * 1.4 : .65 + random() * .7); i++;
  }
  const buffer = new THREE.BufferGeometry();
  buffer.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
  buffer.setAttribute('normal', new THREE.Float32BufferAttribute(normals, 3));
  buffer.setAttribute('seed', new THREE.Float32BufferAttribute(seeds, 1));
  buffer.setAttribute('size', new THREE.Float32BufferAttribute(sizes, 1));
  const material = new THREE.ShaderMaterial({
    transparent: true, depthWrite: false,
    uniforms: { faceSmile: expression.smile, faceBlink: expression.blink, faceBrow: expression.brow,
      formation: animation.formation, waveTime: animation.time, waveStrength: animation.strength,
      nanoPointer: hover.point, nanoHover: hover.strength, pixelRatio: { value: 1 }, gold: { value: new THREE.Color(color) }, fringe: { value: fringe ? 1 : 0 } },
    vertexShader: expressionShader + `attribute float seed; attribute float size;
      uniform vec3 nanoPointer; uniform float nanoHover; uniform float pixelRatio; uniform float fringe;
      uniform float formation; uniform float waveTime; uniform float waveStrength;
      varying float brightness; varying float opacity;
      void main() {
        float influence = exp(-dot(position - nanoPointer, position - nanoPointer) * 20.0) * nanoHover;
        float center = 1.0 - smoothstep(.3, .75, abs(position.x));
        float arrival = smoothstep(.04 + center * .13, .57 + center * .25 + fringe * .16, formation);
        float angle = seed * 628.3185;
        float radius = sqrt(fract(seed * 173.31)) * 1.46;
        vec3 cloud = vec3(cos(angle) * radius, sin(angle) * radius, (fract(seed * 83.17) - .5) * .45);
        float wave = sin(position.y * 3.6 + position.x * 2.2 - waveTime * .85);
        // Only the fringe drifts; the settled facial particles stay on the surface.
        vec3 settled = expressFace(position) + normal * fringe * waveStrength
          * (wave * .010 + influence * (.012 + wave * .018));
        vec3 displaced = mix(cloud, settled, arrival);
        vec4 mv = modelViewMatrix * vec4(displaced, 1.0);
        gl_Position = projectionMatrix * mv;
        gl_PointSize = size * pixelRatio * (1.0 + influence * .6);
        vec3 viewNormal = normalize(normalMatrix * normal);
        brightness = .55 + seed * .65 + influence * .22 + fringe * wave * .045 * waveStrength;
        float population = smoothstep(.0, .66, formation);
        float reveal = 1.0 - smoothstep(mix(.075, 1.0, population), mix(.08, 1.01, population), seed);
        opacity = mix(.42, mix(.62, .55, fringe) * smoothstep(-.12, .45, viewNormal.z), arrival) * reveal;
      }`,
    fragmentShader: `uniform vec3 gold; varying float brightness; varying float opacity;
      void main() {
        float r = length(gl_PointCoord - .5);
        if (r > .5) discard;
        gl_FragColor = vec4(mix(gold, vec3(1.0,.94,.79), clamp(brightness - .6, 0.0, .8)), opacity * (1.0 - smoothstep(.28,.5,r)));
        #include <tonemapping_fragment>
        #include <colorspace_fragment>
      }`,
  });
  return new THREE.Points(buffer, material);
}

// A separate backdrop: depth testing keeps the waves behind the facial surface,
// while the inner mask protects the porous silhouette and its particle edges.
function listeningPulse(animation) {
  const material = new THREE.ShaderMaterial({
    transparent: true, depthWrite: false,
    uniforms: { pulseTime: animation.time, pulseStrength: animation.strength },
    vertexShader: `varying vec2 pulsePosition;
      void main() {
        pulsePosition = position.xy;
        gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
      }`,
    fragmentShader: `uniform float pulseTime; uniform float pulseStrength;
      varying vec2 pulsePosition;
      void main() {
        vec2 p = pulsePosition - vec2(0.0, -.02);
        float angle = atan(p.y, p.x);
        float outsideHead = smoothstep(.98, 1.08, length(p / vec2(.91, 1.43)));
        float boundary = 1.0 - smoothstep(1.44, 1.65, length(p));
        float waves = 0.0;
        for (int i = 0; i < 3; i++) {
          // Nine seconds per wave, spaced three seconds apart. Both ends fade
          // completely, so wrapping a phase never produces a visible reset.
          float phase = fract(pulseTime / 9.0 + float(i) / 3.0);
          vec2 radius = mix(vec2(.91, 1.32), vec2(1.68, 1.77), phase);
          float organic = sin(angle * 3.0 + phase * 1.4) * .012
            + cos(angle * 5.0 - phase) * .006;
          float distanceToWave = (length(p / radius) - 1.0 + organic) * radius.x;
          float softWave = exp(-pow(distanceToWave / .034, 2.0))
            + .23 * exp(-pow(distanceToWave / .085, 2.0));
          float envelope = smoothstep(0.0, .16, phase) * (1.0 - smoothstep(.58, 1.0, phase));
          waves += softWave * envelope;
        }
        float alpha = waves * outsideHead * boundary * min(pulseStrength, 1.0) * .19;
        gl_FragColor = vec4(vec3(.90, .70, .38), alpha);
        #include <colorspace_fragment>
      }`,
  });
  const pulse = new THREE.Mesh(new THREE.PlaneGeometry(3.6, 3.6), material);
  pulse.position.z = -.4;
  pulse.renderOrder = -1;
  return pulse;
}

export async function createAIFace(element, overrides = {}) {
  const options = { ...element.dataset, ...overrides };
  const density = Math.round(number(options.particleDensity, 22000, 2000, 40000));
  const intensity = number(options.motionIntensity, 1, 0, 1.5);
  const expressiveness = number(options.expressionIntensity, 1, 0, 1.25);
  const yawLimit = radians(number(options.horizontalLimit, 12, 0, 12));
  const pitchLimit = radians(number(options.verticalLimit, 8, 0, 8));
  const reduced = matchMedia('(prefers-reduced-motion: reduce)');
  const fine = matchMedia('(hover: hover) and (pointer: fine)');
  const lowPower = !fine.matches || (navigator.hardwareConcurrency || 4) <= 4 || (navigator.deviceMemory || 8) <= 4;
  const abort = new AbortController(), disposables = new Set();
  let renderer, scene, environment, observer, resizeObserver, frame = 0, disposed = false, visible = false, loaded = false;
  let lastFrame = 0, activeTime = 0, lastPointer = -Infinity, frameCount = 0, slowFrames = 0;
  let head, surround, eyes = [], surface, wire, orbit = element.parentElement.querySelector('.tt-hero-visual__orbit');
  const animation = { formation: { value: reduced.matches || intensity === 0 || hasFormed() || element.dataset.formation === 'complete' ? 1 : 0 },
    time: { value: 0 }, strength: { value: 0 } };
  let formationElapsed = 0;
  const meshCanvas = element.closest('.tt-hero')?.querySelector('[data-particle-network]');
  const hover = { point: { value: new THREE.Vector3(20, 20, 20) }, strength: { value: 0 } };
  const expression = { smile: { value: 0 }, blink: { value: 0 }, brow: { value: 0 } };
  const gaze = new THREE.Vector2(), blinkRandom = randomGenerator();
  let nextBlink = 3.8, blinkStart = -Infinity, blinkCount = 0, greetingStart = -Infinity, lastGreeting = -Infinity;
  let hoveredSales = null, focusedSales = null, approvalStart = -Infinity, approvalCount = 0, approvalNod = 0;
  const target = new THREE.Vector2(), current = new THREE.Vector2(), pointer = new THREE.Vector2(5, 5);
  let hovering = false;
  const raycaster = new THREE.Raycaster();
  const hoverPlane = new THREE.Plane(new THREE.Vector3(0, 0, 1), -.65);
  const hoverIntersection = new THREE.Vector3();
  const camera = new THREE.PerspectiveCamera(31, 1, .1, 30);
  camera.position.set(0, .08, 6.1); camera.lookAt(0, 0, 0);
  const listen = (node, type, handler, extra = {}) => node.addEventListener(type, handler, { ...extra, signal: abort.signal });
  const reset = () => { target.set(0, 0); hovering = false; lastPointer = -Infinity; };
  const clearSales = () => { hoveredSales = focusedSales = null; approvalStart = -Infinity; };
  const salesTarget = node => {
    const candidate = node instanceof Element ? node.closest(salesSelector) : null;
    return candidate?.matches('a[href], button:not(:disabled)') && candidate.getAttribute('aria-disabled') !== 'true' ? candidate : null;
  };
  function acknowledgeSales(previous) {
    const active = hoveredSales || focusedSales;
    if (!active || active === previous || !visible || reduced.matches || intensity === 0 || expressiveness === 0) return;
    // Moving between nested button labels/icons must not replay the nod.
    // A short cooldown also prevents rapid boundary crossings from looking jittery.
    if (activeTime - approvalStart >= 1.4) { approvalStart = activeTime; approvalCount++; }
    const rect = active.getBoundingClientRect();
    aimAt(rect.left + rect.width / 2, rect.top + rect.height / 2);
    lastPointer = performance.now();
  }
  function stop() { cancelAnimationFrame(frame); frame = 0; lastFrame = 0; }
  function dispose() {
    if (disposed) return;
    disposed = true; stop(); abort.abort(); observer?.disconnect(); resizeObserver?.disconnect();
    clearTimeout(element.aiFallbackTimer);
    scene?.traverse(object => { if (object.geometry) disposables.add(object.geometry); if (object.material) disposables.add(object.material); });
    disposables.forEach(resource => resource.dispose()); environment?.dispose();
    renderer?.dispose(); renderer?.domElement.remove();
    element.classList.remove('is-ready'); element.dataset.state = 'static';
    element.dataset.formation = 'complete';
    if (orbit) orbit.style.transform = '';
    if (meshCanvas) meshCanvas.aiPulse = null;
    instances.delete(element);
  }
  function render(now = performance.now()) {
    frame = 0;
    if (disposed || !loaded || !visible || document.hidden) return;
    const elapsed = lastFrame ? (now - lastFrame) / 1000 : 0;
    const dt = lastFrame ? Math.min(elapsed, .06) : 1 / 60;
    if (lastFrame && now - lastFrame > 38) slowFrames++;
    lastFrame = now; activeTime += dt;
    if (now - lastPointer > 2400) reset();
    const moving = !reduced.matches && intensity > 0;
    if (animation.formation.value < 1) {
      formationElapsed += elapsed;
      animation.formation.value = moving ? clamp(formationElapsed / 2.6, 0, 1) : 1;
      if (animation.formation.value === 1) { rememberFormation(); element.dataset.formation = 'complete'; }
    }
    const formed = animation.formation.value === 1;
    wire.material.opacity = .12 * THREE.MathUtils.smoothstep(animation.formation.value, .52, .95);
    const eyeReveal = THREE.MathUtils.smoothstep(animation.formation.value, .48, .77);
    eyes.forEach(eye => {
      eye.visible = eyeReveal > 0;
      eye.children.forEach(part => { part.material.opacity = part.userData.restOpacity * eyeReveal; });
    });
    animation.time.value = moving ? activeTime : 0;
    animation.strength.value = moving && formed ? intensity : 0;
    // Keep the surrounding pulse inside the artwork canvas, away from hero copy.
    if (meshCanvas) meshCanvas.aiPulse = null;
    const expressive = moving && formed && expressiveness > 0;
    if (!moving) { reset(); current.set(0, 0); gaze.set(0, 0); hover.strength.value = 0; }
    if (!expressive) {
      expression.smile.value = expression.blink.value = expression.brow.value = 0;
      clearSales();
      blinkStart = greetingStart = -Infinity; nextBlink = activeTime + 3.8;
    } else {
      if (activeTime >= nextBlink) { blinkStart = activeTime; nextBlink = activeTime + 4.3 + blinkRandom() * 2.6; blinkCount++; }
      const blinkAge = activeTime - blinkStart;
      // Quick lid closure, brief contact, then a slower opening. No face scaling.
      expression.blink.value = blinkAge < .1 ? THREE.MathUtils.smoothstep(blinkAge, 0, .1)
        : blinkAge < .14 ? 1 : 1 - THREE.MathUtils.smoothstep(blinkAge, .14, .34);
      const greeting = Math.max(0, 1 - (activeTime - greetingStart) / 1.3);
      const approving = hoveredSales || focusedSales || activeTime - approvalStart < 1.1;
      const smileTarget = (approving ? 1.12 : hovering ? .9 : 0) * expressiveness;
      expression.smile.value += (smileTarget - expression.smile.value) * (1 - Math.exp(-dt * 3.8));
      expression.brow.value += ((approving ? .55 : hovering ? .35 : 0) * expressiveness + greeting * .45 * expressiveness - expression.brow.value) * (1 - Math.exp(-dt * 5));
    }
    const damping = 1 - Math.exp(-dt * 4.6);
    current.lerp(target, damping);
    gaze.lerp(target, 1 - Math.exp(-dt * 11));
    const idle = moving ? Math.sin(activeTime * .42) * .007 * intensity : 0;
    const greetingAge = activeTime - greetingStart;
    const nod = expressive && greetingAge < 1.3 ? Math.sin(greetingAge / 1.3 * Math.PI * 2) * Math.sin(greetingAge / 1.3 * Math.PI) * radians(.85) * expressiveness : 0;
    const approvalAge = activeTime - approvalStart;
    const approvalShape = approvalAge < .3 ? THREE.MathUtils.smoothstep(approvalAge, 0, .3) : 1 - THREE.MathUtils.smoothstep(approvalAge, .3, 1.1);
    approvalNod = expressive ? approvalShape * radians(2.6) * expressiveness : 0;
    // Limits are absolute, including the initial eight-degree turn toward the headline.
    head.rotation.set(clamp(current.y * pitchLimit * intensity + nod + approvalNod, -pitchLimit, pitchLimit),
      clamp(-Math.min(radians(8), yawLimit) + current.x * yawLimit * intensity + idle, -yawLimit, yawLimit),
      expressive ? -clamp(current.x, -1, 1) * radians(.9) * expression.smile.value : 0);
    head.position.y = moving ? Math.sin(activeTime * .53) * .008 * intensity : 0;
    eyes.forEach(eye => {
      eye.rotation.set(clamp(gaze.y * .055 * intensity, -.07, .07), clamp(gaze.x * .07 * intensity, -.085, .085), 0);
      eye.scale.y = 1 - expression.blink.value * .995;
    });
    surround.rotation.copy(head.rotation);
    surround.position.set(current.x * .025 * intensity, head.position.y - current.y * .02 * intensity, -.008);
    if (orbit) orbit.style.transform = `translate3d(${-current.x * 5 * intensity}px, ${current.y * 3 * intensity}px, 0)`;
    hover.strength.value += ((hovering && moving ? 1 : 0) - hover.strength.value) * damping;
    if (hovering && moving) {
      head.updateMatrixWorld(true); raycaster.setFromCamera(pointer, camera);
      const hit = raycaster.intersectObject(surface, false)[0];
      if (hit) hover.point.value.lerp(head.worldToLocal(hit.point), .2);
      else if (raycaster.ray.intersectPlane(hoverPlane, hoverIntersection)) {
        hover.point.value.lerp(head.worldToLocal(hoverIntersection), .2);
      }
    }
    renderer.render(scene, camera);
    frameCount++;
    // One downgrade after sustained slow frames, without changing the resting composition.
    if (frameCount === 120 && slowFrames > 55) {
      renderer.setPixelRatio(1); resize();
      head.children.filter(o => o.isPoints).forEach(o => o.geometry.setDrawRange(0, Math.round(o.geometry.attributes.position.count * .6)));
    }
    if (moving) frame = requestAnimationFrame(render);
  }
  function start() { if (!frame && loaded && visible && !document.hidden && !disposed) render(); }
  function resize() {
    if (!renderer || disposed) return;
    const rect = element.getBoundingClientRect();
    if (!rect.width || !rect.height) return;
    renderer.setSize(rect.width, rect.height, false);
    camera.aspect = rect.width / rect.height; camera.updateProjectionMatrix();
    scene?.traverse(o => { if (o.isPoints) o.material.uniforms.pixelRatio.value = renderer.getPixelRatio(); });
    if (loaded && visible && !document.hidden) { renderer.render(scene, camera); }
  }
  async function load() {
    try {
      renderer = new THREE.WebGLRenderer({ alpha: true, antialias: !lowPower, powerPreference: 'low-power' });
      renderer.setPixelRatio(Math.min(devicePixelRatio || 1, lowPower ? 1.25 : 1.75));
      renderer.setClearColor(0xffffff, 0);
      renderer.toneMapping = THREE.ACESFilmicToneMapping;
      renderer.toneMappingExposure = 1.05;
      renderer.domElement.setAttribute('aria-hidden', 'true');
      element.append(renderer.domElement);
      listen(renderer.domElement, 'webglcontextlost', () => dispose());
      const response = await fetch(options.model, { signal: abort.signal });
      if (!response.ok) throw new Error(`AI face model: HTTP ${response.status}`);
      const data = await response.arrayBuffer();
      const [version, vertices, indexCount, edgeCount] = new Uint32Array(data, 0, 4);
      if (version !== 1 || data.byteLength !== 40 + vertices * 12 + (indexCount + edgeCount) * 2) throw new Error('Invalid AI face geometry');
      const eyeCoordinates = new Float32Array(data, 16, 6);
      const model = {
        positions: new Float32Array(data, 40, vertices * 3),
        indices: new Uint16Array(data, 40 + vertices * 12, indexCount),
        connections: new Uint16Array(data, 40 + vertices * 12 + indexCount * 2, edgeCount),
        eyes: [eyeCoordinates.subarray(0, 3), eyeCoordinates.subarray(3, 6)],
      };
      if (disposed) return;
      scene = new THREE.Scene(); head = new THREE.Group(); surround = new THREE.Group();
      scene.add(head, surround, listeningPulse(animation));
      environment = studioEnvironment(renderer); scene.environment = environment.texture;
      scene.add(new THREE.HemisphereLight(0xfff8e9, 0xb19a72, 1.2));
      const key = new THREE.DirectionalLight(0xfff8ec, 3.1); key.position.set(-3, 4, 5); scene.add(key);
      const rim = new THREE.DirectionalLight(0xffdeaa, 2); rim.position.set(3, 2, -1); scene.add(rim);
      const fill = new THREE.DirectionalLight(0xffffff, .9); fill.position.set(2, 0, 5); scene.add(fill);
      const geometry = new THREE.BufferGeometry();
      geometry.setAttribute('position', new THREE.Float32BufferAttribute(model.positions, 3));
      geometry.setIndex(new THREE.BufferAttribute(model.indices, 1)); geometry.computeVertexNormals();
      surface = new THREE.Mesh(geometry, nanoSurface(options.color || '#c5ad7c', hover, expression, animation)); head.add(surface);
      const wireGeometry = new THREE.BufferGeometry();
      wireGeometry.setAttribute('position', geometry.attributes.position.clone());
      wireGeometry.setIndex(new THREE.BufferAttribute(model.connections, 1));
      wire = new THREE.LineSegments(wireGeometry, new THREE.LineBasicMaterial({ color: 0x94703b, transparent: true, opacity: animation.formation.value * .12, depthWrite: false }));
      wire.material.onBeforeCompile = shader => addExpression(shader, expression);
      wire.scale.setScalar(1.001); head.add(wire);
      head.add(particles(geometry, Math.round(density * (lowPower ? .5 : 1)), hover, options.color || '#c5ad7c', expression, animation));
      surround.add(particles(geometry, lowPower ? 650 : 1400, hover, options.color || '#c5ad7c', expression, animation, true));
      for (const center of model.eyes) {
        const eye = new THREE.Group(); eye.position.fromArray(center);
        const ball = new THREE.Mesh(new THREE.SphereGeometry(.121, 24, 16), new THREE.MeshStandardMaterial({ color: 0xb4a78b, metalness: .7, roughness: .5 }));
        const iris = new THREE.Mesh(new THREE.CircleGeometry(.048, 32), new THREE.MeshStandardMaterial({ color: 0x53432c, metalness: .65, roughness: .48, emissive: 0xc69548, emissiveIntensity: .08 }));
        iris.position.z = .122;
        const pupil = new THREE.Mesh(new THREE.CircleGeometry(.022, 24), new THREE.MeshBasicMaterial({ color: 0x383026 }));
        pupil.position.z = .124;
        const halo = new THREE.Mesh(new THREE.RingGeometry(.046, .048, 32), new THREE.MeshBasicMaterial({ color: 0xf2d59c, transparent: true, opacity: .36 }));
        halo.position.z = .123;
        const catchlight = new THREE.Mesh(new THREE.CircleGeometry(.006, 12), new THREE.MeshBasicMaterial({ color: 0xfff0d5, transparent: true, opacity: .7 }));
        catchlight.position.set(-.012, .017, .126);
        eye.add(ball, iris, pupil, halo, catchlight); head.add(eye); eyes.push(eye);
        eye.visible = animation.formation.value === 1;
        eye.children.forEach(part => { part.userData.restOpacity = part.material.opacity; part.material.transparent = true; });
      }
      resize(); head.rotation.y = -Math.min(radians(8), yawLimit);
      await renderer.compileAsync(scene, camera);
      if (disposed) return;
      clearTimeout(element.aiFallbackTimer);
      // A slow/failed load may already have revealed the still. Never dissolve it again.
      if (element.dataset.formation === 'complete' || reduced.matches || hasFormed()) animation.formation.value = 1;
      if (animation.formation.value === 1) rememberFormation();
      loaded = true; renderer.render(scene, camera);
      element.classList.add('is-ready'); element.dataset.state = 'ready';
      element.dataset.formation = animation.formation.value === 1 ? 'complete' : 'forming'; start();
    } catch (error) {
      if (error.name !== 'AbortError') console.warn('AI face: using static artwork.', error);
      dispose();
    }
  }
  function aimAt(clientX, clientY) {
    const rect = element.getBoundingClientRect();
    // Aim at the pointer in artwork space. Compensate for the resting left turn
    // so a pointer to the right actually turns the face to the right.
    const restOffset = Math.min(radians(8), yawLimit) / Math.max(yawLimit, .0001);
    target.set(clamp((clientX - rect.left - rect.width / 2) / (rect.width * .6), -1, 1) + restOffset,
      clamp((clientY - rect.top - rect.height / 2) / (rect.height * .8), -1, 1));
    pointer.set((clientX - rect.left) / rect.width * 2 - 1, -(clientY - rect.top) / rect.height * 2 + 1);
  }
  listen(window, 'pointermove', event => {
    if (!fine.matches || reduced.matches || !visible || event.pointerType === 'touch') return;
    aimAt(event.clientX, event.clientY);
    const inside = Math.abs(pointer.x) < 1 && Math.abs(pointer.y) < 1;
    if (inside && !hovering && activeTime - lastGreeting > 3) { greetingStart = lastGreeting = activeTime; }
    hovering = inside;
    lastPointer = performance.now();
  }, { passive: true });
  listen(document, 'pointerover', event => {
    if (!fine.matches || event.pointerType === 'touch' || !visible || reduced.matches) return;
    const next = salesTarget(event.target);
    if (next === salesTarget(event.relatedTarget)) return;
    const previous = hoveredSales || focusedSales;
    hoveredSales = next; acknowledgeSales(previous);
  }, { passive: true });
  listen(document, 'pointerout', event => {
    if (event.pointerType === 'touch') return;
    const previous = hoveredSales || focusedSales;
    hoveredSales = salesTarget(event.relatedTarget); acknowledgeSales(previous);
  }, { passive: true });
  listen(document, 'focusin', event => {
    const previous = hoveredSales || focusedSales;
    focusedSales = visible && !reduced.matches && event.target.matches(':focus-visible') ? salesTarget(event.target) : null;
    acknowledgeSales(previous);
  });
  listen(document, 'focusout', () => {
    const previous = hoveredSales || focusedSales;
    focusedSales = null; acknowledgeSales(previous);
  });
  const resetAll = () => { reset(); clearSales(); };
  listen(document.documentElement, 'pointerleave', resetAll);
  listen(window, 'blur', resetAll);
  listen(document, 'visibilitychange', () => { resetAll(); if (document.hidden) stop(); else start(); });
  listen(reduced, 'change', () => { resetAll(); stop(); start(); });
  listen(fine, 'change', resetAll);
  listen(window, 'pagehide', event => { if (event.persisted) { resetAll(); stop(); } else dispose(); });
  listen(window, 'pageshow', start);
  resizeObserver = new ResizeObserver(resize); resizeObserver.observe(element);
  let requested = false;
  observer = new IntersectionObserver(entries => {
    visible = entries[0].isIntersecting;
    if (visible && !requested) { requested = true; load(); }
    else if (visible) start();
    else { resetAll(); stop(); }
  }, { threshold: 0 });
  observer.observe(element);
  const api = { dispose, getStats: () => ({ loaded, visible, running: !!frame, frames: frameCount,
    formation: animation.formation.value, waveStrength: animation.strength.value, waveTime: animation.time.value,
    yaw: head ? THREE.MathUtils.radToDeg(head.rotation.y) : 0,
    pitch: head ? THREE.MathUtils.radToDeg(head.rotation.x) : 0,
    smile: expression.smile.value, blink: expression.blink.value, blinkCount,
    salesActive: !!(hoveredSales || focusedSales), approvalCount, approvalNod: THREE.MathUtils.radToDeg(approvalNod),
    eyeYaw: eyes.length ? THREE.MathUtils.radToDeg(eyes[0].rotation.y) : 0,
    shimmer: hover.strength.value, pixelRatio: renderer?.getPixelRatio(), drawCalls: renderer?.info.render.calls }) };
  instances.set(element, api);
  return api;
}

function mount() { document.querySelectorAll('[data-ai-face]').forEach(element => { if (!instances.has(element)) createAIFace(element); }); }
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mount, { once: true });
else mount();
export { instances };
