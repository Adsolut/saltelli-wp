# Prompt — Impeccable Agent (Step C — Refinements visivi avanzati)

> **Per Claude Code in nuova sessione.** Apri `saltelli-wp/`, leggi questo file, eseguilo. Lavoro previsto: 1-2 ore.
> **PRECEDENZA:** Polish Agent (Step B) deve essere COMPLETO con report `5/5 PASS`. Se non lo è, fermati e segnala a Duccio.

---

## Tu sei

L'**Impeccable Agent**. Il tuo lavoro è raffinare il tema Saltelli post-beta a livello chirurgico usando la skill open source `pbakaus/impeccable` (19k★ Apache 2.0) — 18 comandi + 7 reference per typography, color, spatial design, motion, interaction, responsive, ux-writing.

**Stato di partenza:** v0.3.0-beta-polish (post Step B). Design system stabile, animazioni funzionanti. Tutti i polish "ovvi" già fatti. Quello che resta è la **rifinitura editoriale fine** che separa "demo-ready" da "publication-ready".

**Cosa NON fai:**
- Rifare il design system (è locked: navy / crema / bronzo / Playfair / DM Sans)
- Aggiungere sezioni nuove
- Cambiare copy della Homepage (è copy editoriale Claude Design già validato)
- Toccare schema JSON-LD (territorio GEO Engineer)

---

## Letture obbligatorie (in ordine)

1. `CLAUDE.md` — hard constraints
2. `ux-research/ADSOLUT_UX_REFERENCE_METHOD.md` — il metodo Adsolut, in particolare **Upgrade 4** (anti-pattern detection), che giustifica l'uso di Impeccable
3. `SHIP_PLAN_24H.md` sezione "Fase 1.E.10 Impeccable polishing pass" — workflow operativo già pianificato
4. `.claude/knowledge/design/sessione-1/tokens.css` — tokens canonici per non drift
5. `wp-content/themes/saltelli/assets/css/sections.css` — il file dove vivono CSS sezioni e compat-shim
6. **DOPO l'install:** la skill stessa in `.claude/skills/impeccable/SKILL.md` + reference

---

## Hard rules

| Rule | Reason |
|---|---|
| Decisioni Adsolut superiori a suggerimenti generici Impeccable | Es. se Impeccable suggerisce "più colore", il nostro metodo dice "palette sobria deliberata" — vince Adsolut |
| Mai modificare i design tokens (`--background`, `--primary`, `--accent`, ecc.) senza approvazione esplicita di Duccio | Locked design |
| Niente nuove dipendenze JS oltre GSAP+Lenis+SplitText già caricate | Performance |
| Mai disattivare animazioni esistenti senza ragione tecnica | Lavoro precedente Polish Agent |
| Tutti i fix devono ridurre o stabilizzare il punteggio detector, non peggiorarlo | Quality gate |
| `prefers-reduced-motion: reduce` rispettato | A11y |
| Nessun `console.log` in production | Cleanup |

---

## Task 1 — Setup Impeccable nel repo (10 min)

```bash
cd /Users/aldosantoro/Desktop/DEV/saltelli-wp

# Download skill bundle per Claude Code
curl -L https://impeccable.style/api/download/claude-code -o impeccable.zip
unzip impeccable.zip -d ./impeccable-tmp/
ls impeccable-tmp/

# Verifica struttura: dovrebbe contenere .claude/skills/impeccable/ + .claude/commands/impeccable/
# Sposta in posizione canonica
if [ -d "impeccable-tmp/.claude" ]; then
    cp -r impeccable-tmp/.claude/* .claude/ 2>/dev/null
fi

rm -rf impeccable-tmp impeccable.zip

# Verifica installato
ls .claude/skills/ | grep impeccable
ls .claude/commands/ 2>/dev/null | grep -E "audit|critique|polish|typeset" | head -5
```

Se l'install fallisce (URL down, zip non valido, ecc.), fallback: clona il repo direttamente

```bash
git clone --depth 1 https://github.com/pbakaus/impeccable.git impeccable-tmp
cp -r impeccable-tmp/.agents/skills/* .claude/skills/ 2>/dev/null
rm -rf impeccable-tmp
```

Qualunque sia il path, l'output deve essere `.claude/skills/impeccable/SKILL.md` esistente.

---

## Task 2 — Detector run baseline (5 min)

Prima di toccare nulla, run del CLI detector per stabilire la baseline:

```bash
mkdir -p .claude/knowledge/design/sessione-1/reports/impeccable

# Detector standalone (regex-based, no AI)
npx --yes impeccable detect wp-content/themes/saltelli/ --json \
    > .claude/knowledge/design/sessione-1/reports/impeccable/baseline.json 2>&1

# Anche human-readable
npx --yes impeccable detect wp-content/themes/saltelli/ \
    > .claude/knowledge/design/sessione-1/reports/impeccable/baseline.txt 2>&1

# Anche sull'URL live (ha più info: Puppeteer)
npx --yes impeccable detect http://localhost:8080 --json \
    > .claude/knowledge/design/sessione-1/reports/impeccable/baseline-live.json 2>&1
```

Conta gli issue:

```bash
echo "Issue count baseline (filesystem):"
jq '.issues | length' .claude/knowledge/design/sessione-1/reports/impeccable/baseline.json 2>/dev/null

echo "Issue count baseline (live URL):"
jq '.issues | length' .claude/knowledge/design/sessione-1/reports/impeccable/baseline-live.json 2>/dev/null
```

**Salva questo numero.** Sarà il "before" da confrontare con il "after".

---

## Task 3 — Audit comando `/audit` (15 min)

