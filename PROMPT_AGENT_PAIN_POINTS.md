# Prompt — Pain Points Refinement Agent (POST-DEMO POLISH)

> **Per Claude Code in nuova sessione (o sessione corrente).** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo previsto: 45-60 min. **Sequenza, NON parallelo** — i 7 fix toccano file condivisi.
> **PRECEDENZA:** Audit Alignment (Step Pre-Demo) completato. v0.6.0-beta-audit-aligned o successiva.

---

## Tu sei

Il **Pain Points Refinement Agent**. La build attuale (v0.6.0) è funzionalmente perfetta ma il direttore d'orchestra (Claude in chat) ha eseguito una pre-survey visiva via Claude in Chrome e ha individuato **7 pain points grafici** (3 critici P0, 4 forti P1) che limano la qualità "Legal Luxury Minimal" promessa. Tutti sono fix CSS chirurgici + 1 fix template PHP — niente refactor strutturali.

**Stato di partenza:** v0.6.0-beta-audit-aligned · sitemap Privati/Imprese/Contenzioso · pagina /costi/ · gancio "consulenza gratuita" propagato.

---

## Letture obbligatorie (in ordine)

1. `CLAUDE.md` — hard constraints
2. `wp-content/themes/saltelli/assets/css/sections.css` — file principale dove vivono CSS sezioni
3. `wp-content/themes/saltelli/front-page.php` — per fix P0.3
4. `wp-content/themes/saltelli/single-competenza.php` — per fix P0.2 + P1.3
5. `wp-content/themes/saltelli/page.php` — per fix P0.1
6. `wp-content/themes/saltelli/inc/cpt-competenza.php` — solo letture, NON modificare

---

## Hard rules

| Rule | Reason |
|---|---|
| Design tokens (`tokens.css`) NON si toccano | Locked dall'inizio |
| **Sequenza obbligata**: P0.1 → P0.2 → P0.3 → P1.1 → P1.2 → P1.3 → P1.4. Verifica ogni step prima di passare al prossimo | Stessi file = collisioni se concorrenti |
| Cache flush + curl test dopo ogni fix | Catch regression early |
| Mai sovrascrivere bio_estesa o _thumbnail_id (popolati da Step D) | Stabilità contenuti |
| Mai disattivare plugin durante questo run | Predicibilità |
| Idempotenza: re-run = stesso output | Stabilità |
| Output classi `.sl-*` con namespace consistente | Convenzione |
| Nessun `console.log` o `var_dump` lasciato | Cleanup |

---

## P0.1 — Pagina /costi/ layout sbilanciato (10 min)

### Problema
La pagina `/costi/` mostra il content allineato a destra con un'enorme colonna vuota a sinistra. Il template `page.php` probabilmente ha un layout grid asimmetrico ereditato da altri template (es. single-competenza), inadatto per page semplice.

### Diagnosi
```bash
# Verifica come il content della pagina /costi/ è wrappato nel rendering
curl -s "http://localhost:8080/costi/" | grep -oE '<article[^>]*>|<main[^>]*>|<div class="sl-[^"]*"[^>]*>' | head -10
```

### Fix
1. **Apri `page.php`** e verifica se ha class wrapper tipo `.sl-page`, `.sl-page__content`, `.sl-grid` o simili
2. In `assets/css/sections.css` aggiungi (o sostituisci se esistono già regole `.sl-page`):

