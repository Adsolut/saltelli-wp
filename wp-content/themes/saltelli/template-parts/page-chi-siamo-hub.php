<?php
/**
 * Template part: page-chi-siamo-hub.php
 *
 * Render della page hub /chi-siamo/ (dopo Phase 5 rinomina slug).
 * Wave 5 IA refactor — pagina madre del cluster Chi Siamo (lo-studio, team, risultati).
 *
 * Layout: hero editoriale + grid 3 child cards.
 * Hardcoded copy per Wave 5; ACF Theme Options può sostituire in Wave 6.
 *
 * @package Saltelli
 * @since 1.1.0 Wave 5
 */
defined('ABSPATH') || exit;

$sl_lawyers_count = wp_count_posts('avvocato')->publish ?? 4;
$sl_casi_count    = wp_count_posts('saltelli_caso')->publish ?? 9;

// Wave 4.7.fix.3: hub copy ora attaccato a Page WP "Chi Siamo" (page_slug=chi-siamo).
// Edita da WP-Admin → Pagine → Chi Siamo → metabox "Saltelli — Page Chi Siamo".
$sl_hub_eyebrow = (string) saltelli_page_field('hub_chisiamo_eyebrow', __('§ Chi siamo', 'saltelli'));
$sl_hub_h1_main = (string) saltelli_page_field('hub_chisiamo_h1_main', __('Quattro avvocati,', 'saltelli'));
$sl_hub_h1_em   = (string) saltelli_page_field('hub_chisiamo_h1_emphasis', __('un atelier.', 'saltelli'));
$sl_hub_intro   = (string) saltelli_page_field('hub_chisiamo_intro', __("Studio Legale Emiliano Saltelli & Partners. Quattro professionisti in Via Vannella Gaetani, Chiaia. Una bottega — non una catena. Dal 1999.", 'saltelli'));
?>

<section class="sl-page-hero sl-hub-hero" aria-labelledby="hub-chi-siamo-h1">
    <div class="sl-container sl-hub-hero__inner">
        <?php saltelli_render_breadcrumb(); ?>
        <p class="sl-mono sl-hub-hero__eyebrow"><?php echo esc_html($sl_hub_eyebrow); ?></p>
        <h1 class="sl-page__title sl-hub-hero__h1" id="hub-chi-siamo-h1" data-split-reveal>
            <?php
            $sl_h1 = esc_html($sl_hub_h1_main) . '<br><em>' . esc_html($sl_hub_h1_em) . '</em>';
            echo wp_kses(saltelli_split_h1_words($sl_h1), [
                'span' => ['class' => true, 'data-i' => true],
                'em'   => [],
                'br'   => [],
            ]);
            ?>
        </h1>
        <p class="sl-page__lede sl-hub-hero__lede"><?php echo esc_html($sl_hub_intro); ?></p>
    </div>
</section>

<section class="sl-hub-grid-section" aria-label="<?php esc_attr_e('Sezioni Chi Siamo', 'saltelli'); ?>">
    <div class="sl-container">
        <ul class="sl-hub-grid sl-hub-grid--3">
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/chi-siamo/lo-studio/')); ?>">
                    <p class="sl-mono sl-hub-card__num">01 / 03</p>
                    <h2 class="sl-hub-card__title"><?php echo esc_html(saltelli_page_field('hub_chisiamo_card1_title', 'Lo Studio')); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php echo esc_html(saltelli_page_field('hub_chisiamo_card1_desc', 'Storia dal 1999, valori, sede in Via Vannella Gaetani 27. Atelier napoletano in Chiaia.')); ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php echo esc_html(saltelli_page_field('hub_chisiamo_card1_cta', 'Scopri →')); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/chi-siamo/team/')); ?>">
                    <p class="sl-mono sl-hub-card__num">02 / 03</p>
                    <h2 class="sl-hub-card__title"><?php echo esc_html(saltelli_page_field('hub_chisiamo_card2_title', 'Il Team')); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php
                        printf(
                            esc_html(saltelli_page_field('hub_chisiamo_card2_desc', '%s avvocati, ognuno con specializzazione consolidata. Tributario, lavoro, famiglia LGBTQ+, condominio.')),
                            esc_html(number_format_i18n($sl_lawyers_count))
                        );
                        ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php echo esc_html(saltelli_page_field('hub_chisiamo_card2_cta', 'Conosci il team →')); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/chi-siamo/casi-rappresentativi/')); ?>">
                    <p class="sl-mono sl-hub-card__num">03 / 03</p>
                    <h2 class="sl-hub-card__title"><?php echo esc_html(saltelli_page_field('hub_chisiamo_card3_title', 'Risultati')); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php
                        printf(
                            esc_html(saltelli_page_field('hub_chisiamo_card3_desc', '%s casi rappresentativi vinti — Cassazione, Tribunale di Napoli, CTR Campania, TAR.')),
                            esc_html(number_format_i18n($sl_casi_count))
                        );
                        ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php echo esc_html(saltelli_page_field('hub_chisiamo_card3_cta', 'Vedi i casi →')); ?></span>
                </a>
            </li>
        </ul>
    </div>
</section>

<section class="sl-hub-cta" aria-label="<?php esc_attr_e('CTA Chi Siamo', 'saltelli'); ?>">
    <div class="sl-container sl-hub-cta__inner">
        <p class="sl-mono"><?php echo esc_html(saltelli_page_field('hub_chisiamo_cta_eyebrow', '§ Iniziamo a parlare')); ?></p>
        <h2 class="sl-hub-cta__title">
            <?php echo esc_html(saltelli_page_field('hub_chisiamo_cta_title', 'Prenota una prima consulenza con uno degli avvocati.')); ?>
        </h2>
        <a class="sl-cta sl-cta--primary" href="<?php echo esc_url(home_url(saltelli_page_field('hub_chisiamo_cta_url', '/contatti/'))); ?>">
            <?php echo esc_html(saltelli_page_field('hub_chisiamo_cta_btn_label', 'Contattaci')); ?>
        </a>
    </div>
</section>
