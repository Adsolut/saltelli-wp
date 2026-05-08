# 🔍 Claude Code Agent — Wave 4.7.fix.2 Investigation v2 · Admin↔Frontend Mismatch + Page Discovery + URL/Menu + Recurring Blocks (v2.0)

> **Audience**: Claude Code agent in nuova sessione terminale dedicata.
> **Branch parent**: `main` (post Wave 4.7.fix.1 mergeata, tag `v1.3.7-wave4-7-fix-1-scf-url-validation`)
> **Modo operativo**: **READ-ONLY ASSOLUTO** (zero scritture su DB / FS / config su staging).
> **Output**: report markdown + 4 fix path raccomandati per le 4 categorie.
> **Scope**: 4 categorie investigation (A admin/frontend mismatch + B page discoverability Elena + C URL/Menu incongruence + D recurring blocks cross-page).
> **Tempo stimato**: ~25-30 min Code totali.
> **NO branch nuovo** (sessione read-only, output solo audit + report locale).
> **Riferimento**: bug Duccio 2026-05-08 + feedback Elena (15 URL "non trovo dove modificare" + blocchi ricorrenti).

---

## 🎯 Tu sei

Claude Code agent dedicato a **diagnosticare l'estensione completa di 4 problemi distinti** scoperti nel test post Wave 4.7.fix.1:

**Categoria A** — Mismatch admin↔frontend. Esempio: Studio Section "Body sezione studio" admin vuoto, frontend mostra contenuto. Causa probabile: PHP fallback hardcoded ≠ JSON default_value vuoto.

**Categoria B** — Page discoverability Elena. Elena lamenta 15 URL del sito dove "non trovo dove modificare". Causa probabile: pages renderizzate da CPT custom o template hub PHP, non da "Pages" WP standard come si aspetta Elena.

**Categoria C** — URL/Title/Menu incongruence. Esempi: `/chi-siamo/risultati/` page title="Casi rappresentativi" (URL/title mismatch); `/risorse/` clicked → mostra `/risorse/domande-frequenti/` (menu mapping); `/costi-e-consulenze/` + 3 sub-pages (esistono in WP o solo nel menu?).

**Categoria D** — Blocchi ricorrenti cross-page editing. Elena vuole modificare da una sola posizione (pattern Saltelli Settings) i blocchi che ricorrono cross-page: "ultima chiamata" CTA, banda newsletter, trust signals, footer colophon, sticky WhatsApp widget. Verifica se già SCF Theme Options o hardcoded da migrare.

**Output**: report unificato con 4 sezioni + 4 fix path + raccomandazione orchestratore.

**Decisione già presa Duccio (NO investigation, just plan)**: rename slug `/chi-siamo/risultati/` → `/chi-siamo/casi-rappresentativi/` con redirect legacy. Da inserire come "scheduled action" nel report Phase 9.

**Critical context**: pattern HARD RULE STOP. Onestà tecnica. NO inferenze, dati alla mano.

---

## 🔒 Hard rules (non negotiabili)

1. **READ-ONLY ASSOLUTO**: zero `update_option`, `update_field`, `wp_update_post`, modifiche file, scritture DB. Solo `wp eval` di lettura, `grep`, `cat`, query SELECT, HTTP HEAD/curl smoke.
2. **NO branch nuovo**: lavora su `main` con working tree pulito. Output solo in `.claude/knowledge/audits/wave4-7-fix-2-investigation/`.
3. **NO commit** durante investigation. 1 solo commit finale del report.
4. **NO modifiche template PHP / JSON / DB / menu** anche se "evidente fix".
5. **Pattern HARD RULE STOP**: meglio safety di forced completion.
6. **Output strutturato**: report markdown 4-section structure + raccomandazioni concrete con costi onesti.

---

## 📚 Letture obbligatorie (nell'ordine)

1. **`CLAUDE.md`** — single source of truth post Wave 4.7.fix.1 v1.3.7
2. **`.claude/knowledge/recovery/WAVE4-7-FIX-SCF-REPORT.md`** — Wave 4.7.fix
3. **`.claude/knowledge/recovery/WAVE4-7-FIX-1-SCF-URL-REPORT.md`** — Wave 4.7.fix.1
4. **`prompts/PROMPT_AGENT_WAVE4_7_FIX_2_INVESTIGATION_V2.md`** (questo file) end-to-end
5. **File da leggere**:
   - `wp-content/themes/saltelli/inc/helpers.php` (helper `saltelli_option`, `saltelli_field`)
   - `wp-content/themes/saltelli/front-page.php` (homepage call-site)
   - `wp-content/themes/saltelli/footer.php`, `header.php` (recurring blocks call-site)
   - `wp-content/themes/saltelli/page.php`, `page-templates/*.php`
   - **TUTTI** i `template-parts/*.php` (recurring blocks: CTA, newsletter, trust, sticky)
   - `wp-content/themes/saltelli/acf-json/group_theme_options_v1.json`
   - `wp-content/themes/saltelli/inc/seed-theme-options.php`

---

## 📋 PHASE 1 — Estrai TUTTI i call-site `saltelli_option` con fallback PHP (~5 min)

**Categoria A scope**.

