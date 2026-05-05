# Prompt — Information Architecture Unification v0.13.0

> **Per Claude Code in nuova sessione (o sessione corrente).** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo: 90-120 min. **Comprehensive IA + SEO + visual harmonization** finale prima di Step F.
> **PRECEDENZA:** Layout Harmonization v0.12.0 completato.

---

## Tu sei

L'**Information Architecture Unification Agent**. Audit DOM esteso post-v0.12.0 ha trovato **3 bug strutturali maggiori** che impattano SEO + UX coerenza:

```
B1 — 19 pagine WP del VECCHIO sito ancora attive nel DB
     URL vecchio: /diritto-tributario/, /recupero-crediti/, ecc.
     URL nuovo:   /competenze/diritto-tributario/, ecc.
     → DUE versioni servite simultaneamente (conflitto SEO grave)
     → Google indicizza vecchi URL, backlink esterni puntano lì
     → Content vecchio: Elementor + bullet "•" + H1 dentro post_content

B2 — Breadcrumb NON unificato (5 sistemi diversi)
     Solo 5/12 pagine hanno sl-page__breadcrumb vero (Home / X / Y)
     Le altre 7 hanno eyebrow custom (concettualmente DIVERSO da breadcrumb)
     → Schema BreadcrumbList JSON-LD inconsistente
     → Audit CRO §1.3.1 + Audit GEO requirement non coperti

B3 — /casi/ markup rotto: doppio wrapper <div class="sl-page__prose">
     Causa padding/margin doppio visivo
     post_content emette già .sl-page__prose, page.php lo wrappa di nuovo
```

Cross-reference audit CRO Duccio:
> "Il breadcrumb è diverso da pagina a pagina e va unificato il sistema visivo anche per la SEO"
> "Vanno individuati tutti questi gap tra la nuova UX e la vecchia struttura delle informazioni"

Il tuo lavoro: **chiudere TUTTI questi gap** facendo IA finale + SEO consistency + visual harmonization breadcrumb cross-template.

---

## Letture obbligatorie

1. `CLAUDE.md` — hard constraints
2. `.claude/knowledge/design/sessione-1/reports/visual-walkthrough/REPORT-v0.12.0.md` — audit precedente
3. `.claude/knowledge/project-context.json` — info studio + URL mapping
4. `audit-cro-studiolegalesaltelli.md` (project knowledge) — recommendations IA
5. `wp-content/themes/saltelli/page.php` — template page (wrapper sl-page__prose)
6. `wp-content/themes/saltelli/header.php` — header con eyebrow corrente
7. `wp-content/themes/saltelli/single-competenza.php` — eyebrow tier
8. `wp-content/themes/saltelli/single-avvocato.php` — eyebrow ruolo
9. `wp-content/themes/saltelli/inc/seo/meta-tags.php` — schema generation

---

## Hard rules

| Rule | Reason |
|---|---|
| **Mai sovrascrivere** _thumbnail_id Emiliano + bio_estesa Step D + post_content CPT competenza/avvocato | Content protetto |
| **Mai cancellare hard** le 19 pages vecchie (potrebbero servire per audit/rollback) | Set draft, NON delete |
| Cache flush + smoke test 12+ URL dopo OGNI task | Comprehensive |
| Verifica DOM via curl + grep post-fix | Validation |
| Sequenza obbligata Task 1 → 2 → 3 → 4 → 5 → 6 → 7, NO parallel | File condivisi |
| **Schema BreadcrumbList su TUTTE le pagine** (audit GEO + CRO requirement) | Critico SEO |
| Idempotenza: re-run = stesso output | Stabilità |

---

## TASK 1 — Fix /casi/ doppio wrapper sl-page__prose (10 min)

### Diagnosi
Il post_content della page id 2699 (`/casi/`) inizia con `<div class="sl-page__prose">...</div>` (legacy del prompt v0.12.0). Il template `page.php` poi wrappa il content in un altro `<div class="sl-page__prose">`. Risultato: doppio wrapper + padding/margin doppio.

### Fix — Pulisci post_content
```bash
# Recupera ID
PAGE_CASI=$(docker compose run --rm wpcli post list --post_type=page --name=casi --field=ID 2>&1 | grep -oE '^[0-9]+' | head -1)

# Sovrascrivi post_content rimuovendo wrapper esterno
docker compose run --rm wpcli eval "
\$post = get_post($PAGE_CASI);
\$content = \$post->post_content;
// Rimuovi wrapper sl-page__prose esterno se presente
\$content = preg_replace('#^\s*<div class=\"sl-page__prose\">(.*?)</div>\s*\$#s', '\\1', trim(\$content));
wp_update_post(['ID' => $PAGE_CASI, 'post_content' => trim(\$content)]);
echo 'OK';
"
```

