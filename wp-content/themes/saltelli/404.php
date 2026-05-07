<?php
/**
 * Template: 404 editoriale (Sessione 2 · Wave 3 · Task 10).
 *
 * Layout sacro: .claude/knowledge/design/sessione-2/saltelli-s2-404.jsx
 * Hero "errore quietato" + drop-cap L · 3 card recovery · 5-7 aree mini ·
 * 3 articoli recenti · CTA prenota. Tono editoriale calmo, non drammatico.
 *
 * Schema JSON-LD: WebPage + isPartOf homepage. Yoast coabitation rispettata
 * (skip se WPSEO_VERSION attivo, come da policy del progetto).
 *
 * @package Saltelli
 */

status_header(404);
nocache_headers();
get_header();

$sl404_studio    = saltelli_studio_data();
/* Wave 4.6: legge da studio_telefono_pubblico (Wave 1 schema) — dead alias contact_* rimosso. */
$sl404_phone     = (string) saltelli_option('studio_telefono_pubblico', '+39 081 1813 1119');
$sl404_phone_e164 = saltelli_studio_phone_e164();
$sl404_wa_digits = preg_replace('/[^0-9]/', '', (string) $sl404_studio['whatsapp']);
$sl404_wa_href   = 'https://wa.me/' . $sl404_wa_digits . '?text=' . rawurlencode("Ciao, sono arrivato qui per errore — vorrei una consulenza presso lo Studio Saltelli.");

// Aree: tier-1 prima, poi tier-2 in menu_order, max 6 (range 5-7 da spec).
// Wave 4.6: use is_tier_1 (Wave 1 ACF schema canonico).
$sl404_aree = get_posts([
    'post_type'      => 'competenza',
    'posts_per_page' => 6,
    'meta_key'       => 'is_tier_1',
    'orderby'        => [
        'meta_value_num' => 'DESC',
        'menu_order'     => 'ASC',
        'title'          => 'ASC',
    ],
]);

// Articoli recenti: 3 ultimi post pubblicati.
$sl404_articoli = get_posts([
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
]);
?>