```bash
cd ~/Desktop/DEV/saltelli-wp/

mkdir -p .claude/knowledge/audits/wave4-7-fix-2-investigation/

echo "=== Phase 1 — Call-site saltelli_option con fallback PHP ===" \
  > .claude/knowledge/audits/wave4-7-fix-2-investigation/phase1-callsites.txt

python3 <<'PYEND' >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase1-callsites.txt
import re, os

THEME_DIR = "wp-content/themes/saltelli/"
PATTERN = re.compile(r"""saltelli_option\(\s*['"]([^'"]+)['"]\s*(?:,\s*(.+?))?\s*\)""", re.DOTALL)

results = []
for root, dirs, files in os.walk(THEME_DIR):
    if any(skip in root for skip in ['/node_modules', '/build', '/.git']):
        continue
    for f in files:
        if not f.endswith('.php'): continue
        filepath = os.path.join(root, f)
        try:
            with open(filepath, 'r', encoding='utf-8') as fh:
                lines = fh.readlines()
        except Exception: continue
        for line_num, line in enumerate(lines, 1):
            for m in PATTERN.finditer(line):
                name = m.group(1)
                fb_raw = (m.group(2) or '').strip()
                str_match = re.match(r"^['\"](.+?)['\"]\s*\)?\s*$", fb_raw, re.DOTALL)
                fb_clean = str_match.group(1) if str_match else fb_raw
                has_fb_string = bool(str_match) and len(fb_clean) > 0
                results.append((filepath.replace(THEME_DIR, ''), line_num, name, has_fb_string, fb_clean))

with_fb = [r for r in results if r[3]]
print(f"Total call-site: {len(results)}")
print(f"Con fallback string literal: {len(with_fb)}")

# Group by name
by_name = {}
for filepath, line_num, name, has_fb, fb in with_fb:
    by_name.setdefault(name, []).append((filepath, line_num, fb))

print(f"Unique field name con fallback: {len(by_name)}")
print(f"\n=== UNIQUE field with hardcoded fallback ===")
for name in sorted(by_name.keys()):
    entries = by_name[name]
    print(f"\n  {name}  ({len(entries)} call-site):")
    for filepath, line_num, fb in entries:
        print(f"    {filepath}:{line_num}  fallback={fb[:80]}")
PYEND

echo ""
tail -40 .claude/knowledge/audits/wave4-7-fix-2-investigation/phase1-callsites.txt
```

---

## 📋 PHASE 2 — Estrai default_value JSON per OGNI field (~5 min)

**Categoria A scope**.

```bash
echo "=== Phase 2 — JSON default_value scan ===" \
  > .claude/knowledge/audits/wave4-7-fix-2-investigation/phase2-json-defaults.txt

python3 <<'PYEND' >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase2-json-defaults.txt
import json

with open("wp-content/themes/saltelli/acf-json/group_theme_options_v1.json") as f:
    data = json.load(f)

results = []
def walk(fields, prefix=''):
    for f in fields:
        if f.get('type') == 'tab': continue
        name = prefix + f.get('name', '')
        if f.get('type') in ['repeater', 'group'] and 'sub_fields' in f:
            results.append((name, f.get('type'), '(complex)', False))
            walk(f['sub_fields'], name + '.')
            continue
        default = f.get('default_value', None)
        has_def = bool(default) if not isinstance(default, (int, float, bool)) else True
        display = '(empty)' if not default else str(default)[:80]
        results.append((name, f.get('type'), display, has_def))

walk(data.get('fields', []))

empty = [r for r in results if not r[3] and r[2] != '(complex)']
populated = [r for r in results if r[3]]

print(f"Total field nel JSON (excl tabs): {len(results)}")
print(f"CON default_value: {len(populated)}")
print(f"SENZA default_value (empty): {len(empty)}")

print(f"\n=== Field SENZA default_value ===")
for name, ftype, display, _ in empty:
    print(f"  {name}  type={ftype}")

print(f"\n=== Field CON default_value (sample 20) ===")
for name, ftype, display, _ in populated[:20]:
    print(f"  {name}  type={ftype}  default={display}")
PYEND

head -60 .claude/knowledge/audits/wave4-7-fix-2-investigation/phase2-json-defaults.txt
```

---

## 📋 PHASE 3 — Cross-reference PHP fallback vs JSON default_value (~5 min)

**Categoria A scope — THE KEY SCAN**.

