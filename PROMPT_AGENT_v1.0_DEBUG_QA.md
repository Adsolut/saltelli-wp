# PROMPT v1.0.0 DEBUG & QA — Stress test pre-production (sequenziale single agent)

> **Per Claude Code in nuova sessione (single agent).** Tempo: ~3-4h elapsed (dipende da quanti bug si trovano).
> **PRECEDENZA**: Wave 0+1+2+3 completati. Theme `1.0.0-recovery-wave3` su staging.studiolegalesaltelli.it · 21/21 PASS HTTP 200 · ACF popolato.
> **MISSIONE**: trovare e fixare bug residui prima di Wave 4 (Production Readiness). Questo NON è una wave di sviluppo nuovo — è uno stress test sistematico del lavoro già consegnato. L'output è una **lista di bug trovati + fix applicati**, NON nuove features.
> **PRECEDENZA assoluta su Wave 4**: Wave 4 (WOFF2, SRI, Critical CSS, Lighthouse) parte SOLO dopo che questa fase è chiusa con report consolidato.

---

## 🎯 Tu sei

L'**Agente Debug & QA**. Il sito Wave 3 è funzionante ma è stato testato in modo "happy path" (smoke 21 URL HTTP 200, niente di più profondo). Adesso devi:

1. **Audit visivo cross-browser cross-device** — Chrome, Firefox, Safari, mobile
2. **Audit ACF rendering** — verificare che ogni field popolato si renderizzi correttamente, ogni fallback funzioni
3. **Link check** — link interni rotti, redirect chains, 404 nascosti
4. **Form check** — Contact Form 7 funziona end-to-end
5. **Console error check** — JS errors, network 404, CORS warnings
6. **Schema validation soft** — JSON-LD presente e parsing-valido (validation finale Wave 4)
7. **Editorial check coordinato con Elena** — copy gaps, tono, punteggiatura, sigle
8. **Mobile UX check** — responsive corretto, touch target ≥44px, no horizontal scroll
9. **Accessibility quick check** — alt text, contrast, heading hierarchy
10. **Performance baseline** — solo MISURA, NO ottimizzazione (quella è Wave 4)

```
DEBUG & QA — 6 PHASES sequenziali

Phase 1 (~30 min): Setup branch + smoke ricertificazione + automated checks
Phase 2 (~45 min): Visual + content QA su 25+ URL × 3 browser × 2 viewport
Phase 3 (~30 min): ACF rendering audit + form end-to-end test
Phase 4 (~30 min): Link checker + console errors + schema soft validation
Phase 5 (~45 min): Bug fix iterativo (in branch dedicato, commit per bug)
Phase 6 (~20 min): Report consolidato + bump 1.0.0-recovery-wave3-debug
```

**Pattern**: ogni bug trovato → file ticket markdown in `.claude/knowledge/audits/debug-qa/bugs/` → fix se in scope → re-test → close. Bug fuori scope (Wave 4 / Wave 5) → documenta + skippa.

---

## 📚 Letture obbligatorie

```
CLAUDE.md                                                       (hard constraints + design tokens)
.claude/knowledge/recovery/PROJECT_STATE.md                     (stato progetto)
.claude/knowledge/recovery/v1.0-WAVE3-TEMPLATE-REFACTOR.md      (cosa è stato fatto in Wave 3)
docs/EDITOR-HANDOFF.md                                          (cosa promette al cliente — è anche il "test plan editoriale")
PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md                 (cosa NON è in scope — production prep è Wave 4)

Riferimenti tecnici:
wp-content/themes/saltelli/page.php
wp-content/themes/saltelli/single-avvocato.php
wp-content/themes/saltelli/single-competenza.php
wp-content/themes/saltelli/template-parts/*.php
wp-content/themes/saltelli/inc/acf-fields.php
wp-content/themes/saltelli/acf-json/*.json
```

---

## 🔒 Hard rules

