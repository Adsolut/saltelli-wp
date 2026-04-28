# Prompt Scaffolding — Tema WordPress Saltelli

> Per Claude Code. Apri questa cartella (`saltelli-wp/`), leggi questo file, seguilo alla lettera. Non improvvisare.

---

## Tu sei

Un **scaffolding agent**. Il tuo unico job è costruire l'**ossatura tecnica** del tema custom WordPress di Studio Legale Saltelli. Non il design, non le animazioni, non i contenuti reali. Solo struttura.

Lavori da solo. Quando hai finito, riporta a Duccio cosa hai fatto, e fermati. Non avviare la fase multi-agent — quella ha un altro prompt (`.claude/PROMPT_LEAD_AGENT.md`) e parte dopo, quando il design sarà firmato.

---

## Letture obbligatorie (in quest'ordine, prima di scrivere codice)

1. `CLAUDE.md` — hard constraints e regole non negoziabili
2. `BRIEF_Saltelli_WordPress.md` — architettura informativa, stack, requisiti GEO
3. `.claude/knowledge/project-context.json` — fonte di verità sui dati cliente, decisione tier-1, current_phase
4. `geo-assets/schema/README.md` — strategia di integrazione schema markup PHP partial-driven
5. Tutti i file `geo-assets/schema/*.json` — sono i template di partenza per i PHP partial schema
6. `geo-assets/llms.txt` — il file da servire da `/llms.txt`
7. `geo-assets/robots.txt` — il file robots ottimizzato AI

Quando hai letto, conferma a te stesso: **tier-1 = Tributario, Lavoro, Famiglia LGBTQ+** (è in `project-context.json` → `strategic_focus_decision`). Questo determina un ACF flag che devi scaffoldare.

---

## Hard rules (riassunte da CLAUDE.md, le ribadisco perché sono critiche per te)

1. **Nessun page builder**. Pure PHP template hierarchy. Niente Elementor, Bricks, WPBakery, Divi.
2. **Custom Post Types per `avvocato` e `competenza`** registrati in PHP (non plugin CPT-UI).
3. **Schema JSON-LD inline nei template**. Non plugin Schema Pro. PHP partials in `inc/schema/`.
4. **CSS variables per i design tokens**. Niente Tailwind, niente Bootstrap. CSS custom in `assets/css/`.
5. **GSAP 3.15 + Lenis** come unico stack animazioni — ma **non li configuri tu**. Lascia hook di enqueue pronti, lo Style & Animation agent li popolerà dopo il design firmato.
6. **Mai pubblicare credenziali**. Non leggere `config.local.json` se non strettamente necessario. Tutto ciò che dovesse uscire da te deve essere scrubbed.
7. **Niente contenuto reale**. Bio avvocati, descrizioni aree, FAQ — sono lavoro di Elena. Lascia placeholder espliciti `<!-- TODO: contenuto da Elena -->`.
8. **Niente design reale**. Lascia file CSS minimali con commenti `/* TODO: applicare design tokens da CLAUDE_DESIGN_PROMPT.md una volta firmato */`.
9. **Niente foto reali**. Le foto degli avvocati arriveranno da Ludovica. Usa `<!-- TODO: foto Saltelli -->` nel markup template.

---

## Pre-flight checks (esegui prima di scrivere nulla)

```bash
# 1. Sei nella cartella giusta?
pwd  # deve terminare con /saltelli-wp

# 2. Docker è su?
docker compose ps | grep -E "saltelli-(wp|db)"  # deve mostrare entrambi running/healthy

# 3. Il tema saltelli NON deve già esistere
ls wp-content/themes/saltelli/ 2>/dev/null && echo "STOP: cartella tema già esiste, chiedi a Duccio prima di overwriteare" && exit 1
ls wp-content/themes/saltelli/ 2>/dev/null || echo "OK, nuova cartella tema"

# 4. ACF (free o pro) installato? — non bloccante, ma utile sapere
docker compose run --rm wpcli plugin list --name=advanced-custom-fields --format=json 2>/dev/null
docker compose run --rm wpcli plugin list --name=advanced-custom-fields-pro --format=json 2>/dev/null
```

