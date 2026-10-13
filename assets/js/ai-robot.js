import * as THREE from './vendor/three/three.module.min.js';

const host = document.querySelector('[data-hero-robot]');
const stage = host?.querySelector('[data-robot-stage]');
const button = host?.querySelector('[data-robot-gesture]');

if (stage && button) {
  try {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const scene = new THREE.Scene();
    const camera = new THREE.OrthographicCamera(-2, 2, 2.8, -2.8, .1, 50);
    camera.position.set(1.35, 2.8, 10);
    camera.lookAt(0, 2.48, 0);

    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true, powerPreference: 'low-power' });
    renderer.setClearColor(0xffffff, 0);
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.12;
    stage.appendChild(renderer.domElement);

    scene.add(new THREE.HemisphereLight(0xffffff, 0xc6bdad, 1.35));
    const light = (color, intensity, position) => {
      const source = new THREE.DirectionalLight(color, intensity);
      source.position.set(...position);
      scene.add(source);
    };
    light(0xfff8e9, 2.5, [-3, 7, 8]);
    light(0xd5eaff, .7, [5, 3, 5]);
    light(0xffdf9b, 1.5, [2, 5, -5]);

    const shell = new THREE.MeshPhysicalMaterial({ color: 0xece9e3, metalness: .3, roughness: .25, clearcoat: 1, clearcoatRoughness: .15 });
    const shellShade = new THREE.MeshPhysicalMaterial({ color: 0xdcd9d1, metalness: .35, roughness: .3, clearcoat: .8 });
    const dark = new THREE.MeshPhysicalMaterial({ color: 0x0c1628, metalness: .6, roughness: .25, clearcoat: .65 });
    const visor = new THREE.MeshPhysicalMaterial({ color: 0x020a18, metalness: .12, roughness: .06, clearcoat: 1, clearcoatRoughness: .04 });
    const gold = new THREE.MeshPhysicalMaterial({ color: 0xc49339, metalness: .85, roughness: .21, clearcoat: .5 });
    const glow = new THREE.MeshBasicMaterial({ color: 0x83dfff, toneMapped: false });
    const glowSoft = new THREE.MeshBasicMaterial({ color: 0x4ebcff, transparent: true, opacity: .28, toneMapped: false });
    const sphere = new THREE.SphereGeometry(1, 32, 24);

    const ellipsoid = (parent, material, x, y, z, sx, sy, sz) => {
      const part = new THREE.Mesh(sphere, material);
      part.position.set(x, y, z);
      part.scale.set(sx, sy, sz);
      parent.add(part);
      return part;
    };
    const cylinder = (parent, material, x, y, z, top, bottom, height, segments = 28) => {
      const part = new THREE.Mesh(new THREE.CylinderGeometry(top, bottom, height, segments), material);
      part.position.set(x, y, z);
      parent.add(part);
      return part;
    };
    const ring = (parent, material, x, y, z, radius, tube, scaleX = 1, scaleY = 1) => {
      const part = new THREE.Mesh(new THREE.TorusGeometry(radius, tube, 12, 56), material);
      part.position.set(x, y, z);
      part.scale.set(scaleX, scaleY, 1);
      parent.add(part);
      return part;
    };
    const stroke = (parent, points, material, radius = .012) => {
      const curve = new THREE.CatmullRomCurve3(points.map(point => new THREE.Vector3(...point)));
      const mesh = new THREE.Mesh(new THREE.TubeGeometry(curve, 28, radius, 8, false), material);
      parent.add(mesh);
      return mesh;
    };

    const robot = new THREE.Group();
    robot.rotation.y = -.08;
    scene.add(robot);

    // Stable legs and planted feet.
    cylinder(robot, dark, 0, 2.34, 0, .33, .32, .24);
    ellipsoid(robot, shell, 0, 2.03, .02, .5, .36, .39);
    ring(robot, gold, 0, 2.07, .396, .24, .012, 1.6, .84);
    for (const side of [-1, 1]) {
      const x = side * .33;
      ellipsoid(robot, dark, x, 1.9, 0, .24, .24, .25);
      const thigh = ellipsoid(robot, shell, x, 1.54, .02, .275, .49, .29);
      thigh.rotation.z = side * -.06;
      ellipsoid(robot, dark, x, 1.03, .02, .19, .18, .2);
      ring(robot, gold, x, 1.03, .215, .145, .02);
      const shin = ellipsoid(robot, shell, x, .59, .03, .25, .51, .26);
      shin.rotation.z = side * .035;
      ellipsoid(robot, dark, x, .19, .15, .25, .16, .37);
      ellipsoid(robot, shell, x, .16, .29, .31, .17, .52);
      ring(robot, gold, x, .13, .59, .18, .014, 1.55, .55);
    }

    // Torso, neck, and a lit center core.
    ellipsoid(robot, dark, 0, 2.88, 0, .66, .66, .43);
    ellipsoid(robot, shell, 0, 2.94, .065, .62, .62, .44);
    stroke(robot, [[-.52, 2.92, .34], [-.34, 2.76, .47], [0, 2.62, .51], [.34, 2.76, .47], [.52, 2.92, .34]], gold, .009);
    ellipsoid(robot, dark, .16, 2.65, .48, .19, .19, .07);
    ring(robot, gold, .16, 2.65, .55, .135, .021);
    ellipsoid(robot, glowSoft, .16, 2.65, .558, .075, .075, .015);
    cylinder(robot, dark, 0, 3.58, 0, .2, .19, .25);
    cylinder(robot, gold, 0, 3.62, 0, .205, .205, .025);

    const head = new THREE.Group();
    head.position.set(0, 4.17, 0);
    robot.add(head);
    ellipsoid(head, shell, 0, 0, 0, .85, .68, .59);
    ellipsoid(head, shellShade, 0, -.48, .01, .58, .13, .49);
    ellipsoid(head, gold, 0, .015, .49, .7, .51, .13);
    ellipsoid(head, visor, 0, .01, .54, .67, .48, .19);
    ring(head, gold, 0, .01, .58, .48, .026, 1.43, 1.05);
    stroke(head, [[-.48, .53, .33], [-.21, .62, .37], [.15, .64, .36], [.47, .55, .31]], gold, .012);
    for (const side of [-1, 1]) {
      const ear = cylinder(head, gold, side * .82, -.015, 0, .21, .21, .105);
      ear.rotation.z = Math.PI / 2;
      const center = cylinder(head, dark, side * .89, -.015, 0, .15, .15, .12);
      center.rotation.z = Math.PI / 2;
      const lightRing = cylinder(head, glow, side * .958, -.015, 0, .118, .118, .012);
      lightRing.rotation.z = Math.PI / 2;
      const inset = cylinder(head, dark, side * .968, -.015, 0, .089, .089, .014);
      inset.rotation.z = Math.PI / 2;
    }
    const eyes = [-1, 1].map(side => {
      const eye = new THREE.Group();
      eye.position.set(side * .25, .015, .731);
      head.add(eye);
      stroke(eye, [[-.125, -.065, 0], [-.085, .018, .012], [0, .052, .016], [.088, .018, .012], [.13, -.064, 0]], glowSoft, .049);
      stroke(eye, [[-.125, -.065, .008], [-.085, .018, .02], [0, .052, .024], [.088, .018, .02], [.13, -.064, .008]], glow, .025);
      return eye;
    });
    const surpriseEyes = [-1, 1].map(side => {
      const eye = ring(head, glow, side * .25, .01, .743, .055, .022, .86, 1.2);
      eye.visible = false;
      return eye;
    });
    const surpriseMouth = ring(head, glow, 0, -.235, .738, .035, .014);
    surpriseMouth.visible = false;

    // Each arm is one hierarchy: shoulder, elbow, wrist, and fingers share joints.
    const makeArm = side => {
      const shoulder = new THREE.Group();
      shoulder.position.set(side * .68, 3.24, 0);
      robot.add(shoulder);
      ellipsoid(shoulder, dark, 0, 0, 0, .22, .22, .24);
      ellipsoid(shoulder, shell, side * .045, -.075, .02, .27, .23, .26);
      stroke(shoulder, [[side * -.13, -.02, .233], [0, -.15, .268], [side * .18, -.035, .229]], gold, .011);
      ellipsoid(shoulder, dark, 0, -.35, 0, .155, .33, .17);
      ellipsoid(shoulder, shell, 0, -.39, .035, .19, .31, .205);
      ring(shoulder, gold, 0, -.5, .235, .145, .012, .9, 1.35);

      const elbow = new THREE.Group();
      elbow.position.set(0, -.73, 0);
      shoulder.add(elbow);
      ellipsoid(elbow, dark, 0, 0, 0, .165, .17, .19);
      ring(elbow, gold, 0, 0, .191, .125, .018);
      ellipsoid(elbow, shell, 0, -.38, .025, .21, .4, .22);
      stroke(elbow, [[-.15, -.44, .183], [0, -.54, .235], [.15, -.44, .183]], gold, .009);

      const wrist = new THREE.Group();
      wrist.position.set(0, -.78, .01);
      elbow.add(wrist);
      ellipsoid(wrist, dark, 0, 0, 0, .13, .14, .14);
      ellipsoid(wrist, shell, 0, -.15, .055, .19, .19, .13);
      const fingers = [];
      for (let finger = 0; finger < 4; finger++) {
        const x = (finger - 1.5) * .095;
        const digit = new THREE.Group();
        digit.position.set(x, -.28, .055);
        digit.rotation.z = (finger - 1.5) * .11;
        wrist.add(digit);
        fingers.push(digit);
        ellipsoid(digit, dark, 0, -.045, 0, .041, .075, .052);
        ellipsoid(digit, shell, 0, -.12, .006, .044, .09, .053);
      }
      const thumb = ellipsoid(wrist, shell, side * -.2, -.19, .09, .072, .13, .065);
      thumb.rotation.z = side * -.55;
      return { shoulder, elbow, wrist, fingers };
    };
    const left = makeArm(-1);
    const right = makeArm(1);

    const resize = () => {
      const width = stage.clientWidth;
      const height = stage.clientHeight;
      if (!width || !height) return;
      const aspect = width / height;
      camera.left = -2.78 * aspect;
      camera.right = 2.78 * aspect;
      camera.top = 2.78;
      camera.bottom = -2.78;
      camera.updateProjectionMatrix();
      renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.8));
      renderer.setSize(width, height);
      renderer.render(scene, camera);
    };
    resize();
    stage.classList.add('is-3d');
    const observer = new ResizeObserver(resize);
    observer.observe(stage);

    const smooth = (a, b, t) => {
      const x = Math.max(0, Math.min(1, (t - a) / (b - a)));
      return x * x * (3 - 2 * x);
    };
    let reactionStart = -10;
    button.addEventListener('click', () => { if (!reducedMotion) reactionStart = performance.now() / 1000; });
    if (reducedMotion) {
      button.disabled = true;
      button.setAttribute('aria-label', 'Robot character');
    }

    const animate = now => {
      const seconds = now / 1000;
      const cycle = seconds % 18;
      const automaticWave = smooth(1.8, 2.55, cycle) * (1 - smooth(4.7, 5.45, cycle));
      const clickAge = seconds - reactionStart;
      const clickWave = clickAge >= 0 && clickAge < 2.2
        ? smooth(0, .35, clickAge) * (1 - smooth(1.55, 2.2, clickAge)) : 0;
      const wave = Math.max(automaticWave, clickWave);
      const flutter = Math.sin(seconds * 15);
      const peek = smooth(8.3, 8.65, cycle) * (1 - smooth(9.35, 9.75, cycle));
      const wink = Math.max(
        smooth(2.2, 2.28, cycle) * (1 - smooth(2.65, 2.78, cycle)),
        smooth(14.1, 14.18, cycle) * (1 - smooth(14.48, 14.62, cycle)),
        clickAge >= 0 && clickAge < 2.2 ? smooth(.38, .48, clickAge) * (1 - smooth(.85, 1, clickAge)) : 0
      );
      const blink = smooth(6.1, 6.17, cycle) * (1 - smooth(6.22, 6.31, cycle));

      right.shoulder.rotation.z = wave * (1.02 + .03 * flutter);
      right.elbow.rotation.z = wave * (2.03 + .17 * flutter);
      right.wrist.rotation.z = wave * (.12 * flutter);
      right.fingers.forEach((finger, index) => { finger.rotation.z = (index - 1.5) * (.11 + .17 * wave); });
      left.shoulder.rotation.z = -.07 * peek;
      left.elbow.rotation.z = -.18 * peek;
      head.rotation.z = -.045 * wave + .045 * peek + .018 * Math.sin(seconds * .7);
      head.rotation.y = .045 * Math.sin(seconds * .42);
      eyes[0].scale.y = 1 - blink * .96;
      eyes[1].scale.y = 1 - Math.max(wink, blink) * .9;
      eyes.forEach(eye => { eye.visible = peek < .5; });
      surpriseEyes.forEach(eye => { eye.visible = peek >= .5; });
      surpriseMouth.visible = peek >= .5;
      renderer.render(scene, camera);
      if (!reducedMotion) requestAnimationFrame(animate);
    };
    requestAnimationFrame(animate);
  } catch (error) {
    console.warn('Robot 3D scene unavailable; showing still image.', error);
  }
}
