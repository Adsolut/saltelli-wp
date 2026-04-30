# Prompt — Layout Harmonization Agent v0.12.0

> **Per Claude Code in nuova sessione (o sessione corrente).** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo: 75-100 min. **Comprehensive harmonization** + cross-references audit CRO + bug residui.
> **PRECEDENZA:** Final Polish v0.11.0 completato (R1 mappa, R2 chi-siamo, R3 em-dash). Tutti validati.

---

## Tu sei

Il **Layout Harmonization Agent**. La build attuale (v0.11.0) è funzionalmente solida ma il direttore d'orchestra ha eseguito un audit cliccando ogni voce del menu come fa un cliente comune e ha quantificato:

```
5 valori diversi di GAP header→hero:    22, 53, 115-128, 140, 202px
5 sistemi diversi di PADDING-LEFT:      72, 80, 144, 200, 448-861px
+ 2 bug nuovi: /casi/ 404, /tipo-area/ overlay testo
```

**Pattern segnalato da Duccio:** "ogni pagina ha allineamento header/contenuti diverso. Homepage parte tutta a sinistra. Tutte vanno riviste nello spacing verticale tra contenuti e nella distanza tra head e hero."

**Cross-reference audit CRO** (`audit-cro-studiolegalesaltelli.md`) — l'audit del cliente cita esplicitamente questi pattern come **errori del sito attuale da NON ripetere**:

| Citazione audit CRO | Problema da risolvere |
|---|---|
| "Margin e padding non seguono una scala definita" | Adottare scala 8px (8, 16, 24, 32, 48, 64, 80, 96, 128) |
| "Il container principale sembra variare tra ~1100px e ~1200px senza coerenza" | Container UNIFICATO 1440px max |
| "Whitespace verticale tra sezioni: minimo 80-120px" | --space-hero-top: clamp(64, 8vw, 120) |
| "Grid CSS a 12 colonne con gutter coerente" | Grid system docs |
| "Touch target minimum 48×48px" | Min-height su tutti elementi interattivi mobile |
| "Heading hierarchy non logica" | Audit + fix gerarchia DOM |
| "Smooth scroll behavior assente" | scroll-behavior: smooth |
| "Bottoni CTA stili multipli incoerenti" | Solo `.sl-btn` ovunque |

Il tuo lavoro: **harmonizzare TUTTO il layout system-wide** facendo valere 1 sistema di spacing + 1 container + 1 grid + 1 button system. Più fix dei 2 bug nuovi.

---

## Letture obbligatorie

1. `CLAUDE.md` — hard constraints
2. `.claude/knowledge/design/sessione-1/reports/visual-walkthrough/AUDIT-ALIGNMENT-v0.10.0.md` — diagnosi 5 problemi quantificati
3. `.claude/knowledge/design/sessione-1/reports/final-polish-v0.11.0/REPORT.md` — context fix recenti
4. `.claude/knowledge/design/sessione-1/tokens.css` — design tokens locked (NON modificare valori, solo aggiungere se necessario)
5. `wp-content/themes/saltelli/assets/css/sections.css` — file CSS principale
6. `wp-content/themes/saltelli/page.php` — template page (per fix /casi/ e /tipo-area/ overlap)
7. `wp-content/themes/saltelli/taxonomy-tipo-area.php` — template taxonomy

---

## Hard rules

| Rule | Reason |
|---|---|
| **Mai sovrascrivere** _thumbnail_id Emiliano + bio_estesa Step D + post_content competenza/avvocato | Content protetto |
| **Design tokens valori locked** — puoi AGGIUNGERE token spacing nuovi (`--space-hero-top`), MAI cambiare valori esistenti | Stabilità |
| Cache flush + smoke test 12+ URL dopo OGNI task | Lezione comprehensive |
| **Verifica DOM positions via `javascript_exec` post-fix** — non assumere CSS attivo | Stessa lezione mini-fix v0.8.1 |
| Sequenza obbligata Task 1 → 2 → 3 → 4 → 5, NON parallelo | File condivisi |
| Min-height touch 48px su mobile (audit CRO 12.3) | Compliance + UX |
| `:root` token nuovi vanno in tokens.css (commit aggiungerà al file) | Conventione |