Se uno qualsiasi dei check 1-3 fallisce, fermati e chiedi a Duccio. Il check 4 è informativo: se ACF non c'è, prosegui comunque — i field group JSON saranno comunque pronti per quando ACF verrà installato.

---

## Deliverable: struttura completa del tema

Tutto va in `wp-content/themes/saltelli/`. Crea esattamente questa struttura:

```
wp-content/themes/saltelli/
├── style.css                            # header WP minimale
├── functions.php                        # solo include dei moduli
├── README.md                            # guida del tema (la scrivi tu)
│
├── inc/
│   ├── setup.php                        # theme support, image sizes, menu, widgets
│   ├── enqueue.php                      # enqueue CSS/JS, GSAP CDN, Lenis CDN, fonts preload (placeholder)
│   ├── cpt-avvocato.php                 # registrazione CPT avvocato
│   ├── cpt-competenza.php               # registrazione CPT competenza + tassonomia tipo-area
│   ├── acf-fields.php                   # bootstrap ACF: registra path acf-json/, filter pre/post save
│   ├── acf-json/                        # field group JSON (ACF Local JSON)
│   │   ├── group_avvocato.json          # campi avvocato: foto, ruolo, specializzazioni, bio, sameAs
│   │   ├── group_competenza.json        # campi competenza: answer_capsule, body, is_tier_1_focus, lead_attorneys, faq (repeater)
│   │   └── group_settings.json          # opzioni globali tema (per ora vuoto, scheletro)
│   │
│   ├── schema/
│   │   ├── schema-loader.php            # router: include il partial corretto in base al template corrente
│   │   ├── partial-organization.php     # Organization+LegalService, su OGNI pagina (header globale)
│   │   ├── partial-attorney.php         # Person/Attorney, dinamico dal CPT avvocato corrente
│   │   ├── partial-faqpage.php          # FAQPage, legge ACF FAQ del CPT competenza
│   │   ├── partial-breadcrumb.php       # BreadcrumbList, dinamico (gerarchia pagine + tassonomia)
│   │   └── partial-article.php          # Article, per single post blog
│   │
│   ├── seo/
│   │   ├── meta-tags.php                # filtra/aggiunge meta description, OG, Twitter Cards
│   │   └── ai-files.php                 # rewrite rules per servire /llms.txt + filter robots_txt
│   │
│   └── helpers.php                      # piccole utility (esempi: get_attorney_for_competenza, get_breadcrumb_chain)
│
├── assets/
│   ├── css/
│   │   ├── tokens.css                   # CSS variables (placeholder + commento TODO design)
│   │   ├── base.css                     # reset + tipografia minimale + layout container
│   │   └── components/                  # vuota, .gitkeep — riempita da Style & Animation agent
│   │       └── .gitkeep
│   │
│   ├── js/
│   │   ├── main.js                      # entrypoint, importa moduli
│   │   ├── lenis-init.js                # placeholder con commento TODO
│   │   └── gsap-init.js                 # placeholder con commento TODO
│   │
│   ├── fonts/                           # vuota, .gitkeep — Style & Animation agent metterà WOFF2
│   │   └── .gitkeep
│   │
│   └── images/                          # vuota, .gitkeep — placeholder per asset tema (logo SVG, icon set)
│       └── .gitkeep
│
└── (template files nel root del tema, come da WP standard)
    ├── header.php                       # struttura: <head>, header sito, schema-loader
    ├── footer.php                       # struttura: footer, scripts
    ├── front-page.php                   # homepage, sezioni placeholder commentate
    ├── index.php                        # fallback (loop standard)
    ├── page.php                         # template pagina standard
    ├── single.php                       # singolo post blog
    ├── single-avvocato.php              # template CPT avvocato
    ├── single-competenza.php            # template CPT competenza, branch su is_tier_1_focus
    ├── archive.php                      # archive generico
    ├── archive-avvocato.php             # archive CPT avvocato
    ├── archive-competenza.php           # archive CPT competenza, lista 19 aree con flag tier-1 evidenziato
    ├── 404.php
    ├── searchform.php
    └── search.php
```

---

