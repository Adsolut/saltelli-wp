<?php
/**
 * Template: Taxonomy archive — tipo-area.
 * Lista le competenze taggate con un termine di tipo-area
 * (privati / imprese / contenzioso / altri). Pattern visuale
 * coerente con archive-competenza.php: stessa lista .sl-areas
 * con tier-1 first ordering, drop-cap accent, mobile fix M1.
 *
 * @package Saltelli
 */
get_header();

$term       = get_queried_object();
$term_name  = $term && !empty($term->name) ? $term->name : '';
$term_desc  = $term && !empty($term->description) ? $term->description : '';

// Riusa la query archive-competenza ordering: tier-1 first.
$competenze = get_posts([
    'post_type'   => 'competenza',
    'numberposts' => -1,
    'meta_key'    => 'is_tier_1_focus',
    'orderby'     => [
        'meta_value_num' => 'DESC',
        'menu_order'     => 'ASC',
        'title'          => 'ASC',
    ],
    'tax_query'   => [[
        'taxonomy' => 'tipo-area',
        'field'    => 'term_id',
        'terms'    => [$term ? (int) $term->term_id : 0],
    ]],
]);
?>

<section class="sl-areas sl-areas--archive sl-areas-archive">
    <div class="sl-container">

        <header class="sl-section-head sl-areas__archive-head">
            <nav class="sl-mono sl-page__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'saltelli'); ?>">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'saltelli'); ?></a>
                / <a href="<?php echo esc_url(get_post_type_archive_link('competenza')); ?>"><?php esc_html_e('Competenze', 'saltelli'); ?></a>
                / <span><?php echo esc_html($term_name); ?></span>
            </nav>

            <h1 class="sl-section-title">
                <?php echo esc_html($term_name); ?><br>
                <em><?php
                    printf(
                        /* translators: %d numero competenze */
                        esc_html(_n('%d area', '%d aree', count($competenze), 'saltelli')),
                        (int) count($competenze)
                    );
                ?></em>
            </h1>

            <?php if ($term_desc) : ?>
                <p class="sl-areas__archive-lede"><?php echo esc_html($term_desc); ?></p>
            <?php else : ?>
                <p class="sl-areas__archive-lede">
                    <?php
                    $auto_lede = sprintf(
                        /* translators: %s nome categoria es. "Privati" */
                        esc_html__('Le aree di competenza dello studio dedicate alla categoria %s. Tier-1 sono le tre aree presidiate in profondità.', 'saltelli'),
                        '<strong>' . esc_html(strtolower($term_name)) . '</strong>'
                    );
                    echo wp_kses($auto_lede, ['strong' => []]);
                    ?>
                </p>
            <?php endif; ?>
        </header>

        <?php if (!empty($competenze)) : ?>
            <div class="sl-areas__list">
                <?php
                $i     = 0;
                $total = count($competenze);
                foreach ($competenze as $p) :
                    $i++;
                    $num       = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                    $cat_label = saltelli_competenza_category_label($p->ID);
                    $is_tier_1 = (bool) saltelli_field('is_tier_1_focus', $p->ID, false);
                    $lead      = (string) saltelli_field('lead_breve', $p->ID, '');
                    if ($lead === '') {
                        $lead = (string) saltelli_field('answer_capsule', $p->ID, '');
                        if ($lead !== '') $lead = wp_trim_words($lead, 18, '…');
                    }
                    ?>
                    <a class="sl-area<?php echo $is_tier_1 ? ' sl-area--tier1' : ''; ?>"
                       href="<?php echo esc_url(get_permalink($p)); ?>"
                       data-area-num="<?php echo esc_attr($num); ?>">
                        <span class="sl-area__num sl-mono"><?php echo esc_html($num); ?> / <?php echo esc_html(str_pad((string) $total, 2, '0', STR_PAD_LEFT)); ?></span>
                        <span class="sl-area__title"><?php echo esc_html(get_the_title($p)); ?></span>
                        <span class="sl-area__meta sl-mono">
                            <?php echo esc_html($is_tier_1 ? __('Tier 1 · approfondimento', 'saltelli') : ($cat_label ?: __('Approfondisci', 'saltelli'))); ?>
                            <span class="arrow" aria-hidden="true">→</span>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="sl-mono sl-areas__empty"><?php esc_html_e('Nessuna competenza in questa categoria.', 'saltelli'); ?></p>
        <?php endif; ?>

        <div class="sl-areas__more">
            <a class="sl-btn" href="<?php echo esc_url(get_post_type_archive_link('competenza')); ?>">
                <span><?php esc_html_e('Tutte le 19 aree', 'saltelli'); ?></span>
                <span class="arrow" aria-hidden="true">→</span>
            </a>
        </div>

    </div>
</section>

<?php
get_footer();