| Rule | Decisione |
|---|---|
| **Branch dedicato**: `feat/debug-qa` parte da `main` (`310b994` o successivo) | Isolamento |
| **NESSUNA nuova feature** — solo bug fix | Scope discipline |
| **NESSUNA modifica `tokens.css`, design system, brand identity** | Out of scope |
| **NESSUNA performance optimization** (font, CSS, JS) — è Wave 4 | Out of scope |
| **NESSUN refactor template** — è Wave 3 chiuso | Out of scope |
| **NESSUNA modifica copy editoriale di propria iniziativa** — flag e chiedi a Elena/orchestratore | Editorial autonomy not yours |
| **Ogni bug trovato = ticket markdown** in `.claude/knowledge/audits/debug-qa/bugs/{NN-slug}.md` | Audit trail |
| **Ogni fix = commit dedicato** con riferimento al ticket | Audit trail |
| **Smoke 21 URL DEVE rimanere PASS** dopo ogni fix | Safety |
| **Backup pre-debug** obbligatorio (theme tar.gz + DB dump) | Rollback safety |
| **NO modifica DB direct** (no `wp eval` su tabelle, no SQL UPDATE manuali) — modifiche via WP-Admin o WP-CLI documentate | Reversibilità |
| **Idempotency**: se ri-esegui un check, lo stesso bug NON viene segnalato 2 volte (usa naming univoco file ticket) | Safety |
| **Path droplet**: `/var/www/saltelli/` | Lesson learned |
| **Bug critici (P0)** = fix subito · **Bug medi (P1)** = fix in batch · **Bug bassi (P2)** = doc + skip se Wave 4/5 fuori scope | Priorità sane |
| **STOP & ritorna se >10 bug P0** trovati in Phase 2 — significa che Wave 3 ha problemi sistemici da rivalutare | Safety |
| **Fix solo via theme files o ACF UI**, NO plugin nuovi | Lesson learned |

---

## 📋 PHASE 1 — Setup branch + ricertificazione smoke + automated checks (~30 min)

### 1.1 — Backup pre-debug

```bash
cd /Users/aldosantoro/Desktop/DEV/saltelli-wp

STAMP=$(date +%Y%m%d-%H%M%S)
mkdir -p /tmp/saltelli-backups
tar czf /tmp/saltelli-backups/saltelli-pre-debug-${STAMP}.tar.gz \
    wp-content/themes/saltelli/

ssh deploy@178.62.207.50 "wp db export - --path=/var/www/saltelli 2>/dev/null" | \
    gzip > /tmp/saltelli-backups/saltelli-db-pre-debug-${STAMP}.sql.gz

ls -lh /tmp/saltelli-backups/ | tail -2
echo "✓ Backup pre-debug in /tmp/saltelli-backups/"
```

### 1.2 — Crea branch dedicato

```bash
git fetch origin main
git checkout main
git pull --ff-only origin main
git checkout -b feat/debug-qa
echo "✓ branch feat/debug-qa creato da $(git rev-parse --short HEAD)"
```

### 1.3 — Crea struttura directory ticket

```bash
mkdir -p .claude/knowledge/audits/debug-qa/{bugs,checks,reports}
echo "✓ Struttura debug-qa pronta"
```

### 1.4 — Ricertifica smoke 21 URL su staging

```bash
URLS=(
    "/" "/lo-studio/" "/avvocati/" "/avvocati/emiliano-saltelli/"
    "/avvocati/fabiana-saltelli/" "/avvocati/antonia-battista/"
    "/avvocati/stefano-gaetano-tedesco/" "/casi/" "/costi/" "/contatti/"
    "/faq/" "/come-lavoriamo/" "/prima-consulenza/" "/lavora-con-noi/"
    "/richiedi-preventivo/" "/guide-gratuite/"
    "/competenze/diritto-tributario/" "/competenze/diritto-del-lavoro/"
    "/competenze/diritto-di-famiglia-lgbtq/" "/tipo-area/privati/"
    "/glossario-legale/"
)

PASS=0; FAIL=0
for U in "${URLS[@]}"; do
    code=$(curl -o /dev/null -s -w "%{http_code}" "https://staging.studiolegalesaltelli.it${U}" --max-time 10)
    if [ "$code" = "200" ]; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); echo "FAIL $code  $U"; fi
done
echo "Smoke baseline: $PASS PASS · $FAIL FAIL"
```

Atteso: **21/21 PASS**. Se anche solo 1 FAIL → P0 bug, ferma e segnala all'orchestratore.

### 1.5 — Esegui automated checks (deps)

Deps Mac: `brew install lychee` (link checker), Chrome installato per headless.

```bash
which lychee || brew install lychee
which chrome || which 'google-chrome' || ls /Applications/Google\ Chrome.app
which curl jq node
```

### 1.6 — Estendi URL list a 30+ (per Phase 2 visual check)

Aggiungi queste 9 URL all'elenco visual check (sotto-aree spesso non smoke-testate):

```bash
URLS_EXT=(
    # Le 21 base sopra
    # +
    "/competenze/cartelle-esattoriali-e-multe/"
    "/competenze/diritto-bancario/"
    "/competenze/diritto-condominiale/"
    "/competenze/diritto-penale/"
    "/competenze/recupero-crediti/"
    "/blog/"
    "/categoria/diritto-tributario/"  # se esiste
    "/?s=cartella"   # search results
    "/wp-sitemap.xml"
    "/llms.txt"
    "/robots.txt"
)
```

