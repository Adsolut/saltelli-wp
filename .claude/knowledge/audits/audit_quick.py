"""
Audit script generico Saltelli WP — riusabile post-Wave/version.

Usage:
  python3 .claude/knowledge/audits/audit_quick.py

Verifica:
- Theme version live + git status
- ACF Field Groups creati
- Custom Post Types registrati
- Smoke test 12 URL HTTP 200
- Editor readiness (page WP con post_content vs hardcoded)
"""
import os, json, glob, subprocess, urllib.request, ssl, re, sys

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE
BASE = 'https://staging.studiolegalesaltelli.it'
REPO = '/Users/aldosantoro/Desktop/DEV/saltelli-wp'
ACF_DIR = f'{REPO}/wp-content/themes/saltelli/acf-json'

def fetch(url):
    try:
        with urllib.request.urlopen(BASE + url + '?_=audit', context=ctx, timeout=10) as r:
            return r.read().decode('utf-8', errors='ignore')
    except: return ''

print("="*100)
print("  AUDIT QUICK Saltelli WP")
print("="*100)

# Theme version
home = fetch('/')
m = re.search(r'sections\.css\?ver=([^"\']+)', home)
print(f"\nTheme live: {m.group(1) if m else 'unknown'}")

# Git
try:
    res = subprocess.run(['git', '-C', REPO, 'log', '--oneline', '-5'], capture_output=True, text=True, timeout=8)
    print("\n─── Git log ultimi 5 ───")
    print(res.stdout)
except: pass

# ACF Field Groups
print("─── ACF Field Groups ───")
if os.path.exists(ACF_DIR):
    files = sorted(glob.glob(f'{ACF_DIR}/*.json'))
    print(f"  Total: {len(files)}")
    total_fields = 0
    for f in files:
        try:
            with open(f) as fh:
                d = json.load(fh)
            n = len(d.get('fields', []))
            total_fields += n
            print(f"  {os.path.basename(f):<55} {n:>3} fields")
        except: pass
    print(f"\n  Total custom fields: {total_fields}")
else:
    print("  ✗ acf-json/ not found")

# Smoke
print("\n─── Smoke 12 URL ───")
urls = ['/', '/chi-siamo/', '/avvocati/', '/avvocati/emiliano-saltelli/',
        '/competenze/diritto-tributario/', '/casi/', '/contatti/', '/costi/',
        '/blog/', '/faq/', '/come-lavoriamo/', '/glossario-legale/']
ok = 0
for u in urls:
    try:
        req = urllib.request.Request(BASE + u, method='HEAD')
        with urllib.request.urlopen(req, context=ctx, timeout=8) as r:
            if r.status == 200: ok += 1; sym = "✓"
            else: sym = f"✗ {r.status}"
    except: sym = "✗"
    print(f"  {sym:<5} {u}")
print(f"\n  → {ok}/{len(urls)} HTTP 200")

# Editor readiness — sample
print("\n─── Editor readiness sample (page WP /costi/) ───")
try:
    res = subprocess.run(
        ['docker', 'compose', 'run', '--rm', 'wpcli', 'post', 'get', '2695', '--field=post_content'],
        capture_output=True, text=True, timeout=30, cwd=REPO
    )
    content_len = len([l for l in res.stdout.split('\n') if 'Container' not in l and 'Success' not in l])
    print(f"  /costi/ post_content lines: {content_len}")
    if content_len < 20:
        print("  ⚠ Content molto corto: probabile hardcoded in template (richiede ACF migration)")
except Exception as e:
    print(f"  Docker check: {e}")

print("\n" + "="*100)
print("  END AUDIT")
print("="*100)