## Specifiche per file (le cose che devono ESSERCI dentro)

### `style.css`

Header WP standard, minimale. NIENTE styling. Solo:

```css
/*
Theme Name: Saltelli
Theme URI: https://studiolegalesaltelli.it
Author: Adsolut SRLS
Author URI: https://adsolut.it
Description: Custom theme — AI-Ready, GEO-optimized — built for Studio Legale Emiliano Saltelli & Partners. No page builder. Pure PHP, GSAP+Lenis ready, schema JSON-LD inline.
Version: 0.1.0-scaffold
Requires at least: 6.5
Requires PHP: 8.2
License: Proprietary — © 2026 Studio Legale Saltelli & Partners
Text Domain: saltelli
*/

/* Tutto lo styling vive in assets/css/. Questo file esiste solo per WP. */
```

### `functions.php`

Solo include dei moduli. Niente logica diretta. Esempio:

```php
<?php
/**
 * Saltelli Theme — bootstrap.
 * Tutta la logica vive in inc/. Questo file solo orchestra.
 */
defined('ABSPATH') || exit;

define('SALTELLI_THEME_VERSION', '0.1.0-scaffold');
define('SALTELLI_THEME_DIR', get_template_directory());
define('SALTELLI_THEME_URI', get_template_directory_uri());

require_once SALTELLI_THEME_DIR . '/inc/setup.php';
require_once SALTELLI_THEME_DIR . '/inc/enqueue.php';
require_once SALTELLI_THEME_DIR . '/inc/helpers.php';
require_once SALTELLI_THEME_DIR . '/inc/cpt-avvocato.php';
require_once SALTELLI_THEME_DIR . '/inc/cpt-competenza.php';
require_once SALTELLI_THEME_DIR . '/inc/acf-fields.php';
require_once SALTELLI_THEME_DIR . '/inc/schema/schema-loader.php';
require_once SALTELLI_THEME_DIR . '/inc/seo/meta-tags.php';
require_once SALTELLI_THEME_DIR . '/inc/seo/ai-files.php';
```

### `inc/setup.php`

`after_setup_theme` hook con:
- `add_theme_support('title-tag')`, `'post-thumbnails'`, `'html5' (search-form, comment-form, gallery, caption, style, script)`, `'custom-logo'`, `'menus'`, `'editor-styles'`, `'responsive-embeds'`
- Image sizes:
  - `saltelli-attorney-portrait` 600×800 (3:4) — ritratti avvocato
  - `saltelli-attorney-square` 600×600 — versione 1:1
  - `saltelli-hero` 1920×1080 — hero homepage
  - `saltelli-card` 800×500 — card blog/competenza
- `register_nav_menus`: `primary`, `footer-studio`, `footer-aree`, `footer-legal`
- Text domain `saltelli`

### `inc/enqueue.php`

`wp_enqueue_scripts` hook. Per ora **placeholder**, NON caricare GSAP/Lenis/font reali — lascia i wp_enqueue commentati con TODO. Cosa enqueue:
- `saltelli-tokens` → `assets/css/tokens.css`
- `saltelli-base` → `assets/css/base.css`, depend on `tokens`
- `saltelli-main` → `assets/js/main.js`, in footer, defer
- Commenti `// TODO Style & Animation agent: enqueue Playfair + DM Sans WOFF2 con preload`
- Commenti `// TODO Style & Animation agent: enqueue GSAP 3.15 + ScrollTrigger + SplitText da CDN con SRI`
- Commenti `// TODO Style & Animation agent: enqueue Lenis da CDN`

### `inc/cpt-avvocato.php`

Registra CPT `avvocato`:
- public, has_archive `avvocati`, rewrite slug `avvocati`
- supports: title, editor, thumbnail, excerpt, custom-fields
- menu_position 5, menu_icon `dashicons-businessperson`
- show_in_rest true (per Block Editor + REST)
- labels in italiano

### `inc/cpt-competenza.php`

Registra CPT `competenza`:
- public, has_archive `competenze`, rewrite slug `competenze`
- supports: title, editor, thumbnail, excerpt, custom-fields
- menu_position 6, menu_icon `dashicons-portfolio`
- show_in_rest true
- labels in italiano