```css
/* ═══════════════════════════════════════════════════════════════
   FIX P0.1 — Pagina standard (page.php) layout editoriale centrato
   ═══════════════════════════════════════════════════════════════ */

.sl-page {
    padding-block: clamp(64px, 8vw, 128px);
    padding-inline: clamp(24px, 5vw, 96px);
    max-width: 1440px;
    margin: 0 auto;
}

.sl-page__breadcrumb {
    margin-bottom: 32px;
}

.sl-page__title {
    font-family: var(--font-display);
    font-size: clamp(48px, 6vw, 96px);
    line-height: 1.05;
    color: var(--primary);
    margin-bottom: 64px;
    max-width: 18ch;
}

.sl-page__content {
    max-width: 720px;
    margin-left: auto;
    margin-right: auto;
}

/* Asimmetria editoriale: eyebrow a sinistra in colonna stretta, body a destra */
.sl-costi__section {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
    max-width: 720px;
    margin: 0 auto 64px;
}

@media (min-width: 1024px) {
    .sl-costi__section {
        grid-template-columns: 240px 1fr;
        gap: 64px;
        max-width: 1100px;
    }
    .sl-costi__section .sl-mono {
        text-align: right;
        padding-top: 24px;
    }
    .sl-costi__section h2,
    .sl-costi__section p,
    .sl-costi__section ul,
    .sl-costi__section .sl-acc {
        grid-column: 2;
    }
    .sl-costi__section .sl-mono {
        grid-column: 1;
        grid-row: 1 / span 99;
    }
}

.sl-costi__intro {
    max-width: 720px;
    margin: 0 auto 96px;
}

.sl-costi__cta {
    max-width: 1100px;
    margin: 96px auto 0;
}
```

3. **Se `page.php` non usa `.sl-page` come wrapper**, modifica il template per averlo:

```php
<article class="sl-page">
    <?php if (function_exists('saltelli_get_breadcrumb_chain')) : ?>
        <nav class="sl-page__breadcrumb sl-mono"><?php /* breadcrumb output */ ?></nav>
    <?php endif; ?>
    <h1 class="sl-page__title"><?php the_title(); ?></h1>
    <div class="sl-page__content">
        <?php the_content(); ?>
    </div>
</article>
```

(Mantieni il template esistente se già usa pattern simile. Solo aggiungi le classi mancanti.)

### Verify
```bash
docker compose run --rm wpcli cache flush
sleep 1
HTML=$(curl -s "http://localhost:8080/costi/?_=p01")
echo "  .sl-page presente: $(echo "$HTML" | grep -c 'class="sl-page')"
echo "  .sl-costi__section presente: $(echo "$HTML" | grep -c 'sl-costi__section')"
```

Visual check: l'orchestratore farà screenshot in seguito, ma se nel HTML appaiono entrambe le classi e la pagina è 200, il fix è applicato.

---

## P0.2 — Heading "AVVOCATO TRIBUTARISTA NAPOLI" tutto MAIUSCOLO (5 min)

### Problema
Nel content migrato dei CPT competenza tier-1 c'è un `<h2>` (es. "AVVOCATO TRIBUTARISTA NAPOLI") in MAIUSCOLO + Playfair gigante. Anti-pattern visivo (anche Impeccable l'aveva flaggato come "all-caps-body" su Hero).

### Diagnosi
```bash
# Cerca quanti h2 in caps esistono nei post_content delle 19 competenze
docker exec saltelli-db mysql -u saltelli -psaltelli_dev saltelli_wp -sNe \
    "SELECT post_name, SUBSTRING(post_content, LOCATE('<h2', post_content), 80) FROM wp_posts WHERE post_type='competenza' AND post_status='publish' AND post_content REGEXP '<h2[^>]*>[A-ZÀ-Ú ]{15,}</h2>' LIMIT 5;" 2>&1 | tail -10
```

### Fix
**Approccio CSS (più sicuro, retro-compatibile):**

```css
/* ═══════════════════════════════════════════════════════════════
   FIX P0.2 — h2 nel content competenza tier-1: trasforma all-caps
   in case sentence + scala più contenuta
   ═══════════════════════════════════════════════════════════════ */

.sl-competenza__body h2,
.sl-competenza__content h2,
.entry-content h2 {
    text-transform: none;  /* override eventuale all-caps */
    font-family: var(--font-display);
    font-size: clamp(28px, 3.5vw, 48px);  /* riduce da 80px+ a max 48 */
    line-height: 1.15;
    letter-spacing: -0.01em;  /* scioglie kerning Playfair caps */
    margin-block: 64px 24px;
    color: var(--primary);
}

/* Se il content originale ha letteralmente <h2>AVVOCATO TRIBUTARISTA NAPOLI</h2>,
   sopravvivono in caps. Aggiungi text-transform: capitalize OPZIONALE: */
.sl-competenza__body h2 {
    /* Mantieni le maiuscole originali ma più piccole + spaziate */
}
```