### Verify
```bash
docker compose run --rm wpcli cache flush
HTML=$(curl -s "http://localhost:8080/casi/?_=task1verify")
# Conta wrapper sl-page__prose annidati
NESTED=$(echo "$HTML" | grep -oE '<div class="sl-page__prose">' | wc -l | tr -d ' ')
echo "  sl-page__prose count: $NESTED (atteso 1)"
```

---

## TASK 2 — Disattiva (NON delete) le 19 page WP del vecchio sito (15 min)

### Obiettivo
Le 19 pages vecchie (`/diritto-tributario/`, `/recupero-crediti/`, ecc.) non devono più servire content. Le metti in `post_status=draft` così WP serve 404 → poi Task 3 le redirige al nuovo URL CPT competenza.

### Mapping vecchio→nuovo

```php
// Mapping da popolare in inc/seo/legacy-redirects.php
$LEGACY_MAP = [
    '/recupero-crediti/'                       => '/competenze/recupero-crediti/',
    '/cartelle-esattoriali-e-multe/'           => '/competenze/cartelle-esattoriali-e-multe/',
    '/diritto-societario/'                     => '/competenze/',  // CPT non esiste, redirect a archive
    '/diritto-bancario/'                       => '/competenze/diritto-bancario/',
    '/avvocato-divorzista/'                    => '/competenze/diritto-di-famiglia/',
    '/avvocato-divorzista-italia/'             => '/competenze/diritto-di-famiglia/',
    '/lavoro/'                                 => '/competenze/diritto-del-lavoro/',
    '/eredita-e-successioni/'                  => '/competenze/diritto-delle-successioni/',
    '/condominio-e-locazioni/'                 => '/competenze/diritto-condominiale/',
    '/responsabilita-medica/'                  => '/competenze/responsabilita-medica/',
    '/contrattualistica/'                      => '/competenze/',  // CPT non esiste, redirect a archive
    '/aste-immobiliari/'                       => '/competenze/',
    '/immigrazione/'                           => '/competenze/diritto-dellimmigrazione/',
    '/infortunistica-stradale/'                => '/competenze/risarcimento-danni/',
    '/infortunistica-stradale-italia/'         => '/competenze/risarcimento-danni/',
    '/risarcimento-del-danno/'                 => '/competenze/risarcimento-danni/',
    '/diritto-tributario/'                     => '/competenze/diritto-tributario/',
    '/ricorsi-napoli-obiettivo-valore/'        => '/competenze/cartelle-esattoriali-e-multe/',
    '/invalidita-civile-diritto-previdenziale/'=> '/competenze/diritto-previdenziale/',
    '/diritto-amministrativo/'                 => '/competenze/diritto-amministrativo/',
    '/diritto-penale/'                         => '/competenze/diritto-penale/',
    '/domicilia-la-tua-azienda/'               => '/competenze/domiciliazione-impresa/',
    '/servizi-legali/'                         => '/competenze/',  // page orfana 2019
    '/prenota-un-appuntamento/'                => '/contatti/',  // mantieni se utile, o redirect
];
```

**IMPORTANTE:** verifica via WP-CLI quali slug CPT competenza esistono effettivamente prima di applicare il mapping. Adatta gli URL in base ai veri slug.

```bash
docker compose run --rm wpcli post list --post_type=competenza --post_status=publish --fields=name --format=csv
```

### Disattivazione pages vecchie

```bash
SLUG_VECCHI=(
    "recupero-crediti" "cartelle-esattoriali-e-multe" "diritto-societario"
    "diritto-bancario" "avvocato-divorzista" "avvocato-divorzista-italia"
    "lavoro" "eredita-e-successioni" "condominio-e-locazioni"
    "responsabilita-medica" "contrattualistica" "aste-immobiliari"
    "immigrazione" "infortunistica-stradale" "infortunistica-stradale-italia"
    "risarcimento-del-danno" "diritto-tributario" "ricorsi-napoli-obiettivo-valore"
    "invalidita-civile-diritto-previdenziale" "diritto-amministrativo"
    "diritto-penale" "domicilia-la-tua-azienda" "servizi-legali"
)

for slug in "${SLUG_VECCHI[@]}"; do
    ID=$(docker compose run --rm wpcli post list --post_type=page --name="$slug" --field=ID 2>&1 | grep -oE '^[0-9]+' | head -1)
    if [ -n "$ID" ]; then
        docker compose run --rm wpcli post update "$ID" --post_status=draft 2>&1 | tail -1
        echo "  ✓ $slug (ID $ID) → draft"
    fi
done
```

