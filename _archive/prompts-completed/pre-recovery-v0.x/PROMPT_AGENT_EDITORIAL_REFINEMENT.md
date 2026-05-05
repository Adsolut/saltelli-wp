# Prompt — Editorial Refinement Agent v0.10.0

> **Per Claude Code in nuova sessione (o sessione corrente).** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo previsto: 90-120 min. Lavoro di **rifinitura editoriale** profonda.
> **PRECEDENZA:** Recovery Agent v0.9.0 completato. v0.9.0-beta-recovery con 17/17 punti coperti (6 FAIL fixati + 11 PASS preservati).

---

## Tu sei

L'**Editorial Refinement Agent**. Il direttore d'orchestra (Claude in chat) ha eseguito un **walkthrough approfondito** v0.8.1 mentre Recovery v0.9.0 era in flight, e ha trovato **11 bug nuovi** (oltre ai 6 FAIL già fixati da Recovery) raggruppati in 3 GROUP:

```
GROUP A — Typography wall of text (4 bug, CSS-only)
GROUP B — Immagini sparate (3 bug, CSS + WP filter)
GROUP C — Routing/Content (3 bug + 1 minor, template + WP-CLI)
```

**Il pattern principale identificato dal direttore (e da Duccio):** "il testo è poco arioso, i titoli sono attaccati ai sottotitoli e ai contenuti successivi, le immagini sono sparate a sinistra, l'effetto tipografico si perde nel wall of text. Va prevista armonia tra immagini e testo."

Questo agent **non è un fix tecnico**, è una **rifinitura editoriale**. L'output deve sentirsi come una rivista (The Atlantic, Aeon, Bick Law) non come un blog WordPress.

---

## Letture obbligatorie

1. `CLAUDE.md` — hard constraints
2. `.claude/knowledge/design/sessione-1/reports/visual-walkthrough/REPORT-v0.8.1-DEEP.md` — **diagnosi completa 11 bug** (FONDAMENTALE)
3. `.claude/knowledge/design/sessione-1/reports/recovery-v0.9.0/REPORT.md` — context lavoro Recovery
4. `.claude/knowledge/design/sessione-1/tokens.css` — tokens locked (NON modificare)
5. `wp-content/themes/saltelli/assets/css/sections.css` — file CSS principale (post-Recovery)
6. `wp-content/themes/saltelli/single.php` — template blog post
7. `wp-content/themes/saltelli/page.php` — template page
8. `wp-content/themes/saltelli/archive.php` o `home.php` — template /blog/ archive (**verifica quale**)

---

## Hard rules

| Rule | Reason |
|---|---|
| **Mai sovrascrivere `_thumbnail_id` Emiliano (CPT 2660)** | Foto Step C.5 |
| **Mai sovrascrivere `bio_estesa` o `post_content` CPT competenza/avvocato** | Step D content |
| **Design tokens NON si toccano** | Locked |
| **Sequenza obbligata**: GROUP A → GROUP B → GROUP C → smoke test esteso | Approach disciplinato |
| **Cache flush + smoke test 8+ URL dopo OGNI fix** | Lezione Recovery v0.9.0 |
| Per le immagini: SOLO CSS + WP filter `the_content`, niente edit `wp_posts` | Reversibilità |
| **Verifica visiva DOM dopo ogni fix** via `curl ... | grep` per confermare CSS attivo | Zero assunzioni |
| Niente `!important` se non documentato come override esplicito | Maintainability |

---

## GROUP A — Typography Respiro (30-40 min)

### A1 — Spacing H1 ↔ lede ↔ first paragraph

**Bug:** H1 attaccata al lede (zero margin-bottom h1 + zero margin-top p:first). Affligge **TUTTI 326 blog post + 19 CPT competenza**.

