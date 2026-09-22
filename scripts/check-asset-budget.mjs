import fs from 'node:fs';
import path from 'node:path';

const directory = path.resolve('public/build/assets');
const limits = {
  '.js': 180 * 1024,
  '.css': 140 * 1024,
};
const totalLimit = 360 * 1024;

if (!fs.existsSync(directory)) {
  console.error('Asset budget check failed: public/build/assets does not exist.');
  process.exit(1);
}

const files = fs.readdirSync(directory)
  .filter((name) => Object.hasOwn(limits, path.extname(name)))
  .map((name) => {
    const filePath = path.join(directory, name);
    return { name, ext: path.extname(name), bytes: fs.statSync(filePath).size };
  });

let failed = false;
let total = 0;

for (const file of files) {
  total += file.bytes;
  const limit = limits[file.ext];
  console.log(`${file.name}: ${file.bytes} bytes (limit ${limit})`);

  if (file.bytes > limit) {
    console.error(`Asset budget exceeded: ${file.name}`);
    failed = true;
  }
}

console.log(`Total JS/CSS: ${total} bytes (limit ${totalLimit})`);

if (total > totalLimit) {
  console.error('Combined frontend asset budget exceeded.');
  failed = true;
}

process.exit(failed ? 1 : 0);
