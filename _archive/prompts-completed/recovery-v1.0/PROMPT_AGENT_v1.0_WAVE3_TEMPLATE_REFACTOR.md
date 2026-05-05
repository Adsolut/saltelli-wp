# PROMPT v1.0.0 WAVE 3 — Template Refactor (sequenziale single agent)

> **Per Claude Code in nuova sessione (single agent).** Tempo: ~2-2.5h.
> **PRECEDENZA**: Wave 0+1+2 completati. 16 ACF Field Groups con 273 fields populated + 63 CPT items.
> **MISSIONE**: refactor template PHP da hardcoded → `get_field()` per leggere i valori ACF popolati. Frontend deve restare INVARIATO ma ora *editabile* da WP-Admin.

---

## 🎯 Tu sei

L'**Agente Template Refactor**. Wave 2 ha popolato 273 ACF fields + 63 CPT items (Theme Options, page WP, lawyer, tier-1, modalità, scenari, principi, trust, FAQ, casi, formazione). Adesso devi:

1. **Refactor `page.php`** (1274 righe → ~350 righe + 5-6 template-parts)
2. **Refactor `single-avvocato.php`** + **`single-competenza.php`** per usare `get_field()`
3. **Footer/header** con Theme Options globali
4. **Mantenere fallback grazioso** (se ACF empty → hardcoded come prima)

```
WAVE 3 — 5 PHASES sequenziali

Phase 1 (~30 min): Template-parts struttura + page.php router
Phase 2 (~45 min): Refactor 6 template-parts (costi, casi, contatti, faq, info-shared, chi-siamo)
Phase 3 (~30 min): Refactor single-avvocato.php + single-competenza.php
Phase 4 (~20 min): Footer/header con Theme Options + global helpers
Phase 5 (~15 min): Smoke + bump + deploy + visual diff
```

---

## 📚 Letture obbligatorie

```
.claude/knowledge/recovery/PROJECT_STATE.md
.claude/knowledge/recovery/v1.0-WAVE2-CONTENT-MIGRATION.md  (riferimento fields populated)
PROMPT_AGENT_v1.0_WAVE2_CONTENT_MIGRATION.md                (schema fields)
CLAUDE.md                                                    (hard constraints)

wp-content/themes/saltelli/
  ├── page.php (1274 righe — TARGET REFACTOR)
  ├── single-avvocato.php (lawyer template)
  ├── single-competenza.php (competenza template — refactor solo tier-1 scope)
  ├── footer.php (target Theme Options binding)
  ├── header.php (target Theme Options binding)
  ├── inc/                                  (helpers, verifica saltelli_field, etc.)
  └── acf-json/*.json                       (16 Field Groups schema)
```

---

## 🔒 Hard rules

| Rule | Decisione |
|---|---|
| **Frontend INVARIATO post-refactor** (stesso content da ACF, prima da hardcoded) | Test critical |
| **Fallback grazioso obbligatorio**: `get_field('x') ?: 'default hardcoded'` | Safety |
| **NESSUNA modifica tokens.css o foundation CSS** | Locked |
| **NON sovrascrivere** `_thumbnail_id` Emiliano + bio_estesa Step D + post_content CPT | Content protetto |
| **Helper `saltelli_field()`** già esistente in `inc/` — RIUSARE | No rewrite helper |
| **Template-parts** in `wp-content/themes/saltelli/template-parts/` | WP convention |
| **Smoke test** dopo OGNI Phase (atteso INVARIATO HTTP 200) | Safety |
| **Commit incrementale** 1 per Phase | Audit trail |
| **Path droplet**: `/var/www/saltelli/` | Lesson learned |
| **Backup pre-refactor**: copia originale `page.php.pre-wave3.backup` | Rollback safety |

---

## 📋 PHASE 1 — Template-parts struttura + page.php router (~30 min)

### 1.1 — Backup pre-refactor

```bash
cd /Users/aldosantoro/Desktop/DEV/saltelli-wp

# Backup originali
cp wp-content/themes/saltelli/page.php /tmp/saltelli-page.php.pre-wave3.backup
cp wp-content/themes/saltelli/single-avvocato.php /tmp/saltelli-single-avvocato.php.pre-wave3.backup
cp wp-content/themes/saltelli/single-competenza.php /tmp/saltelli-single-competenza.php.pre-wave3.backup
echo "✓ Backup pre-Wave3 in /tmp/"
```

### 1.2 — Crea directory template-parts

```bash
mkdir -p wp-content/themes/saltelli/template-parts
```

### 1.3 — Identifica scope blocchi `is_page()` esistenti

```bash
grep -nE "is_page\(['\"]" wp-content/themes/saltelli/page.php | head -20
```

