# Prompt — Polish Agent (Step B post-beta v0.2.1)

> **Per Claude Code in nuova sessione (singolo pane).** Apri `saltelli-wp/`, leggi questo file, eseguilo. Lavoro mirato, ~30-45 minuti stimati.

---

## Tu sei

Il **Polish Agent** del tema Saltelli. Il tema è già **funzionante e renderizzato correttamente** in beta locale (v0.2.1-beta-local, commit `5993b17`). Il tuo lavoro è raffinare 4 polish P2 specifici identificati dall'orchestrator (Claude in chat) durante la review visiva con Claude in Chrome.

Non devi rifare il design, non devi toccare PHP template, non devi creare nuove sezioni. Solo **JS interattività** + un piccolo CSS fix.

---

## Letture obbligatorie

1. `CLAUDE.md` — hard constraints
2. `.claude/knowledge/design/sessione-1/homepage-desktop.jsx` — riferimento del comportamento JS atteso (sezioni 'AREE' e 'AVVOCATI' nel JSX hanno useState hover, replicalo in vanilla JS)
3. `wp-content/themes/saltelli/assets/js/main.js` — entrypoint dove aggiungi i nuovi handler
4. `wp-content/themes/saltelli/assets/css/sections.css` — solo per consultazione, qui hai gli stili reveal/transitions già pronti

---

## Hard rules

| Rule | Reason |
|---|---|
| Solo JavaScript vanilla, no jQuery, no nuove librerie esterne | GSAP + Lenis bastano |
| Ogni hook a `DOMContentLoaded` o equivalente, no `body onload` | WP standard |
| `prefers-reduced-motion: reduce` skippa animazioni | A11y |
| Mobile (<768px) skippa SplitText e hover binding (touch-only) | Performance + UX |
| Tutto idempotente: re-init non duplica handler | Robustness |
| Nessun `console.log` in production code | Cleanup |

---

## Task 1 — Sticky preview hover su `.sl-area` (10 min)

**Cosa serve:** quando l'utente passa il cursore su una `.sl-area` della lista 19 aree, popolare `.sl-area__preview` con il lead della area corrente.

**Data-attributes già presenti su ogni `.sl-area`** (verificato nel DOM live):
- `data-area-num` — numero ordinale (es. "01")
- `data-area-cat` — categoria slug (es. "tributario")
- `data-area-lead` — lead breve (es. "Cartelle esattoriali, contenzioso fiscale, accertamenti.")
- `data-area-label` — label tier ("Tier 1 · approfondimento" o cat name)

**Comportamento atteso (replica JSX):**
```javascript
// Pseudocodice
const areas = document.querySelectorAll('.sl-area');
const preview = document.querySelector('.sl-area__preview');
const previewDefault = preview.innerHTML; // salva il placeholder default

areas.forEach(area => {
    area.addEventListener('mouseenter', () => {
        preview.innerHTML = `
            <div class="sl-mono">${area.dataset.areaLabel}</div>
            <p class="sl-area__preview-lead">${area.dataset.areaLead}</p>
            <a class="sl-btn" href="${area.querySelector('a').href}">
                Approfondisci <span class="arrow">→</span>
            </a>
        `;
    });
    area.addEventListener('mouseleave', () => {
        // Solo se nessun'altra area è hovered
        if (!document.querySelector('.sl-area:hover')) {
            preview.innerHTML = previewDefault;
        }
    });
});
```

**Skippa su mobile (<768px) o se preview non esiste.** Touch device non hanno hover.

---

## Task 2 — SplitText reveal hero (10-15 min)

**Cosa serve:** Le 3 parole `<span class="sl-hero__word">` dentro `.sl-hero__headline[data-split-reveal]` devono apparire con stagger 80ms al page load.

**Stato attuale:** GSAP è caricato (CDN, vedi `enqueue.php`), SplitText è caricato. Le parole hanno `opacity: 1 !important` forzato dal compat-shim FIX D — **rimuovilo** per le parole che vuoi animare e riapplica solo come fallback se GSAP non parte.

**Comportamento atteso (replica JSX `revealed` state):**
```javascript
if (typeof gsap === 'undefined' || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    // fallback: opacity 1, no animation (niente cambia)
    return;
}

// Imposta lo stato iniziale
gsap.set('.sl-hero__word', { opacity: 0, y: 40 });

// Anima
gsap.to('.sl-hero__word', {
    opacity: 1,
    y: 0,
    duration: 0.7,
    stagger: 0.08,
    ease: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
    delay: 0.08
});
```

