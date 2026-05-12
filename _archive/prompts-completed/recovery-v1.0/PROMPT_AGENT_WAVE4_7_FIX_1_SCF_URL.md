# 🔧 Claude Code Agent — Wave 4.7.fix.1 · SCF URL Validation Hotfix (v1.0)

> **Audience**: Claude Code agent in nuova sessione terminale dedicata.
> **Branch parent**: `main` (post-Wave 4.7.fix mergeata, tag `v1.3.6-wave4-7-fix-scf-migration`)
> **Branch nuovo**: `fix/wave4-7-fix-1-scf-url-validation`
> **Theme version target**: `1.3.7-wave4-7-fix-1-scf-url-validation`
> **Scope**: chirurgico — fix SCF URL validation strict che rifiuta URL relativi `/contatti/` su 2-3 field CTA interni.
> **Pattern**: sequenziale (NO multi-agent — scope troppo piccolo).
> **Tempo stimato**: ~30 min Code totali.
> **Riferimento**: bug riportato Duccio 2026-05-08 mattina post-deploy Wave 4.7.fix, screenshot WP Admin Saltelli Settings → "Validazione fallita. 2 campi necessitano attenzione" su `cta_hero_url` e `cta_default_url` con valore `/contatti/`.

---

## 🎯 Tu sei

Claude Code agent dedicato a **risolvere bug validazione URL SCF** scoperto nel primo test post Wave 4.7.fix.

**Sintomo**: WP Admin → Saltelli — Settings → tab Hero Homepage / CTA Defaults. Modifica qualunque campo + click "Aggiorna" → banner rosso "Validazione fallita. 2 campi necessitano attenzione" + campi `CTA hero — URL` e `CTA default URL` flaggati con "Il valore deve essere un URL valido". Valore corrente: `/contatti/`.

**Causa root**: SCF è **più strict** di ACF Free nella validazione del field `type: url`. ACF Free accettava URL relativi (`/path/`), SCF richiede URL assoluti (`https://...` o `http://...`).

**Side effect del switch ACF→SCF non identificato in Wave 4.7.fix Phase 1.5** (CPT regression check non testava field URL relativi su Theme Options).

**Fix path scelto**: **Strategy B** — modifica field type `url` → `text` per CTA URL interni (relativi). I field URL **esterni** (social, press_outlets) restano `type: url` (correttamente strict).

**Razionale architetturale**: URL CTA interni DEVONO essere relativi per portabilità staging↔produzione (al cut produzione Wave 7, URL assoluti hardcoded a staging punterebbero ancora a staging — bug critico). URL esterni (LinkedIn, Facebook, partner press) sono assoluti per natura.

---

## 🔒 Hard rules (non negotiabili)

1. **NO commit su main**. Sempre su `fix/wave4-7-fix-1-scf-url-validation`.
2. **Phase 1 PRE-FLIGHT MANDATORY**: backup completo del JSON `group_theme_options_v1.json` prima di modifiche.
3. **NO modifiche template PHP**: helper `saltelli_option()` invariato. `get_field()` ritorna string sia per `type: url` che `type: text` — frontend rendering invariato.
4. **NO modifiche a field URL esterni**: social_facebook, social_instagram, social_linkedin, social_twitter, press_outlets/url restano `type: url` (correttamente strict).
5. **Identificazione field interni rigorosa**: solo i campi che PUNTANO a pagine interne del sito (es. `/contatti/`, `/aree-di-pratica/`) cambiano a `type: text`. Verifica esplicita prima di modificare.
6. **Smoke test save E2E mandatory**: replica esatta dell'errore Duccio (modifica + Update + verifica NO errore validazione + verifica frontend reflect).
7. **NO regression Wave 5+6+4+4.5+4.6+4.7+4.7.1+4.8+4.7.fix smoke**.
8. **rsync `--checksum` mandatory** per deploy delta (DEC-036 lesson learned).
9. **Pattern HARD RULE STOP**: meglio safety di forced completion.

---

## 📚 Letture obbligatorie (nell'ordine)