Salva `.claude/knowledge/audits/debug-qa/checks/url-list.txt` con tutte le URL da auditare in Phase 2.

### 1.7 — Phase 1 commit (struttura + baseline)

```bash
git add .claude/knowledge/audits/debug-qa/

git commit -m "chore(debug-qa): Phase 1 — setup baseline + URL list

- Branch feat/debug-qa creato da main 310b994
- Backup pre-debug in /tmp/saltelli-backups/
- Smoke ricertificato 21/21 PASS su staging
- URL list estesa (32 URLs) per Phase 2 visual audit
- Struttura directory bugs/checks/reports pronta"
```

---

## 📋 PHASE 2 — Visual + content QA cross-browser/device (~45 min)

### 2.1 — Matrice di test

| Browser | Viewport | Cosa cercare |
|---|---|---|
| Chrome 124+ desktop 1440×900 | Layout, font rendering, GSAP scroll triggers, hover states |
| Chrome mobile 375×812 | Responsive, touch target ≥44px, no horizontal scroll |
| Firefox desktop 1440×900 | CSS quirks, font fallback |
| Safari desktop (se Mac) | Webkit issues (es. backdrop-filter, scroll snap) |
| iPhone Safari (se possibile) | iOS Safari quirks (es. 100vh issue) |

Per ogni viewport × browser, naviga la **URL list completa** (32 URLs) e osserva.

### 2.2 — Checklist visual per ogni pagina

Apri ogni URL e verifica:

```
Hero
  ☐ Eyebrow visibile, formato corretto "§ Topic · Subtopic"
  ☐ H1 presente, font Playfair Display, weight 700
  ☐ Lede italic visibile sotto H1
  ☐ Aspect ratio rispettato su mobile

Body
  ☐ Drop-cap automatico sul primo paragrafo (se applicabile)
  ☐ Heading hierarchy corretta (H1 → H2 → H3, no salti)
  ☐ Linee non troppo lunghe (max ~75 caratteri per riga su desktop)
  ☐ Drop-cap NON appare in body inline (solo apertura)

CTA
  ☐ Bottone visibile, contrast ratio ≥4.5
  ☐ Hover state presente (lift, color shift)
  ☐ Click porta all'URL atteso (no 404, no link relativo errato)
  ☐ Mobile: touch target ≥44×44px

Footer
  ☐ NAP completo (telefono, email, indirizzo)
  ☐ Click-to-call funziona su mobile
  ☐ Social links non cliccano se vuoti (no <a href="">)
  ☐ Newsletter form (se attivo) renderizza

Mobile-specific
  ☐ Menu hamburger apre/chiude
  ☐ Niente horizontal scroll (test: console: document.body.scrollWidth > window.innerWidth)
  ☐ Tutti i font leggibili (min 14px body)
  ☐ Touch target spacing adeguato

Performance check (osservazione, NO fix qui)
  ☐ FOUT/FOIT visibile? Documenta (Wave 4 fix)
  ☐ LCP visualmente accettabile? Cronometra mentalmente
  ☐ Layout shift (CLS) presente? Documenta
```

### 2.3 — Per ogni issue: file ticket

Format `.claude/knowledge/audits/debug-qa/bugs/{NN}-{slug}.md`:

```markdown
# Bug #02 — Drop-cap appare anche in primo paragrafo body inline (non hero)

**Severity:** P1 (medium)
**Found:** 2026-05-05 by Debug QA Agent
**Browser:** Chrome 124 desktop 1440×900
**URL:** /competenze/diritto-tributario/

## Descrizione

Sulla pagina tributario il body editorial ha un drop-cap automatico anche
sul primo paragrafo del body, non solo sul lede.

## Atteso

Drop-cap solo sul primo paragrafo del **lede** (sezione hero).
Body editorial → no drop-cap automatico (è già stato applicato sopra).

## Reproduce

1. Apri https://staging.studiolegalesaltelli.it/competenze/diritto-tributario/
2. Scrolla fino al body editorial dopo l'hero
3. Vedi che la prima lettera del body ha drop-cap

## Screenshot

[allegato: bug-02-dropcap.png]

## Status

- [ ] Reproduced
- [ ] Root cause identified
- [ ] Fix applied
- [ ] Re-tested
- [ ] Closed

## Fix proposto

`assets/css/base.css` — restringe `.sl-page-h1 + p::first-letter` a non
applicarsi se il paragrafo è già dentro `.sl-page__prose`.
```

### 2.4 — Aggrega tutti gli ticket trovati