```bash
echo "=== Phase 3 — Cross-reference PHP fallback vs JSON default ===" \
  > .claude/knowledge/audits/wave4-7-fix-2-investigation/phase3-mismatch.txt

python3 <<'PYEND' >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase3-mismatch.txt
import re, os, json

THEME_DIR = "wp-content/themes/saltelli/"
JSON_PATH = "wp-content/themes/saltelli/acf-json/group_theme_options_v1.json"
PATTERN = re.compile(r"""saltelli_option\(\s*['"]([^'"]+)['"]\s*(?:,\s*(.+?))?\s*\)""", re.DOTALL)

php_fb = {}
for root, dirs, files in os.walk(THEME_DIR):
    if any(s in root for s in ['/node_modules', '/build', '/.git']): continue
    for f in files:
        if not f.endswith('.php'): continue
        fp = os.path.join(root, f)
        try:
            with open(fp, 'r', encoding='utf-8') as fh: lines = fh.readlines()
        except: continue
        for ln, line in enumerate(lines, 1):
            for m in PATTERN.finditer(line):
                name = m.group(1)
                fb_raw = (m.group(2) or '').strip()
                str_m = re.match(r"^['\"](.+?)['\"]\s*\)?\s*$", fb_raw, re.DOTALL)
                fb = str_m.group(1) if str_m else ''
                if fb:
                    php_fb.setdefault(name, []).append((fp.replace(THEME_DIR, ''), ln, fb))

with open(JSON_PATH) as f: jd = json.load(f)
json_def = {}
def walk(fields, prefix=''):
    for f in fields:
        if f.get('type') == 'tab': continue
        n = prefix + f.get('name', '')
        if f.get('type') in ['repeater', 'group'] and 'sub_fields' in f:
            walk(f['sub_fields'], n + '.')
            continue
        json_def[n] = f.get('default_value', '') or ''
walk(jd.get('fields', []))

critical, diverging, aligned, only_php = [], [], [], []
for name, entries in php_fb.items():
    php_first = entries[0][2]
    if name not in json_def:
        only_php.append((name, php_first, entries))
        continue
    jd_val = str(json_def[name])
    if not jd_val:
        critical.append((name, php_first, jd_val, entries))
    elif php_first.strip() == jd_val.strip():
        aligned.append((name, php_first))
    else:
        diverging.append((name, php_first, jd_val, entries))

print(f"\n=== SUMMARY ===")
print(f"Mismatch CRITICAL (PHP fallback + JSON empty): {len(critical)}")
print(f"Mismatch DIVERGING (PHP != JSON): {len(diverging)}")
print(f"OK aligned: {len(aligned)}")
print(f"Field PHP custom (no JSON): {len(only_php)}")

print(f"\n=== MISMATCH CRITICAL ({len(critical)}) ===")
print("(admin vuoto, frontend mostra fallback PHP — Elena non sa dove modificare)")
for name, php_fb_v, jd_val, entries in critical:
    print(f"\n  {name}:")
    print(f"    PHP fallback : {php_fb_v[:100]}")
    print(f"    JSON default: '{jd_val}'")
    print(f"    Call-sites ({len(entries)}):")
    for fp, ln, _ in entries:
        print(f"      {fp}:{ln}")

print(f"\n=== MISMATCH DIVERGING ({len(diverging)}) ===")
for name, php_fb_v, jd_val, entries in diverging:
    print(f"\n  {name}:")
    print(f"    PHP : {php_fb_v[:100]}")
    print(f"    JSON: {jd_val[:100]}")

print(f"\n=== Field PHP custom (no JSON) - {len(only_php)} ===")
for name, php_fb_v, _ in only_php:
    print(f"  {name}: {php_fb_v[:80]}")
PYEND

head -30 .claude/knowledge/audits/wave4-7-fix-2-investigation/phase3-mismatch.txt
```

---

## 📋 PHASE 4 — Verifica DB staging wp_options.options_* state (~3 min)

**Categoria A scope**.

```bash
echo "=== Phase 4 — DB staging wp_options.options_* ===" \
  > .claude/knowledge/audits/wave4-7-fix-2-investigation/phase4-db-state.txt

ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
  global \$wpdb;
  \$rows = \$wpdb->get_results(\"SELECT option_name, LENGTH(option_value) as len, LEFT(option_value, 80) as preview FROM {\$wpdb->options} WHERE option_name LIKE \\\"options_%\\\" AND option_name NOT LIKE \\\"_options_%\\\" ORDER BY option_name\");
  echo \"Total: \" . count(\$rows) . \"\\n\\n\";
  foreach (\$rows as \$r) {
    \$pv = \$r->preview ?: \"(empty)\";
    echo \"  {\$r->option_name}  len=\$r->len  preview=\\\"\$pv\\\"\\n\";
  }
' --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase4-db-state.txt

tail -30 .claude/knowledge/audits/wave4-7-fix-2-investigation/phase4-db-state.txt
```

**Atteso**: 50 righe (Wave 4.7.fix Phase 2 seed). Cerca `len=0` o preview `(empty)`.

---

## 📋 PHASE 6 — Page Discovery Map per i 15 URL Elena (~5 min)

**Categoria B scope** (Phase 5 saltata, riposizionata come Phase 9 Report finale).

Per ognuno dei 15 URL Elena lamenta, identificare:
1. **Source rendering**: Page WP standard / CPT custom / Template hub PHP / 404
2. **Posizione admin**: dove Elena dovrebbe andare per modificare
3. **Editability score**: 100% editable / partial / hardcoded
4. **Gap UX**: cosa Elena vede vs cosa esiste in admin