**Importante:** in `sections.css` rimuovi (o gating) il `!important` su `.sl-hero__word { opacity: 1 !important; }` aggiunto da FIX D, altrimenti GSAP non riesce a settare `opacity: 0`. Soluzione consigliata: usa una classe gate `.js-reveal-ready` aggiunta da JS quando GSAP è pronto, e applica il `!important` SOLO se `:not(.js-reveal-ready) .sl-hero__word`.

```css
/* sections.css — sostituisci il blocco FIX D opacity:1 !important con: */
.sl-hero__word {
    /* default visibile per no-JS / fallback */
    opacity: 1;
}
html:not(.js-reveal-ready) .sl-hero__word {
    /* solo se JS non parte, manteniamo visibili */
}
html.js-reveal-ready .sl-hero__word {
    /* GSAP gestisce; non forziamo nulla */
}
```

In JS:
```javascript
document.documentElement.classList.add('js-reveal-ready');
gsap.set('.sl-hero__word', { opacity: 0, y: 40 });
// poi anima
```

---

## Task 3 — Scroll-triggered fade-in sezioni (10 min)

**Cosa serve:** ogni sezione (`.sl-areas`, `.sl-studio`, `.sl-team`, `.sl-cases`, `.sl-press`, `.sl-contact`) fa fade-in + translateY(24px → 0) al 80% di entry nel viewport.

**Implementazione GSAP ScrollTrigger:**
```javascript
gsap.registerPlugin(ScrollTrigger);

const sections = document.querySelectorAll('.sl-areas, .sl-studio, .sl-team, .sl-cases, .sl-press, .sl-contact');

sections.forEach(section => {
    gsap.from(section, {
        opacity: 0,
        y: 24,
        duration: 0.6,
        ease: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
        scrollTrigger: {
            trigger: section,
            start: 'top 80%',
            toggleActions: 'play none none none' // play once
        }
    });
});
```

**Skippa su `prefers-reduced-motion`.** ScrollTrigger ha `matchMedia` per gestirlo nativamente:

```javascript
ScrollTrigger.matchMedia({
    "(prefers-reduced-motion: no-preference)": function() {
        // animazioni sezioni
    }
});
```

---

## Task 4 — List items area-pratica stagger reveal (5 min)

**Cosa serve:** quando la sezione `.sl-areas` entra in viewport, le 19 `.sl-area` rivelano una alla volta con stagger 80ms.

```javascript
gsap.from('.sl-areas .sl-area', {
    opacity: 0,
    y: 16,
    duration: 0.5,
    stagger: 0.08,
    ease: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
    scrollTrigger: {
        trigger: '.sl-areas__grid',
        start: 'top 70%',
        toggleActions: 'play none none none'
    }
});
```

Skippa su mobile (sotto 768px), per perf.

---

## Test (Definition of Done)

```bash
# 1. JS file è valido e no syntax error
docker exec saltelli-wp php -l /var/www/html/wp-content/themes/saltelli/assets/js/main.js 2>&1 || echo "main.js è JS, php -l non si applica — alternativa node:"
node --check wp-content/themes/saltelli/assets/js/main.js 2>&1 || echo "(skip se node non disponibile)"

# 2. Tema attivo, no errori PHP
docker exec saltelli-wp tail -10 /var/www/html/wp-content/debug.log 2>/dev/null || echo "Log vuoto, perfetto"

# 3. Console browser pulita (verifica manuale: Cmd+Opt+J e ricarica home)
echo "Verifica manuale: ricarica http://localhost:8080/ con Cmd+Shift+R, controlla DevTools Console — ZERO errori attesi"
```

---

## Bump version + cache flush al termine

```bash
# Aggiorna a 0.3.0-beta-polish
sed -i.bak 's/Version: 0.2.1-beta-local/Version: 0.3.0-beta-polish/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.2.1-beta-local')/define('SALTELLI_THEME_VERSION', '0.3.0-beta-polish')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/style.css.bak wp-content/themes/saltelli/functions.php.bak
docker compose run --rm wpcli cache flush
```

---

## Report finale

1. ✅/❌ ciascuno dei 4 task
2. File modificati
3. Eventuali decisioni autonome (es. "Ho aggiunto un debounce al hover area per evitare flicker")
4. Console browser pulita? (verifica manuale richiesta)
5. Animazioni fluide su Chrome / Safari? (verifica manuale)

Poi **fermati**. Non passare ad altri polish, aspetta istruzioni per Step C.

---

*v1.0 — 2026-04-29 SHIP MODE post-beta v0.2.1*