1. **`CLAUDE.md`** — single source of truth (post Wave 4.7.fix v1.3.6)
2. **`.claude/knowledge/recovery/WAVE4-7-FIX-SCF-REPORT.md`** — Wave 4.7.fix report
3. **`prompts/PROMPT_AGENT_WAVE4_7_FIX_1_SCF_URL.md`** (questo file) end-to-end
4. **File da leggere obbligatoriamente**:
   - `wp-content/themes/saltelli/acf-json/group_theme_options_v1.json` (60+ field, identificare TUTTI quelli con `type: url`)

---

## 📋 PHASE 1 — Investigation field URL (~5 min)

### 1.1 Branch + backup JSON

```bash
cd ~/Desktop/DEV/saltelli-wp/

git fetch origin --prune
git checkout main
git pull --ff-only origin main   # → tag v1.3.6-wave4-7-fix-scf-migration

# Verifica baseline
grep "^Version:" wp-content/themes/saltelli/style.css
# Atteso: Version: 1.3.6-wave4-7-fix-scf-migration

git checkout -b fix/wave4-7-fix-1-scf-url-validation
mkdir -p .claude/knowledge/audits/wave4-7-fix-1/

# Backup JSON
cp wp-content/themes/saltelli/acf-json/group_theme_options_v1.json \
   .claude/knowledge/audits/wave4-7-fix-1/group_theme_options_v1.BACKUP.json
```

### 1.2 Identifica TUTTI i field `type: url` nel JSON

```bash
echo "=== Phase 1 — Identifica field type=url ===" > .claude/knowledge/audits/wave4-7-fix-1/phase1-investigation.txt

# Estrai tutti i field con type=url
python3 <<'PYEND' >> .claude/knowledge/audits/wave4-7-fix-1/phase1-investigation.txt
import json

with open('wp-content/themes/saltelli/acf-json/group_theme_options_v1.json') as f:
    data = json.load(f)

def walk(fields, prefix=''):
    results = []
    for f in fields:
        if f.get('type') == 'url':
            results.append({
                'name': prefix + f.get('name', ''),
                'label': f.get('label', ''),
                'default_value': f.get('default_value', ''),
                'instructions': f.get('instructions', '')[:80],
            })
        if f.get('type') in ['repeater', 'group'] and 'sub_fields' in f:
            results.extend(walk(f['sub_fields'], prefix + f.get('name', '') + '.'))
    return results

url_fields = walk(data.get('fields', []))
print(f"\nFOUND {len(url_fields)} field con type=url:\n")
for f in url_fields:
    print(f"  - name: {f['name']}")
    print(f"    label: {f['label']}")
    print(f"    default_value: {f['default_value']}")
    print(f"    instructions: {f['instructions']}")
    print()
PYEND

cat .claude/knowledge/audits/wave4-7-fix-1/phase1-investigation.txt
```

### 1.3 Classifica interni vs esterni

```bash
echo "" >> .claude/knowledge/audits/wave4-7-fix-1/phase1-investigation.txt
echo "=== Classification interni (cambiare a text) vs esterni (lasciare url) ===" \
  >> .claude/knowledge/audits/wave4-7-fix-1/phase1-investigation.txt

python3 <<'PYEND' >> .claude/knowledge/audits/wave4-7-fix-1/phase1-investigation.txt
import json

with open('wp-content/themes/saltelli/acf-json/group_theme_options_v1.json') as f:
    data = json.load(f)

INTERNAL_HINTS = ['cta_', 'footer_credit_url']  # field name patterns che suggeriscono URL interni
EXTERNAL_HINTS = ['social_', 'press_', 'newsletter_provider_url']  # patterns esterni

def walk(fields, prefix=''):
    results = []
    for f in fields:
        if f.get('type') == 'url':
            name = prefix + f.get('name', '')
            default = f.get('default_value', '')

            # Heuristica
            is_internal = (
                default.startswith('/') or  # default_value relativo
                any(h in name for h in INTERNAL_HINTS) or
                'contatti' in default or 'aree-di-pratica' in default
            )
            is_external = (
                default.startswith('http') or
                any(h in name for h in EXTERNAL_HINTS) or
                'facebook' in default or 'linkedin' in default or 'instagram' in default
            )

            results.append({
                'name': name,
                'classification': 'INTERNAL' if is_internal else ('EXTERNAL' if is_external else 'UNKNOWN'),
                'default_value': default,
            })
        if f.get('type') in ['repeater', 'group'] and 'sub_fields' in f:
            results.extend(walk(f['sub_fields'], prefix + f.get('name', '') + '.'))
    return results

fields = walk(data.get('fields', []))

internal = [f for f in fields if f['classification'] == 'INTERNAL']
external = [f for f in fields if f['classification'] == 'EXTERNAL']
unknown = [f for f in fields if f['classification'] == 'UNKNOWN']

print(f"\nINTERNAL ({len(internal)}) — change to type:text:")
for f in internal:
    print(f"  - {f['name']} (default: {f['default_value']})")

print(f"\nEXTERNAL ({len(external)}) — keep type:url:")
for f in external:
    print(f"  - {f['name']} (default: {f['default_value']})")

if unknown:
    print(f"\n⚠️ UNKNOWN ({len(unknown)}) — REQUIRES MANUAL REVIEW:")
    for f in unknown:
        print(f"  - {f['name']} (default: {f['default_value']})")
PYEND

cat .claude/knowledge/audits/wave4-7-fix-1/phase1-investigation.txt
```