A fine Phase 2 deve esistere `.claude/knowledge/audits/debug-qa/bugs/{01..NN}-*.md` con uno o più ticket per ogni issue trovato. Conta:

```bash
ls -1 .claude/knowledge/audits/debug-qa/bugs/*.md | wc -l
```

### 2.5 — Phase 2 commit

```bash
git add .claude/knowledge/audits/debug-qa/bugs/

git commit -m "chore(debug-qa): Phase 2 — visual + content QA findings

NN bug ticket creati in .claude/knowledge/audits/debug-qa/bugs/:
- P0: NN
- P1: NN
- P2: NN

Cross-browser test: Chrome desktop+mobile, Firefox desktop, Safari desktop.
URL coperte: 32 (21 smoke + 11 estensioni).

Bug categorizzati per area: hero, body, CTA, footer, mobile, performance-baseline.
Fix iterativo in Phase 5."
```

---

## 📋 PHASE 3 — ACF rendering audit + form end-to-end (~30 min)

### 3.1 — ACF field rendering audit

Verifica che ogni field popolato si renderizzi e ogni fallback funzioni.

```bash
ssh deploy@178.62.207.50 "cd /var/www/saltelli; \
echo '═ Theme Options completeness ═'; \
wp db query 'SELECT option_name, CHAR_LENGTH(option_value) AS len FROM wp_options WHERE option_name LIKE \"options\\_%\" AND option_name NOT LIKE \"\\_options\\_%\" ORDER BY option_name;' --path=/var/www/saltelli 2>&1 | head -30; \
echo ''; \
echo '═ Page WP fields (campionatura su 9 pagine) ═'; \
for PAGE_ID in 2695 2699 23 2705 2706 2708 2709 2710 372; do \
  TITLE=\$(wp post get \$PAGE_ID --field=post_title --path=/var/www/saltelli); \
  COUNT=\$(wp db query \"SELECT COUNT(*) FROM wp_postmeta WHERE post_id = \$PAGE_ID AND meta_key NOT LIKE '\\_%';\" --path=/var/www/saltelli --skip-column-names 2>&1 | tail -1); \
  echo \"  #\$PAGE_ID  \$TITLE  →  \$COUNT meta keys\"; \
done; \
echo ''; \
echo '═ Avvocati fields (4 schede) ═'; \
for PAGE_ID in 2660 2661 2662 2663; do \
  TITLE=\$(wp post get \$PAGE_ID --field=post_title --path=/var/www/saltelli); \
  HERO_ROLE=\$(wp eval \"echo get_field('hero_role', \$PAGE_ID) ?: 'VUOTO';\" --path=/var/www/saltelli); \
  BIO_BRE=\$(wp eval \"echo strlen(get_field('bio_breve', \$PAGE_ID));\" --path=/var/www/saltelli); \
  BIO_EST=\$(wp eval \"echo strlen(get_field('bio_estesa', \$PAGE_ID));\" --path=/var/www/saltelli); \
  POST_C=\$(wp eval \"\\\$p=get_post(\$PAGE_ID); echo strlen(\\\$p->post_content);\" --path=/var/www/saltelli); \
  echo \"  #\$PAGE_ID  \$TITLE  →  role:\$HERO_ROLE | bio_breve:\$BIO_BRE | bio_estesa:\$BIO_EST | post_content:\$POST_C\"; \
done; \
echo ''; \
echo '═ Tier-1 competenze answer capsule ═'; \
for PAGE_ID in 2664 2665 2666; do \
  TITLE=\$(wp post get \$PAGE_ID --field=post_title --path=/var/www/saltelli); \
  CAPSULE=\$(wp eval \"echo strlen(get_field('answer_capsule', \$PAGE_ID));\" --path=/var/www/saltelli); \
  echo \"  #\$PAGE_ID  \$TITLE  →  answer_capsule: \$CAPSULE chars\"; \
done" 2>&1 > .claude/knowledge/audits/debug-qa/checks/acf-rendering.txt

cat .claude/knowledge/audits/debug-qa/checks/acf-rendering.txt
```

**Cosa cercare**:
- Theme Options con `len = 0` non vuoti (suspect, verifica)
- Pagine con < 5 meta keys (probabilmente Wave 2 ha skippato qualcosa)
- Avvocati con `bio_estesa` < 100 chars E `post_content` < 100 chars (entrambi vuoti = bug)
- Answer capsule Tier-1 < 200 chars (sotto target 50-60 parole)

Per ogni anomalia → ticket bug `.claude/knowledge/audits/debug-qa/bugs/{NN}-acf-*.md`.

### 3.2 — Form Contact Form 7 end-to-end test