Atteso (i 9 page WP custom):
- `is_page('costi')`           → page id 2695
- `is_page('casi')`            → page id 2699
- `is_page('contatti')`        → page id 23
- `is_page('faq')`             → page id 2705
- `is_page(['guide-gratuite', 'come-lavoriamo', 'prima-consulenza', 'lavora-con-noi', 'richiedi-preventivo'])` → info-shared

### 1.4 — Crea template-parts stub

Per ognuno crea il file vuoto con header WordPress + commento TODO:

```bash
for slug in costi casi contatti faq info-shared chi-siamo; do
    cat > "wp-content/themes/saltelli/template-parts/page-${slug}.php" <<EOF
<?php
/**
 * Template part: page-${slug}.php
 * Renderizza i fields ACF della page WP "${slug}".
 *
 * @package Saltelli
 * @since 1.0.0 Wave 3
 */
defined('ABSPATH') || exit;

\$page_id = get_the_ID();

// Hero
\$hero_eyebrow = get_field('hero_eyebrow', \$page_id) ?: '';
\$hero_h1_pre  = get_field('hero_h1_pre', \$page_id) ?: get_the_title();
\$hero_h1_em   = get_field('hero_h1_em', \$page_id) ?: '';
\$hero_lede    = get_field('hero_lede', \$page_id) ?: '';

// Resto fields specifici → vedi Phase 2 per content esatto
?>
<!-- TODO Wave 3 Phase 2: replace hardcoded ${slug} block with ACF reads -->
EOF
done

ls -la wp-content/themes/saltelli/template-parts/
```

### 1.5 — Refactor page.php → router

Sostituisci il contenuto di `page.php` con un router compatto:

```php
<?php
/**
 * Template: page.php (router refactored Wave 3)
 *
 * @package Saltelli
 * @since 1.0.0 Wave 3
 */
get_header();

while (have_posts()) :
    the_post();
    
    if (is_page('costi')) {
        get_template_part('template-parts/page', 'costi');
    } elseif (is_page('casi')) {
        get_template_part('template-parts/page', 'casi');
    } elseif (is_page('contatti')) {
        get_template_part('template-parts/page', 'contatti');
    } elseif (is_page('faq')) {
        get_template_part('template-parts/page', 'faq');
    } elseif (is_page(['guide-gratuite', 'come-lavoriamo', 'prima-consulenza', 'lavora-con-noi', 'richiedi-preventivo', 'prenota-appuntamento'])) {
        get_template_part('template-parts/page', 'info-shared');
    } elseif (is_page('chi-siamo')) {
        get_template_part('template-parts/page', 'chi-siamo');
    } else {
        // Default fallback: standard WordPress page rendering
        ?>
        <article class="sl-page sl-page-shell sl-page-hero">
            <header class="sl-page__header">
                <?php saltelli_render_breadcrumb('page'); ?>
                <h1 class="sl-page__title sl-page-h1"><?php the_title(); ?></h1>
            </header>
            <div class="sl-page__prose">
                <?php the_content(); ?>
            </div>
        </article>
        <?php
    }
    
endwhile;

get_footer();
```

NB: NON ancora popolato il content dei template-parts — li scrivi in Phase 2 estraendo dal backup.

### 1.6 — Phase 1 verify

```bash
docker compose run --rm wpcli cache flush

# Frontend smoke (TUTTE le 9 page custom dovrebbero ancora funzionare)
for U in /costi/ /casi/ /contatti/ /faq/ /come-lavoriamo/ /chi-siamo/ /; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "http://localhost:8080$U?_=v1w3p1" -m 5)
    SIZE=$(curl -sL "http://localhost:8080$U?_=v1w3p1" -m 5 | wc -c | tr -d ' ')
    echo "  $U → HTTP $HTTP (size $SIZE)"
done

# ⚠ ATTESO: 200 OK ma SIZE drasticamente RIDOTTO
# (perché template-parts hanno solo TODO comment, content sparito)
# Risolvi in Phase 2 popolando i template-parts.
```

### 1.7 — Commit Phase 1

```bash
git add -A
git commit -m "feat(s2-v1.0.0-wave3): Phase 1 — page.php router + template-parts stub structure"
```

---

## 📋 PHASE 2 — Refactor 6 template-parts (~45 min)

Per ogni `template-parts/page-{slug}.php`, popola con il content estratto dal backup `page.php.pre-wave3.backup` ma sostituendo le stringhe hardcoded con `get_field()`.

### 2.1 — page-costi.php (~15 min)

Apri `/tmp/saltelli-page.php.pre-wave3.backup`, cerca blocco `is_page('costi')`. Estrai markup e sostituisci:

```php
<?php
/**
 * Template part: page-costi.php
 *
 * @package Saltelli
 * @since 1.0.0 Wave 3
 */
defined('ABSPATH') || exit;

$pid = get_the_ID();

// Hero
$hero_eyebrow = get_field('hero_eyebrow', $pid) ?: '§ Trasparenza · Costi e tariffe';
$hero_h1_pre  = get_field('hero_h1_pre', $pid)  ?: 'Costi e prima';
$hero_h1_em   = get_field('hero_h1_em', $pid)   ?: 'consulenza.';
$hero_lede    = get_field('hero_lede', $pid)    ?: '';

// Aside trust box
$aside_eyebrow    = get_field('aside_eyebrow', $pid) ?: '';
$aside_h3         = get_field('aside_h3', $pid) ?: '';
$aside_p          = get_field('aside_p', $pid) ?: '';
$aside_cta_label  = get_field('aside_cta_label', $pid) ?: 'Prenota un incontro →';
$aside_cta_url    = get_field('aside_cta_url', $pid) ?: '/contatti/';

// § 03 Body editorial
$calc_body = get_field('calc_body', $pid) ?: '';

// CTA finale
$cta_eyebrow = get_field('cta_eyebrow', $pid) ?: '§ Pronto?';
$cta_h2      = get_field('cta_h2', $pid)      ?: 'La prima consulenza è gratuita. Sempre.';
$cta_p       = get_field('cta_p', $pid)       ?: '';
$cta_label   = get_field('cta_label', $pid)   ?: 'Prenota un incontro →';
$cta_url     = get_field('cta_url', $pid)     ?: '/contatti/';
$cta_trust   = get_field('cta_trust', $pid)   ?: 'Risposta entro 24 ore · Riservatezza assoluta';

// CPT items: Modalità, Scenari, Trust signals (querati dalle CPT)
$modalita_items = get_posts([
    'post_type' => 'saltelli_modalita',
    'posts_per_page' => 3,
    'orderby' => 'menu_order',
    'order' => 'ASC',
]);

$scenari_items = get_posts([
    'post_type' => 'saltelli_scenario',
    'posts_per_page' => 3,
    'orderby' => 'menu_order',
    'order' => 'ASC',
]);

$trust_items = get_posts([
    'post_type' => 'saltelli_trust',
    'posts_per_page' => 4,
    'orderby' => 'menu_order',
    'order' => 'ASC',
]);

// FAQ specifiche per /costi/ (riusa CPT saltelli_faq con taxonomy faq_topic = 'costi')
$faq_costi = get_posts([
    'post_type' => 'saltelli_faq',
    'posts_per_page' => 5,
    'tax_query' => [
        [
            'taxonomy' => 'faq_topic',
            'field' => 'slug',
            'terms' => ['costi', 'prezzi'],
        ],
    ],
]);
?>

<article class="sl-costi sl-page-shell">

    <header class="sl-costi__hero sl-page-hero">
        <?php saltelli_render_breadcrumb('page'); ?>
        <div class="sl-costi__hero-grid">
            <div class="sl-costi__hero-text">
                <div class="sl-mono sl-page-eyebrow"><?php echo esc_html($hero_eyebrow); ?></div>
                <h1 class="sl-costi__hero-h1 sl-page-h1">
                    <?php echo esc_html($hero_h1_pre); ?>
                    <em><?php echo esc_html($hero_h1_em); ?></em>
                </h1>
                <p class="sl-costi__hero-lede sl-page-lede"><?php echo esc_html($hero_lede); ?></p>
            </div>
            
            <?php if ($aside_h3) : ?>
            <aside class="sl-costi__hero-aside">
                <?php if ($aside_eyebrow) : ?>
                    <div class="sl-mono sl-costi__hero-aside-eyebrow"><?php echo esc_html($aside_eyebrow); ?></div>
                <?php endif; ?>
                <h3><?php echo esc_html($aside_h3); ?></h3>
                <?php if ($aside_p) : ?>
                    <p><?php echo esc_html($aside_p); ?></p>
                <?php endif; ?>
                <a href="<?php echo esc_url($aside_cta_url); ?>" class="sl-link sl-link--accent">
                    <?php echo esc_html($aside_cta_label); ?>
                </a>
            </aside>
            <?php endif; ?>
        </div>
    </header>

    <?php /* § 01 Modalità */ if (!empty($modalita_items)) : ?>
    <section class="sl-costi__modalita" data-reveal>
        <header class="sl-costi__section-head">
            <div class="sl-mono">§ 01 · Come funziona la prima consulenza</div>
            <h2 class="sl-costi__h2">Tre modalità.</h2>
        </header>
        <div class="sl-costi__modalita-grid">
            <?php foreach ($modalita_items as $m) : ?>
                <article class="sl-costi__modalita-card">
                    <div class="sl-mono"><?php echo esc_html(get_field('num_label', $m->ID)); ?></div>
                    <h3><?php echo esc_html(get_field('title', $m->ID) ?: get_the_title($m)); ?></h3>
                    <p><?php echo esc_html(get_field('body', $m->ID)); ?></p>
                    <?php if ($trust = get_field('trust_mini', $m->ID)) : ?>
                        <p class="sl-costi__modalita-trust sl-mono"><?php echo esc_html($trust); ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php /* § 02 Scenari */ if (!empty($scenari_items)) : ?>
    <section class="sl-costi__scenari" data-reveal>
        <header class="sl-costi__section-head">
            <div class="sl-mono">§ 02 · Tre scenari dopo i 30 minuti</div>
            <h2 class="sl-costi__h2">Trasparenza prima di tutto.</h2>
        </header>
        <ol class="sl-costi__scenari-list">
            <?php foreach ($scenari_items as $s) : ?>
                <li class="sl-costi__scenari-item">
                    <span class="sl-mono"><?php echo esc_html(get_field('num_label', $s->ID)); ?></span>
                    <div>
                        <h3><?php echo esc_html(get_field('title', $s->ID) ?: get_the_title($s)); ?></h3>
                        <p><?php echo esc_html(get_field('body', $s->ID)); ?></p>
                        <?php if ($trust = get_field('trust_mini', $s->ID)) : ?>
                            <p class="sl-mono sl-costi__scenari-trust"><?php echo esc_html($trust); ?></p>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    </section>
    <?php endif; ?>

    <?php /* § 03 Body editorial */ if ($calc_body) : ?>
    <section class="sl-costi__calc">
        <div class="sl-costi__calc-grid">
            <div class="sl-costi__calc-text">
                <header class="sl-costi__section-head">
                    <div class="sl-mono">§ 03 · Trasparenza preventivi</div>
                    <h2 class="sl-costi__h2">Come calcoliamo i preventivi.</h2>
                </header>
                <div class="sl-costi__calc-body sl-prose">
                    <?php echo wp_kses_post($calc_body); ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php /* § 04 FAQ */ if (!empty($faq_costi)) : ?>
    <section class="sl-costi__faq" data-reveal>
        <header class="sl-costi__section-head">
            <div class="sl-mono">§ 04 · Sui costi, in chiaro</div>
            <h2 class="sl-costi__h2">Domande frequenti sui costi.</h2>
        </header>
        <div class="sl-acc">
            <?php foreach ($faq_costi as $i => $faq) : 
                $aria_id = 'costi-faq-' . $i;
            ?>
                <div class="sl-acc__item">
                    <button class="sl-acc__btn" aria-expanded="false" aria-controls="<?php echo esc_attr($aria_id); ?>">
                        <span><?php echo esc_html(get_the_title($faq)); ?></span>
                        <span class="sl-acc__icon" aria-hidden="true">+</span>
                    </button>
                    <div class="sl-acc__panel" id="<?php echo esc_attr($aria_id); ?>" aria-hidden="true">
                        <div class="sl-acc__inner"><?php echo wp_kses_post(get_field('risposta', $faq->ID)); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php /* Schema FAQPage */ ?>
        <script type="application/ld+json">
        <?php echo wp_json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function($f) {
                return [
                    '@type' => 'Question',
                    'name' => get_the_title($f),
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => wp_strip_all_tags(get_field('risposta', $f->ID)),
                    ],
                ];
            }, $faq_costi),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
        </script>
    </section>
    <?php endif; ?>

    <?php /* § 05 Trust signals */ if (!empty($trust_items)) : ?>
    <section class="sl-costi__trust-grid" data-reveal>
        <header class="sl-costi__section-head">
            <div class="sl-mono">§ 05 · Garanzie professionali</div>
            <h2 class="sl-costi__h2">I nostri valori in chiaro.</h2>
        </header>
        <div class="sl-costi__trust-plates">
            <?php foreach ($trust_items as $t) : ?>
                <div class="sl-costi__trust-plate">
                    <div class="sl-mono"><?php echo esc_html(get_field('label', $t->ID) ?: get_the_title($t)); ?></div>
                    <p><?php echo esc_html(get_field('valore', $t->ID)); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php /* CTA finale */ ?>
    <section class="sl-costi__cta-final sl-page-cta-final">
        <div class="sl-mono"><?php echo esc_html($cta_eyebrow); ?></div>
        <h2><?php echo esc_html($cta_h2); ?></h2>
        <?php if ($cta_p) : ?>
            <p><?php echo esc_html($cta_p); ?></p>
        <?php endif; ?>
        <a href="<?php echo esc_url($cta_url); ?>" class="sl-btn sl-btn--primary"><?php echo esc_html($cta_label); ?></a>
        <?php if ($cta_trust) : ?>
            <p class="sl-mono sl-costi__cta-trust"><?php echo esc_html($cta_trust); ?></p>
        <?php endif; ?>
    </section>

</article>
```

