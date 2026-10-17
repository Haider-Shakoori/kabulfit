#!/usr/bin/env python3
"""Read-only KabulFit Laravel staging storefront/API readiness check.

Never modifies product data or performs checkout. Intended for staging before
any Base44 -> Laravel domain cutover.
"""
from __future__ import annotations

import argparse
import json
import sys
from urllib.error import HTTPError, URLError
from urllib.parse import quote, urljoin, urlparse
from urllib.request import Request, urlopen


def request(url: str, *, accept: str = "application/json") -> tuple[str, bytes]:
    req = Request(url, headers={"Accept": accept, "User-Agent": "KabulFit-Staging-Readiness/1.0"})
    with urlopen(req, timeout=15) as response:
        return response.headers.get("Content-Type", ""), response.read()


def require(ok: bool, reason: str) -> None:
    if not ok:
        raise RuntimeError(reason)


def verify(base: str, sample: int, expected_active: int, expected_categories: int, all_galleries: bool = False) -> None:
    base = base.rstrip("/")
    host = urlparse(base)
    require(host.scheme == "https" and bool(host.netloc), "Only an HTTPS staging base URL is supported")
    require(host.hostname != "kabulfit.com", "Refusing to target the live Base44 domain")
    for locale in ("en", "fa", "ps"):
        content_type, body = request(f"{base}/api/v1/{locale}/categories")
        require("application/json" in content_type, f"{locale}: categories is not Laravel JSON")
        categories = json.loads(body)["data"]
        require(len(categories) == expected_categories, f"{locale}: expected {expected_categories} categories, got {len(categories)}")
        print(f"PASS {locale} categories: {len(categories)}")

    products: list[dict] = []
    page = 1
    while True:
        content_type, body = request(f"{base}/api/v1/en/catalog?page={page}")
        require("application/json" in content_type, f"Catalog page {page} is not JSON")
        payload = json.loads(body)
        meta = payload["meta"]
        products.extend(payload["data"])
        require(int(meta["total"]) == expected_active, f"Unexpected catalog total {meta['total']}")
        if page >= int(meta["last_page"]):
            break
        page += 1
        require(page <= 100, "Catalog pagination exceeded safety limit")

    require(len(products) == expected_active, f"Expected {expected_active} active products, received {len(products)}")
    sku_set = {str(item["sku"]) for item in products}
    require(len(sku_set) == expected_active, "Catalog has duplicate SKUs")
    for product in products:
        price = product.get("price") or {}
        require(price.get("currency") == "USD", f"Non-USD price for {product['sku']}")
        require(str(price.get("formatted", "")).startswith("$"), f"Missing USD display price for {product['sku']}")
        require(isinstance(price.get("minor"), int) and price["minor"] > 0, f"Invalid price minor units for {product['sku']}")
        media = product.get("primary_media") or {}
        require(media.get("url"), f"Missing primary image for {product['sku']}")
    print(f"PASS {len(products)} unique active SKUs, all USD, with primary media URLs")

    media_urls: list[tuple[str, str]] = [
        (str(item["sku"]), str(item["primary_media"]["url"]))
        for item in products
    ]

    if all_galleries:
        gallery_urls: set[str] = set()
        for product in products:
            slug = quote(str(product["slug"]), safe="")
            content_type, body = request(f"{base}/api/v1/en/products/{slug}")
            require("application/json" in content_type, f"Product detail is not JSON: {slug}")
            detail = json.loads(body)["data"]
            require(detail.get("sku") == product["sku"], f"Product detail SKU mismatch: {slug}")
            require(detail.get("price") == product["price"], f"Product detail price mismatch: {slug}")
            require(isinstance(detail.get("variants"), list), f"Missing variants array: {slug}")
            require(isinstance(detail.get("media"), list), f"Missing gallery array: {slug}")
            for media in detail["media"]:
                if media.get("url"):
                    gallery_urls.add(str(media["url"]))
        media_urls = [("gallery", url) for url in sorted(gallery_urls)]
        print(f"PASS all {len(products)} product-detail JSON records; {len(media_urls)} distinct gallery image URLs")

    checks = media_urls if sample == 0 else media_urls[:sample]
    failures: list[str] = []
    for sku, url in checks:
        target = urlparse(urljoin(base, url))
        require(target.scheme == "https" and target.netloc == host.netloc, f"Unexpected external image hostname for {sku}")
        try:
            content_type, body = request(target.geturl(), accept="image/avif,image/webp,image/*")
            if not content_type.startswith("image/") or not body:
                failures.append(f"{sku}: unexpected content type {content_type}")
        except (HTTPError, URLError) as error:
            failures.append(f"{sku}: {error}")
    if failures:
        raise RuntimeError(f"{len(failures)} of {len(checks)} tested product images failed: " + " | ".join(failures[:8]))
    print(f"PASS {len(checks)} product images served as image/* over HTTPS")


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--base", default="https://kabulfit.businessos.af")
    parser.add_argument("--sample", type=int, default=5, help="Image sample size; 0 checks all primary images")
    parser.add_argument("--expected-active", type=int, default=130)
    parser.add_argument("--expected-categories", type=int, default=8)
    parser.add_argument("--all-galleries", action="store_true", help="Fetch all active product details and test gallery URLs")
    args = parser.parse_args()
    try:
        require(args.sample >= 0, "Sample must be non-negative")
        verify(args.base, args.sample, args.expected_active, args.expected_categories, args.all_galleries)
    except (RuntimeError, HTTPError, URLError, KeyError, ValueError, json.JSONDecodeError) as error:
        print("FAIL", error, file=sys.stderr)
        sys.exit(1)