**Solo se le caps restano insopportabili,** sostituisci nel DB:

```bash
# Sostituzione case-by-case (idempotente — runna 1 volta sola)
docker exec saltelli-db mysql -u saltelli -psaltelli_dev saltelli_wp -e "
UPDATE wp_posts SET post_content = REPLACE(post_content, '<h2>AVVOCATO TRIBUTARISTA NAPOLI</h2>', '<h2>Avvocato tributarista a Napoli</h2>') WHERE post_type='competenza';
UPDATE wp_posts SET post_content = REPLACE(post_content, '<h2>STUDIO LEGALE SALTELLI A NAPOLI</h2>', '<h2>Studio Legale Saltelli a Napoli</h2>') WHERE post_type='competenza';
UPDATE wp_posts SET post_content = REPLACE(post_content, '<h2>STUDIO LEGALE SALTELLI DI NAPOLI</h2>', '<h2>Studio Legale Saltelli di Napoli</h2>') WHERE post_type='competenza';
" 2>&1 | tail -3
```

### Verify
```bash
docker compose run --rm wpcli cache flush
HTML=$(curl -s "http://localhost:8080/competenze/diritto-tributario/?_=p02")
# Conta h2 in caps (dovrebbe essere 0 dopo fix DB) o stilare correttamente
echo "  h2 ALL CAPS: $(echo "$HTML" | grep -oE '<h2[^>]*>[A-ZÀ-Ú ]{15,}</h2>' | wc -l)"
```

---

## P0.3 — Hero homepage che taglia/sovrappone (10 min)

### Problema
Dopo aver scrollato di mezzo viewport, l'h1 hero ("misura.") sovrappone "STUDIO LEGALE · NAPOLI · CHIAIA · DAL 1999" in alto e "COLOPHON" a destra. Bug di layout: probabilmente `.sl-hero` ha height fissa che non scala con content, oppure colophon è position:absolute e non si adatta.

### Diagnosi
```bash
# Verifica le rule CSS attualmente attive su .sl-hero
grep -A 30 '\.sl-hero' wp-content/themes/saltelli/assets/css/sections.css | head -60
```

### Fix
Cerca in `sections.css` la regola `.sl-hero` esistente. Sostituisci l'intero blocco con:

```css
/* ═══════════════════════════════════════════════════════════════
   FIX P0.3 — Hero homepage: 100vh real, no overlap, scroll naturale
   ═══════════════════════════════════════════════════════════════ */

.sl-hero {
    position: relative;        /* NON sticky, NON fixed */
    min-height: 100vh;
    padding-block: clamp(48px, 8vh, 120px);
    padding-inline: clamp(24px, 5vw, 96px);
    display: grid;
    grid-template-columns: 1fr;
    gap: 32px;
    align-content: space-between;
    overflow: visible;         /* il content può eccedere se necessario */
}

@media (min-width: 1024px) {
    .sl-hero {
        grid-template-columns: minmax(0, 8fr) minmax(0, 4fr);
        gap: 64px;
    }
}

.sl-hero__eyebrow {
    /* La riga "STUDIO LEGALE · NAPOLI · CHIAIA · DAL 1999" */
    grid-column: 1 / -1;
    margin-bottom: 0;
    align-self: start;
}

.sl-hero__main {
    /* Wrapper della headline + subline + CTA */
    grid-column: 1;
    align-self: end;            /* push verso il basso del 100vh */
    max-width: 14ch;
}

.sl-hero__colophon {
    grid-column: 2;
    align-self: end;
    max-width: 320px;
}

@media (max-width: 1023px) {
    .sl-hero__colophon {
        grid-column: 1;
        align-self: start;
        margin-top: 32px;
    }
}

.sl-hero__headline {
    font-family: var(--font-display);
    font-size: clamp(80px, 10vw, 160px);
    line-height: 1;
    color: var(--primary);
    max-width: 14ch;
}

.sl-hero__scroll-indicator {
    grid-column: 1 / -1;
    align-self: end;
    justify-self: end;
    /* "SCORRI" verticale a destra */
}
```

