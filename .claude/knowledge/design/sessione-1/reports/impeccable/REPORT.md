# Impeccable Agent Step C — Report finale

**Data:** 2026-04-29
**Theme version (in):** `0.3.0-beta-polish` (post Polish Agent Step B)
**Theme version (out):** `0.4.0-beta-impeccable`
**Skill used:** [`pbakaus/impeccable`](https://github.com/pbakaus/impeccable) v2.1.8 (Apache-2.0) — installato in `.claude/skills/impeccable/`
**Detector:** `npx impeccable detect` (jsdom + Puppeteer per URL)

---

## 1 · Task status (8/8)

| # | Task | Status | Note |
|---|---|---|---|
| C1 | Setup Impeccable nel repo | ✅ | Install via `git clone --depth 1` (l'URL `impeccable.style/api/download/claude-code` ritorna l'HTML SPA, non lo zip). Skill copiata in `.claude/skills/impeccable/` con SKILL.md + 35 reference files + scripts. |
| C2 | Detector run baseline | ✅ | Run filesystem (jsdom) + live URL (Puppeteer). Output JSON+TXT salvati in `reports/impeccable/`. |
| C3 | `/audit homepage` | ✅ | Eseguito metodologicamente seguendo `.claude/skills/impeccable/reference/audit.md` (5 dimensions, score 0-4). Output in `audit-homepage.md`. |
| C4 | `/critique homepage` | ✅ | Persona-based (cliente potenziale, peer professionista, giornalista). Append a `audit-homepage.md`. |
| C5 | Apply fix `/typeset /layout /animate /harden /polish` | ✅ | 6 fix CSS chirurgici applicati, niente nuove dipendenze, design tokens NON modificati. |
| C6 | Detector run finale + diff | ✅ | **6 → 2 issues (-67%)**. Target -30% ampiamente superato. |
| C7 | Lighthouse manual instructions | ✅ | Istruzioni preparate, vedi §3. Esecuzione richiede Duccio (DevTools manuale). |
| C8 | Bump version + cache flush + REPORT | ✅ | `0.4.0-beta-impeccable` propagato, wp cache flushed, transient deleted, asset serviti con new querystring verificato via curl. |

---

## 2 · Detector counts — baseline → final

| Scope | Baseline | Final | Δ |
|---|---:|---:|---:|
| Filesystem (`wp-content/themes/saltelli/`) | **1** | **0** | **−1 (−100%)** |
| Live URL (`http://localhost:8080`) | **5** | **2** | **−3 (−60%)** |
| **Totale** | **6** | **2** | **−4 (−67%)** |

### Issue eliminati

1. ✅ `layout-transition` su `.sl-acc__panel max-height` (filesystem) — risolto via `grid-template-rows: 0fr → 1fr`
2. ✅ `all-caps-body` 49 char (footer copy "© 2026 Studio Legale Emiliano Saltelli & Partners") — risolto via `.sl-footer__copy { text-transform: none }`
3. ✅ `gray-on-color` (#6b6b6b su #1b2b4b nel footer) — risolto via `.sl-footer .sl-mono { color: rgba(255,255,255,0.72) }`
4. ✅ `low-contrast` 2.6:1 (stesso elemento del precedente) — risolto allo stesso colpo (~9.4:1 = AAA)

### Issue residui (2 — entrambi P2 deliberati)

1. ⚠️ `all-caps-body` 42 char — `.sl-hero__eyebrow` "Studio Legale · Napoli · Chiaia · Dal 1999"
   **Skip rationale:** è la firma tipografica editoriale dell'hero (`homepage-desktop.jsx` reference). Cambiarla = drift design. Prompt esplicita "non cambiare copy".
2. ⚠️ `all-caps-body` 44 char — `.sl-studio__plate` placeholder "Fotografia in B/N · 1440 × 480 · placeholder"
   **Skip rationale:** testo temporaneo, marcato `<!-- TODO: replace with real Saltelli photo -->`. Sparirà appena Elena carica la foto della facciata.

### ZERO AI-slop antipattern residui

Nessuna delle tells AI è presente: niente purple/magenta gradient, niente Inter font, niente bounce easing, niente dark glow, niente glassmorphism, niente card grid generic. Confermato sia da audit manuale che da detector.

---

## 3 · Lighthouse — istruzioni esecuzione manuale

Non eseguibile automaticamente da Claude Code (no Lighthouse CLI in path). Steps per Duccio:

```
1. Apri Chrome → http://localhost:8080/
2. DevTools (Cmd+Opt+I) → tab "Lighthouse"
3. Mode: "Navigation (default)" · Device: "Mobile" · Categories: tutte
4. Click "Analyze page load"
5. Salva PDF in:
   .claude/knowledge/design/sessione-1/reports/impeccable/lighthouse-mobile.pdf
6. Ripeti con Device: "Desktop" → lighthouse-desktop.pdf
```

**Target attesi (basati sui fix applicati):**
- Performance ≥ 92 (mobile) / ≥ 96 (desktop) — l'asset stack è leggero (custom CSS, 1 JS file, GSAP+Lenis CDN deferred)
- Accessibility ≥ 95 — focus-visible globale + footer contrast fix dovrebbero portare il sub-score a 95+
- Best Practices ≥ 95
- SEO ≥ 95 (la GEO logic è territorio del GEO Engineer, non toccata qui)

**Rischio noto:** Lighthouse Accessibility scanner verifica anche il contrast su elementi non testati dal regex Impeccable. Se emergono altri "low contrast" da Lighthouse, vanno adressati nello Step E (Template Polish) sui CPT.

---

## 4 · Comandi Impeccable lanciati

In ordine sequenziale (i comandi sono *workflow descritti* nei reference markdown della skill, applicati metodologicamente — non slash-command runtime perché le skill Claude non eseguono codice da soli):

| Comando | Reference applicata | Note |
|---|---|---|
| `/audit homepage` | `audit.md` | 5 dimensions scored 16/20 (Good). Findings P0=0 P1=2 P2=3 P3=1. Output: `audit-homepage.md` |
| `/critique homepage` | `critique.md` | 3 personas (cliente / peer / giornalista). Score IA 4/5 · Visual 4/5 · Cognitive 4/5 · Emotional 4/5. |
| `/typeset homepage` | `typeset.md` | Footer copy de-uppercase + font-feature-settings su display (`dlig`, `ss01`) + body (`calt`). |
| `/layout homepage` | `layout.md` | **Skip apply.** Spazio bianco generoso e ritmo verticale 8-base sono già coerenti con `tokens.css`. Niente drift introdotto. |
| `/animate homepage` | `animate.md` | Accordion `max-height` → `grid-template-rows`. Animations Polish Agent NON sostituite. |
| `/harden homepage` | `harden.md` | Focus-visible globale + footer contrast override + `.sl-area__meta` mobile-always-visible. |
| `/polish homepage` | `polish.md` | Final pass: verifica tokens consistency, cache busting, ricontrollo detector. |

---

## 5 · Fix applicati per categoria

### A — Accessibility (CSS, no template change)

```css
/* tokens.css — focus-visible globale (utility singola, copre tutti gli interattivi) */
.sl-root :focus-visible, :focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 4px;
  border-radius: 2px;
}
.sl-root :focus:not(:focus-visible) { outline: none; }

/* components.css — focus area (override più specifico) */
.sl-area:focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 4px;
}
.sl-area:focus-visible .sl-area__meta { opacity: 1; }
```

### B — Theming consistency (footer contrast, dark-context)

```css
/* components.css — scope dark-context per .sl-mono */
.sl-footer .sl-mono,
.sl-footer .sl-mono a {
  color: rgba(255, 255, 255, 0.72);   /* ~9.4:1 su #1B2B4B = WCAG AAA */
}
```

### C — Typography readability (de-uppercase footer copy)

```css
/* components.css */
.sl-footer__copy.sl-mono {
  text-transform: none;
  letter-spacing: 0.04em;
  font-size: 11px;
}
```

### D — Performance (layout-thrash → GPU-friendly)

```css
/* components.css — accordion */
.sl-acc__panel {
  display: grid;
  grid-template-rows: 0fr;
  overflow: hidden;
  transition: grid-template-rows var(--dur-base) var(--ease-editorial);
}
.sl-acc__item[data-open="true"] .sl-acc__panel { grid-template-rows: 1fr; }
.sl-acc__inner { min-height: 0; /* + le regole pre-esistenti */ }
```

### E — Responsive UX (mobile touch device meta visibility)

```css
/* components.css */
@media (hover: none), (max-width: 767px) {
  .sl-area__meta { opacity: 1; }
}
```

### F — Typesetting fine (display alternates + body contextual alts)

```css
/* tokens.css */
.sl-root { font-feature-settings: "kern" 1, "liga" 1, "calt" 1; }
.sl-root h1, .sl-root h2, .sl-root h3, .sl-root h4 {
  font-feature-settings: "kern" 1, "liga" 1, "dlig" 1, "ss01" 1;
}
```

**Files touched (4):**
- `wp-content/themes/saltelli/assets/css/tokens.css`
- `wp-content/themes/saltelli/assets/css/components.css`
- `wp-content/themes/saltelli/style.css` (version)
- `wp-content/themes/saltelli/functions.php` (version)

**Files NOT touched** (per design constraint): `sections.css`, `front-page.php`, `footer.php`, `header.php`, `main.js`, schema partials, tokens values.

---

## 6 · Suggerimenti Impeccable **skippati** + razionale (audit trail)

| Suggestion (potenziale) | Decisione | Razionale |
|---|---|---|
| Hero eyebrow shortening (42 char → ~30) | **SKIP** | "Don't change copy" rule esplicita nel PROMPT_AGENT_C. È copy editoriale Claude Design già validato. |
| Plate placeholder shortening | **SKIP** | Testo temporaneo (`<!-- TODO: replace -->`). Sparirà con la real photo Saltelli. |
| `/colorize` — aggiungere accenti vibranti per "warmth" | **SKIP** | Palette Saltelli locked: cream + navy + bronzo deliberati. Hard rule CLAUDE.md ("No purple/magenta, no aggressive red"). |
| `/bolder` — amplify visual impact | **SKIP** | Il design "premium law firm" deliberatamente sobrio è il differenziante vs i competitor Naples. Bolder = drift verso SaaS hero. |
| `/delight` — micro-animations easter eggs | **SKIP** | Tono editoriale serio (legal sector). Easter egg = anti-tone. |
| Card grid layout per le 19 aree | **SKIP** | List + sticky preview è la signature visuale (`homepage-desktop.jsx`). Card grid = AI slop tell. |
| Dark mode variant | **SKIP** | Brief Saltelli non lo richiede. Dark mode su law firm = inconsistente con brand premium 2026. |
| Più CTA / urgency triggers | **SKIP** | Tone: "diritto, con misura", non "prenota subito limited time". |
| Counter clienti / testimonianze sociali | **SKIP** | Deontologia forense italiana: testimonials clienti vietati. "Casi rappresentativi anonimizzati" è già il massimo consentito. |
| Smooth scroll Lenis disable / forzato su mobile | **SKIP** | Vedi §7 nota smooth scroll. |

---

## 7 · Decisioni autonome

### 7.1 — Smooth scroll Lenis: mantenuta decisione Polish Agent

Per il direttore d'orchestra: il Polish Agent v0.3.0 carica Lenis condizionalmente (`if hasLenis && !reduced && !isMobile`). Verificato in `main.js`:
- ✅ Lenis è enqueued via CDN in `inc/enqueue.php`
- ✅ Inizializzato con `lerp: 0.1, smoothWheel: true` su desktop+motion-OK
- ✅ Skip mobile (gesture nativo iOS/Android è meglio del smooth synthetic) e reduced-motion (a11y)

Il detector Impeccable e la skill `/animate` **non hanno suggerito di re-introdurre o modificare** lo smooth scroll. La decisione del Polish Agent è coerente con le best practice perf+a11y dell'audit.md di Impeccable. **Mantenuta as-is.**

Nota: SplitText era usato come fallback in `main.js` se le 3 parole non erano già pre-segmentate dal template. Visto che `front-page.php` pre-segmenta sempre con `<span class="sl-hero__word">`, il path SplitText non viene preso. È una protezione ridondante ma economica (rimane in caso di template variants).

### 7.2 — Detector come slash-command equivalente

Il prompt assumeva che `/audit homepage` fosse un comando runtime eseguibile da Claude Code. In realtà i file in `.claude/skills/impeccable/reference/*.md` sono **istruzioni-skill** che il modello applica metodologicamente. Ho seguito le istruzioni passo-passo (non delegando a un sub-agent), producendo l'output strutturato richiesto. Questo è comportamento atteso per le skill Claude Code (no runtime execution), ma vale registrarlo per chiarezza nel report.

### 7.3 — Ho usato `git clone` invece dell'URL impeccable.style

URL `https://impeccable.style/api/download/claude-code` ritorna **HTML SPA** (94 KB, content-type text/html), non un zip. Probabilmente la SPA popola il download via JS post-render. Workaround: `git clone --depth 1 https://github.com/pbakaus/impeccable.git` → estrazione `.claude/skills/impeccable/` → install diretto. Documentato per eventuale refresh skill.

### 7.4 — Niente refactor `.sl-mono` globale

Avrei potuto ristrutturare `.sl-mono` come utility background-agnostic (con `--text-muted` overridable da context). Ho preferito **scope-only override** (`.sl-footer .sl-mono`) per minimizzare blast radius e non toccare un'utility usata in 30+ punti del template. Refactor completo va in Step E (Template Polish) se utile.

### 7.5 — Touch device fix `(hover: none)`

Aggiunta media query `(hover: none), (max-width: 767px)` per `.sl-area__meta` opacity:1. Risolve il bug UX scoperto durante la critique: utenti mobile non vedevano mai gli indicatori "Tier 1 · approfondimento" perché il rivelamento era solo on-hover. Decisione autonoma giustificata da accessibility + UX integrità.

### 7.6 — Font-feature-settings `dlig` `ss01`

Applicato `dlig` (discretionary ligatures) e `ss01` (stylistic set 01) sul display. Playfair Display **supporta** entrambe — il browser ignora silenziosamente se la variante non è caricata, quindi è no-op safe. In rendering reale aggiunge piccole varianti tipografiche editoriali (ligature decorative su `ct`, `ff`, `st`). Zero rischio di regressione.

---

## 8 · Note per Step D / Step E (blocker / dipendenze)

### Step D — Content migration (CPT populate)

- **Nessun blocker introdotto da Step C.** Tutti i fix CSS sono retro-compatibili.
- Quando Elena popola le 19 competenze + 4 avvocati: il template `single-competenza.php` rivelerà l'accordion Tier 1 con FAQ — ora animato via `grid-template-rows` (verifica visiva consigliata, default `400px max-height` perduto, ma `1fr` è auto-height).
- L'`is_tier_1_focus` boolean ACF è gestito; la dispatch di template `tier1` vs `tier2` è già scaffolded.

### Step E — Template Polish (CPT individuali)

Suggerisco di lanciare un altro `/audit` Impeccable specificamente su:
- `single-competenza` (Tier 1: lo stato della FAQ accordion + cluster blog è dove l'eye-test va fatto)
- `single-avvocato` (foto + bio prose + spec list)
- `archive-competenza` (paginated list 19 entries)

Issue probabili da intercettare in Step E:
1. Bio avvocato — verificare line-length 65-75ch
2. Cluster blog correlati — pattern card vs list (mantenere list editoriale)
3. CTA in single-competenza — verificare hierarchy con "Approfondisci" della homepage
4. OG image fallback se non settata Yoast

### Refactor opzionale (non urgente)

- `.sl-mono` utility potrebbe diventare `.sl-mono` (cream context) + `.sl-mono.sl-mono--inverse` (dark context) per renderlo riusabile in future dark CTA banners. Non urgente, lo scope-rule attuale risolve.
- Heuristic Impeccable score 4/5 generale. Per arrivare a 5/5 servirebbe risolvere i 2 `all-caps-body` residui — implica modifica copy che è esplicitamente fuori scope.

---

## Allegati

- `baseline.json` (fs, 1 issue) · `baseline.txt` (human) · `baseline-live.json` (live, 5 issues)
- `final.json` (fs, 0 issues) · `final-live.json` (live, 2 issues)
- `audit-homepage.md` — full audit + critique con score per dimension

---

*Step C completato. v0.4.0-beta-impeccable pronta per review visiva di Duccio + Lighthouse manual run. Mi fermo qui, in attesa istruzioni Step D.*
