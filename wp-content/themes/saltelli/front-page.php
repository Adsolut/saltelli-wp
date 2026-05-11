<?php
/**
 * Template: Front page (homepage).
 * Markup tradotto da .claude/knowledge/design/sessione-1/homepage-desktop.jsx
 *
 * @package Saltelli
 */
get_header();

$studio = saltelli_studio_data();

// Wave 4.7.fix.3: hero/studio/team/cases ora attaccati a Page WP "Home" (page_on_front).
// Edita da WP-Admin → Pagine → Home. Helper saltelli_page_field auto-resolve homepage_id.
$hero_eyebrow    = saltelli_page_field('hero_eyebrow', 'Studio Legale · Napoli · Chiaia · Dal 1999');
$hero_headline   = saltelli_page_field('hero_headline', 'Diritto, con misura.');
$hero_sub        = saltelli_page_field('hero_subheadline', "Studio Legale Saltelli &amp; Partners. Quattro avvocati a Chiaia, diciassette aree di pratica, vent'anni di lavoro accanto a famiglie e imprese di Napoli.");
$hero_cta_label  = saltelli_page_field('hero_cta_label', 'Prenota una consulenza gratuita');
$hero_cta_url    = saltelli_page_field('hero_cta_url', '/contatti/');

// Colophon resta in Theme Options (globale: anche footer.php usa colophon_*).
$col_indirizzo = saltelli_option('colophon_indirizzo', "Via Vannella Gaetani, 27\n80121 Napoli — Chiaia");
$col_orari     = saltelli_option('colophon_orari', "Lun – Ven · 10:00 – 19:00\nSolo su appuntamento");
$col_email     = saltelli_option('colophon_email', $studio['email']);
$col_tel       = saltelli_option('colophon_telefono', '+39 081 1813 1119');
$col_tel_e164  = saltelli_studio_phone_e164();

$studio_titolo = saltelli_page_field('studio_titolo_sezione', 'Un atelier, in senso napoletano.');
$studio_body   = saltelli_page_field('studio_body', '');
$studio_foto   = saltelli_page_field('studio_foto_facciata');

$team_titolo   = saltelli_page_field('team_titolo', "Quattro\nprofessionisti.");
$cases_titolo  = saltelli_page_field('cases_titolo', 'Casi rappresentativi.');

/* Wave 4.6: CTA default — usata in sl-contact final CTA section.
   Default ACF identici a quelli legacy hero_* per backward compat. */
$cta_default_url     = saltelli_option('cta_default_url', $hero_cta_url);
$cta_default_label   = saltelli_option('cta_default_label', $hero_cta_label);
$cta_subline_italic  = (string) saltelli_option('cta_subline_italic', '');

// 19 aree — WP_Query CPT competenza, tier-1 first, poi menu_order, poi title.
// Wave 4.6: use is_tier_1 (Wave 1 ACF schema canonico).
$competenze = get_posts([
    'post_type'   => 'competenza',
    'numberposts' => -1,
    'meta_key'    => 'is_tier_1',
    'orderby'     => [
        'meta_value_num' => 'DESC',
        'menu_order'     => 'ASC',
        'title'          => 'ASC',
    ],
]);
// fallback: se nessuna competenza creata, lista dummy mostra solo CTA.

// 4 lawyers
$avvocati = get_posts([
    'post_type'   => 'avvocato',
    'numberposts' => 4,
    'orderby'     => ['menu_order' => 'ASC', 'date' => 'ASC'],
]);
$layout_team = saltelli_team_grid_layout();

// Filter pillole (tassonomia)
$tipo_terms = get_terms([
    'taxonomy'   => 'tipo-area',
    'hide_empty' => false,
    'orderby'    => 'count',
    'order'      => 'DESC',
]);

$cases = saltelli_homepage_cases();
$press = saltelli_press_outlets();
?>

