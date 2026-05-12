---
title: Design Handoff — PHASE 3 · Design Token Reconciliation
date: 2026-05-12
author: Claude Code (branch audit/design-handoff-strategy)
scope: design-handoff/_reference/tokens-design-bundle.css + saltelli-design-bundle.css vs wp-content/themes/saltelli/assets/css/tokens.css (post Wave 5 STEP 2) + components.css
companion: 01-jsx-inventory.md, 02-jsx-to-wp-mapping.md, RECOMMENDATION.md, ../wave5-step4-sections-cleanup/02-phantom-values-remaining.md
---

# PHASE 3 — Design Token Reconciliation

## Verdetto in una riga

**`tokens-design-bundle.css` (298 righe) è un artefatto PRELIMINARE/OBSOLETO** — riflette il design
system *prima* di Wave 5 STEP 2 (il rebuild di `tokens.css` da `docs/DESIGN.md` come SoT). Gli stessi
JSX del bundle **non usano** i token piccoli del bundle CSS: hardcodano inline i `clamp()` GRANDI
allineati al sistema corrente. → **Decisione: KEEP CURRENT `tokens.css`. Il bundle CSS è
informativo. `docs/DESIGN.md` resta SoT, nessun update.** Dettaglio + motivazioni sotto.

---

## 1 · Token-by-token: Bundle Design vs Current (post Wave 5 STEP 2)