<main id="main" class="sl-404">

    <section class="sl-404__hero">
        <div class="sl-container sl-404__hero-inner">
            <div class="sl-mono sl-404__eyebrow"><?php esc_html_e('Errore 404 · Pagina non trovata', 'saltelli'); ?></div>
            <div class="sl-404__hero-grid">
                <h1 class="sl-404__title">
                    <?php esc_html_e('La pagina', 'saltelli'); ?><br>
                    <em><?php esc_html_e('non esiste.', 'saltelli'); ?></em>
                </h1>
                <div class="sl-404__lede">
                    <p>
                        <span class="sl-404__dropcap" aria-hidden="true">L</span><span class="sl-404__lede-prose"><?php esc_html_e("a pagina che cercavi non esiste, o forse era qui e l'abbiamo spostata. Il diritto italiano è in continua evoluzione, anche le pagine.", 'saltelli'); ?></span>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="sl-404__recovery">
        <div class="sl-container">
            <div class="sl-mono sl-404__section-tag"><?php esc_html_e('§ 01 — Cosa puoi fare', 'saltelli'); ?></div>
            <div class="sl-404__cards">

                <article class="sl-404__card">
                    <div class="sl-mono sl-404__card-num"><?php esc_html_e('01 · Torna alla home', 'saltelli'); ?></div>
                    <h2 class="sl-404__card-title"><?php esc_html_e("Riparti dall'inizio.", 'saltelli'); ?></h2>
                    <p class="sl-404__card-text">
                        <?php esc_html_e('La homepage raccoglie tutte le 17 aree di pratica, gli avvocati e i casi rappresentativi.', 'saltelli'); ?>
                    </p>
                    <a class="sl-btn sl-btn--primary sl-404__card-cta" href="<?php echo esc_url(home_url('/')); ?>">
                        <span><?php esc_html_e('Vai alla home', 'saltelli'); ?></span>
                        <span class="arrow" aria-hidden="true">→</span>
                    </a>
                </article>

                <article class="sl-404__card">
                    <div class="sl-mono sl-404__card-num"><?php esc_html_e('02 · Cerca', 'saltelli'); ?></div>
                    <h2 class="sl-404__card-title"><?php esc_html_e('Cerca quello', 'saltelli'); ?><br><?php esc_html_e('che ti serviva.', 'saltelli'); ?></h2>
                    <form class="sl-404__search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <label for="search-404" class="screen-reader-text"><?php esc_html_e('Cerca nel sito', 'saltelli'); ?></label>
                        <input id="search-404"
                               class="sl-404__search-input"
                               type="search"
                               name="s"
                               value="<?php echo esc_attr(get_search_query()); ?>"
                               placeholder="<?php esc_attr_e('Es. cartella, separazione, lavoro…', 'saltelli'); ?>">
                        <button type="submit" class="sl-btn sl-404__search-submit">
                            <span><?php esc_html_e('Cerca', 'saltelli'); ?></span>
                            <span class="arrow" aria-hidden="true">→</span>
                        </button>
                    </form>
                </article>

                <article class="sl-404__card">
                    <div class="sl-mono sl-404__card-num"><?php esc_html_e('03 · Contatto diretto', 'saltelli'); ?></div>
                    <h2 class="sl-404__card-title"><?php esc_html_e('Scrivici, chiamaci.', 'saltelli'); ?></h2>
                    <div class="sl-404__contact-list">
                        <a class="sl-404__contact-row" href="tel:<?php echo esc_attr($sl404_phone_e164); ?>">
                            <span class="sl-mono"><?php esc_html_e('Telefono', 'saltelli'); ?></span>
                            <span class="sl-404__contact-value"><?php echo esc_html($sl404_phone); ?> <span class="arrow" aria-hidden="true">→</span></span>
                        </a>
                        <a class="sl-404__contact-row" href="<?php echo esc_url($sl404_wa_href); ?>" rel="noopener" target="_blank">
                            <span class="sl-mono"><?php esc_html_e('WhatsApp', 'saltelli'); ?></span>
                            <span class="sl-404__contact-value"><?php esc_html_e('Scrivi su WhatsApp', 'saltelli'); ?> <span class="arrow" aria-hidden="true">→</span></span>
                        </a>
                    </div>
                </article>

            </div>
        </div>
    </section>

    <?php if (!empty($sl404_aree)) : ?>
    <section class="sl-404__suggest">
        <div class="sl-container">
            <header class="sl-404__suggest-head">
                <div class="sl-mono sl-404__section-tag"><?php esc_html_e('§ 02 — Forse cercavi', 'saltelli'); ?></div>
                <h2 class="sl-404__suggest-title">
                    <?php esc_html_e('Una di queste', 'saltelli'); ?><br>
                    <em><?php esc_html_e('diciassette aree?', 'saltelli'); ?></em>
                </h2>
            </header>
            <div class="sl-404__areas sl-areas-archive">
                <?php
                $sl404_i = 0;
                foreach ($sl404_aree as $sl404_p) :
                    $sl404_i++;
                    $sl404_is_tier1 = (bool) saltelli_field('is_tier_1_focus', $sl404_p->ID, false);
                    $sl404_cat_label = saltelli_competenza_category_label($sl404_p->ID);
                    $sl404_num       = str_pad((string) $sl404_i, 2, '0', STR_PAD_LEFT);
                    ?>
                    <a class="sl-area<?php echo $sl404_is_tier1 ? ' sl-area--tier1' : ''; ?>"
                       href="<?php echo esc_url(get_permalink($sl404_p)); ?>">
                        <span class="sl-area__num sl-mono"><?php echo esc_html($sl404_num); ?> / 17</span>
                        <span class="sl-area__title"><?php echo esc_html(get_the_title($sl404_p)); ?></span>
                        <span class="sl-area__meta sl-mono">
                            <?php echo esc_html($sl404_is_tier1 ? __('Tier 1', 'saltelli') : ($sl404_cat_label ?: __('Tier 2', 'saltelli'))); ?>
                            <span class="arrow" aria-hidden="true">→</span>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="sl-404__suggest-foot">
                <a class="sl-btn" href="<?php echo esc_url(get_post_type_archive_link('competenza')); ?>">
                    <span><?php esc_html_e('Tutte le 17 aree', 'saltelli'); ?></span>
                    <span class="arrow" aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($sl404_articoli)) : ?>
    <section class="sl-404__editorial">
        <div class="sl-container">
            <header class="sl-404__editorial-head">
                <div class="sl-mono sl-404__section-tag"><?php esc_html_e('§ 03 — Articoli recenti', 'saltelli'); ?></div>
                <h2 class="sl-404__editorial-title"><?php esc_html_e('Editoriale.', 'saltelli'); ?></h2>
            </header>
            <div class="sl-404__articles">
                <?php foreach ($sl404_articoli as $sl404_post) :
                    $sl404_cats = get_the_category($sl404_post->ID);
                    $sl404_cat  = !empty($sl404_cats) ? $sl404_cats[0]->name : __('Editoriale', 'saltelli');
                    $sl404_read = (string) saltelli_field('reading_time', $sl404_post->ID, '');
                    if ($sl404_read === '') {
                        $sl404_words = str_word_count(wp_strip_all_tags(get_post_field('post_content', $sl404_post->ID)));
                        $sl404_read  = max(1, (int) round($sl404_words / 220)) . ' min';
                    }
                    $sl404_has_thumb = has_post_thumbnail($sl404_post->ID);
                    ?>
                    <a class="sl-404__article" href="<?php echo esc_url(get_permalink($sl404_post)); ?>">
                        <?php if ($sl404_has_thumb) : ?>
                            <div class="sl-404__article-media">
                                <?php echo get_the_post_thumbnail($sl404_post, 'medium_large', ['class' => 'sl-404__article-img', 'loading' => 'lazy']); ?>
                            </div>
                        <?php else : ?>
                            <div class="sl-404__article-media sl-404__article-media--placeholder" aria-hidden="true"></div>
                        <?php endif; ?>
                        <div class="sl-mono sl-404__article-cat"><?php echo esc_html($sl404_cat); ?></div>
                        <h3 class="sl-404__article-title"><?php echo esc_html(get_the_title($sl404_post)); ?></h3>
                        <div class="sl-mono sl-404__article-meta">
                            <?php echo esc_html(get_the_date('j M Y', $sl404_post)); ?> · <?php echo esc_html($sl404_read); ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="sl-404__cta">
        <div class="sl-container sl-404__cta-grid">
            <div class="sl-mono sl-404__section-tag"><?php esc_html_e('§ Sempre presente', 'saltelli'); ?></div>
            <div class="sl-404__cta-body">
                <h2 class="sl-404__cta-title">
                    <?php esc_html_e('Prenota una', 'saltelli'); ?><br>
                    <em><?php esc_html_e('consulenza gratuita.', 'saltelli'); ?></em>
                </h2>
                <p class="sl-404__cta-text">
                    <?php esc_html_e('Anche se sei capitato qui per errore: il primo incontro resta gratuito.', 'saltelli'); ?>
                </p>
                <a class="sl-btn sl-btn--primary sl-404__cta-btn" href="<?php echo esc_url(home_url('/contatti/')); ?>">
                    <span><?php esc_html_e('Prenota gratuita', 'saltelli'); ?></span>
                    <span class="arrow" aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </section>

</main>

<?php
/**
 * Schema JSON-LD WebPage + isPartOf homepage.
 * Skip se Yoast attivo (coabitation policy: niente duplicati).
 */
if (function_exists('saltelli_emit_jsonld') && !saltelli_seo_plugin_active()) {
    $sl404_home = home_url('/');
    saltelli_emit_jsonld([
        '@context'    => 'https://schema.org',
        '@type'       => 'WebPage',
        '@id'         => trailingslashit($sl404_home) . '#404',
        'url'         => trailingslashit($sl404_home) . '#404',
        'name'        => __('Pagina non trovata · Studio Legale Saltelli & Partners', 'saltelli'),
        'description' => __('La pagina richiesta non esiste o è stata spostata. Esplora le 17 aree di pratica, gli articoli recenti o prenota una consulenza gratuita.', 'saltelli'),
        'inLanguage'  => 'it-IT',
        'isPartOf'    => [
            '@type' => 'WebSite',
            '@id'   => $sl404_home . '#website',
            'url'   => $sl404_home,
            'name'  => $sl404_studio['legal_name'],
        ],
    ]);
}

get_footer();
