---
title: Wave 5 Design Realign — Fix Applied
date: 2026-05-11
branch: feat/wave5-design-realign (worktree-isolated)
baseline: /tmp/drift-audit-baseline.md (git blob 3dd00417aff4ca646a94cd6872f0d0dac4bc42e4, 410 righe)
scope: lean — rebuild tokens.css da docs/DESIGN.md + top-15 hardcoded violations. NO version bump, NO EDITOR-HANDOFF update.
---

# Wave 5 Design Realign — Fix Applied

Riferimento baseline: il drift audit 3-layer (`/tmp/drift-audit-baseline.md`, blob permanente `3dd0041`).
Source of truth: `docs/DESIGN.md` (front-matter Google Stitch YAML + body specs §Typography "letter-spacing optical" / "line-height optical" / §spacing / §rounded).

Files toccati (3): `assets/css/tokens.css` (rebuild integrale), `assets/css/sections.css` (7 punti), `assets/css/components.css` (8 punti). `assets/css/base.css` **non toccato** (vedi §"Note autonome" punto 3).

---

## LAYER 1 — tokens.css rebuilt from DESIGN.md

`tokens.css` riscritto integralmente. Header del file ora dichiara provenienza:
`Generated from docs/DESIGN.md (dated 2026-05-08) by Wave 5 Design Realign — 2026-05-11. DO NOT EDIT MANUALLY.`

### 1.1 — 12 token value re-aligned (DESIGN.md as SoT)

| Token | era (tokens.css) | ora | Fonte DESIGN.md |
|---|---|---|---|
| `--fs-display` | `clamp(48px, 8vw, 120px)` | `clamp(80px, 9vw, 132px)` (via floor/vw/max sub-tokens) | `typography.display.fontSize` |
| `--fs-display-floor` | — (era inline 48px) | `80px` | |
| `--fs-display-vw` | — (era inline 8vw) | `9vw` | |
| `--fs-display-max` | — (era inline 120px) | `132px` | |
| `--fs-h1` | `clamp(36px, 5vw, 64px)` | `clamp(48px, 6vw, 96px)` (via floor/vw/max sub-tokens) | `typography.h1.fontSize` |
| `--fs-h1-floor` | — (era inline 36px) | `48px` | |
| `--fs-h1-vw` | — (era inline 5vw) | `6vw` | |
| `--fs-h1-max` | — (era inline 64px) | `96px` | |
| `--fs-h3` | `clamp(20px, 2vw, 28px)` | `clamp(22px, 2.2vw, 32px)` (via floor/max sub-tokens) | `typography.h3.fontSize` (vw 2→2.2 per prompt directive) |
| `--fs-h3-floor` | — (era inline 20px) | `22px` | |
| `--fs-h3-max` | — (era inline 28px) | `32px` | |
| `--fs-body` | `clamp(16px, 1.1vw, 18px)` | `16px` (fisso, no clamp) | `typography.body.fontSize` |
| `--lh-display` | `1.05` | `0.98` | §Typography line-height optical "Display ≥80px" |
| `--lh-body` | `1.65` | `1.7` | §Typography line-height optical "Body DM Sans — legal content density requires generous leading" |
| `--ls-display` | `-0.02em` | `-0.035em` | §Typography letter-spacing optical "≥80px display" |

`--fs-h2` (`clamp(28px, 3.5vw, 44px)`) e `--ls-mono` (`0.08em`) e `--lh-heading` (`1.15`) erano già allineati — invariati.

### 1.2 — Token mancanti aggiunti (21 nuovi)