```bash
echo "═ Form CF7 endpoint check ═"
curl -s "https://staging.studiolegalesaltelli.it/contatti/" | grep -E 'wpcf7|form action|action=' | head -10

# Test invio reale:
# 1. Apri https://staging.studiolegalesaltelli.it/contatti/ in browser
# 2. Compila il form con email di test (es. test+saltelli@adsolut.it)
# 3. Invia
# 4. Verifica entro 1-2 min:
#    - Conferma visiva sul frontend
#    - Email arrivata a info@studiolegalesaltelli.it (chiedi a Elena)
#    - Email NON in spam
```

Se non hai accesso a info@studiolegalesaltelli.it, **delega test a Elena** e documenta come "test pendente".

### 3.3 — Phase 3 commit

```bash
git add .claude/knowledge/audits/debug-qa/checks/ \
        .claude/knowledge/audits/debug-qa/bugs/

git commit -m "chore(debug-qa): Phase 3 — ACF rendering + form audit

- 26 Theme Options validati (NN OK + NN VUOTO)
- 9 page WP custom field audit
- 4 avvocati field audit (vedi bug-NN bio_estesa migration)
- 3 Tier-1 competenze answer capsule validation
- Form CF7 test pendente (delegato a Elena per verifica email)

Audit raw: .claude/knowledge/audits/debug-qa/checks/acf-rendering.txt"
```

---

## 📋 PHASE 4 — Link checker + console errors + schema soft (~30 min)

### 4.1 — Link checker (lychee)

```bash
mkdir -p .claude/knowledge/audits/debug-qa/checks

lychee \
  --max-concurrency 4 \
  --timeout 15 \
  --exclude-mail \
  --include "https?://staging\.studiolegalesaltelli\.it/.*" \
  --output .claude/knowledge/audits/debug-qa/checks/link-check.md \
  --format markdown \
  https://staging.studiolegalesaltelli.it/ \
  2>&1 | tail -10

# Per ogni URL della list (sitemap completa)
lychee \
  --base https://staging.studiolegalesaltelli.it \
  --output .claude/knowledge/audits/debug-qa/checks/link-check-sitemap.md \
  https://staging.studiolegalesaltelli.it/wp-sitemap.xml \
  2>&1 | tail -10

cat .claude/knowledge/audits/debug-qa/checks/link-check.md | head -50
```

**Cosa cercare**:
- HTTP 404 → ticket bug
- HTTP 5xx → server error, ticket urgente
- HTTP 3xx redirect chains > 1 → ticket P2 (Wave 5)
- Link a `studiolegalesaltelli.it` (production) invece di `staging.*` → ticket P1 (cross-env link)

### 4.2 — Console errors check via headless Chrome

```bash
mkdir -p .claude/knowledge/audits/debug-qa/checks/console-logs

# Headless Chrome con CDP per catturare console
URLS_CONSOLE=(
    "/" "/avvocati/emiliano-saltelli/" "/competenze/diritto-tributario/"
    "/faq/" "/contatti/" "/lo-studio/"
)

for U in "${URLS_CONSOLE[@]}"; do
    SLUG=$(echo "$U" | sed 's|/||g;s|^|page-|;s|page-$|page-home|')
    OUT=".claude/knowledge/audits/debug-qa/checks/console-logs/${SLUG}.txt"
    
    "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" \
      --headless --disable-gpu --dump-dom --enable-logging --v=0 \
      "https://staging.studiolegalesaltelli.it${U}" \
      > /dev/null 2> "$OUT" 2>&1
    
    # Filter solo error + warning
    grep -iE 'error|warning|failed|cors|404|500' "$OUT" > "${OUT}.filtered" || echo "  (no issues)" > "${OUT}.filtered"
    
    SIZE=$(wc -l < "${OUT}.filtered" | tr -d ' ')
    echo "  $U → $SIZE issues"
done
```

⚠ Se headless Chrome non è installato/non-trivialmente eseguibile via shell: **delega a Phase 5 con Playwright o Puppeteer** (richiede `npm install playwright`). Oppure: **manuale** con DevTools console su 6 URL chiave + screenshot.

### 4.3 — Schema soft validation

Verifica solo che il JSON-LD sia parsable e contenga i tipi attesi (validation finale Wave 4):

