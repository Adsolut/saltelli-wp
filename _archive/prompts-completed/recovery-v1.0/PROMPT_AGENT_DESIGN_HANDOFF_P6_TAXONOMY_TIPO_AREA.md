# PROMPT AGENT — Design Handoff Wave P6 · taxonomy-tipo-area verify & drift cleanup

> **Scope**: allineare `taxonomy-tipo-area.php` + `.sl-tipoarea*` di sections.css al design source `design-handoff/taxonomy-tipo-area/index.jsx`. Template per 3 term (privati 992, imprese 993, contenzioso-amministrativo 994). 23 field per-term già coperti (Wave 5 STEP 3 coverage).
>
> **Branch**: `feat/design-handoff-taxonomy-tipo-area`
> **Stima**: 1-1.5h (severity 🟡 MEDIUM, 4 nuove CSS rule)
> **Modalità**: lean, no version bump (chore frontend, no SCF changes)
> **Sessione**: una sola Claude Code, no parallelismo.

---

## CONTESTO

Wave 6/12 sequenza Design Handoff. P1 chrome + P3 home mergeate, P2 footer skipped, P4 + P5 in sequenza.

**3 term taxonomy**: privati (992), imprese (993), contenzioso-amministrativo (994). Già Wave 5 STEP 3 coverage ha implementato `group_tipo_area_term_v1.json` (23 field per content per-term) + `taxonomy-tipo-area.php` refactor (+110 righe SCF reads).

**Decisioni orchestratore acquisite** (NON rinegoziare):
1. SoT design tokens = `tokens.css` corrente vince (KEEP CURRENT)
2. SCF data contract immutabile: 23 field `group_tipo_area_term_v1` Elena-approved → 🟢 COMPLIANT (0 additive)
3. **Hero H1 `line-height: 0.95`**: literal per-selector (phantom doc candidate `--lh-display-tight` ma defer Wave 5 STEP 5)
4. **Hero lede `font-size: 24px`**: literal per-selector (defer token `--fs-lede-lg`)
5. **Scenario title `28px`**: literal per-selector (defer `--fs-h2-floor`)
6. **CTA h2 `clamp(56px, 6.5vw, 96px)`**: literal per-selector (one-off display curve)
7. **CTA lede `22px italic`**: usa `var(--fs-lede)` (= 22px già in tokens.css)

**Pre-flight orchestratore già fatto** (Explore agent output):

| Drift | JSX | WP | Action |
|---|---|---|---|
| `.sl-tipoarea__h1` | font-size clamp(64,8vw,132) + ls -0.035em + lh 0.95 | undefinito in CSS | CREATE rule |
| `.sl-tipoarea__lede` | font-size 24px + lh 1.5 italic | undefinito | CREATE rule |
| `.sl-tipoarea__scenario-title` | font-size 28px + ls tight | undefinito | CREATE rule |
| `.sl-tipoarea__scenario-desc` | font-size 16px | undefinito | CREATE rule (con `var(--fs-body)`) |
| `.sl-tipoarea__cta-title` | font-size clamp(56,6.5vw,96) + ls -0.035em | undefinito | CREATE rule |
| `.sl-tipoarea__cta-lede` | font-size 22px italic | esiste L5425 — verify | VERIFY/ADJUST |

Tutti i phantom cross-referenced in `02-phantom-values-remaining.md`.

---

## ⚠️ HARD INVARIANT — SCF DATA CONTRACT PRESERVATION

`group_tipo_area_term_v1.json` 23 field per-term invariati: tipo_area_term_eyebrow/h1_main/h1_emphasis/intro/aside_eyebrow/scenario[1-3]_{title,desc}/quando_{label,h2_main,h2_em}/lista_{label,empty}/casi_label/cta_*.

Plus tab "Taxonomy Tipo Area" in Theme Options (UX strings comuni: `taxonomy_tipoarea_eyebrow` fallback).

**NON ACCETTABILE**: refactor field per-term, cambio taxonomy registration `tipo-area`, cambio location rule `taxonomy=tipo-area`. Solo CSS additive.

---

## PRE-FLIGHT (5 min)