**Fix:**
```css
/* In sections.css, dopo il blocco Recovery v0.9.0 fix */

/* ═══════════════════════════════════════════════════════════════
   EDITORIAL REFINEMENT — Wall of text breaker
   Obiettivo: trasformare la prosa da "blog WP standard" a "rivista
   editoriale" (Aeon / Atlantic / Bick Law model)
   ═══════════════════════════════════════════════════════════════ */

/* A1 — H1 hero post breathing room */
.sl-post__title,
.sl-post__hero h1,
.sl-page__title {
    margin-bottom: 32px;
}

/* Lede / first paragraph editoriale italic serif */
.sl-post__hero p,
.sl-post__lede,
.sl-post__body > p:first-of-type,
.entry-content > p:first-of-type {
    font-family: var(--font-display);
    font-size: clamp(20px, 1.6vw, 24px);
    font-style: italic;
    line-height: 1.55;
    color: var(--text);
    margin-block: 0 56px;
    max-width: 56ch;
}

/* Drop-cap solo su single blog post body */
body.single-post .sl-post__body > p:first-of-type::first-letter,
body.single-post .entry-content > p:first-of-type::first-letter {
    font-family: var(--font-display);
    float: left;
    font-size: 4.2em;
    line-height: 0.85;
    margin: 8px 14px 0 0;
    color: var(--primary);
    font-weight: 400;
}
```

### A2 — H2/H3 sub-section spacing

**Bug:** H2 senza respiro sopra/sotto, sembra "decorazione" non sezione.

**Fix:**
```css
/* A2 — H2/H3 dentro prose body */
.sl-post__body h2,
.sl-post__body h3,
.entry-content h2,
.entry-content h3,
.sl-page__prose h2,
.sl-page__prose h3 {
    font-family: var(--font-display);
    font-weight: 400;
    color: var(--primary);
    line-height: 1.15;
    max-width: 24ch;
}

.sl-post__body h2,
.entry-content h2,
.sl-page__prose h2 {
    font-size: clamp(28px, 3vw, 44px);
    margin-block: 80px 24px;
}

.sl-post__body h3,
.entry-content h3,
.sl-page__prose h3 {
    font-size: clamp(22px, 2.2vw, 32px);
    margin-block: 56px 16px;
}

/* H2 + H2 adjacent: ridurre stacco */
.sl-post__body h2 + h2,
.entry-content h2 + h2 {
    margin-top: 32px;
}

/* :first-child no top margin (eviter gap iniziale) */
.sl-post__body > h2:first-child,
.sl-post__body > h3:first-child,
.entry-content > h2:first-child,
.entry-content > h3:first-child {
    margin-top: 0;
}
```

### A3 — List `<li>` editoriali con bullet accent

**Bug:** Liste bullet default browser, nere, no editoriale.

**Fix:**
```css
/* A3 — Lists editoriali */
.sl-post__body ul,
.sl-post__body ol,
.entry-content ul,
.entry-content ol,
.sl-page__prose ul,
.sl-page__prose ol {
    margin-block: 32px;
    padding-left: 28px;
    max-width: 60ch;
}

.sl-post__body ul li,
.entry-content ul li,
.sl-page__prose ul li {
    list-style: none;
    position: relative;
    margin-bottom: 12px;
    line-height: 1.7;
}

.sl-post__body ul li::before,
.entry-content ul li::before,
.sl-page__prose ul li::before {
    content: "—";  /* em-dash editoriale invece del bullet */
    position: absolute;
    left: -28px;
    color: var(--accent);
    font-weight: 400;
}

.sl-post__body ol li,
.entry-content ol li,
.sl-page__prose ol li {
    margin-bottom: 12px;
    line-height: 1.7;
}

.sl-post__body ol li::marker,
.entry-content ol li::marker {
    color: var(--accent);
    font-family: var(--font-mono);
    font-size: 0.9em;
}
```

### A4 — Body paragraph max-width + line-height

**Bug:** Wall of text senza breathe, paragrafi troppo larghi (1100px+) per lettura.

**Fix:**
```css
/* A4 — Body prose ottimizzato per lettura */
.sl-post__body > p,
.entry-content > p,
.sl-page__prose > p {
    max-width: 60ch;          /* ~600px su font 18px */
    line-height: 1.75;
    margin-block: 0 24px;
}

/* Pull-quote pattern (per future <blockquote>) */
.sl-post__body blockquote,
.entry-content blockquote {
    margin-block: 56px;
    padding: 0 0 0 32px;
    border-left: 2px solid var(--accent);
    font-family: var(--font-display);
    font-style: italic;
    font-size: clamp(22px, 2vw, 28px);
    line-height: 1.5;
    color: var(--primary);
    max-width: 60ch;
}

/* Strong/em treatment */
.sl-post__body strong,
.entry-content strong {
    font-weight: 500;          /* DM Sans medium invece di bold */
    color: var(--primary);
}

.sl-post__body em,
.entry-content em {
    font-style: italic;
    color: var(--text);
}
```

