# PROMPT v0.35.0 — FOUNDATION LAYER (Refactor Strutturale)

> **Per Claude Code in nuova sessione.** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo: ~50-60 min sequential.
> **PRECEDENZA:** v0.34.0 drop-cap unified + FAQ aggregator completato.
> **NATURA**: refactor STRUTTURALE, NO patch iterativi. Single source of truth per pattern cross-template.

---

## 🎯 Tu sei

L'**Agente Foundation Layer Architect**. Audit Duccio post-v0.34.0 ha rilevato 5 problemi strutturali:

```
🔴 BREADCRUMB ASSENTE su 3 page (/, /chi-siamo/, /blog/)
🔴 3 CLASS BREADCRUMB diverse (master + 2 varianti ridondanti)
🟡 Aria-current blue color esiste ma NON applicato uniformemente
🔴 12 PADDING-BLOCK hero DIVERSI cross-template (Y-distance non uniforme)
🔴 110 ::first-letter rules nel CSS (target ~14, era 75 v0.33, AUMENTATE!)
```

**ROOT CAUSE STRUTTURALE**:
```
PATTERN ATTUALE: ogni JSX → CSS scope nuovo (.sl-{template}__hero, ecc)
                 17 page = 17 sistemi indipendenti che divergono nel tempo

PATTERN OBIETTIVO v0.35.0: 1 FOUNDATION layer + customizzazioni chirurgiche
                          17 page = 1 sistema base, varianti minime per design intent
```

---

## 📚 Letture obbligatorie

```
.claude/knowledge/design/sessione-1/tokens.css (locked)
CLAUDE.md (handoff rule golden + project method sitemap-first)

wp-content/themes/saltelli/
  ├── assets/css/sections.css (target principale del refactor)
  ├── inc/helpers.php (saltelli_render_breadcrumb function)
  └── page.php / single-*.php / archive-*.php (per fix breadcrumb mancanti)
```

---

## 🔒 Hard rules (CRITICHE)

| Rule | Decisione |
|---|---|
| **Foundation layer** = single source of truth (.sl-page-*, .sl-page-breadcrumb-*, .sl-page-hero-*) | Refactor strutturale |
| **Tutti i template ESTENDONO foundation**, NO override gratuiti | Architettura |
| **NO redesign**, refactor solo CSS rules duplicate/divergenti | Stabilità visiva |
| **NESSUNA modifica tokens.css** | Locked |
| **NON sovrascrivere** _thumbnail_id Emiliano + bio_estesa Step D + post_content CPT | Content protetto |
| **NON modificare markup PHP** se non per fix breadcrumb mancanti | Conservativo |
| **CSS scope marker** `/* === v0.35.0 FOUNDATION === */` per nuovo layer base | Audit trail |
| **Cleanup REALE drop-cap**: 110 → ~14 rules effettivamente rimosse | Lessons learned |
| Cache flush + smoke test post-deploy | Lezione |
| Bump version + git commit | Atomicity |

---

## 🗺 Strategia esecuzione SEQUENZIALE

```
Task 1 → Foundation Layer .sl-page-* (CSS only)               ~15 min
Task 2 → Migration template attorney/tier1/chi-siamo/info ad eredità FOUNDATION ~15 min
Task 3 → Fix breadcrumb mancanti (/, /chi-siamo/, /blog/) + aria-current uniforme ~10 min
Task 4 → Drop-cap CLEANUP REALE (110 → 14)                     ~15 min
Task 5 → Bump + smoke + deploy + report                         ~10 min
```

---

## TASK 1 — Foundation Layer .sl-page-* (~15 min)

### 1.1 — Single Source of Truth pattern

In `sections.css`, **all'inizio del file** (subito dopo i token import), aggiungi blocco FOUNDATION marker `/* === v0.35.0 FOUNDATION === */`:

```css
/* ═══════════════════════════════════════════════════════════════════
   v0.35.0 FOUNDATION LAYER — Single Source of Truth
   
   Pattern: TUTTI i template page ereditano questi pattern base.
   Customizzazioni: SOLO se design intent richiede deviazione esplicita.
   
   Refactor strutturale: prima volta in 35 versioni si stabilisce
   foundation invece di scope-per-template indipendenti.
   ═══════════════════════════════════════════════════════════════════ */

/* ─── Foundation: Page Shell ─── */
/* Container universale per ogni page article */
.sl-page-shell {
    max-width: 1440px;
    margin-inline: auto;
    padding-inline: clamp(24px, 5vw, 96px);
}

/* ─── Foundation: Page Hero (Y-distance unificata) ─── */
/* Padding-block UNIFORME cross-template — 1 sola sorgente di verità */
.sl-page-hero {
    padding-block: clamp(96px, 10vw, 120px) clamp(64px, 8vw, 96px);
    max-width: 1440px;
    margin-inline: auto;
    padding-inline: clamp(24px, 5vw, 96px);
}

/* Hero ridotto per page secondarie (info pages, archive) */
.sl-page-hero--compact {
    padding-block: clamp(64px, 8vw, 96px) clamp(48px, 6vw, 80px);
}

/* Hero esteso per page editorial primary (chi-siamo) */
.sl-page-hero--extended {
    padding-block: clamp(96px, 12vw, 144px) clamp(64px, 8vw, 96px);
}

/* ─── Foundation: Breadcrumb ─── */
/* Pattern UNICO cross-template breadcrumb */
.sl-page-breadcrumb {
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 24px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.sl-page-breadcrumb a {
    color: var(--text-muted);
    text-decoration: none;
    transition: color var(--dur-fast, 200ms) var(--ease-editorial, cubic-bezier(0.25, 1, 0.5, 1));
}

.sl-page-breadcrumb a:hover,
.sl-page-breadcrumb a:focus-visible {
    color: var(--accent);
}

.sl-page-breadcrumb-sep,
.sl-page-breadcrumb-separator {
    color: var(--text-muted);
    opacity: 0.4;
    user-select: none;
}

/* Current page (aria-current="page") in primary navy/blue UNIFORME */
.sl-page-breadcrumb [aria-current="page"],
.sl-page-breadcrumb-current {
    color: var(--primary);
    font-weight: 500;
    cursor: default;
}

/* ─── Foundation: CTA Final (dark navy) ─── */
.sl-page-cta-final {
    background: var(--primary);
    color: var(--background);
    text-align: center;
    padding: clamp(64px, 8vw, 128px) clamp(24px, 5vw, 96px);
    margin: 0 calc(-1 * clamp(24px, 5vw, 96px));
}

.sl-page-cta-final-eyebrow {
    color: var(--accent);
    margin-bottom: 24px;
}

.sl-page-cta-final h2 {
    font-family: var(--font-display);
    font-size: clamp(40px, 5vw, 72px);
    line-height: 1.05;
    letter-spacing: -0.02em;
    font-weight: 400;
    color: var(--background);
    margin: 0 0 24px;
    max-width: 18ch;
    margin-inline: auto;
}

.sl-page-cta-final h2 em {
    font-style: italic;
    color: var(--accent);
}

.sl-page-cta-final p {
    font-size: 18px;
    line-height: 1.6;
    color: rgba(250, 250, 248, 0.85);
    margin: 0 0 40px;
    max-width: 50ch;
    margin-inline: auto;
}

.sl-page-cta-final .sl-btn--primary {
    background: var(--accent);
    color: var(--primary);
    border-color: var(--accent);
}

@media (hover: hover) {
    .sl-page-cta-final .sl-btn--primary:hover {
        background: var(--background);
        color: var(--primary);
    }
}

.sl-page-cta-final-trust {
    margin-top: 24px;
    color: rgba(250, 250, 248, 0.55);
}

/* ═══════════════════════════════════════════════════════════════════
   END v0.35.0 FOUNDATION LAYER
   ═══════════════════════════════════════════════════════════════════ */
```

