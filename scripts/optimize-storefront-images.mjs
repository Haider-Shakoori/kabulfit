import fs from 'node:fs';
import path from 'node:path';
import sharp from 'sharp';

const root = path.resolve('public');
const outRoot = path.join(root, 'images', 'kabulfit-optimized');

const tasks = [
  ['images/kabulfit-live/logo-header.png', 'logo-header.webp', 320, 74],
  ['images/kabulfit-live/logo-footer.png', 'logo-footer.webp', 320, 74],
  ['images/kabulfit-live/catalog/category-3ed66ce35408.webp', 'category-men.webp', 640, 68],
  ['images/kabulfit-live/catalog/category-861c19f93a16.webp', 'category-women.webp', 640, 68],
  ['images/kabulfit-live/catalog/category-15e0668e6ecf.png', 'category-kids.webp', 640, 68],
  ['images/kabulfit-live/catalog/product-e3216ebea370.webp', 'category-accessories.webp', 640, 68],
  ['images/kabulfit-base44/source/1cce53a01_2.png', 'story-bg.webp', 1280, 64],
  ['images/kabulfit-base44/source/f065351e6_bn.jpg', 'craftsmanship.webp', 800, 70],
  ['images/kabulfit-base44/source/58f1df170_2.png', 'measurement.webp', 800, 68],
  ['images/kabulfit-base44/source/6b7f27f2f_H1.webp', 'hero-h1-mobile.webp', 768, 70],
  ['images/kabulfit-base44/source/f2989fdbd_H2.webp', 'hero-h2-mobile.webp', 768, 70],
  ['images/kabulfit-base44/source/b6b455e59_H3.webp', 'hero-h3-mobile.webp', 768, 70],
];

fs.mkdirSync(outRoot, { recursive: true });

for (const [sourceRelative, targetName, width, quality] of tasks) {
  const source = path.join(root, sourceRelative);
  const target = path.join(outRoot, targetName);

  if (!fs.existsSync(source)) {
    throw new Error(`Missing storefront image source: ${sourceRelative}`);
  }

  await sharp(source)
    .rotate()
    .resize({ width, withoutEnlargement: true })
    .webp({ quality, effort: 5, smartSubsample: true })
    .toFile(target);

  const bytes = fs.statSync(target).size;
  console.log(`${targetName}: ${bytes} bytes`);
}

const faviconSource = path.join(root, 'images', 'kabulfit-live', 'favicon.png');
const faviconTarget = path.join(outRoot, 'favicon-64.png');

await sharp(faviconSource)
  .rotate()
  .resize({ width: 64, height: 64, fit: 'contain' })
  .png({ compressionLevel: 9, palette: true })
  .toFile(faviconTarget);

console.log(`favicon-64.png: ${fs.statSync(faviconTarget).size} bytes`);

const catalogSourceDir = path.join(root, 'images', 'kabulfit-live', 'catalog');
const catalogOutDir = path.join(outRoot, 'catalog');
fs.mkdirSync(catalogOutDir, { recursive: true });

for (const name of fs.readdirSync(catalogSourceDir).filter((name) => /^product-.*\.(?:png|jpe?g|webp)$/i.test(name))) {
  const source = path.join(catalogSourceDir, name);
  const base = path.parse(name).name;

  for (const width of [320, 640]) {
    const target = path.join(catalogOutDir, `${base}-${width}.webp`);

    await sharp(source)
      .rotate()
      .resize({ width, withoutEnlargement: true })
      .webp({ quality: width === 320 ? 68 : 72, effort: 5, smartSubsample: true })
      .toFile(target);

    console.log(`catalog/${path.basename(target)}: ${fs.statSync(target).size} bytes`);
  }
}
