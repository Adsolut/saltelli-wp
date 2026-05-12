# PROMPT AGENT — Design Handoff Wave P4 · single-competenza-tier1 verify & drift cleanup

> **Scope**: allineare `single-competenza.php` (branch tier-1) e `.sl-tier1*` di sections.css al design source `design-handoff/single-competenza-tier1/index.jsx`. 5 drift identificati nel pre-flight, ETA 1-1.5h.
>
> **Branch**: `feat/design-handoff-single-competenza-tier1`
> **Stima**: 1-1.5h (severity 🟡 MEDIUM)
> **Modalità**: lean, no version bump (chore frontend, no schema/data change)
> **Sessione**: una sola Claude Code, no parallelismo.

---

## CONTESTO

Wave 4/12 sequenza Design Handoff. P1 chrome + P2 footer + P3 home dovrebbero essere già mergeate.

Pagina tier-1 = template branch condizionale di `single-competenza.php` quando `is_tier_1 == true`. Riguarda i 3 cluster deep alto-traffico SEO: **Tributario · Lavoro · Famiglia LGBTQ+**.

**Decisioni orchestratore acquisite** (NON rinegoziare):
1. SoT design tokens = `tokens.css` corrente vince (KEEP CURRENT)
2. SCF data contract immutabile: 16 field in `group_competenza_v1` esistenti → 🟢 COMPLIANT (0 additive needed)
3. **H1 `clamp(72px, 10vw, 160px)`**: literal per-selector (no nuovo token, è one-off tier-1)
4. **H1 letter-spacing `-0.035em`**: usa `var(--ls-display)` (= -0.035em già in tokens.css)
5. **Answer capsule `margin-left: 20%`**: applica solo desktop (`@media (min-width: 1024px)`), mobile resta full-width
6. **JSX body editorial hardcoded** è proof-of-concept; per WP usa `body_extended` SCF field popolato (no copia HTML hardcoded JSX nel template)

**Pre-flight orchestratore già fatto** (Explore agent output):

| Element | JSX value | WP attuale | Fix |
|---|---|---|---|
| H1 font-size | `clamp(72px, 10vw, 160px)` | `clamp(48px, 7vw, 132px)` | UPDATE literal |
| H1 letter-spacing | `-0.035em` | `var(--ls-h1)` (-0.02em) | UPDATE → `var(--ls-display)` |
| H1 line-height | `0.95` | `1.1` | UPDATE literal (phantom doc candidate `--lh-display-tight`, mantieni literal per-selector ora) |
| Answer capsule margin-left | `20%` (JSX inline) | mancante | ADD `@media ≥1024 { margin-left: 20% }` |
| Avvocato photo aspect-ratio | `1/1` | mancante CSS (only width 80px) | ADD `aspect-ratio: 1/1` |

---

## ⚠️ HARD INVARIANT — SCF DATA CONTRACT PRESERVATION

`group_competenza_v1.json` 16 field invariati: `is_tier_1`, `tier_label`, `subtitle`, `answer_capsule`, `body_extended`, `lead_attorneys`, `casi_rappresentativi`, `faq`, `articoli_correlati`, `cta_label`, `cta_url`, `cta_top_label`, `cta_top_url`, `cta_middle_label`, `cta_middle_url`, `related_competenze`.

**NON ACCETTABILE**: refactor field, rinominare, rimuovere, cambiare CPT registration `competenza`, cambiare location rules.

---

## PRE-FLIGHT (5 min)

1. Leggi:
   - `CLAUDE.md` (Hard constraints, Design system, "Design → Code handoff rule golden", Lessons learned)
   - `.claude/knowledge/audits/design-handoff/RECOMMENDATION.md` (§A KEEP CURRENT, §B P4 prioritization)
   - `.claude/knowledge/audits/wave5-step4-sections-cleanup/02-phantom-values-remaining.md` (phantom §3a/§4/§5 cross-ref)
   - **JSX source**: `design-handoff/single-competenza-tier1/index.jsx`
   - **WP target**:
     - `wp-content/themes/saltelli/single-competenza.php` (branch tier-1 conditional)
     - Blocchi `.sl-tier1*` + `.sl-competenza*` in `wp-content/themes/saltelli/assets/css/sections.css`
   - `wp-content/themes/saltelli/acf-json/group_competenza_v1.json` (verifica 16 field invariati)

2. Verifica stato:
   ```sh
   git fetch origin
   git status
   git log --oneline -3   # atteso HEAD post-P3 home merge
   git checkout -b feat/design-handoff-single-competenza-tier1
   ```

3. Conferma in chat: branch creato + JSX + WP target letti + prosegui.

---

## PHASE 1 — VERIFY (10 min, focused su 5 drift noti)

### 1.A — Hero H1 tier-1 (3 drift typography)

Localizza `.sl-tier1__h1` in sections.css. Atteso 3 proprietà drift:

| Property | Current value | Target value | Source |
|---|---|---|---|
| `font-size` | `clamp(48px, 7vw, 132px)` | `clamp(72px, 10vw, 160px)` | JSX literal |
| `letter-spacing` | `var(--ls-h1)` | `var(--ls-display)` | JSX `-0.035em` = `--ls-display` esistente |
| `line-height` | `1.1` | `0.95` | JSX literal (phantom doc candidate, mantieni literal per ora) |

### 1.B — Answer capsule (1 drift layout)

Localizza `.sl-tier1__capsule` in sections.css. Verifica `margin-left` non presente. Aggiunta target:

```css
@media (min-width: 1024px) {
  .sl-tier1__capsule {
    margin-left: 20%;
  }
}
```

(Decisione orchestratore: desktop-only, mobile full-width).

### 1.C — Avvocato lead photo (1 drift property)

Localizza `.sl-tier1__lawyer-photo` o equivalente. Aggiungi:

```css
.sl-tier1__lawyer-photo {
  aspect-ratio: 1 / 1;  /* JSX explicit */
}
```

### 1.D — SCF reads spot-check (atteso 100% match)

Lettura `single-competenza.php` branch tier-1 — verifica chiamate `saltelli_field()` / `get_field()`:
- `is_tier_1` (boolean check), `subtitle`, `answer_capsule`, `body_extended`, `lead_attorneys` (post_object), `casi_rappresentativi` (post_object), `faq` (post_object), `articoli_correlati` (post_object), `cta_*`

Atteso 16/16 ✓. Zero additive. NO toccare PHP per SCF.

### 1.E — Tabella drift consolidata in chat

Posta in chat la tabella drift completa (5 righe) prima di toccare codice. Atteso 5/5 confermati dal pre-flight.

---

## PHASE 2 — IMPLEMENT (30-45 min)

### 2.A — CSS fix in sections.css

Aggiungi/modifica nello scope blocco `.sl-tier1*` esistente con scope marker `/* === design-handoff single-competenza-tier1 P4 === */`:

```css
/* === design-handoff single-competenza-tier1 P4 === */

.sl-tier1__h1 {
  font-size: clamp(72px, 10vw, 160px);   /* JSX literal — tier-1 display-band */
  letter-spacing: var(--ls-display);     /* -0.035em — tier-1 H1 è display-sized */
  line-height: 0.95;                     /* JSX literal — candidate --lh-display-tight (phantom doc §5), deferred */
}

.sl-tier1__lawyer-photo {
  aspect-ratio: 1 / 1;
}

@media (min-width: 1024px) {
  .sl-tier1__capsule {
    margin-left: 20%;
  }
}
```

**Token alignment rule (§A KEEP CURRENT)**:
- `font-size` literal: motivato come per-selector (tier-1 display-band, one-off, non riusabile across pages)
- `letter-spacing` usa token esistente `var(--ls-display)`
- `line-height` literal motivato (phantom doc §5 ha candidate `--lh-display-tight` ma è deferred Wave 5 STEP 5)

### 2.B — Sync staging

```sh
rsync -avz wp-content/themes/saltelli/assets/css/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/css/
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

(No PHP changes attesi = no OPcache reload. Se per qualsiasi motivo tocchi `single-competenza.php`, allora SI: `sudo systemctl reload php8.2-fpm`.)

---

## PHASE 3 — SMOKE TEST (10-15 min)

### 3.A — Frontend curl smoke (3 URL tier-1)

```sh
for URL in /aree-di-pratica/privati/diritto-tributario/ \
           /aree-di-pratica/privati/diritto-del-lavoro/ \
           /aree-di-pratica/privati/diritto-di-famiglia-lgbtq/; do
  echo "=== $URL ==="
  curl -s "https://staging.studiolegalesaltelli.it$URL" | grep -cE "sl-tier1__h1|sl-tier1__capsule|sl-tier1__lawyer-photo"
  # atteso count >= 3 (markup tier-1 presente)
done
```

### 3.B — getComputedStyle spot-check (Playwright/dev tools)

Su `/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/` (la pagina su cui Duccio aveva fatto polish in precedenza):

- `.sl-tier1__h1` computed:
  - `font-size`: atteso compreso tra `72px` (mobile floor) e `160px` (desktop max), in viewport 1440 atteso ≈ `144px` (10vw)
  - `letter-spacing`: atteso `-0.035em` (= computed in px ≈ -5px at 144px)
  - `line-height`: atteso `0.95`
- `.sl-tier1__capsule` (≥1024px viewport): `margin-left` atteso `20%`
- `.sl-tier1__lawyer-photo`: `aspect-ratio` atteso `1 / 1`

### 3.C — Visual breakpoint test (375/768/1024/1440)

- **375 mobile**: H1 ≈ 72px (floor), capsule no indent
- **768 tablet**: H1 ≈ 76.8px (10vw), capsule no indent
- **1024 desktop**: H1 ≈ 102.4px, capsule indent 20%
- **1440 wide**: H1 ≈ 144px, capsule indent 20%

### 3.D — Admin-side smoke (lesson Wave 4.7.fix.4)

WP Admin → Competenze → seleziona una tier-1 (es. "Diritto di famiglia LGBTQ+") → Modifica:
- Metabox SCF "Competenza — Area di pratica (v1)" visibile
- 16 field popolati intatti (`is_tier_1=true`, `subtitle`, `answer_capsule`, `body_extended`, `lead_attorneys`, `casi_rappresentativi`, `faq`, `articoli_correlati`, `cta_*`)
- Save senza modifiche → frontend invariato strutturalmente

---

## PHASE 4 — COMMIT + PUSH

```sh
git add -A
git diff --cached --stat