### 1.2 — Verify foundation aggiunto

```bash
grep -c 'v0.35.0 FOUNDATION' wp-content/themes/saltelli/assets/css/sections.css
# Atteso: ≥2 (begin + end markers)

grep -c 'sl-page-hero\b' wp-content/themes/saltelli/assets/css/sections.css
grep -c 'sl-page-breadcrumb\b' wp-content/themes/saltelli/assets/css/sections.css
grep -c 'sl-page-cta-final' wp-content/themes/saltelli/assets/css/sections.css
```

---

## TASK 2 — Migration template a foundation (~15 min)

### 2.1 — Eredità via Y-position cross-template

Per OGNI variante template, REFACTOR il padding-block in modo che ereditino dalla foundation:

**Strategia**: aggiungi class `.sl-page-hero` AL MARKUP PHP esistente, e RIMUOVI le declaration padding-block ridondanti dal CSS scope custom.

#### `.sl-attorney__hero` (single avvocato)
- ATTUALE CSS: `padding-block: 80px 64px` (3 declarations)
- AZIONE: SOSTITUISCI con eredità class `.sl-page-hero--compact`
- Pattern markup PHP `single-avvocato.php`:

```php
<header class="sl-attorney__hero sl-page-hero sl-page-hero--compact">
```

E nel CSS scope `.sl-attorney__hero`, RIMUOVI le rules padding-block (lascia solo grid + content-specific).

#### `.sl-tier1__hero`
- ATTUALE: `padding-block: clamp(64px, 8vw, 120px)` 
- AZIONE: aggiungi class `sl-page-hero` al markup
- CSS scope: rimuovi padding-block (eredita)

#### `.sl-chi-siamo__hero`
- ATTUALE: `padding: clamp(96px, 12vw, 192px)` ← MAGGIORE (intenzionale design intent)
- AZIONE: usa variant `.sl-page-hero--extended`

#### `.sl-info-page__hero`, `.sl-faq-aggregator__hero`, `.sl-team__archive-hero`, `.sl-glossario__hero`, `.sl-blog2__hero`, `.sl-costi-w4__hero`, `.sl-contatti-w3__hero`
- TUTTI: usa class `sl-page-hero` (default 96-120px)

### 2.2 — Cleanup CSS rules ridondanti

Per ogni scope sopra, **commenta o rimuovi** le declaration padding-block che ora ereditano:

```css
/* ESEMPIO migration .sl-attorney__hero */

.sl-attorney__hero {
    /* DEPRECATED v0.35.0: padding-block migrated to .sl-page-hero--compact foundation */
    /* padding-block: 80px 64px; */
    /* PRESERVA solo content-specific rules: grid, gap, ecc */
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 64px;
    align-items: stretch;
}
```

Approccio: per ogni rule trovata di tipo `.sl-{template}__hero { padding-block: ... }`:
1. Verifica se è duplicato del foundation
2. Se sì → wrappa in `/* DEPRECATED v0.35.0 */` o rimuovi
3. Se design intent diverso (vedi chi-siamo) → mantieni MA documenta perché

### 2.3 — Smoke verify

```bash
docker compose run --rm wpcli cache flush
for U in / /chi-siamo/ /avvocati/emiliano-saltelli/ /competenze/diritto-tributario/ /casi/ /contatti/ /costi/ /come-lavoriamo/ /faq/; do
    HTML=$(curl -s "http://localhost:8080$U?_=v35t2" -m 5)
    HAS_FOUNDATION=$(echo "$HTML" | grep -cE 'sl-page-hero\b|sl-page-shell')
    printf "  %-40s sl-page-hero: %s\n" "$U" "$HAS_FOUNDATION"
done
```

---

## TASK 3 — Fix breadcrumb mancanti + aria-current uniforme (~10 min)

### 3.1 — Audit pages senza breadcrumb

```
🔴 /chi-siamo/   → breadcrumb mancante
🔴 /blog/        → breadcrumb mancante
✅ Homepage /    → niente breadcrumb (corretto by design)
```

