# Prompt — GEO Engineer Agent (SHIP MODE 24H)

> **Per Claude Code in tmux pane 3.** Apri questa cartella (`saltelli-wp/`), leggi questo file, eseguilo. Non improvvisare. Non comunicare con gli altri 2 agent (Style & Animation, Theme Architect): lavorate su file disjoint.

---

## Tu sei

Il **GEO Engineer Agent** del build Saltelli. Il tuo lavoro è **rendere il sito AI-ready al 100%** prima della demo cliente.

In concreto:

1. Convertire i **5 template schema JSON-LD** (`geo-assets/schema/*.json`) in **PHP partial runtime** che generano output dinamici basati sui dati WP reali
2. Implementare il **router schema** (`schema-loader.php`) che inietta il giusto blocco JSON-LD per ogni tipo di pagina
3. Implementare i **meta tag SEO** (description + Open Graph + Twitter Cards), con coabitazione safe se Yoast è attivo
4. Implementare il **routing `/llms.txt`** dinamico via WP rewrite
5. Implementare il **filter `robots_txt`** WordPress per servire la versione AI-friendly
6. Validare tutto con i tool ufficiali (schema.org validator + Google Rich Results Test)

**Non tocchi**:
- File CSS/JS (Style Agent)
- Template PHP root del tema (Theme Architect)
- ACF JSON (Theme Architect)

---

## Letture obbligatorie (in quest'ordine, prima di scrivere codice)

1. `CLAUDE.md` — hard constraints
2. `BRIEF_Saltelli_WordPress.md` — sezione "Requisiti tecnici GEO"
3. `.claude/knowledge/project-context.json` — `client_data`, `team_members`, `geo_requirements`
4. `geo-assets/schema/README.md` — strategia integrazione PHP partial-driven
5. **Tutti i 5 file** `geo-assets/schema/0[1-5]-*.json` — sono i template di partenza
6. `geo-assets/llms.txt` + `geo-assets/robots.txt` — i file da servire dinamicamente
7. `wp-content/themes/saltelli/inc/schema/*.php` — partial scaffolded, oggi vuoti con TODO
8. `wp-content/themes/saltelli/inc/seo/*.php` — `meta-tags.php` e `ai-files.php` scaffolded

---

## Hard rules (non negoziabili)

| Rule | Reason |
|---|---|
| `wp_json_encode($schema, JSON_UNESCAPED_SLASHES \| JSON_UNESCAPED_UNICODE)` sempre | UTF-8 italiano + URL leggibili |
| Output schema dentro `<script type="application/ld+json">...</script>` nel `<head>` | Standard schema.org |
| Hook su `wp_head` priority 5 (PRIMA dei plugin SEO) per evitare duplicati gestibili | Coordinamento con Yoast |
| **Skip schema duplicati** se Yoast/Rank Math sono attivi e già generano lo stesso tipo (LegalBusiness, Person, FAQPage) | No duplicati che confondono Google |
| `@id` come URL canonico stabile per ogni entità | Knowledge graph coerente |
| Schema **valida sempre** su `validator.schema.org` PRIMA di considerare done | Zero errori |
| Telefono in formato **E.164** (`+390811813119`) | schema.org PhoneNumber spec |
| Campi `_TODO_*` lasciati nei JSON (sameAs social, alumniOf) → trasformare in `// TODO Duccio: ...` PHP comments | Visibilità ai gap |
| Mai esporre dati sensibili (P.IVA solo in footer, niente PEC se non pubblica già) | Privacy |

---

## Task 1 — `partial-organization.php` (15 min)

Converti `geo-assets/schema/01-organization-legalservice.json` in PHP. Pattern:

```php
<?php
/**
 * Schema JSON-LD: Organization + LegalService + WebSite (header globale)
 * Iniettato su OGNI pagina (vedi schema-loader.php).
 */
defined('ABSPATH') || exit;

$telephone_e164 = '+390811813119'; // TODO Duccio: spostare in ACF Options se serve mutabilità
$site_url = home_url('/');

$organization = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => ['Organization', 'LegalService'],
            '@id' => $site_url . '#organization',
            'name' => 'Studio Legale Emiliano Saltelli & Partners',
            'alternateName' => ['Studio Legale Saltelli', 'Saltelli & Partners'],
            'url' => $site_url,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => SALTELLI_THEME_URI . '/assets/images/logo.png', // TODO se non esiste fallback safe
            ],
            'telephone' => $telephone_e164,
            'email' => 'info@studiolegalesaltelli.it',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Via Vannella Gaetani, 27',
                'addressLocality' => 'Napoli',
                'postalCode' => '80121',
                'addressRegion' => 'NA',
                'addressCountry' => 'IT',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 40.8295,    // TODO Duccio: verificare lat/lng esatti Via Vannella Gaetani 27
                'longitude' => 14.2417,
            ],
            'openingHoursSpecification' => [/* ... */],
            'areaServed' => ['Napoli', 'Campania', 'Italia'],
            'knowsAbout' => [/* lista da JSON */],
            'sameAs' => [
                // TODO Duccio: aggiungere quando Ludovica recupera i social URL
                // 'https://www.instagram.com/...',
                // 'https://www.linkedin.com/company/...',
                // 'https://www.facebook.com/...',
            ],
            'founder' => [
                '@type' => 'Person',
                '@id' => home_url('/avvocati/emiliano-saltelli/') . '#person',
                'name' => 'Emiliano Saltelli',
            ],
        ],
        [
            '@type' => 'WebSite',
            '@id' => $site_url . '#website',
            'url' => $site_url,
            'name' => get_bloginfo('name'),
            'publisher' => ['@id' => $site_url . '#organization'],
            'inLanguage' => 'it-IT',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => $site_url . '?s={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ],
    ],
];
?>
<script type="application/ld+json">
<?php echo wp_json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>
```

**Coabitazione Yoast/Rank Math**: se uno dei due è attivo, **non emettere il blocco Organization** (lo emettono loro). Ma SI emetti `LegalService` separatamente perché Yoast non lo gestisce.

```php
$has_yoast = defined('WPSEO_VERSION') || function_exists('YoastSEO');
$has_rankmath = class_exists('RankMath') || defined('RANK_MATH_VERSION');
if (!$has_yoast && !$has_rankmath) {
    // Emit full graph (Organization + LegalService + WebSite)
} else {
    // Emit only LegalService (a parte) per non duplicare Organization
}
```

---

## Task 2 — `partial-attorney.php` (15 min)

Converti `geo-assets/schema/02-attorneys.json` in PHP, ma **dinamico per il singolo CPT avvocato corrente**:

```php
<?php
defined('ABSPATH') || exit;
if (!is_singular('avvocato')) return;

$post_id = get_the_ID();
$slug = get_post_field('post_name', $post_id);
$site = home_url('/');

$attorney = [
    '@context' => 'https://schema.org',
    '@type' => ['Person', 'Attorney'],
    '@id' => $site . 'avvocati/' . $slug . '/#person',
    'name' => get_the_title($post_id),
    'jobTitle' => function_exists('get_field') ? get_field('ruolo_breve', $post_id) : '',
    'description' => function_exists('get_field') ? get_field('bio_breve', $post_id) : '',
    'image' => has_post_thumbnail($post_id) ? get_the_post_thumbnail_url($post_id, 'saltelli-attorney-portrait') : '',
    'url' => get_permalink($post_id),
    'worksFor' => ['@id' => $site . '#organization'],
    'knowsAbout' => function_exists('get_field') ? (array) get_field('specializzazioni', $post_id) : [],
    'telephone' => function_exists('get_field') ? get_field('telefono_pubblico', $post_id) : '+390811813119',
    'email' => function_exists('get_field') ? get_field('email_pubblica', $post_id) : 'info@studiolegalesaltelli.it',
    'sameAs' => array_filter([function_exists('get_field') ? get_field('same_as_linkedin', $post_id) : '']),
    // TODO Duccio: alumniOf da popolare quando Ludovica recupera dati formazione 4 avvocati
];

// Strip empty
$attorney = array_filter($attorney, fn($v) => $v !== '' && $v !== null && $v !== []);
?>
<script type="application/ld+json">
<?php echo wp_json_encode($attorney, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>
```

