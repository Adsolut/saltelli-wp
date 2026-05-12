# 🚑 Claude Code Agent — Wave 4.7 CMS Hotfix + Critical Copy Fix (v1.0)

> **Audience**: Claude Code agent in nuova sessione terminale dedicata.
> **Branch parent**: `main` (post-Wave 4.6 mergeata, tag `v1.3.2-wave4-6-cms-editability`)
> **Branch nuovo**: `feat/wave4-7-cms-hotfix-critical-fixes`
> **Theme version target**: `1.3.3-wave4-7-cms-hotfix`
> **Scope**: cleanup CMS (page draft confliggenti, duplicate, surplus competenze) + 2 critical visual fix (numeri aree, date archive casi) + sticky widget identification
> **Tempo stimato**: ~2-3h (5 phases)
> **Riferimento**: DEC-033 (audit cumulativo), DEC-034 (lancio Wave 4.7), CMS Diagnosis report 2026-05-07, Visual Audit report 2026-05-07

---

## 🎯 Tu sei

Claude Code agent dedicato a sbloccare l'editorialità di Elena chiudendo il gap CMS (11 page draft confliggenti, duplicate, clutter) + i 2 issue critical visual (numeri aree, date archive casi) + identificare lo sticky widget verde mobile.

**NON è una wave di refactor o feature**. È una **hotfix surgical** che risolve issue noti e documentati. Scope chiuso, nessuno scope creep.

**Critical context** (da CMS Diagnosis 2026-05-07):
- 11 page WP DRAFT con titolo identico ai CPT competenza (Diritto Tributario, Cartelle..., ecc.) — Elena le clicca per errore in Pages list
- Duplicate "lo-studio" (ID 19 + 2811) — confusione UI
- 23 page DRAFT totali = clutter editoriale massivo
- 22 CPT competenza vs 17 attesi — surplus +5 da indagare e cleanup

**Critical context** (da Visual Audit 2026-05-07):
- Numeri aree inconsistenti: homepage "Diciannove aree" / aree-hub "Diciotto aree" / trust bar "17 AREE" — DEC-021 firma 17
- archive-saltelli_caso mostra "5 Maggio 2026" (`get_the_date()`) per tutti i 9 casi invece di `data_caso` ACF field
- Sticky widget verde top-left mobile su ~15 pagine — origin sconosciuta

---

## 🔒 Hard rules (non negotiabili)

1. **NO commit su main**. Sempre su `feat/wave4-7-cms-hotfix-critical-fixes`.
2. **Phase 1 è READ-ONLY**. NO modifiche DB/codice in Phase 1. Investigation prima di fix.
3. **Cleanup pages = trash, NON force delete**. Le draft vanno in trash (recoverable 30gg) per safety. Force delete solo dopo conferma orchestratore.
4. **NO regression smoke** Wave 5 (33+18+33) + Wave 6 (21+render) + Wave 4 (5 headers) + Wave 4.5 (critical CSS + WebP) + Wave 4.6 (CMS gap audit 0+0).
5. **NO modifica `wave5-blog-rewrites.php`**, `inc/perf.php`, `inc/security.php`, `inc/critical-css.php`, `inc/wave4-6-migration.php`. Sono wave precedenti stable.
6. **NO modifica CPT registration o tassonomie** — Wave 4.7 è solo cleanup DB + copy fix + identificazione widget.
7. **DRY**: prima di trash una page, verifica che il suo contenuto NON sia stato migrato altrove (es. content storico Lo Studio originale).
8. **Idempotenza**: tutti gli script DB devono essere safely re-eseguibili.

---

## 📚 Letture obbligatorie (nell'ordine)

1. **`CLAUDE.md`** — single source of truth
2. **`prompts/PROMPT_AGENT_WAVE4_7_CMS_HOTFIX.md`** (questo file) end-to-end
3. **CMS Diagnosis report** in `~/saltelli-cms-diagnosis/20260507/REPORT.md` (sul Mac di Duccio, già letto da orchestratore — referenze ai findings nel prompt)
4. **Visual Audit report** in `~/saltelli-visual-audit/20260507/REPORT.md` (idem)
5. **File da modificare** (in lettura preliminare):
   - `wp-content/themes/saltelli/front-page.php` (numero aree hardcoded)
   - `wp-content/themes/saltelli/archive-competenza.php` o `taxonomy-tipo-area.php` (numero aree hub)
   - `wp-content/themes/saltelli/archive-saltelli_caso.php` o `template-parts/archive-caso.php` (date archive)
   - `wp-content/themes/saltelli/inc/cpt-competenza.php` (registrazione CPT, NON modificare)

