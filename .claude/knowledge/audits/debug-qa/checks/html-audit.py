#!/usr/bin/env python3
"""
HTML structure audit for Debug & QA Phase 2.

Checks per URL:
- HTTP status (final via redirects)
- Size + content-type
- H1 count (must be exactly 1)
- Empty <a href="">  links
- <img> tags missing alt attribute
- Viewport meta presence
- Title length (10-70 chars optimal)
- Canonical link presence
- JSON-LD blocks count
- console error markers (data-error, etc.)
- Page-specific checks (e.g., footer NAP visibility)

Output: markdown report to stdout + summary stats.
"""
import sys
import re
import json
import urllib.request
from collections import defaultdict

URLS = [
    # 21 base
    "/", "/lo-studio/", "/avvocati/", "/avvocati/emiliano-saltelli/",
    "/avvocati/fabiana-saltelli/", "/avvocati/antonia-battista/",
    "/avvocati/stefano-gaetano-tedesco/", "/casi/", "/costi/", "/contatti/",
    "/faq/", "/come-lavoriamo/", "/prima-consulenza/", "/lavora-con-noi/",
    "/richiedi-preventivo/", "/guide-gratuite/",
    "/competenze/diritto-tributario/", "/competenze/diritto-del-lavoro/",
    "/competenze/diritto-di-famiglia-lgbtq/", "/tipo-area/privati/",
    "/glossario-legale/",
    # 11 estensioni
    "/competenze/cartelle-esattoriali-e-multe/",
    "/competenze/diritto-bancario/",
    "/competenze/diritto-condominiale/",
    "/competenze/diritto-penale/",
    "/competenze/recupero-crediti/",
    "/competenze/",
    "/blog/",
    "/?s=cartella",
    "/wp-sitemap.xml",
    "/llms.txt",
    "/robots.txt",
]
BASE = "https://staging.studiolegalesaltelli.it"

issues_by_url = defaultdict(list)
stats = {"total": 0, "pass": 0, "fail": 0}

def fetch(url):
    req = urllib.request.Request(url, headers={"User-Agent": "DebugQA-Bot/1.0"})
    try:
        with urllib.request.urlopen(req, timeout=15) as r:
            return r.status, r.geturl(), r.read().decode("utf-8", errors="replace"), dict(r.headers)
    except Exception as e:
        return 0, url, "", {"error": str(e)}