---

## TASK 1 — Container Unificato `.sl-container` System-Wide (15 min)

### Obiettivo

UN solo container CSS che governa padding-left orizzontale di TUTTE le pagine. A 1440px viewport: padding-left = 72px effettivi (1440 - 96 - 96 - max-content-width).

### Diagnosi attuale
```bash
# Verifica cosa esiste già come container
grep -B 1 -A 5 '\.sl-container' wp-content/themes/saltelli/assets/css/tokens.css wp-content/themes/saltelli/assets/css/sections.css | head -30
```

### Fix in `sections.css` (in cima, post tokens)

```css
/* ═══════════════════════════════════════════════════════════════
   LAYOUT HARMONIZATION v0.12.0 — Container Unificato
   ═══════════════════════════════════════════════════════════════
   Audit CRO: "Il container principale sembra variare tra ~1100px
   e ~1200px senza coerenza" → UNICO container per tutto il sito.
   ═══════════════════════════════════════════════════════════════ */

.sl-container,
.sl-page,
.sl-post,
.sl-section,
.sl-section-head,
.sl-hero__inner,
.sl-areas .sl-container,
.sl-team,
.sl-team .sl-container,
.sl-cases,
.sl-cases .sl-container,
.sl-press,
.sl-contact,
.sl-page-contatti__map,
.sl-page-contatti__cta,
.sl-blog-archive,
.sl-blog__list,
.sl-areas-archive,
.sl-post__hero,
.sl-post__body,
.sl-post__author-card,
.sl-post__related {
    width: 100%;
    max-width: var(--sl-container-max);
    margin-inline: auto;
    padding-inline: var(--sl-container-pad);
    box-sizing: border-box;
}
```

### Token in `tokens.css` (aggiungi a `:root`)
```css
/* Container unificato system-wide */
--sl-container-max: 1440px;
--sl-container-pad: clamp(24px, 5vw, 96px);
```

### Verify Task 1
```bash
docker compose run --rm wpcli cache flush

# DOM check posizione H1 su 6 pagine
for URL in "/" "/chi-siamo/" "/avvocati/" "/competenze/" "/blog/" "/contatti/" "/costi/" "/competenze/diritto-tributario/" "/avvocati/emiliano-saltelli/"; do
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$URL?_=task1")
    printf "  %-40s HTTP %s\n" "$URL" "$HTTP"
done
```

Visual check: orchestrator può fare DOM measure post-fix per confermare h1.left = 72-96 px coerente su tutte le pagine.

---

## TASK 2 — Spacing Verticale Tokenizzato (15 min)

### Obiettivo

UN solo sistema di spacing verticale per gap header→hero su TUTTE le pagine. Audit CRO §2.5: "Whitespace verticale tra sezioni: minimo 80-120px" + "scala 8px-based".

### Token in `tokens.css` (aggiungi a `:root`)

```css
/* Spacing scale 8px-based (audit CRO recommendation) */
--space-1: 8px;
--space-2: 16px;
--space-3: 24px;
--space-4: 32px;
--space-5: 48px;
--space-6: 64px;
--space-7: 80px;
--space-8: 96px;
--space-9: 128px;

/* Hero spacing — gap header→primo elemento page */
--space-hero-top: clamp(64px, 8vw, 120px);
--space-hero-bottom: clamp(48px, 6vw, 80px);
```

### Fix in `sections.css`