---

## Task 3 — `partial-faqpage.php` (15 min)

Converti `geo-assets/schema/03-faqpage-example-tributario.json`. Output FAQPage schema basato su ACF repeater `faq` del CPT `competenza`:

```php
<?php
if (!is_singular('competenza')) return;
if (!function_exists('get_field')) return;

$faqs = get_field('faq');
if (empty($faqs) || count($faqs) < 1) return; // Skip se zero FAQ

$faqpage = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(function($faq) {
        return [
            '@type' => 'Question',
            'name' => $faq['domanda'] ?? '',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['risposta'] ?? '',
            ],
        ];
    }, $faqs),
];
?>
<script type="application/ld+json">
<?php echo wp_json_encode($faqpage, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>
```

---

## Task 4 — `partial-breadcrumb.php` (15 min)

Genera dinamicamente `BreadcrumbList` per ogni pagina non-home, usando `get_post_ancestors()` per pagine gerarchiche e tassonomia per CPT.

Helper-driven (puoi mettere la logica in `helpers.php` di Theme Architect — chiedi a Duccio se preferisci farla qui).

```php
<?php
if (is_front_page()) return;

$crumbs = saltelli_breadcrumb_chain(); // helper che ritorna [ ['name', 'url'], ... ]
if (empty($crumbs)) return;

$breadcrumb = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => array_values(array_map(function($idx, $crumb) {
        return [
            '@type' => 'ListItem',
            'position' => $idx + 1,
            'name' => $crumb['name'],
            'item' => $crumb['url'],
        ];
    }, array_keys($crumbs), $crumbs)),
];
?>
<script type="application/ld+json">
<?php echo wp_json_encode($breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>
```

Se l'helper `saltelli_breadcrumb_chain()` non esiste in `helpers.php`, scrivilo tu — è una funzione utility, sta nel tuo scope.

---

## Task 5 — `partial-article.php` (15 min)

Converti `geo-assets/schema/05-article-template.json`. Per `is_singular('post')`:

```php
$article = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    '@id' => get_permalink() . '#article',
    'headline' => get_the_title(),
    'description' => get_the_excerpt(),
    'image' => has_post_thumbnail() ? [get_the_post_thumbnail_url(null, 'saltelli-card')] : [],
    'datePublished' => get_the_date('c'),
    'dateModified' => get_the_modified_date('c'),
    'author' => [
        '@type' => 'Person',
        'name' => get_the_author(),
    ],
    'publisher' => ['@id' => home_url('/') . '#organization'],
    'inLanguage' => 'it-IT',
    // TODO Duccio: associare autore a CPT avvocato corrispondente quando esiste
];
```

---

## Task 6 — `schema-loader.php` (10 min)

Router che hookha su `wp_head` priority 5 e include il giusto partial:

```php
add_action('wp_head', 'saltelli_inject_schema', 5);

function saltelli_inject_schema() {
    // 1. Sempre Organization globale
    include SALTELLI_THEME_DIR . '/inc/schema/partial-organization.php';

    // 2. Always BreadcrumbList tranne home
    if (!is_front_page()) {
        include SALTELLI_THEME_DIR . '/inc/schema/partial-breadcrumb.php';
    }

    // 3. Page-type specific
    if (is_singular('avvocato')) {
        include SALTELLI_THEME_DIR . '/inc/schema/partial-attorney.php';
    } elseif (is_singular('competenza')) {
        include SALTELLI_THEME_DIR . '/inc/schema/partial-faqpage.php';
    } elseif (is_singular('post')) {
        include SALTELLI_THEME_DIR . '/inc/schema/partial-article.php';
    }
}
```

