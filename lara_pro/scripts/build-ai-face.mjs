// From repo root: node lara_pro/scripts/build-ai-face.mjs .tmp-hero/base.obj .tmp-hero/female.target
import { readFileSync, writeFileSync } from 'node:fs';
const vertices = [], groups = {};
let group;
for (const line of readFileSync(process.argv[2], 'utf8').split('\n')) {
  const a = line.trim().split(/\s+/);
  if (a[0] === 'v') vertices.push(a.slice(1).map(Number));
  if (a[0] === 'g') group = a[1];
  if (a[0] === 'f') (groups[group] ??= []).push(a.slice(1).map(s => parseInt(s) - 1));
}
for (const line of readFileSync(process.argv[3], 'utf8').split('\n')) {
  if (!/^\d/.test(line)) continue;
  const [i, ...delta] = line.trim().split(/\s+/).map(Number);
  vertices[i] = vertices[i].map((n, j) => n + delta[j]);
}
const average = ps => [0, 1, 2].map(j => ps.reduce((s, p) => s + p[j], 0) / ps.length);
const normalize = p => [p[0], p[1] - 6.62, p[2] - 0.45];
const eyes = ['joint-r-eye', 'joint-l-eye'].map(g => normalize(average([...new Set(groups[g].flat())].map(i => vertices[i]))));
let faces = groups.body.filter(f => f.every(i => vertices[i][1] > 5.32));
const used = [...new Set(faces.flat())], remap = new Map(used.map((i, j) => [i, j]));
let points = used.map(i => normalize(vertices[i]));
faces = faces.map(f => f.map(i => remap.get(i)));
// Catmull-Clark subdivision retains the authored anatomy and smooths the silhouette.
const facePoints = faces.map(f => average(f.map(i => points[i])));
const edges = new Map(), vertexFaces = points.map(() => []), vertexEdges = points.map(() => []);
faces.forEach((f, fi) => f.forEach((a, j) => {
  vertexFaces[a].push(fi);
  const b = f[(j + 1) % f.length], key = [a, b].sort((x, y) => x - y).join(':');
  if (!edges.has(key)) {
    const edge = { a, b, faces: [] };
    edges.set(key, edge); vertexEdges[a].push(edge); vertexEdges[b].push(edge);
  }
  edges.get(key).faces.push(fi);
}));
const next = points.map((p, i) => {
  const boundary = vertexEdges[i].filter(e => e.faces.length === 1);
  if (boundary.length) {
    const neighbors = average(boundary.map(e => points[e.a === i ? e.b : e.a]));
    return p.map((v, j) => v * .75 + neighbors[j] * .25);
  }
  const n = vertexFaces[i].length;
  const f = average(vertexFaces[i].map(i => facePoints[i]));
  const r = average(vertexEdges[i].map(e => average([points[e.a], points[e.b]])));
  return p.map((v, j) => (f[j] + 2 * r[j] + (n - 3) * v) / n);
});
for (const e of edges.values()) {
  e.index = next.length;
  next.push(average([points[e.a], points[e.b], ...e.faces.map(i => facePoints[i])]));
}
const faceOffset = next.length;
next.push(...facePoints);
const triangles = [], connections = [];
faces.forEach((f, fi) => f.forEach((a, j) => {
  const edge = b => edges.get([a, b].sort((x, y) => x - y).join(':')).index;
  const b = edge(f[(j + 1) % f.length]), d = edge(f[(j + f.length - 1) % f.length]), c = faceOffset + fi;
  triangles.push(a, b, c, a, c, d);
  connections.push(a, b, b, c);
}));
// Little-endian binary v1: four uint32 lengths, six float32 eye coordinates,
// float32 positions, uint16 triangle indices, uint16 cell-edge indices.
if (next.length > 65535) throw new Error('Head exceeds uint16 index capacity');
const header = new Uint32Array([1, next.length, triangles.length, connections.length]);
const output = Buffer.concat([header, new Float32Array(eyes.flat()), new Float32Array(next.flat()),
  new Uint16Array(triangles), new Uint16Array(connections)].map(a => Buffer.from(a.buffer)));
writeFileSync('assets/models/ai-face/female-head.bin', output);
console.log(`${next.length} vertices, ${triangles.length / 3} triangles; head and neck only.`);