E registra **tassonomia `tipo-area`** associata a `competenza`:
- gerarchica (come categories)
- rewrite slug `tipo`
- termini consigliati (NON crearli, solo documentarli in commento): civile, penale, tributario, lavoro, famiglia, amministrativo, commerciale, immobiliare

### `inc/acf-fields.php`

- Filter `acf/settings/save_json` → ritorna `SALTELLI_THEME_DIR . '/inc/acf-json'`
- Filter `acf/settings/load_json` → aggiunge stesso path
- Documenta in commento: "I field group sono in `inc/acf-json/`. ACF (Free o Pro) li picka automaticamente. Per repeater FAQ serve ACF Pro."

### `inc/acf-json/group_avvocato.json`

Field group per CPT `avvocato`. Campi (lista; tu definisci la struttura JSON ACF corretta):
- `foto_ritratto` (image) — l'immagine ritratto principale
- `ruolo_breve` (text) — es. "Fondatore", "Giuslavorista"
- `specializzazioni` (textarea o repeater di text) — es. "Diritto tributario", "Cartelle esattoriali"
- `bio_breve` (textarea, max 300 char) — usata in archive e schema description
- `bio_estesa` (wysiwyg) — usata in single-avvocato.php
- `formazione` (repeater) — sub: anno (text), titolo (text), istituzione (text)
- `email_pubblica` (email)
- `telefono_pubblico` (text)
- `whatsapp` (text)
- `same_as_linkedin` (url)
- `aree_competenza_correlate` (post_object multiple, post_type=competenza)

### `inc/acf-json/group_competenza.json`

Field group per CPT `competenza`. Campi:
- `answer_capsule` (textarea, 40-60 parole) — paragrafo answer-engine ottimizzato
- `is_tier_1_focus` (true_false, default false) — flag strategia
- `lead_attorneys` (post_object multiple, post_type=avvocato) — chi presidia l'area
- `body_extended` (wysiwyg) — solo per tier-1, contenuto profondo
- `faq` (repeater, label "Domande frequenti", 3-5 max) — sub:
  - `domanda` (text)
  - `risposta` (textarea, 40-60 parole)
- `casi_rappresentativi` (repeater, solo tier-1) — sub: titolo, descrizione_anonimizzata, esito
- `cta_label` (text, default "Parlane con i nostri avvocati")
- `cta_url` (url, default "/contatti/")
- `articoli_correlati` (post_object multiple, post_type=post)

### `inc/acf-json/group_settings.json`

Per ora **scheletro vuoto** (ACF Options Page se ACF Pro disponibile, altrimenti commento TODO). Piazza un campo singolo `placeholder_settings` text, da rimuovere quando ne aggiungeremo veri.

### `inc/schema/schema-loader.php`

Hook `wp_head` priority alta (dopo i meta).
Logica:
- SEMPRE include `partial-organization.php` (su tutte le pagine)
- Se `is_singular('avvocato')` → include `partial-attorney.php`
- Se `is_singular('competenza')` → include `partial-faqpage.php`
- Se `is_singular('post')` → include `partial-article.php`
- Se NOT `is_front_page()` → include `partial-breadcrumb.php`

### `inc/schema/partial-organization.php`

**Replica fedelmente** la struttura di `geo-assets/schema/01-organization-legalservice.json`. Converti in PHP array. Sostituisci i valori statici con WP functions dove possibile (es. `home_url()`, `get_bloginfo()`). I campi che hanno `_TODO_*` come placeholder nel JSON, lasciali come `// TODO: completare con dato reale`.

Output: `<script type="application/ld+json">` con `wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)`.

### `inc/schema/partial-attorney.php`

Replica `geo-assets/schema/02-attorneys.json` (struttura per UN avvocato). Per il post avvocato corrente, usa:
- `get_post_field('post_name')` per slug
- `get_field('foto_ritratto')` per image
- `get_field('bio_breve')` per description
- `get_field('email_pubblica')`, `telefono_pubblico` per contatti
- `get_field('specializzazioni')` per knowsAbout
- `get_field('same_as_linkedin')` per sameAs
- Hardcoded `worksFor` con `@id` dell'Organization