### 1.4 Decision tree pre-modifica

Sulla base output Phase 1.3:

- Se `INTERNAL` count ≥ 2 (atteso): procedi Phase 2 con quei field
- Se `EXTERNAL` count ≥ 4 (atteso: social_*4 + press): mantieni `type: url`
- Se `UNKNOWN` count > 0: **STOP**, riporta orchestratore per classificazione manuale

**Atteso (basato su screenshot Duccio + JSON Wave 4.6)**:
```
INTERNAL (2-3):
  - cta_hero_url (default: /contatti/)
  - cta_default_url (default: /contatti/)
  - eventualmente footer_credit_url
EXTERNAL (4+):
  - social_facebook, social_instagram, social_linkedin, social_twitter
  - press_outlets.url (sub-field repeater)
```

### 1.5 Commit Phase 1

```bash
git add .claude/knowledge/audits/wave4-7-fix-1/
git commit -m "wave4-7-fix-1: phase 1 — investigation field type=url + classification

Findings (Phase 1.3 classification):
- INTERNAL count: <N> (cta_hero_url, cta_default_url, ...)
- EXTERNAL count: <N> (social_*, press_outlets.url)
- UNKNOWN count: 0 (clean classification)

Backup JSON pre-modifica salvato in audit dir.

Decision tree: procede Phase 2 con N field INTERNAL → type:text"
```

---

## 📋 PHASE 2 — Fix JSON: type url→text per field interni (~5 min)

### 2.1 Modifica JSON

Per ogni field INTERNAL identificato in Phase 1.3:

```bash
python3 <<'PYEND'
import json

# Lista field INTERNAL identificati Phase 1.3 (adatta basato su output)
INTERNAL_FIELDS_TO_FIX = [
    'cta_hero_url',
    'cta_default_url',
    # 'footer_credit_url',  # decommentare SE classificato INTERNAL
]

NEW_INSTRUCTION_SUFFIX = " Accetta URL relativi (es. /contatti/) o assoluti (https://...)."

with open('wp-content/themes/saltelli/acf-json/group_theme_options_v1.json', 'r') as f:
    data = json.load(f)

modified_count = 0

def walk_and_fix(fields, prefix=''):
    global modified_count
    for f in fields:
        full_name = prefix + f.get('name', '')
        if f.get('type') == 'url' and full_name in INTERNAL_FIELDS_TO_FIX:
            f['type'] = 'text'

            # Update instructions per chiarire flessibilità
            current_instructions = f.get('instructions', '').rstrip()
            if NEW_INSTRUCTION_SUFFIX.strip() not in current_instructions:
                f['instructions'] = (current_instructions + NEW_INSTRUCTION_SUFFIX).strip()

            # Rimuovi prepend 'http://' se presente (era appendage url type)
            if 'prepend' in f and f['prepend'] in ['http://', 'https://']:
                del f['prepend']

            modified_count += 1
            print(f"FIXED: {full_name} → type:text")

        if f.get('type') in ['repeater', 'group'] and 'sub_fields' in f:
            walk_and_fix(f['sub_fields'], prefix + f.get('name', '') + '.')

walk_and_fix(data.get('fields', []))

# Bump 'modified' timestamp per trigger ACF/SCF re-sync
import time
data['modified'] = int(time.time())

# Write back
with open('wp-content/themes/saltelli/acf-json/group_theme_options_v1.json', 'w') as f:
    json.dump(data, f, indent=4, ensure_ascii=False)

print(f"\n✅ Modified {modified_count} field(s). JSON saved.")
print(f"Timestamp 'modified' bumped to {data['modified']}.")
PYEND
```