```css
/* ═══════════════════════════════════════════════════════════════
   LAYOUT HARMONIZATION v0.12.0 — Spacing Hero Unificato
   Audit CRO: "minimum 80-120px whitespace tra sezioni"
   ═══════════════════════════════════════════════════════════════ */

/* Hero element FIRST in the page (eyebrow / breadcrumb / title) */
.sl-page__breadcrumb,
.sl-page__title:first-child,
.sl-section-head:first-child,
.sl-hero__main:first-child,
.sl-post__hero:first-child,
.sl-areas-archive .sl-section-head:first-child {
    margin-top: var(--space-hero-top);
}

/* Eyebrow spacing → titolo */
.sl-page__breadcrumb,
.sl-section-head .sl-mono:first-child,
.sl-post__hero .sl-mono:first-child {
    margin-bottom: var(--space-3);  /* 24px */
}

/* Hero title → next element */
.sl-page__title,
.sl-section-head h1,
.sl-section-head h2,
.sl-hero__headline,
.sl-post__title {
    margin-bottom: var(--space-5);  /* 48px */
}

/* Section-to-section gap globale */
.sl-section + .sl-section,
.sl-page > * + *:not(.sl-page__breadcrumb):not(.sl-page__title) {
    margin-top: var(--space-7);  /* 80px */
}

/* Body content spacing */
.sl-post__body > * + *,
.sl-page__prose > * + *,
.entry-content > * + * {
    margin-top: var(--space-3);  /* 24px between paragraphs */
}

.sl-post__body > h2 + *,
.sl-post__body > h3 + *,
.sl-page__prose > h2 + *,
.sl-page__prose > h3 + * {
    margin-top: var(--space-3);  /* 24px after heading */
}
```

### Verify Task 2

```bash
docker compose run --rm wpcli cache flush
```

Visual check via orchestrator (vedi Task 6).

---

## TASK 3 — Bug fix /casi/ 404 (15 min)

### Obiettivo

`/casi/` cliccato dal menu deve servire una page valida con design coerente.

### Diagnosi
```bash
# Verifica WP page list
docker compose run --rm wpcli post list --post_type=page --fields=ID,post_name --format=csv 2>&1 | grep -i "cas\|vittor\|risultati"

# Verifica menu item esistente che punta a /casi/
docker compose run --rm wpcli menu item list saltelli-header --fields=db_id,title,url --format=csv 2>&1 | grep -i casi
```

### Fix — Crea page `/casi/`

```bash
# Crea page WP "Casi e vittorie" con content che riusa helper saltelli_homepage_cases()
docker compose run --rm wpcli post create \
    --post_type=page \
    --post_title="Casi rappresentativi" \
    --post_name="casi" \
    --post_status=publish \
    --post_excerpt="Vittorie selezionate dello Studio Legale Saltelli & Partners. Annullamenti di cartelle, riforme di accertamenti, sentenze favorevoli." \
    --post_content="<div class=\"sl-page__prose\"><p class=\"sl-page__lede\">Una selezione di casi rappresentativi affrontati e vinti dallo Studio. Identificativi anonimizzati per riservatezza, ma documentati e verificabili in studio.</p></div>" \
    --porcelain
```

Nota il `--porcelain` ritorna l'ID. Memorizza `PAGE_ID_CASI`.

### Custom rendering in `page.php`

In `page.php` aggiungi un blocco condizionale `is_page('casi')` che usa il helper `saltelli_homepage_cases()`:

```php
<?php if (is_page('casi') && function_exists('saltelli_homepage_cases')) :
    $cases = saltelli_homepage_cases();
    if (!empty($cases)) : ?>
    <section class="sl-cases sl-cases--archive">
        <header class="sl-section-head">
            <div class="sl-mono">§ Casi rappresentativi</div>
            <h2>Casi rappresentativi</h2>
        </header>
        <ol class="sl-cases__list">
            <?php foreach ($cases as $case) : ?>
                <li class="sl-cases__row">
                    <div class="sl-mono sl-cases__id"><?php echo esc_html($case['id_label']); ?></div>
                    <p class="sl-cases__desc"><?php echo esc_html($case['description']); ?></p>
                    <div class="sl-cases__outcome"><?php echo esc_html($case['outcome']); ?></div>
                </li>
            <?php endforeach; ?>
        </ol>
    </section>
<?php endif; endif; ?>
```

### Yoast meta description
```bash
PAGE_ID_CASI=$(docker compose run --rm wpcli post list --post_type=page --name=casi --field=ID 2>&1 | grep -oE '^[0-9]+' | head -1)
docker compose run --rm wpcli post meta update "$PAGE_ID_CASI" _yoast_wpseo_metadesc \
    "Casi rappresentativi vinti da Studio Legale Saltelli e Partners. Annullamenti cartelle, riforme accertamenti tributari, sentenze favorevoli."
```