**Se il template `front-page.php` non ha già `.sl-hero__main` come wrapper** (i.e. headline + subline + CTA sono fratelli direttamente in `.sl-hero`), aggiungilo:

```php
<section class="sl-hero">
    <div class="sl-mono sl-hero__eyebrow">Studio Legale · Napoli · Chiaia · Dal 1999</div>

    <div class="sl-hero__main">
        <h1 class="sl-hero__headline" data-split-reveal>
            <!-- ... -->
        </h1>
        <p class="sl-hero__subheadline"><!-- ... --></p>
        <a class="sl-btn sl-btn--primary" href="..."><!-- ... --></a>
        <div class="sl-mono sl-hero__cta-note">Prima consulenza conoscitiva — risposta entro 24 ore</div>
    </div>

    <aside class="sl-hero__colophon">
        <!-- COLOPHON / INDIRIZZO / ORARI / CONTATTI -->
    </aside>

    <div class="sl-hero__scroll-indicator sl-mono">Scorri ↓</div>
</section>
```

### Verify
```bash
docker compose run --rm wpcli cache flush
HTML=$(curl -s "http://localhost:8080/?_=p03")
echo "  .sl-hero__main wrapper: $(echo "$HTML" | grep -c 'sl-hero__main')"
```

Visual check: orchestratore farà screenshot per confermare zero overlap.

---

## P1.1 — Numerazione 19 aree non rispetta tier (5 min)

### Problema
Le aree in homepage sono numerate "01/19" → "19/19" indistintamente, mescolando tier-1 e tier-2 in una numerazione sequenziale che non aiuta a leggere la gerarchia.

### Fix
Soluzione minimale: **togli la numerazione sequenziale, sostituisci con tag tier** + mantieni solo l'ordering query (tier-1 first, già attivo).

In `assets/css/sections.css`:

```css
/* ═══════════════════════════════════════════════════════════════
   FIX P1.1 — Aree pratica: nascondi numerazione sequenziale,
   tier-1 si distingue dal solo first-letter accent (P1.2 sotto)
   ═══════════════════════════════════════════════════════════════ */

.sl-area__num {
    display: none;  /* via la numerazione 01/19 */
}

@media (min-width: 1024px) {
    .sl-area__num {
        display: inline-block;
        font-size: 11px;
        color: var(--text-muted);
        opacity: 0.4;
        font-feature-settings: "tnum";  /* tabular numbers */
        letter-spacing: 0.06em;
    }
    .sl-area--tier1 .sl-area__num {
        opacity: 1;
        color: var(--accent);
    }
}
```

**Alternativa se preferisci mantenere "01 / 03" per tier-1 e nessun numero per tier-2:** chiedi al template di passare un attributo data-tier-number solo per tier-1. Ma è più complesso. La soluzione sopra (display: none su mobile, opacity gerarchica desktop) è più rapida e elegante.

---

## P1.2 — Drop-cap oro su tutte le aree (3 min)

### Problema
Tutte le 19 aree hanno la prima lettera in **bronzo/oro grande** (`::first-letter` color: var(--accent)). Andrebbe SOLO sulle 3 tier-1 per coerenza visiva con la strategy.

### Fix
In `sections.css`, cerca la regola `::first-letter` su `.sl-area__title` o `.sl-area`. Sostituiscila con:

```css
/* ═══════════════════════════════════════════════════════════════
   FIX P1.2 — Drop-cap oro SOLO su tier-1
   ═══════════════════════════════════════════════════════════════ */

/* Reset eventuale drop-cap globale */
.sl-area__title::first-letter {
    color: inherit;
    font-size: inherit;
}

/* Drop-cap solo tier-1 */
.sl-area--tier1 .sl-area__title::first-letter {
    color: var(--accent);
    /* opzionale: font-size leggermente più grande per ulteriore enfasi */
}
```