```bash
echo "=== Phase 6 — Page Discovery Map (15 URL Elena) ===" \
  > .claude/knowledge/audits/wave4-7-fix-2-investigation/phase6-page-discovery.txt

# I 15 URL Elena (compatti, dedupe)
URLS=(
  "/chi-siamo/"
  "/chi-siamo/team/"
  "/chi-siamo/risultati/"
  "/aree-di-pratica/"
  "/aree-di-pratica/privati/"
  "/aree-di-pratica/imprese/"
  "/aree-di-pratica/contenzioso-amministrativo/"
  "/risorse/"
  "/risorse/domande-frequenti/"
  "/risorse/guide-gratuite/"
  "/risorse/glossario-legale/"
  "/costi-e-consulenze/"
  "/costi-e-consulenze/prima-consulenza/"
  "/costi-e-consulenze/come-lavoriamo/"
  "/costi-e-consulenze/richiedi-preventivo/"
)

# Per ogni URL: HTTP HEAD + WP query rivedi
for url in "${URLS[@]}"; do
  echo "" >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase6-page-discovery.txt
  echo "=== URL: $url ===" >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase6-page-discovery.txt

  # HTTP status + curl HEAD
  HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -L "https://staging.studiolegalesaltelli.it$url" --max-time 10)
  echo "  HTTP code: $HTTP_CODE" >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase6-page-discovery.txt

  # WP query: identifica source rendering
  ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
    \$url_path = \"$url\";
    // Strip leading/trailing slashes for query
    \$path_clean = trim(\$url_path, \"/\");

    // Try get_page_by_path (Pages standard)
    \$page = get_page_by_path(\$path_clean, OBJECT, [\"page\"]);
    if (\$page) {
      echo \"  source: PAGE wp_posts.ID=\$page->ID title=\\\"\".html_entity_decode(\$page->post_title).\"\\\" status=\$page->post_status template=\".(\$page->_wp_page_template ?? \"default\").\"\\n\";
      echo \"  admin_edit: /wp-admin/post.php?post=\$page->ID&action=edit\\n\";
      \$content_len = strlen(\$page->post_content);
      echo \"  post_content_len: \$content_len chars\\n\";
      exit;
    }

    // Try CPT competenza by slug
    \$slug_only = basename(\$path_clean);
    \$cpt_competenza = get_page_by_path(\$slug_only, OBJECT, [\"competenza\"]);
    if (\$cpt_competenza) {
      echo \"  source: CPT competenza ID=\$cpt_competenza->ID title=\\\"\$cpt_competenza->post_title\\\"\\n\";
      echo \"  admin_edit: /wp-admin/post.php?post=\$cpt_competenza->ID&action=edit\\n\";
      exit;
    }

    // Try saltelli_caso
    \$cpt_caso = get_page_by_path(\$slug_only, OBJECT, [\"saltelli_caso\"]);
    if (\$cpt_caso) {
      echo \"  source: CPT saltelli_caso ID=\$cpt_caso->ID title=\\\"\$cpt_caso->post_title\\\"\\n\";
      echo \"  admin_edit: /wp-admin/post.php?post=\$cpt_caso->ID&action=edit\\n\";
      exit;
    }

    // Try CPT avvocato
    \$cpt_av = get_page_by_path(\$slug_only, OBJECT, [\"avvocato\"]);
    if (\$cpt_av) {
      echo \"  source: CPT avvocato ID=\$cpt_av->ID title=\\\"\$cpt_av->post_title\\\"\\n\";
      echo \"  admin_edit: /wp-admin/post.php?post=\$cpt_av->ID&action=edit\\n\";
      exit;
    }

    // Try term tipo-area
    \$term = get_term_by(\"slug\", \$slug_only, \"tipo-area\");
    if (\$term) {
      echo \"  source: TERM tipo-area term_id=\$term->term_id name=\\\"\$term->name\\\" slug=\$term->slug\\n\";
      echo \"  admin_edit: /wp-admin/term.php?taxonomy=tipo-area&tag_ID=\$term->term_id\\n\";
      echo \"  description_len: \".strlen(\$term->description).\" chars\\n\";
      exit;
    }

    // Fallback: not found in standard sources → likely template hub PHP
    echo \"  source: NOT FOUND in pages/CPT/term — likely PHP template hub or 404\\n\";
    echo \"  admin_edit: NONE (probabilmente template-parts/page-*-hub.php hardcoded)\\n\";
  ' --path=/var/www/saltelli" \
    >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase6-page-discovery.txt 2>&1
done

echo ""
head -100 .claude/knowledge/audits/wave4-7-fix-2-investigation/phase6-page-discovery.txt
```

---

## 📋 PHASE 7 — URL/Title incongruence + Menu mapping (~3 min)

**Categoria C scope**.

### 7.1 URL/Title check (`/risultati/` decision)

Decision Duccio già presa: rename slug `/chi-siamo/risultati/` → `/chi-siamo/casi-rappresentativi/` con redirect legacy. Investigation Phase 7 documenta solo lo **stato attuale** + altri URL/title incongruence simili.

```bash
echo "=== Phase 7 — URL/Title check ===" \
  > .claude/knowledge/audits/wave4-7-fix-2-investigation/phase7-url-menu.txt

ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
  global \$wpdb;
  // Find pages with slug != normalized title
  \$pages = \$wpdb->get_results(\"SELECT ID, post_title, post_name, post_status FROM {\$wpdb->posts} WHERE post_type=\\\"page\\\" AND post_status=\\\"publish\\\"\");

  echo \"=== Page slug vs title (potential mismatch) ===\\n\";
  foreach (\$pages as \$p) {
    \$title_slug = sanitize_title(\$p->post_title);
    if (\$title_slug !== \$p->post_name) {
      echo \"  ID {\$p->ID}: slug=\\\"\".\$p->post_name.\"\\\" title=\\\"\".html_entity_decode(\$p->post_title).\"\\\" (sanitize_title→\$title_slug)\\n\";
    }
  }
' --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase7-url-menu.txt
```

### 7.2 Menu mapping

```bash
echo "" >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase7-url-menu.txt
echo "=== Menu items (nav_menu) check ===" >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase7-url-menu.txt

ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
  // List all menus
  \$menus = wp_get_nav_menus();
  echo \"=== Registered menus ===\\n\";
  foreach (\$menus as \$m) {
    echo \"\\nMenu: {\$m->name} (slug={\$m->slug}, term_id={\$m->term_id}, count={\$m->count})\\n\";

    \$items = wp_get_nav_menu_items(\$m->term_id);
    if (\$items) {
      foreach (\$items as \$it) {
        \$indent = \$it->menu_item_parent != \"0\" ? \"    \" : \"  \";
        echo \"{\$indent}- {\$it->title} → {\$it->url}\\n\";
      }
    }
  }
' --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase7-url-menu.txt
```

### 7.3 Verifica `/risorse/` clic comportamento (Elena: clic risorse → mostra domande-frequenti)

