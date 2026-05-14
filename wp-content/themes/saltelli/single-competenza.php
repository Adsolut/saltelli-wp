<?php
/**
 * Template: Single CPT Competenza.
 *
 * Wave Elena FB Batch 2 — #23 layout unify (2026-05-12)
 * ────────────────────────────────────────────────────
 * Tutte le 19 competenze passano dalla STESSA struttura editoriale.
 * Differenze tier-1 vs tier-2 sono CSS-driven via modifier `.sl-competenza--tier-1`
 * / `.sl-competenza--tier-2` (display-band H1, capsule indent, photo ratio, asym grid).
 *
 * Body rendering — single render path con conditional graceful:
 *   1. `body_extended` SCF (priorità) → renderizzato in `.sl-competenza__body`
 *   2. `post_content` (fallback)       → renderizzato in `.sl-competenza__intro`
 *   3. tier-1 hardcoded clusters helper → fallback ulteriore solo se entrambi i sopra vuoti
 *      (preserva il deep cluster GEO-rich per i 3 tier-1 quando body_extended non popolato)
 *   4. nessuno popolato → sezione body skip (graceful empty state)
 *
 * Sezioni opzionali (avvocato lead, casi, FAQ, correlati, articoli):
 * tutte conditional su field SCF popolati, NON su tier. Se tier-2 popola `casi_rappresentativi`
 * vedrà la sezione "vittorie recenti". Se tier-1 non popola, non vedrà.
 *
 * NO data migration in questa wave (post_content lasciato in DB invariato).
 * NO modifica field group SCF.
 * Defer Wave 6.0 Strategy A per migration post_content → body_extended.
 *
 * @package Saltelli
 */
get_header();