| Token (bundle) | Valore **Bundle Design** | Token (current) | Valore **Current** (Wave 5 STEP 2 = DESIGN.md SoT) | Drift | Decisione | Note |
|---|---|---|---|---|---|---|
| `--background` | `#FAFAF8` | `--background` | `#FAFAF8` | 0 | — | identico |
| `--surface` | `#F2F0EA` | `--surface` | `#F2F0EA` | 0 | — | identico |
| `--primary` | `#1B2B4B` | `--primary` | `#1B2B4B` | 0 | — | identico |
| `--accent` | `#B8860B` | `--accent` | `#B8860B` | 0 | — | identico |
| `--text` | `#2D2D2D` | `--text` | `#2D2D2D` | 0 | — | identico |
| `--text-muted` | `#6B6B6B` | `--text-muted` | `#6B6B6B` | 0 | — | identico |
| `--border` | `#E5E0D5` | `--border` | `#E5E0D5` | 0 | — | identico — **palette 100% allineata** |
| `--font-display` | `"Playfair Display", "Cormorant Garamond", Georgia, serif` | `--font-display` | idem | 0 | — | identico |
| `--font-body` | `"DM Sans", "Helvetica Neue", Helvetica, Arial, sans-serif` | `--font-body` | `"DM Sans", "Satoshi", -apple-system, sans-serif` | fallback chain diversa | KEEP CURRENT | nessun impatto computed (DM Sans c'è in entrambi) |
| `--font-mono` | `"JetBrains Mono", ui-monospace, "SF Mono", Menlo, monospace` | `--font-mono` | `"JetBrains Mono", ui-monospace, "SF Mono", monospace` | minore | KEEP CURRENT | nessun impatto |
| `--fs-display` | **`clamp(48px, 8vw, 120px)`** | `--fs-display` | **`clamp(80px, 9vw, 132px)`** | **floor +67%, max +10%** | **KEEP CURRENT** | DESIGN.md §typography.display = `clamp(80px,9vw,132px)`. I JSX hero hardcodano `clamp(80px,9vw,132px)` (home/index.jsx:95) → confermano current. |
| `--fs-h1` | **`clamp(36px, 5vw, 64px)`** | `--fs-h1` | **`clamp(48px, 6vw, 96px)`** | **floor +33%, max +50%** | **KEEP CURRENT** | DESIGN.md §typography.h1 = `clamp(48px,6vw,96px)`. JSX `archive-casi` h1 = `clamp(64px,8vw,132px)`, `contatti`/`blog`/`404` h1 = `clamp(72px,9vw,140px)` → tutti più vicini a current che al bundle. |
| `--fs-h2` | `clamp(28px, 3.5vw, 44px)` | `--fs-h2` | `clamp(28px, 3.5vw, 44px)` | **0** | — | **identico** — l'unico clamp tipografico allineato byte-per-byte |
| `--fs-h3` | **`clamp(20px, 2vw, 28px)`** | `--fs-h3` | **`clamp(22px, 2.2vw, 32px)`** | floor +10%, vw +10%, max +14% | **KEEP CURRENT** | DESIGN.md §typography.h3 = `clamp(22px,2vw,32px)` (tokens.css usa `2.2vw` per la curva, ma floor/max = DESIGN.md). Il bundle è la versione vecchia. |
| `--fs-body` | **`clamp(16px, 1.1vw, 18px)`** (fluido) | `--fs-body` | **`16px`** (fisso) | bundle ha un body fluido che current non ha | **KEEP CURRENT** (16px fisso) | DESIGN.md §typography.body = `16px` fisso ("fisso, no clamp — DESIGN.md"). NB: c'è una richiesta backlog (phantom-doc §3d) di introdurre `--fs-body-fluid: clamp(16px,1.4vw,18px)` per ~20 selettori che oggi hanno clamp ad-hoc — il bundle conferma che un body fluido era stato considerato. Valutarlo nella phantom-resolution wave (cambia computed → serve design sign-off). |
| `--fs-small` | `14px` | `--fs-small` | `14px` | 0 | — | identico |
| (assente) | — | `--fs-lede` | `22px` | bundle non ha `--fs-lede` (usa `font-size: 22px` inline) | — | current è più completo |
| (assente) | — | `--fs-body-marketing` | `18px` | bundle non ha (usa `18px` inline su press/lede) | — | current è più completo |
| (assente) | — | `--fs-caption` | `11px` | bundle usa `font-size: 11px` su `.sl-area__meta`, `.sl-placeholder` | — | current è più completo |
| (assente) | — | `--fs-micro` | `12px` | bundle `.mono` usa `font-size: 12px` | — | current ha sia 11 che 12 |
| `--lh-display` | **`1.05`** | `--lh-display` | **`0.98`** | bundle più lasco di ~7% | **KEEP CURRENT** | DESIGN.md §"line-height optical": "Display ≥80px: **0.98** (tight for visual cohesion)". I JSX hero usano `lineHeight: 0.98` (home/index.jsx:96) e `0.95` (archive/blog/404 — ancora più tight) → confermano current, non il bundle `1.05`. |
| `--lh-body` | **`1.65`** | `--lh-body` | **`1.7`** | bundle più stretto di ~3% | **KEEP CURRENT** | DESIGN.md §"line-height optical": "Body DM Sans: **1.7** (legal content density requires generous leading)". NB: `1.65` era il valore *precedente* di `--lh-body` (vedi phantom-doc §5 bucket C — 5 occorrenze di `line-height: 1.65` lasciate hardcoded perché swapparle cambierebbe computed). Il bundle `1.65` conferma che è la versione vecchia. |
| (assente) | — | `--lh-h1` | `1.05` | — | — | current ha la scala ottica completa (`--lh-h1` 1.05, `--lh-heading` 1.15, `--lh-h3` 1.2, `--lh-lede` 1.5, `--lh-mono` 1.4) |
| (assente) | — | `--lh-heading` `--lh-h3` `--lh-lede` `--lh-mono` | 1.15 / 1.2 / 1.5 / 1.4 | — | — | bundle non ha nessuna di queste |
| heading `letter-spacing` | **unico `-0.01em`** (`.sl-root h1,h2,h3,h4`) | `--ls-display/h1/h2/h3` | **4 valori ottici: `-0.035em` / `-0.02em` / `-0.01em` / `-0.005em`** | bundle ha 1 valore, current 4 | **KEEP CURRENT** | DESIGN.md §"letter-spacing optical" specifica i 4 valori per hierarchy. I JSX hero usano `letterSpacing: "-0.035em"` (home h1), `-0.02em` (h2 sezioni), `-0.025em` (contact h2), `-0.015em` (varie h2/h3) — il bundle `-0.01em` non li copre, current sì (più i phantom `-0.015em`/`-0.025em` da promuovere). |
| `.sl-root .mono` | `font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase` | `.sl-mono` | `font-size: var(--fs-micro)` (12px); `letter-spacing: var(--ls-mono)` (0.08em); uppercase | 0 (computed) | — | identico in computed. Current usa token, bundle hardcoda. |
| `--sp-1 .. --sp-10` | `4/8/16/24/32/48/64/96/128/192px` | `--s-1 .. --s-10` | **stessi identici valori** `4/8/16/24/32/48/64/96/128/192px` | **0** (valori); **nome diverso** (`--sp-*` vs `--s-*`) | **KEEP CURRENT naming** (`--s-*`) | DESIGN.md §spacing usa `s-1..s-10`. tokens.css ha già anche gli alias legacy `--space-*`/`--sl-space-*`/`--sp-*`? — NO, `--sp-*` NON esiste in tokens.css corrente. Se un JSX usasse `var(--sp-N)` fallirebbe; ma i JSX hardcodano px numerici inline (`gap: 24`, `padding: "128px 96px"`), non usano `--sp-*`. → nessun rischio. |
| `--container-max` | `1440px` | `--container-max` | `1440px` | 0 | — | identico |
| `--container-pad` | `clamp(24px, 5vw, 96px)` | `--container-pad` | `clamp(24px, 5vw, 96px)` | 0 | — | identico (i JSX usano `padding: "... clamp(24px, 5vw, 96px)"` inline → matcha) |
| `--ease-soft` | `cubic-bezier(0.25, 0.46, 0.45, 0.94)` | `--ease-editorial` | `cubic-bezier(0.25, 0.46, 0.45, 0.94)` | 0 (valore); **nome diverso** | **KEEP CURRENT naming** (`--ease-editorial`) | identico in valore. I JSX usano `var(--ease-editorial)` inline (home, single-avvocato, ecc.) — quindi i JSX usano già il nome current, NON `--ease-soft`. Coerente con "bundle CSS obsoleto, JSX aggiornati". |
| (assente) | — | `--dur-fast` `--dur-base` `--dur-slow` | 200/300/600ms | bundle non ha duration token | — | current più completo. I JSX hardcodano `300ms`/`600ms`/`200ms`/`700ms` inline. |
| (assente) | — | `--radius-none/xs/sm` | 0/2px/4px | bundle non ha radius token | — | DESIGN.md §rounded. I JSX non usano `border-radius` (tutto spigolo vivo, eccetto `border-radius: "50%"` per cerchi avatar/monogramma e `999px` per `.sl-pill` nel bundle — ma `.sl-pill` non è usato nei JSX). |

### Reset scoped (`.sl-root`, `.sl-root h1..h4`)
- Bundle: `.sl-root h1,h2,h3,h4 { line-height: var(--lh-display); letter-spacing: -0.01em; }` — line-height display su TUTTI gli heading (sbagliato per h2/h3).
- Current `tokens.css`: `.sl-root h1 { letter-spacing: var(--ls-display) }`, `.sl-root h3,h4 { letter-spacing: var(--ls-h3) }`, default `var(--ls-h2)`; line-height fornito dalle component class. **Current è più corretto.** KEEP CURRENT.
- Current ha in più: `font-feature-settings` con `dlig`/`ss01` su heading, `kern/liga/calt` su `.sl-root`, focus-visible globale bronzo, smooth scroll con opt-out `prefers-reduced-motion`. Il bundle ha solo `kern/liga` basic. KEEP CURRENT.

---

## 2 · Cross-reference con il phantom-doc Wave 5 STEP 4

Il phantom-doc (`audits/wave5-step4-sections-cleanup/02-phantom-values-remaining.md`) cataloga ~460
literal tipografici hardcoded ancora in `sections.css`. **Diversi di questi sono ESATTAMENTE i
`clamp()` ad-hoc che si vedono inline nei JSX del bundle** — conferma che bundle CSS e i `clamp()`
inline dei JSX sono "lo stesso vocabolario non-tokenizzato":

| Pattern nel JSX (inline style) | JSX | Phantom-doc bucket | Stato |
|---|---|---|---|
| `clamp(48px, 5vw, 72px)` (h2 sezioni home) | home/index.jsx:160,216,255,270 | §3a display-band drift (`clamp(48px,5vw,72px)` ×4 L463,584,687,765) | phantom — già catalogato, va promosso/consolidato |
| `clamp(56px, 6vw, 96px)` (contact h2 home) | home/index.jsx:307 | §3a (`clamp(56px,6vw,96px)` L866 `.sl-contact__title`) | phantom |
| `clamp(56px, 6vw, 88px)` (single-avvocato h1) | single-avvocato:82 | §3a (`clamp(56px,6vw,88px)` L8157) | phantom |
| `clamp(64px, 8vw, 132px)` (archive-casi h1) | archive-casi:40 | §3a (`clamp(64px,8vw,132px)` ×2 L4012,9252) | phantom |
| `clamp(72px, 9vw, 140px)` (contatti/blog/404 h1) | contatti:42, blog:45, 404:39 | §3a (`clamp(64px,9vw,140px)` ×3, `clamp(72px,9vw,140px)` L5857) | phantom |
| `clamp(56px, 6.5vw, 96px)` (CTA finale varie) | chi-siamo:252, archive-casi:131, single-comp:236 | §3a (`clamp(56px,6.5vw,96px)` ×4 L3776,4245,6096,8881) | phantom |
| `clamp(40px, 4.5vw, 64px)` (h2 sezioni chi-siamo/taxonomy) | chi-siamo:146,187,219, taxonomy:101,134,154,183, single-comp:164,190,214 | §3a (`clamp(40px,4.5vw,64px)` ×3 L3541,5761 + `clamp(36px,4.5vw,64px)` ×2) | phantom |
| `clamp(36px, 4vw, 56px)` (h2 blog/contatti/404) | blog:145,172, contatti:68,233, 404:126,152 | §3a (`clamp(36px,4vw,56px)` ×3 L6001,7789…) | phantom |
| `clamp(32px, 3.5vw, 48px)` (h2 1999 / si-occupa-di) | chi-siamo:123, single-avvocato:169,186,210 | §3a (`clamp(32px,3.5vw,48px)` ×3 L8497,9533) | phantom |
| `clamp(28px, 3vw, 44px)` (sub italic competenza tier-1) | single-comp:57 | §3b H2-band drift (`clamp(28px,3vw,44px)` ×6) | phantom |
| `clamp(28px, 3vw, 36px)` (sub-h2 inline competenza) | single-comp:106,118 | §3b (`clamp(28px,3vw,36px)` ×3 L7953,8898) | phantom |
| `clamp(72px, 10vw, 160px)` (competenza tier-1 h1 GIGANTE) | single-comp:50 | non ancora catalogato esplicitamente (curva display nuova >140 max) | phantom — il più grande di tutti, da risolvere per-selector |
| `clamp(80px, 9vw, 140px)` (pull-quote €240k archive-casi) | archive-casi:71 | §3a (`clamp(80px,9vw,140px)` L4065) | phantom |
| `clamp(24px, 2.5vw, 32px)` (blockquote case) | archive-casi:80 | §3c H3-band drift (`clamp(24px,2.5vw,32px)` L4078) | phantom |
| `clamp(28px, 2.8vw, 36px)` (newsletter success h3) | footer/index.jsx:430 | §3b (`clamp(28px,2.8vw,36px)` L6839) | phantom |
| `clamp(32px, 3.2vw, 40px)` (newsletter h3) | footer/index.jsx:406 | §3b (`clamp(32px,3.2vw,40px)` L6685) | phantom |
| `clamp(36px, 4.5vw, 56px)` (footer precta h2) | footer/index.jsx:169 | §3a (`clamp(36px,4.5vw,56px)` L7735) | phantom |
| `line-height: 0.95` (hero h1 archive/blog/404/casi) | archive-casi:41, blog:46, 404:40, taxonomy:58 | §5 (`0.95` ×10 — "tighter than `--lh-display` 0.98") | phantom — candidato `--lh-display-tight: 0.95` |
| `line-height: 1.75` (prose lede chi-siamo/avvocato/competenza) | chi-siamo:65,126, single-avvocato:142, single-comp:87,109 | §5 (`1.75` ×6 — "just above `--lh-body` 1.7") | phantom — candidato `--lh-body-loose: 1.75` |
| `letter-spacing: -0.015em` (h2/h3 varie) | chi-siamo:123,203,237, single-avvocato:169,186,197,210, single-comp:58,106,118, archive-casi:178, taxonomy:117,169 | §4 (`-0.015em` ×21 — "between `--ls-h2` -0.01 and `--ls-h1` -0.02") | phantom — candidato `--ls-h2-tight: -0.015em` |
| `letter-spacing: -0.025em` (CTA h2 grandi) | home:307, chi-siamo:252, archive-casi:131, single-comp:236, 404:183 | §4 (`-0.025em` ×15 — "between `--ls-h1` -0.02 and `--ls-display` -0.035") | phantom — candidato `--ls-h1-tight: -0.025em` |
| `letter-spacing: -0.03em` (chi-siamo h1, pull-quote €240k) | chi-siamo:50, archive-casi:71 | §4 (`-0.03em` ×4 — "hybrid hero/heading") | phantom |
| `font-size: 17px` (mobile lede, prose secondaria) | home/mobile:96, chi-siamo:206, single-avvocato:67, single-comp:151, glossario:88,199, contatti:115/187, 404:52/110 | §1 (`17px` ×19 — "mobile lede per DESIGN.md ma NO token") | phantom — **forte candidato `--fs-lede-mobile: 17px`** |
| `font-size: 19px` (prose body editoriale) | home:111/220, chi-siamo:65,126, single-avvocato:142, blog:125, single-comp:87, taxonomy:166, 404:45 | §1 (`19px` ×6) | phantom — candidato normalizzare a 18px o promuovere |
| `font-size: 13px` (footer copy, meta) | chrome/footer (varie), single-avvocato:67/200, chi-siamo:176, blog:144 | §1 (`13px` ×27 — "largest no-token px bucket") | phantom — candidato `--fs-body-sm: 13px` |
| `font-size: 11px` (mono caption) | ovunque (`sl-mono`-ish, spec tags) | mappato a `--fs-caption` in STEP 4 dove era literale | OK dove tokenizzato |
| `font-size: 22px` AMBIGUO (`--fs-h3-floor` vs `--fs-lede`) | home:113, glossario:67, contatti:169, single-comp:71, ecc. | §2 (`22px` ×23 — bucket B ambiguo) | phantom — risolvere per-selector (computed-neutral) |
| `letter-spacing: 0.06em` (spec tag mono — `.sl-tag`-ish) | home/index.jsx:431, single-avvocato:96 | §4 (`0.06em` ×14 — "sub-mono tracking, anti-pattern baseline audit") | phantom — il tema usa `0.08em` (`--ls-mono`); il JSX usa `0.06em` sui tag. Drift micro — normalizzare a `--ls-mono` = computed change. |

**Conclusione**: il "drift tipografico" del design-handoff e il "drift di `sections.css`" documentato in Wave 5 STEP 4 sono **lo stesso problema visto da due angoli**. Le due wave (verifica design-handoff + phantom-resolution) vanno **sequenziate**, non parallelizzate: chi tocca `sections.css` per allineare un template ai JSX dovrebbe, sui selettori che tocca, anche risolvere i phantom di quei selettori (computed-neutral first). La phantom-resolution "completa" va dopo, come pass finale.

---

## 3 · `saltelli-design-bundle.css` — utility: già presenti vs nuove

Il bundle di utility (164 righe) definisce classi `.sl-*`. Confronto con `components.css` (440 righe) del tema:

| Utility nel bundle | Stato nel tema | Note |
|---|---|---|
| `.sl-root` reset | ✅ presente (in `tokens.css` + `components.css`) — più ricco | KEEP tema |
| `.sl-mono` | ✅ presente (`components.css`) — usa token (`--fs-micro`, `--ls-mono`) | KEEP tema |
| `.sl-link` | ✅ presente (`components.css`) — underline border-bottom → bronze hover | KEEP tema. NB: il bundle ne ha **due versioni**: `saltelli-design-bundle.css` ha la versione "border-bottom 1px var(--border) → bronze"; `tokens-design-bundle.css` ha una versione "inline-flex gap arrow + border-bottom 1px var(--primary) + `.sl-link--lg`". I JSX usano `.sl-link` per il border-bottom semplice → la versione `components.css` del tema è OK. |
| `.sl-btn` / `.sl-btn--primary` | ✅ presente (`components.css`) — testo + line sotto, arrow translateX 6px su hover | KEEP tema. DESIGN.md §components button-primary dice "navy background, cream text, padding 16px 24px" (filled) — ma il tema (e il bundle, e i JSX) usano la versione "ghost" (linea sotto, no fill). **C'è un mismatch DESIGN.md vs implementazione** sul bottone primario (DESIGN.md = filled navy; tutto il resto = underline ghost). Non l'ha introdotto il bundle — pre-esistente. Flag minore: o si aggiorna DESIGN.md, o si introduce davvero il filled. Bassa prio. |
| `.sl-area` / `.sl-area--tier1` / `__num` `__title` `__meta` | ✅ presente (`components.css`) | KEEP tema. NB: il bundle ha **due geometrie** per `.sl-area`: `tokens-design-bundle.css` → `grid-template-columns: 64px 1fr auto; gap: 24px; padding: 22px 0; border-top` + `::after` linea bronzo che cresce. `saltelli-design-bundle.css` → `80px 1fr 200px; gap: 32px; padding: 32px 0; border-bottom`. **`components.css` del tema usa `80px 1fr 200px` / `gap: 32px` / `padding: 32px 0` / `border-bottom`** — matcha la seconda. I JSX (home/index.jsx) NON specificano `grid-template-columns` su `.sl-area` (lasciano fare al CSS), quindi vince il CSS del tema. Mobile (home/mobile.jsx) override inline a `40px 1fr auto` / `padding: 20px 0`. OK. Verificare: tier-1 marker — il bundle ha `::first-letter { color: accent }` (tokens-bundle) vs `__num::before { content: "★ " }` (saltelli-bundle). Il tema (`front-page.php`) usa `sl-area--tier1` come class modifier — verificare cosa fa il CSS (probabilmente il pallino/stella bronzo sul num). I JSX home mettono `data-area-num` etc. — già allineato a `front-page.php`. |
| `.sl-acc` / `.sl-acc__item` / `__btn` / `__icon` / `__panel` / `__inner` (FAQ) | ✅ presente (`components.css`) — `data-open` + grid-template-rows 0fr→1fr | KEEP tema. NB il bundle ha anche una versione `.sl-acc__head`/`__sign`/`__body` con `aria-expanded` (tokens-bundle) — il tema + i JSX usano `.sl-acc__item[data-open]` / `__btn` / `__icon` / `__panel` / `__inner` → matcha `saltelli-design-bundle.css` + `components.css`. OK. |
| `.sl-pill` (filter pill, `border-radius: 999px`) | ⚠️ il tema NON ha `.sl-pill` rounded — usa `.sl-areas__filter` / `.sl-blog2__tab` (spigolo vivo, border-bottom). I JSX usano i filtri come **mono uppercase con border-bottom bronzo su active** (`fontFamily: var(--font-mono), letterSpacing: 0.08em, borderBottom: 1px var(--accent)`), NON come pill rounded. → Il `.sl-pill` del bundle è una utility vestigiale non usata. KEEP tema (filtri border-bottom). |
| `.sl-tlink` (inline typographic link) | ⚠️ nome non presente; il tema usa `.sl-link` per gli inline link nella prose. I JSX usano `.sl-link` inline → OK con il tema. |
| `.sl-rule` (hairline `height:1px; background:var(--border)`) | ⚠️ il tema usa `<hr class="sl-foot-hairline">` e `border-top/bottom` diretti. Equivalente. Bassa prio (eventuale alias). |
| `.sl-placeholder` (striped `repeating-linear-gradient` + grayscale→color hover) | ⚠️ il tema usa placeholder per-component (`.sl-team__placeholder`, `.sl-studio__plate-placeholder`, `.sl-blog2__*-media.is-placeholder`) — non una utility unica. I JSX usano gradient diagonale inline + `filter: grayscale`. Funzionalmente coperto. Eventuale armonizzazione (una `.sl-placeholder` riusabile) = nice-to-have, bassa prio. |
| `@keyframes sl-rise` + `.sl-word > span` (hero word reveal) | ⚠️ il tema fa il reveal con GSAP (`data-split-reveal` + `main.js`), non con CSS `@keyframes sl-rise`. I JSX usano transition CSS inline (`opacity`/`translateY` con delay `i*80ms`), che è più vicino al `@keyframes sl-rise` del bundle che a GSAP. → **Decisione: tenere GSAP (già funzionante, opt-out reduced-motion gestito); il `@keyframes sl-rise` del bundle è la versione CSS-only alternativa che NON serve adottare.** Se in futuro si volesse togliere la dipendenza GSAP, il bundle ha già la ricetta CSS. |
| `@keyframes` + `.sl-reveal` / `.is-in` (section fade-in) | ⚠️ il tema usa `data-reveal` + IntersectionObserver in `main.js`. Equivalente. KEEP tema. |
| `.sl-foot-link` (+ `::after` underline bronze scaleX, `.is-hover`) — iniettato runtime da `footer/index.jsx` | ✅ presente (`components.css`/`sections.css` come `.sl-foot-link`) — il `footer.php` lo usa già. KEEP tema. |
| `.sl-newsletter__input` / `__check` / `.sl-spinner` — iniettati runtime da `footer/index.jsx` | ⚠️ il `footer.php` ha `.sl-foot-newsletter__input` / `__check` (nomi leggermente diversi). Verificare che lo styling (border-bottom 1px rgba(255,255,255,0.3) → bronze on focus; checkbox custom 16×16 con check bronzo; spinner) sia presente. Probabilmente sì. Drift di naming, non funzionale. |
| `.sl-input` (form contatti — iniettato runtime da `contatti/index.jsx`) | ⚠️ `template-parts/page-contatti.php` ha il proprio form CSS. Verificare match (underline-only, border-bottom var(--border) → bronze on focus, `select.sl-input` con freccia CSS). |

**Utility netto-nuove introdotte dal bundle**: nessuna che serva adottare. Tutte le utility del bundle hanno un equivalente (a volte con nome diverso) nel tema. Le poche "vestigiali" (`.sl-pill` rounded, `.sl-tlink`, `.sl-rule`) non sono usate dai JSX e non vanno aggiunte.

---

## 4 · Sintesi operativa

1. **`tokens.css` corrente vince. `docs/DESIGN.md` resta SoT. Nessun update a DESIGN.md.** Il bundle `tokens-design-bundle.css` è obsoleto (pre-Wave 5 STEP 2). Documentarlo come "reference informativo, non SoT" (lo dice già il README del bundle).
2. **Durante l'implementazione**, quando un JSX hardcoda inline un valore NON in `tokens.css`:
   - se matcha un token esistente → usa `var(--token)` (computed-neutral)
   - se è un phantom già catalogato (Wave 5 STEP 4 doc) → segui il piano di quel doc (promuovi a token / consolida / lascia)
   - se è genuinamente nuovo (es. `clamp(72px,10vw,160px)` del competenza-tier1 h1) → decisione per-selector: o lo si avvicina alla curva `--fs-display`/`--fs-h1`, o lo si tiene come phantom documentato. **Mai cambiare i token `:root` per inseguire un JSX.**
3. **Niente `border-radius`** da introdurre (tutto spigolo vivo eccetto cerchi avatar/monogramma `50%`).
4. **Animazioni**: tenere GSAP/`main.js` (già con opt-out reduced-motion). I `@keyframes` del bundle (`sl-rise`, `sl-reveal`) sono la ricetta CSS-only di fallback, non da adottare ora.
5. **Bottone primario**: c'è un mismatch storico DESIGN.md (filled navy) vs implementazione (underline ghost). Non l'ha introdotto il bundle. Flag minore — decidere con Duccio se aggiornare DESIGN.md o introdurre il filled. Bassa prio (post-cut).
6. **Sequenziare** le wave design-handoff e la phantom-resolution Wave 5 STEP 4 (stesso `sections.css` — one-writer-at-a-time; e si sovrappongono semanticamente).