```bash
echo "" >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase7-url-menu.txt
echo "=== /risorse/ behavior check ===" >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase7-url-menu.txt

# Final URL post-redirect
RESORSE_FINAL=$(curl -s -o /dev/null -w "%{url_effective}" -L "https://staging.studiolegalesaltelli.it/risorse/" --max-time 10)
echo "  /risorse/ final URL after redirect: $RESORSE_FINAL" \
  >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase7-url-menu.txt

# Page exist?
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
  \$risorse = get_page_by_path(\"risorse\", OBJECT, [\"page\"]);
  echo \"  Page risorse exists: \" . (\$risorse ? \"YES (ID {\$risorse->ID}, status {\$risorse->post_status})\" : \"NO\") . \"\\n\";

  // Risorse hub template part?
  echo \"  Template-parts/page-risorse-hub.php exists: \" . (file_exists(get_template_directory() . \"/template-parts/page-risorse-hub.php\") ? \"YES\" : \"NO\") . \"\\n\";
' --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase7-url-menu.txt
```

### 7.4 Verifica esistenza `/costi-e-consulenze/` + 3 sub-pages

```bash
echo "" >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase7-url-menu.txt
echo "=== /costi-e-consulenze/ pages exist? ===" >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase7-url-menu.txt

for slug in "costi-e-consulenze" "costi-e-consulenze/prima-consulenza" "costi-e-consulenze/come-lavoriamo" "costi-e-consulenze/richiedi-preventivo"; do
  ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
    \$p = get_page_by_path(\"$slug\", OBJECT, [\"page\"]);
    if (\$p) {
      echo \"  $slug: YES (ID {\$p->ID}, status {\$p->post_status}, template \" . (\$p->_wp_page_template ?? \"default\") . \")\\n\";
    } else {
      echo \"  $slug: NO (Page non trovata)\\n\";
    }
  ' --path=/var/www/saltelli" \
    >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase7-url-menu.txt
done
```

---

## 📋 PHASE 8 — Recurring blocks cross-page scan (~5 min)

**Categoria D scope** — la grande domanda di Duccio.

Identificare blocchi che si ripetono cross-template e classificarli:
- **Già SCF Theme Options** (Elena può modificare)
- **Hardcoded — da migrare a SCF**
- **Hardcoded ma OK** (decisione architetturale, non editoriale)

### 8.1 Pattern-based scan

```bash
echo "=== Phase 8 — Recurring blocks cross-page scan ===" \
  > .claude/knowledge/audits/wave4-7-fix-2-investigation/phase8-recurring-blocks.txt

# Identifica template-parts che si includono in più di N template
echo "" >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase8-recurring-blocks.txt
echo "=== Template-parts utilizzati cross-template ===" \
  >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase8-recurring-blocks.txt

python3 <<'PYEND' >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase8-recurring-blocks.txt
import re, os
from collections import defaultdict

THEME_DIR = "wp-content/themes/saltelli/"
GET_PART_PATTERN = re.compile(r"""get_template_part\(\s*['"]([^'"]+)['"](?:\s*,\s*['"]([^'"]*)['"])?""", re.DOTALL)

template_part_usage = defaultdict(list)  # part_path -> [(file_using_it, line)]

for root, dirs, files in os.walk(THEME_DIR):
    if any(s in root for s in ['/node_modules', '/build', '/.git']): continue
    for f in files:
        if not f.endswith('.php'): continue
        fp = os.path.join(root, f)
        try:
            with open(fp, 'r', encoding='utf-8') as fh: content = fh.read()
        except: continue
        for ln, line in enumerate(content.split('\n'), 1):
            for m in GET_PART_PATTERN.finditer(line):
                slug = m.group(1)
                name = m.group(2) or ''
                full = f"{slug}-{name}" if name else slug
                template_part_usage[full].append((fp.replace(THEME_DIR, ''), ln))

print(f"\n=== get_template_part usage (>= 2 includes = recurring) ===")
recurring = {k: v for k, v in template_part_usage.items() if len(v) >= 2}
for part, includes in sorted(recurring.items(), key=lambda x: -len(x[1])):
    print(f"\n  {part}.php — used in {len(includes)} templates:")
    for fp, ln in includes[:5]:
        print(f"    {fp}:{ln}")
    if len(includes) > 5:
        print(f"    ... +{len(includes)-5} more")
PYEND

echo ""
head -60 .claude/knowledge/audits/wave4-7-fix-2-investigation/phase8-recurring-blocks.txt
```

### 8.2 Identifica recurring blocks specifici (CTA, Newsletter, Trust, Sticky)