### 2.2 Verifica diff JSON

```bash
echo "=== Phase 2 — JSON diff ===" > .claude/knowledge/audits/wave4-7-fix-1/phase2-fix.txt
git diff wp-content/themes/saltelli/acf-json/group_theme_options_v1.json \
  >> .claude/knowledge/audits/wave4-7-fix-1/phase2-fix.txt

# Atteso: modifiche solo su:
# - "type": "url" → "type": "text" per field interni
# - "instructions" updated con suffix
# - "modified" timestamp bumped

cat .claude/knowledge/audits/wave4-7-fix-1/phase2-fix.txt
```

### 2.3 Commit Phase 2

```bash
git add wp-content/themes/saltelli/acf-json/group_theme_options_v1.json
git add .claude/knowledge/audits/wave4-7-fix-1/

git commit -m "wave4-7-fix-1: phase 2 — JSON fix type:url→text per CTA interni

Modifiche surgical su acf-json/group_theme_options_v1.json:
- cta_hero_url: type url → text
- cta_default_url: type url → text
- (eventualmente footer_credit_url: type url → text)

Instructions aggiornate con: 'Accetta URL relativi (es. /contatti/) o assoluti (https://...).'

Timestamp 'modified' bumped per trigger ACF/SCF re-sync.

Field URL esterni invariati (type:url):
- social_facebook, social_instagram, social_linkedin, social_twitter
- press_outlets.url (sub-field repeater)

Razionale: URL CTA interni del proprio sito DEVONO essere relativi
per portabilità staging↔produzione. URL esterni (social, partner)
sono assoluti per natura → type:url correttamente strict."
```

---

## 📋 PHASE 3 — SCF re-sync su staging (~5 min)

### 3.1 Trigger sync JSON → DB

```bash
echo "=== Phase 3 — SCF sync ===" > .claude/knowledge/audits/wave4-7-fix-1/phase3-sync.txt

# Step 1: scp JSON modificato
scp wp-content/themes/saltelli/acf-json/group_theme_options_v1.json \
    deploy@178.62.207.50:/tmp/group_theme_options_v1.json

ssh deploy@178.62.207.50 "
  sudo cp /tmp/group_theme_options_v1.json /var/www/saltelli/wp-content/themes/saltelli/acf-json/group_theme_options_v1.json &&
  sudo chown www-data:www-data /var/www/saltelli/wp-content/themes/saltelli/acf-json/group_theme_options_v1.json
" >> .claude/knowledge/audits/wave4-7-fix-1/phase3-sync.txt 2>&1

# Step 2: SCF sync command
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp acf sync --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix-1/phase3-sync.txt 2>&1

# Step 3: Cache flush
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli && \
  sudo -u www-data wp transient delete --all --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix-1/phase3-sync.txt 2>&1

# Cleanup tmp
ssh deploy@178.62.207.50 "rm /tmp/group_theme_options_v1.json"

cat .claude/knowledge/audits/wave4-7-fix-1/phase3-sync.txt
```

### 3.2 Verifica field type post-sync

```bash
echo "" >> .claude/knowledge/audits/wave4-7-fix-1/phase3-sync.txt
echo "=== Verifica field type post-sync ===" >> .claude/knowledge/audits/wave4-7-fix-1/phase3-sync.txt

ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
  // Ricarica field group e verifica field types
  if (function_exists(\"acf_get_fields\")) {
    \$group = acf_get_field_groups([\"key\" => \"group_theme_options_v1\"]);
    if (!empty(\$group)) {
      \$fields = acf_get_fields(\$group[0]);
      foreach (\$fields as \$f) {
        if (in_array(\$f[\"name\"], [\"cta_hero_url\", \"cta_default_url\", \"footer_credit_url\"])) {
          echo \"  - {\$f[\"name\"]}: type={\$f[\"type\"]}\\n\";
        }
      }
    }
  }
' --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix-1/phase3-sync.txt

# Atteso:
#   - cta_hero_url: type=text
#   - cta_default_url: type=text
#   (eventualmente footer_credit_url: type=text)
```

