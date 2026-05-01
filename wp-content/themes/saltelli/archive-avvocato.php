<?php
/**
 * Template: Archive CPT avvocato.
 * 4 lawyers asimmetrici (riusa pattern .sl-team della homepage).
 *
 * @package Saltelli
 */
get_header();

$avvocati = get_posts([
    'post_type'   => 'avvocato',
    'numberposts' => -1,
    'orderby'     => ['menu_order' => 'ASC', 'date' => 'ASC'],
]);
$layout_team = saltelli_team_grid_layout();
?>

<section class="sl-team sl-team--archive">
    <div class="sl-container">
        <header class="sl-section-head">
            <?php saltelli_render_breadcrumb(); ?>
            <h1 class="sl-section-title">
                <?php esc_html_e('Quattro', 'saltelli'); ?><br>
                <em><?php esc_html_e('professionisti.', 'saltelli'); ?></em>
            </h1>
            <p class="sl-team__archive-lede">
                <?php esc_html_e('Un atelier di quattro avvocati a Chiaia. Ogni cliente è una storia, e ogni storia merita il tempo di essere capita.', 'saltelli'); ?>
            </p>
        </header>

        <?php if (!empty($avvocati)) : ?>
            <div class="sl-team__grid">
                <?php foreach ($avvocati as $i => $av) :
                    $layout = $layout_team[$i] ?? ['col' => 1, 'span' => 12, 'offset' => 0];
                    $ruolo  = (string) saltelli_field('ruolo_breve', $av->ID, '');
                    $specs  = saltelli_get_attorney_specializations($av->ID);
                    $foto   = saltelli_field('foto_ritratto', $av->ID);
                    ?>
                    <article class="sl-team__lawyer"
                             style="--sl-col:<?php echo (int) $layout['col']; ?>; --sl-span:<?php echo (int) $layout['span']; ?>; --sl-offset:<?php echo (int) $layout['offset']; ?>px;">
                        <a class="sl-team__portrait" href="<?php echo esc_url(get_permalink($av)); ?>" aria-label="<?php echo esc_attr(get_the_title($av)); ?>">
                            <?php
                            if (has_post_thumbnail($av->ID)) {
                                echo get_the_post_thumbnail($av->ID, 'saltelli-attorney-portrait', [
                                    'loading'  => 'lazy',
                                    'decoding' => 'async',
                                    'alt'      => esc_attr(get_the_title($av) . ($ruolo ? ' · ' . $ruolo : '')),
                                ]);
                            } elseif (is_array($foto) && !empty($foto['url'])) {
                                /* IMPECCABLE v0.21.0 [perf-T2]: width/height esplicite (CLS prevention) */
                                echo '<img src="' . esc_url($foto['url']) . '" alt="' . esc_attr($foto['alt'] ?: get_the_title($av)) . '" loading="lazy" decoding="async" width="600" height="800">';
                            } else {
                                echo '<span class="sl-team__placeholder" aria-hidden="true"><span class="sl-mono">' . esc_html__('Ritratto · 3:4', 'saltelli') . '</span></span>';
                                echo '<!-- TODO: replace with real Saltelli photo -->';
                            }
                            ?>
                        </a>
                        <?php if ($ruolo) : ?>
                            <div class="sl-mono sl-team__role"><?php echo esc_html($ruolo); ?></div>
                        <?php endif; ?>
                        <h2 class="sl-team__name">
                            <a href="<?php echo esc_url(get_permalink($av)); ?>"><?php echo esc_html(get_the_title($av)); ?></a>
                        </h2>
                        <?php if (!empty($specs)) : ?>
                            <ul class="sl-team__specs">
                                <?php foreach ($specs as $s) : ?>
                                    <li class="sl-tag"><?php echo esc_html($s); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="sl-mono"><?php esc_html_e('Nessun avvocato pubblicato.', 'saltelli'); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();
