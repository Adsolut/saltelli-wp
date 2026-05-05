# Impeccable Audit — Homepage `http://localhost:8080/`

**Run date:** 2026-04-29
**Theme version (audit):** 0.3.0-beta-polish (post Step B)
**Auditor:** Claude Impeccable Agent (Step C) — combined detector output (`npx impeccable detect`) + manual structured scan per `reference/audit.md` methodology

---

## Audit Health Score

| # | Dimension | Score | Key Finding |
|---|-----------|-------|-------------|
| 1 | Accessibility | 2 | Footer copy contrast 2.6:1 (WCAG AA fail) + missing `:focus-visible` su elementi interattivi (solo 2 regole `:focus` in tutto il CSS, entrambe sullo skip-link) |
| 2 | Performance | 3 | Animazione `max-height` su `.sl-acc__panel` (layout property, jank). Hero/sezioni già ottimizzate con transform+opacity |
| 3 | Responsive Design | 4 | Mobile-first solido, breakpoint 375/768/1024, sticky preview desktop-only, burger nascosto desktop, touch target ≥ 44px |
| 4 | Theming | 4 | Tutti i colori da tokens, niente hard-coded, dark navy footer coerente. NB: nessun dark mode richiesto dal brief |
| 5 | Anti-Patterns | 3 | 3× `all-caps-body` (label uppercase su 42-49 char), 1× `gray-on-color` (sl-mono in footer dark). Niente AI slop (no purple, no Inter, no glass, no card grid sloppy) |
| **Total** | | **16/20** | **Good** — address weak dimensions: accessibility focus + footer contrast |

---

## Anti-Patterns Verdict

**Pass.** Il design NON sembra AI-generato. Tells AI tipiche assenti:
- ❌ NIENTE purple/magenta gradient
- ❌ NIENTE Inter font (usiamo Playfair + DM Sans)
- ❌ NIENTE bounce easing (solo `cubic-bezier(0.25, 0.46, 0.45, 0.94)` editorial)
- ❌ NIENTE dark glow / metric cards / saas hero
- ❌ NIENTE glassmorphism / claymorphism
- ❌ NIENTE generic stock illustrations (placeholder esplicitamente marcati)

