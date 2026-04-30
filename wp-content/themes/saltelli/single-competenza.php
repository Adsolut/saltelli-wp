<?php
/**
 * Template: Single CPT Competenza.
 * Branch su is_tier_1_focus per profondità contenuto.
 *
 * @package Saltelli
 */
get_header();

while (have_posts()) :
    the_post();
    $post_id    = get_the_ID();
    $is_tier_1  = (bool) saltelli_field('is_tier_1_focus', $post_id, false);
    $answer     = (string) saltelli_field('answer_capsule', $post_id, '');
    $body_ext   = (string) saltelli_field('body_extended', $post_id, '');
    $faq        = saltelli_field('faq', $post_id, []);
    $casi       = saltelli_field('casi_rappresentativi', $post_id, []);
    $cta_label  = (string) saltelli_field('cta_label', $post_id, __('Parlane con i nostri avvocati', 'saltelli'));
    $cta_url    = (string) saltelli_field('cta_url', $post_id, '/contatti/');
    $lead_atts  = saltelli_get_attorneys_for_competenza($post_id);
    $articoli   = saltelli_field('articoli_correlati', $post_id, []);
    $cat_label  = saltelli_competenza_category_label($post_id);
    $tier_label = $is_tier_1 ? __('Tier 1 · approfondimento', 'saltelli') : __('Area di pratica', 'saltelli');
    ?>
    <article <?php post_class('sl-competenza sl-competenza--' . ($is_tier_1 ? 'tier-1' : 'tier-2')); ?>>

        <header class="sl-competenza__hero">
            <div class="sl-container">
                <a class="sl-mono sl-competenza__back" href="<?php echo esc_url(get_post_type_archive_link('competenza')); ?>">
                    ← <?php esc_html_e('Tutte le aree', 'saltelli'); ?>
                </a>

                <div class="sl-mono sl-competenza__eyebrow">
                    <?php echo esc_html($tier_label); ?><?php echo $cat_label ? ' · ' . esc_html($cat_label) : ''; ?>
                </div>

                <h1 class="sl-competenza__title"><?php the_title(); ?></h1>

                <?php if ($answer !== '') : ?>
                    <p class="sl-competenza__answer"><?php echo esc_html($answer); ?></p>
                <?php else : ?>
                    <!-- TODO: answer capsule da Elena (40-60 parole) -->
                <?php endif; ?>

                <div class="sl-competenza__hero-cta">
                    <a class="sl-btn sl-btn--primary" href="<?php echo esc_url($cta_url); ?>">
                        <span><?php echo esc_html($cta_label); ?></span>
                        <span class="arrow" aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </header>

        <?php if (get_the_content()) : ?>
            <section class="sl-competenza__intro">
                <div class="sl-container">
                    <div class="sl-competenza__prose"><?php the_content(); ?></div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($is_tier_1 && $body_ext !== '') : ?>
            <section class="sl-competenza__body">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Approfondimento', 'saltelli'); ?></div>
                    <div class="sl-competenza__prose sl-competenza__prose--extended" data-toc-source>
                        <?php echo wp_kses_post($body_ext); ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($lead_atts)) : ?>
            <section class="sl-competenza__avvocati" aria-labelledby="comp-avv-h">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Referenti', 'saltelli'); ?></div>
                    <h2 class="sl-section-title" id="comp-avv-h"><?php esc_html_e('Avvocati referenti', 'saltelli'); ?></h2>
                    <ul class="sl-competenza__lead-list">
                        <?php foreach ($lead_atts as $av) :
                            $ruolo = (string) saltelli_field('ruolo_breve', $av->ID, '');
                            $foto  = saltelli_field('foto_ritratto', $av->ID);
                            ?>
                            <li class="sl-competenza__lead">
                                <a class="sl-team__portrait sl-competenza__lead-portrait" href="<?php echo esc_url(get_permalink($av)); ?>" aria-label="<?php echo esc_attr(get_the_title($av)); ?>">
                                    <?php
                                    if (has_post_thumbnail($av->ID)) {
                                        echo get_the_post_thumbnail($av->ID, 'saltelli-attorney-square', [
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                            'alt'      => esc_attr(get_the_title($av)),
                                        ]);
                                    } elseif (is_array($foto) && !empty($foto['url'])) {
                                        echo '<img src="' . esc_url($foto['url']) . '" alt="' . esc_attr($foto['alt'] ?: get_the_title($av)) . '" loading="lazy" decoding="async">';
                                    } else {
                                        echo '<span class="sl-team__placeholder" aria-hidden="true"></span>';
                                    }
                                    ?>
                                </a>
                                <?php if ($ruolo) : ?>
                                    <div class="sl-mono"><?php echo esc_html($ruolo); ?></div>
                                <?php endif; ?>
                                <h3 class="sl-team__name">
                                    <a href="<?php echo esc_url(get_permalink($av)); ?>"><?php echo esc_html(get_the_title($av)); ?></a>
                                </h3>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($is_tier_1 && is_array($casi) && !empty($casi)) : ?>
            <section class="sl-competenza__casi" aria-labelledby="comp-casi-h">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Casi rappresentativi', 'saltelli'); ?></div>
                    <h2 class="sl-section-title" id="comp-casi-h"><?php esc_html_e('Casi rappresentativi', 'saltelli'); ?></h2>
                    <ol class="sl-cases__list" role="list">
                        <?php foreach ($casi as $caso) :
                            if (empty($caso['titolo']) || empty($caso['descrizione_anonimizzata'])) continue;
                            ?>
                            <li class="sl-cases__row">
                                <span class="sl-mono sl-cases__id"><?php echo esc_html($caso['titolo']); ?></span>
                                <p class="sl-cases__desc"><?php echo esc_html($caso['descrizione_anonimizzata']); ?></p>
                                <?php if (!empty($caso['esito'])) : ?>
                                    <span class="sl-cases__outcome"><?php echo esc_html($caso['esito']); ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </section>
        <?php endif; ?>

        <?php if (is_array($faq) && !empty($faq)) :
            $valid_faq = array_filter($faq, static function ($r) {
                return !empty($r['domanda']) && !empty($r['risposta']);
            });
            if (!empty($valid_faq)) : ?>
                <section class="sl-competenza__faq" aria-labelledby="comp-faq-h">
                    <div class="sl-container">
                        <div class="sl-mono">§ <?php esc_html_e('Domande frequenti', 'saltelli'); ?></div>
                        <h2 class="sl-section-title" id="comp-faq-h"><?php esc_html_e('Domande frequenti', 'saltelli'); ?></h2>
                        <div class="sl-faq">
                            <?php foreach ($valid_faq as $row) : ?>
                                <details class="sl-faq__item">
                                    <summary class="sl-faq__question"><?php echo esc_html($row['domanda']); ?></summary>
                                    <div class="sl-faq__answer"><?php echo wp_kses_post(wpautop($row['risposta'])); ?></div>
                                </details>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif;
        endif; ?>

        <?php if (!empty($articoli)) : ?>
            <section class="sl-competenza__articoli" aria-labelledby="comp-art-h">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Editoriale', 'saltelli'); ?></div>
                    <h2 class="sl-section-title" id="comp-art-h"><?php esc_html_e('Approfondimenti correlati', 'saltelli'); ?></h2>
                    <ul class="sl-articles">
                        <?php
                        $ids = is_array($articoli) ? $articoli : [$articoli];
                        foreach ($ids as $aid) :
                            $aid = is_object($aid) ? (int) $aid->ID : (int) $aid;
                            if (!$aid) continue;
                            ?>
                            <li class="sl-articles__item">
                                <a href="<?php echo esc_url(get_permalink($aid)); ?>">
                                    <span class="sl-mono"><?php echo esc_html(get_the_date('', $aid)); ?></span>
                                    <span class="sl-articles__title"><?php echo esc_html(get_the_title($aid)); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        <?php endif; ?>

        <section class="sl-competenza__cta">
            <div class="sl-container">
                <h2 class="sl-section-title">
                    <?php esc_html_e('Hai una pratica simile?', 'saltelli'); ?>
                </h2>
                <a class="sl-btn sl-btn--primary" href="<?php echo esc_url($cta_url); ?>">
                    <span><?php echo esc_html($cta_label); ?></span>
                    <span class="arrow" aria-hidden="true">→</span>
                </a>
                <div class="sl-mono sl-competenza__cta-note">
                    <?php esc_html_e('Prima consulenza conoscitiva gratuita · Risposta entro 24 ore · In studio o online', 'saltelli'); ?>
                </div>
            </div>
        </section>

    </article>
    <?php
endwhile;

get_footer();
