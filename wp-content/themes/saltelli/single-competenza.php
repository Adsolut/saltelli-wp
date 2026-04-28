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
    ?>
    <article class="competenza competenza--<?php echo $is_tier_1 ? 'tier-1' : 'tier-2'; ?>">

        <!-- TODO Style & Animation agent: hero section (H1 + answer capsule prominent) -->
        <header class="competenza__hero container">
            <h1><?php the_title(); ?></h1>
            <?php if ($answer !== '') : ?>
                <p class="answer-capsule"><?php echo esc_html($answer); ?></p>
            <?php else : ?>
                <!-- TODO: answer capsule da Elena (40-60 parole) -->
            <?php endif; ?>
        </header>

        <!-- Body principale: sempre dal post_content (the_content) -->
        <section class="competenza__content container">
            <?php the_content(); ?>
        </section>

        <!-- TODO Style & Animation agent: body extended (solo tier-1) -->
        <?php if ($is_tier_1 && $body_ext !== '') : ?>
            <section class="competenza__body container">
                <?php echo wp_kses_post($body_ext); ?>
            </section>
        <?php endif; ?>

        <!-- TODO Style & Animation agent: lead attorneys section (avatar + nome + ruolo) -->
        <?php if (!empty($lead_atts)) : ?>
            <section class="competenza__avvocati container" aria-labelledby="competenza-avvocati-h">
                <h2 id="competenza-avvocati-h"><?php esc_html_e('Avvocati referenti', 'saltelli'); ?></h2>
                <ul>
                    <?php foreach ($lead_atts as $av) : ?>
                        <li>
                            <a href="<?php echo esc_url(get_permalink($av)); ?>"><?php echo esc_html(get_the_title($av)); ?></a>
                            <?php
                            $r = saltelli_field('ruolo_breve', $av->ID, '');
                            if ($r) {
                                echo ' — <span class="avvocato-ruolo">' . esc_html($r) . '</span>';
                            }
                            ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <!-- TODO Style & Animation agent: FAQ accordion (schema FAQPage iniettato dal partial) -->
        <?php if (is_array($faq) && !empty($faq)) : ?>
            <section class="competenza__faq container" aria-labelledby="competenza-faq-h">
                <h2 id="competenza-faq-h"><?php esc_html_e('Domande frequenti', 'saltelli'); ?></h2>
                <div class="faq-list">
                    <?php foreach ($faq as $row) :
                        if (empty($row['domanda']) || empty($row['risposta'])) {
                            continue;
                        }
                        ?>
                        <details class="faq-item">
                            <summary><?php echo esc_html($row['domanda']); ?></summary>
                            <p><?php echo esc_html($row['risposta']); ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- TODO Style & Animation agent: casi rappresentativi (solo tier-1) -->
        <?php if ($is_tier_1 && is_array($casi) && !empty($casi)) : ?>
            <section class="competenza__casi container" aria-labelledby="competenza-casi-h">
                <h2 id="competenza-casi-h"><?php esc_html_e('Casi rappresentativi', 'saltelli'); ?></h2>
                <ul>
                    <?php foreach ($casi as $caso) :
                        if (empty($caso['titolo']) || empty($caso['descrizione_anonimizzata'])) {
                            continue;
                        }
                        ?>
                        <li>
                            <h3><?php echo esc_html($caso['titolo']); ?></h3>
                            <p><?php echo esc_html($caso['descrizione_anonimizzata']); ?></p>
                            <?php if (!empty($caso['esito'])) : ?>
                                <p class="caso-esito"><strong><?php esc_html_e('Esito:', 'saltelli'); ?></strong> <?php echo esc_html($caso['esito']); ?></p>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <!-- TODO Style & Animation agent: CTA + articoli correlati cluster -->
        <section class="competenza__cta container">
            <a class="btn btn--primary" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_label); ?></a>
        </section>

        <?php if (!empty($articoli)) : ?>
            <section class="competenza__articoli container" aria-labelledby="competenza-articoli-h">
                <h2 id="competenza-articoli-h"><?php esc_html_e('Approfondimenti correlati', 'saltelli'); ?></h2>
                <ul>
                    <?php
                    $ids = is_array($articoli) ? $articoli : [$articoli];
                    foreach ($ids as $aid) :
                        $aid = is_object($aid) ? (int) $aid->ID : (int) $aid;
                        if (!$aid) {
                            continue;
                        }
                        ?>
                        <li><a href="<?php echo esc_url(get_permalink($aid)); ?>"><?php echo esc_html(get_the_title($aid)); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

    </article>
    <?php
endwhile;

get_footer();