NB: il markup esatto deve replicare quello del backup `page.php.pre-wave3.backup` blocco `is_page('costi')`. Se il backup ha layout/wrapper aggiuntivi, mantienili. Lo scopo è frontend INVARIATO.

### 2.2 — page-casi.php (~10 min)

Pattern simile estratto dal backup:

```php
<?php
defined('ABSPATH') || exit;
$pid = get_the_ID();

$hero_eyebrow = get_field('hero_eyebrow', $pid) ?: '§ Risultati · Casi rappresentativi';
$hero_h1_pre  = get_field('hero_h1_pre', $pid) ?: 'Casi';
$hero_h1_em   = get_field('hero_h1_em', $pid) ?: 'rappresentativi.';
$hero_lede    = get_field('hero_lede', $pid) ?: '';
$intro_body   = get_field('intro_body', $pid) ?: '';

// Casi list (CPT saltelli_caso)
$casi_items = get_posts([
    'post_type' => 'saltelli_caso',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
]);

$cta_eyebrow = get_field('cta_eyebrow', $pid) ?: '';
$cta_h2      = get_field('cta_h2', $pid) ?: '';
// ... CTA fields

include __DIR__ . '/_partial-page-casi-render.php';
```

(O markup inline come per costi).

### 2.3 — page-contatti.php (~5 min)