| Token nuovo | Valore | Uso previsto |
|---|---|---|
| `--fs-caption` | `11px` | primitiva metadata mono (eyebrow, breadcrumb, "01 / 19", label) — distinta da `--fs-micro: 12px` (decorativo) |
| `--fs-lede` | `22px` | lede italico Playfair sotto h1 (desktop) |
| `--fs-body-marketing` | `18px` | body copy marketing (NON legal prose) |
| `--lh-h1` | `1.05` | line-height H1 |
| `--lh-h3` | `1.2` | line-height H3 |
| `--lh-lede` | `1.5` | line-height lede italico |
| `--lh-mono` | `1.4` | line-height caption mono |
| `--ls-h1` | `-0.02em` | letter-spacing H1 (48-80px) |
| `--ls-h2` | `-0.01em` | letter-spacing H2 (28-44px) |
| `--ls-h3` | `-0.005em` | letter-spacing H3 (22-32px) |
| `--radius-none` | `0` | DESIGN.md §rounded.none |
| `--radius-xs` | `2px` | DESIGN.md §rounded.xs |
| `--radius-sm` | `4px` | DESIGN.md §rounded.sm |
| + 8 sub-token (`--fs-display-floor/vw/max`, `--fs-h1-floor/vw/max`, `--fs-h3-floor/max`) | — | curva responsive editabile in un punto |

`--fs-micro: 12px` **mantenuto** come token separato per usage decorativo (per direttiva prompt). `--fs-small: 14px` mantenuto (skip-link, accordion icon).

**Cross-check:** nessun token rimosso (verificato: `comm -23` orig vs new = vuoto). Tutti i `var(--token)` referenziati nel theme CSS sono ancora definiti (unica eccezione `--ease-quart-out`, dangling reference pre-esistente con fallback inline, fuori scope).

### 1.3 — Heading reset (`.sl-root h1..h4`) — letter-spacing per-hierarchy