### Verify GROUP A
```bash
docker compose run --rm wpcli cache flush

# Test su 1 blog post + 1 competenza tier-1 + 1 page
for URL in "/intimazione-tari-annullata-vittoria-dello-studio-legale-saltelli/" "/competenze/diritto-tributario/" "/costi/"; do
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$URL?_=groupAverify")
    echo "  $URL HTTP $HTTP"
done

# Verifica CSS rules attive nel file servito
curl -s "http://localhost:8080/wp-content/themes/saltelli/assets/css/sections.css?_=groupAverify" | grep -c "sl-post__body" 
```

---

## GROUP B — Immagini con cornice editoriale (30-40 min)

### B1 — Auto-wrap `<img>` in `<figure>` + caption + max-width

**Bug:** Immagini sentenze sparate a sinistra senza container, no caption, no max-width. Affligge ~50-100 blog post che hanno screenshot di sentenze.

**Fix CSS:**
```css
/* B1 — Image wrappers editoriali */
.sl-post__body img,
.entry-content img:not(.alignleft):not(.alignright):not(.attachment-saltelli-attorney-portrait) {
    display: block;
    max-width: min(720px, 100%);
    height: auto;
    margin: 56px auto;
    border: 1px solid var(--border);
    background: var(--surface);
}

.sl-post__body figure,
.entry-content figure {
    max-width: 720px;
    margin: 56px auto;
}

.sl-post__body figure img,
.entry-content figure img {
    display: block;
    width: 100%;
    height: auto;
    margin: 0;
}

.sl-post__body figcaption,
.entry-content figcaption,
.wp-caption-text {
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-muted);
    text-align: center;
    margin-top: 16px;
    padding-inline: 16px;
}

/* Featured image hero del blog post */
.sl-post__hero-image,
.sl-post__featured img,
.post-thumbnail img {
    max-width: min(960px, 100%);  /* leggermente più large dell'inline */
    margin: 32px auto 56px;
}
```

### B2 — Featured image gigante stock NON da fixare visualmente

L'audit walkthrough ha segnalato **stock images cartoon AI (uomo terrorizzato, ecc.)** che sono **out-of-mood** rispetto al brand Legal Luxury Minimal. **NON cambiare le immagini** (sono content del cliente), ma **ridimensionarle** affinché non occupino full-viewport:

```css
/* B2 — Featured images non gigantesche */
.sl-post__hero-image,
.sl-post__featured,
.post-thumbnail {
    max-width: 960px;       /* anziché 1562px viewport-wide */
    margin: 0 auto 48px;
}

.sl-post__hero-image img,
.sl-post__featured img,
.post-thumbnail img {
    width: 100%;
    height: auto;
    aspect-ratio: 16 / 9;   /* uniforma anche se foto verticali */
    object-fit: cover;
    object-position: center;
}
```

### B3 — Author bio card piccolo invece di foto gigante autore

**Bug:** Foto Emiliano (1562×800px) appare in fondo blog post come "decoration".

**Fix:** Verifica come il template `single.php` rende l'autore. Cerca pattern tipo `<div class="sl-post__author">` o `wp-image-2683` sul fondo post. Aggiungi CSS per ridurla a card.

```css
/* B3 — Author bio card piccolo */
.sl-post__author,
.sl-post__author-card,
.sl-author-bio {
    display: grid;
    grid-template-columns: 80px 1fr;
    gap: 20px;
    align-items: start;
    max-width: 60ch;
    margin: 80px auto 48px;
    padding: 32px 0;
    border-top: 1px solid var(--border);
}

.sl-post__author img,
.sl-post__author-card img,
.sl-author-bio img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    object-position: center top;
    border-radius: 0;        /* squared, editoriale */
    margin: 0;               /* override max-width 720px del B1 */
}

.sl-post__author-name,
.sl-author-bio__name {
    font-family: var(--font-display);
    font-size: 18px;
    margin: 0 0 4px;
}

.sl-post__author-role,
.sl-author-bio__role {
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-muted);
}

.sl-post__author-link,
.sl-author-bio__link {
    color: var(--accent);
    text-decoration: none;
    font-size: 13px;
    margin-top: 8px;
    display: inline-block;
}
```