Hero fields + map_iframe + come_arrivare + trust_signal + CF7 form.

### 2.4 — page-faq.php (~5 min)

Hero fields + TOC + 28 FAQ items query da CPT con grouping per `faq_topic` taxonomy.

```php
$topics = get_terms(['taxonomy' => 'faq_topic', 'hide_empty' => true]);
foreach ($topics as $topic) {
    $faqs_in_topic = get_posts([
        'post_type' => 'saltelli_faq',
        'posts_per_page' => -1,
        'tax_query' => [['taxonomy' => 'faq_topic', 'field' => 'slug', 'terms' => $topic->slug]],
    ]);
    // Render group + accordion items
}
```

### 2.5 — page-info-shared.php (~5 min)

Pattern unico per 5 page (guide-gratuite, come-lavoriamo, prima-consulenza, lavora-con-noi, richiedi-preventivo).

### 2.6 — page-chi-siamo.php (~5 min)

NB: chi-siamo ha 8 H2 e content rich. Refactor cauto:
- Hero da ACF (se esiste Field Group, altrimenti hardcoded fallback)
- Lawyer team query da CPT avvocato (post_object multiple)
- Principi query da CPT saltelli_principio
- Resto: mantieni hardcoded ma con commento `// TODO Wave 3.1: migrate to ACF when fields ready`

Se `chi-siamo` NON ha ACF Field Group dedicato, lascia il blocco hardcoded ma DENTRO `template-parts/page-chi-siamo.php` (più maintainable).

### 2.7 — Phase 2 verify

```bash
docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all

# Smoke + size compare con pre-Wave 3
for U in /costi/ /casi/ /contatti/ /faq/ /come-lavoriamo/ /chi-siamo/ /; do
    SIZE=$(curl -sL "http://localhost:8080$U?_=v1w3p2" -m 8 | wc -c | tr -d ' ')
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "http://localhost:8080$U?_=v1w3p2" -m 5)
    echo "  $U → HTTP $HTTP · size $SIZE bytes"
done

# Atteso: SIZE simile a pre-Wave 3 (±10%)
# Se SIZE molto più piccolo → fields ACF non sono renderizzati correttamente.
```

### 2.8 — Commit Phase 2

```bash
git add -A
git commit -m "feat(s2-v1.0.0-wave3): Phase 2 — 6 template-parts refactored (ACF fields + CPT query)"
```

---

## 📋 PHASE 3 — single-avvocato.php + single-competenza.php (~30 min)

### 3.1 — single-avvocato.php

Backup → leggi struttura attuale → identifica blocchi hardcoded che usano `bio_estesa`, `formazione`, `casi`, `aree_competenza_correlate`.

Sostituzioni chiave:

```php
$lawyer_id = get_the_ID();

// Hero
$hero_role = get_field('hero_role', $lawyer_id) ?: '';
$specs     = get_field('specializzazioni', $lawyer_id) ?: '';
$specs_arr = $specs ? array_filter(array_map('trim', explode("\n", $specs))) : [];

// Bio
$bio_breve  = get_field('bio_breve', $lawyer_id) ?: get_the_excerpt();
$bio_estesa = get_field('bio_estesa', $lawyer_id) ?: get_the_content();

// Foto (NON sovrascrivere _thumbnail_id — usa solo se ACF popolato)
$foto = get_field('foto_ritratto', $lawyer_id);

// Contatti diretti
$email     = get_field('email_pubblica', $lawyer_id);
$tel       = get_field('telefono_pubblico', $lawyer_id);
$whatsapp  = get_field('whatsapp', $lawyer_id);
$linkedin  = get_field('same_as_linkedin', $lawyer_id);

// Aree correlate (post_object multiple)
$aree_ids = get_field('aree_competenza_correlate', $lawyer_id) ?: [];

// Formazione (post_object multiple → CPT items)
$formazione_ids = get_field('formazione', $lawyer_id) ?: [];
$formazione_items = !empty($formazione_ids) ? get_posts([
    'post_type' => 'saltelli_formazione',
    'post__in' => is_array($formazione_ids) ? $formazione_ids : [$formazione_ids],
    'posts_per_page' => -1,
    'orderby' => 'post__in',
]) : [];

// Casi (post_object multiple → CPT items)
$casi_ids = get_field('casi_rappresentativi', $lawyer_id) ?: [];
$casi_items = !empty($casi_ids) ? get_posts([
    'post_type' => 'saltelli_caso',
    'post__in' => is_array($casi_ids) ? $casi_ids : [$casi_ids],
    'posts_per_page' => -1,
    'orderby' => 'post__in',
]) : [];

// ... il resto del template usa queste variabili invece di hardcoded ...
```

NB: per ogni lawyer (Emiliano/Fabiana/Antonia/Stefano), `_thumbnail_id` è già impostato (Step D protetto). Usa `has_post_thumbnail()` come priority, ACF `foto_ritratto` come secondario.

### 3.2 — single-competenza.php (scope tier-1)

Refactor blocco `is_tier_1` per usare ACF:

```php
$comp_id = get_the_ID();
$is_tier_1 = (bool) get_field('is_tier_1', $comp_id);

if ($is_tier_1) {
    $tier_label    = get_field('tier_label', $comp_id) ?: '';
    $subtitle      = get_field('subtitle', $comp_id) ?: '';
    $capsule       = get_field('answer_capsule', $comp_id) ?: '';
    $body_extended = get_field('body_extended', $comp_id) ?: get_the_content();
    
    $lead_attorneys_ids = get_field('lead_attorneys', $comp_id) ?: [];
    $casi_ids = get_field('casi_rappresentativi', $comp_id) ?: [];
    $faq_ids = get_field('faq', $comp_id) ?: [];
    $articoli_ids = get_field('articoli_correlati', $comp_id) ?: [];
    
    // Render con questi valori invece di hardcoded
}
```

### 3.3 — Phase 3 verify

```bash
docker compose run --rm wpcli cache flush

# Smoke 4 lawyer + 3 tier-1
for SLUG in emiliano-saltelli fabiana-saltelli antonia-battista stefano-gaetano-tedesco; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "http://localhost:8080/avvocati/$SLUG/?_=v1w3p3" -m 5)
    echo "  /avvocati/$SLUG/ → HTTP $HTTP"
done

for SLUG in diritto-tributario diritto-del-lavoro diritto-di-famiglia-lgbtq; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "http://localhost:8080/competenze/$SLUG/?_=v1w3p3" -m 5)
    echo "  /competenze/$SLUG/ → HTTP $HTTP"
done
```

### 3.4 — Commit Phase 3

```bash
git add -A
git commit -m "feat(s2-v1.0.0-wave3): Phase 3 — single-avvocato + single-competenza ACF refactor"
```

---

## 📋 PHASE 4 — Footer/header con Theme Options (~20 min)

### 4.1 — footer.php

Sostituisci hardcoded values con `get_field('x', 'options')`:

```php
// Brand
$brand_payoff    = get_field('brand_payoff', 'options') ?: 'Diritto, con misura';
$brand_statement = get_field('brand_statement_short', 'options') ?: '';

// Studio NAP
$studio_via      = get_field('studio_indirizzo_via', 'options') ?: 'Via Vannella Gaetani 27';
$studio_cap_citta = get_field('studio_cap_citta', 'options') ?: '80121 Napoli';
$studio_quartiere = get_field('studio_quartiere', 'options') ?: 'Chiaia';
$studio_orari    = get_field('studio_orari_settimana', 'options') ?: '';
$studio_orari_sabato = get_field('studio_orari_sabato', 'options') ?: '';
$studio_telefono = get_field('studio_telefono_pubblico', 'options') ?: '';
$studio_email    = get_field('studio_email', 'options') ?: '';
$studio_pec      = get_field('studio_pec', 'options') ?: '';
$studio_piva     = get_field('studio_piva', 'options') ?: '';
$studio_ordine   = get_field('studio_ordine_avvocati', 'options') ?: '';

// Footer
$footer_credit_text = get_field('footer_credit_text', 'options') ?: 'Realizzato da Adsolut Web Agency';
$footer_credit_url  = get_field('footer_credit_url', 'options') ?: 'https://adsolut.it';
$footer_newsletter_enabled = (bool) get_field('footer_newsletter_enabled', 'options');

// Social
$social_instagram = get_field('social_instagram', 'options') ?: '';
$social_linkedin  = get_field('social_linkedin', 'options') ?: '';
$social_twitter   = get_field('social_twitter', 'options') ?: '';
$social_facebook  = get_field('social_facebook', 'options') ?: '';

// CTA Defaults
$cta_default_label = get_field('cta_default_label', 'options') ?: 'Prenota un incontro →';
$cta_default_url   = get_field('cta_default_url', 'options') ?: '/contatti/';
$cta_trust_signal  = get_field('cta_trust_signal', 'options') ?: 'Risposta entro 24 ore · Riservatezza assoluta';

// ... il resto del footer.php usa queste variabili ...
```

### 4.2 — header.php

Stesso pattern: telefono click-to-call, brand payoff, ecc. usano Theme Options.

### 4.3 — Helper globale (opzionale)

Crea `inc/saltelli-options.php` per accesso rapido:

```php
function saltelli_opt($key, $default = '') {
    $v = get_field($key, 'options');
    return $v !== '' && $v !== null && $v !== false ? $v : $default;
}
```

Poi nel template usi `saltelli_opt('studio_telefono_pubblico')`. Più conciso.

### 4.4 — Phase 4 verify

```bash
docker compose run --rm wpcli cache flush

# Verify NAP/brand visibili in footer
curl -sL "http://localhost:8080/?_=v1w3p4" -m 5 | grep -ciE "Vannella Gaetani|Diritto, con misura|Realizzato da Adsolut"
# Atteso: ≥2 (NAP + brand statement + credit visibili)
```

### 4.5 — Commit Phase 4

```bash
git add -A
git commit -m "feat(s2-v1.0.0-wave3): Phase 4 — footer/header bound to Theme Options ACF"
```

---

## 📋 PHASE 5 — Smoke + Bump + Deploy (~15 min)

### 5.1 — Smoke globale

```bash
echo "═══ FRONTEND SMOKE TEST POST-WAVE3 ═══"
PASS=0
for U in / /chi-siamo/ /avvocati/ /avvocati/emiliano-saltelli/ /casi/ /costi/ /contatti/ /faq/ /come-lavoriamo/ /prima-consulenza/ /lavora-con-noi/ /richiedi-preventivo/ /competenze/diritto-tributario/ /tipo-area/privati/ /glossario-legale/; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "http://localhost:8080$U?_=v1w3p5" -m 8)
    SYM="✓"; [ "$HTTP" != "200" ] && SYM="✗"
    [ "$HTTP" = "200" ] && PASS=$((PASS+1))
    printf "  %s %-45s HTTP %s\n" "$SYM" "$U" "$HTTP"
done
echo "  → $PASS/15 PASS"

# Verify NAP cross-page (deve apparire nel footer di TUTTE)
echo ""
echo "═══ NAP cross-page (Theme Options) ═══"
for U in / /costi/ /chi-siamo/; do
    NAP=$(curl -sL "http://localhost:8080$U" -m 5 | grep -c "Vannella Gaetani")
    echo "  $U → NAP visible: $NAP"
done
```

### 5.2 — Bump version

```bash
sed -i.bak 's/Version: [0-9.]\+.*/Version: 1.0.0-recovery-wave3/' wp-content/themes/saltelli/style.css
sed -i.bak "s/SALTELLI_THEME_VERSION', '[^']*'/SALTELLI_THEME_VERSION', '1.0.0-recovery-wave3'/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak
```

### 5.3 — Deploy droplet

⚠ ATTENZIONE: il refactor cambia file PHP, ma i fields ACF DEVONO essere già popolati sul droplet (Wave 2 deploy). Verifica:

```bash
# Verify droplet ACF fields populated
ssh deploy@178.62.207.50 "
cd /var/www/saltelli
sudo -u www-data wp eval '
echo \"costi hero_eyebrow: \" . get_field(\"hero_eyebrow\", 2695) . \"\\n\";
echo \"theme telefono: \" . get_field(\"studio_telefono_pubblico\", \"options\") . \"\\n\";
'
"
```

Se vuoto: prima replica Wave 2 sul droplet (ri-esegui WP-CLI eval scripts), POI deploy Wave 3.