### 3.3 Commit Phase 3

```bash
git add .claude/knowledge/audits/wave4-7-fix-1/
git commit -m "wave4-7-fix-1: phase 3 — SCF sync staging post JSON fix

Steps eseguiti:
- scp JSON modificato → /tmp/ staging
- sudo cp → wp-content/themes/saltelli/acf-json/ con ownership www-data
- wp acf sync --path=/var/www/saltelli
- wp cache flush + transient delete

Verifica post-sync (acf_get_fields):
- cta_hero_url: type=text ✓
- cta_default_url: type=text ✓
(- footer_credit_url: type=text ✓ se incluso)

ACF/SCF JSON auto-load propagato correttamente al DB."
```

---

## 📋 PHASE 4 — Smoke test save E2E (~10 min)

### 4.1 Replica esatta del bug Duccio

Test E2E che replica il flow esatto che ha fallito:

```bash
echo "=== Phase 4 — Smoke test save E2E ===" > .claude/knowledge/audits/wave4-7-fix-1/phase4-smoke.txt

# Test 1: prove update_field con valore relativo NON fallisce
echo "" >> .claude/knowledge/audits/wave4-7-fix-1/phase4-smoke.txt
echo "--- Test 1: update_field cta_hero_url con /contatti/ ---" \
  >> .claude/knowledge/audits/wave4-7-fix-1/phase4-smoke.txt

ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
  // Backup current value
  \$current = get_field(\"cta_hero_url\", \"option\");
  echo \"Current value: \" . strval(\$current) . \"\\n\";

  // Try update with relative URL (questo FALLIVA pre-fix)
  \$result = update_field(\"cta_hero_url\", \"/contatti/\", \"option\");
  echo \"update_field result: \" . (\$result ? \"TRUE (success)\" : \"FALSE (failed)\") . \"\\n\";

  // Verify written value
  \$new_val = get_field(\"cta_hero_url\", \"option\");
  echo \"New value (post-update): \" . strval(\$new_val) . \"\\n\";

  // Same for cta_default_url
  \$current2 = get_field(\"cta_default_url\", \"option\");
  \$result2 = update_field(\"cta_default_url\", \"/contatti/\", \"option\");
  echo \"\\ncta_default_url update result: \" . (\$result2 ? \"TRUE\" : \"FALSE\") . \"\\n\";
' --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix-1/phase4-smoke.txt 2>&1
```

### 4.2 Smoke E2E HTTP — modifica via admin form simulation

```bash
echo "" >> .claude/knowledge/audits/wave4-7-fix-1/phase4-smoke.txt
echo "--- Test 2: cache flush + frontend reflect ---" \
  >> .claude/knowledge/audits/wave4-7-fix-1/phase4-smoke.txt

# Cache flush
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"

sleep 2

# Verifica frontend ha link giusto
HOMEPAGE_HTML=$(curl -s "https://staging.studiolegalesaltelli.it/" --max-time 10)

# Cerca href="/contatti/" nel CTA hero (dovrebbe esserci)
HREF_RELATIVE_COUNT=$(echo "$HOMEPAGE_HTML" | grep -oE 'href="/contatti/"' | wc -l)
HREF_ABSOLUTE_COUNT=$(echo "$HOMEPAGE_HTML" | grep -oE 'href="https?://[^/]+/contatti/"' | wc -l)

echo "  href='/contatti/' relative count: $HREF_RELATIVE_COUNT (atteso: ≥1)" \
  >> .claude/knowledge/audits/wave4-7-fix-1/phase4-smoke.txt
echo "  href absolute /contatti/ count: $HREF_ABSOLUTE_COUNT" \
  >> .claude/knowledge/audits/wave4-7-fix-1/phase4-smoke.txt

if [ "$HREF_RELATIVE_COUNT" -ge 1 ]; then
  echo "  ✅ Frontend renders relative URL correctly" \
    >> .claude/knowledge/audits/wave4-7-fix-1/phase4-smoke.txt
else
  echo "  ❌ Frontend does NOT render relative URL — verifica" \
    >> .claude/knowledge/audits/wave4-7-fix-1/phase4-smoke.txt
fi
```