<section class="sl-hero sl-page-hero sl-page-hero--homepage" id="hero">
    <div class="sl-hero__inner sl-container">
        <div class="sl-hero__main">
            <div class="sl-mono sl-hero__eyebrow"><?php echo esc_html($hero_eyebrow); ?></div>

            <h1 class="sl-hero__headline" data-split-reveal>
                <?php
                $words = preg_split('/\s+/', wp_strip_all_tags($hero_headline));
                foreach ($words as $i => $w) :
                    if ($w === '') continue;
                    ?>
                    <span class="sl-word sl-hero__word" data-i="<?php echo (int) $i; ?>"><?php echo esc_html($w); ?></span>
                <?php endforeach; ?>
            </h1>

            <div class="sl-hero__subheadline">
                <?php echo wp_kses_post($hero_sub); ?>
            </div>

            <a class="sl-btn sl-btn--primary" href="<?php echo esc_url($hero_cta_url); ?>">
                <span><?php echo esc_html($hero_cta_label); ?></span>
                <span class="arrow" aria-hidden="true">→</span>
            </a>
            <div class="sl-mono sl-hero__cta-note">
                <?php echo esc_html(saltelli_page_field('hero_cta_note', 'Prima consulenza conoscitiva — risposta entro 24 ore')); ?>
            </div>
        </div>

        <aside class="sl-hero__colophon" aria-label="<?php esc_attr_e('Coordinate studio', 'saltelli'); ?>">
            <div class="sl-mono sl-hero__colophon-label"><?php esc_html_e('Coordinate', 'saltelli'); ?></div>
            <div class="sl-hero__colophon-grid">
                <div class="sl-hero__colophon-item">
                    <div class="sl-mono"><?php esc_html_e('Indirizzo', 'saltelli'); ?></div>
                    <p><?php echo wp_kses_post(nl2br(esc_html($col_indirizzo))); ?></p>
                </div>
                <div class="sl-hero__colophon-item">
                    <div class="sl-mono"><?php esc_html_e('Orari', 'saltelli'); ?></div>
                    <p><?php echo wp_kses_post(nl2br(esc_html($col_orari))); ?></p>
                </div>
                <div class="sl-hero__colophon-item">
                    <div class="sl-mono"><?php esc_html_e('Contatti', 'saltelli'); ?></div>
                    <p>
                        <a class="sl-link" href="mailto:<?php echo esc_attr($col_email); ?>"><?php echo esc_html($col_email); ?></a><br>
                        <a class="sl-mono sl-hero__colophon-tel" href="tel:<?php echo esc_attr($col_tel_e164); ?>"><?php echo esc_html($col_tel); ?></a>
                    </p>
                </div>
            </div>
        </aside>
    </div>
    <div class="sl-hero__scroll" aria-hidden="true">
        <span class="sl-hero__scroll-line"></span>
        <span class="sl-mono"><?php esc_html_e('Scorri', 'saltelli'); ?></span>
    </div>
</section>

<section class="sl-areas" id="aree" aria-labelledby="aree-h">
    <div class="sl-container">
        <div class="sl-section-head">
            <div class="sl-mono"><?php echo esc_html(saltelli_page_field('home_areas_eyebrow', '§ 01 — Aree di pratica')); ?></div>
            <h2 class="sl-section-title" id="aree-h">
                <?php echo esc_html(saltelli_page_field('home_areas_h2_main', 'Diciassette aree.')); ?><br>
                <em><?php echo esc_html(saltelli_page_field('home_areas_h2_em', 'Tre presidiate in profondità.')); ?></em>
            </h2>
        </div>

        <div class="sl-areas__filters" role="tablist">
            <button class="sl-areas__filter sl-mono is-active" type="button" data-filter="*" aria-pressed="true"><?php esc_html_e('Tutte', 'saltelli'); ?></button>
            <?php foreach ($tipo_terms as $term) : ?>
                <button class="sl-areas__filter sl-mono" type="button" data-filter="<?php echo esc_attr($term->slug); ?>" aria-pressed="false"><?php echo esc_html($term->name); ?></button>
            <?php endforeach; ?>
        </div>

        <div class="sl-areas__grid">
            <div class="sl-areas__list">
                <?php
                if (!empty($competenze)) :
                    $i = 0;
                    foreach ($competenze as $p) :
                        $i++;
                        $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                        $cat_slug  = saltelli_competenza_category_slug($p->ID);
                        $cat_label = saltelli_competenza_category_label($p->ID);
                        // Wave 4.6: use is_tier_1 (Wave 1 ACF schema canonico).
                        $is_tier_1 = (bool) saltelli_field('is_tier_1', $p->ID, false);
                        $lead      = (string) saltelli_field('lead_breve', $p->ID, '');
                        if ($lead === '') {
                            $lead = (string) saltelli_field('answer_capsule', $p->ID, '');
                            if ($lead !== '') {
                                $lead = wp_trim_words($lead, 18, '…');
                            }
                        }
                        ?>
                        <a class="sl-area<?php echo $is_tier_1 ? ' sl-area--tier1' : ''; ?>"
                           href="<?php echo esc_url(get_permalink($p)); ?>"
                           data-area-num="<?php echo esc_attr($num); ?>"
                           data-area-cat="<?php echo esc_attr($cat_slug); ?>"
                           data-area-lead="<?php echo esc_attr($lead); ?>"
                           data-area-label="<?php echo esc_attr($cat_label ?: ($is_tier_1 ? 'Tier 1' : 'Tier 2')); ?>">
                            <span class="sl-area__num sl-mono"><?php echo esc_html($num); ?> / <?php echo esc_html(str_pad((string) count($competenze), 2, '0', STR_PAD_LEFT)); ?></span>
                            <span class="sl-area__title"><?php echo esc_html(get_the_title($p)); ?></span>
                            <span class="sl-area__meta sl-mono">
                                <?php echo esc_html($is_tier_1 ? __('Tier 1 · approfondimento', 'saltelli') : ($cat_label ?: __('Tier 2', 'saltelli'))); ?>
                                <span class="arrow" aria-hidden="true">→</span>
                            </span>
                        </a>
                    <?php endforeach;
                else :
                    ?>
                    <p class="sl-mono"><?php esc_html_e('Nessuna area di pratica pubblicata.', 'saltelli'); ?></p>
                <?php endif; ?>
            </div>

            <aside class="sl-area__preview" aria-live="polite" data-area-preview>
                <p class="sl-area__preview-empty"><?php echo esc_html(saltelli_page_field('home_areas_preview_hint', "Passa il cursore su un'area per leggerne la sintesi.")); ?></p>
            </aside>
        </div>
    </div>
