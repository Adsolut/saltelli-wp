<?php
/**
 * Template: Home — blog index editoriale (page_for_posts).
 *
 * Wave 3 · Task 07 · v0.19.0 — match JSX saltelli-s2-blog-archive.jsx (LAYOUT SACRO).
 * Override pulito di index.php per /blog/. Non tocca archive.php (category/tag/tax/date/author).
 *
 * @package Saltelli
 */

get_header();

global $wp_query;
$paged       = max(1, (int) ($wp_query->query_vars['paged'] ?? 0) ?: 1);
$page_count  = max(1, (int) $wp_query->post_count);
$total_posts = (int) ($wp_query->found_posts ?: wp_count_posts('post')->publish);
$ppp         = (int) ($wp_query->query_vars['posts_per_page'] ?? 0);
if ($ppp <= 0 || $ppp < $page_count) { $ppp = $page_count; }
$cat_count   = (int) wp_count_terms(['taxonomy' => 'category', 'hide_empty' => true]);
if (is_wp_error($cat_count) || !$cat_count) { $cat_count = 0; }
$first       = ($paged - 1) * $ppp + 1;
$last_query  = min($first + $page_count - 1, $total_posts);

$tabs        = get_categories([
    'taxonomy'   => 'category',
    'hide_empty' => true,
    'orderby'    => 'count',
    'order'      => 'DESC',
    'number'     => 7,
]);
$current_cat = is_category() ? get_queried_object_id() : 0;

$featured = null;
$rest_ids = [];
if (have_posts()) {
    $i = 0;
    while (have_posts()) {
        the_post();
        if ($i === 0) {
            $featured = get_post();
        } else {
            $rest_ids[] = get_the_ID();
        }
        $i++;
    }
    rewind_posts();
}

$last_mod = get_lastpostmodified('blog');
$last_mod_human = $last_mod ? wp_date('j F Y', strtotime($last_mod)) : '';
?>