```bash
# Deploy template files refactored
rsync -avz \
    wp-content/themes/saltelli/page.php \
    wp-content/themes/saltelli/single-avvocato.php \
    wp-content/themes/saltelli/single-competenza.php \
    wp-content/themes/saltelli/footer.php \
    wp-content/themes/saltelli/header.php \
    wp-content/themes/saltelli/style.css \
    wp-content/themes/saltelli/functions.php \
    deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/

rsync -avz wp-content/themes/saltelli/template-parts/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/template-parts/

ssh deploy@178.62.207.50 "
    cd /var/www/saltelli
    sudo -u www-data wp cache flush --path=/var/www/saltelli
    sudo -u www-data wp transient delete --all --path=/var/www/saltelli
"

# Smoke live
echo "═══ SMOKE LIVE v1.0-wave3 ═══"
for URL in / /costi/ /casi/ /contatti/ /faq/ /come-lavoriamo/ /avvocati/emiliano-saltelli/ /competenze/diritto-tributario/; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it$URL?_=v1w3" -m 10)
    echo "  $URL → HTTP $HTTP"
done
```

### 5.4 — Final commit

```bash
git add -A
git commit -m "feat(v1.0.0-wave3): Wave 3 complete — Template refactor (page.php router + 6 template-parts + single-CPT + footer/header bound to ACF)"
git push origin main
```

---

## 📊 DELIVERABLE finale Wave 3

Report: `.claude/knowledge/recovery/v1.0-WAVE3-TEMPLATE-REFACTOR.md`

```markdown
# v1.0.0 Wave 3 COMPLETE — Template Refactor

## Score: 5/5 phases PASS

## Per phase
- Phase 1 — page.php router + template-parts struttura: ✓
- Phase 2 — 6 template-parts refactored (ACF reads): ✓
- Phase 3 — single-avvocato + single-competenza refactor: ✓
- Phase 4 — footer/header bound to Theme Options: ✓
- Phase 5 — Smoke + bump + deploy: ✓

## Cumulative refactor stats

page.php:
  Pre Wave 3:   1274 righe (94KB hardcoded)
  Post Wave 3:  ~80 righe (router) + 6 template-parts (avg ~150 righe)
  Reduction:    -72% codice page.php

Hardcoded → ACF reads:
  Theme Options:  32 fields read da get_field('x', 'options')
  Page WP fields: ~100 fields cross-page
  Lawyer/Tier-1:  ~80 fields
  CPT items:      63 items render via WP_Query

Frontend status: INVARIATO (smoke 15/15 URL HTTP 200, content identico).

## Cliente Elena/Ludovica autonomy

Adesso il cliente può modificare via WP-Admin:
- ✓ Saltelli Settings (NAP, brand, footer, social, CTA)
- ✓ Page WP custom (hero, lede, CTA, content sezioni)
- ✓ Lawyer profiles (bio, formazione, casi, aree)
- ✓ CPT items (FAQ, casi, modalità, scenari, principi, trust, formazione, guide)
- ✓ Tier-1 competenze (capsule, body, FAQ, casi)

Sito si aggiorna automaticamente al salvataggio.

## Next: v1.0 Production cut
- Replica Wave 2 + Wave 3 sul droplet (idempotente)
- Sessione formazione editor (Adsolut interno con Elena/Ludovica)
- WOFF2 self-hosted + SRI + Lighthouse pre-launch
- DNS switch staging → production
```

Quando finito, segnala: **"Wave 3 COMPLETE. Cliente autonomous. ~140 fields ACF + 63 CPT items binded. Frontend invariato."**

---

## 🆘 Se incontri imprevisti

```
- Frontend size drop drastico post-Phase 1: TODO comment in template-parts non popolati. 
  Continua Phase 2 immediatamente per ripristinare content.
  
- get_field() ritorna null per page WP: verifica $page_id corretto + Field Group active.
  
- chi-siamo Field Group non esiste: refactor MINIMAL per chi-siamo (solo team + principi via CPT).
  
- single-avvocato foto_ritratto vs has_post_thumbnail: usa has_post_thumbnail() come PRIMARY 
  (Step D protetto), foto_ritratto ACF come fallback.
  
- Schema FAQPage duplicato (Yoast + custom): wrap in `if (!has_filter('wpseo_schema_faqpage'))`.
  
- Droplet Wave 2 fields vuoti: replica idempotente WP-CLI eval prima di deploy Wave 3.
  
- Backup pre-Wave 3 in /tmp/saltelli-*.php.pre-wave3.backup (rollback sicuro).
```

Tempo realistic Wave 3: **~2-2.5h sequenziale single agent**.

Quando completata Wave 3, il sito sarà **production-ready CMS-managed**: cliente autonomous, sviluppatore non più necessario per content updates.
