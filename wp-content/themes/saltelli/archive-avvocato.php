<?php
/**
 * Template: Archive CPT avvocato — Sessione 2 enriched (v0.33.0).
 *
 * Hero asym 8/4 + lede drop-cap + trust aside + 4 lawyer card grid +
 * § Come lavoriamo (3 principi) + CTA finale dark navy.
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

// Wave 4.7.fix.2 P4: archive header copy editable da SCF tab "Archive Headers".
$sl_arch_eyebrow = (string) saltelli_option('archive_avvocato_eyebrow', __('§ Studio · Avvocati', 'saltelli'));
$sl_arch_h1_main = (string) saltelli_option('archive_avvocato_h1_main', __('Quattro', 'saltelli'));
$sl_arch_h1_em   = (string) saltelli_option('archive_avvocato_h1_emphasis', __('professionisti.', 'saltelli'));
$sl_arch_intro   = (string) saltelli_option('archive_avvocato_intro', __('Un atelier di quattro avvocati a Chiaia. Ogni cliente è una storia, e ogni storia merita il tempo di essere capita.', 'saltelli'));
?>

<article class="sl-team sl-team--archive sl-team--archive-w2">

    <header class="sl-team__archive-hero sl-page-hero sl-page-hero--compact">
        <div>
            <?php saltelli_render_breadcrumb(); ?>
            <div class="sl-mono sl-team__archive-eyebrow" style="margin-bottom: 32px;">
                <?php echo esc_html($sl_arch_eyebrow); ?>
            </div>
            <h1 class="sl-team__archive-h1" data-split-reveal>
                <?php
                $sl_arch_h1 = esc_html($sl_arch_h1_main) . '<br><em>' . esc_html($sl_arch_h1_em) . '</em>';
                echo wp_kses(saltelli_split_h1_words($sl_arch_h1), [
                    'span' => ['class' => true, 'data-i' => true],
                    'em'   => [],
                    'br'   => [],
                ]);
                ?>
            </h1>
            <p class="sl-team__archive-lede"><?php echo esc_html($sl_arch_intro); ?></p>
        </div>
        <aside class="sl-team__archive-trust">
            <div class="sl-mono sl-team__archive-trust-eyebrow">
                <?php echo esc_html(saltelli_option('archive_avvocato_trust_eyebrow', '§ Dal 1999')); ?>
            </div>
            <p class="sl-team__archive-trust-headline">
                <?php echo esc_html(saltelli_option('archive_avvocato_trust_headline_l1', 'Vannella Gaetani, 27.')); ?><br>
                <em><?php echo esc_html(saltelli_option('archive_avvocato_trust_headline_em', 'Chiaia · Napoli.')); ?></em>
            </p>
            <p class="sl-team__archive-trust-text">
                <?php echo esc_html(saltelli_option('archive_avvocato_trust_text', 'Quattro avvocati, una pratica alla volta. Riceviamo solo su appuntamento, in studio o in videocall.')); ?>
            </p>
        </aside>
    </header>

    <?php if (!empty($avvocati)) : ?>
        <section class="sl-container">
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
                            /* Wave 4: first portrait above-fold (LCP candidate) → eager + high priority */
                            $is_first = ($i === 0);
                            $loading_attr = $is_first ? 'eager' : 'lazy';
                            $fetchpri_attr = $is_first ? 'high' : 'auto';
                            if (has_post_thumbnail($av->ID)) {
                                echo get_the_post_thumbnail($av->ID, 'saltelli-attorney-portrait', [
                                    'loading'       => $loading_attr,
                                    'decoding'      => 'async',
                                    'fetchpriority' => $fetchpri_attr,
                                    'alt'           => esc_attr(get_the_title($av) . ($ruolo ? ' · ' . $ruolo : '')),
                                ]);
                            } elseif (is_array($foto) && !empty($foto['url'])) {
                                echo '<img src="' . esc_url($foto['url']) . '" alt="' . esc_attr($foto['alt'] ?: get_the_title($av)) . '" loading="' . esc_attr($loading_attr) . '" decoding="async" fetchpriority="' . esc_attr($fetchpri_attr) . '" width="600" height="800">';
                            } else {
                                echo '<span class="sl-team__placeholder" aria-hidden="true"><span class="sl-mono">' . esc_html__('Ritratto · 3:4', 'saltelli') . '</span></span>';
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
        </section>
    <?php else : ?>
        <p class="sl-mono"><?php esc_html_e('Nessun avvocato pubblicato.', 'saltelli'); ?></p>
    <?php endif; ?>

    <?php /* § Come lavoriamo — 3 principi (riusa pattern chi-siamo) */ ?>
    <section class="sl-team__archive-principles">
        <div class="sl-team__archive-principles-inner">
            <div class="sl-mono"><?php echo esc_html(saltelli_option('archive_avvocato_principles_eyebrow', '§ Come lavoriamo')); ?></div>
            <div>
                <ol class="sl-team__archive-principles-list" role="list">
                    <li class="sl-team__archive-principle">
                        <span class="sl-team__archive-principle-num">01</span>
                        <div>
                            <h3 class="sl-team__archive-principle-t"><?php esc_html_e('Ascoltiamo prima.', 'saltelli'); ?></h3>
                            <p class="sl-team__archive-principle-d"><?php esc_html_e('Il primo incontro è gratuito e dura il tempo necessario. Capire la storia viene sempre prima delle carte.', 'saltelli'); ?></p>
                        </div>
                    </li>
                    <li class="sl-team__archive-principle">
                        <span class="sl-team__archive-principle-num">02</span>
                        <div>
                            <h3 class="sl-team__archive-principle-t"><?php esc_html_e('Lavoriamo in atelier.', 'saltelli'); ?></h3>
                            <p class="sl-team__archive-principle-d"><?php esc_html_e('Ogni pratica è seguita personalmente da uno dei quattro avvocati. Niente call center, niente passaggi.', 'saltelli'); ?></p>
                        </div>
                    </li>
                    <li class="sl-team__archive-principle">
                        <span class="sl-team__archive-principle-num">03</span>
                        <div>
                            <h3 class="sl-team__archive-principle-t"><?php esc_html_e('Diciamo la verità.', 'saltelli'); ?></h3>
                            <p class="sl-team__archive-principle-d"><?php esc_html_e('Anche quando significa sconsigliare un\'azione legale. La nostra reputazione vale più di un mandato.', 'saltelli'); ?></p>
                        </div>
                    </li>
                </ol>
            </div>
        </div>
    </section>

    <?php /* Elena fix 2026-05-14: rimossa <section sl-info-page__cta> "§ Pronto?
       Vuoi raccontarci la tua pratica?" dark navy — ridondante con footer
       pre-CTA "§ Contattaci" cross-page. Stesso fix applicato in page-faq.php
       e page-info-shared.php. CSS .sl-info-page__cta* resta orphan (cleanup
       target Wave 6.1). */ ?>

</article>

<?php
get_footer();