---

## 📋 PHASE 1 — Investigation finale READ-ONLY (~30 min)

### 1.1 Backup pre-Wave 4.7

```bash
mkdir -p ~/backups
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp db export ~/backups/saltelli-staging-pre-wave47-$(date +%Y%m%d-%H%M).sql --add-drop-table --path=/var/www/saltelli"

# Locale (per safety, anche se modifiche staging-only)
mkdir -p ~/backups
docker-compose exec -T db mysqldump -u root -proot saltelli > ~/backups/saltelli-local-pre-wave47-$(date +%Y%m%d-%H%M).sql 2>/dev/null || echo "Skip: docker locale non in uso per Wave 4.7"
```

### 1.2 Branch dedicato

```bash
cd ~/Desktop/DEV/saltelli-wp/
git fetch origin
git checkout main
git pull --ff-only origin main   # → tag v1.3.2-wave4-6-cms-editability
git checkout -b feat/wave4-7-cms-hotfix-critical-fixes
```

### 1.3 Investigation: 5 query critiche

Esegui queste **5 query read-only** su staging e salva output in `.claude/knowledge/audits/wave4-7/investigation-pre.txt`:

```bash
mkdir -p .claude/knowledge/audits/wave4-7/

# QUERY 1 — UID + role Elena + altri editor
echo "=== Q1: Users editor + admin ===" >> .claude/knowledge/audits/wave4-7/investigation-pre.txt
ssh deploy@178.62.207.50 "sudo -u www-data wp user list \
  --fields=ID,user_login,user_email,roles,user_registered \
  --orderby=ID --format=table --path=/var/www/saltelli" \
  | tee -a .claude/knowledge/audits/wave4-7/investigation-pre.txt

# QUERY 2 — Lista 22 competenze per identificare i 5 surplus
echo "=== Q2: Competenze (22 vs 17 atteso) ===" >> .claude/knowledge/audits/wave4-7/investigation-pre.txt
ssh deploy@178.62.207.50 "sudo -u www-data wp post list \
  --post_type=competenza --post_status=any \
  --fields=ID,post_status,post_name,post_title,post_modified \
  --orderby=post_status --order=DESC --format=table --path=/var/www/saltelli" \
  | tee -a .claude/knowledge/audits/wave4-7/investigation-pre.txt

# QUERY 3 — Contenuto delle 11 page draft confliggenti (sono empty o hanno content?)
echo "=== Q3: Page draft confliggenti — content size ===" >> .claude/knowledge/audits/wave4-7/investigation-pre.txt
for ID in 202 208 170 223 232 260 279 288 297 2246 2251; do
  ssh deploy@178.62.207.50 "sudo -u www-data wp eval '
    \$p = get_post($ID);
    if (\$p) {
      \$content_len = strlen(\$p->post_content);
      \$excerpt_len = strlen(\$p->post_excerpt);
      \$meta_count = count(get_post_meta($ID));
      echo \"ID $ID: content_len={\$content_len} excerpt_len={\$excerpt_len} meta_count={\$meta_count} title=\\\"{\$p->post_title}\\\"\\n\";
    }
  ' --path=/var/www/saltelli" \
    | tee -a .claude/knowledge/audits/wave4-7/investigation-pre.txt
done

# QUERY 4 — Page hub "Competenze" (ID 321) e "Aree di Pratica" (ID 2812) — content
echo "=== Q4: Page hub Competenze + Aree di Pratica content ===" >> .claude/knowledge/audits/wave4-7/investigation-pre.txt
for ID in 321 2812 19; do
  ssh deploy@178.62.207.50 "sudo -u www-data wp eval '
    \$p = get_post($ID);
    if (\$p) {
      \$content_len = strlen(\$p->post_content);
      echo \"ID $ID: status={\$p->post_status} parent={\$p->post_parent} slug=\\\"{\$p->post_name}\\\" title=\\\"{\$p->post_title}\\\" content_len={\$content_len}\\n\";
      if (\$content_len > 0 && \$content_len < 500) {
        echo \"--- content preview ---\\n\";
        echo wp_trim_words(\$p->post_content, 30);
        echo \"\\n--- ---\\n\";
      }
    }
  ' --path=/var/www/saltelli" \
    | tee -a .claude/knowledge/audits/wave4-7/investigation-pre.txt
done

# QUERY 5 — Object cache attivo + custom admin_bar hooks tema
echo "=== Q5: Cache + admin bar hooks ===" >> .claude/knowledge/audits/wave4-7/investigation-pre.txt
ssh deploy@178.62.207.50 "sudo -u www-data wp option get advanced_cache --path=/var/www/saltelli 2>&1 || echo 'no advanced_cache option'" \
  | tee -a .claude/knowledge/audits/wave4-7/investigation-pre.txt
ssh deploy@178.62.207.50 "ls /var/www/saltelli/wp-content/object-cache.php 2>&1 || echo 'no object-cache.php'" \
  | tee -a .claude/knowledge/audits/wave4-7/investigation-pre.txt
grep -rn "admin_bar_menu\|wp_before_admin_bar_render\|edit_post_link" wp-content/themes/saltelli/ \
  | tee -a .claude/knowledge/audits/wave4-7/investigation-pre.txt || echo "NO custom admin bar hooks (atteso)"
```