**Se il template `single.php` non emette già `.sl-post__author` o classe simile**, identifica come emette autore (probabilmente raw `the_post_thumbnail()` di nuovo o `<img>` di ATT 2683 inline) e:

**Opzione 1** (preferita, CSS-only): scope override su `.sl-post__body img[src*="AvvEmiliano"]` o `.sl-post__body img[width="600"]` per ridurla.

**Opzione 2** (se il template lo richiede): edit `single.php` per emettere `<aside class="sl-post__author"><img>...</aside>` invece di markup precedente.

### Verify GROUP B
```bash
docker compose run --rm wpcli cache flush
HTML=$(curl -s "http://localhost:8080/intimazione-tari-annullata-vittoria-dello-studio-legale-saltelli/?_=groupBverify")
echo "  Images count: $(echo "$HTML" | grep -c '<img')"
echo "  Figure wrappers: $(echo "$HTML" | grep -c '<figure')"
# Verifica che class .sl-post__body sia attiva
echo "  sl-post__body class hits: $(echo "$HTML" | grep -c 'sl-post__body')"
```

---

## GROUP C — Routing/Content (30-40 min)

### C1 — `/lo-studio/` rotto: redirect a blog post invece di pagina

**Bug:** apri `/lo-studio/` → vai a `/lo-studio-legale-saltelli-fa-annullare-fermo-amministrativo-...`. Il menu "Studio" è broken.

**Cause:** WP rewrite cerca page con slug `lo-studio` ma trova solo post che inizia con quella stringa. Probabile la page WP ha slug diverso (es. `chi-siamo` o `studio`).

**Diagnosi:**
```bash
# Verifica quale page esiste con potenziale slug
docker compose run --rm wpcli post list --post_type=page --post_status=publish --fields=ID,post_name,post_title --format=csv 2>&1 | head -20
```

**Fix possibili (in ordine):**

1. **Se la page WP esiste con slug `chi-siamo` o `studio`**: aggiorna il menu `Saltelli Header` per puntare al permalink corretto (NON a `/lo-studio/`):

```bash
# Trova ID page reale
PAGE_ID=$(docker compose run --rm wpcli post list --post_type=page --name=chi-siamo --field=ID 2>&1 | grep -oE '^[0-9]+' | head -1)
PAGE_URL=$(docker compose run --rm wpcli post url "$PAGE_ID" 2>&1 | tail -1)

# Trova menu item "Studio" e aggiorna URL
# Via WP-CLI: wp menu item update <item_id> --url="$PAGE_URL"
docker compose run --rm wpcli menu item list saltelli-header --fields=db_id,title,url --format=csv 2>&1 | tail -10

# Trova item "Studio" → aggiorna
# es. wp menu item update 2687 --url=/chi-siamo/ --title=Studio
```

2. **Se manca la page**: crea la page WP con slug `lo-studio` (priorità rispetto al post):

```bash
docker compose run --rm wpcli post create \
    --post_type=page \
    --post_title="Lo studio" \
    --post_name="lo-studio" \
    --post_status=publish \
    --post_content='<div class="sl-page__prose">[Bio studio Saltelli editoriale TBD]</div>' \
    --porcelain
```

**Test post-fix:**
```bash
curl -s -o /dev/null -w "HTTP %{http_code} · %{redirect_url}\n" "http://localhost:8080/lo-studio/"
# Atteso: HTTP 200, no redirect
```

### C2 — `/blog/` archive vuoto (mostra solo categorie, non i 326 post)

**Bug:** apri `/blog/` → vede solo "BLOG / Blog / BLOG LEGALE / Informazioni legali / Leggi tutti". I 326 post pubblicati nel DB **non appaiono**.

**Cause:** template `home.php` o `archive.php` o `index.php` rende WP_Query `category` invece di posts. Oppure usa `query_posts` rotto.