<section class="sl-blog2">

    <!-- HERO ARCHIVE -->
    <header class="sl-blog2__hero sl-container">
        <div class="sl-blog2__hero-left">
            <div class="sl-mono sl-blog2__eyebrow">§ Editoriale &middot; Saltelli</div>
            <h1 class="sl-blog2__h1" data-split-reveal><?php echo wp_kses(saltelli_split_h1_words('Editoriale.'), ['span' => ['class' => true, 'data-i' => true]]); ?></h1>
        </div>
        <div class="sl-blog2__hero-right">
            <p class="sl-blog2__lede">
                Articoli, casi vinti, novit&agrave; giurisprudenziali da Studio Legale Saltelli &amp; Partners. Aggiornato settimanalmente.
            </p>
            <div class="sl-mono sl-blog2__counter">
                <?php echo esc_html(number_format_i18n($total_posts)); ?> articoli &middot;
                <?php echo esc_html(number_format_i18n($cat_count)); ?> categorie<?php
                if ($last_mod_human) {
                    echo ' &middot; agg. ' . esc_html($last_mod_human);
                }
                ?>
            </div>
        </div>
    </header>

    <!-- CATEGORY TABS sticky -->
    <?php if (!empty($tabs)) : ?>
    <nav class="sl-blog2__tabs" aria-label="<?php esc_attr_e('Categorie editoriale', 'saltelli'); ?>">
        <div class="sl-blog2__tabs-inner sl-container">
            <a href="<?php echo esc_url(get_post_type_archive_link('post') ?: home_url('/blog/')); ?>"
               class="sl-blog2__tab<?php echo $current_cat === 0 ? ' is-active' : ''; ?>">Tutti</a>
            <?php foreach ($tabs as $term) : ?>
                <a href="<?php echo esc_url(get_term_link($term)); ?>"
                   class="sl-blog2__tab<?php echo $current_cat === (int) $term->term_id ? ' is-active' : ''; ?>">
                    <?php echo esc_html($term->name); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>
    <?php endif; ?>

    <?php if ($featured && $paged === 1) :
        $f_id    = $featured->ID;
        $f_link  = get_permalink($f_id);
        $f_cats  = get_the_category($f_id);
        $f_cat   = !empty($f_cats) ? $f_cats[0] : null;
        $f_date  = get_the_date('j F Y', $f_id);
        $f_auth  = get_the_author_meta('display_name', $featured->post_author);
        $f_excerpt = wp_trim_words(get_the_excerpt($f_id), 38, '&hellip;');
        $f_thumb = has_post_thumbnail($f_id) ? get_the_post_thumbnail_url($f_id, 'large') : '';
        $f_words = str_word_count(wp_strip_all_tags($featured->post_content));
        $f_read  = max(2, (int) round($f_words / 220));
    ?>
    <!-- FEATURED -->
    <section class="sl-blog2__featured-wrap sl-container">
        <div class="sl-mono sl-blog2__featured-eyebrow">&sect; In evidenza &middot; <?php echo esc_html($f_date); ?></div>
        <a href="<?php echo esc_url($f_link); ?>" class="sl-blog2__featured">
            <div class="sl-blog2__featured-media<?php echo $f_thumb ? '' : ' is-placeholder'; ?>"
                 <?php echo $f_thumb ? 'style="background-image:url(' . esc_url($f_thumb) . ')"' : ''; ?>>
                <span class="sl-mono sl-blog2__featured-plate">Plate &middot; IV</span>
            </div>
            <div class="sl-blog2__featured-body">
                <div>
                    <?php if ($f_cat) : ?>
                        <div class="sl-mono sl-blog2__featured-cat"><?php echo esc_html($f_cat->name); ?></div>
                    <?php endif; ?>
                    <h2 class="sl-blog2__featured-title"><?php echo esc_html(get_the_title($f_id)); ?></h2>
                    <p class="sl-blog2__featured-lede"><?php echo esc_html($f_excerpt); ?></p>
                </div>
                <div>
                    <div class="sl-mono sl-blog2__featured-meta">
                        <?php echo esc_html($f_date); ?> &middot;
                        <?php echo esc_html($f_auth); ?> &middot;
                        <?php echo esc_html($f_read); ?> min
                    </div>
                    <span class="sl-btn">Leggi l'articolo<span class="arrow">&rarr;</span></span>
                </div>
            </div>
        </a>
    </section>
    <?php endif; ?>

    <!-- GRID 3-col -->
    <section class="sl-blog2__grid-wrap sl-container">
        <header class="sl-blog2__grid-head">
            <div class="sl-mono">&sect; Archivio &middot; <?php
                $grid_count = $paged === 1 ? count($rest_ids) : $last_query - $first + 1;
                echo esc_html(number_format_i18n($grid_count));
            ?> di <?php echo esc_html(number_format_i18n($total_posts)); ?></div>
            <h2 class="sl-blog2__grid-title">Tutti gli articoli.</h2>
        </header>

        <?php if ($paged === 1 && empty($rest_ids) && !$featured) : ?>
            <p class="sl-mono"><?php esc_html_e('Nessun contenuto trovato.', 'saltelli'); ?></p>
        <?php else : ?>
            <ul class="sl-blog2__grid">
                <?php
                rewind_posts();
                $idx = 0;
                while (have_posts()) :
                    the_post();
                    if ($paged === 1 && $idx === 0) { $idx++; continue; }
                    $idx++;
                    $cats   = get_the_category();
                    $c      = !empty($cats) ? $cats[0] : null;
                    $thumb  = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'medium_large') : '';
                    $auth   = get_the_author_meta('display_name', get_post_field('post_author', get_the_ID()));
                    $words  = str_word_count(wp_strip_all_tags(get_the_content()));
                    $read   = max(2, (int) round($words / 220));
                ?>
                    <li class="sl-blog2__cell">
                        <a href="<?php the_permalink(); ?>" class="sl-blog2__card">
                            <div class="sl-blog2__card-media<?php echo $thumb ? '' : ' is-placeholder'; ?>"
                                 <?php echo $thumb ? 'style="background-image:url(' . esc_url($thumb) . ')"' : ''; ?>>
                                <span class="sl-blog2__card-media-zoom" aria-hidden="true"></span>
                            </div>
                            <?php if ($c) : ?>
                                <div class="sl-mono sl-blog2__card-cat"><?php echo esc_html($c->name); ?></div>
                            <?php endif; ?>
                            <h3 class="sl-blog2__card-title"><?php the_title(); ?></h3>
                            <p class="sl-blog2__card-excerpt">
                                <?php echo esc_html(wp_trim_words(get_the_excerpt(), 24, '&hellip;')); ?>
                            </p>
                            <div class="sl-mono sl-blog2__card-meta">
                                <?php echo esc_html(get_the_date('j M Y')); ?> &middot;
                                <?php echo esc_html($auth); ?> &middot;
                                <?php echo esc_html($read); ?> min
                            </div>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </section>

    <!-- PAGINATION editoriale -->
    <?php
    global $wp_query;
    $max_pages = (int) $wp_query->max_num_pages;
    if ($max_pages > 1) :
        $prev_link = get_previous_posts_page_link();
        $next_link = $paged < $max_pages ? next_posts($max_pages, false) : '';
    ?>
    <section class="sl-blog2__pager-wrap sl-container">
        <div class="sl-blog2__pager">
            <?php if ($paged > 1 && $prev_link) : ?>
                <a href="<?php echo esc_url($prev_link); ?>" class="sl-mono sl-blog2__pager-prev">&larr; Precedenti</a>
            <?php else : ?>
                <span class="sl-mono sl-blog2__pager-prev is-disabled">&larr; Precedenti</span>
            <?php endif; ?>
            <div class="sl-mono sl-blog2__pager-counter">
                <?php echo esc_html(number_format_i18n($first)); ?>
                &mdash;
                <?php echo esc_html(number_format_i18n($last_query)); ?>
                di <?php echo esc_html(number_format_i18n($total_posts)); ?>
            </div>
            <?php if ($paged < $max_pages && $next_link) : ?>
                <a href="<?php echo esc_url($next_link); ?>" class="sl-mono sl-blog2__pager-next">Successivi &rarr;</a>
            <?php else : ?>
                <span class="sl-mono sl-blog2__pager-next is-disabled">Successivi &rarr;</span>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- NEWSLETTER inline -->
    <section class="sl-blog2__newsletter">
        <div class="sl-blog2__newsletter-inner sl-container">
            <div class="sl-blog2__newsletter-left">
                <div class="sl-mono">&sect; Newsletter</div>
                <h2 class="sl-blog2__newsletter-h2">
                    Un articolo<br>
                    <em>al mese.</em>
                </h2>
            </div>
            <div class="sl-blog2__newsletter-right">
                <p class="sl-blog2__newsletter-lede">
                    Una sola mail al mese. Solo casi vinti, novit&agrave; giurisprudenziali, e qualche nota personale. Niente promozione.
                </p>
                <form class="sl-blog2__newsletter-form" method="post" action="<?php echo esc_url(home_url('/contatti/')); ?>" novalidate>
                    <label class="sl-blog2__newsletter-field">
                        <span class="sl-mono">Email</span>
                        <input type="email" name="newsletter_email" required placeholder="lei@esempio.it" autocomplete="email">
                    </label>
                    <button type="submit" class="sl-btn">Iscriviti<span class="arrow">&rarr;</span></button>
                </form>
            </div>
        </div>
    </section>