### `inc/schema/partial-faqpage.php`

Replica `geo-assets/schema/03-faqpage-example-tributario.json`. Loop su `get_field('faq')` (repeater). Genera `mainEntity[]` con Question/Answer. Se zero FAQ, NON output (skip).

### `inc/schema/partial-breadcrumb.php`

Replica `geo-assets/schema/04-breadcrumblist-template.json`. Genera dinamicamente l'array `itemListElement[]` basandosi su:
- Per page gerarchiche: `get_post_ancestors()`
- Per CPT: archive → singolo
- Per blog post: blog → categoria principale → titolo

Helper utile: `helpers.php::saltelli_get_breadcrumb_chain($post_id)`.

### `inc/schema/partial-article.php`

Replica `geo-assets/schema/05-article-template.json`. Per single post:
- `get_the_title()`, `get_the_excerpt()` (con strip_tags)
- Featured image come `image[]`
- `get_the_date('c')` per ISO 8601
- `get_the_author_meta()` per author (con fallback a Organization se autore generico)
- Categoria primaria per `articleSection`

### `inc/seo/meta-tags.php`

`wp_head` hook:
- Meta description (da Yoast se attivo, altrimenti dal `excerpt` o ACF)
- Open Graph: og:title, og:description, og:url, og:image, og:type, og:locale
- Twitter Cards: twitter:card=summary_large_image, twitter:title, twitter:description, twitter:image
- Skip se Yoast/Rank Math sono attivi e già forniscono questi tag (controlla con `function_exists('YoastSEO')`).

### `inc/seo/ai-files.php`

Due hook:
1. **`/llms.txt` endpoint**: `init` hook con `add_rewrite_rule`, `template_redirect` hook che intercetta la richiesta e serve il file `geo-assets/llms.txt` con `Content-Type: text/plain; charset=utf-8`. Documenta in commento: "Questo serve `/llms.txt` dinamicamente. In produzione, alternativa: copiare il file in webroot."
2. **`robots_txt` filter**: prendi il robots di default e mergia con le righe AI-friendly da `geo-assets/robots.txt`. Se mismatch, sovrascrivi con il nostro file.

### Template files (root del tema)

Tutti i template sono **scheletri minimi** con commenti TODO. Esempio per `single-competenza.php`:

```php
<?php
/**
 * Template: Single CPT Competenza
 * Branch su is_tier_1_focus per profondità contenuto.
 *
 * @package Saltelli
 */
get_header();

while (have_posts()) :
    the_post();
    $is_tier_1 = function_exists('get_field') ? (bool) get_field('is_tier_1_focus') : false;
    ?>
    <article class="competenza competenza--<?php echo $is_tier_1 ? 'tier-1' : 'tier-2'; ?>">

        <!-- TODO Style & Animation agent: hero section -->
        <header class="competenza__hero">
            <h1><?php the_title(); ?></h1>
            <?php if (function_exists('get_field')) : ?>
                <p class="answer-capsule"><?php echo esc_html(get_field('answer_capsule')); ?></p>
            <?php endif; ?>
        </header>

        <!-- TODO Style & Animation agent: body extended (solo tier-1) -->
        <?php if ($is_tier_1 && function_exists('get_field')) : ?>
            <section class="competenza__body">
                <?php echo wp_kses_post(get_field('body_extended')); ?>
            </section>
        <?php endif; ?>

        <!-- TODO Style & Animation agent: lead attorneys section -->
        <!-- TODO Style & Animation agent: FAQ accordion -->
        <!-- TODO Style & Animation agent: casi rappresentativi (solo tier-1) -->
        <!-- TODO Style & Animation agent: CTA + articoli correlati -->

    </article>
    <?php
endwhile;

get_footer();
```

Stessa filosofia per gli altri template: **markup semantico minimo + TODO commentati per le sezioni**. Mai stylare, mai animare.

### `README.md` del tema