```bash
URLS_SCHEMA=(
    "/" "/lo-studio/" "/avvocati/emiliano-saltelli/"
    "/competenze/diritto-tributario/" "/faq/"
)

for U in "${URLS_SCHEMA[@]}"; do
    SLUG=$(echo "$U" | sed 's|/||g;s|^|page-|;s|page-$|page-home|')
    OUT=".claude/knowledge/audits/debug-qa/checks/schema-${SLUG}.txt"
    
    curl -sL "https://staging.studiolegalesaltelli.it${U}" | python3 -c "
import sys, re, json
html = sys.stdin.read()
matches = re.findall(r'<script[^>]*type=[\"\']application/ld\+json[\"\'][^>]*>(.*?)</script>', html, re.DOTALL)
print(f'URL: $U')
print(f'JSON-LD blocks: {len(matches)}')
for i, m in enumerate(matches):
    try:
        d = json.loads(m.strip())
        if isinstance(d, dict):
            ts = d.get('@type') or [g.get('@type','?') for g in d.get('@graph',[])]
            print(f'  block {i}: @type = {ts}')
    except json.JSONDecodeError as e:
        print(f'  block {i}: INVALID JSON: {e}')
" > "$OUT"
    
    cat "$OUT"
    echo ""
done
```

**Cosa cercare**:
- 0 JSON-LD blocks → bug critico schema mancante
- INVALID JSON → bug critico, schema rotto
- Tipi attesi mancanti (es. /lo-studio/ senza LocalBusiness) → ticket bug

### 4.4 — Phase 4 commit

```bash
git add .claude/knowledge/audits/debug-qa/checks/ \
        .claude/knowledge/audits/debug-qa/bugs/

git commit -m "chore(debug-qa): Phase 4 — link check + console errors + schema soft

- Link check via lychee: NN total, NN broken, NN redirects
- Console errors capture su 6 URL chiave (headless Chrome)
- Schema JSON-LD soft validation su 5 URL (presenza + parsing)

Findings:
- NN link rotti (ticket bug-NN..)
- NN console errors di rilievo (ticket bug-NN..)
- NN issue schema (rinviato a Wave 4 per validation finale Google Rich Results)

Audit raw: .claude/knowledge/audits/debug-qa/checks/{link-check,console-logs,schema-*}.md"
```

---

## 📋 PHASE 5 — Bug fix iterativo (~45 min, dipende dai bug)

### 5.1 — Triage bug per priorità

```bash
# Lista tutti i ticket bug
ls -1 .claude/knowledge/audits/debug-qa/bugs/*.md

# Filtra per severity (cerca "Severity: P0" / P1 / P2 nel front-matter)
grep -l 'Severity: P0' .claude/knowledge/audits/debug-qa/bugs/*.md
grep -l 'Severity: P1' .claude/knowledge/audits/debug-qa/bugs/*.md
grep -l 'Severity: P2' .claude/knowledge/audits/debug-qa/bugs/*.md
```

### 5.2 — Fix loop per ogni bug P0/P1 in scope

Per ogni bug P0 → fix subito. Per ogni bug P1 → fix se in scope (non-Wave 4 / non-Wave 5). Per P2 → documenta + skippa.

**Pattern fix**:

1. Leggi ticket → identifica root cause
2. Implementa fix minimal nel theme file (NO refactor)
3. Test locale + smoke 21/21 PASS
4. Aggiorna ticket: `## Status` → mark `[x] Fix applied` + paragrafo "Fix applied" con commit hash
5. Commit dedicato:
   ```bash
   git commit -m "fix(debug-qa): bug-NN — {one-line description}
   
   Ticket: .claude/knowledge/audits/debug-qa/bugs/NN-slug.md
   Root cause: {sintesi}
   Fix: {sintesi modifica}
   Files modified: {list}
   Tested: smoke 21/21 PASS"
   ```
6. Re-test bug specifico → conferma chiusura
7. Aggiorna ticket: `[x] Closed`

### 5.3 — Bug fuori scope: documenta e skippa

Per ogni bug **fuori scope** Debug QA (es. richiede WOFF2, Critical CSS, schema fix complesso):

- Aggiorna ticket: `## Status` → `[x] Out of scope — defer to Wave 4` o `Wave 5`
- Aggiungi nota chiara con razionale
- NON committare un fix qui

### 5.4 — Verifica finale stato bugs

```bash
echo "═ Bug status summary ═"
for f in .claude/knowledge/audits/debug-qa/bugs/*.md; do
    NAME=$(basename "$f" .md)
    SEVERITY=$(grep '^\*\*Severity:' "$f" | head -1 | sed 's/.*Severity:\*\* //')
    STATUS=$(grep -c '\[x\] Closed' "$f")
    DEFER=$(grep -c 'Out of scope' "$f")
    
    if [ "$STATUS" = "1" ]; then SYM="✅ CLOSED"; \
    elif [ "$DEFER" = "1" ]; then SYM="⏸ DEFERRED"; \
    else SYM="🔧 OPEN"; fi
    
    printf "  %-30s  %-12s  %s\n" "$NAME" "$SEVERITY" "$SYM"
done
```