while (have_posts()) :
    the_post();
    $post_id    = get_the_ID();
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
    $tier_label    = saltelli_tier_badge_label($post_id, $is_tier_1, $cat_label);
    $subtitle      = (string) saltelli_field('subtitle', $post_id, '');

    // === Wave Elena FB Batch 2 #23 — Body source resolution (single path) ===
    // Priorità: body_extended SCF → post_content → tier-1 hardcoded clusters fallback.
    // Per ogni competenza è renderizzato UN SOLO body source, mai più di uno.
    $has_post_content = (trim(strip_tags(get_the_content())) !== '');
    $render_body_extended = ($body_ext !== '');
    $render_post_content  = (! $render_body_extended && $has_post_content);
    // Tier-1 hardcoded clusters: fallback ulteriore solo per i 3 tier-1 slug noti,
    // SOLO se body_extended e post_content sono entrambi vuoti.
    $render_tier1_clusters = false;
    $tier1_clusters_data = [];
    if ($is_tier_1 && ! $render_body_extended && ! $render_post_content) {
        $tier1_slug = get_post_field('post_name', $post_id);
        $tier1_clusters_data = saltelli_tier1_clusters($tier1_slug);
        $render_tier1_clusters = ! empty($tier1_clusters_data);
    }
    ?>
    <?php
    /* Elena fix 2026-05-14: modifier --long-title se title > 20 chars
       → CSS stack del box answer sotto invece di affianco (evita box "tower
       narrow" per title lunghi tipo "Eredità e successioni", "Contrattualistica"). */
    $sl_title_long = mb_strlen(get_the_title()) > 20;
    $sl_comp_classes = 'sl-competenza sl-competenza--' . ($is_tier_1 ? 'tier-1' : 'tier-2')
                     . ($sl_title_long ? ' sl-competenza--long-title' : '');
    ?>
    <article <?php post_class($sl_comp_classes); ?>>

        <header class="sl-competenza__hero sl-page-hero">
            <div class="sl-container">
                <?php saltelli_render_breadcrumb(); ?>

                <a class="sl-mono sl-competenza__back" href="<?php echo esc_url(saltelli_aree_hub_url()); ?>">
                    ← <?php esc_html_e('Tutte le aree', 'saltelli'); ?>
                </a>

                <div class="sl-mono sl-competenza__eyebrow">
                    <?php echo esc_html($tier_label); ?><?php echo $cat_label ? ' · ' . esc_html($cat_label) : ''; ?>
                </div>

                <h1 class="sl-competenza__title" data-split-reveal><?php echo wp_kses(saltelli_split_h1_words(get_the_title()), ['span' => ['class' => true, 'data-i' => true]]); ?></h1>

                <?php if ($subtitle !== '') : ?>
                    <p class="sl-competenza__sub"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>

                <?php if ($answer !== '') : ?>
                    <div class="sl-competenza__answer-wrap">
                        <div class="sl-mono sl-competenza__answer-eyebrow"><?php esc_html_e('Risposta in 50 parole', 'saltelli'); ?></div>
                        <p class="sl-competenza__answer"><?php echo esc_html($answer); ?></p>
                    </div>
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
                // === Avvocato di riferimento card ===
                // Wave Elena FB Batch 2 #23: in precedenza tier-1-only via map fisso
                // (3 slug → 3 lawyers). Ora deriva dal field SCF `lead_attorneys` per
                // TUTTE le competenze: rendera la prima lead se popolata. Tier-1 con
                // mapping legacy mantiene compat via fallback hardcoded.
                $sl_ref_lawyer_id = 0;
                if (! empty($lead_atts) && isset($lead_atts[0])) {
                    $sl_ref_lawyer_id = (int) $lead_atts[0]->ID;
                }
                if (! $sl_ref_lawyer_id && $is_tier_1) {
                    // Legacy fallback per i 3 tier-1 senza lead_attorneys popolato.
                    $sl_t1_slug = get_post_field('post_name', $post_id);
                    $sl_t1_lawyer_map = [
                        'diritto-tributario'         => 'emiliano-saltelli',
                        'diritto-del-lavoro'         => 'fabiana-saltelli',
                        'diritto-di-famiglia-lgbtq'  => 'antonia-battista',
                    ];
                    $sl_t1_lawyer_slug = $sl_t1_lawyer_map[$sl_t1_slug] ?? null;
                    if ($sl_t1_lawyer_slug) {
                        $sl_t1_lawyer = get_page_by_path($sl_t1_lawyer_slug, OBJECT, 'avvocato');
                        if ($sl_t1_lawyer) {
                            $sl_ref_lawyer_id = (int) $sl_t1_lawyer->ID;
                        }
                    }
                }
                if ($sl_ref_lawyer_id) :
                    $sl_t1_lawyer_title = get_the_title($sl_ref_lawyer_id);
                    $sl_t1_lawyer_role  = (string) saltelli_field('ruolo_breve', $sl_ref_lawyer_id, '');
                    $sl_t1_lawyer_photo = get_the_post_thumbnail_url($sl_ref_lawyer_id, 'saltelli-attorney-square');
                    if (! $sl_t1_lawyer_photo) {
                        $sl_t1_foto = saltelli_field('foto_ritratto', $sl_ref_lawyer_id);
                        if (is_array($sl_t1_foto) && ! empty($sl_t1_foto['url'])) {
                            $sl_t1_lawyer_photo = $sl_t1_foto['url'];
                        }
                    }
                ?>
                <aside class="sl-competenza__ref-lawyer" aria-label="<?php esc_attr_e('Avvocato di riferimento', 'saltelli'); ?>" data-reveal>
                    <div class="sl-mono sl-competenza__ref-lawyer-eyebrow"><?php esc_html_e('§ Avvocato di riferimento', 'saltelli'); ?></div>
                    <a href="<?php echo esc_url(get_permalink($sl_ref_lawyer_id)); ?>" class="sl-competenza__ref-lawyer-card">
                        <?php if ($sl_t1_lawyer_photo) : ?>
                            <img
                                src="<?php echo esc_url($sl_t1_lawyer_photo); ?>"
                                alt="<?php echo esc_attr($sl_t1_lawyer_title); ?>"
                                class="sl-competenza__ref-lawyer-photo"
                                width="80" height="80"
                                loading="lazy" decoding="async">
                        <?php else : ?>
                            <span class="sl-competenza__ref-lawyer-placeholder" aria-hidden="true">
                                <?php echo esc_html(mb_strtoupper(mb_substr($sl_t1_lawyer_title, 0, 1))); ?>
                            </span>
                        <?php endif; ?>
                        <div class="sl-competenza__ref-lawyer-info">
                            <h3 class="sl-competenza__ref-lawyer-name"><?php echo esc_html($sl_t1_lawyer_title); ?></h3>
                            <?php if ($sl_t1_lawyer_role !== '') : ?>
                                <p class="sl-competenza__ref-lawyer-role sl-mono"><?php echo esc_html($sl_t1_lawyer_role); ?></p>
                            <?php endif; ?>
                            <span class="sl-competenza__ref-lawyer-link"><?php esc_html_e('Vai alla scheda →', 'saltelli'); ?></span>
                        </div>
                    </a>
                </aside>
                <?php endif; ?>
            </div>
        </header>

        <?php /* === Body source: post_content (fallback) === */ ?>
        <?php if ($render_post_content) : ?>
            <section class="sl-competenza__intro sl-competenza__body-wrap">
                <div class="sl-container">
                    <div class="sl-competenza__prose"><?php the_content(); ?></div>
                </div>
            </section>
        <?php endif; ?>

        <?php /* === Body source: tier-1 hardcoded clusters (fallback solo se body_extended e post_content vuoti) === */ ?>
        <?php if ($render_tier1_clusters) : ?>
            <section class="sl-competenza__clusters sl-competenza__body-wrap" aria-label="<?php esc_attr_e('Approfondimenti tematici', 'saltelli'); ?>">
                <div class="sl-container">
                    <?php foreach ($tier1_clusters_data as $sl_idx_c => $sl_cluster) :
                        $sl_cluster_id = 'tier1-cluster-' . (int) $sl_idx_c;
                    ?>
                        <article class="sl-competenza__cluster" data-reveal aria-labelledby="<?php echo esc_attr($sl_cluster_id); ?>">
                            <h2 class="sl-competenza__cluster-h2" id="<?php echo esc_attr($sl_cluster_id); ?>">
                                <?php echo esc_html($sl_cluster['h2']); ?>
                            </h2>
                            <?php foreach ($sl_cluster['paragraphs'] as $sl_par) : ?>
                                <p class="sl-competenza__cluster-p"><?php echo esc_html($sl_par); ?></p>
                            <?php endforeach; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php /* === Body source: body_extended SCF (canonico, priorità) === */ ?>
        <?php if ($render_body_extended) : ?>
            <section class="sl-competenza__body sl-competenza__body-wrap">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Approfondimento', 'saltelli'); ?></div>
                    <div class="sl-competenza__prose sl-competenza__prose--extended" data-toc-source>
                        <?php
                        /* Wave 6.0 partial ROLLBACK 2026-05-13 — apply_filters('the_content', ...) causava WSOD su staging
                         * (likely cause: shortcode legacy nel content migrato OR do_blocks parse error OR oEmbed timeout).
                         * Ripristinato wp_kses_post() — render safe per HTML literal (i contenuti migrati 16/19 sono
                         * `<h2>` + `<p>` + `<strong>` literal, no shortcode, no Gutenberg blocks → renderizzano correttamente).
                         * Investigation: vedi error log droplet + git log v1.3.23-rollback.
                         * Apply_filters re-introduzione futura: serve sanitize shortcode + do_blocks guard pre-call. */
                        echo wp_kses_post($body_ext);
                        ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php /* Elena fix 2026-05-14 (Issue 4): rimossa <section sl-competenza__avvocati>
                  "§ Referenti / Avvocati referenti" — ridondante con .sl-competenza__ref-lawyer
                  già renderizzato nel hero sopra ("§ Avvocato di riferimento" Fabiana ecc.).
                  CSS .sl-competenza__avvocati* + .sl-competenza__lead* restano orphan
                  (cleanup Wave 6.1). */ ?>

        <?php /* Wave Elena FB Batch 2 #23: casi sezione resa universale (era tier-1-only).
                Conditional su field SCF populated, NON su tier. Tier-2 con casi popolati ora visibili. */ ?>
        <?php if (is_array($casi) && ! empty($casi)) :
            // Pre-filter validity per evitare sezione vuota se tutti i row sono incompleti.
            $valid_casi = [];
            foreach ($casi as $caso) {
                if (! empty($caso['titolo']) && ! empty($caso['descrizione_anonimizzata'])) {
                    $valid_casi[] = $caso;
                }
            }
            if (! empty($valid_casi)) :
        ?>
            <section class="sl-competenza__casi" aria-labelledby="comp-casi-h">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Tre vittorie recenti', 'saltelli'); ?></div>
                    <h2 class="sl-section-title" id="comp-casi-h"><?php esc_html_e('Tre vittorie recenti.', 'saltelli'); ?></h2>
                    <ol class="sl-cases__list" role="list">
                        <?php foreach ($valid_casi as $caso) : ?>
                            <li class="sl-cases__row">
                                <span class="sl-mono sl-cases__id"><?php echo esc_html($caso['titolo']); ?></span>
                                <p class="sl-cases__desc"><?php echo esc_html($caso['descrizione_anonimizzata']); ?></p>
                                <?php if (! empty($caso['esito'])) : ?>
                                    <span class="sl-cases__outcome"><?php echo esc_html($caso['esito']); ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </section>
        <?php
            endif;
        endif; ?>

        <?php /* Elena fix 2026-05-14: rimossa <section sl-competenza__cta-middle>
                  "§ Pronto a iniziare? · Parlane con i nostri avvocati" — ridondante con
                  footer pre-CTA "§ Contattaci" cross-page (footer.php) e con la CTA-top
                  del hero competenza. SCF helper $cta_middle_label/_url restano definiti
                  (dead vars, cleanup Wave 6.1). */ ?>

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
                <section class="sl-competenza__faq" aria-labelledby="comp-faq-h">
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

        <?php /* Elena fix 2026-05-14: rimosse <section sl-competenza__mini-form-wrap>
                  (Pattern 4 "PARLA CON NOI · Hai una domanda su X?") + <section
                  sl-competenza__cta> ("§ PRONTO? · Hai una pratica simile?") finale —
                  ENTRAMBE ridondanti con footer pre-CTA "§ Contattaci" cross-page +
                  hero CTA top. Template-parts/mini-form.php + CSS .sl-competenza__cta*
                  restano orphan (cleanup Wave 6.1). */ ?>

    </article>
    <?php
endwhile;

get_footer();