### Verify
```bash
# Tutte le 19 vecchie URL devono dare 404 (poi Task 3 le redirige)
for path in "/diritto-tributario/" "/recupero-crediti/" "/avvocato-divorzista/" "/lavoro/"; do
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$path?_=task2")
    printf "  %-50s HTTP %s\n" "$path" "$HTTP"
done
```

Atteso: 404 ovunque (Task 3 fixa).

---

## TASK 3 — Sistema redirect legacy 301 (15 min)

### Obiettivo
Implementare redirect 301 SEO-friendly per tutti gli URL vecchi → nuovi.

### File: `inc/seo/legacy-redirects.php`

```php
<?php
/**
 * Saltelli — Legacy URL redirect 301
 * Mapping URL vecchio sito (pre-2026) → nuovo CPT competenza/page.
 * Preserva SEO + backlink esterni esistenti.
 */
defined('ABSPATH') || exit;

if (!function_exists('saltelli_legacy_redirect')) :
function saltelli_legacy_redirect() {
    if (is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) return;

    $request = trim(strtok($_SERVER['REQUEST_URI'] ?? '', '?'), '/');
    $request = '/' . $request . '/';

    static $map = [
        '/recupero-crediti/'                        => '/competenze/recupero-crediti/',
        '/cartelle-esattoriali-e-multe/'            => '/competenze/cartelle-esattoriali-e-multe/',
        '/diritto-bancario/'                        => '/competenze/diritto-bancario/',
        '/avvocato-divorzista/'                     => '/competenze/diritto-di-famiglia/',
        '/avvocato-divorzista-italia/'              => '/competenze/diritto-di-famiglia/',
        '/lavoro/'                                  => '/competenze/diritto-del-lavoro/',
        '/eredita-e-successioni/'                   => '/competenze/diritto-delle-successioni/',
        '/condominio-e-locazioni/'                  => '/competenze/diritto-condominiale/',
        '/responsabilita-medica/'                   => '/competenze/responsabilita-medica/',
        '/immigrazione/'                            => '/competenze/diritto-dellimmigrazione/',
        '/infortunistica-stradale/'                 => '/competenze/risarcimento-danni/',
        '/infortunistica-stradale-italia/'          => '/competenze/risarcimento-danni/',
        '/risarcimento-del-danno/'                  => '/competenze/risarcimento-danni/',
        '/diritto-tributario/'                      => '/competenze/diritto-tributario/',
        '/ricorsi-napoli-obiettivo-valore/'         => '/competenze/cartelle-esattoriali-e-multe/',
        '/invalidita-civile-diritto-previdenziale/' => '/competenze/diritto-previdenziale/',
        '/diritto-amministrativo/'                  => '/competenze/diritto-amministrativo/',
        '/diritto-penale/'                          => '/competenze/diritto-penale/',
        '/domicilia-la-tua-azienda/'                => '/competenze/domiciliazione-impresa/',
        // Pages orfane → archive
        '/diritto-societario/'                      => '/competenze/',
        '/contrattualistica/'                       => '/competenze/',
        '/aste-immobiliari/'                        => '/competenze/',
        '/servizi-legali/'                          => '/competenze/',
        // Prenota appuntamento → contatti
        '/prenota-un-appuntamento/'                 => '/contatti/',
        // Lo studio → chi-siamo (già fixato altrove ma ridondante OK)
        '/lo-studio/'                               => '/chi-siamo/',
    ];

    if (isset($map[$request])) {
        wp_safe_redirect(home_url($map[$request]), 301);
        exit;
    }
}
endif;

add_action('template_redirect', 'saltelli_legacy_redirect', 1);
```

### Include in functions.php
```php
require_once SALTELLI_THEME_DIR . '/inc/seo/legacy-redirects.php';
```

### Verify
```bash
docker compose run --rm wpcli cache flush
docker compose run --rm wpcli rewrite flush --hard

echo ""
echo "─── Verify legacy redirect 301 ───"
for path in "/diritto-tributario/" "/recupero-crediti/" "/avvocato-divorzista/" "/lavoro/" "/diritto-penale/" "/domicilia-la-tua-azienda/"; do
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$path?_=task3")
    FINAL=$(curl -sL -o /dev/null -w "%{url_effective}" "http://localhost:8080$path?_=task3" | sed 's|http://localhost:8080||;s|?.*||')
    printf "  %-50s HTTP %s → %s\n" "$path" "$HTTP" "$FINAL"
done
```

Atteso: tutti HTTP 301, FINAL URL = `/competenze/{slug}/` corretto.

---

## TASK 4 — Breadcrumb unificato system-wide (30 min)