**Diagnosi:**
```bash
# Identifica quale template viene usato per /blog/
ls -la wp-content/themes/saltelli/{home.php,archive.php,index.php} 2>&1

# Verifica WP-CLI quale template è risolto
docker compose run --rm wpcli option get show_on_front 2>&1 | tail -1
docker compose run --rm wpcli option get page_for_posts 2>&1 | tail -1
docker compose run --rm wpcli post list --post_type=page --name=blog --field=ID 2>&1 | tail -1
```

**Fix probabile:** se /blog/ è una page WP (`page_for_posts`), il template `home.php` (o WP fallback `index.php`) è quello che rende. Verifica e fix:

```bash
# Esempio diagnosi: se page_for_posts non è settato
docker compose run --rm wpcli option get page_for_posts
# Se vuoto (0): WP serve home come blog, /blog/ va a un'altra page
# Se ha un ID: /blog/ usa template home.php

# Se home.php non esiste, WP cade su index.php
# Verifica content di index.php / home.php
cat wp-content/themes/saltelli/home.php 2>/dev/null || cat wp-content/themes/saltelli/index.php
```

Se il template loop è rotto (`while (have_posts())` non itera correttamente), correggi con loop classico:

```php
<?php
get_header();
?>
<section class="sl-blog-archive">
    <header class="sl-section-head sl-blog__head">
        <div class="sl-mono">Editoriale</div>
        <h1 class="sl-blog__title">Blog</h1>
        <p class="sl-blog__lede">Articoli, casi vinti, novità giurisprudenziali da Studio Legale Saltelli & Partners.</p>
    </header>

    <div class="sl-blog__list">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article class="sl-blog__item">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>" class="sl-blog__thumb">
                        <?php the_post_thumbnail('medium_large'); ?>
                    </a>
                <?php endif; ?>
                <div class="sl-blog__meta">
                    <?php
                    $cat = get_the_category();
                    if ($cat) echo '<span class="sl-mono">' . esc_html($cat[0]->name) . '</span>';
                    ?>
                    <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('j F Y'); ?></time>
                </div>
                <h2 class="sl-blog__item-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <p class="sl-blog__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p>
            </article>
        <?php endwhile; ?>
            <nav class="sl-pagination">
                <?php the_posts_pagination(['prev_text' => '←', 'next_text' => '→']); ?>
            </nav>
        <?php else : ?>
            <p>Nessun articolo trovato.</p>
        <?php endif; ?>
    </div>
</section>
<?php get_footer(); ?>
```

CSS minimale:
```css
.sl-blog__list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 64px;
    max-width: 960px;
    margin: 0 auto;
    padding-inline: clamp(24px, 5vw, 96px);
}

@media (min-width: 768px) {
    .sl-blog__list {
        grid-template-columns: repeat(2, 1fr);
        gap: 64px 48px;
    }
}

.sl-blog__item {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.sl-blog__thumb {
    display: block;
    aspect-ratio: 16 / 9;
    overflow: hidden;
}

.sl-blog__thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 400ms var(--ease-editorial);
}

.sl-blog__thumb:hover img {
    transform: scale(1.03);
}

.sl-blog__meta {
    display: flex;
    gap: 16px;
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-muted);
}

.sl-blog__item-title {
    font-family: var(--font-display);
    font-size: clamp(22px, 2vw, 28px);
    font-weight: 400;
    line-height: 1.2;
    margin: 0;
}

.sl-blog__item-title a {
    color: var(--primary);
    text-decoration: none;
}

.sl-blog__excerpt {
    color: var(--text-muted);
    line-height: 1.6;
}
```

### C3 — `/contatti/` arricchimento (form + mappa)

**Bug:** content scarno, manca form, mappa, foto.

**Approach lite:** non rifare il form da zero (rischio rottura). **Aggiungi solo**:
1. Mappa Google embed iframe (no JS API key)
2. Sezione "Prima consulenza gratuita" CTA
3. Foto sede placeholder editoriale `Plate II · Via Vannella Gaetani 27`

Edit `page.php` con condition `is_page('contatti')` o cerca template `page-contatti.php` se esiste.