### 4.3 Smoke regression Wave 5+6+4+4.5+4.6+4.7+4.7.1+4.8+4.7.fix

```bash
mkdir -p .claude/knowledge/audits/wave4-7-fix-1/regression/

URLS=(
  "/" "/chi-siamo/" "/chi-siamo/lo-studio/" "/chi-siamo/team/"
  "/aree-di-pratica/"
  "/aree-di-pratica/privati/diritto-tributario/"
  "/aree-di-pratica/privati/cartelle-esattoriali-e-multe/"
  "/aree-di-pratica/privati/diritto-del-lavoro/"
  "/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/"
  "/aree-di-pratica/privati/infortunistica-stradale/"
  "/aree-di-pratica/privati/aste-immobiliari/"
  "/aree-di-pratica/imprese/recupero-crediti/"
  "/aree-di-pratica/imprese/diritto-dellimmigrazione/"
  "/aree-di-pratica/contenzioso-amministrativo/"
  "/aree-di-pratica/contenzioso-amministrativo/diritto-amministrativo/"
  "/risorse/" "/risorse/blog/" "/risorse/domande-frequenti/"
  "/risorse/glossario-legale/"
  "/costi-e-consulenze/" "/contatti/"
)

PASS=0; FAIL=0
echo "=== Smoke regression Wave 4.7.fix.1 ===" > .claude/knowledge/audits/wave4-7-fix-1/regression/smoke.txt
echo "Date: $(date -Iseconds)" >> .claude/knowledge/audits/wave4-7-fix-1/regression/smoke.txt
echo "URL count: ${#URLS[@]}" >> .claude/knowledge/audits/wave4-7-fix-1/regression/smoke.txt
echo "" >> .claude/knowledge/audits/wave4-7-fix-1/regression/smoke.txt

for url in "${URLS[@]}"; do
  code=$(curl -s -o /dev/null -w "%{http_code}" -L "https://staging.studiolegalesaltelli.it$url" --max-time 10)
  if [ "$code" = "200" ]; then
    echo "  PASS [$code] $url" >> .claude/knowledge/audits/wave4-7-fix-1/regression/smoke.txt
    PASS=$((PASS+1))
  else
    echo "  FAIL [$code] $url" >> .claude/knowledge/audits/wave4-7-fix-1/regression/smoke.txt
    FAIL=$((FAIL+1))
  fi
done

echo "" >> .claude/knowledge/audits/wave4-7-fix-1/regression/smoke.txt
echo "TOTAL: $PASS/${#URLS[@]} PASS · $FAIL FAIL" >> .claude/knowledge/audits/wave4-7-fix-1/regression/smoke.txt

cat .claude/knowledge/audits/wave4-7-fix-1/regression/smoke.txt | tail -5

# Atteso: 21/21 PASS
```

### 4.4 Acceptance criteria Phase 4

- ✅ `update_field('cta_hero_url', '/contatti/', 'option')` ritorna **TRUE** (era false pre-fix)
- ✅ `get_field('cta_hero_url', 'option')` ritorna `/contatti/` post-update
- ✅ Frontend `<a href="/contatti/">` rendering correttamente (relative URL)
- ✅ 21/21 smoke regression PASS
- ✅ NO regression Wave 5+6+4+4.5+4.6+4.7+4.7.1+4.8+4.7.fix

**Se uno qualunque FAIL → STOP, riporta orchestratore**.

### 4.5 Commit Phase 4

```bash
git add .claude/knowledge/audits/wave4-7-fix-1/
git commit -m "wave4-7-fix-1: phase 4 — smoke test save E2E + regression

Test 1 update_field con URL relativo:
- cta_hero_url '/contatti/': TRUE (era FALSE pre-fix)
- cta_default_url '/contatti/': TRUE

Test 2 frontend reflect:
- href='/contatti/' relative count: ≥1 ✓
- Rendering corretto

Smoke regression 21 URL: 21/21 PASS · 0 FAIL
NO regression Wave 5+6+4+4.5+4.6+4.7+4.7.1+4.8+4.7.fix"
```