### Obiettivo
**TUTTE le pagine** del sito devono avere `<nav class="sl-page__breadcrumb">` con schema BreadcrumbList JSON-LD coerente. Vincolo audit CRO: "breadcrumb diverso da pagina a pagina va unificato per la SEO".

### Strategia

Crea un helper `saltelli_render_breadcrumb()` che emette markup uniformato + schema. Usalo in TUTTI i template dove ora manca o è inconsistente.

### Helper in `inc/helpers.php`

```php
/**
 * Render breadcrumb uniforme + emit BreadcrumbList schema.
 * Da chiamare ALL'INIZIO di ogni hero section template.
 */
function saltelli_render_breadcrumb($context = null) {
    $crumbs = saltelli_get_breadcrumb_chain($context);
    if (empty($crumbs) || count($crumbs) < 2) return; // homepage skip

    // Schema BreadcrumbList
    $items = [];
    foreach ($crumbs as $i => $c) {
        $items[] = [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $c['title'],
            'item' => $c['url'] ?? '',
        ];
    }
    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
    
    // HTML + schema
    ?>
    <nav class="sl-mono sl-page__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'saltelli'); ?>">
        <?php foreach ($crumbs as $i => $c) : ?>
            <?php if ($i > 0) echo '<span class="sl-page__breadcrumb-sep" aria-hidden="true"> / </span>'; ?>
            <?php if ($i < count($crumbs) - 1 && !empty($c['url'])) : ?>
                <a href="<?php echo esc_url($c['url']); ?>"><?php echo esc_html($c['title']); ?></a>
            <?php else : ?>
                <span aria-current="page"><?php echo esc_html($c['title']); ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
    <script type="application/ld+json">
    <?php echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php
}
```

### Estendi `saltelli_get_breadcrumb_chain()`

Verifica che funzioni per tutti i context:
- Homepage: skip (empty array)
- WP page: Home / {Page title}
- Single CPT competenza: Home / Competenze / {Term tier} / {Title}
- Single CPT avvocato: Home / Avvocati / {Title}
- Single blog post: Home / Editoriale / {Category} / {Title}
- Archive avvocati/competenze: Home / Avvocati o Home / Competenze
- Taxonomy tipo-area: Home / Competenze / {Term name}

### Apply nei template

In ordine di priority:

1. **`page.php`** già usa `saltelli_render_breadcrumb()` → verifica
2. **`front-page.php`** homepage → SKIP breadcrumb (homepage non ha senso)
3. **`single-competenza.php`** → AGGIUNGI breadcrumb prima dell'eyebrow tier
4. **`single-avvocato.php`** → AGGIUNGI breadcrumb prima dell'eyebrow ruolo
5. **`single.php`** blog post → AGGIUNGI breadcrumb (manca completamente)
6. **`archive-avvocato.php`** + **`archive-competenza.php`** → AGGIUNGI breadcrumb prima dell'eyebrow archive
7. **`taxonomy-tipo-area.php`** → ha già, verifica formato uniforme

**Esempio applicazione su `single-competenza.php`:**

```php
<header class="sl-competenza__hero">
    <div class="sl-container">
        <?php saltelli_render_breadcrumb('competenza'); ?>
        
        <?php if ($is_tier_1) : ?>
            <div class="sl-mono sl-competenza__tier">Tier 1 · Approfondimento · <?php echo esc_html($tier_label); ?></div>
        <?php endif; ?>
        
        <h1 class="sl-competenza__title"><?php the_title(); ?></h1>
        ...
    </div>
</header>
```

L'eyebrow tier rimane MA viene posizionato DOPO il breadcrumb (il breadcrumb è sempre primo elemento del hero).

### CSS per breadcrumb consistency

In `sections.css`:

```css
/* ═══════════════════════════════════════════════════════════════
   Breadcrumb unificato sl-page__breadcrumb
   Audit CRO requirement: sistema visivo uniforme + schema SEO
   ═══════════════════════════════════════════════════════════════ */

.sl-page__breadcrumb {
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: var(--space-3);  /* 24px gap to title */
    display: block;
}

.sl-page__breadcrumb a {
    color: var(--text-muted);
    text-decoration: none;
    transition: color var(--dur-fast) var(--ease-editorial);
}

.sl-page__breadcrumb a:hover,
.sl-page__breadcrumb a:focus-visible {
    color: var(--accent);
}

.sl-page__breadcrumb-sep {
    color: var(--text-muted);
    opacity: 0.5;
    margin-inline: 4px;
}

.sl-page__breadcrumb [aria-current="page"] {
    color: var(--primary);
    font-weight: 500;
}
```