### 1.4 Decision tree post-investigation

Sulla base di Q2 (22 competenze):
- Se i 5 surplus sono **draft di test** (post_modified recente, content corto): trash sicuro
- Se sono **publish con content reale**: NO trash, segnala orchestratore (potrebbero essere competenze nuove cliente)

Sulla base di Q3 (page draft content):
- Se tutte 11 sono `content_len=0` (empty): trash sicuro
- Se alcune hanno content significativo: investigate prima del trash, valuta migration content a CPT

Sulla base di Q4 (page hub):
- Se page 321 "Competenze" è empty o duplicato: candidate per trash o rename
- Se page 19 "lo-studio" è empty: trash (è duplicato di 2811)

Sulla base di Q5 (cache):
- Se object-cache.php presente: ricorda di flush al fine di Phase 2

### 1.5 Commit Phase 1

```bash
git add .claude/knowledge/audits/wave4-7/investigation-pre.txt
git commit -m "wave4-7: phase 1 — investigation read-only pre-cleanup

Findings query 1-5:
- Users + roles (UID Elena identificato)
- 22 competenze breakdown (5 surplus identificati)
- 11 page draft confliggenti — content size analysis
- Page hub Competenze/Aree-di-Pratica content
- Object cache + custom admin bar hooks status

Decision tree per Phase 2 documentato in investigation-pre.txt."
```

---

## 📋 PHASE 2 — CMS cleanup (~60 min)

### 2.1 Trash 11 page draft confliggenti CPT competenza

Solo quelle con `content_len=0` da Q3. Per quelle con content non-zero: STOP, riporta orchestratore prima di proseguire.

```bash
# Lista IDs da Phase 1 query 3 (filtrate per content_len=0)
DRAFT_IDS_TO_TRASH="202 208 170 223 232 260 279 288 297 2246 2251"

for ID in $DRAFT_IDS_TO_TRASH; do
  ssh deploy@178.62.207.50 "sudo -u www-data wp post delete $ID --path=/var/www/saltelli"
  # NOTA: wp post delete senza --force = trash (recoverable 30gg)
done

# Verifica trash
ssh deploy@178.62.207.50 "sudo -u www-data wp post list \
  --post_type=page --post_status=trash --format=count --path=/var/www/saltelli"
# Atteso: 11+ (le 11 + altre eventuali già in trash)
```

### 2.2 Cleanup duplicate "lo-studio" (ID 19) se vuota

Da Q4 Phase 1, se page 19 ha `content_len=0` o content placeholder:

```bash
ssh deploy@178.62.207.50 "sudo -u www-data wp post delete 19 --path=/var/www/saltelli"
```

Se Q4 mostra che 19 ha content reale: STOP, segnala orchestratore.

### 2.3 Rename page hub "Competenze" (ID 321) per disambiguare

La page WP "Competenze" (ID 321, `slug=competenze`) confonde Elena perché ha stesso semantico del CPT competenza menu. Rinominare per chiarezza:

```bash
ssh deploy@178.62.207.50 "sudo -u www-data wp post update 321 \
  --post_title='[Hub legacy] Competenze' \
  --post_status=draft \
  --path=/var/www/saltelli"
```

Strategia: status=draft + title `[Hub legacy]` la rende invisibile a frontend e visibilmente "non da modificare" a Elena. Se lo step rompe qualcosa frontend (verifica smoke), revert.

### 2.4 Cleanup 5 competenze surplus (basato su Q2 Phase 1)

Solo se i 5 surplus sono draft test (NO content, post_modified recente). Per ognuno:

```bash
ssh deploy@178.62.207.50 "sudo -u www-data wp post delete <ID> --path=/var/www/saltelli"
```

Per i publish surplus (se ne esistono): NO trash automatico. Riporta orchestratore lista IDs+titles per decisione caso-per-caso.

### 2.5 Verifica capability Elena (UID da Q1 Phase 1)

```bash
# Sostituisci <ELENA_ID> con UID identificato in Q1
ELENA_ID=<ID>

ssh deploy@178.62.207.50 "sudo -u www-data wp user get $ELENA_ID \
  --field=allcaps --path=/var/www/saltelli" \
  | grep -E "edit_posts|edit_competenza|edit_avvocato|edit_pages|publish"

# Atteso (per role Editor):
# edit_posts: 1
# edit_published_posts: 1
# publish_posts: 1
# (no edit_competenza/edit_avvocato perché CPT ha capability_type=post quindi usa edit_posts)
```

Se Elena NON ha `edit_posts=1`: bug capability, segnala orchestratore.

### 2.6 Commit Phase 2

```bash
git add .claude/knowledge/audits/wave4-7/
git commit -m "wave4-7: phase 2 — CMS cleanup (page draft confliggenti + duplicate + page hub disambiguazione)

CMS DB cleanup (idempotente, recoverable via trash):
- 11 page draft confliggenti CPT competenza → trash
- Duplicate 'lo-studio' (ID 19) → trash (era empty, duplicato di 2811)
- Page hub 'Competenze' (ID 321) → renamed '[Hub legacy] Competenze' + status=draft
- 5 competenze surplus identificati e gestiti (vedi audit log)

Capability Elena verificata: edit_posts ✓ (role Editor sufficiente per CPT competenza/avvocato/caso/saltelli_trust/saltelli_faq).

Closes CMS frustration Elena: Pages list non mostra più 11 record duplicati con titolo competenze."
```

---

## 📋 PHASE 3 — Critical copy fix (~30 min)

### 3.1 Fix numeri aree (front-page.php + aree-hub)

#### Trova occorrenze hardcoded "Diciannove" / "Diciotto" / "diciannove" / "diciotto"

```bash
grep -rn -E "[Dd]iciannove|[Dd]iciotto|19 aree|18 aree" wp-content/themes/saltelli/ --include="*.php"
```

Atteso match in:
- `front-page.php` (hero "Diciannove aree.")
- `archive-competenza.php` o `taxonomy-tipo-area.php` o template-part hub aree (H1 "Diciotto aree")

#### Replace surgical (NO regex globale unsafe)

Per ogni occorrenza, edita manually:
- "Diciannove aree" → "Diciassette aree"
- "Diciotto aree" → "Diciassette aree"
- "diciannove aree" → "diciassette aree" (mantieni capitalization)

(NOTA: 17 in italiano si scrive "diciassette", non "diciassette" — verifica con orchestratore se preferisce numero "17" o parola "diciassette". Default: parola "Diciassette" per coerenza editoriale boutique italiano.)

### 3.2 Fix date archive-saltelli_caso

Trova template archive casi:

```bash
ls wp-content/themes/saltelli/archive-saltelli_caso.php 2>&1
ls wp-content/themes/saltelli/template-parts/archive-caso.php 2>&1
```

Identifica la riga che usa `get_the_date()` per l'eyebrow:

```bash
grep -n "get_the_date\|the_time" wp-content/themes/saltelli/archive-saltelli_caso.php
grep -n "get_the_date\|the_time" wp-content/themes/saltelli/template-parts/archive-caso.php
```

Replace surgical:

```php
// PRIMA:
<span class="sl-caso__date"><?php echo strtoupper(get_the_date('F Y')); ?></span>

// DOPO:
<?php
$data_caso = get_field('data_caso');
$display_date = $data_caso 
    ? wp_date('F Y', strtotime($data_caso))
    : strtoupper(get_the_date('F Y'));
?>
<span class="sl-caso__date"><?php echo strtoupper($display_date); ?></span>
```

Italian formatting: usa `wp_date()` invece di `date()` per i18n (locale Italian deve già essere settato sul sito).

### 3.3 Smoke test fix copy

```bash
# Numeri aree
curl -s https://staging.studiolegalesaltelli.it/ | grep -oE "(D|d)ici(otto|annove)" || echo "✓ NO diciotto/diciannove"
curl -s https://staging.studiolegalesaltelli.it/aree-di-pratica/ | grep -oE "(D|d)ici(otto|annove)" || echo "✓ NO diciotto/diciannove"

# Trust bar conferma "17"
curl -s https://staging.studiolegalesaltelli.it/ | grep -A 2 "trust-bar" | grep "17"

# Date archive casi (post-deploy del fix)
# NON puoi testare staging finché non deploy. Test locale Docker se disponibile, oppure via Playwright dopo deploy.
```

### 3.4 Commit Phase 3

```bash
git add wp-content/themes/saltelli/
git commit -m "wave4-7: phase 3 — critical copy fix (numeri aree 17 + date archive casi)

Fix:
- front-page.php: hero 'Diciannove aree' → 'Diciassette aree' (DEC-021 17 cliente-firmato)
- <hub aree template>: H1 'Diciotto aree' → 'Diciassette aree'
- archive-saltelli_caso: eyebrow date da get_the_date() → get_field('data_caso') + wp_date('F Y') i18n

Closes Visual Audit:
- CRIT-01 (numeri aree inconsistenti 17/18/19 → 17 ovunque)
- HIGH-02 (date '5 Maggio 2026' identiche → data_caso ACF per ogni caso)"
```

---

## 📋 PHASE 4 — Sticky widget verde identification (~20 min)

### 4.1 Indagine origin

```bash
# Cerca elementi position:fixed nel CSS tema
grep -rn "position:\s*fixed\|position: fixed" wp-content/themes/saltelli/assets/css/ --include="*.css" | head -20

# Cerca elementi top/left fixed nel CSS tema
grep -rn "top:\s*0\|left:\s*0" wp-content/themes/saltelli/assets/css/ --include="*.css" | grep -v "/\*" | head -20

# Cerca widget chat/WhatsApp nel tema
grep -rn -E "whatsapp|chat-bubble|floating-button|sticky-cta" wp-content/themes/saltelli/ --include="*.php" --include="*.css" | head -10

# Plugin chat installati?
ssh deploy@178.62.207.50 "sudo -u www-data wp plugin list --status=active --format=csv --path=/var/www/saltelli" | grep -iE "chat|whatsapp|crisp|tawk|tidio|messenger"
```

### 4.2 Ispeziona DOM live

```bash
# Salva HTML della homepage staging
curl -s https://staging.studiolegalesaltelli.it/ > /tmp/staging-home.html

# Cerca elementi sospetti top-left
grep -E "position:\s*fixed.*top.*left|class=\"[^\"]*sticky[^\"]*top[^\"]*left|whatsapp" /tmp/staging-home.html | head -5

# Body class + admin bar
grep -E "body class|admin-bar|wp-admin-bar" /tmp/staging-home.html | head -5
```

### 4.3 Hypothesis check

Possibili origini del widget verde mobile:

1. **WP Admin bar resto da logged-out test**: se Code in audit visivo era logged-out ma c'era un cookie residuo, l'admin bar avrebbe mostrato il "W" WordPress logo top-left. Verifica: rifare uno screenshot logged-out clean.
2. **Plugin chat WhatsApp** non in active list ma magari custom JS. Verifica `wp-content/themes/saltelli/assets/js/`.
3. **Mobile menu hamburger** disposizionato male (top-left invece dell'angolo standard top-right).
4. **Widget custom Yoast SEO** o ACF.

### 4.4 Decisione fix (depende su 4.1-4.3 findings)

Casi:

- **A) È WP Admin bar**: nessun fix codice, era artefatto audit logged-in. Smoke con incognito mode conferma assenza.
- **B) È plugin chat**: disable se non necessario, oppure config bottom-right.
- **C) È hamburger menu mobile**: CSS fix per riposizionare.
- **D) È widget legacy custom**: rimuovi codice CSS/JS responsabile.

Documenta in audit + applica fix surgical.

### 4.5 Commit Phase 4

```bash
git add .
git commit -m "wave4-7: phase 4 — sticky widget verde mobile identification + fix

Origin identificato: <A/B/C/D>
Fix applicato: <descrizione>

Closes Visual Audit HIGH-03 (sticky widget verde top-left mobile su ~15 pagine)."
```

---

## 📋 PHASE 5 — Smoke regression + cache flush + report (~20 min)

### 5.1 NO regression smoke

```bash
mkdir -p .claude/knowledge/audits/wave4-7/regression/

# Wave 5 smoke (riusa script esistente)
bash .claude/knowledge/audits/wave5-ia-refactor/cli-output/smoke-runner.sh \
  > .claude/knowledge/audits/wave4-7/regression/wave5.txt 2>&1 || true

# Wave 6 smoke
bash .claude/knowledge/audits/wave6/smoke-runner.sh \
  > .claude/knowledge/audits/wave4-7/regression/wave6.txt 2>&1 || true

# Wave 4.6 gap audit (deve essere 0+0)
bash /tmp/audit-gap.sh > .claude/knowledge/audits/wave4-7/regression/wave46-gap.txt 2>&1 || \
  echo "audit-gap.sh non in /tmp, skipping (script Wave 4.6 transient)"
```

### 5.2 Cache flush remoto

```bash
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli && \
  sudo -u www-data wp transient delete --all --path=/var/www/saltelli && \
  sudo -u www-data wp rewrite flush --path=/var/www/saltelli"
```

### 5.3 Bump theme version

In `wp-content/themes/saltelli/style.css`:
```
Version: 1.3.3-wave4-7-cms-hotfix
```

In `wp-content/themes/saltelli/functions.php`:
```php
define('SALTELLI_VERSION', '1.3.3-wave4-7-cms-hotfix');
```

### 5.4 Report finale

Crea `.claude/knowledge/recovery/WAVE4-7-CMS-HOTFIX-REPORT.md` con:
- Phase 1 investigation findings
- Phase 2 CMS cleanup actions taken
- Phase 3 copy fix details
- Phase 4 sticky widget origin + fix
- Smoke regression Wave 5+6+4+4.5+4.6 PASS
- File modificati / creati riepilogo
- Open items (eventuali finding inattesi)

### 5.5 Commit + push

```bash
git add -A
git commit -m "wave4-7: phase 5 — bump 1.3.3 + smoke regression + report

CMS hotfix:
- 11 page draft confliggenti CPT trashed
- Duplicate lo-studio (ID 19) trashed
- Page hub Competenze (ID 321) renamed [Hub legacy]
- N competenze surplus cleanup (vedi report)

Critical copy fix:
- Numeri aree 17 ovunque (front-page + aree-hub allineati a trust bar)
- Date archive casi: data_caso ACF + wp_date i18n

Sticky widget verde mobile: <origin + fix>

NO regression smoke Wave 5+6+4+4.5+4.6.

Closes DEC-034 (Wave 4.7 lancio)."

git push origin feat/wave4-7-cms-hotfix-critical-fixes
```

---

## ✅ Acceptance criteria Wave 4.7