**Tells minori detected dal detector regex:**
- 3× `all-caps-body` (long uppercase) — **parzialmente intentional** (eyebrow editoriale `.sl-mono`), parzialmente da fixare (footer copy)
- 1× `gray-on-color` (#6b6b6b su #1b2b4b) — **da fixare**, è un override mancante per `.sl-mono` dentro `.sl-footer`

---

## Executive Summary

- **Audit Health Score:** 16/20 (Good)
- **Issues totali:** 6 (1 fs + 5 live = 6 distinct after de-dup, ma il detector raggruppa per occorrenza)
- **Severity breakdown:** P0=0 · P1=2 · P2=3 · P3=1
- **Top 3 critical:**
  1. **[P1] Footer contrast WCAG fail** — `.sl-mono` dentro footer dark navy = 2.6:1 (need 4.5:1)
  2. **[P1] Focus-visible mancante** — solo 2 regole focus globali, gli altri elementi interattivi (`<a class="sl-area">`, `.sl-areas__filter`, `.sl-link`, ecc.) usano outline default browser (poco visibile / inconsistente)
  3. **[P2] Layout-thrash su accordion `max-height` transition** — non visibile in homepage ma usato in `single-competenza` e `archive-competenza`
- **Recommended next steps:** Apply `/typeset` (eyebrow), `/harden` (focus-visible + footer contrast override), `/polish` (final pass)

---

## Detailed Findings by Severity

### [P1] Footer copy & labels contrast — WCAG AA fail

- **Location:** `.sl-footer .sl-mono` — file `assets/css/components.css:10-16` (regola base) e usata in `footer.php` per `.sl-footer__col-label`, `.sl-footer__copy`, `.sl-footer__social`, ecc.
- **Category:** Accessibility
- **Impact:** Testo "Diciannove aree", "Contatti", copyright "© 2026 …", social labels — illeggibili a basso contrasto su sfondo navy `#1B2B4B`. Detector measured: 2.6:1 (fail).
- **WCAG:** 1.4.3 (Contrast Minimum, AA) — needs 4.5:1 for body text.
- **Recommendation:** Override `color` per `.sl-footer .sl-mono` a `rgba(255, 255, 255, 0.7)` (≈ 9.4:1, AAA). NIENTE cambio del token `--text-muted` (locked).
- **Suggested command:** `/harden`

### [P1] Focus-visible mancante su elementi interattivi

- **Location:** Tutto il CSS theme. Solo `.sl-skip-link:focus` e `.skip-link:focus` (legacy) hanno regole esplicite.
- **Category:** Accessibility
- **Impact:** Utenti tastiera/screen reader perdono il keyboard tracking. `<a class="sl-area">`, `.sl-areas__filter`, `.sl-link`, `.sl-btn`, `.sl-acc__btn`, `.sl-header__nav a`, `.sl-mobile-menu a`, `.sl-footer__menu a` — tutti senza focus-visible style.
- **WCAG:** 2.4.7 (Focus Visible, AA).
- **Recommendation:** Aggiungere blocco focus-visible in `tokens.css` (utility globale) con outline 2px accent + offset 4px. Non over-engineer: una regola sintetica copre tutti.
- **Suggested command:** `/harden`

### [P2] All-caps body — `.sl-mono` su long strings

- **Location:** Tre occorrenze:
  1. `.sl-hero__eyebrow` — "Studio Legale · Napoli · Chiaia · Dal 1999" (42 char)
  2. `.sl-studio__plate-tl > .sl-mono` (placeholder) — "Fotografia in B/N · 1440 × 480 · placeholder" (44 char)
  3. `.sl-footer__copy` — "© 2026 Studio Legale Emiliano Saltelli & Partners" (49 char)
- **Category:** Anti-Pattern (typography readability)
- **Impact:** Long uppercase rallenta lettura (manca pattern di ascendenti/discendenti). 42-49 char è oltre il threshold "label corta".
- **Standard:** Heuristic Impeccable / WCAG informativa
- **Recommendation:**
  - Caso 3 (footer copy) — **fix**: aggiungere modifier `.sl-footer__copy` che disattiva `text-transform`. Copyright deve essere leggibile, non un'etichetta.
  - Casi 1 + 2 — **skip con razionale**: l'eyebrow `.sl-mono` è la firma tipografica editoriale del design (vedi `homepage-desktop.jsx`). Il placeholder è esplicitamente temporaneo (verrà rimpiazzato da una real photo, vedi commento `<!-- TODO: replace with real Saltelli photo -->`).
- **Suggested command:** `/typeset` (footer copy de-uppercase)

### [P2] Layout-property animation — `max-height` su accordion

- **Location:** `assets/css/components.css:165-170` (`.sl-acc__panel`).
- **Category:** Performance
- **Impact:** Animare `max-height` causa layout thrash + jank a 60fps. NB: l'accordion non è renderizzato su homepage (Frame 1) ma su `single-competenza` (Tier 1 areas con FAQ).
- **Standard:** GPU-friendly transitions only
- **Recommendation:** Convertire a `grid-template-rows: 0fr → 1fr` con wrapper `display: grid; overflow: hidden` (tecnica Bramus 2023). Mantiene auto-height, no JS.
- **Suggested command:** `/animate` o `/polish`

### [P2] `.sl-mono` color override mancante in footer dark

- **Location:** `assets/css/components.css:10-16` (regola base senza scope).
- **Category:** Theming consistency
- **Impact:** Quando `.sl-mono` viene usato in contesto dark (footer), eredita color from var(--text-muted) che è progettato per surface chiaro. Questo è il root cause del [P1] contrast fail e del [P2] gray-on-color flag detector.
- **Recommendation:** Override `.sl-footer .sl-mono { color: rgba(255, 255, 255, 0.7); }` — fix unico per contrast + gray-on-color (un solo Edit risolve 2 issue detector).
- **Suggested command:** `/harden`

### [P3] Heading hierarchy — verifica H1 unicità

- **Location:** Homepage rendered DOM
- **Category:** Accessibility / SEO
- **Impact:** CLAUDE.md hard rule "1 H1 per page". Verifica veloce: 1× `<h1>` (`.sl-hero__headline`) + sezioni con `<h2>` `.sl-section-title`. Hierarchy pulita.
- **Status:** **Pass** — no fix needed.

---

## Patterns & Systemic Issues

- Il pattern problematico è "**utility class agnostica del background**": `.sl-mono` viene usata sia su sfondo chiaro (hero, sezioni) sia su sfondo scuro (footer) ma il colore è hardcoded a `--text-muted`. Soluzione di design: scoping per contesto (`.sl-footer .sl-mono`), o mod scoped (`.sl-mono--inverse`).
- Lo stesso problema potrebbe presentarsi se in futuro venisse aggiunta una sezione su `--primary` background (es. CTA banner).

---

# Critique — Section (post-`/critique homepage`)

> Eseguito metodologicamente seguendo `reference/critique.md` — review UX persona-based, gerarchia, risonanza emotiva.

## Personas testate

1. **Cliente potenziale, 45-65, ricerca tributarista a Napoli** (target principale)
2. **Avvocato peer / professionista referente** (target secondario, network)
3. **Giornalista che cita lo studio** (target terziario, GEO)

## Hierarchy / IA — score 4/5
- ✅ Hero immediato comunica brand + claim ("Diritto, con misura.") + tagline + CTA
- ✅ Section eyebrows numerate `§ 01-06` creano un sommario implicito (firma editoriale)
- ✅ Aree pratica con preview sticky desktop = excellent affordance
- ⚠️ Su mobile, la sticky preview NON c'è (CSS `.sl-areas__preview { display: none }` < 768) → utente mobile non ha shortcut sintetico delle 19 aree. **Accettabile**: il design mobile ha colonne strette, una preview verticale ruberebbe spazio. Decisione design preserved.

## Visual hierarchy — score 4/5
- ✅ Display 9vw clamp navy + accent oro = rapporto sobrio e di alto valore
- ✅ Italic em-dash treatment ("Diciannove aree. *Tre presidiate in profondità.*") = signature editoriale
- ⚠️ Nelle 19 aree liste, `.sl-area__meta` è `opacity: 0` di default → si vede solo on-hover. Su mobile (no hover) i meta NON appaiono mai → utente mobile non vede "Tier 1 · approfondimento" indicator. **Da fixare**: opacity 1 di default su mobile (CSS @media), opacity 0 + hover desktop.
- **Suggested command:** `/typeset` o `/adapt`

## Cognitive load — score 4/5
- ✅ Spazio bianco generoso (padding 128px desktop, 80px mobile)
- ✅ Una sola CTA prominente per sezione
- ⚠️ Section "casi rappresentativi" ha 4 casi con dettagli tribunale + outcome. Buon density. Possible micro-fix: aggiungere visual divider tra casi (oltre al border-bottom)? **Skip**: non necessario, il border-bottom + padding 32px già fa lavoro.

## Risonanza emotiva — score 4/5
- ✅ "Vent'anni di lavoro accanto a famiglie e imprese di Napoli" — risuona sul target locale
- ✅ Toni gravity-without-coldness (navy + crema + bronzo) → "premium ma umano"
- ⚠️ Footer copy "© 2026 Studio Legale Emiliano Saltelli & Partners" all-caps = freddezza istituzionale. De-uppercased sarebbe più caldo. **Già flagged come [P2] da fixare**.

## Critique-led decisions Adsolut > Impeccable

Eventuali suggerimenti generici Impeccable potrebbe dare e che NOI rifiutiamo:

| Sugg. potenziale | Decisione Adsolut | Razionale |
|---|---|---|
| "Aggiungere più colore / accenti vibranti" | **REJECT** | Palette deliberatamente sobria (cream + navy + bronzo). Più colore violerebbe brand premium law firm |
| "Hero più grande / più CTA" | **REJECT** | Una CTA editoriale + scroll indicator. Lo studio NON vende un SaaS |
| "Card grid per le aree pratica" | **REJECT** | Il design "list editoriale + sticky preview" è la signature differenziante vs i competitor Naples |
| "Aggiungere testimonianze sociali / counter clienti" | **REJECT** | Setup conservativo deontologia forense; "casi rappresentativi anonimizzati" è già il massimo consentito |
| "Headline più piccola / lettura facile" | **REJECT** | 9vw clamp è la cifra editoriale (matches `homepage-desktop.jsx`) |
| "Smooth scroll Lenis su mobile" | **REJECT** | Lenis caricato solo desktop+!reduced-motion (decisione Polish Agent v0.3.0, motivata perf mobile) |

---

## Apply / Skip decisions

| Issue | Decision | Note |
|---|---|---|
| Footer contrast `.sl-mono` color | **APPLY** | CSS override scoped, no template change |
| Footer copy de-uppercase | **APPLY** | Modifier class `.sl-footer__copy` aggiunto |
| Focus-visible globale | **APPLY** | Utility CSS scoped `.sl-root *:focus-visible` (o equiv) |
| Hero eyebrow shorten | **SKIP** | "Don't change copy" rule (PROMPT_AGENT_C) — è copy editoriale Claude Design |
| Plate placeholder shorten | **SKIP** | Verrà rimpiazzata con real photo, è temporaneo |
| Accordion `max-height` → `grid-template-rows` | **APPLY** | Componente generico, sarà attivo in single-competenza |
| `.sl-area__meta` opacity sempre visibile mobile | **APPLY** | Adattamento touch — bug fix UX mobile |
| H1 unicità | **PASS** | Già conforme |

---

*Audit completato — pronto per Step C5 (apply fix selettivi).*