```php
<?php if (is_page('contatti')) : ?>
<section class="sl-page-contatti__map">
    <div class="sl-mono">§ Sede</div>
    <h2>Dove trovarci</h2>
    <div class="sl-page-contatti__map-wrap">
        <iframe
            src="https://www.openstreetmap.org/export/embed.html?bbox=14.235%2C40.828%2C14.243%2C40.832&amp;layer=mapnik&amp;marker=40.830%2C14.239"
            width="100%" height="400"
            style="border: 1px solid var(--border);"
            loading="lazy"
            title="Studio Legale Saltelli - Via Vannella Gaetani 27, Napoli">
        </iframe>
    </div>
    <p class="sl-mono">Via Vannella Gaetani 27, 80121 Napoli — Chiaia</p>
</section>
<?php endif; ?>
```

(OpenStreetMap embed è gratis, no API key, GDPR-friendly.)

### Verify GROUP C
```bash
# C1
echo "C1 — /lo-studio/ verifica:"
curl -s -o /dev/null -w "  HTTP %{http_code} · final URL: %{url_effective}\n" "http://localhost:8080/lo-studio/"

# C2
echo "C2 — /blog/ articles count:"
curl -s "http://localhost:8080/blog/?_=groupCverify" | grep -c "<article"
# Atteso: ≥ 6 articoli (page 1)

# C3
echo "C3 — /contatti/ map embed:"
curl -s "http://localhost:8080/contatti/?_=groupCverify" | grep -c "openstreetmap\|google.com/maps"
```

---

## TASK FINALE — Bump version + smoke test esteso 12 URL (10 min)

```bash
sed -i.bak 's/Version: 0.9.0-beta-recovery/Version: 0.10.0-beta-editorial/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.9.0-beta-recovery')/define('SALTELLI_THEME_VERSION', '0.10.0-beta-editorial')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all
docker compose run --rm wpcli rewrite flush --hard

# Smoke test 12 URL
echo "═══════════ SMOKE TEST EDITORIAL v0.10.0 ═══════════"
for URL in "/" "/costi/" "/lo-studio/" "/blog/" "/competenze/" "/competenze/diritto-tributario/" "/avvocati/" "/avvocati/emiliano-saltelli/" "/avvocati/fabiana-saltelli/" "/tipo-area/privati/" "/contatti/" "/intimazione-tari-annullata-vittoria-dello-studio-legale-saltelli/"; do
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$URL?_=v010final")
    H1=$(curl -s "http://localhost:8080$URL?_=v010final" | grep -c "<h1")
    printf "  %-60s HTTP %s · %sH1\n" "$URL" "$HTTP" "$H1"
done
```

Tutti devono dare HTTP 200, 1 H1.

---

## Report finale

`.claude/knowledge/design/sessione-1/reports/editorial-refinement-v0.10.0/REPORT.md`:

1. ✅/❌ ciascuno dei GROUP A/B/C
2. Smoke test 12 URL post-fix
3. Diagnosi precisa per ogni bug (cosa hai trovato, cosa hai cambiato)
4. Verifica regressione: i 17 punti delle fasi precedenti preservati?
5. Decisioni autonome
6. Tempo per group
7. Note per l'orchestrator: cosa è stato lasciato come content quality (es. "le stock images del cliente non sono state cambiate, sono state solo ridimensionate via CSS")

Poi **fermati**. Direttore d'orchestra eseguirà nuovo Visual Walkthrough completo prima di passare a Step F (Production Readiness).

---

## Cosa fare se incontri imprevisti

| Situazione | Azione |
|---|---|
| `/lo-studio/` page non esiste in DB e non puoi crearla (perché conflict con post) | Cambia menu URL a `/chi-siamo/` o crea page con slug `studio` |
| Template `single.php` non emette `.sl-post__author` | Identifica markup esistente, usa CSS scope su classe vera (`.sl-post__bio`, `.entry-meta`, ecc.) |
| Filter `the_content` per wrap `<img>` in `<figure>` causa side effect | Skippa, fai solo CSS. La maggior parte degli `<img>` sono già wrappati in `<figure>` se editor WP |
| OpenStreetMap embed bloccato | Fallback Google Maps embed senza API key tramite `<iframe src="https://maps.google.com/maps?q=...&output=embed">` |

---

*v1.0 — Editorial Refinement Agent. Direttore d'orchestra: Claude (chat). Target: v0.10.0-beta-editorial con typography respiro + immagini ariose + routing fix.*