</section>

<section class="sl-studio" id="studio" aria-labelledby="studio-h">
    <div class="sl-container">
        <div class="sl-section-head">
            <div class="sl-mono"><?php echo esc_html(saltelli_page_field('home_studio_eyebrow', '§ 02 — Lo studio')); ?></div>
            <h2 class="sl-section-title" id="studio-h"><?php echo esc_html($studio_titolo); ?></h2>
        </div>

        <div class="sl-studio__prose" data-drop-cap>
            <?php
            // Wave 4.7.fix.2: l'inline 3-paragrafi fallback è stato migrato a JSON
            // default_value (acf-json/group_theme_options_v1.json:studio_body) e
            // seedato in DB via inc/seed-theme-options.php → admin sempre coerente
            // con frontend. Empty state significa "Elena ha esplicitamente svuotato".
            if ($studio_body) {
                echo wp_kses_post($studio_body);
            }
            ?>
        </div>

        <figure class="sl-studio__plate">
            <?php if (is_array($studio_foto) && !empty($studio_foto['url'])) : ?>
                <img src="<?php echo esc_url($studio_foto['url']); ?>" alt="<?php echo esc_attr($studio_foto['alt'] ?: __('Facciata Studio Saltelli, Via Vannella Gaetani 27, Napoli', 'saltelli')); ?>" loading="lazy" decoding="async" width="1440" height="480">
            <?php else : ?>
                <div class="sl-studio__plate-placeholder" aria-hidden="true">
                    <span class="sl-mono sl-studio__plate-tl">Plate I · <?php esc_html_e('Facciata', 'saltelli'); ?></span>
                    <span class="sl-studio__plate-center">
                        <span class="sl-studio__plate-line">Via Vannella Gaetani, 27</span>
                        <span class="sl-mono">Fotografia in B/N · 1440 × 480 · placeholder</span>
                    </span>
                    <span class="sl-mono sl-studio__plate-br">Napoli · Chiaia</span>
                </div>
                <!-- TODO: replace with real Saltelli photo (facciata Via Vannella Gaetani, 27) -->
            <?php endif; ?>
        </figure>
    </div>
</section>

<section class="sl-team" id="avvocati" aria-labelledby="team-h">
    <div class="sl-container">
        <div class="sl-section-head">
            <div class="sl-mono"><?php echo esc_html(saltelli_page_field('home_team_eyebrow', '§ 03 — Avvocati')); ?></div>
            <h2 class="sl-section-title" id="team-h">
                <?php
                $title_lines = preg_split('/\r\n|\r|\n/', $team_titolo);
                foreach ($title_lines as $idx => $line) {
                    if ($line === '') continue;
                    if ($idx === 0) {
                        echo esc_html($line);
                    } else {
                        echo '<br><em>' . esc_html($line) . '</em>';
                    }
                }
                ?>
            </h2>
        </div>

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
                                    'alt'      => esc_attr(get_the_title($av) . ' · ' . $ruolo),
                                ]);
                            } elseif (is_array($foto) && !empty($foto['url'])) {
                                echo '<img src="' . esc_url($foto['url']) . '" alt="' . esc_attr($foto['alt'] ?: get_the_title($av)) . '" loading="lazy" decoding="async" width="600" height="800">';
                            } else {
                                echo '<span class="sl-team__placeholder" aria-hidden="true"><span class="sl-mono">' . esc_html__('Ritratto · 3:4', 'saltelli') . '</span></span>';
                                echo '<!-- TODO: replace with real Saltelli photo (ritratto avvocato) -->';
                            }
                            ?>
                        </a>
                        <?php if ($ruolo) : ?>
                            <div class="sl-mono sl-team__role"><?php echo esc_html($ruolo); ?></div>
                        <?php endif; ?>
                        <h3 class="sl-team__name">
                            <a href="<?php echo esc_url(get_permalink($av)); ?>"><?php echo esc_html(get_the_title($av)); ?></a>
                        </h3>
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