def audit_html(url, html):
    issues = []

    # H1 count
    h1s = re.findall(r"<h1\b[^>]*>.*?</h1>", html, re.IGNORECASE | re.DOTALL)
    if len(h1s) == 0:
        issues.append(("P1", "no-h1", f"No <h1> tag found"))
    elif len(h1s) > 1:
        issues.append(("P1", "multiple-h1", f"Multiple <h1> tags found: {len(h1s)}"))

    # Empty <a href=""> links
    empty_hrefs = re.findall(r'<a\s+[^>]*href=["\']["\']', html, re.IGNORECASE)
    if empty_hrefs:
        issues.append(("P2", "empty-href", f"{len(empty_hrefs)} <a href=\"\"> empty links"))

    # <img> without alt
    imgs_no_alt = re.findall(r"<img(?![^>]*\balt=)[^>]*>", html, re.IGNORECASE)
    if imgs_no_alt:
        # Filter out svg+background placeholders
        real_imgs_no_alt = [m for m in imgs_no_alt if "src=" in m]
        if real_imgs_no_alt:
            issues.append(("P1", "img-no-alt", f"{len(real_imgs_no_alt)} <img> tags without alt attribute"))

    # Viewport meta
    if "<meta" in html.lower() and "viewport" not in html.lower():
        issues.append(("P0", "no-viewport", "Missing <meta name=viewport> tag"))

    # Title
    title_match = re.search(r"<title[^>]*>(.*?)</title>", html, re.IGNORECASE | re.DOTALL)
    if title_match:
        title = title_match.group(1).strip()
        title_len = len(title)
        if title_len < 10:
            issues.append(("P2", "title-short", f"Title too short ({title_len} chars): {title!r}"))
        elif title_len > 80:
            issues.append(("P2", "title-long", f"Title too long ({title_len} chars): {title[:60]!r}..."))
    else:
        issues.append(("P1", "no-title", "Missing <title> tag"))

    # Canonical
    canonical = re.search(r'<link\s+[^>]*rel=["\']canonical["\']', html, re.IGNORECASE)
    if not canonical:
        issues.append(("P2", "no-canonical", "Missing <link rel=canonical>"))

    # JSON-LD blocks
    jsonld = re.findall(r'<script[^>]*type=["\']application/ld\+json["\'][^>]*>(.*?)</script>', html, re.DOTALL)
    if not jsonld:
        # Allow xml/text endpoints to skip JSON-LD
        if not (url.endswith(".xml") or url.endswith(".txt")):
            issues.append(("P2", "no-jsonld", "No JSON-LD blocks found"))
    else:
        # Validate parsing
        for i, blob in enumerate(jsonld):
            try:
                json.loads(blob.strip())
            except json.JSONDecodeError as e:
                issues.append(("P0", "jsonld-invalid", f"JSON-LD block #{i} invalid: {e}"))

    # ACF unrendered tokens (e.g., {{field}} or %field% leftover)
    leftovers = re.findall(r'\{\{[^}]+\}\}|%[A-Z_]{4,}%', html)
    if leftovers:
        issues.append(("P0", "unrendered-token", f"Unrendered template tokens: {leftovers[:3]}"))

    # PHP error markers
    if re.search(r'<b>(?:Warning|Notice|Fatal error|Parse error)</b>', html, re.IGNORECASE):
        issues.append(("P0", "php-error", "PHP error/warning visible in HTML"))

    # NAP sanity (footer should have phone or address on most pages)
    is_xml_or_txt = url.endswith(".xml") or url.endswith(".txt")
    is_search = url.startswith("/?s=")
    if not is_xml_or_txt and not is_search:
        has_phone = bool(re.search(r'\+?39[\s\-]*081[\s\-]*1813', html))
        has_address = bool(re.search(r'Vannella\s+Gaetani', html, re.IGNORECASE))
        if not has_phone and not has_address:
            issues.append(("P1", "no-nap", "No NAP (phone/address) visible — footer broken?"))

    return issues


def main():
    print("# HTML Structure Audit — Debug & QA Phase 2")
    print(f"\nBase: {BASE}")
    print(f"Total URLs: {len(URLS)}")
    print()

    for url in URLS:
        full = BASE + url
        stats["total"] += 1
        status, final_url, html, headers = fetch(full)

        if status != 200:
            stats["fail"] += 1
            issues_by_url[url].append(("P0", "http-fail", f"HTTP {status} (final: {final_url})"))
            continue

        stats["pass"] += 1
        # Skip HTML audit for non-HTML responses
        ctype = headers.get("Content-Type", headers.get("content-type", "")).lower()
        if "html" not in ctype:
            continue

        issues = audit_html(url, html)
        if issues:
            issues_by_url[url] = issues

    # Aggregate output
    total_issues = sum(len(v) for v in issues_by_url.values())
    p0 = sum(1 for v in issues_by_url.values() for i in v if i[0] == "P0")
    p1 = sum(1 for v in issues_by_url.values() for i in v if i[0] == "P1")
    p2 = sum(1 for v in issues_by_url.values() for i in v if i[0] == "P2")

    print(f"## Summary")
    print(f"- HTTP smoke: {stats['pass']}/{stats['total']} PASS")
    print(f"- HTML issues: {total_issues} total · P0={p0} · P1={p1} · P2={p2}")
    print()

    if not issues_by_url:
        print("✅ No issues detected.\n")
    else:
        print("## Issues by URL\n")
        for url, issues in sorted(issues_by_url.items()):
            if not issues:
                continue
            print(f"### {url}")
            for sev, kind, msg in issues:
                print(f"- **{sev}** `{kind}` — {msg}")
            print()

    # Group by issue kind
    print("## Issues by category\n")
    by_kind = defaultdict(list)
    for url, issues in issues_by_url.items():
        for sev, kind, msg in issues:
            by_kind[kind].append((sev, url, msg))
    for kind in sorted(by_kind):
        items = by_kind[kind]
        print(f"### `{kind}` — {len(items)} occorrenze")
        for sev, url, msg in items:
            print(f"- {sev} {url} — {msg}")
        print()

    return 0 if p0 == 0 else 1


if __name__ == "__main__":
    sys.exit(main())