### Verify Task 4
```bash
docker compose run --rm wpcli cache flush

echo ""
echo "─── Breadcrumb unificato cross-page ───"
for URL in "/" "/chi-siamo/" "/avvocati/" "/competenze/" "/blog/" "/contatti/" "/costi/" "/casi/" "/competenze/diritto-tributario/" "/avvocati/emiliano-saltelli/" "/tipo-area/privati/" "/intimazione-tari-annullata-vittoria-dello-studio-legale-saltelli/"; do
    HTML=$(curl -s "http://localhost:8080$URL?_=task4")
    BC_NAV=$(echo "$HTML" | grep -c '<nav[^>]*sl-page__breadcrumb')
    BC_SCHEMA=$(echo "$HTML" | grep -c 'BreadcrumbList')
    printf "  %-65s nav-bc:%s schema-bc:%s\n" "$URL" "$BC_NAV" "$BC_SCHEMA"
done
```

**Atteso:**
- Homepage: nav-bc=0 (skip homepage), schema-bc=0
- Tutte le altre 11 URL: nav-bc=1, schema-bc=1

---

## TASK 5 — Hero header sistema visivo unificato (15 min)

### Obiettivo
**Ordine VERTICALE** del hero header sezione (top → bottom) deve essere identico su TUTTE le pagine:

```
1. Breadcrumb (sl-page__breadcrumb)         ← NUOVO uniforme
2. Eyebrow opzionale (tier/role/category)   ← context-specific
3. H1 / Title
4. Lede (sl-page__lede / sl-post__lede)
5. CTA opzionale (sl-btn)
```

### Audit attuale (cosa cambia)

| Template | Pre v0.13 | Post v0.13 |
|---|---|---|
| Homepage | Eyebrow | (skip breadcrumb) + Eyebrow + H1 + Lede + CTA + Subline italic |
| Page generic | Breadcrumb + H1 | Breadcrumb + H1 + (Lede se presente) |
| Single competenza | Eyebrow tier + H1 | **Breadcrumb** + Eyebrow tier + H1 + Answer capsule |
| Single avvocato | Eyebrow ruolo + H1 | **Breadcrumb** + Eyebrow ruolo + H1 + Bio breve |
| Single blog | (niente) + H1 + meta | **Breadcrumb** + Eyebrow categoria + H1 + Lede |
| Archive avvocato | Eyebrow archive + H1 | **Breadcrumb** + Eyebrow archive + H1 + Lede |
| Archive competenza | Eyebrow archive + H1 | **Breadcrumb** + Eyebrow archive + H1 + Lede |
| Taxonomy | Breadcrumb + H1 | (already OK) |

### Verifica visiva richiesta

Dopo Task 4-5 implementati, l'orchestrator (Claude in chat) farà visual walkthrough con Chrome MCP (se torna online) o Python audit.

---

## TASK 6 — Homepage Fidelity Audit vs Claude Design (35 min · CRITICO)

### Obiettivo
Match fedele tra `front-page.php` rendering e il riferimento `homepage-desktop.jsx` (Claude Design Sessione 1) + PNG `home-desktop.png` su Desktop di Duccio.

Cross-iterations precedenti (Pain Points, Editorial, Layout Harmonization, IA Unification) hanno introdotto lievi drift dai valori esatti del design originale. Questo task è il **return-to-source** finale.

### Letture obbligatorie aggiuntive

1. `.claude/knowledge/design/sessione-1/homepage-desktop.jsx` — riferimento JSX completo (446 righe)
2. `.claude/knowledge/design/sessione-1/tokens.css` — tokens (NON modificare valori)
3. `wp-content/themes/saltelli/front-page.php` — implementazione corrente da matchare al JSX
4. `wp-content/themes/saltelli/header.php` — header (D1+D2)
5. `wp-content/themes/saltelli/assets/css/sections.css` — la maggior parte dei fix qui

### 12 discrepanze identificate dal direttore d'orchestra

#### D1 — Header logo: manca sub-eyebrow

**JSX:**
```jsx
<div style={{ fontFamily: "var(--font-display)", fontSize: 22, color: "var(--primary)", letterSpacing: "-0.01em", lineHeight: 1.1 }}>
    Saltelli &amp; Partners
</div>
<div className="sl-mono" style={{ fontSize: 10, marginTop: 2 }}>Studio Legale · Napoli</div>
```

**Attuale:** wordmark "Saltelli & Partners" senza sub-eyebrow.

**Fix:** in `header.php`, sotto il wordmark, aggiungi:
```html
<div class="sl-mono sl-header__sub" style="font-size:10px;margin-top:2px;">Studio Legale · Napoli</div>
```

#### D2 — Header padding orizzontale