### Verify Task 3
```bash
docker compose run --rm wpcli cache flush
docker compose run --rm wpcli rewrite flush --hard

HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080/casi/?_=task3verify")
echo "  /casi/ HTTP: $HTTP (atteso 200)"
echo "  Numero casi visibili: $(curl -s 'http://localhost:8080/casi/?_=task3' | grep -c 'sl-cases__row')"
```

---

## TASK 4 — Bug fix /tipo-area/* overlap testo (10 min)

### Obiettivo

Sul template `taxonomy-tipo-area.php` ho identificato due elementi che renderizzano nello stesso punto:
1. `<div class="sl-mono">Studio · Aree per categoria</div>` (eyebrow custom)
2. `<nav class="sl-page__breadcrumb">HOME / COMPETENZE / PER CATEGORIA / PRIVATI</nav>` (breadcrumb)

Risultato: testi sovrapposti illeggibili.

### Diagnosi
```bash
grep -B 2 -A 10 "sl-mono\|sl-page__breadcrumb\|sl-areas__archive" wp-content/themes/saltelli/taxonomy-tipo-area.php | head -30
```

### Fix in `taxonomy-tipo-area.php`

**Rimuovi il blocco `<div class="sl-mono">Studio · Aree per categoria</div>`** che duplica info del breadcrumb. Mantieni solo:
- `<nav class="sl-page__breadcrumb">` (breadcrumb)
- `<h1 class="sl-areas__archive-title">{term_name}</h1>`
- `<p class="sl-areas__archive-lede">{term_description}</p>`

Markup atteso post-fix:
```php
<header class="sl-section-head sl-areas__archive-head">
    <nav class="sl-page__breadcrumb sl-mono" aria-label="Breadcrumb">
        <a href="/">Home</a> / <a href="/competenze/">Competenze</a> / <span><?php echo esc_html($term->name); ?></span>
    </nav>
    <h1 class="sl-areas__archive-title"><?php echo esc_html($term->name); ?></h1>
    <p class="sl-areas__archive-lede">
        <em><?php echo number_format_i18n(count($posts ?? [])); ?> aree</em>
        <?php if ($term->description) : ?>
            · <?php echo esc_html($term->description); ?>
        <?php endif; ?>
    </p>
</header>
```

### Verify Task 4
```bash
HTML=$(curl -s "http://localhost:8080/tipo-area/privati/?_=task4")
echo "  Studio · Aree per categoria duplicato presente: $(echo "$HTML" | grep -c 'Studio · Aree per categoria\|Aree per categoria')"
echo "  Atteso 0 (eyebrow eliminato)"
echo ""
echo "  Breadcrumb presente: $(echo "$HTML" | grep -c 'sl-page__breadcrumb')"
echo "  H1 presente: $(echo "$HTML" | grep -c '<h1')"
```

---

## TASK 5 — Homepage Hero Compattazione (10 min)

### Obiettivo

Above-the-fold della homepage a 1440×900 deve mostrare ENTRAMBI: hero text (sx) + colophon (dx). Attualmente colophon parte a top:725px, sotto fold.

### Fix in `sections.css`

```css
/* ═══════════════════════════════════════════════════════════════
   LAYOUT HARMONIZATION v0.12.0 — Homepage Hero compattata
   Above-the-fold visibile colophon
   ═══════════════════════════════════════════════════════════════ */

.sl-hero {
    /* Riduci padding-block hero per dare spazio a colophon above-fold */
    padding-block: var(--space-7) var(--space-5);  /* 80px / 48px */
    min-height: calc(100vh - var(--space-7));      /* 100vh meno header */
}

@media (min-width: 1024px) {
    .sl-hero__inner {
        display: grid;
        grid-template-columns: minmax(0, 8fr) minmax(280px, 4fr);
        column-gap: var(--space-7);  /* 80px */
        align-items: start;
    }

    .sl-hero__main {
        align-self: end;     /* spinge il content verso il basso */
    }

    .sl-hero__colophon {
        align-self: start;   /* colophon pinned in alto a destra */
        padding-top: var(--space-5);  /* 48px */
    }
}

/* Riduci leggermente scale headline per garantire 3 righe + colophon visibili */
.sl-hero__headline {
    font-size: clamp(64px, 8vw, 120px);
    line-height: 1;
}
```