- [ ] Branch `feat/wave4-7-cms-hotfix-critical-fixes` da main post-Wave 4.6
- [ ] 5 phases eseguite, 5+ commit phase-by-phase
- [ ] **CMS cleanup**: 11 page draft trashed + duplicate lo-studio trashed + page hub Competenze renamed
- [ ] **22 competenze → 17** (cleanup surplus, draft surplus → trash)
- [ ] **Numero "17 aree" coerente** cross-page (homepage + aree-hub + trust bar)
- [ ] **Date archive casi**: ogni caso mostra la propria `data_caso` ACF formattata Italian
- [ ] **Sticky widget verde** identificato + risolto/rimosso/documentato
- [ ] **Capability Elena verificata**: edit_posts ✓
- [ ] **Cache + rewrite flush** post-cleanup
- [ ] **NO regression** smoke Wave 5+6+4+4.5+4.6
- [ ] Theme version `1.3.3-wave4-7-cms-hotfix`
- [ ] Branch pushed (NO merge automatico)
- [ ] Report `.claude/knowledge/recovery/WAVE4-7-CMS-HOTFIX-REPORT.md`

---

## 🚨 Cosa fare in caso di problemi

| Situazione | Action |
|---|---|
| Page draft hanno content significativo (Q3) | STOP, segnala orchestratore. Possibile migration content prima di trash. |
| Competenze surplus sono publish con content reale (Q2) | STOP, NON trash. Orchestratore decide caso-per-caso (potrebbero essere competenze cliente nuove non DEC-021). |
| Capability Elena non include edit_posts | STOP, segnala orchestratore. Possibile bug role assignment. |
| Numeri aree hardcoded in più di 2 file | OK, fix tutti — è surgical replace |
| Sticky widget verde è WP Admin bar logged-in artifact | Nessun fix, era artefatto audit. Documenta + closes. |
| Sticky widget è hamburger mobile mal-posizionato | CSS surgical fix in `assets/css/components/header.css` (o equivalente) |
| Cache flush rompe qualcosa | Re-flush + verify rewrite rules + report |
| NO regression smoke fallisce | STOP immediato, debug isolato, ripristina |

---

## 🎯 Output expected

1. Branch `feat/wave4-7-cms-hotfix-critical-fixes` con 5+ commit
2. File modificati / creati:
   - **MOD**: `front-page.php` (numeri aree)
   - **MOD**: hub aree template (numeri aree)
   - **MOD**: `archive-saltelli_caso.php` o `template-parts/archive-caso.php` (date ACF)
   - **MOD**: eventuali CSS/PHP per sticky widget fix
   - **MOD**: `style.css` + `functions.php` (version bump)
   - **NEW**: `inc/wave4-7-migration.php` (idempotente, opzionale se serve)
3. Audit trail in `.claude/knowledge/audits/wave4-7/`:
   - `investigation-pre.txt`
   - `regression/` (Wave 5+6+4+4.5+4.6 smoke)
4. Report `.claude/knowledge/recovery/WAVE4-7-CMS-HOTFIX-REPORT.md`
5. Theme version `1.3.3-wave4-7-cms-hotfix`

L'orchestratore audisce + procede con merge `feat/wave4-7-cms-hotfix-critical-fixes → main` (no-ff) + tag `v1.3.3-wave4-7-cms-hotfix` + rsync delta staging.

**Critical**: post-merge serve **rsync delta + applicazione modifiche DB su staging** (le modifiche DB di Phase 2 erano già su staging via SSH, OK; ma il codice tema serve rsync).

---

## 🔗 Riferimenti

- DEC-021 (URL audit-aligned 17 competenze cliente-firmato)
- DEC-027 (EDITOR-HANDOFF Elena)
- DEC-029-COMPLETED (Wave 4.6 CMS Editability)
- DEC-030 (Deploy staging)
- DEC-031 (Hand-off Elena)
- DEC-033 (Audit cumulativo Visual + CMS)
- DEC-034 (Wave 4.7 lancio — questo prompt)
- `~/saltelli-cms-diagnosis/20260507/REPORT.md`
- `~/saltelli-visual-audit/20260507/REPORT.md`
- `EDITOR-HANDOFF.md` (riferimento user expectation Elena)
- `CLAUDE.md` — single source of truth