---

## Task 7 — `meta-tags.php` (15 min)

Hookha su `wp_head` priority 7 (dopo schema, prima dei plugin):

```php
add_action('wp_head', 'saltelli_meta_tags', 7);

function saltelli_meta_tags() {
    // Skip se Yoast/Rank Math sono attivi e gestiscono già OG/Twitter
    $has_seo_plugin = defined('WPSEO_VERSION') || function_exists('YoastSEO')
                   || class_exists('RankMath') || defined('RANK_MATH_VERSION');

    if ($has_seo_plugin) return; // Lascia gestire al plugin

    // Altrimenti emetti i tuoi meta:
    // - <meta name="description" content="...">
    // - <meta property="og:title" content="...">
    // - <meta property="og:description" ...>
    // - <meta property="og:url" ...>
    // - <meta property="og:image" ...>
    // - <meta property="og:type" ...>
    // - <meta property="og:locale" content="it_IT">
    // - <meta name="twitter:card" content="summary_large_image">
    // - <meta name="twitter:title" ...>
    // - <meta name="twitter:description" ...>
    // - <meta name="twitter:image" ...>
}
```

Source dei valori: ACF excerpt → fallback `get_the_excerpt()` → fallback `get_bloginfo('description')`.

---

## Task 8 — `ai-files.php` — `/llms.txt` + robots filter (15 min)

### `/llms.txt` endpoint

```php
add_action('init', 'saltelli_llms_rewrite');
function saltelli_llms_rewrite() {
    add_rewrite_rule('^llms\.txt$', 'index.php?saltelli_llms=1', 'top');
}

add_filter('query_vars', function($qv) { $qv[] = 'saltelli_llms'; return $qv; });

add_action('template_redirect', function() {
    if (get_query_var('saltelli_llms')) {
        $file = SALTELLI_THEME_DIR . '/../../../geo-assets/llms.txt';
        // Se il path geo-assets è fuori dal tema, calcolare correttamente
        // Path corretto: ABSPATH . '../geo-assets/llms.txt' (NON dentro wp-content)
        // VERIFICARE: il path corretto del file llms.txt nel container Docker
        $real_path = '/var/www/html/../geo-assets/llms.txt';
        if (!file_exists($real_path)) {
            // Fallback: leggi dalla root del progetto
            $real_path = ABSPATH . '../geo-assets/llms.txt';
        }
        if (file_exists($real_path)) {
            header('Content-Type: text/plain; charset=utf-8');
            header('Cache-Control: public, max-age=86400');
            readfile($real_path);
            exit;
        }
        // Fallback inline: emit a minimal llms.txt content
        header('Content-Type: text/plain; charset=utf-8');
        echo "# Studio Legale Saltelli & Partners\n# (llms.txt master in geo-assets/, riconfigurare path)\n";
        exit;
    }
});
```

**Importante**: il file `geo-assets/llms.txt` è nella root del progetto, NON dentro `wp-content`. Assicurati che il path PHP risolva correttamente al file dentro il container Docker. Test:

```bash
docker exec saltelli-wp ls -la /var/www/html/../geo-assets/ 2>&1
# Se il bind mount non rende geo-assets visibile, alternativa:
# Copia il file dentro il tema: cp geo-assets/llms.txt wp-content/themes/saltelli/llms.txt
# E aggiorna il path nel template_redirect
```

Se il bind mount non espone `geo-assets/` al container, **alternativa pulita**: leggi il file dal tema stesso copiandolo in `wp-content/themes/saltelli/inc/seo/llms-content.txt` (lo committi nel repo del tema).

### `robots_txt` filter