git commit -m "feat(design-handoff): Wave P4 single-competenza-tier1 — H1 display-band + answer capsule indent + photo aspect-ratio

Wave 4/12 sequenza Design Handoff. Template tier-1 (Tributario / Lavoro / Famiglia LGBTQ+) allineato al JSX design source design-handoff/single-competenza-tier1/index.jsx.

5 drift fixati (sections.css scope /* === design-handoff single-competenza-tier1 P4 === */):

1. .sl-tier1__h1 font-size: clamp(48,7vw,132) → clamp(72,10vw,160) [JSX literal, tier-1 display-band, phantom doc §3a]
2. .sl-tier1__h1 letter-spacing: var(--ls-h1) → var(--ls-display) [-0.035em existing token, H1 tier-1 è display-sized]
3. .sl-tier1__h1 line-height: 1.1 → 0.95 [JSX literal, phantom doc §5 candidate --lh-display-tight deferred Wave 5 STEP 5]
4. .sl-tier1__capsule margin-left desktop: + @media ≥1024 → 20% [JSX inline, mobile resta full-width]
5. .sl-tier1__lawyer-photo aspect-ratio: + 1/1 [JSX explicit]

Token alignment §A KEEP CURRENT confermato:
- font-size + line-height: literal per-selector (motivato — tier-1 display-band, non riusabile across pages)
- letter-spacing: token esistente var(--ls-display) (-0.035em)

SCF: 🟢 COMPLIANT. 16 field group_competenza_v1 invariati. Zero additive.

Smoke test:
- Frontend curl 3 URL tier-1: markup .sl-tier1__h1 + .sl-tier1__capsule + .sl-tier1__lawyer-photo presente
- getComputedStyle desktop 1440: H1 font-size ≈144px, letter-spacing -0.035em, line-height 0.95
- Visual 4 breakpoint: capsule indent solo ≥1024px
- Admin: metabox SCF 16 field intatti

No version bump (chore frontend, no schema/data change).
Branch: feat/design-handoff-single-competenza-tier1 · 1 file changed · ~12 righe CSS"

git push origin feat/design-handoff-single-competenza-tier1
```

---

## OUTPUT FINALE in chat

- Tabella drift PHASE 1 (5 righe confermate)
- 5 fix applicati (1 file changed, ~12 righe CSS)
- Smoke test risultati (3 URL tier-1 + getComputedStyle + 4 breakpoint + admin)
- SHA commit pushato
- ETA proposto P5 single-avvocato

---

## HARD RULES

1. **5 fix scope limitato**: NIENTE altri changes a `.sl-tier1*` o `.sl-competenza*` (NO drift residui da phantom doc, scope Wave 5 STEP 5).
2. **Token alignment §A**: KEEP CURRENT, mai toccare `:root`.
3. **SCF schema immutabile**: 16 field invariati, NO additive.
4. **PHP single-competenza.php INVARIATO**: nessun touch al template, solo CSS.
5. **OPcache reload SOLO se per qualsiasi motivo tocchi single-competenza.php** (non atteso).
6. **Admin-side smoke test obbligatorio** (lesson Wave 4.7.fix.4): verifica metabox SCF 16 field intatti.
7. **One-writer-at-a-time**: UNICA sessione Code attiva.

---

## DECISIONE AUTONOMA AUTORIZZATA

- Scope marker CSS wording: `/* === design-handoff single-competenza-tier1 P4 === */`
- Spot-check pages tier-1: 3 URL (Tributario, Lavoro, Famiglia LGBTQ+) — se nomi slug differiscono, adatta a quelli del DB
- Eventuale phantom collateral catch in `.sl-tier1*` (oltre i 3 typography già noti): segnala in chat, NON fixare (scope Wave 5 STEP 5)

---

## TONO

Direct, concrete, zero filler. Stile commit usato dal progetto.

---

*Wave P4/12 sequenza Design Handoff. Prossima: P5 single-avvocato (verify + drift ritratto 1:1 vs 3:4 + sticky CTA layout). Pattern lean = 1 wave alla volta su main.*