### Verify

DOM check: orchestrator può misurare se `.sl-hero__colophon` è above fold (top < 900).

---

## TASK 6 — Heading hierarchy + button consistency audit (10 min)

### Obiettivo

Audit CRO §1.3.1: "Heading structure: gerarchia non logica, salti H1→H3 senza H2".

### Audit DOM via curl + grep

```bash
echo "─── HEADING HIERARCHY AUDIT ───"
for URL in "/" "/chi-siamo/" "/avvocati/" "/competenze/" "/blog/" "/contatti/" "/costi/" "/casi/" "/competenze/diritto-tributario/" "/avvocati/emiliano-saltelli/" "/intimazione-tari-annullata-vittoria-dello-studio-legale-saltelli/"; do
    HTML=$(curl -s "http://localhost:8080$URL?_=task6")
    H1=$(echo "$HTML" | grep -oE '<h1[^>]*>' | wc -l | tr -d ' ')
    H2=$(echo "$HTML" | grep -oE '<h2[^>]*>' | wc -l | tr -d ' ')
    H3=$(echo "$HTML" | grep -oE '<h3[^>]*>' | wc -l | tr -d ' ')
    H4=$(echo "$HTML" | grep -oE '<h4[^>]*>' | wc -l | tr -d ' ')
    printf "  %-65s H1:%s H2:%s H3:%s H4:%s\n" "$URL" "$H1" "$H2" "$H3" "$H4"
done
```

**Atteso:** H1=1 ovunque, H2 ≥ 1 se ci sono sub-section, no salti H1→H3.

### Audit button consistency

```bash
echo "─── BUTTON CONSISTENCY AUDIT ───"
for URL in "/" "/chi-siamo/" "/avvocati/" "/competenze/" "/blog/" "/contatti/" "/costi/" "/casi/"; do
    HTML=$(curl -s "http://localhost:8080$URL?_=task6btn")
    SL_BTN=$(echo "$HTML" | grep -c 'class="[^"]*sl-btn')
    NON_SL_BTN=$(echo "$HTML" | grep -cE '<button[^>]*>|class="[^"]*btn[^"]*"' | head -1)
    printf "  %-65s .sl-btn:%s · other btn:%s\n" "$URL" "$SL_BTN" "$NON_SL_BTN"
done
```

**Atteso:** tutti CTA usano `.sl-btn`, no button raw senza classe.

### Smooth scroll behavior

In `tokens.css` aggiungi a `:root` o `html`:
```css
html {
    scroll-behavior: smooth;
}

@media (prefers-reduced-motion: reduce) {
    html {
        scroll-behavior: auto;
    }
}
```

### Touch target mobile (audit CRO 12.3)

In `sections.css`:
```css
/* Touch target 48x48px min on mobile (audit CRO compliance) */
@media (max-width: 1023px) {
    .sl-btn,
    .sl-link,
    .sl-header__nav a,
    .sl-footer a,
    .sl-attorney__sticky-btn,
    .sl-page__breadcrumb a {
        min-height: 48px;
        display: inline-flex;
        align-items: center;
    }
}
```

---

## TASK 7 — Bump version + smoke test esteso (5 min)