---

## 📋 PHASE 5 — Bump 1.3.7 + report + push (~5 min)

### 5.1 Bump theme version

```bash
sed -i.bak 's/^Version: 1.3.6-wave4-7-fix-scf-migration/Version: 1.3.7-wave4-7-fix-1-scf-url-validation/' \
  wp-content/themes/saltelli/style.css

sed -i.bak "s/define('SALTELLI_VERSION', '1.3.6-wave4-7-fix-scf-migration')/define('SALTELLI_VERSION', '1.3.7-wave4-7-fix-1-scf-url-validation')/" \
  wp-content/themes/saltelli/functions.php

find wp-content/themes/saltelli/ -name "*.bak" -delete

grep "^Version:" wp-content/themes/saltelli/style.css
grep "SALTELLI_VERSION" wp-content/themes/saltelli/functions.php
```

### 5.2 Report finale

Crea `.claude/knowledge/recovery/WAVE4-7-FIX-1-SCF-URL-REPORT.md`:

```markdown
# Wave 4.7.fix.1 — SCF URL Validation Hotfix · Report

**Branch**: fix/wave4-7-fix-1-scf-url-validation
**Theme version**: 1.3.7-wave4-7-fix-1-scf-url-validation
**Generated**: <ISO timestamp>
**Phases**: 5/5

## Executive summary

Wave 4.7.fix.1 risolve bug SCF URL validation strict che bloccava il save dei
campi CTA con URL relativi (es. `/contatti/`).

**Causa root**: SCF è più strict di ACF Free su `type: url` — richiede URL
assoluti `https://...`. Side effect del switch ACF→SCF di Wave 4.7.fix.

**Fix**: Strategy B — type `url` → `text` per field URL **interni** (CTA),
mantenuto `type: url` per field URL **esterni** (social, press).

## Phase summary

| Phase | Outcome |
|---|---|
| 1 Investigation | <N> field type=url identificati, <M> classificati INTERNAL |
| 2 JSON fix | <M> field cambiati type→text, instructions aggiornate, timestamp bumped |
| 3 SCF sync staging | wp acf sync OK, field type=text confermato post-sync |
| 4 Smoke save E2E | update_field('/contatti/'): TRUE (era FALSE), 21/21 regression PASS |
| 5 Bump 1.3.7 + report + push | theme version bumped, branch pushed |

## Lesson learned cristallizzata

**SCF validation strict vs ACF Free permissive**: il field `type: url` di SCF
rifiuta URL relativi (`/path/`). Pattern futuro per tutti i siti WP con SCF:
- URL **interni** del proprio sito → `type: text`
- URL **esterni** (social, partner, CDN) → `type: url`

Cross-environment portability (staging ↔ produzione) richiede URL relativi
nel DB. URL assoluti hardcoded a un dominio sono anti-pattern.

## Field modificati

INTERNAL → text:
- cta_hero_url
- cta_default_url
- (footer_credit_url se applicabile)

EXTERNAL invariati (type:url):
- social_facebook, social_instagram, social_linkedin, social_twitter
- press_outlets.url
```

### 5.3 Commit Phase 5 + push

```bash
git add -A
git commit -m "wave4-7-fix-1: phase 5 — bump 1.3.7 + report + push

Theme version: 1.3.6-wave4-7-fix-scf-migration → 1.3.7-wave4-7-fix-1-scf-url-validation

Closes Wave 4.7.fix.1 — bug validazione SCF URL strict risolto.

Lesson learned cristallizzata:
- URL interni → type:text (cross-env portability)
- URL esterni → type:url (correttamente strict)

Report: .claude/knowledge/recovery/WAVE4-7-FIX-1-SCF-URL-REPORT.md"