**JSX:** `padding: "24px 96px"` desktop fisso.
**Attuale:** `clamp(24, 5vw, 96)` post-Layout Harmonization (a 1440 = 72px).

**Fix:** override desktop fisso `padding: 24px 96px` per match. Mobile resta clamp.

#### D3 — Hero `min-height: 820px` (NON 100vh)

**JSX:** `minHeight: 820`.
**Attuale:** `min-height: calc(100vh - var(--space-7))` post-v0.12.

**Fix:** ripristina `min-height: 820px` desktop. A 1440×900 viewport, 820 + header 78 = 898 < 900 → colophon visibile above-fold ✓.

```css
.sl-hero {
    min-height: 820px;
    padding: 120px 96px 80px;  /* JSX exact */
}
```

#### D4 — Hero h1 font-size clamp esatto

**JSX:** `fontSize: "clamp(80px, 9vw, 132px)"`, line-height 0.98, letter-spacing -0.035em, weight 400.

**Attuale:** `clamp(64px, 8vw, 120px)` post-v0.12 (ridotto per compattezza).

**Fix:** ripristina valori JSX:
```css
.sl-hero__headline {
    font-size: clamp(80px, 9vw, 132px);
    line-height: 0.98;
    letter-spacing: -0.035em;
    font-weight: 400;
    margin-bottom: 56px;
}
.sl-hero__headline > span {
    display: inline-block;
    margin-right: 24px;
}
```

#### D5 — Hero eyebrow margin-bottom 64px

**JSX:** eyebrow "Studio Legale · Napoli · Chiaia · Dal 1999" → `marginBottom: 64`.

**Fix:** `.sl-hero__eyebrow { margin-bottom: 64px; }` esplicito.

#### D6 — Hero lede dimensioni esatte

**JSX:**
```jsx
fontFamily: "var(--font-display)", fontSize: 22, fontStyle: "italic",
fontWeight: 400, lineHeight: 1.5, color: "var(--text)",
maxWidth: "44ch", marginBottom: 64
```

**Attuale:** `clamp(20-24)px`.

**Fix:**
```css
.sl-hero__subheadline,
.sl-hero__lede {
    font-family: var(--font-display);
    font-size: 22px;
    font-style: italic;
    font-weight: 400;
    line-height: 1.5;
    color: var(--text);
    max-width: 44ch;
    margin-bottom: 64px;
}
```

Testo atteso (verifica corrisponda):
> "Studio Legale Saltelli & Partners. Quattro avvocati a Chiaia, diciannove aree di pratica, vent'anni di lavoro accanto a famiglie e imprese di Napoli."

#### D7 — Hero CTA testo (DECISIONE ORCHESTRATOR)

**JSX:** `<button className="sl-btn sl-btn--primary">Prenota un primo incontro →</button>`

**Attuale:** "Prenota una consulenza gratuita" + subline italic "Prima consulenza conoscitiva..." (post-Audit Alignment).

**Decisione orchestrator:** **MANTIENI** "Prenota una consulenza gratuita" + subline. Audit CRO Quick Win #1 esplicito ("aggiungere 'Prima consulenza conoscitiva gratuita' ovunque, +10-20% conversioni"). Il design Claude originale è pre-CRO audit.

**NON ripristinare il testo JSX**. Solo verifica che la subline sia editorial italic (post v0.10) NON mono caps aggressiva.

#### D8 — Hero colophon styling esatto

**JSX:**
```jsx
alignSelf: "end", borderLeft: "1px solid var(--border)", paddingLeft: 32

<div className="sl-mono" style={{ marginBottom: 16 }}>Colophon</div>
<div style={{ display: "grid", gap: 24, fontSize: 13, color: "var(--text)", lineHeight: 1.7 }}>
    <div>
        <div className="sl-mono" style={{ marginBottom: 6 }}>Indirizzo</div>
        Via Vannella Gaetani, 27<br/>80121 Napoli — Chiaia
    </div>
    <div>
        <div className="sl-mono" style={{ marginBottom: 6 }}>Orari</div>
        Lun – Ven · 09:30 – 18:30<br/>Sabato su appuntamento
    </div>
    <div>
        <div className="sl-mono" style={{ marginBottom: 6 }}>Contatti</div>
        <a className="sl-link">studio@saltellipartners.it</a><br/>
        <span className="sl-mono">+39 081 245 67 89</span>
    </div>
</div>
```

**Attuale:** colophon presente con dati `info@studiolegalesaltelli.it` + `+39 081 1813 1119` (project-context corretto).