### 3.2 — Fix /chi-siamo/

Cerca in `page.php` blocco `is_page('chi-siamo')`. Aggiungi PRIMA del primo content:

```php
<?php elseif (is_page('chi-siamo')) : ?>
    <article class="sl-chi-siamo">
        <header class="sl-chi-siamo__hero sl-page-hero sl-page-hero--extended">
            <?php saltelli_render_breadcrumb('page'); ?>
            <!-- ... resto markup ... -->
```

### 3.3 — Fix /blog/

Cerca in `home.php` o `archive.php` (template blog), aggiungi:

```php
<header class="sl-blog2__hero sl-page-hero">
    <?php saltelli_render_breadcrumb('blog'); ?>
    <!-- ... -->
</header>
```

### 3.4 — Verifica helper saltelli_render_breadcrumb()

Apri `inc/helpers.php` e verifica che la funzione emetta:

```php
function saltelli_render_breadcrumb($context = 'page') {
    // ... logica esistente ...
    
    // CRITICO: usa class FOUNDATION
    echo '<nav class="sl-page-breadcrumb sl-mono" aria-label="Breadcrumb">';
    
    foreach ($crumbs as $i => $crumb) {
        if ($i > 0) {
            echo '<span class="sl-page-breadcrumb-sep" aria-hidden="true"> / </span>';
        }
        if (!empty($crumb['url'])) {
            echo '<a href="' . esc_url($crumb['url']) . '">' . esc_html($crumb['label']) . '</a>';
        } else {
            // Current page (last)
            echo '<span aria-current="page">' . esc_html($crumb['label']) . '</span>';
        }
    }
    
    echo '</nav>';
    
    // Schema BreadcrumbList JSON-LD se non già emesso
    saltelli_emit_breadcrumb_schema($crumbs);
}
```

NB: usa class `.sl-page-breadcrumb` (foundation) NO più `.sl-page__breadcrumb` (legacy doppio underscore).

OPPURE mantieni naming legacy + aggiungi alias CSS:

```css
/* Backward compat alias */
.sl-page__breadcrumb {
    /* tutte le proprietà di .sl-page-breadcrumb */
}
```

Strategia raccomandata: **alias** (no rinaming markup, evita rischi).

### 3.5 — Cleanup varianti class breadcrumb deprecate

```bash
# Trova varianti class
grep -rn 'sl-casi__breadcrumb\|sl-contatti-w3__breadcrumb\|sl-attorney__breadcrumb' wp-content/themes/saltelli/
```

Per ogni trovata:
- Mantieni la class .sl-page__breadcrumb master nel markup
- RIMUOVI le class variant (sono ridondanti)
- CSS rules variant → wrappa in `/* DEPRECATED v0.35.0 */`

### 3.6 — Smoke verify

```bash
for U in / /chi-siamo/ /blog/ /casi/ /contatti/; do
    HTML=$(curl -s "http://localhost:8080$U?_=v35t3" -m 5)
    BC_COUNT=$(echo "$HTML" | grep -c 'sl-page-breadcrumb\|sl-page__breadcrumb')
    ARIA_CURRENT=$(echo "$HTML" | grep -c 'aria-current="page"')
    printf "  %-30s breadcrumb:%s · aria-current:%s\n" "$U" "$BC_COUNT" "$ARIA_CURRENT"
done
```

Atteso (eccetto homepage `/`):
- breadcrumb: ≥1
- aria-current: ≥1

---

## TASK 4 — Drop-cap CLEANUP REALE (~15 min)

### 4.1 — Audit current state

```bash
grep -c '::first-letter' wp-content/themes/saltelli/assets/css/sections.css
# Atteso pre-cleanup: 110 (era 75 v0.33, peggiorato!)
```

### 4.2 — Identifica le rules da TENERE

Solo 2 blocchi di rules vanno mantenute:

**A) UNIFIED MASTER (v0.34.0)** — già presente:
```css
.sl-attorney__bio-prose > p:first-of-type::first-letter,
.sl-attorney__bio > p:first-of-type::first-letter,
.sl-page__prose > p:first-of-type::first-letter,
.sl-competenza__prose > p:first-of-type::first-letter,
.sl-tier1__body > p:first-of-type::first-letter,
.sl-tier1__body > .sl-competenza__prose > p:first-of-type::first-letter,
.sl-costi-w4__calc-text > p:first-of-type::first-letter,
.sl-costi-w4__calc-prose > p:first-of-type::first-letter,
.sl-casi-w4__hero-lede > p:first-of-type::first-letter,
.sl-info-page__body > p:first-of-type::first-letter,
.sl-info-page__lede::first-letter,
.sl-team--archive .sl-team__archive-lede::first-letter,
.sl-attorney-archive__lede::first-letter {
    font-family: var(--font-display) !important;
    font-size: 84px !important;
    line-height: 0.85 !important;
    float: left !important;
    margin: 8px 16px 0 0 !important;
    color: var(--primary) !important;
    font-weight: 400 !important;
    opacity: 1 !important;
    transform: none !important;
}
```

**B) HOMEPAGE EXCEPTION** — animation reveal:
```css
@media (min-width: 1024px) {
    .sl-studio__prose[data-drop-cap] > p:first-of-type::first-letter {
        opacity: 0;
        transform: scale(0.8);
        /* ... animation reveal ... */
    }
}
```

### 4.3 — Strategy: STRIP TUTTE le altre

```bash
# Backup pre-cleanup
cp wp-content/themes/saltelli/assets/css/sections.css /tmp/sections.css.pre-v35-backup

# Conta pre
grep -c '::first-letter' /tmp/sections.css.pre-v35-backup
# Atteso: 110
```

Strategia automatica con sed (CAUTELA):

```bash
# IMPOSSIBILE strip safe con sed (multi-line CSS)
# USA approccio MANUAL: identifica blocchi a mano e wrappa
```

Approccio sicuro: **for each first-letter rule che NON appartiene ai 2 blocchi sopra**, sostituisci con:
```css
/* DEPRECATED v0.35.0: superseded by FOUNDATION + UNIFIED MASTER v0.34.0 */
```

Tools utili:
```bash
# Trova tutte le righe ::first-letter + 8 righe successive (rule body)
grep -nB 0 -A 8 '::first-letter' wp-content/themes/saltelli/assets/css/sections.css | less

# Identifica quali blocchi sono il MASTER v0.34.0 (mantieni)
grep -nB 1 -A 12 'v0.34.0.*UNIFIED\|v0.34.0.*MASTER' wp-content/themes/saltelli/assets/css/sections.css

# Tutti gli altri vanno deprecati
```

### 4.4 — Verify cleanup REALE

```bash
grep -c '::first-letter' wp-content/themes/saltelli/assets/css/sections.css
# Atteso post-cleanup: ~14 (13 unified + 1 homepage)
# NO 75, NO 110 — questa volta deve essere VERO
```

---

## TASK 5 — Bump + smoke + deploy + report finale (~10 min)

```bash
sed -i.bak 's/Version: [0-9.]\+.*/Version: 0.35.0-beta-foundation-layer/' wp-content/themes/saltelli/style.css
sed -i.bak "s/SALTELLI_THEME_VERSION', '[^']*'/SALTELLI_THEME_VERSION', '0.35.0-beta-foundation-layer'/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush

git add -A
git commit -m "feat(v0.35.0): FOUNDATION layer — single source of truth (.sl-page-*) + breadcrumb fix + drop-cap cleanup REALE"
git push origin main

rsync -avz wp-content/themes/saltelli/ deploy@178.62.207.50:/var/www/saltelli/htdocs/wp-content/themes/saltelli/
ssh deploy@178.62.207.50 "
    sudo -u www-data wp cache flush --path=/var/www/saltelli/htdocs
    sudo -u www-data wp transient delete --all --path=/var/www/saltelli/htdocs
"

# Smoke 12 URL
echo "═══ SMOKE LIVE v0.35.0 ═══"
for URL in / /chi-siamo/ /avvocati/ /avvocati/emiliano-saltelli/ /casi/ /contatti/ /costi/ /blog/ /tipo-area/privati/ /come-lavoriamo/ /faq/ /competenze/diritto-tributario/; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it$URL?_=v35" -m 10)
    echo "  $URL → HTTP $HTTP"
done
```