</section>

<?php
/* ─── Schema JSON-LD: Blog + ItemList Article ──────────────────────────
 * Yoast emette WebPage/CollectionPage + BreadcrumbList + Organization. Per
 * coabitazione, NON ri-emettiamo quei nodi: aggiungiamo solo Blog + ItemList. */
if (function_exists('saltelli_emit_jsonld')) {
    $items = [];
    $position = 1;
    rewind_posts();
    while (have_posts()) {
        the_post();
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $position++,
            'url'      => get_permalink(),
            'name'     => wp_strip_all_tags(get_the_title()),
        ];
    }
    rewind_posts();

    $blog_url = get_permalink(get_option('page_for_posts')) ?: home_url('/blog/');

    saltelli_emit_jsonld([
        '@context'    => 'https://schema.org',
        '@type'       => 'Blog',
        '@id'         => trailingslashit($blog_url) . '#blog',
        'url'         => $blog_url,
        'name'        => 'Editoriale · Studio Legale Saltelli',
        'description' => 'Articoli, casi vinti, novità giurisprudenziali da Studio Legale Saltelli & Partners.',
        'inLanguage'  => 'it-IT',
        'publisher'   => [
            '@type' => 'Organization',
            'name'  => 'Studio Legale Saltelli & Partners',
            'url'   => home_url('/'),
        ],
    ]);

    if (!empty($items)) {
        saltelli_emit_jsonld([
            '@context'        => 'https://schema.org',
            '@type'           => 'ItemList',
            '@id'             => trailingslashit($blog_url) . '#itemlist',
            'itemListOrder'   => 'https://schema.org/ItemListOrderDescending',
            'numberOfItems'   => count($items),
            'itemListElement' => $items,
        ]);
    }
}

get_footer();