### Verify
Apri il browser su `/`, scrolla a sezione "Diciannove aree". Conferma:
- ✅ Tributario, Lavoro, Famiglia LGBTQ+ hanno la D iniziale in bronzo
- ✅ Tutte le altre 16 hanno la prima lettera nel colore primary normale

---

## P1.3 — FAQ accordion inconsistenti (10 min)

### Problema
- `/costi/`: simbolo `+` a destra (corretto, editoriale)
- `single-competenza tier-1` (es. tributario): simbolo `▶` triangolo default browser

### Fix
In `sections.css` aggiungi (o sostituisci se esistono):

```css
/* ═══════════════════════════════════════════════════════════════
   FIX P1.3 — FAQ accordion unificato (+/-) editoriale a destra
   ═══════════════════════════════════════════════════════════════ */

.sl-acc {
    border-top: 1px solid var(--border);
    list-style: none;
}

.sl-acc:last-child {
    border-bottom: 1px solid var(--border);
}

.sl-acc__summary,
.sl-acc summary,
details.sl-acc summary {
    list-style: none;          /* rimuove triangolo browser default */
    cursor: pointer;
    padding: 24px 48px 24px 0;
    font-family: var(--font-display);
    font-size: clamp(18px, 1.5vw, 22px);
    color: var(--primary);
    position: relative;
    transition: color var(--dur-fast) var(--ease-editorial);
}

.sl-acc__summary::-webkit-details-marker,
.sl-acc summary::-webkit-details-marker,
details.sl-acc summary::-webkit-details-marker {
    display: none;             /* Safari override */
}

.sl-acc__summary::marker,
.sl-acc summary::marker,
details.sl-acc summary::marker {
    display: none;             /* Firefox override */
    content: "";
}

/* Icona + a destra */
.sl-acc__summary::after,
details.sl-acc > summary::after {
    content: "+";
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    font-family: var(--font-mono);
    font-size: 24px;
    color: var(--accent);
    transition: transform var(--dur-fast) var(--ease-editorial);
}

/* Open: rotated to "−" */
details.sl-acc[open] > summary::after,
.sl-acc[open] .sl-acc__summary::after {
    content: "−";
}

.sl-acc__summary:hover {
    color: var(--accent);
}

/* Panel content */
.sl-acc__panel,
details.sl-acc > div,
details.sl-acc[open] > *:not(summary) {
    padding: 0 0 24px 0;
    color: var(--text);
    line-height: 1.7;
}
```

### Verify
```bash
HTML_T=$(curl -s "http://localhost:8080/competenze/diritto-tributario/?_=p13")
HTML_C=$(curl -s "http://localhost:8080/costi/?_=p13")
echo "  CSS rule applicata su tributario: $(echo "$HTML_T" | grep -c 'sl-acc')"
echo "  CSS rule applicata su costi:      $(echo "$HTML_C" | grep -c 'sl-acc')"
```

Apri il browser, espandi le FAQ su entrambe le pagine. Confermi: stesso `+/-` editoriale.

---

## P1.4 — Subline "consulenza gratuita" troppo aggressiva (5 min)

### Problema
Le subline mono uppercase letter-spacing 0.08em sembrano "disclaimer legale". Vanno ammorbidite.

### Fix
In `sections.css`, sostituisci (o aggiungi) le regole esistenti per `.sl-hero__cta-note`, `.sl-competenza__cta-note`, `.sl-contact__eyebrow`:

```css
/* ═══════════════════════════════════════════════════════════════
   FIX P1.4 — Subline "consulenza gratuita" softer, più editoriali
   ═══════════════════════════════════════════════════════════════ */

.sl-hero__cta-note,
.sl-competenza__cta-note {
    margin-top: 16px;
    color: var(--text-muted);
    font-family: var(--font-display);  /* serif, NON mono */
    font-style: italic;                 /* corsivo elegante */
    font-size: 15px;
    letter-spacing: 0;                  /* NO tracking aggressivo */
    text-transform: none;               /* NO uppercase */
    line-height: 1.5;
}

.sl-contact__eyebrow {
    color: var(--text-muted);
    font-family: var(--font-mono);
    font-size: 11px;                    /* ridotto da 12 */
    letter-spacing: 0.06em;             /* meno aggressivo */
    text-transform: uppercase;          /* mantenuto qui per coerenza con "§ 06 — CONTATTI" */
    margin-bottom: 8px;
}
```

### Verify
```bash
HTML=$(curl -s "http://localhost:8080/?_=p14")
# Le classi devono essere nel HTML (non sparite)
echo "  .sl-hero__cta-note: $(echo "$HTML" | grep -c 'sl-hero__cta-note')"
echo "  .sl-contact__eyebrow: $(echo "$HTML" | grep -c 'sl-contact__eyebrow')"
```

Visual: orchestratore confermerà che le subline italic serif si fondono naturalmente.

---

## TASK FINALE — Bump version + smoke test completo (5 min)

```bash
# Bump version
sed -i.bak 's/Version: 0.6.0-beta-audit-aligned/Version: 0.7.0-beta-pain-points-fixed/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.6.0-beta-audit-aligned')/define('SALTELLI_THEME_VERSION', '0.7.0-beta-pain-points-fixed')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all

echo ""
echo "═══════════ SMOKE TEST FINALE ═══════════"
for URL in "/" "/costi/" "/competenze/" "/competenze/diritto-tributario/" "/avvocati/emiliano-saltelli/" "/tipo-area/privati/"; do
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$URL")
    SIZE=$(curl -s "http://localhost:8080$URL" | wc -c | tr -d ' ')
    printf "  %-45s HTTP %s · %s bytes\n" "$URL" "$HTTP" "$SIZE"
done

echo ""
echo "═══════════ PHP error log ═══════════"
docker exec saltelli-wp tail -10 /var/www/html/wp-content/debug.log 2>/dev/null | grep -vE "Brevo|WonderPush|WonderPushIntegration" || echo "  (vuoto/solo Brevo plugin = OK)"
```

Tutti gli URL devono dare HTTP 200. Nessun fatal/parse error nel log (Brevo/WonderPush sono noise dai plugin disattivati, ignorabili).

---

## Report finale

Scrivi `.claude/knowledge/design/sessione-1/reports/pain-points-refinement/REPORT.md`:

1. ✅/❌ ciascuno dei 7 fix (P0.1, P0.2, P0.3, P1.1, P1.2, P1.3, P1.4)
2. Smoke test result (6 URL con HTTP code)
3. Decisioni autonome (es. "Ho preferito approach CSS-only su P0.2 invece di SQL UPDATE perché meno invasivo")
4. Eventuali blocker o issue residui che NON sei riuscito a fixare
5. File modificati (lista path)
6. Tempo totale

Poi **fermati**. Il direttore d'orchestra (Claude in chat) farà il visual walkthrough end-to-end e poi committerà.

---

## Cosa fare se qualcosa va storto

| Situazione | Azione |
|---|---|
| Una regola CSS già esistente con stessa specificity | Cerca con `grep -n` prima di aggiungere, sostituisci invece di duplicare |
| Layout `/costi/` ancora rotto dopo fix | Verifica template `page.php` ha `the_content()` dentro `.sl-page__content` |
| FAQ accordion non si apre | Verifica markup `<details class="sl-acc"><summary class="sl-acc__summary">` |
| Hero overflow su mobile | `overflow-x: hidden` su body solo se necessario, NON su `.sl-hero` |
| h2 caps non più visibili dopo CSS | Probabilmente browser caching: hard reload + `wp cache flush` |

In ogni dubbio: **stop e segnala**. Non improvvisare.

---

*v1.0 — POST-DEMO PAIN POINTS REFINEMENT · Direttore d'orchestra: Claude (chat).*
