"""
Audit v0.33.0 — Info pages + /avvocati/ archive + drop-cap visibility

Verifica:
- 4 info pages renderizzano sl-info-page__* con drop-cap visibile
- /avvocati/ archive ha .sl-attorney-archive__* enriched layout
- Drop-cap CSS rules con !important applicate cross-template
- Smoke 9 URL HTTP 200
"""
import urllib.request, ssl, re, json
ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE
BASE = 'https://staging.studiolegalesaltelli.it'

def fetch(url):
    try:
        with urllib.request.urlopen(BASE + url + '?_=v33audit', context=ctx, timeout=12) as r:
            return r.read().decode('utf-8', errors='ignore')
    except: return ''

def fetch_css():
    try:
        with urllib.request.urlopen(BASE + '/wp-content/themes/saltelli/assets/css/sections.css', context=ctx, timeout=12) as r:
            return r.read().decode('utf-8', errors='ignore')
    except: return ''

print("="*100)
print("  AUDIT v0.33.0 — Info pages + /avvocati/ archive + drop-cap")
print("="*100)

CSS = fetch_css()

# Theme version live
home = fetch('/')
ver_match = re.search(r'sections\.css\?ver=([^"\']+)', home)
print(f"\nTheme live: {ver_match.group(1) if ver_match else 'unknown'}")

print("\n" + "="*100)
print("  TASK 1+2 — Info pages template (.sl-info-page__*)")
print("="*100)

INFO_PAGES = [
    {'url': '/guide-gratuite/', 'expected_letter': 'S', 'eyebrow': 'Guide gratuite'},
    {'url': '/come-lavoriamo/', 'expected_letter': 'A', 'eyebrow': 'Come lavoriamo'},
    {'url': '/prima-consulenza/', 'expected_letter': 'T', 'eyebrow': 'Prima consulenza'},
    {'url': '/lavora-con-noi/', 'expected_letter': 'C', 'eyebrow': 'Lavora con noi'},
]

t1_pass = 0
for p in INFO_PAGES:
    html = fetch(p['url'])
    has_wrapper = 'sl-info-page' in html
    has_hero = 'sl-info-page__hero' in html
    has_body = 'sl-info-page__body' in html
    has_cta = 'sl-info-page__cta-final' in html
    has_eyebrow_text = p['eyebrow'].lower() in html.lower()
    
    score = sum([has_wrapper, has_hero, has_body, has_cta, has_eyebrow_text])
    status = "✓" if score == 5 else ("≈" if score >= 3 else "✗")
    if score == 5: t1_pass += 1
    
    print(f"\n{status} {p['url']:<25} score:{score}/5")
    print(f"    sl-info-page wrap:    {'✓' if has_wrapper else '✗'}")
    print(f"    hero asym:            {'✓' if has_hero else '✗'}")
    print(f"    body editorial:        {'✓' if has_body else '✗'}")
    print(f"    cta final dark:        {'✓' if has_cta else '✗'}")
    print(f"    eyebrow text match:    {'✓' if has_eyebrow_text else '✗'}")

print(f"\nTASK 1+2 SCORE: {t1_pass}/4 page con sl-info-page completo")

print("\n" + "="*100)
print("  TASK 3 — /avvocati/ archive refactor")
print("="*100)

avv_html = fetch('/avvocati/')
avv_checks = [
    ('sl-attorney-archive wrapper', 'sl-attorney-archive\\b' in avv_html or re.search(r'class=\"[^\"]*sl-attorney-archive', avv_html)),
    ('hero asymmetric 8fr/4fr', 'sl-attorney-archive__hero' in avv_html),
    ('hero aside trust', 'sl-attorney-archive__hero-aside' in avv_html),
    ('h1 split-reveal', 'data-split-reveal' in avv_html and 'professionisti' in avv_html.lower()),
    ('lede drop-cap target', 'sl-attorney-archive__lede' in avv_html),
    ('4 lawyer card grid', avv_html.count('sl-attorney-archive__card') >= 4 or avv_html.count('sl-team__lawyer') >= 4),
    ('§ principi 3 numerati', 'sl-attorney-archive__principi' in avv_html),
    ('CTA finale dark', 'sl-attorney-archive__cta-final' in avv_html),
]

t3_pass = 0
for name, ok in avv_checks:
    sym = "✓" if ok else "✗"
    if ok: t3_pass += 1
    print(f"  {sym} {name}")

print(f"\nTASK 3 SCORE: {t3_pass}/{len(avv_checks)}")

print("\n" + "="*100)
print("  TASK 4 — Drop-cap !important visibility")
print("="*100)

drop_cap_scopes = [
    'sl-attorney__bio-prose',
    'sl-attorney__bio',
    'sl-page__prose',
    'sl-competenza__prose',
    'sl-tier1__body',
    'sl-costi-w4__calc-text',
    'sl-chi-siamo__lede-text',
    'sl-info-page__body',
]

t4_pass = 0
for scope in drop_cap_scopes:
    # Cerca first-letter rule per scope con !important
    pattern = rf'\.{re.escape(scope)}[^{{]*::first-letter\b'
    has_rule = bool(re.search(pattern, CSS))
    has_important = bool(re.search(rf'\.{re.escape(scope)}[^{{]*::first-letter\s*\{{[^}}]*!important', CSS))
    
    if has_rule and has_important:
        sym = "✓"
        t4_pass += 1
    elif has_rule:
        sym = "≈ (no !important)"
    else:
        sym = "✗"
    print(f"  {sym:<25} .{scope}")

print(f"\nTASK 4 SCORE: {t4_pass}/{len(drop_cap_scopes)} scope con first-letter + !important")

print("\n" + "="*100)
print("  SMOKE 9 URL HTTP 200")
print("="*100)

urls = ['/avvocati/', '/guide-gratuite/', '/come-lavoriamo/', '/prima-consulenza/', '/lavora-con-noi/',
        '/chi-siamo/', '/costi/', '/competenze/diritto-tributario/', '/casi/']

t5_pass = 0
for u in urls:
    try:
        req = urllib.request.Request(BASE + u, method='HEAD')
        with urllib.request.urlopen(req, context=ctx, timeout=8) as r:
            sym = "✓" if r.status == 200 else f"✗ {r.status}"
            if r.status == 200: t5_pass += 1
    except Exception as e:
        sym = f"✗ {str(e)[:30]}"
    print(f"  {sym:<10} {u}")

print(f"\nSMOKE: {t5_pass}/{len(urls)} URL HTTP 200")

print("\n" + "="*100)
print("  EXECUTIVE SUMMARY v0.33.0")
print("="*100)
total = t1_pass + t3_pass + t4_pass + t5_pass
max_total = 4 + len(avv_checks) + len(drop_cap_scopes) + len(urls)
pct = total * 100 // max_total
print(f"\n  TASK 1+2 (info pages):       {t1_pass}/4")
print(f"  TASK 3 (/avvocati/ archive): {t3_pass}/{len(avv_checks)}")
print(f"  TASK 4 (drop-cap !important): {t4_pass}/{len(drop_cap_scopes)}")
print(f"  SMOKE (9 URL HTTP 200):      {t5_pass}/{len(urls)}")
print(f"\n  TOTAL: {total}/{max_total} ({pct}%)")

if pct >= 90:
    verdict = "OK PASS - GO walkthrough finale"
elif pct >= 75:
    verdict = "Mostly OK - eventual mini-fix"
else:
    verdict = "Gap residui - micro-fix v0.33.1 prima di walkthrough"
print(f"\n  VERDETTO: {verdict}")