<section class="sl-cases" id="casi" aria-labelledby="cases-h">
    <div class="sl-container">
        <div class="sl-section-head">
            <div class="sl-mono"><?php echo esc_html(saltelli_page_field('home_cases_eyebrow', '§ 04 — Vittorie recenti')); ?></div>
            <h2 class="sl-section-title" id="cases-h"><?php echo esc_html($cases_titolo); ?></h2>
        </div>

        <ol class="sl-cases__list" role="list">
            <?php foreach ($cases as $c) : ?>
                <li class="sl-cases__row">
                    <span class="sl-mono sl-cases__id"><?php echo esc_html($c['identifier']); ?></span>
                    <p class="sl-cases__desc"><?php echo esc_html($c['descrizione']); ?></p>
                    <span class="sl-cases__outcome"><?php echo esc_html($c['outcome']); ?></span>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<?php /* Wave 6 Pattern 6 — Testimonials block (renderizza solo se ci sono trust items con type=testimonianza). */ ?>
<section class="sl-front-testimonials">
    <div class="sl-container">
        <?php get_template_part('template-parts/testimonials-block'); ?>
    </div>
</section>

<section class="sl-press" aria-labelledby="press-h">
    <div class="sl-container">
        <div class="sl-press__inner">
            <div class="sl-mono sl-press__label" id="press-h"><?php echo esc_html(saltelli_page_field('home_press_eyebrow', '§ 05 — Parlano di noi')); ?></div>
            <ul class="sl-press__outlets">
                <?php foreach ($press as $p) : ?>
                    <li class="sl-press__outlet"><?php echo esc_html($p); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<?php /* Wave 6 Pattern 2 — Trust bar globale, prima della §06 contact (max pressure conversione). */ ?>
<section class="sl-front-trust">
    <div class="sl-container">
        <?php get_template_part('template-parts/trust-bar'); ?>
    </div>
</section>

<section class="sl-contact" id="contatti" aria-labelledby="contact-h">
    <div class="sl-container">
        <div class="sl-section-head">
            <div class="sl-mono"><?php echo esc_html(saltelli_page_field('home_contact_eyebrow', '§ 06 — Contatti')); ?></div>
            <div>
                <div class="sl-mono sl-contact__eyebrow">
                    <?php echo esc_html(saltelli_page_field('home_contact_subline', 'Prima consulenza conoscitiva gratuita · Risposta entro 24 ore')); ?>
                </div>
                <h2 class="sl-section-title sl-contact__title" id="contact-h">
                    <?php echo esc_html(saltelli_page_field('home_contact_h2_line1', 'Prenota')); ?><br>
                    <?php echo esc_html(saltelli_page_field('home_contact_h2_line2', 'un primo')); ?><br>
                    <em><?php echo esc_html(saltelli_page_field('home_contact_h2_line3', 'incontro.')); ?></em>
                </h2>
            </div>
        </div>

        <div class="sl-contact__grid">
            <div class="sl-contact__item">
                <div class="sl-mono"><?php esc_html_e('Indirizzo', 'saltelli'); ?></div>
                <p class="sl-contact__big"><?php echo wp_kses_post(nl2br(esc_html($col_indirizzo))); ?></p>
            </div>
            <div class="sl-contact__item">
                <div class="sl-mono"><?php esc_html_e('Telefono', 'saltelli'); ?></div>
                <p class="sl-contact__big"><a href="tel:<?php echo esc_attr($col_tel_e164); ?>"><?php echo esc_html($col_tel); ?></a></p>
            </div>
            <div class="sl-contact__item">
                <div class="sl-mono"><?php esc_html_e('Email', 'saltelli'); ?></div>
                <p class="sl-contact__big"><a href="mailto:<?php echo esc_attr($col_email); ?>"><?php echo esc_html($col_email); ?></a></p>
            </div>
        </div>

        <div class="sl-contact__cta">
            <?php /* Wave 4.6: cta_default_url + cta_default_label editabili globally.
                    cta_subline_italic emessa hidden per future styling editor (designer can show via CSS). */ ?>
            <a class="sl-btn sl-btn--primary" href="<?php echo esc_url($cta_default_url); ?>">
                <span><?php echo esc_html($cta_default_label); ?></span>
                <span class="arrow" aria-hidden="true">→</span>
            </a>
            <?php if ($cta_subline_italic !== '') : ?>
                <p class="sl-contact__cta-subline" hidden><em><?php echo esc_html($cta_subline_italic); ?></em></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
get_footer();
