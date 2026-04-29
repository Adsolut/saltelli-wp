# Prompt — Style & Animation Agent (SHIP MODE 24H)

> **Per Claude Code in tmux pane 1.** Apri questa cartella (`saltelli-wp/`), leggi questo file, eseguilo. Non improvvisare. Non comunicare con gli altri 2 agent (Theme Architect, GEO Engineer): lavorate su file disjoint.

---

## Tu sei

Lo **Style & Animation Agent** del build Saltelli. Il tuo lavoro:

1. Trasferire i tokens CSS dalla **sessione Claude Design (`tokens.css`)** dentro il tema in `assets/css/tokens.css`
2. Scrivere il CSS base + components ricavandolo dal `tokens.css` di Claude Design + dal `homepage-desktop.jsx`
3. Configurare GSAP + Lenis + SplitText (CDN, defer, SRI)
4. Scrivere `main.js` come entrypoint con tutte le animazioni del Frame 1

**Non tocchi**:
- File PHP (li gestisce Theme Architect)
- File schema (li gestisce GEO Engineer)
- ACF JSON (li gestisce Theme Architect)
- Contenuti reali (vengono dopo, da Elena)

---

## Letture obbligatorie (in quest'ordine, prima di scrivere codice)

1. `CLAUDE.md` — hard constraints
2. `BRIEF_Saltelli_WordPress.md` — sezione "Stack tecnico" + "Stack effetti WOW"
3. `.claude/knowledge/project-context.json` — design system locked
4. `CLAUDE_DESIGN_PROMPT.md` v2.1 — sezioni 4 (Design tokens) + 6 (Comportamenti animazioni)
5. **`.claude/knowledge/design/sessione-1/tokens.css`** ← FONTE DI VERITÀ per i tokens
6. **`.claude/knowledge/design/sessione-1/homepage-desktop.jsx`** ← per estrarre styling specifico delle 7 sezioni Homepage
7. **`.claude/knowledge/design/sessione-1/homepage-mobile.jsx`** ← per estrarre il behavior mobile
8. `wp-content/themes/saltelli/assets/css/` — i file scaffold da popolare (oggi vuoti con TODO)

**NON leggere**: `design-canvas.jsx`, `Saltelli Partners - Sessione 1.html`, `design-system.jsx` — sono visualizzazioni meta di Claude Design, non source-of-truth per il build.

---

## Hard rules (non negoziabili)

| Rule | Reason |
|---|---|
| Namespace CSS `.sl-*` per tutti i custom (`.sl-root`, `.sl-area`, `.sl-btn`, ecc.) | Evita collisioni con plugin esistenti |
| **NO Tailwind, NO Bootstrap.** Solo CSS custom + variables | Già stabilito nel CLAUDE.md |
| GSAP 3.15+ + Lenis SOLO da CDN con SRI hash | Performance + sicurezza |
| **Mobile-first**, ogni media query `@media (min-width: ...)` | 60% traffico mobile |
| Type scale **clamp() responsive** ovunque | Già nei tokens |
| Animazioni **mobile-light**: solo fade + opacity, NO SplitText, NO translate aggressivi su mobile | LCP target |
| Caricamento font: WOFF2 self-hosted, `font-display: swap`, preload SOLO i 2 critical weights | Performance Lighthouse |
| Niente `console.log` di debug nel JS finale | Produzione-grade |

---

## Task 1 — Trasferire i tokens (10 min)

Copia il contenuto di `.claude/knowledge/design/sessione-1/tokens.css` in `wp-content/themes/saltelli/assets/css/tokens.css`, sostituendo eventuale TODO esistente.

**Modifiche da fare durante la copia:**
- Mantieni il blocco `:root { ... }` identico (i CSS variables)
- Mantieni il reset `.sl-root` e successivi components (`.sl-mono`, `.sl-btn`, `.sl-link`, `.sl-area`, `.sl-acc`)
- **Sposta i selettori components** da `tokens.css` a un file dedicato `assets/css/components.css` (mantieni solo `:root` + reset `.sl-root` in `tokens.css`)

Risultato atteso:
```
assets/css/
├── tokens.css      ← :root + .sl-root reset
├── base.css        ← typography setup, container, layout primitives
└── components.css  ← .sl-mono, .sl-btn, .sl-link, .sl-area, .sl-acc
```

---

## Task 2 — Scrivere `base.css` (15 min)

Contenuti minimi:

- Fonts loading: `@font-face` per Playfair Display 400/700 e DM Sans 400/500/700, file in `assets/fonts/` (WOFF2). Per ora se i WOFF2 non sono presenti, lascia placeholder commentato `/* TODO Duccio: carica i WOFF2 in assets/fonts/ */` e usa Google Fonts inline come fallback temporaneo via `@import` con `display=swap`
- Container utility: `.sl-container { max-width: var(--container-max); margin: 0 auto; padding-inline: var(--container-pad); }`
- Section utility: `.sl-section { padding-block: clamp(64px, 10vw, 128px); }`
- Mono utility (già in components.css se l'hai spostato lì)
- Helpers: `.sl-flow > * + * { margin-top: 1.5em; }` per ritmo verticale prose
- Reduced motion: `@media (prefers-reduced-motion: reduce) { * { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; } }`

---

## Task 3 — Scrivere CSS specifici delle 7 sezioni Homepage (30 min)

Estrai dal `homepage-desktop.jsx` lo styling delle 7 sezioni e scrivilo in `assets/css/sections.css` (importato dopo components.css). Ogni sezione ha la sua classe `.sl-section--<nome>`:

1. `.sl-hero` — minHeight 820, grid 8fr 4fr, headline gigante (clamp 80-132px), reveal con classi `.sl-revealed`
2. `.sl-areas` — la lista 19 aree, sticky preview a destra, filter pillole, tier-1 con `::first-letter` accent
3. `.sl-studio` — prose editoriale con drop-cap (`::first-letter` 84px float), max-width 640, marginLeft 20%
4. `.sl-team` — grid 12 col, 4 lawyer asimmetrici (offset specifici come nel JSX), placeholder con gradient + filter grayscale che rimuove al hover
5. `.sl-cases` — grid 200px 1fr 200px, casi rappresentativi tipografici
6. `.sl-press` — strip earned media su `--surface`, flex wrap
7. `.sl-contact` + `.sl-footer` — contatti tipografici grandi, footer 3 colonne con tema scuro `--primary`

**Critico:** non reinventare lo styling — leggilo dal JSX e traducilo in CSS classes.

Mobile breakpoint a 768px: stack tutto in colonna singola, riduci le scale tipografiche, replica il behavior dal `homepage-mobile.jsx`.

---

## Task 4 — Configurare GSAP + Lenis (15 min)

In `inc/enqueue.php`, attiva gli enqueue commentati. CDN da usare:

```
GSAP core 3.12.5:    https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js
ScrollTrigger:       https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js
SplitText (free 4Q26): https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/SplitText.min.js
Lenis 1.1.13:        https://cdnjs.cloudflare.com/ajax/libs/lenis/1.1.13/lenis.min.js
```

Per ora **senza SRI hash** (li aggiungiamo dopo, in fase di hardening). Marca con `defer` tutti.

Enqueue:
- `wp_enqueue_script('saltelli-gsap', ..., [], null, ['strategy' => 'defer', 'in_footer' => true])`
- idem per ScrollTrigger, SplitText, Lenis
- `wp_enqueue_script('saltelli-main', SALTELLI_THEME_URI . '/assets/js/main.js', ['saltelli-gsap', 'saltelli-lenis'], SALTELLI_THEME_VERSION, ['strategy' => 'defer'])`

---

## Task 5 — Scrivere `main.js` con animazioni Frame 1 (30 min)

Entry point in `assets/js/main.js`. Struttura:

```js
(function() {
  'use strict';

  // 1. Lenis smooth scroll (lerp 0.1)
  const lenis = new Lenis({ lerp: 0.1, smoothWheel: true });
  function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
  requestAnimationFrame(raf);

  // 2. GSAP + ScrollTrigger registration
  gsap.registerPlugin(ScrollTrigger, SplitText);

  // 3. Hero text reveal — SplitText word-by-word
  // (replica logica revealed dal JSX: stagger 60-80ms, ease editorial, opacity + translateY 40)

  // 4. Sezioni in scroll — fade-in 400ms + translateY(24→0) trigger 80% viewport

  // 5. List items area pratica — stagger 80ms

  // 6. Header solid-on-scroll — class toggle dopo 80px scroll

  // 7. Hover area pratica — translateX 8px + linea bronzo (gestito da CSS, qui nulla)

  // 8. Mobile: skippa SplitText e translate aggressive
})();
```

Non scrivere il codice in modo da pesare — meglio leggibile e ~150 righe. Tieni un comment header che indica "TODO Style & Animation Agent: aggiungere refinement post-demo cliente" per le polish future.

---

## Task 6 — Verifica finale (10 min)

Esegui:

```bash
# 1. Tema attivo, no errori PHP
docker compose run --rm wpcli theme list --status=active 2>&1
docker exec saltelli-wp tail -20 /var/www/html/wp-content/debug.log 2>/dev/null

# 2. CSS files raggiungibili
curl -sI http://localhost:8080/wp-content/themes/saltelli/assets/css/tokens.css | head -1
curl -sI http://localhost:8080/wp-content/themes/saltelli/assets/css/base.css | head -1
curl -sI http://localhost:8080/wp-content/themes/saltelli/assets/css/components.css | head -1
curl -sI http://localhost:8080/wp-content/themes/saltelli/assets/css/sections.css | head -1

# 3. JS files raggiungibili
curl -sI http://localhost:8080/wp-content/themes/saltelli/assets/js/main.js | head -1

# 4. CSS variables effettivamente nel DOM
curl -s http://localhost:8080/ | grep -E "(font-display|--background|--primary)" | head -3
```

Tutti devono dare 200 OK / output non vuoto.

---

## Coordinamento con gli altri agent

**File scope tuoi (esclusivi):**
- `assets/css/tokens.css`
- `assets/css/base.css`
- `assets/css/components.css` (NEW)
- `assets/css/sections.css` (NEW)
- `assets/js/main.js`
- `assets/js/lenis-init.js` (puoi cancellarlo se main.js lo include — ma mantienilo se serve modularità)
- `assets/js/gsap-init.js` (idem)

**File condiviso che modifichi tu:**
- `inc/enqueue.php` — solo gli enqueue, NON l'HTML head template (quello è di Theme Architect)

**Se trovi che un file PHP sta bloccando il rendering del CSS**, fermati e segnala a Duccio. Non toccarlo.

---

## Report finale a Duccio

Quando hai finito, scrivi un report breve in chat:

1. ✅/❌ stato dei test del Task 6
2. Lista file creati/modificati
3. Eventuali differenze prese rispetto al `tokens.css` di Claude Design (es. typo, valori da correggere)
4. Note per Theme Architect (es. "Il body deve avere class `sl-root` per attivare il reset")
5. Note per gli agent successivi (es. "Animazioni hero richiedono che `<h1>` abbia data-attribute X")

Poi **fermati**. Aspetta istruzioni.

---

*v1.0 — 2026-04-29 SHIP MODE 24H — Direttore d'orchestra: Claude (chat).*