```bash
echo "" >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase8-recurring-blocks.txt
echo "=== Recurring block patterns scan ===" \
  >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase8-recurring-blocks.txt

python3 <<'PYEND' >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase8-recurring-blocks.txt
import re, os
from collections import defaultdict

THEME_DIR = "wp-content/themes/saltelli/"

# Pattern signatures per blocchi ricorrenti tipici
BLOCK_PATTERNS = {
    "CTA 'ultima chiamata' / pre-footer": [
        r'class=["\'][^"\']*sl-cta[^"\']*["\']',
        r'(ultima\s*chiamata|prenota.*incontro|consulenza.*gratuita)',
        r'data-cta=',
    ],
    "Banda Newsletter": [
        r'class=["\'][^"\']*newsletter[^"\']*["\']',
        r'(newsletter|iscriviti.*newsletter|brevo|mailchimp)',
        r'<!--\s*newsletter',
    ],
    "Trust signals plate (4 plate)": [
        r'class=["\'][^"\']*trust[-_]signal[^"\']*["\']',
        r'trust[-_]plate',
        r'brand[-_]trust',
    ],
    "Sticky widget (WhatsApp/Phone)": [
        r'sl-whatsapp-sticky',
        r'class=["\'][^"\']*sticky[-_]widget[^"\']*["\']',
        r'data-sticky',
    ],
    "Footer colophon": [
        r'class=["\'][^"\']*colophon[^"\']*["\']',
        r'<!--\s*colophon',
        r'sl-footer__col',
    ],
    "Header navigation": [
        r'class=["\'][^"\']*sl-header[^"\']*["\']',
        r'wp_nav_menu.*primary',
    ],
}

results = defaultdict(list)  # block_name -> [(filepath, line, pattern_match)]
files_scanned = 0

for root, dirs, files in os.walk(THEME_DIR):
    if any(s in root for s in ['/node_modules', '/build', '/.git']): continue
    for f in files:
        if not f.endswith('.php'): continue
        fp = os.path.join(root, f)
        try:
            with open(fp, 'r', encoding='utf-8') as fh: content = fh.read()
        except: continue
        files_scanned += 1
        for block_name, patterns in BLOCK_PATTERNS.items():
            for pat in patterns:
                for m in re.finditer(pat, content, re.IGNORECASE | re.DOTALL):
                    # Find line number
                    line_num = content[:m.start()].count('\n') + 1
                    snippet = content[max(0, m.start()-20):m.end()+20].replace('\n', ' ')[:100]
                    results[block_name].append((fp.replace(THEME_DIR, ''), line_num, snippet))
                    break  # 1 match per pattern per file (per evitare flood)

print(f"\n(Scanned {files_scanned} PHP files)")

for block_name, hits in results.items():
    print(f"\n=== {block_name} ===")
    files_unique = set(h[0] for h in hits)
    print(f"  Found in {len(files_unique)} files, {len(hits)} matches total")
    for fp, ln, snip in hits[:8]:
        print(f"    {fp}:{ln}  {snip[:80]}")
    if len(hits) > 8:
        print(f"    ... +{len(hits)-8} more")
PYEND

echo ""
head -80 .claude/knowledge/audits/wave4-7-fix-2-investigation/phase8-recurring-blocks.txt
```

### 8.3 Cross-check con SCF Theme Options esistenti

Per ogni recurring block identificato, verificare se ha già field SCF associati:

```bash
echo "" >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase8-recurring-blocks.txt
echo "=== Recurring blocks vs SCF Theme Options coverage ===" \
  >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase8-recurring-blocks.txt

python3 <<'PYEND' >> .claude/knowledge/audits/wave4-7-fix-2-investigation/phase8-recurring-blocks.txt
import json

with open("wp-content/themes/saltelli/acf-json/group_theme_options_v1.json") as f:
    data = json.load(f)

# Estrai tutti i field name + tab parent
fields_by_tab = {}
current_tab = None
def walk(fields, prefix=''):
    global current_tab
    for f in fields:
        if f.get('type') == 'tab':
            current_tab = f.get('label', f.get('name'))
            fields_by_tab.setdefault(current_tab, [])
            continue
        n = prefix + f.get('name', '')
        if current_tab:
            fields_by_tab[current_tab].append({
                'name': n, 'type': f.get('type'), 'label': f.get('label', '')
            })
        if f.get('type') in ['repeater', 'group'] and 'sub_fields' in f:
            walk(f['sub_fields'], n + '.')
walk(data.get('fields', []))

print("\n=== SCF Theme Options coverage by tab ===")
for tab_name, fields in fields_by_tab.items():
    print(f"\n  Tab: {tab_name} ({len(fields)} field)")
    for f in fields[:6]:
        print(f"    - {f['name']} (type={f['type']}, label={f['label'][:40]})")
    if len(fields) > 6:
        print(f"    ... +{len(fields)-6} more")

# Coverage analysis: sappiamo che footer + social + cta defaults sono in tab dedicate
# Cross-ref con recurring blocks identificati Phase 8.2
RECURRING_BLOCKS_EXPECTED_TABS = {
    "CTA 'ultima chiamata' / pre-footer": "CTA Defaults",
    "Banda Newsletter": "Footer",
    "Trust signals plate (4 plate)": "Brand",
    "Sticky widget (WhatsApp/Phone)": "(NESSUNA — verifica)",
    "Footer colophon": "Footer",
    "Header navigation": "(WP nav_menu, NON in Theme Options)",
}

print(f"\n=== Recurring blocks expected coverage ===")
for block, expected_tab in RECURRING_BLOCKS_EXPECTED_TABS.items():
    in_scf = expected_tab in fields_by_tab
    if in_scf:
        n_fields = len(fields_by_tab[expected_tab])
        status = f"✓ COVERED in tab '{expected_tab}' ({n_fields} field)"
    elif "NON" in expected_tab or "NESSUNA" in expected_tab:
        status = f"✗ NOT IN SCF: {expected_tab}"
    else:
        status = f"⚠ EXPECTED tab '{expected_tab}' MISSING"
    print(f"  {block}: {status}")
PYEND

tail -30 .claude/knowledge/audits/wave4-7-fix-2-investigation/phase8-recurring-blocks.txt
```

---

## 📋 PHASE 9 — Report finale unificato + raccomandazioni (~3 min)

Crea `.claude/knowledge/recovery/WAVE4-7-FIX-2-INVESTIGATION-REPORT.md`:

```markdown
# Wave 4.7.fix.2 Investigation v2 — Admin↔Frontend + Page Discovery + URL/Menu + Recurring Blocks · Report

**Data**: <ISO timestamp>
**Scope**: READ-ONLY diagnostic scan, 4 categorie A+B+C+D
**Pattern**: sequenziale 8 phases
**Branch**: main (no branch nuovo, sessione read-only)
**Riferimento**: bug Duccio 2026-05-08 + feedback Elena (15 URL "non trovo dove modificare" + blocchi ricorrenti)

---

## TL;DR (4 frasi)

1. **Categoria A — Mismatch admin/frontend**: <N> field con PHP fallback ≠ JSON empty (CRITICAL) + <M> DIVERGING.
2. **Categoria B — Page discoverability**: <X>/15 URL Elena puntano a <breakdown sources>: <N> Pages standard, <M> CPT, <K> template hub PHP hardcoded, <Z> 404.
3. **Categoria C — URL/Menu incongruence**: <list bugs identified>.
4. **Categoria D — Recurring blocks**: <N>/6 blocchi ricorrenti già coperti SCF, <M> hardcoded da migrare per editing Elena.

---

## 📊 Numeri chiave

| Metrica | Valore |
|---|---|
| Total call-site `saltelli_option` | <N> |
| Mismatch CRITICAL (PHP+JSON empty) | **<N>** |
| Mismatch DIVERGING | **<N>** |
| OK aligned | <N> |
| 15 URL Elena classificati | <breakdown> |
| Pages WP esistenti dei 15 URL | <N> |
| CPT come source dei 15 URL | <N> |
| Template hub PHP hardcoded | <N> |
| 404 (URL inesistenti in CMS) | <N> |
| Recurring blocks identified | <N> |
| Recurring blocks SCF-covered | <N> |
| Recurring blocks da migrare | <N> |

---

## 🅰️ Sezione A — Mismatch CRITICAL admin↔frontend

[ ... lista field da Phase 3 ... ]

## 🅱️ Sezione B — Page Discovery Map (15 URL Elena)

| URL | Source | Editable Position | Score | Note |
|---|---|---|---|---|
| /chi-siamo/ | <Page/CPT/Hub PHP> | <admin path> | <100/partial/0> | <gap UX> |
| /chi-siamo/team/ | ... | ... | ... | ... |
| ... (tutti 15) ... |

## 🇨 Sezione C — URL/Title/Menu incongruence

- `/chi-siamo/risultati/` → title "Casi rappresentativi" → **decision Duccio**: rename slug a `casi-rappresentativi` con redirect legacy
- `/risorse/` → redirect to `/risorse/domande-frequenti/` → <causa>
- `/costi-e-consulenze/` + 3 subpages → <esistono / non esistono>
- Altri URL/title mismatch identificati: <list>

## 🇩 Sezione D — Recurring blocks coverage

| Block | Used in N templates | SCF-covered | Action raccomandata |
|---|---|---|---|
| CTA 'ultima chiamata' | <N> | <YES/NO> | <recommendation> |
| Banda Newsletter | <N> | <YES/NO> | <recommendation> |
| Trust signals plate | <N> | <YES/NO> | <recommendation> |
| Sticky WhatsApp widget | <N> | <YES/NO> | <recommendation> |
| Footer colophon | <N> | <YES/NO> | <recommendation> |
| Header navigation | <N> | <YES/NO> | <recommendation> |

---

## 🎯 Diagnosi finale

[ ... valutazione tecnica complessiva ... ]

---

## 🛠️ Raccomandazione fix path (Wave 4.7.fix.2 vero)

Scope unificato proposto, prioritizzato:

### Priority 1 — Categoria A: Re-seed DB + JSON alignment

[ ... Strategy 1/2/3 dal report v1 ... ]

### Priority 2 — Categoria C: Slug rename + redirect

```
DECISION DUCCIO ALREADY TAKEN:
- Rename /chi-siamo/risultati/ → /chi-siamo/casi-rappresentativi/
- wp post update <id> --post_name=casi-rappresentativi
- wp rewrite flush
- Add legacy redirect /chi-siamo/risultati/ → /chi-siamo/casi-rappresentativi/ 301
- Update menu items if hardcoded
```

Plus altri URL/title mismatch identificati Phase 7.

### Priority 3 — Categoria B: Discoverability admin

[ ... per ogni URL: dove documentare il path admin in EDITOR-HANDOFF.md v3 ... ]

### Priority 4 — Categoria D: Recurring blocks SCF migration

Per ogni block hardcoded identificato Phase 8:
- Aggiungere SCF field in `group_theme_options_v1.json` nuovo tab dedicato (es. "Blocchi ricorrenti")
- Refactor template-parts per usare `saltelli_option(name, 'fallback')` con fallback identico a default_value JSON
- Wave 4.7.fix Phase 2 seed re-eseguito o seed aggiuntivo per nuovi field
- Cost: <stima onesta>

---

## ⏱️ Stima costi totali Wave 4.7.fix.2 vero

| Priority | Categoria | Tempo Code | Multi-agent? |
|---|---|---|---|
| P1 | A — Mismatch fix | <X> min | <Y/N> |
| P2 | C — Slug rename | <Y> min | N |
| P3 | B — Documentation | <Z> min | N |
| P4 | D — Recurring blocks SCF | <W> min | possibly |
| **TOTAL** | | **<sum>** min | |

Confronto con sequenziale separato: <onestà — Wave 4.7.fix.2 vero in 1 sessione vs 4 sessioni separate>.

---

## 🚦 Open items per orchestratore

1. Audit findings + decisione fix path consolidato
2. Decisione scope Wave 4.7.fix.2 vero (P1+P2 minimum, oppure P1+P2+P4 unified)
3. P3 (documentation) può essere parte di EDITOR-HANDOFF.md v3 post-fix
4. Validazione decision slug rename `casi-rappresentativi` con cliente (è SEO impact)

---

## 🔗 Riferimenti

- DEC-040-COMPLETED (Wave 4.7.fix.1)
- DEC-039-COMPLETED (Wave 4.7.fix)
- CMS Diagnosis Round 2 REPORT.md
- Bug Duccio 2026-05-08 + feedback Elena 15 URL
- DEC-029 (Wave 4.6 origine fallback pattern)
- DEC-021 (URL audit-aligned 17 cliente-firmato)
```