### 5.5 — Smoke finale 21 URL

```bash
PASS=0; FAIL=0
for U in "${URLS[@]}"; do
    code=$(curl -o /dev/null -s -w "%{http_code}" "https://staging.studiolegalesaltelli.it${U}" --max-time 10)
    if [ "$code" = "200" ]; then PASS=$((PASS+1)); else FAIL=$((FAIL+1)); fi
done
echo "Smoke finale: $PASS PASS · $FAIL FAIL"
```

Atteso: **21/21 PASS**. Se anche solo 1 FAIL → un fix ha rotto qualcosa, **ROLLBACK** quel commit specifico (`git reset HEAD~ --hard` se è l'ultimo, altrimenti `git revert`).

---

## 📋 PHASE 6 — Report consolidato + bump (~20 min)

### 6.1 — Genera report finale

```bash
cat > .claude/knowledge/audits/debug-qa/reports/REPORT.md <<'MD'
# Debug & QA — Report Finale

> Stress test pre-production · 2026-05-XX
> Branch: `feat/debug-qa` · Theme version: `1.0.0-recovery-wave3-debug`

---

## 📊 Score: NN/NN bugs closed · NN deferred · NN out of scope

## 🐛 Bug breakdown

### P0 (Critical) — NN found
[ELENCO con link a ticket]

### P1 (Medium) — NN found
[ELENCO con link a ticket]

### P2 (Low) — NN found
[ELENCO con link a ticket]

## ✅ Cosa è stato fixato

- bug-NN: {sintesi} (commit XXX)
- ...

## ⏸ Cosa è stato deferred (Wave 4 / Wave 5)

- bug-NN: {motivo}
- ...

## 🔍 Cosa è stato verificato OK

- Smoke 21/21 URL PASS (cross-test)
- Theme Options 26/26 popolati
- ACF rendering completo per page custom
- Form CF7 endpoint reachable (delivery delegato a Elena)
- Schema JSON-LD presente su tutte le 5 URL chiave (validation finale Wave 4)
- Link check via lychee NN total / NN broken
- Console errors capture su 6 URL chiave

## 🚦 Pronto per Wave 4

Sì / No con motivazione.

## Next steps (orchestratore in chat)

1. Audit di questo report
2. Merge feat/debug-qa → main
3. Bump version droplet
4. Lancio Wave 4 / Production Readiness

---
*Generato by Debug & QA Agent · {date}*
MD

echo "✓ Report generato (popolare gli NN reali)"
```

### 6.2 — Bump version → `1.0.0-recovery-wave3-debug`

```bash
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '[^']*');/define('SALTELLI_THEME_VERSION', '1.0.0-recovery-wave3-debug');/" \
    wp-content/themes/saltelli/functions.php
rm wp-content/themes/saltelli/functions.php.bak

sed -i.bak "s/^Version: .*$/Version: 1.0.0-recovery-wave3-debug/" \
    wp-content/themes/saltelli/style.css
rm wp-content/themes/saltelli/style.css.bak

grep -E "Version|SALTELLI_THEME_VERSION" \
    wp-content/themes/saltelli/style.css \
    wp-content/themes/saltelli/functions.php | head -5
```

### 6.3 — Phase 6 commit + push branch

```bash
git add wp-content/themes/saltelli/style.css \
        wp-content/themes/saltelli/functions.php \
        .claude/knowledge/audits/debug-qa/reports/

git commit -m "chore(s2-v1.0.0-recovery-wave3-debug): Phase 6 — Debug & QA report finale + bump

NN bugs trovati durante stress test pre-production:
- NN P0 (critical) — tutti chiusi
- NN P1 (medium) — NN chiusi · NN deferred
- NN P2 (low) — NN chiusi · NN deferred

Wave 4 ready: SI/NO ({motivazione})

Report: .claude/knowledge/audits/debug-qa/reports/REPORT.md
Tickets: .claude/knowledge/audits/debug-qa/bugs/*
Audit raw: .claude/knowledge/audits/debug-qa/checks/*

Bump SALTELLI_THEME_VERSION → 1.0.0-recovery-wave3-debug.
Branch feat/debug-qa pronto per audit + merge in main da orchestratore."

git push -u origin feat/debug-qa 2>&1 | tail -3
```

### 6.4 — Deploy fix su droplet (decisione orchestratore)

Se i bug fixati richiedono deploy droplet (es. fix template parts), **delega all'orchestratore**:

> Branch `feat/debug-qa` pushato. NN file modificati nel theme richiedono rsync su droplet. Decisione orchestratore: deploy ora o batch con merge main?

NON eseguire rsync droplet in autonomia.

---

## ✅ Definition of Done (Debug & QA)

- [ ] **Phase 1**: branch + backup + smoke baseline 21/21 PASS
- [ ] **Phase 2**: visual + content QA cross-browser × 32 URL — ticket creati per ogni issue
- [ ] **Phase 3**: ACF rendering audit + form CF7 test (anche se delegato a Elena)
- [ ] **Phase 4**: link check + console errors + schema soft validation
- [ ] **Phase 5**: bug fix iterativo (P0 tutti chiusi · P1 in-scope chiusi · P2 documentati)
- [ ] **Phase 6**: report consolidato + bump 1.0.0-recovery-wave3-debug + push branch
- [ ] **Smoke finale 21/21 PASS** dopo tutti i fix
- [ ] **NN ticket bug** in `.claude/knowledge/audits/debug-qa/bugs/` con stato chiaro (closed / deferred / open)
- [ ] **Report finale** in `.claude/knowledge/audits/debug-qa/reports/REPORT.md`
- [ ] **Branch `feat/debug-qa` pushato** su origin (NON merge in main — orchestratore in chat)

---

## 🚦 Branch & deploy state finale

**Branch**: `feat/debug-qa`
- 6+ commit Phase 1-6 + N commit fix (uno per bug)
- Push: `git push -u origin feat/debug-qa`
- Merge in main: **decisione orchestratore in chat dopo audit**

**Droplet staging**:
- Decisione orchestratore: rsync delta dei file modificati post-merge main
- Smoke 21 URL https su droplet aggiornato
- Bump version droplet a `1.0.0-recovery-wave3-debug`

**Backup pre-debug**: `/tmp/saltelli-backups/saltelli-pre-debug-${STAMP}.tar.gz` + DB

---

## 🚦 Next dopo Debug & QA (out of scope, info per orchestratore)

```
Sequenza pianificata:
  ☐ Audit Debug & QA da orchestratore (chat)
  ☐ Merge feat/debug-qa → main
  ☐ Deploy fix su droplet
  ☐ Lancio Wave 4 / Production Readiness (prompt già in repo)
  ☐ Wave 4 chiusura → bump 1.0.0-rc1
  ☐ Cut produzione 1.0.0 (DNS switch)
```

---

## ⚠️ Quando STOP e ritorno orchestratore

Ferma esecuzione e ritorna all'orchestratore (chat) se:

1. **>10 bug P0** trovati in Phase 2 — significa Wave 3 ha problemi sistemici da rivalutare prima di procedere
2. **Smoke 21/21 NON PASS** in Phase 1 — c'è una regression silenziosa pre-debug
3. **Schema JSON-LD totalmente mancante** su tutte le URL — bug strutturale, non in scope debug
4. **CF7 form NON funziona** end-to-end (delivery email fallisce) — può essere SMTP / nginx / DKIM, fuori scope
5. **Branch divergence** con main durante l'esecuzione (es. orchestratore committa parallelo) — rebase coordinato
6. **Bug richiede modifica `tokens.css` / design system** — out of scope assoluto, escalation a orchestratore
7. **Trovato P0 + fix richiede modifica DB structure** (es. migrate field) — coordinazione necessaria

**Tono comunicazione di ritorno**: come da CLAUDE.md — diretto, concreto, ranked options, no apology padding.

---

## 📋 Note operative

### Modalità di esecuzione consigliata

**Esegui Debug & QA come 1-2 sessioni di lavoro**, NON una sola maratona:

- **Sessione 1** (~2h): Phase 1-4 — collect tutti i bug, NO fix
- **Sessione 2** (~2h): Phase 5-6 — fix iterativo + report

In mezzo, sync con orchestratore per:
- Triage bug list (concorda P0/P1/P2)
- Coordinare fix che richiedono input editorial Elena
- Approvare deferral di bug a Wave 4/5

### Coordinazione con Elena (editorial QA)

Elena/Ludovica stanno lavorando con `docs/EDITOR-HANDOFF.md` come strumento di QA editoriale. Possono:

- Validare copy / tono / sigle
- Caricare 1+ guida di test
- Migrare bio_estesa nel campo ACF
- Riassociare blog ai cluster Tier-1

Tu (Debug QA Agent) non duplichi il loro lavoro. Se trovi un bug editorial **flag** ma NON modificare copy senza loro input.

---

*Generato 2026-05-05 by orchestrator (Claude in chat) per Claude Code single-agent execution.*
*Pattern simmetrico a PROMPT_AGENT_v1.0_WAVE*.md ma scope = stress test, NON nuove feature.*