Lancia il comando di Impeccable nella tua sessione Claude Code:

```
/audit homepage
```

Output atteso: catalogo issues tecniche su Homepage:
- a11y (contrast, focus states, alt text, heading hierarchy)
- performance (image sizes, font loading, JS bloat)
- responsive (touch targets, viewport, font scale)

**Cosa fai con l'output:**
1. Salvalo in `.claude/knowledge/design/sessione-1/reports/impeccable/audit-homepage.md`
2. Per ogni issue, decidi se è da fixare ora (apply) o da skippare (con razionale scritto)
3. **Skippa** suggerimenti che contraddicono il metodo Adsolut (es. "aggiungere più colore", "più contrasto" se rompe l'editorial calm)

---

## Task 4 — Critique `/critique` (15 min)

```
/critique homepage
```

Output atteso: review UX su gerarchia, chiarezza, risonanza emotiva. Salvalo in `audit-homepage.md` come sezione "Critique".

**Decisione cruciale:** se Impeccable critica scelte deliberate del nostro design (es. "spazio bianco eccessivo", "headline troppo grande", "filtri pillole poco visibili"), **mantieni la nostra scelta** e annota nel report perché.

---

## Task 5 — Apply fix selettivi (30-45 min)

In ordine di priorità:

### 5a — `/typeset homepage`
Micro-fix tipografici: hierarchy, sizing, kerning, ottimizzazioni per leggibilità. Sezioni tipiche su cui agisce: line-height body 1.65 → 1.7 se troppo stretto, letter-spacing display, font-feature-settings.

**Apply solo i fix che non cambiano la scala canonica.** Es: `font-feature-settings: "kern" 1, "liga" 1, "ss01"` aggiunto su display = OK. `--fs-display: clamp(64px, 7vw, 100px)` invece dei nostri `(48px, 8vw, 120px)` = NO (drift design).

### 5b — `/layout homepage`
Spacing, ritmo verticale, grid alignment.

**Skippa** se contraddice il padding editorial generoso voluto da design.

### 5c — `/animate homepage`
Upgrade animazioni con purposeful motion.

**Apply solo se** le nuove animazioni non sostituiscono quelle del Polish Agent. Eventualmente integra (es. micro-feedback su button click, loading shimmer su CTA).

### 5d — `/harden homepage`
Error handling, loading states, empty states.

**Importante** per noi: tutti i nostri CPT possono essere vuoti in produzione finché Elena non popola. L'`/harden` aggiunge gracefully:
- Empty state per `.sl-areas__grid` se 0 competenze
- Loading skeleton per `.sl-team__lawyer` se foto non ancora caricate
- Fallback se Yoast attivo ma OG image non ancora settata

### 5e — `/polish homepage`
Final pass design system alignment + shipping readiness.

Apply tutto.

---

## Task 6 — Detector run finale + diff (10 min)

```bash
npx --yes impeccable detect wp-content/themes/saltelli/ --json \
    > .claude/knowledge/design/sessione-1/reports/impeccable/final.json

npx --yes impeccable detect http://localhost:8080 --json \
    > .claude/knowledge/design/sessione-1/reports/impeccable/final-live.json

# Confronta
echo "=== Diff issue count ==="
echo "Baseline (filesystem): $(jq '.issues | length' .../baseline.json)"
echo "Final (filesystem):    $(jq '.issues | length' .../final.json)"
echo "Baseline (live):       $(jq '.issues | length' .../baseline-live.json)"
echo "Final (live):          $(jq '.issues | length' .../final-live.json)"
```

**Definition of Done quantitativa:**
- ZERO anti-pattern AI slop residui (purple gradients, Inter font, bounce easing, dark glows, side-tab borders)
- Issue count diminuito di almeno 30% vs baseline
- Lighthouse Performance mobile ≥ 90 (verifica manuale, vedi Task 7)
- Lighthouse Accessibility ≥ 95

---

## Task 7 — Lighthouse run manuale (10 min)

In Claude Code non hai accesso a Lighthouse CLI direttamente. Suggerisci a Duccio di fare:

1. Aprire Chrome DevTools su `http://localhost:8080/`
2. Tab "Lighthouse" → "Mobile" → "Performance + Accessibility + Best Practices + SEO"
3. Generate report
4. Salvare PDF in `.claude/knowledge/design/sessione-1/reports/lighthouse-mobile.pdf`
5. Idem per Desktop

Includere il numero finale nel report.

---

## Task 8 — Bump version + cache flush

```bash
# 0.3.0-beta-polish → 0.4.0-beta-impeccable
sed -i.bak 's/Version: 0.3.0-beta-polish/Version: 0.4.0-beta-impeccable/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.3.0-beta-polish')/define('SALTELLI_THEME_VERSION', '0.4.0-beta-impeccable')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all
```

---

## Report finale

Scrivi report in `.claude/knowledge/design/sessione-1/reports/impeccable/REPORT.md`:

1. ✅/❌ ciascuno dei 8 task
2. Detector counts: baseline → final (numeri puntuali)
3. Lighthouse mobile + desktop (numeri)
4. Lista comandi `/audit /critique /typeset /layout /animate /harden /polish` lanciati
5. Lista fix applicati (suddivisi per categoria)
6. Lista suggerimenti Impeccable **skippati** + razionale (per audit trail)
7. Eventuali decisioni autonome
8. Note per Step D (Content Migration) o Step E (Template Polish): blocker/dipendenze

Poi **fermati**. Non procedere a Step D, aspetta istruzioni.

---

*v1.0 — Step C post-beta v0.3.0-polish*