### 9.1 Commit finale (1 solo commit)

```bash
git add .claude/knowledge/audits/wave4-7-fix-2-investigation/
git add .claude/knowledge/recovery/WAVE4-7-FIX-2-INVESTIGATION-REPORT.md
git add prompts/PROMPT_AGENT_WAVE4_7_FIX_2_INVESTIGATION_V2.md

git commit -m "wave4-7-fix-2-investigation-v2: read-only scan 4 categorie

Investigation diagnostica espansa post feedback Elena 2026-05-08:
1. Categoria A: Admin↔Frontend mismatch (PHP fallback vs JSON default)
2. Categoria B: Page discoverability (15 URL Elena 'non trovo dove modificare')
3. Categoria C: URL/Title/Menu incongruence (/risultati → casi-rappresentativi decision Duccio)
4. Categoria D: Recurring blocks cross-page (CTA, Newsletter, Trust, Sticky, Footer)

Phases (READ-ONLY):
1. Estrai call-site saltelli_option con fallback PHP
2. Estrai default_value JSON
3. Cross-reference PHP vs JSON → identify mismatch
4. DB staging wp_options.options_* state
6. Page Discovery Map per 15 URL Elena
7. URL/Title incongruence + menu mapping
8. Recurring blocks scan + SCF coverage analysis
9. Report unificato + raccomandazione

Risultati chiave:
- Mismatch CRITICAL: <N>
- Pages mapping breakdown: <N> Pages, <M> CPT, <K> Hub PHP, <Z> 404
- Recurring blocks: <N>/6 SCF-covered, <M> da migrare

Audit logs: .claude/knowledge/audits/wave4-7-fix-2-investigation/
Report: .claude/knowledge/recovery/WAVE4-7-FIX-2-INVESTIGATION-REPORT.md

NO modifiche DB / FS / config. Output → orchestratore decide scope Wave 4.7.fix.2 vero."

# NO push automatico (sessione investigation)
# Orchestratore audisce + decide scope + lancia Wave 4.7.fix.2 vero
```

---

## ✅ Acceptance criteria

- [ ] 8 phases eseguite (Phase 5 saltata, riposizionata come 9 Report finale)
- [ ] **NO modifiche DB / FS / config** (verificato: zero `update_*`, zero scritture)
- [ ] **NO branch nuovo**
- [ ] Audit trail in `.claude/knowledge/audits/wave4-7-fix-2-investigation/`:
  - `phase1-callsites.txt` (Categoria A)
  - `phase2-json-defaults.txt` (Categoria A)
  - `phase3-mismatch.txt` (Categoria A)
  - `phase4-db-state.txt` (Categoria A)
  - `phase6-page-discovery.txt` (Categoria B)
  - `phase7-url-menu.txt` (Categoria C)
  - `phase8-recurring-blocks.txt` (Categoria D)
- [ ] Report `.claude/knowledge/recovery/WAVE4-7-FIX-2-INVESTIGATION-REPORT.md` con 4 sezioni
- [ ] 1 commit finale (NO push automatico)

---

## 🚨 Cosa fare in caso di problemi

| Situazione | Action |
|---|---|
| Phase 1-4 grep/JSON parse fail | STOP, riporta orchestratore con stack |
| Phase 6 ssh fail | STOP, possibile firewall/credentials |
| Phase 7 menu query no result | OK, documenta nel report (possibile assenza menu items) |
| Phase 8 recurring blocks 0 trovati | OK, documenta che pattern signature non match (possibile naming diverso) |
| Mismatch CRITICAL = 0 | OK report comunque, possibile bug isolato |

---

## 🎯 Output expected

1. NO branch nuovo (sessione investigation)
2. 7 audit log in `.claude/knowledge/audits/wave4-7-fix-2-investigation/`
3. Report unificato `WAVE4-7-FIX-2-INVESTIGATION-REPORT.md` 4 sezioni
4. 1 commit finale (NO push automatico)
5. Restituisci a orchestratore:
   - **Numeri chiave** Categoria A/B/C/D (Mismatch count, Page mapping breakdown, Recurring blocks coverage)
   - **Raccomandazione fix path** unificato Priority 1-4
   - **Stima costi totali** Wave 4.7.fix.2 vero
   - **Path file report**

---

## 🔗 Riferimenti

- DEC-041 (Wave 4.7.fix.2 Investigation lancio)
- DEC-040-COMPLETED (Wave 4.7.fix.1)
- DEC-039-COMPLETED (Wave 4.7.fix)
- DEC-029 (Wave 4.6 origine fallback pattern)
- DEC-021 (URL audit-aligned 17 cliente-firmato)
- CMS Diagnosis Round 2 REPORT.md
- Bug Duccio 2026-05-08 (Studio Section editor vuoto)
- Feedback Elena 2026-05-08 (15 URL "non trovo dove modificare" + blocchi ricorrenti)
- `inc/seed-theme-options.php` (Wave 4.7.fix Phase 2)
- `inc/helpers.php` `saltelli_option()`, `saltelli_field()`
- `CLAUDE.md` — single source of truth