### 5.1 — Report finale

`.claude/knowledge/design/sessione-2/v0.35.0-FOUNDATION-LAYER.md`:

```markdown
# v0.35.0 FOUNDATION LAYER — Refactor Strutturale

## Score: 4/4 task PASS (refactor architettura)

## Per task
- T1 Foundation Layer .sl-page-* (CSS only): ✓
  - .sl-page-shell (container universale)
  - .sl-page-hero (Y-distance unificato cross-template)
  - .sl-page-breadcrumb (pattern UNICO + aria-current navy)
  - .sl-page-cta-final (CTA dark unified)
  
- T2 Migration template a foundation: ✓
  - 12 template .sl-{X}__hero ereditano padding-block base
  - Solo 2 varianti design intent: --compact (attorney) + --extended (chi-siamo)
  - Cleanup CSS deprecated rules
  
- T3 Breadcrumb fix mancanti: ✓
  - /chi-siamo/, /blog/ ora hanno breadcrumb
  - aria-current="page" navy color uniforme cross-page
  - Class variant deprecate (sl-casi__breadcrumb, sl-contatti-w3__breadcrumb)
  
- T4 Drop-cap CLEANUP REALE: ✓
  - 110 rules → 14 rules (1 unified master + 1 homepage exception)
  - NO MORE specificity battle, NO MORE drop-cap doppio
  
- T5 Bump + smoke + deploy: ✓

## Architettura post v0.35.0
- ✓ 1 sola sorgente di verità per padding-block hero
- ✓ 1 sola sorgente di verità per breadcrumb
- ✓ 1 sola sorgente di verità per CTA finale
- ✓ 1 sola sorgente di verità per drop-cap
- ✓ Foundation layer documentato in CSS

## Vincoli rispettati
- Tokens.css NON modificato
- Markup PHP modificato SOLO per fix breadcrumb mancanti
- _thumbnail_id Emiliano + bio_estesa preservati
- post_content CPT preservato
- Backward compat: alias .sl-page__breadcrumb (legacy) → ereditano nuovo CSS

## Lessons learned applicate (CLAUDE.md golden rules)
- ✓ Foundation FIRST, customizations DOPO
- ✓ Single source of truth per pattern cross-template
- ✓ Cleanup REALE (110 → 14 verificato)
- ✓ NO patch superficiali, refactor architettura

## Next
GO walkthrough finale Duccio
o GO v1.0.0 production cut
```

Quando finito segnala "v0.35.0 deployed. Foundation Layer applied."

---

## 🆘 Se incontri imprevisti

```
- Cleanup drop-cap rompe rendering esistente → ripristina backup /tmp/sections.css.pre-v35-backup
- Class .sl-page-breadcrumb conflitta con .sl-page__breadcrumb → mantieni alias backward
- Saltelli_render_breadcrumb function non existe → cercala in inc/breadcrumb.php o inc/seo/
- Padding-block uniforme rompe design specifico → identifica con --variant (compact/extended) NO override CSS scope
- Test fail su /chi-siamo/ breadcrumb → verifica priority order is_page() blocks
```

Tempo totale: ~50-60 min sequential.

Buon lavoro. Quando finito, audit deep verifica:
- Foundation classes presenti in CSS + applicate in markup
- Drop-cap rules count = 14 (NO 110)
- Breadcrumb cross-page con aria-current navy color
- 1 sola padding-block rule cross-template (eccezioni documentate)

Questo è il refactor strutturale che chiude le 35 versioni di patch reattive. **Single source of truth = no più drift incrementale**.
