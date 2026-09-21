# KabulFit Visual Reference Audit

Last audited: 2026-09-21  
Primary reference: https://kabulfit.com/

## Confirmed live-site structure

The live homepage currently presents the following recognizable KabulFit sequence:

1. "Authentic Afghan Elegance" hero with Shop Now and Measurement Guide actions.
2. Three service assurances: Custom Sizing, Global Shipping, Quality Assured.
3. Shop by Category.
4. Featured products.
5. "Our Story / Afghan Culture" craftsmanship section with a 100+ Years of Tradition proof point.
6. "Perfect Fit Technology / Get Your Perfect Measurements" call-to-action.
7. Long-form "Authentic Afghan Clothes & Custom Tailoring" editorial content covering Gand e Afghani / Perahan Tunban, Afghan embroidery and custom tailoring.
8. "What Our Customers Say" with three customer quotes.
9. Contact positioning for Kabul, Afghanistan and Dubai, United Arab Emirates.

Batch 2 deliberately preserves that information hierarchy while replacing weak implementation details with semantic HTML, localized strings, accessible controls and responsive components.

## Design-system implementation

All primary colors remain centralized in `resources/css/app.css`:

- `--brand-primary`
- `--brand-secondary`
- `--brand-accent`
- `--brand-background`
- `--brand-surface`
- `--brand-text`
- `--brand-muted`
- `--brand-border`

Spacing, radii, shadows, container widths and display typography are also tokenized. Components use logical CSS properties so English LTR and Dari/Pashto RTL share one implementation.

## Media strategy

The repository does not yet contain the current production product photography. Batch 2 therefore uses two small original SVG visual assets for the hero/story composition and a neutral branded product-media treatment for deterministic demo products.

Batch 3 will introduce the catalog media domain and responsive image records. Production media should then:

- retain an original master;
- generate correctly sized WebP/AVIF derivatives;
- store intrinsic width/height and localized alt text;
- render `srcset`/`sizes` with explicit dimensions;
- lazy-load below-the-fold media;
- prioritize only the true LCP image;
- remain CDN-ready.

No third-party stock image was added merely to make the demo look populated.

## Accessibility and responsive notes

Batch 2 adds/refines:

- skip navigation;
- semantic landmarks/headings;
- keyboard-operable mobile navigation with Escape close;
- visible focus states;
- reduced-motion handling;
- logical RTL layout properties;
- explicit image dimensions;
- minimum touch-target sizing;
- no intentionally horizontal-scrolling page sections;
- responsive breakpoints covering the required 320px–1920px range structurally.

## Fidelity limitation

The available crawler can inspect the live page content and public page inventory, but it does not expose a browser screenshot, the original CSS bundle, or reliable source URLs for the live photography. Therefore the current token values are centralized and visually aligned to the recognizable KabulFit green / burgundy / gold / warm-neutral identity, but **pixel-level color and spacing parity is not claimed yet**.

Before final acceptance, browser-based visual regression screenshots must compare the rebuilt site against the live reference at the required target widths. Any measured token differences can then be corrected centrally without rewriting templates.