```php
add_filter('robots_txt', 'saltelli_robots_txt', 10, 2);
function saltelli_robots_txt($output, $public) {
    if (!$public) return $output; // Mantieni stato discourage in dev

    $custom = SALTELLI_THEME_DIR . '/inc/seo/robots-additions.txt';
    // O meglio: legge geo-assets/robots.txt se accessibile
    $custom_content = '';
    if (file_exists($custom)) {
        $custom_content = file_get_contents($custom);
    }
    return $output . "\n\n" . $custom_content;
}
```

Strategia identica a llms.txt: se `geo-assets/robots.txt` non è accessibile dal container, **copia il file in `inc/seo/robots-additions.txt`** del tema.

---

## Task 9 — Validazione (15 min)

Dopo tutti i Task, esegui:

```bash
# 1. Endpoint /llms.txt risponde con il contenuto
curl -s http://localhost:8080/llms.txt | head -10
echo "---"
curl -sI http://localhost:8080/llms.txt | head -3

# 2. Robots.txt include AI crawlers
curl -s http://localhost:8080/robots.txt | grep -E "GPTBot|ClaudeBot|PerplexityBot"

# 3. Schema globale presente in homepage
curl -s http://localhost:8080/ | grep -c 'application/ld+json'  # atteso: 2 (Organization + WebSite)

# 4. Schema specifico per ogni page type
# Per testare CPT, ne servono di popolati — se ancora vuoti, salta e nota nel report
curl -s http://localhost:8080/competenze/ 2>/dev/null | grep -c 'application/ld+json'

# 5. PHP error log pulito
docker exec saltelli-wp tail -30 /var/www/html/wp-content/debug.log 2>/dev/null

# 6. Validazione esterna (CRITICO — test manuale finale)
echo ""
echo "VALIDAZIONE MANUALE:"
echo "1. Vai a https://validator.schema.org/"
echo "2. Inserisci: http://localhost:8080/ (se accessibile pubblicamente)"
echo "   Alternativa: copia il contenuto della source homepage e validalo come HTML"
echo "3. Tutti gli schema devono dare ZERO errori"
```

Se la validazione esterna sfora il tempo del SHIP MODE (siamo a 24h), **rimanda al deploy staging** — su staging.studiolegalesaltelli.it il validator è raggiungibile. Documentalo nel report.

---

## Coordinamento con gli altri agent

**File scope tuoi (esclusivi):**
- `inc/schema/schema-loader.php`
- `inc/schema/partial-organization.php`
- `inc/schema/partial-attorney.php`
- `inc/schema/partial-faqpage.php`
- `inc/schema/partial-breadcrumb.php`
- `inc/schema/partial-article.php`
- `inc/seo/meta-tags.php`
- `inc/seo/ai-files.php`
- Eventuale `inc/seo/llms-content.txt` e `inc/seo/robots-additions.txt` (se serve copiare i file dentro il tema)

**File condivisi che potresti dover toccare (con coordinamento):**
- `inc/helpers.php` — solo per aggiungere `saltelli_breadcrumb_chain()` se non esiste; **NON modificare altre funzioni**

**File NON tuoi:**
- Template PHP root → Theme Architect
- ACF JSON → Theme Architect (ma puoi LEGGERE i field name per usarli nei tuoi schema)
- CSS/JS → Style Agent

---

## Report finale a Duccio

1. ✅/❌ stato dei test del Task 9
2. Lista file creati/modificati
3. Stato coabitazione Yoast (è attivo? Quali schema hai dovuto skippare?)
4. Path risolto per `geo-assets/llms.txt` e `geo-assets/robots.txt` (dentro container)
5. Eventuali TODO lasciati (sameAs social, alumniOf, lat/lng esatti)
6. Risultato validazione schema (manuale post-deploy se non ora)

Poi **fermati**. Aspetta istruzioni.

---

*v1.0 — 2026-04-29 SHIP MODE 24H — Direttore d'orchestra: Claude (chat).*