**Fix:** mantieni i **dati reali** (NON quelli JSX placeholder), ma applica:
```css
.sl-hero__colophon {
    align-self: end;
    border-left: 1px solid var(--border);
    padding-left: 32px;
}

.sl-hero__colophon-title {  /* "Colophon" */
    margin-bottom: 16px;
}

.sl-hero__colophon-grid {
    display: grid;
    gap: 24px;
    font-size: 13px;
    color: var(--text);
    line-height: 1.7;
}

.sl-hero__colophon-grid .sl-mono {
    margin-bottom: 6px;
}
```

E verifica che il template `front-page.php` emetta esattamente queste 3 sub-section: Indirizzo + Orari + Contatti, nell'ordine indicato.

#### D9 — Hero scroll indicator: orizzontale bottom-left (NON verticale destra)

**JSX:**
```jsx
position: "absolute", bottom: 32, left: 96
display: "flex", alignItems: "center", gap: 12
linea 1×32px var(--text-muted) + "Scorri" sl-mono
```

**Attuale:** "SCORRI" mono uppercase **verticale a destra**.

**Fix CRITICO:**
```css
.sl-hero__scroll-indicator {
    position: absolute;
    bottom: 32px;
    left: 96px;
    display: flex;
    align-items: center;
    gap: 12px;
    /* RIMUOVI eventuali writing-mode: vertical-rl */
}

.sl-hero__scroll-indicator::before {
    content: "";
    display: block;
    width: 1px;
    height: 32px;
    background: var(--text-muted);
}
```

Template:
```php
<div class="sl-hero__scroll-indicator">
    <span class="sl-mono">Scorri</span>
</div>
```

#### D10 — Sezioni padding-block 128px

**JSX:** TUTTE le sezioni post-hero (`§ 01 Aree`, `§ 02 Lo studio`, `§ 03 Avvocati`, `§ 04 Casi`, ecc.) hanno `padding: "128px 96px"` esatto.

**Attuale:** `var(--space-7)` (80px) post-Layout Harmonization.

**Fix:**
```css
.sl-areas,
.sl-studio,
.sl-team,
.sl-cases,
.sl-press,
.sl-contact {
    padding-block: 128px;
}

@media (max-width: 1023px) {
    .sl-areas,
    .sl-studio,
    .sl-team,
    .sl-cases,
    .sl-press,
    .sl-contact {
        padding-block: var(--space-7);  /* 80px mobile */
    }
}
```

#### D11 — § 02 Lo studio asimmetria specifica

**JSX:**
```jsx
<div style={{ maxWidth: 640, marginLeft: "20%", fontSize: 19, lineHeight: 1.75 }}>
```

**Attuale:** content centrato.

**Fix asimmetria deliberata:**
```css
.sl-studio__body {
    max-width: 640px;
    margin-left: 20%;
    font-size: 19px;
    line-height: 1.75;
    color: var(--text);
}

@media (max-width: 1023px) {
    .sl-studio__body {
        max-width: 100%;
        margin-left: 0;
    }
}
```

#### D12 — Drop-cap "L" specifiche numeriche esatte

**JSX:**
```jsx
fontFamily: "var(--font-display)", fontSize: 84, float: "left",
lineHeight: 0.85, marginRight: 16, marginTop: 8, color: "var(--primary)"
```

**Attuale:** drop-cap presente (post Editorial v0.10) ma con valori clamp/em.

**Fix valori esatti:**
```css
.sl-studio__body p:first-of-type::first-letter {
    font-family: var(--font-display);
    font-size: 84px;
    float: left;
    line-height: 0.85;
    margin-right: 16px;
    margin-top: 8px;
    color: var(--primary);
    font-weight: 400;
}
```

### Verify Task 6

```bash
docker compose run --rm wpcli cache flush

echo "─── Homepage fidelity verify ───"
HTML=$(curl -s "http://localhost:8080/?_=task7")

echo "  D1 Header sub-eyebrow 'Studio Legale · Napoli': $(echo "$HTML" | grep -c 'sl-header__sub\|font-size:10px.*Studio Legale.*Napoli')"
echo "  D5 Hero eyebrow 'Dal 1999': $(echo "$HTML" | grep -c 'Dal 1999')"
echo "  D6 Lede 'Quattro avvocati a Chiaia': $(echo "$HTML" | grep -c 'Quattro avvocati a Chiaia')"
echo "  D8 Colophon labels: $(echo "$HTML" | grep -cE 'Indirizzo|Orari|Contatti')"
echo "  D9 Scroll indicator 'Scorri': $(echo "$HTML" | grep -c 'sl-hero__scroll-indicator')"
echo "  D11 'Una bottega' lo studio: $(echo "$HTML" | grep -c 'Una bottega')"

echo ""
echo "─── CSS rules servite ───"
CSS=$(curl -s "http://localhost:8080/wp-content/themes/saltelli/assets/css/sections.css?_=task7")
echo "  D3 hero min-height 820px: $(echo "$CSS" | grep -c '820px')"
echo "  D4 hero h1 clamp 132: $(echo "$CSS" | grep -c '132px')"
echo "  D10 sezioni 128px padding-block: $(echo "$CSS" | grep -c 'padding-block: 128')"
echo "  D11 .sl-studio__body marginLeft 20: $(echo "$CSS" | grep -c 'margin-left: 20%\|margin-left:20%')"
echo "  D12 drop-cap font-size 84: $(echo "$CSS" | grep -c 'font-size: 84px\|font-size:84px')"
```