1. Leggi:
   - `CLAUDE.md` (Hard constraints, Design system, "Design → Code handoff rule golden", Lessons learned)
   - `.claude/knowledge/audits/design-handoff/RECOMMENDATION.md` (§A KEEP CURRENT, §B P6)
   - `.claude/knowledge/audits/wave5-step4-sections-cleanup/02-phantom-values-remaining.md`
   - **JSX source**: `design-handoff/taxonomy-tipo-area/index.jsx`
   - **WP target**:
     - `wp-content/themes/saltelli/taxonomy-tipo-area.php` (Wave 5 STEP 3 coverage refactor +110 righe)
     - Blocchi `.sl-tipoarea*` in sections.css
   - `wp-content/themes/saltelli/acf-json/group_tipo_area_term_v1.json` (verifica 23 field intatti)

2. Verifica stato:
   ```sh
   git fetch origin
   git status
   git log --oneline -3   # atteso HEAD post-P5 single-avvocato merge (o post-P4 se P5 saltata)
   git checkout -b feat/design-handoff-taxonomy-tipo-area
   ```

3. Conferma in chat: branch creato + prosegui.

---

## PHASE 1 — VERIFY (10 min)

### 1.A — Selettori da CREATE

Cerca in sections.css se esistono già:
- `.sl-tipoarea__h1` → atteso ASSENTE (CREATE)
- `.sl-tipoarea__lede` → atteso ASSENTE (CREATE)
- `.sl-tipoarea__scenario-title` → atteso ASSENTE (CREATE)
- `.sl-tipoarea__scenario-desc` → atteso ASSENTE (CREATE)
- `.sl-tipoarea__cta-title` → atteso ASSENTE (CREATE)
- `.sl-tipoarea__cta-lede` → atteso PRESENTE (~L5425), VERIFY/ADJUST se font-size diverso da 22px

### 1.B — Selettori già allineati (no fix)

- `.sl-tipoarea__eyebrow` (mono caption, già stile globale)
- `.sl-tipoarea__count` (mono caption, già stile globale)
- `.sl-tipoarea__attorney*` (avvocati card, già patternizzato)
- `.sl-tipoarea__areas-list` `.sl-area*` (lista 19 aree, cascade da .sl-area)
- `.sl-tipoarea__caso*` (casi cluster, riusa pattern `.sl-attorney__casi`)

### 1.C — SCF reads verify (atteso 100% match)

Lettura `taxonomy-tipo-area.php` — verifica chiamate `get_field('<name>', $term)`:
- 23 field per-term + fallback su `taxonomy_tipoarea_*` Theme Options
- Atteso 23/23 ✓. NO additive.

### 1.D — Tabella drift finale in chat

Posta:
- 5 nuove CSS rule da CREATE
- 1 rule da VERIFY/ADJUST (.sl-tipoarea__cta-lede)
- 0 SCF changes
- Eventuali phantom collateral catch: segnala, non fixare

---

## PHASE 2 — IMPLEMENT (30-45 min)

### 2.A — CSS additive in sections.css

Aggiungi con scope marker `/* === design-handoff taxonomy-tipo-area P6 === */`:

```css
/* === design-handoff taxonomy-tipo-area P6 === */

.sl-tipoarea__h1 {
  font-size: clamp(64px, 8vw, 132px);    /* JSX literal hub hero */
  letter-spacing: var(--ls-display);     /* -0.035em existing token */
  line-height: 0.95;                     /* JSX literal (phantom doc candidate --lh-display-tight, deferred) */
  font-family: var(--font-display);
  font-style: italic;                    /* JSX hub h1 è italic */
  font-weight: 400;
  color: var(--primary);
  margin: 0;
}

.sl-tipoarea__lede {
  font-family: var(--font-display);
  font-style: italic;
  font-size: 24px;                       /* JSX literal (phantom doc candidate --fs-lede-lg, deferred) */
  line-height: var(--lh-lede);           /* 1.5 existing token */
  color: var(--text);
  max-width: 60ch;
  margin: 0;
}

.sl-tipoarea__scenario-title {
  font-family: var(--font-display);
  font-size: 28px;                       /* JSX literal (phantom doc candidate --fs-h2-floor, deferred) */
  letter-spacing: var(--ls-h2);          /* -0.01em existing */
  line-height: var(--lh-heading);        /* 1.15 existing */
  font-weight: 400;
  color: var(--primary);
  margin: 0 0 var(--s-3) 0;              /* 16px existing */
}

.sl-tipoarea__scenario-desc {
  font-size: var(--fs-body);             /* 16px existing token */
  line-height: var(--lh-body);           /* 1.7 existing */
  color: var(--text);
  margin: 0;
}

.sl-tipoarea__cta-title {
  font-family: var(--font-display);
  font-size: clamp(56px, 6.5vw, 96px);   /* JSX literal one-off CTA curve */
  letter-spacing: var(--ls-display);     /* -0.035em existing */
  line-height: 0.98;                     /* var(--lh-display) existing */
  font-weight: 400;
  color: var(--primary);
  margin: 0 0 var(--s-4) 0;              /* 24px existing */
}

/* .sl-tipoarea__cta-lede — VERIFY se esiste L5425, adjust se font-size diverso da 22px */
.sl-tipoarea__cta-lede {
  font-size: var(--fs-lede);             /* 22px existing */
  font-style: italic;
  font-family: var(--font-display);
  line-height: var(--lh-lede);           /* 1.5 existing */
}
```

