<?php
/**
 * Template: Archive CPT competenza.
 * Lista 19 aree con filtro pillole tassonomia tipo-area.
 *
 * @package Saltelli
 */
get_header();

$competenze = get_posts([
    'post_type'   => 'competenza',
    'numberposts' => -1,
    'meta_key'    => 'is_tier_1_focus',
    'orderby'     => [
        'meta_value_num' => 'DESC',
        'menu_order'     => 'ASC',
        'title'          => 'ASC',
    ],
]);

$tipo_terms = get_terms([
    'taxonomy'   => 'tipo-area',
    'hide_empty' => false,
    'orderby'    => 'count',
    'order'      => 'DESC',
]);
?>

<section class="sl-areas sl-areas--archive">
    <div class="sl-container">

        <header class="sl-section-head sl-areas__archive-head">
            <?php saltelli_render_breadcrumb(); ?>
            <h1 class="sl-section-title">
                <?php esc_html_e('Diciannove aree.', 'saltelli'); ?><br>
                <em><?php esc_html_e('Tre presidiate in profondità.', 'saltelli'); ?></em>
            </h1>
            <p class="sl-areas__archive-lede">
                <?php esc_html_e('Lavoriamo in profondità su tributario, lavoro e famiglia LGBTQ+. Le altre sedici aree mantengono presidio attivo per famiglie e imprese di Napoli.', 'saltelli'); ?>
            </p>
        </header>

        <?php if (!empty($tipo_terms)) : ?>
            <div class="sl-areas__filters" role="tablist">
                <button class="sl-areas__filter sl-mono is-active" type="button" data-filter="*" aria-pressed="true"><?php esc_html_e('Tutte', 'saltelli'); ?></button>
                <?php foreach ($tipo_terms as $term) : ?>
                    <button class="sl-areas__filter sl-mono" type="button" data-filter="<?php echo esc_attr($term->slug); ?>" aria-pressed="false"><?php echo esc_html($term->name); ?></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($competenze)) : ?>
            <div class="sl-areas__list">
                <?php
                $i = 0;
                $total = count($competenze);
                foreach ($competenze as $p) :
                    $i++;
                    $num       = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                    $cat_slug  = saltelli_competenza_category_slug($p->ID);
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
                       data-area-num="<?php echo esc_attr($num); ?>"
                       data-area-cat="<?php echo esc_attr($cat_slug); ?>"
                       data-area-lead="<?php echo esc_attr($lead); ?>">
                        <span class="sl-area__num sl-mono"><?php echo esc_html($num); ?> / <?php echo esc_html(str_pad((string) $total, 2, '0', STR_PAD_LEFT)); ?></span>
                        <span class="sl-area__title"><?php echo esc_html(get_the_title($p)); ?></span>
                        <span class="sl-area__meta sl-mono">
                            <?php echo esc_html($is_tier_1 ? __('Tier 1 · approfondimento', 'saltelli') : ($cat_label ?: __('Tier 2', 'saltelli'))); ?>
                            <span class="arrow" aria-hidden="true">→</span>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="sl-mono"><?php esc_html_e('Nessuna area di competenza pubblicata.', 'saltelli'); ?></p>
        <?php endif; ?>

    </div>
</section>

<?php
get_footer();