Tutti gli hit attesi ≥ 1.

### Conferma visiva richiesta

Dopo Task 6, l'orchestrator (Claude in chat) farà **side-by-side compare** tra screenshot homepage current e PNG reference su Desktop Duccio.

---

## TASK 7 — Bump version + smoke test esteso (5 min)

```bash
sed -i.bak 's/Version: 0.12.0-beta-layout-harmonized/Version: 0.13.0-beta-ia-unified/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.12.0-beta-layout-harmonized')/define('SALTELLI_THEME_VERSION', '0.13.0-beta-ia-unified')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all
docker compose run --rm wpcli rewrite flush --hard

echo ""
echo "═══════════ FINAL SMOKE TEST v0.13.0 ═══════════"
for URL in "/" "/chi-siamo/" "/avvocati/" "/competenze/" "/blog/" "/contatti/" "/costi/" "/casi/" "/competenze/diritto-tributario/" "/competenze/diritto-del-lavoro/" "/avvocati/emiliano-saltelli/" "/tipo-area/privati/" "/intimazione-tari-annullata-vittoria-dello-studio-legale-saltelli/" "/diritto-tributario/" "/recupero-crediti/" "/avvocato-divorzista/" "/lavoro/" "/diritto-penale/"; do
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$URL?_=v013final")
    FINAL=$(curl -sL -o /dev/null -w "%{url_effective}" "http://localhost:8080$URL?_=v013final" 2>/dev/null | sed 's|http://localhost:8080||;s|?.*||' | head -c 50)
    H1=$(curl -sL "http://localhost:8080$URL?_=v013final" | grep -c "<h1")
    printf "  %-55s direct:%s final:%-50s H1:%s\n" "$URL" "$HTTP" "$FINAL" "$H1"
done
```

Atteso:
- 13 nuovi URL: HTTP 200, 1 H1
- 5 vecchi URL (legacy): HTTP 301, final URL su nuova destinazione, 1 H1 sulla destination

---

## Report finale

`.claude/knowledge/design/sessione-1/reports/ia-unification-v0.13.0/REPORT.md`:

1. ✅/❌ Task 1-7
2. Smoke test 18 URL (12 nuovi + 5 legacy + homepage)
3. Mapping legacy → nuovo eseguito (tabella)
4. Breadcrumb cross-page audit (tabella DOM count)
5. /casi/ doppio wrapper risolto?
6. 19 pages vecchie status `draft`?
7. Schema BreadcrumbList su 11/12 pagine (no homepage)?
8. **Homepage Fidelity Audit (Task 6): tutte le 12 D risolte?**
9. Decisioni autonome
10. Tempo per task
11. **GO/NO-GO per Step F** dal tuo punto di vista

Poi **fermati**. Direttore d'orchestra eseguirà visual walkthrough esteso (Chrome MCP).

---

## Cosa fare se imprevisti

| Situazione | Azione |
|---|---|
| Slug CPT competenza diverso da quello atteso nel mapping | Verifica con `wp post list --post_type=competenza --fields=name`, adatta legacy-redirects.php |
| `wp_safe_redirect` non funziona perché WordPress override | Usa hook priority `1` (early) o `template_redirect` priority 0 |
| Breadcrumb già presente in qualche template causa duplicazione | Cerca `saltelli_render_breadcrumb` o `sl-page__breadcrumb` in tutti i template, rimuovi vecchi |
| Schema BreadcrumbList rotto su qualche pagina | Verifica `saltelli_get_breadcrumb_chain()` ritorni array consistente, non null |
| Page "Lavora con noi" / "Conferma" / "Prenota appuntamento" rotte | Lascia attive (sono utility, non praticate), no redirect necessario |

---

*v1.1 — Information Architecture Unification + SEO consistency + Homepage Fidelity vs Claude Design. Direttore d'orchestra: Claude (chat). Target: v0.13.0 con breadcrumb uniforme + schema BreadcrumbList su 11/12 pagine + 19 legacy URLs redirect 301 + /casi/ doppio wrapper risolto + 12 discrepanze homepage fixate.*