(Se `.sl-tipoarea__cta-lede` esiste già con valori corretti, lascia stare e non duplicare.)

**Token alignment rule (§A KEEP CURRENT)**:
- `var(--ls-display)` per letter-spacing (esistente -0.035em)
- `var(--ls-h2)` per scenario title (esistente -0.01em)
- `var(--lh-lede)` per lede (esistente 1.5)
- `var(--lh-body)` per scenario desc (esistente 1.7)
- `var(--lh-heading)` per scenario title (esistente 1.15)
- `var(--lh-display)` per cta-title (esistente 0.98)
- `var(--fs-body)` per scenario desc (esistente 16px)
- `var(--fs-lede)` per cta-lede (esistente 22px)
- Literal per: `0.95` lh hero h1, `24px` lede, `28px` scenario title, `clamp(56,6.5vw,96)` cta-title — motivati come per-selector (phantom defer Wave 5 STEP 5)

### 2.B — Sync staging

```sh
rsync -avz wp-content/themes/saltelli/assets/css/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/css/
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

No PHP changes = no OPcache reload.

---

## PHASE 3 — SMOKE TEST (10-15 min)

### 3.A — Frontend curl smoke (3 URL term)

```sh
for SLUG in privati imprese contenzioso-amministrativo; do
  echo "=== /aree-di-pratica/$SLUG/ ==="
  curl -s "https://staging.studiolegalesaltelli.it/aree-di-pratica/$SLUG/" | grep -cE "sl-tipoarea__h1|sl-tipoarea__lede|sl-tipoarea__scenario"
  # atteso count >= 3
done
```

### 3.B — getComputedStyle spot-check

Su `/aree-di-pratica/privati/` (desktop 1440):
- `.sl-tipoarea__h1` font-size atteso ≈ `115px` (8vw), letter-spacing `-0.035em`, line-height `0.95`, font-style `italic`
- `.sl-tipoarea__lede` font-size `24px`, line-height `1.5`
- `.sl-tipoarea__scenario-title` font-size `28px`, letter-spacing `-0.01em`
- `.sl-tipoarea__scenario-desc` font-size `16px`, line-height `1.7`
- `.sl-tipoarea__cta-title` font-size atteso ≈ `93.6px` (6.5vw), letter-spacing `-0.035em`

### 3.C — Visual breakpoint (375/768/1024/1440)

- **375 mobile**: H1 ≈ 64px (floor), CTA h2 ≈ 56px (floor), lede 24px, scenario 28px
- **768 tablet**: H1 ≈ 61px (8vw), CTA h2 ≈ 50px
- **1024 desktop**: H1 ≈ 82px, CTA h2 ≈ 67px
- **1440 wide**: H1 ≈ 115px (8vw), CTA h2 ≈ 94px

### 3.D — Admin-side smoke (lesson Wave 4.7.fix.4)

WP Admin → Articoli → Tipo area → seleziona uno dei 3 term (es. "Per i privati"):
- Metabox SCF "group_tipo_area_term_v1" visibile
- 23 field per-term popolati (eyebrow, h1, lede, 3 scenari, quando, lista, casi, cta) o vuoti con fallback hardcoded nel template (decision Wave 5 STEP 3 coverage)
- Save → frontend invariato strutturalmente

---

## PHASE 4 — COMMIT + PUSH

```sh
git add -A
git diff --cached --stat