Vedi §"Note autonome" punto 1. La reset rule passava `letter-spacing: var(--ls-display)` a *tutti* gli heading; ora:
- gruppo `.sl-root h1, h2, h3, h4` → default `var(--ls-h2)` (-0.01em)
- `.sl-root h1` → `var(--ls-display)` (-0.035em — l'h1 principale di pagina su questo sito è tipicamente display-sized, es. hero)
- `.sl-root h3, h4` → `var(--ls-h3)` (-0.005em)

Effetto: h1 -0.02→-0.035em (corretto per hero), h2 -0.02→-0.01em (corretto), h3/h4 -0.02→-0.005em (corretto). Nessuna regola component "smascherata" (l'override (0,1,1) di `.sl-root hN` rimane più specifico dei component class 0,1,0 — comportamento pre-esistente preservato).

---

## LAYER 2 — Spacing scale cleanup (parziale)

- `--s-1..s-10` (4/8/16/24/32/48/64/96/128/192) confermata **canonica** — commento aggiornato "use --s-* for all new CSS".
- `--space-*` e `--sl-space-*` **NON rimosse** (~590 occorrenze CSS le usano). Aggiunto blocco di deprecation comment esplicito: i due valori fantasma marcati inline — `--space-3: 12px` `/* ⚠ PHANTOM — not in DESIGN.md */`, `--sl-space-7: 80px` `/* ⚠ PHANTOM — not in DESIGN.md */`. Aggiunto warning su naming ambiguo `--space-12 = 48px ≠ 12px` e indici sfasati `--sl-space-1 = 8px ≠ --s-1 = 4px`.
- `--space-hero-top/bottom` (clamp con cap 120px/80px fantasma) marcati DEPRECATED ma mantenuti (`.sl-page__hero` li usa).
- `--radius-none/xs/sm` aggiunti (DESIGN.md §rounded). `border-radius: 2px` nella focus rule di tokens.css → `var(--radius-xs)` (stesso valore, ora token-driven).

---

## LAYER 3 — Top-15 hardcoded violations fixed

Le restanti ~575 occorrenze typography/spacing in `sections.css` **non toccate** (wave dedicata post-Elena-OK).

| # | Selettore | File:line | Prop | Before | After | Note |
|---|---|---|---|---|---|---|
| 1 | `.sl-hero__headline` | sections.css:292 (base), :346 (≥1024), :2083 (≤767) | `font-size` | `64px` / `clamp(80px,9vw,132px)` / `clamp(56px,14vw,80px)` | `var(--fs-display)` (= clamp 80,9vw,132) su tutti i breakpoint | **fix drift principale**: mobile narrow passa da ~56-64px a min 80px |
| 2 | `.sl-hero__headline` | sections.css:294 (base), :347 (≥1024), :2085 (≤767) | `letter-spacing` | `-0.03em` / `-0.035em` / `-0.02em` | `var(--ls-display)` (= -0.035em) su tutti i breakpoint | elimina valore ibrido fantasma -0.03em. NB: già di fatto -0.035em via `.sl-root h1` post-Layer-1 |
| 3 | `.sl-hero__headline` | sections.css:293 (base), :2084 (≤767) | `line-height` | `0.98` / `1.05` | `var(--lh-display)` (= 0.98) su tutti i breakpoint | mobile passa da 1.05 a 0.98 (display tightness) |
| 4 | `.sl-hero__eyebrow` | sections.css:290 | `font-size` | `10px` | `var(--fs-caption)` (= 11px) | caption primitive uniformata |
| 5 | `.sl-hero__colophon-label` | sections.css:333 (base, dead), :3464 (live override) | `font-size` | `9px` (base) + `10px` (override T3) | `var(--fs-caption)` (= 11px) entrambi | il 9px era già morto (override 10px a riga 3464); ora 11px ovunque |
| 6 | `.sl-hero__colophon-body` | sections.css:334 | `font-size` | `12px` | `var(--fs-caption)` (= 11px) | (riga 370 desktop override 13px lasciata — selettore non renderizzato in front-page) |
| 7 | `.sl-mono` (utility globale) | components.css:12 | `font-size` | `var(--fs-micro)` (= 12px) | `var(--fs-caption)` (= 11px) | uniforma la primitiva caption a 11px |
| 8 | `.sl-footer__copy.sl-mono` | components.css:33 | `letter-spacing` | `0.04em` | `var(--ls-mono)` (= 0.08em) | per direttiva prompt — NB: widening su stringa lunga lowercase (© 2026…), valutare in visual review |
| 9 | `.sl-page__breadcrumb` | sections.css:64 (dead 0.06em), :3012 (live 0.08em) | `letter-spacing` | `0.06em` (riga 64, morta) + `0.08em` (riga 3012, live) | `var(--ls-mono)` (= 0.08em) entrambe | nessun cambio visivo: il breadcrumb renderizzava già 0.08em via override riga 3009 (l'audit baseline aveva il valore sbagliato — diceva 0.06em); ora token-driven |
| 10 | `.sl-area__num` | components.css:237 | `font-size` | `12px` | `var(--fs-caption)` (= 11px) | caption primitive uniformata |
| 11 | `.sl-btn` | components.css:43 | `font-size` | `16px` | `var(--fs-body)` (= 16px) | tokenizzato (stesso valore) |
| 12 | `.sl-btn` | components.css:51 (rimossa) | `letter-spacing` | `0.01em` (fantasma) | *rimossa* → `normal` | DESIGN.md: body text letter-spacing = normal |
| 13 | `.sl-faq__answer, details.sl-acc > div, .sl-acc__panel` | components.css:429 | `line-height` | `1.7` | `var(--lh-body)` (= 1.7) | tokenizzato (stesso valore, ora consistente con `--lh-body`) |
| 14 | `.sl-area` | components.css:206 | `padding` | `28px 0` (fantasma) | `var(--s-4) 0` (= 24px 0) | **decisione**: scelto 24px per consistenza con `.sl-acc__btn`/`.sl-faq__question` che usano `padding: 24px 0`. Le righe della areas list diventano ~8px più compatte |
| 15 | `.sl-area` | components.css:205 | `gap` | `32px` | `var(--s-5)` (= 32px) | tokenizzato (stesso valore) |

---

## Note autonome (decisioni prese, da segnalare a Duccio)

1. **Heading reset split (tokens.css).** Il prompt P1.C chiedeva "4 valori letter-spacing invece di 1". I 4 token `--ls-display/-h1/-h2/-h3` sono stati definiti **e** wirati nella reset rule `.sl-root h1..h4` (h1→display, gruppo→h2, h3/h4→h3). Motivo: senza il wiring, cambiare `--ls-display` a -0.035em avrebbe reso h2/h3 *più* tight del già-sbagliato -0.02em. Lo split è strict-improvement (ogni heading va verso il valore DESIGN.md o resta neutro) e zero-rischio (preserva l'override (0,1,1) esistente — nessun valore component smascherato). Considerato parte di "rebuild tokens.css from DESIGN.md".

2. **Hero headline letter-spacing & specificity.** `.sl-hero__headline` è un `<h1>` (front-page.php:79) dentro `.sl-root`. `.sl-root h1` (0,1,1) batte `.sl-hero__headline` (0,1,0): il letter-spacing del hero viene **sempre** da `.sl-root h1` = `var(--ls-display)`. Quindi item 2 (cambiare `.sl-hero__headline` letter-spacing → `var(--ls-display)`) è di fatto cosmetico — il valore effettivo del hero è già `var(--ls-display)` = -0.035em post-Layer-1. L'audit baseline credeva il hero fosse a -0.03em (mobile), ma quel valore era morto. **Side effect noto:** `.sl-page__title` (h1 H1-sized su `/contatti/` ecc.) eredita -0.035em da `.sl-root h1` invece dell'ideale `var(--ls-h1)` = -0.02em. Tradeoff accettato: il prompt prioritizza esplicitamente il hero ("letter-spacing più tight"). Fix proprio per-component → wave deferita.

3. **base.css NON toccato.** Il prompt dice "Solo rebuild tokens + cleanup top-15". `base.css` ha `h1..h6 { letter-spacing: var(--ls-display) }` che ora vale -0.035em (era -0.02em) → heading *fuori* `.sl-root` (wp-admin-bar, plugin output, e h5/h6 dentro `.sl-root` non coperti dalla reset di tokens.css) ricevono -0.035em (troppo tight per h2-h6). Impatto visitor-facing trascurabile (il contenuto reale è dentro `.sl-root`, dove tokens.css `.sl-root h1..h4` vince). Wiring per-element di `base.css h1..h6` → wave deferita.

4. **`--fs-body: 16px` fisso.** Su viewport ≥~1455px il body passava fino a 18px (clamp); ora 16px fisso ovunque (DESIGN.md §typography.body). Effetto visibile su schermi wide: body marketing leggermente più piccolo. Mitigazione futura: wirare `var(--fs-body-marketing)` (= 18px, token nuovo) sulle pagine marketing nella wave deferita.

5. **Item 14 — `.sl-area` padding 28px → 24px** (scelto `var(--s-4)`, non `var(--s-5)` = 32px): rounding *down* (minor visual delta) + consistenza con gli altri row interattivi (accordion/FAQ a 24px).

---

## Known-remaining (deliberatamente NON toccato — wave deferita)

- ~575 occorrenze hardcoded `font-size`/`letter-spacing`/`line-height` in `sections.css`.
- `components.css`: `.sl-acc__inner { line-height: 1.65 }` (vecchio valore `--lh-body`, ora stale vs 1.7), `.sl-area__title { clamp(28px,3vw,40px) }` (scala parallela), `.sl-acc__btn` / `.sl-faq__question` `clamp(18px,1.5vw,22px)` (scala parallela, duplicati tra loro), `.sl-acc__icon { 14px }`, `.sl-faq__question::after { font-weight: 300 }` (300 non in DESIGN.md / variable font può non averlo), `.sl-area__meta { 11px }` (valore ok ma bypassa token), vari `letter-spacing: 0.08em` hardcoded (= `--ls-mono`).
- `base.css` heading letter-spacing per-element split.
- 10 critical CSS in `assets/css/critical/*.css`: contengono hex `color:#F...` hardcoded — staleness risk se i color token cambiano (qui non cambiati). Build pipeline da `tokens.css` → wave deferita.
- `--ease-quart-out` dangling reference (mai definito in `:root`, usato con fallback inline).
- `--space-*` / `--sl-space-*` rimozione vera (richiede sostituire ~590 usi prima).

---

*Prodotto in worktree `feat/wave5-design-realign` (`/tmp/saltelli-design-realign`), parallelo a `audit/wave5-pages-completeness` — file disgiunti, zero conflitti.*
