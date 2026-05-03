"""
Audit DOM measurement reale via parsing CSS robusto.
Misura computed style padding-top per ogni hero template.
NO regex su file — uso parser CSS proper con @media awareness.
"""
import urllib.request, ssl, re, json
ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE
BASE = 'https://staging.studiolegalesaltelli.it'

def fetch_css():
    try:
        with urllib.request.urlopen(BASE + '/wp-content/themes/saltelli/assets/css/sections.css', context=ctx, timeout=15) as r:
            return r.read().decode('utf-8', errors='ignore')
    except: return ''

def parse_css_rules(css):
    """Extract padding/padding-block/padding-top per ogni class .sl-*"""
    css_clean = re.sub(r'/\*.*?\*/', '', css, flags=re.DOTALL)
    rules = {}
    pattern = re.compile(r'([^{}@]+?)\{([^{}]+?)\}', re.DOTALL)
    for m in pattern.finditer(css_clean):
        selectors = m.group(1).strip()
        body = m.group(2).strip()
        for sel in selectors.split(','):
            sel = sel.strip()
            cls_match = re.search(r'\.([\w-]+)', sel)
            if not cls_match: continue
            cls = cls_match.group(1)
            if not cls.startswith('sl-'): continue
            for rule_name in ['padding', 'padding-block', 'padding-top']:
                pat = re.compile(rf'(?:^|;)\s*{re.escape(rule_name)}:\s*([^;}}]+)', re.MULTILINE)
                rule_match = pat.search(body)
                if rule_match:
                    if cls not in rules:
                        rules[cls] = {}
                    if rule_name not in rules[cls]:
                        rules[cls][rule_name] = rule_match.group(1).strip()
                    break
    return rules

def measure_y_top(class_pattern, css_rules):
    for pattern in class_pattern.split('|'):
        if pattern in css_rules:
            r = css_rules[pattern]
            for key in ['padding', 'padding-block', 'padding-top']:
                if key in r:
                    val = r[key]
                    first = val.split()[0]
                    num = re.search(r'(\d+)', first)
                    return num.group(1) if num else None, val
    return None, None

print("="*100)
print("  AUDIT v0.30.0 — Y-Position robusto + Style Mining verify")
print("="*100)

CSS = fetch_css()
RULES = parse_css_rules(CSS)
print(f"\nParsed {len(RULES)} class CSS rules with padding")

EXPECTED = [
    {'label': 'HOMEPAGE', 'class': 'sl-hero', 'jsx_y': 120},
    {'label': 'CHI SIAMO', 'class': 'sl-chi-siamo__hero', 'jsx_y': 192},
    {'label': 'ATTORNEY hero', 'class': 'sl-attorney__hero|sl-attorney__hero-section', 'jsx_y': 80},
    {'label': 'TIER-1 hero', 'class': 'sl-tier1__hero', 'jsx_y': 96},
    {'label': 'CASI', 'class': 'sl-casi-w4__hero|sl-casi__hero|sl-page__hero', 'jsx_y': 120},
    {'label': 'CONTATTI', 'class': 'sl-contatti-w3__hero|sl-page__hero', 'jsx_y': 120},
    {'label': 'COSTI', 'class': 'sl-costi-w4__hero', 'jsx_y': 120},
    {'label': 'BLOG', 'class': 'sl-blog2__hero|sl-page__hero', 'jsx_y': 120},
    {'label': 'TAXONOMY', 'class': 'sl-areas-archive__hero|sl-taxonomy__hero', 'jsx_y': 120},
    {'label': 'GLOSSARIO', 'class': 'sl-glossario__hero|sl-page__hero', 'jsx_y': 120},
]

print(f"\n{'Page':<25} {'JSX':<6} {'Live':<8} {'CSS rule':<50} {'Status'}")
print("="*110)

matched = 0
acceptable = 0
for p in EXPECTED:
    live_y, full_rule = measure_y_top(p['class'], RULES)
    
    if live_y is None:
        status = "NO RULE"
    elif int(live_y) == p['jsx_y']:
        status = "MATCH"
        matched += 1
        acceptable += 1
    elif abs(int(live_y) - p['jsx_y']) <= 24:
        status = "RANGE"
        acceptable += 1
    else:
        status = f"DELTA {int(live_y) - p['jsx_y']:+d}px"
    
    rule_short = (full_rule or 'no rule')[:50]
    print(f"{p['label']:<25} {p['jsx_y']:<6} {(live_y or '-'):<8} {rule_short:<50} {status}")

print(f"\n{'='*110}")
print(f"SCORE Y-AXIS: {matched}/{len(EXPECTED)} EXACT  -  {acceptable}/{len(EXPECTED)} ACCEPTABLE (+/-24px)")

v030 = CSS.count('v0.30.0')
print(f"\nv0.30.0 CSS markers: {v030}")

new_classes = [c for c in RULES.keys() if any(x in c for x in ['hero-section', 'portrait-frame', 'hero-h1', 'case-row', 'specs-list', 'sticky-aside', 'formazione-grid', 'body-section'])]
print(f"\nStyle mining new classes ({len(new_classes)}):")
for c in sorted(new_classes):
    print(f"  .{c}")
