# KabulFit Visual Reference Audit

Last audited: 2026-09-22  
Primary visual source of truth: https://kabulfit.com/

## Live-site alignment

The Laravel storefront now preserves the recognizable live KabulFit presentation while keeping the production Laravel architecture, localization, commerce, accessibility and SEO foundations.

The aligned homepage includes:

1. Full-height photographic hero with the live KabulFit red-to-teal brand treatment.
2. Four service assurances: Custom Sizing, Global Shipping, Quality Assured and Handcrafted.
3. Four image-led category cards.
4. Featured product cards using locally stored KabulFit catalog photography.
5. Dark photographic Afghan Culture / craftsmanship section with the 100+ Years of Tradition proof point.
6. Split red-to-teal measurement call-to-action with the KabulFit measurement imagery.
7. Long-form Afghan clothing and custom-tailoring editorial content.
8. Three customer testimonials.
9. Dark footer with the original white KabulFit logo, contact details and company/support/shop navigation.

The primary navigation follows the live-site order: Home, Shop, Measurement Guide, About, Contact.

## Brand and media assets

The live KabulFit logo, white footer logo, favicon, hero, craftsmanship, measurement and catalog photography have been captured into the repository under:

- `public/images/kabulfit-live/`
- `public/images/kabulfit-live/catalog/`

Runtime templates and seed data reference only these local paths. The storefront does not depend on Supabase, Base44 or another remote image host at render time.

`docs/live-reference/asset-manifest.json` records the source provenance used during the alignment audit. It is documentation only and is not loaded by the application.

## Seeded catalog media

Each of the five deterministic demo products receives a distinct locally stored KabulFit WebP image with English, Dari and Pashto alt text. The former shared SVG catalog placeholder is no longer used.

The current deterministic catalog continues to provide stable fixtures for automated tests and commerce flows; live photography is used to make those fixtures representative of KabulFit rather than to change their business identities.

## Design system

The live-site visual layer is defined in `resources/css/live-site.css` and loaded through the main Vite CSS entry. It uses the live KabulFit burgundy/red and teal treatment while retaining the existing reusable base components and responsive behavior.

Key alignment colors:

- burgundy/red: `#881C27`
- teal: `#2A6867`
- warm page background: `#FDFBF7`
- dark footer/story surfaces: near-black/charcoal

Layouts use logical properties and responsive breakpoints so English LTR and Dari/Pashto RTL share the same implementation.

## Accessibility, performance and SEO

The rebuilt Laravel implementation retains:

- skip navigation and semantic landmarks;
- keyboard-operable mobile navigation;
- visible focus states;
- localized EN/FA/PS content and RTL support;
- explicit image dimensions and lazy loading below the fold;
- local canonical/alternate SEO behavior;
- structured data and localized metadata;
- responsive product-image support;
- no remote runtime image URLs.

Automated visual-alignment regression coverage prevents the old hero/story SVGs and catalog placeholder from being reintroduced into rendered customer pages.