```bash
sed -i.bak 's/Version: 0.11.0-beta-final-polish/Version: 0.12.0-beta-layout-harmonized/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.11.0-beta-final-polish')/define('SALTELLI_THEME_VERSION', '0.12.0-beta-layout-harmonized')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all
docker compose run --rm wpcli rewrite flush --hard

echo ""
echo "═══════════ FINAL SMOKE TEST v0.12.0 ═══════════"
for URL in "/" "/lo-studio/" "/chi-siamo/" "/avvocati/" "/competenze/" "/blog/" "/contatti/" "/costi/" "/casi/" "/competenze/diritto-tributario/" "/competenze/diritto-del-lavoro/" "/avvocati/emiliano-saltelli/" "/avvocati/fabiana-saltelli/" "/tipo-area/privati/" "/tipo-area/imprese/" "/intimazione-tari-annullata-vittoria-dello-studio-legale-saltelli/"; do
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$URL?_=v012final")
    H1=$(curl -s "http://localhost:8080$URL?_=v012final" | grep -c "<h1")
    LOREM=$(curl -s "http://localhost:8080$URL?_=v012final" | grep -ci "lorem ipsum")
    printf "  %-65s HTTP %s · %sH1 · Lorem:%s\n" "$URL" "$HTTP" "$H1" "$LOREM"
done
```

Atteso:
- Tutti HTTP 200 (eccetto /lo-studio/ HTTP 301 → /chi-siamo/ → 200)
- 1 H1 ovunque
- Lorem: 0 ovunque
- **Anche /casi/ ora HTTP 200** (era 404)

---

## Report finale

Scrivi `.claude/knowledge/design/sessione-1/reports/layout-harmonization-v0.12.0/REPORT.md`:

1. ✅/❌ ciascuno dei 7 task
2. Smoke test 16 URL post-fix (tabella)
3. DOM measure positions h1.left + gap header→hero su 6+ URL: confermano coerenza?
4. Verifica regressione: 21 PASS precedenti preservati?
5. Decisioni autonome
6. Tempo per task
7. **GO/NO-GO per Step F** dal tuo punto di vista

Poi **fermati**. Direttore d'orchestra eseguirà visual walkthrough esteso completo.

---

## Cosa fare se imprevisti

| Situazione | Azione |
|---|---|
| `.sl-container` selettore aggregato troppo generico → impatta layout esistenti rotti | Identifica conflict, aggiungi `:not()` specifici alla regola unificata |
| Spacing tokenizzati causano regression in qualche pagina | Restringi scope con `:not(:first-child)` o classi più specifiche |
| /casi/ template renderizza vuoto perché `saltelli_homepage_cases()` non esiste | Crea fallback hardcoded 4 casi nel content della page WP |
| /tipo-area/ overlay still visibile post-fix | Verifica i 4 termini (privati/imprese/contenzioso/altri) — fix deve aplicare a tutti |
| Mobile touch target rompe layout desktop | Verifica `@media (max-width: 1023px)` scope |
| Smoot scroll provoca jitter | `scroll-behavior: auto` come fallback default + opt-in JS-driven solo se richiesto |

---

## Cross-reference vincoli audit CRO (per non ripetere errori vecchio sito)

Quando applichi i fix, verifica esplicitamente che:

1. ✅ Spacing scale 8px-based (8/16/24/32/48/64/80/96/128) — Task 2
2. ✅ Container UNIFICATO 1440px max — Task 1
3. ✅ Whitespace verticale 80-120px tra sezioni — Task 2
4. ✅ Padding-inline coerente con `clamp(24, 5vw, 96)` — Task 1
5. ✅ Touch target 48×48 mobile — Task 6
6. ✅ Heading hierarchy logica — Task 6 audit
7. ✅ CTA buttons consistent (solo `.sl-btn`) — Task 6 audit
8. ✅ Smooth scroll behavior — Task 6
9. ✅ `/casi/` non rotta — Task 3
10. ✅ Overlay testi taxonomy fixato — Task 4

L'audit CRO originale ha dato score `3.5/10` al vecchio sito su questi punti. Il nuovo sito v0.12.0 deve ottenere ≥ `8/10` su tutti loro.

---

*v1.0 — Layout Harmonization comprehensive. Direttore d'orchestra: Claude (chat). Target: v0.12.0 con padding-left coerente 72-96px, gap header→hero ~120px, touch targets 48×48, tutti i 16 URL HTTP 200, /casi/ creato, /tipo-area/ overlay fixato.*