git push origin fix/wave4-7-fix-1-scf-url-validation
```

NO merge automatico. Orchestratore audisce + procede con merge + tag + cleanup.

---

## ✅ Acceptance criteria Wave 4.7.fix.1

- [ ] Branch `fix/wave4-7-fix-1-scf-url-validation` da main post-Wave 4.7.fix
- [ ] 5 phases eseguite, 5 commit phase-by-phase
- [ ] Field type=url **INTERNAL** identificati e cambiati a `type: text`
- [ ] Field type=url **EXTERNAL** invariati (verifica esplicita)
- [ ] `update_field('cta_hero_url', '/contatti/', 'option')`: TRUE (era FALSE)
- [ ] Frontend rendering relative URL: PASS
- [ ] 21/21 smoke regression PASS
- [ ] NO regression Wave 5+6+4+4.5+4.6+4.7+4.7.1+4.8+4.7.fix
- [ ] Theme version `1.3.7-wave4-7-fix-1-scf-url-validation`
- [ ] Branch pushed (NO merge automatico)
- [ ] Report `.claude/knowledge/recovery/WAVE4-7-FIX-1-SCF-URL-REPORT.md`

---

## 🚨 Cosa fare in caso di problemi

| Situazione | Action |
|---|---|
| Phase 1.3 trova field UNKNOWN | STOP, riporta orchestratore per classificazione manuale |
| Phase 2 JSON modifica fallisce | STOP, ripristina backup `.claude/knowledge/audits/wave4-7-fix-1/group_theme_options_v1.BACKUP.json` |
| Phase 3 wp acf sync errore | STOP, verifica permessi file su staging + riporta |
| Phase 4 update_field ancora ritorna FALSE | STOP, possibile sync non propagato. Verifica wp acf list-fields |
| Phase 4 smoke regression < 21/21 PASS | Investigate URL specifici, NO completion forzato |

**Rollback emergency** (qualsiasi step):
```bash
# Ripristina JSON original
cp .claude/knowledge/audits/wave4-7-fix-1/group_theme_options_v1.BACKUP.json \
   wp-content/themes/saltelli/acf-json/group_theme_options_v1.json

# Re-sync staging
scp wp-content/themes/saltelli/acf-json/group_theme_options_v1.json \
    deploy@178.62.207.50:/tmp/ && \
ssh deploy@178.62.207.50 "
  sudo cp /tmp/group_theme_options_v1.json /var/www/saltelli/wp-content/themes/saltelli/acf-json/ &&
  sudo chown www-data:www-data /var/www/saltelli/wp-content/themes/saltelli/acf-json/group_theme_options_v1.json &&
  cd /var/www/saltelli &&
  sudo -u www-data wp acf sync --path=/var/www/saltelli &&
  sudo -u www-data wp cache flush --path=/var/www/saltelli
"
```

---

## 🎯 Output expected

1. Branch `fix/wave4-7-fix-1-scf-url-validation` con 5 commit
2. File modificati:
   - **MOD**: `wp-content/themes/saltelli/acf-json/group_theme_options_v1.json` (2-3 field type:url→text)
   - **MOD**: `wp-content/themes/saltelli/style.css` + `functions.php` (version bump)
3. Audit trail in `.claude/knowledge/audits/wave4-7-fix-1/`:
   - `group_theme_options_v1.BACKUP.json`
   - `phase1-investigation.txt`, `phase2-fix.txt`, `phase3-sync.txt`, `phase4-smoke.txt`
   - `regression/smoke.txt`
4. Report `.claude/knowledge/recovery/WAVE4-7-FIX-1-SCF-URL-REPORT.md`
5. Theme version `1.3.7-wave4-7-fix-1-scf-url-validation`

L'orchestratore audisce + procede con merge `fix/wave4-7-fix-1 → main` (no-ff) + tag `v1.3.7-wave4-7-fix-1-scf-url-validation` + deploy delta `--checksum`.

**Critical**: post-merge solo 3 file richiedono rsync delta (JSON + style.css + functions.php). Le modifiche DB (re-sync field type) erano già su staging via SSH durante Phase 3.

---

## 🔗 Riferimenti

- DEC-039-COMPLETED (Wave 4.7.fix SCF Migration) — base wave
- CMS Diagnosis Round 2 REPORT.md (2026-05-08) — origine fix architetturale
- Bug Duccio (2026-05-08 mattina): screenshot WP Admin Saltelli Settings "Validazione fallita 2 campi"
- DEC-036 (lesson rsync --checksum mandatory)
- SCF docs URL field validation: https://wordpress.org/plugins/secure-custom-fields/
- `EDITOR-HANDOFF.md` v2.0 — riferimento Elena (probabilmente nessun update necessario)
- `CLAUDE.md` — single source of truth