Crea un README in `wp-content/themes/saltelli/README.md` che spieghi:
- Stack: WP 6.x, PHP 8.2+, ACF (Pro consigliato per repeater FAQ)
- Struttura cartelle (riferimento a questa lista)
- Come attivare il tema in dev: `docker compose run --rm wpcli theme activate saltelli`
- Come importare i field group ACF (vengono picked up automaticamente dal path `inc/acf-json/`)
- Stato corrente: "0.1.0-scaffold — solo struttura. Design + animazioni + contenuti = lavoro successivo degli agent multi-team."

---

## Ordine di lavoro consigliato

1. Pre-flight checks
2. Crea la struttura folders + file vuoti con header/commento
3. `style.css`, `functions.php`, `inc/setup.php`, `inc/enqueue.php` (bootstrap minimale)
4. CPT (`cpt-avvocato.php`, `cpt-competenza.php`)
5. ACF JSON field groups
6. Schema partials (uno alla volta, partial-organization per primo)
7. SEO meta + AI files
8. Template files (scheletri)
9. README del tema
10. Test (vedi sotto)
11. Report finale

---

## Test (Definition of Done)

Esegui questi comandi e mostra output a fine lavoro:

```bash
# 1. Attivazione tema senza warning
docker compose run --rm wpcli theme activate saltelli
# atteso: "Success: Switched to 'Saltelli' theme."

# 2. CPT registrati
docker compose run --rm wpcli post-type list --fields=name,label
# atteso: 'avvocato' e 'competenza' nella lista

# 3. Tassonomia registrata
docker compose run --rm wpcli taxonomy list --fields=name,object_type
# atteso: 'tipo-area' associata a 'competenza'

# 4. Permalink rewrite
docker compose run --rm wpcli rewrite flush --hard
# atteso: "Success: Rewrite rules flushed."

# 5. URL archive raggiungibili (anche se vuoti)
curl -sI http://localhost:8080/avvocati/ | head -3
curl -sI http://localhost:8080/competenze/ | head -3
# atteso: 200 (o 404 se WP serve "no posts found", entrambi accettabili — l'importante è no 500)

# 6. /llms.txt servito correttamente
curl -s http://localhost:8080/llms.txt | head -5
# atteso: prime righe del nostro llms.txt

# 7. robots.txt include AI crawlers
curl -s http://localhost:8080/robots.txt | grep -E "GPTBot|ClaudeBot|PerplexityBot"
# atteso: 3 righe Allow

# 8. Schema globale presente nel <head>
curl -s http://localhost:8080/ | grep -c "application/ld+json"
# atteso: >= 1

# 9. PHP error log pulito
docker compose run --rm wpcli config get WP_DEBUG_LOG --type=constant
docker exec saltelli-wp tail -20 /var/www/html/wp-content/debug.log 2>/dev/null
# atteso: nessun fatal error, nessun warning critico relativo al tema
```

Se uno qualsiasi di questi fallisce, indaga, fixa, riprova.

---

## Report finale (da inviare a Duccio in chat)

Quando hai finito, scrivi un report markdown breve con:

1. ✅/❌ stato dei 9 test sopra
2. Lista dei file creati (path)
3. Eventuali decisioni che hai dovuto prendere autonomamente (es. una sintassi ACF JSON ambigua) — flagga
4. Note di attenzione per gli agent successivi (es. "Il design TODO commentato è denso, leggetelo prima di stylare")
5. Versione del tema in `style.css`: `0.1.0-scaffold`

Poi **fermati**. Non avviare il design, non avviare gli agent successivi. Aspetta istruzioni.

---

## Cosa fare se sei bloccato

- **Ambiguità sui dati cliente**: NON inventare. Lascia placeholder TODO esplicito, segnala nel report.
- **ACF non installato**: prosegui — i JSON saranno pronti per quando ACF arriverà.
- **Conflitto con un plugin esistente nel container**: NON disattivare plugin senza chiedere. Documenta nel report.
- **Errore PHP che non riesci a fixare**: stop, raccogli stack trace, riporta a Duccio.

In ogni caso di dubbio reale: fermati, segnala, aspetta. Non procedere a tentoni — è meglio una pausa che un commit di codice incerto.

---

*Ultimo aggiornamento: 2026-04-28 — Claude × Duccio.*