git commit -m "feat(design-handoff): Wave P6 taxonomy-tipo-area — 5 nuove CSS rule per hub editorial

Wave 6/12 sequenza Design Handoff. Template taxonomy-tipo-area.php (Wave 5 STEP 3 coverage refactor) ora con styling completo per 3 term (privati 992, imprese 993, contenzioso-amministrativo 994).

5 nuove CSS rule create (sections.css scope /* === design-handoff taxonomy-tipo-area P6 === */):

1. .sl-tipoarea__h1: clamp(64,8vw,132) italic + var(--ls-display) + lh 0.95 [phantom doc candidate --lh-display-tight deferred]
2. .sl-tipoarea__lede: 24px italic + var(--lh-lede) [phantom candidate --fs-lede-lg deferred]
3. .sl-tipoarea__scenario-title: 28px + var(--ls-h2) + var(--lh-heading) [phantom candidate --fs-h2-floor deferred]
4. .sl-tipoarea__scenario-desc: var(--fs-body) + var(--lh-body)
5. .sl-tipoarea__cta-title: clamp(56,6.5vw,96) + var(--ls-display) + var(--lh-display) [one-off CTA curve]
+ VERIFY .sl-tipoarea__cta-lede usa var(--fs-lede)=22px

Token alignment §A KEEP CURRENT:
- Token esistenti usati: --ls-display, --ls-h2, --lh-display, --lh-lede, --lh-heading, --lh-body, --fs-body, --fs-lede
- Literal per-selector motivati (phantom defer Wave 5 STEP 5): 0.95 lh, 24px lede, 28px scenario, clamp(56,6.5vw,96) cta

SCF: 🟢 COMPLIANT. 23 field group_tipo_area_term_v1 invariati. Plus tab Taxonomy Tipo Area Theme Options invariata. Zero additive.

Smoke test:
- Frontend curl 3 term: markup .sl-tipoarea__h1 + lede + scenario presente
- getComputedStyle desktop 1440: H1 ≈115px italic ls -0.035em lh 0.95, CTA h2 ≈94px
- Admin: metabox 23 field per-term intatti per 3 term

No version bump (chore frontend, no schema/data change).
Branch: feat/design-handoff-taxonomy-tipo-area · 1 file changed · ~25 righe CSS"

git push origin feat/design-handoff-taxonomy-tipo-area
```

---

## OUTPUT FINALE in chat

- Tabella drift PHASE 1 (5 CREATE + 1 VERIFY)
- 5 fix applicati (1 file changed, ~25 righe CSS)
- Smoke test risultati (3 term URL + getComputedStyle + 4 breakpoint + admin)
- SHA commit pushato
- ETA proposto P7 chi-siamo (refactor hub — wave più impegnativa)

---

## HARD RULES

1. **5 CREATE + 1 VERIFY scope limitato**: NIENTE altri changes a `.sl-tipoarea*` (NO drift residui da phantom doc, scope Wave 5 STEP 5).
2. **Token alignment §A**: KEEP CURRENT, mai toccare `:root`.
3. **SCF schema immutabile**: 23 field per-term + Theme Options tab invariati. NO additive.
4. **PHP taxonomy-tipo-area.php**: INVARIATO (no touch, Wave 5 STEP 3 coverage refactor è già allineato a SCF reads).
5. **Admin-side smoke test obbligatorio** (lesson Wave 4.7.fix.4): verifica 23 field per-term intatti su 3 term.
6. **One-writer-at-a-time**: UNICA sessione Code attiva.

---

## DECISIONE AUTONOMA AUTORIZZATA

- Scope marker CSS wording: `/* === design-handoff taxonomy-tipo-area P6 === */`
- Se `.sl-tipoarea__cta-lede` esiste e font-size già 22px → no duplicate rule
- Eventuale phantom collateral catch: segnala, NON fixare (scope Wave 5 STEP 5)
- Se trovi differenze inattese tra i 3 term (es. count aree filtrate per cluster): documenta, non fixare se non strettamente CSS

---

## TONO

Direct, concrete, zero filler. Stile commit usato dal progetto.

---

*Wave P6/12 sequenza Design Handoff. Prossima: P7 chi-siamo (refactor hub /chi-siamo/ con design lo-studio editorial, ESPANSIONE group_chi_siamo_v1 additive, 2-3h scope più alto). Pattern lean = 1 wave alla volta su main.*
