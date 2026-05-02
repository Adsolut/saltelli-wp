<?php
/**
 * Template: Single CPT avvocato.
 *
 * @package Saltelli
 */
get_header();

while (have_posts()) :
    the_post();
    $post_id    = get_the_ID();
    $ruolo      = (string) saltelli_field('ruolo_breve', $post_id, '');
    $bio_breve  = (string) saltelli_field('bio_breve', $post_id, '');
    $bio_est    = (string) saltelli_field('bio_estesa', $post_id, '');
    $email      = (string) saltelli_field('email_pubblica', $post_id, '');
    $tel        = (string) saltelli_field('telefono_pubblico', $post_id, '');
    $tel_e164   = preg_replace('/[^0-9+]/', '', $tel);
    $whatsapp   = (string) saltelli_field('whatsapp', $post_id, '');
    $whatsapp_e164 = preg_replace('/[^0-9+]/', '', $whatsapp);
    $linkedin   = (string) saltelli_field('same_as_linkedin', $post_id, '');
    if (!$linkedin) {
        $linkedin = saltelli_attorney_linkedin(get_post_field('post_name', $post_id));
    }
    $specs      = saltelli_get_attorney_specializations($post_id);
    $aree       = saltelli_field('aree_competenza_correlate', $post_id, []);
    $formazione = saltelli_field('formazione', $post_id, []);
    $foto       = saltelli_field('foto_ritratto', $post_id);
    ?>
    <article <?php post_class('sl-attorney'); ?>>

        <header class="sl-attorney__hero">
            <div class="sl-container sl-attorney__hero-inner">
                <?php saltelli_render_breadcrumb(); ?>

                <a class="sl-mono sl-attorney__back" href="<?php echo esc_url(get_post_type_archive_link('avvocato')); ?>">
                    ← <?php esc_html_e('Tutti gli avvocati', 'saltelli'); ?>
                </a>

                <div class="sl-attorney__hero-grid">
                    <figure class="sl-attorney__portrait">
                        <?php
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('saltelli-attorney-portrait', [
                                'loading'  => 'eager',
                                'decoding' => 'async',
                                'alt'      => esc_attr(get_the_title() . ($ruolo ? ' · ' . $ruolo : '')),
                            ]);
                        } elseif (is_array($foto) && !empty($foto['url'])) {
                            echo '<img src="' . esc_url($foto['url']) . '" alt="' . esc_attr($foto['alt'] ?: get_the_title()) . '" width="600" height="800" decoding="async">';
                        } else {
                            echo '<span class="sl-team__placeholder" aria-hidden="true"><span class="sl-mono">' . esc_html__('Ritratto · 3:4', 'saltelli') . '</span></span>';
                            echo '<!-- TODO: replace with real Saltelli photo -->';
                        }
                        ?>
                    </figure>

                    <div class="sl-attorney__hero-text">
                        <?php if ($ruolo) : ?>
                            <div class="sl-mono sl-attorney__role"><?php echo esc_html($ruolo); ?></div>
                        <?php endif; ?>

                        <h1 class="sl-attorney__name" data-split-reveal><?php echo wp_kses(saltelli_split_h1_words(get_the_title()), ['span' => ['class' => true, 'data-i' => true]]); ?></h1>

                        <?php if ($bio_breve) : ?>
                            <p class="sl-attorney__lede"><?php echo esc_html($bio_breve); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($specs)) : ?>
                            <ul class="sl-team__specs sl-attorney__specs">
                                <?php foreach ($specs as $s) : ?>
                                    <li class="sl-tag"><?php echo esc_html($s); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <aside class="sl-attorney__sticky" aria-label="<?php esc_attr_e('Contatti rapidi', 'saltelli'); ?>">
            <?php if ($tel_e164) : ?>
                <a class="sl-attorney__sticky-btn sl-mono" href="tel:<?php echo esc_attr($tel_e164); ?>">
                    <span class="sl-mono"><?php esc_html_e('Tel', 'saltelli'); ?></span>
                </a>
            <?php endif; ?>
            <?php if ($email) : ?>
                <a class="sl-attorney__sticky-btn sl-mono" href="mailto:<?php echo esc_attr($email); ?>">
                    <span class="sl-mono"><?php esc_html_e('Email', 'saltelli'); ?></span>
                </a>
            <?php endif; ?>
            <?php if ($whatsapp_e164) : ?>
                <a class="sl-attorney__sticky-btn sl-mono" href="https://wa.me/<?php echo esc_attr(ltrim($whatsapp_e164, '+')); ?>" rel="noopener" target="_blank">
                    <span class="sl-mono">WhatsApp</span>
                </a>
            <?php endif; ?>
        </aside>

        <?php if ($bio_est) : ?>
            <section class="sl-attorney__bio">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Bio', 'saltelli'); ?></div>
                    <div class="sl-attorney__bio-prose">
                        <?php echo wp_kses_post($bio_est); ?>
                    </div>
                </div>
            </section>
        <?php elseif (get_the_content()) : ?>
            <section class="sl-attorney__bio">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Bio', 'saltelli'); ?></div>
                    <div class="sl-attorney__bio-prose">
                        <?php the_content(); ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php
        // === v0.24.0 TASK 2 — "Sei aree di competenza" sezione JSX-faithful ===
        // Pattern: lista numerata (01-06) + h3 titolo + meta (tier · cluster).
        // Source: saltelli-s2-attorney-single.jsx
        if (!empty($aree)) :
            $sl_aree_ids = is_array($aree) ? $aree : [$aree];
            $sl_aree_ids = array_values(array_filter(array_map(static function ($x) {
                return is_object($x) ? (int) $x->ID : (int) $x;
            }, $sl_aree_ids)));
            if (!empty($sl_aree_ids)) :
                $sl_aree_count = count($sl_aree_ids);
                $sl_aree_h2 = $sl_aree_count === 6 ? __('Sei aree di competenza.', 'saltelli')
                            : sprintf(_n('%s area di competenza.', '%s aree di competenza.', $sl_aree_count, 'saltelli'),
                                      number_format_i18n($sl_aree_count));
            ?>
            <section class="sl-attorney__competenze sl-attorney__aree" aria-labelledby="aree-h" data-reveal>
                <div class="sl-container">
                    <header class="sl-attorney__competenze-head">
                        <div class="sl-mono">§ <?php esc_html_e('Competenze', 'saltelli'); ?></div>
                        <h2 class="sl-attorney__competenze-h2 sl-section-title" id="aree-h"><?php echo esc_html($sl_aree_h2); ?></h2>
                    </header>
                    <ol class="sl-attorney__areas-list" role="list">
                        <?php foreach ($sl_aree_ids as $sl_idx => $aid) :
                            $is_tier_1 = function_exists('saltelli_is_tier1_competenza')
                                ? saltelli_is_tier1_competenza($aid)
                                : (bool) saltelli_field('is_tier_1_focus', $aid, false);
                            $tier_label = $is_tier_1
                                ? __('Tier 1 · approfondimento', 'saltelli')
                                : __('Tier 2', 'saltelli');
                            $cluster = saltelli_competenza_category_label($aid);
                            $num     = str_pad((string) ($sl_idx + 1), 2, '0', STR_PAD_LEFT);
                        ?>
                            <li class="sl-area<?php echo $is_tier_1 ? ' sl-area--tier1' : ''; ?>">
                                <span class="sl-area__num sl-mono"><?php echo esc_html($num); ?></span>
                                <h3 class="sl-area__title">
                                    <a href="<?php echo esc_url(get_permalink($aid)); ?>" class="sl-link sl-link--clean">
                                        <?php echo esc_html(get_the_title($aid)); ?>
                                    </a>
                                </h3>
                                <p class="sl-area__meta sl-mono">
                                    <?php echo esc_html($tier_label); ?><?php if ($cluster) : ?> · <?php echo esc_html($cluster); ?><?php endif; ?>
                                </p>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </section>
        <?php endif; endif; ?>

        <?php if (!empty($formazione) && is_array($formazione)) : ?>
            <section class="sl-attorney__timeline" aria-labelledby="formazione-h">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Formazione', 'saltelli'); ?></div>
                    <h2 class="sl-section-title" id="formazione-h"><?php esc_html_e('Formazione', 'saltelli'); ?></h2>
                    <ol class="sl-timeline" role="list">
                        <?php foreach ($formazione as $row) :
                            $anno = !empty($row['anno']) ? (string) $row['anno'] : '';
                            $tit  = !empty($row['titolo']) ? (string) $row['titolo'] : '';
                            $ist  = !empty($row['istituzione']) ? (string) $row['istituzione'] : '';
                            if (!$tit) continue;
                            ?>
                            <li class="sl-timeline__row">
                                <span class="sl-mono sl-timeline__year"><?php echo esc_html($anno); ?></span>
                                <span class="sl-timeline__title"><?php echo esc_html($tit); ?></span>
                                <?php if ($ist) : ?>
                                    <span class="sl-timeline__inst sl-mono"><?php echo esc_html($ist); ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </section>
        <?php endif; ?>

        <?php
        // v0.19.0 — Casi rappresentativi per attorney (Sessione 2 single-avvocato spec)
        $sl_atty_slug  = get_post_field('post_name', $post_id);
        $sl_atty_casi  = saltelli_attorney_cases($sl_atty_slug);
        if (!empty($sl_atty_casi)) :
            $sl_atty_casi_count = count($sl_atty_casi);
            $sl_count_label = $sl_atty_casi_count === 3 ? __('Tre casi rappresentativi.', 'saltelli')
                            : ($sl_atty_casi_count === 2 ? __('Due casi rappresentativi.', 'saltelli')
                            : __('Un caso rappresentativo.', 'saltelli'));
            ?>
            <section class="sl-attorney__casi" aria-labelledby="atty-casi-h">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Vittorie recenti', 'saltelli'); ?></div>
                    <h2 class="sl-section-title" id="atty-casi-h"><?php echo esc_html($sl_count_label); ?></h2>
                    <ol class="sl-attorney__casi-list" role="list">
                        <?php foreach ($sl_atty_casi as $case) : ?>
                            <li class="sl-attorney__casi-row">
                                <span class="sl-mono sl-attorney__casi-id"><?php echo esc_html($case['id']); ?></span>
                                <p class="sl-attorney__casi-desc"><?php echo esc_html($case['desc']); ?></p>
                                <span class="sl-attorney__casi-outcome">
                                    <?php echo esc_html($case['outcome']); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </section>
        <?php endif; ?>

        <?php
        // Articoli del blog correlati: post che hanno una categoria con stesso nome di una delle aree dell'avvocato.
        if (!empty($aree)) :
            $ids = is_array($aree) ? $aree : [$aree];
            $ids = array_filter(array_map(static function ($x) { return is_object($x) ? (int) $x->ID : (int) $x; }, $ids));
            $cat_names = array_filter(array_map('get_the_title', $ids));
            $blog_q = new WP_Query([
                'post_type'      => 'post',
                'posts_per_page' => 3,
                'category_name'  => '',
                'no_found_rows'  => true,
                'meta_query'     => [],
                'tax_query'      => $cat_names ? [[
                    'taxonomy' => 'category',
                    'field'    => 'name',
                    'terms'    => $cat_names,
                ]] : [],
            ]);
            if ($blog_q->have_posts()) : ?>
                <section class="sl-attorney__articles" aria-labelledby="articles-h">
                    <div class="sl-container">
                        <div class="sl-mono">§ <?php esc_html_e('Editoriale', 'saltelli'); ?></div>
                        <h2 class="sl-section-title" id="articles-h"><?php esc_html_e('Articoli recenti', 'saltelli'); ?></h2>
                        <ul class="sl-articles">
                            <?php while ($blog_q->have_posts()) : $blog_q->the_post(); ?>
                                <li class="sl-articles__item">
                                    <a href="<?php the_permalink(); ?>">
                                        <span class="sl-mono"><?php echo esc_html(get_the_date()); ?></span>
                                        <span class="sl-articles__title"><?php the_title(); ?></span>
                                    </a>
                                </li>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </ul>
                    </div>
                </section>
            <?php endif;
        endif;
        ?>

        <section class="sl-attorney__cta">
            <div class="sl-container">
                <h2 class="sl-section-title">
                    <?php
                    $first_name = explode(' ', preg_replace('/^Avv\.\s+/u', '', get_the_title()))[0] ?? '';
                    printf(
                        esc_html__('Prenota un incontro con %s', 'saltelli'),
                        '<em>' . esc_html(get_the_title()) . '</em>'
                    );
                    ?>
                </h2>
                <a class="sl-btn sl-btn--primary" href="<?php echo esc_url(home_url('/contatti/')); ?>">
                    <span><?php esc_html_e('Prenota un primo incontro', 'saltelli'); ?></span>
                    <span class="arrow" aria-hidden="true">→</span>
                </a>
            </div>
        </section>

    </article>
    <?php
endwhile;

get_footer();
