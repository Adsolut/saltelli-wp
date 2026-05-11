<?php
/**
 * Template: Single CPT Competenza.
 * Branch su is_tier_1 (Wave 1 ACF schema canonico) per profondità contenuto.
 *
 * @package Saltelli
 */
get_header();

while (have_posts()) :
    the_post();
    $post_id    = get_the_ID();
    // Wave 4.6: rimosso fallback is_tier_1_focus legacy (rinominato in is_tier_1
    // dal Wave 1). Tutti i contenuti sono migrati al campo canonico.
    $is_tier_1  = (bool) saltelli_field('is_tier_1', $post_id, false);
    $answer     = (string) saltelli_field('answer_capsule', $post_id, '');
    $body_ext   = (string) saltelli_field('body_extended', $post_id, '');
    $faq        = saltelli_field('faq', $post_id, []);
    $casi       = saltelli_field('casi_rappresentativi', $post_id, []);
    $cta_label  = (string) saltelli_field('cta_label', $post_id, __('Parlane con i nostri avvocati', 'saltelli'));
    $cta_url    = (string) saltelli_field('cta_url', $post_id, '/contatti/');
    // Wave 6 — CTA progressive (ghost top + primary middle, default fallback al cta_label/url generico)
    $cta_top_label    = (string) saltelli_field('cta_top_label', $post_id, '');
    $cta_top_url      = (string) saltelli_field('cta_top_url', $post_id, '');
    $cta_middle_label = (string) saltelli_field('cta_middle_label', $post_id, '');
    $cta_middle_url   = (string) saltelli_field('cta_middle_url', $post_id, '');
    if ($cta_middle_label === '') $cta_middle_label = $cta_label;
    if ($cta_middle_url === '')   $cta_middle_url   = $cta_url;
    $lead_atts  = saltelli_get_attorneys_for_competenza($post_id);
    $articoli   = saltelli_field('articoli_correlati', $post_id, []);
    $cat_label  = saltelli_competenza_category_label($post_id);
    $tier_label    = $is_tier_1 ? __('Tier 1 · approfondimento', 'saltelli') : __('Area di pratica', 'saltelli');
    $tier1_subtitle = (string) saltelli_field('subtitle', $post_id, '');
    $tier1_class   = $is_tier_1 ? 'sl-tier1 ' : '';
    // Bugfix: "uno o l'altro" — la sezione __body (body_extended SCF) prevale su
    // __intro (post_content). Mai entrambe renderizzate insieme.
    $render_extended_body = $is_tier_1 && $body_ext !== '';
    ?>
    <article <?php post_class($tier1_class . 'sl-competenza sl-competenza--' . ($is_tier_1 ? 'tier-1' : 'tier-2')); ?>>

        <header class="sl-competenza__hero sl-page-hero <?php echo $is_tier_1 ? 'sl-tier1__hero' : ''; ?>">
            <div class="sl-container">
                <?php saltelli_render_breadcrumb(); ?>

                <a class="sl-mono sl-competenza__back" href="<?php echo esc_url(saltelli_aree_hub_url()); ?>">
                    ← <?php esc_html_e('Tutte le aree', 'saltelli'); ?>
                </a>

                <div class="sl-mono sl-competenza__eyebrow <?php echo $is_tier_1 ? 'sl-tier1__eyebrow' : ''; ?>">
                    <?php echo esc_html($tier_label); ?><?php echo $cat_label ? ' · ' . esc_html($cat_label) : ''; ?>
                </div>

                <h1 class="sl-competenza__title <?php echo $is_tier_1 ? 'sl-tier1__h1' : ''; ?>" data-split-reveal><?php echo wp_kses(saltelli_split_h1_words(get_the_title()), ['span' => ['class' => true, 'data-i' => true]]); ?></h1>

                <?php if ($is_tier_1 && $tier1_subtitle !== '') : ?>
                    <p class="sl-tier1__sub"><?php echo esc_html($tier1_subtitle); ?></p>
                <?php endif; ?>

                <?php if ($answer !== '') : ?>
                    <div class="sl-competenza__answer-wrap <?php echo $is_tier_1 ? 'sl-tier1__capsule' : ''; ?>">
                        <div class="sl-mono sl-competenza__answer-eyebrow"><?php esc_html_e('Risposta in 50 parole', 'saltelli'); ?></div>
                        <p class="sl-competenza__answer <?php echo $is_tier_1 ? 'sl-tier1__capsule-text' : ''; ?>"><?php echo esc_html($answer); ?></p>
                    </div>
                <?php else : ?>
                    <!-- TODO: answer capsule da Elena (40-60 parole) -->
                <?php endif; ?>

                <?php /* Wave 6 — CTA progressive top (ghost) sotto answer capsule */ ?>
                <?php if ($cta_top_label !== '' && $cta_top_url !== '') : ?>
                    <div class="sl-competenza__cta-top">
                        <a class="sl-btn sl-btn--ghost" href="<?php echo esc_url($cta_top_url); ?>">
                            <span><?php echo esc_html($cta_top_label); ?></span>
                            <span class="arrow" aria-hidden="true">→</span>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="sl-competenza__hero-cta">
                    <a class="sl-btn sl-btn--primary" href="<?php echo esc_url($cta_url); ?>">
                        <span><?php echo esc_html($cta_label); ?></span>
                        <span class="arrow" aria-hidden="true">→</span>
                    </a>
                </div>

                <?php
                // === v0.24.0 TASK 4 — Tier-1 "Avvocato di riferimento" card ===
                // Mapping fisso: tributario→Emiliano, lavoro→Fabiana, lgbtq→Antonia.
                // Source: saltelli-s2-practice-tier1.jsx (referente block).
                if ($is_tier_1) :
                    $sl_t1_slug = get_post_field('post_name', $post_id);
                    $sl_t1_lawyer_map = [
                        'diritto-tributario'         => 'emiliano-saltelli',
                        'diritto-del-lavoro'         => 'fabiana-saltelli',
                        'diritto-di-famiglia-lgbtq'  => 'antonia-battista',
                    ];
                    $sl_t1_lawyer_slug = $sl_t1_lawyer_map[$sl_t1_slug] ?? null;
                    if ($sl_t1_lawyer_slug) :
                        $sl_t1_lawyer = get_page_by_path($sl_t1_lawyer_slug, OBJECT, 'avvocato');
                        if ($sl_t1_lawyer) :
                            $sl_t1_lawyer_id    = (int) $sl_t1_lawyer->ID;
                            $sl_t1_lawyer_title = get_the_title($sl_t1_lawyer_id);
                            $sl_t1_lawyer_role  = (string) saltelli_field('ruolo_breve', $sl_t1_lawyer_id, '');
                            $sl_t1_lawyer_photo = get_the_post_thumbnail_url($sl_t1_lawyer_id, 'saltelli-attorney-square');
                            if (!$sl_t1_lawyer_photo) {
                                $sl_t1_foto = saltelli_field('foto_ritratto', $sl_t1_lawyer_id);
                                if (is_array($sl_t1_foto) && !empty($sl_t1_foto['url'])) {
                                    $sl_t1_lawyer_photo = $sl_t1_foto['url'];
                                }
                            }
                ?>
                <aside class="sl-tier1__lawyer" aria-label="<?php esc_attr_e('Avvocato di riferimento', 'saltelli'); ?>" data-reveal>
                    <div class="sl-mono sl-tier1__lawyer-eyebrow"><?php esc_html_e('§ Avvocato di riferimento', 'saltelli'); ?></div>
                    <a href="<?php echo esc_url(get_permalink($sl_t1_lawyer_id)); ?>" class="sl-tier1__lawyer-card">
                        <?php if ($sl_t1_lawyer_photo) : ?>
                            <img
                                src="<?php echo esc_url($sl_t1_lawyer_photo); ?>"
                                alt="<?php echo esc_attr($sl_t1_lawyer_title); ?>"
                                class="sl-tier1__lawyer-photo"
                                width="80" height="80"
                                loading="lazy" decoding="async">
                        <?php else : ?>
                            <span class="sl-tier1__lawyer-placeholder" aria-hidden="true">
                                <?php echo esc_html(mb_strtoupper(mb_substr($sl_t1_lawyer_title, 0, 1))); ?>
                            </span>
                        <?php endif; ?>
                        <div class="sl-tier1__lawyer-info">
                            <h3 class="sl-tier1__lawyer-name"><?php echo esc_html($sl_t1_lawyer_title); ?></h3>
                            <?php if ($sl_t1_lawyer_role !== '') : ?>
                                <p class="sl-tier1__lawyer-role sl-mono"><?php echo esc_html($sl_t1_lawyer_role); ?></p>
                            <?php endif; ?>
                            <span class="sl-tier1__lawyer-link"><?php esc_html_e('Vai alla scheda →', 'saltelli'); ?></span>
                        </div>
                    </a>
                </aside>
                <?php
                        endif;
                    endif;
                endif;
                ?>
            </div>
        </header>

        <?php if (! $render_extended_body && get_the_content()) : // fallback su post_content solo se la sezione __body (body_extended SCF) non renderizza ?>
            <section class="sl-competenza__intro <?php echo $is_tier_1 ? 'sl-tier1__body' : ''; ?>">
                <div class="sl-container">
                    <div class="sl-competenza__prose"><?php the_content(); ?></div>
                </div>
            </section>
        <?php endif; ?>

        <?php
        // === v0.25.0 T2 — Tier-1 deep cluster H2 (3 cluster × tier-1) ===
        // Source: helper saltelli_tier1_clusters() · paragraphs GEO-rich 200-300 parole.
        // Inserito DOPO the_content() per arricchimento topical authority.
        if ($is_tier_1) :
            $sl_t1_slug_cluster = get_post_field('post_name', $post_id);
            $sl_t1_clusters = saltelli_tier1_clusters($sl_t1_slug_cluster);
            if (!empty($sl_t1_clusters)) :
        ?>
            <section class="sl-tier1__clusters sl-tier1__body" aria-label="<?php esc_attr_e('Approfondimenti tematici', 'saltelli'); ?>">
                <div class="sl-container">
                    <?php foreach ($sl_t1_clusters as $sl_idx_c => $sl_cluster) :
                        $sl_cluster_id = 'tier1-cluster-' . (int) $sl_idx_c;
                    ?>
                        <article class="sl-tier1__cluster" data-reveal aria-labelledby="<?php echo esc_attr($sl_cluster_id); ?>">
                            <h2 class="sl-tier1__cluster-h2" id="<?php echo esc_attr($sl_cluster_id); ?>">
                                <?php echo esc_html($sl_cluster['h2']); ?>
                            </h2>
                            <?php foreach ($sl_cluster['paragraphs'] as $sl_par) : ?>
                                <p class="sl-tier1__cluster-p"><?php echo esc_html($sl_par); ?></p>
                            <?php endforeach; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php
            endif;
        endif;
        ?>

        <?php if ($render_extended_body) : // sezione canonica del corpo editoriale, prevale su post_content ?>
            <section class="sl-competenza__body sl-tier1__body">
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
                                        /* IMPECCABLE v0.21.0 [perf-T2]: width/height esplicite (CLS prevention) */
                                        echo '<img src="' . esc_url($foto['url']) . '" alt="' . esc_attr($foto['alt'] ?: get_the_title($av)) . '" loading="lazy" decoding="async" width="600" height="600">';
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
            <section class="sl-competenza__casi sl-tier1__cases" aria-labelledby="comp-casi-h">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Tre vittorie recenti', 'saltelli'); ?></div>
                    <h2 class="sl-section-title" id="comp-casi-h"><?php esc_html_e('Tre vittorie recenti.', 'saltelli'); ?></h2>
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

        <?php /* Wave 6 — CTA progressive middle (primary), prima della FAQ */ ?>
        <?php if ($cta_middle_label !== '' && $cta_middle_url !== '') : ?>
            <section class="sl-competenza__cta-middle" aria-label="<?php esc_attr_e('Parla con noi', 'saltelli'); ?>">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Pronto a iniziare?', 'saltelli'); ?></div>
                    <a class="sl-btn sl-btn--primary" href="<?php echo esc_url($cta_middle_url); ?>">
                        <span><?php echo esc_html($cta_middle_label); ?></span>
                        <span class="arrow" aria-hidden="true">→</span>
                    </a>
                </div>
            </section>
        <?php endif; ?>

        <?php
        // Wave 6 — Normalizza FAQ: supporta legacy rows fake-repeater + Wave 1+ post_object (saltelli_faq CPT)
        $valid_faq = [];
        if (is_array($faq) && !empty($faq)) {
            foreach ($faq as $sl_faq_row) {
                if (is_array($sl_faq_row)) {
                    if (!empty($sl_faq_row['domanda']) && !empty($sl_faq_row['risposta'])) {
                        $valid_faq[] = [
                            'domanda'  => (string) $sl_faq_row['domanda'],
                            'risposta' => (string) $sl_faq_row['risposta'],
                        ];
                    }
                    continue;
                }
                $sl_faq_id = is_object($sl_faq_row) && isset($sl_faq_row->ID) ? (int) $sl_faq_row->ID
                           : (is_numeric($sl_faq_row) ? (int) $sl_faq_row : 0);
                if (!$sl_faq_id) continue;
                $sl_faq_q = get_the_title($sl_faq_id);
                $sl_faq_a = (string) saltelli_field('risposta', $sl_faq_id, '');
                if ($sl_faq_q !== '' && $sl_faq_a !== '') {
                    $valid_faq[] = ['domanda' => $sl_faq_q, 'risposta' => $sl_faq_a];
                }
            }
        }
        if (!empty($valid_faq)) : ?>
                <section class="sl-competenza__faq <?php echo $is_tier_1 ? 'sl-tier1__faq' : ''; ?>" aria-labelledby="comp-faq-h">
                    <div class="sl-container">
                        <div class="sl-mono">§ <?php esc_html_e('Domande frequenti', 'saltelli'); ?></div>
                        <h2 class="sl-section-title" id="comp-faq-h"><?php esc_html_e('Cinque domande frequenti.', 'saltelli'); ?></h2>
                        <div class="sl-acc" data-sl-acc>
                            <?php foreach ($valid_faq as $i => $row) : ?>
                                <div class="sl-acc__item" data-open="<?php echo $i === 0 ? 'true' : 'false'; ?>">
                                    <button class="sl-acc__btn" type="button" aria-expanded="<?php echo $i === 0 ? 'true' : 'false'; ?>" aria-controls="comp-faq-panel-<?php echo (int) $i; ?>">
                                        <span><?php echo esc_html($row['domanda']); ?></span>
                                        <span class="sl-acc__icon" aria-hidden="true">+</span>
                                    </button>
                                    <div class="sl-acc__panel" id="comp-faq-panel-<?php echo (int) $i; ?>">
                                        <div class="sl-acc__inner">
                                            <?php echo wp_kses_post(wpautop($row['risposta'])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

        <?php
        // === Wave 6 Pattern 10 — Related services (Aree correlate) ===
        // Manual: ACF related_competenze. Auto-fallback: 3 random stesso cluster (tassonomia tipo-area).
        $sl_related_ids = saltelli_field('related_competenze', $post_id, []);
        if (!is_array($sl_related_ids)) {
            $sl_related_ids = [];
        }
        // Normalize: object → ID
        $sl_related_ids = array_map(function ($r) {
            if (is_object($r) && isset($r->ID)) return (int) $r->ID;
            return (int) $r;
        }, $sl_related_ids);
        $sl_related_ids = array_values(array_filter($sl_related_ids, 'intval'));

        if (empty($sl_related_ids)) {
            // Auto-fallback su tassonomia tipo-area
            $sl_current_terms = wp_get_object_terms($post_id, 'tipo-area', ['fields' => 'ids']);
            if (!is_wp_error($sl_current_terms) && !empty($sl_current_terms)) {
                $sl_rel_q = new WP_Query([
                    'post_type'      => 'competenza',
                    'posts_per_page' => 3,
                    'post__not_in'   => [$post_id],
                    'no_found_rows'  => true,
                    'fields'         => 'ids',
                    'orderby'        => 'rand',
                    'tax_query'      => [[
                        'taxonomy' => 'tipo-area',
                        'field'    => 'term_id',
                        'terms'    => $sl_current_terms,
                    ]],
                ]);
                $sl_related_ids = $sl_rel_q->posts;
            }
        }
        $sl_related_ids = array_slice($sl_related_ids, 0, 3);
        if (!empty($sl_related_ids)) :
        ?>
            <section class="sl-related-services" aria-labelledby="comp-rel-h">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Aree correlate', 'saltelli'); ?></div>
                    <h2 class="sl-section-title" id="comp-rel-h"><?php esc_html_e('Approfondisci.', 'saltelli'); ?></h2>
                    <div class="sl-area-list">
                        <?php
                        $sl_rel_total = count($sl_related_ids);
                        foreach ($sl_related_ids as $sl_rel_idx => $sl_rel_id) :
                            $sl_rel_id = (int) $sl_rel_id;
                            if (!$sl_rel_id) continue;
                            $sl_rel_url   = get_permalink($sl_rel_id);
                            $sl_rel_title = get_the_title($sl_rel_id);
                        ?>
                            <a class="sl-area sl-area--related" href="<?php echo esc_url($sl_rel_url); ?>">
                                <span class="sl-area__num"><?php echo esc_html(sprintf('%02d', $sl_rel_idx + 1)); ?></span>
                                <span class="sl-area__title"><?php echo esc_html($sl_rel_title); ?></span>
                                <span class="sl-area__meta" aria-hidden="true">→</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($articoli)) : ?>
            <section class="sl-competenza__articoli <?php echo $is_tier_1 ? 'sl-tier1__related' : ''; ?>" aria-labelledby="comp-art-h">
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

        <?php /* Wave 6 Pattern 4 — Mini-form contestuale, prima della CTA finale */ ?>
        <section class="sl-competenza__mini-form-wrap">
            <div class="sl-container">
                <?php
                $sl_mini_topic = get_post_field('post_name', $post_id);
                $sl_mini_title = sprintf(
                    /* translators: %s = titolo competenza */
                    __('Hai una domanda su %s?', 'saltelli'),
                    get_the_title($post_id)
                );
                get_template_part('template-parts/mini-form', null, [
                    'topic_default' => $sl_mini_topic,
                    'title'         => $sl_mini_title,
                ]);
                ?>
            </div>
        </section>

        <section class="sl-competenza__cta <?php echo $is_tier_1 ? 'sl-tier1__cta-final' : ''; ?>">
            <div class="sl-container">
                <div class="sl-mono">§ <?php esc_html_e('Pronto?', 'saltelli'); ?></div>
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
