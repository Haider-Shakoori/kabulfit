# Content, SEO CMS and Legacy URL Migration

Batch 10 adds a localized editorial/content layer and an explicit migration boundary for the previous KabulFit URL structure.

## CMS

Public content is stored separately from catalog data:

- fixed public pages with English, Dari and Pashto translations;
- journal/blog posts with localized slugs, excerpts, body copy and SEO fields;
- publish/unpublish controls;
- server-rendered canonical and hreflang metadata;
- WebPage and BlogPosting JSON-LD;
- related editorial links and links from the main navigation/footer.

The administrator CMS uses existing first-party RBAC and immutable administrative audit logs. `content.manage` controls page/journal editing.

## Sitemap architecture

`/sitemap.xml` is a sitemap index referencing:

- `/sitemaps/catalog.xml` for homepage, shop, categories, collections and products;
- `/sitemaps/content.xml` for public pages, journal index and published articles.

Private account, commerce, administration and tailor-workspace routes remain excluded and are disallowed in `robots.txt`.

## Verified legacy inventory

A public crawl performed on 2026-09-22 directly exposed legacy KabulFit routes including:

- `/About`
- `/Contact`
- `/FAQ`
- `/MeasurementGuide`
- `/PrivacyPolicy`
- `/ProductDetail`
- `/ReturnPolicy`
- `/ShippingPolicy`
- `/Shop`
- `/TermsConditions`
- account/cart/checkout/orders/wishlist/measurements/tailor routes

The database inventory records verification time, disposition, target and notes. Public old routes receive explicit permanent redirects to the clean localized URLs.

## ProductDetail rule

`/ProductDetail?id=...` remains deliberately separate from generic page redirects. A legacy product ID is redirected only when it has an explicit `legacy_product_redirects` mapping to a known current product. Unknown IDs return 404.

This prevents SEO-damaging or commercially incorrect redirects to guessed products.

## Administration

`redirects.manage` controls the legacy inventory screen. Mapping changes are audited. The inventory distinguishes redirectable public URLs from mapping-only product URLs and can also represent private/gone legacy surfaces when needed.
