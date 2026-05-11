<?php
/**
 * Template part: page-risorse-hub.php
 *
 * Render della page hub /risorse/ — pagina madre Risorse (blog, faq, glossario, guide).
 * Wave 5 IA refactor.
 *
 * Layout: hero editoriale + grid 4 risorse cards.
 *
 * @package Saltelli
 * @since 1.1.0 Wave 5
 */
defined('ABSPATH') || exit;

$sl_blog_count   = wp_count_posts('post')->publish ?? 0;
$sl_faq_count    = wp_count_posts('saltelli_faq')->publish ?? 0;
$sl_guide_count  = wp_count_posts('saltelli_guida')->publish ?? 0;

// Wave 4.7.fix.3: hub copy ora attaccato a Page WP "Risorse" (page_slug=risorse).
// Edita da WP-Admin → Pagine → Risorse → metabox "Saltelli — Page Risorse".
$sl_hub_eyebrow = (string) saltelli_page_field('hub_risorse_eyebrow', __('§ Risorse', 'saltelli'));
$sl_hub_h1_main = (string) saltelli_page_field('hub_risorse_h1_main', __('Approfondire,', 'saltelli'));
$sl_hub_h1_em   = (string) saltelli_page_field('hub_risorse_h1_emphasis', __('senza fretta.', 'saltelli'));
$sl_hub_intro   = (string) saltelli_page_field('hub_risorse_intro', __('Articoli, glossario, domande frequenti, guide gratuite. Materiale per orientarti prima di prenotare una consulenza.', 'saltelli'));
?>

<section class="sl-page-hero sl-hub-hero" aria-labelledby="hub-risorse-h1">
    <div class="sl-container sl-hub-hero__inner">
        <?php saltelli_render_breadcrumb(); ?>
        <p class="sl-mono sl-hub-hero__eyebrow"><?php echo esc_html($sl_hub_eyebrow); ?></p>
        <h1 class="sl-page__title sl-hub-hero__h1" id="hub-risorse-h1" data-split-reveal>
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

<section class="sl-hub-grid-section" aria-label="<?php esc_attr_e('Risorse', 'saltelli'); ?>">
    <div class="sl-container">
        <ul class="sl-hub-grid sl-hub-grid--4">
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/risorse/blog/')); ?>">
                    <p class="sl-mono sl-hub-card__num">01 / 04</p>
                    <h2 class="sl-hub-card__title"><?php echo esc_html(saltelli_page_field('hub_risorse_card1_title', 'Blog')); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php echo esc_html(saltelli_page_field('hub_risorse_card1_desc', 'Articoli scritti dai nostri avvocati su sentenze, novità normative, casi reali.')); ?>
                    </p>
                    <p class="sl-mono sl-hub-card__meta">
                        <?php
                        printf(
                            esc_html(_n('%s articolo', '%s articoli', $sl_blog_count, 'saltelli')),
                            esc_html(number_format_i18n($sl_blog_count))
                        );
                        ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php echo esc_html(saltelli_page_field('hub_risorse_card1_cta', 'Leggi →')); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/risorse/domande-frequenti/')); ?>">
                    <p class="sl-mono sl-hub-card__num">02 / 04</p>
                    <h2 class="sl-hub-card__title"><?php echo esc_html(saltelli_page_field('hub_risorse_card2_title', 'Domande frequenti')); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php echo esc_html(saltelli_page_field('hub_risorse_card2_desc', 'Risposte sintetiche alle domande che riceviamo più spesso, divise per area di pratica.')); ?>
                    </p>
                    <p class="sl-mono sl-hub-card__meta">
                        <?php
                        printf(
                            esc_html(_n('%s domanda', '%s domande', $sl_faq_count, 'saltelli')),
                            esc_html(number_format_i18n($sl_faq_count))
                        );
                        ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php echo esc_html(saltelli_page_field('hub_risorse_card2_cta', 'Apri le FAQ →')); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/risorse/glossario-legale/')); ?>">
                    <p class="sl-mono sl-hub-card__num">03 / 04</p>
                    <h2 class="sl-hub-card__title"><?php echo esc_html(saltelli_page_field('hub_risorse_card3_title', 'Glossario legale')); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php echo esc_html(saltelli_page_field('hub_risorse_card3_desc', 'Termini giuridici spiegati con linguaggio piano. Ricerca alfabetica, voci compatte.')); ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php echo esc_html(saltelli_page_field('hub_risorse_card3_cta', 'Sfoglia →')); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/risorse/guide-gratuite/')); ?>">
                    <p class="sl-mono sl-hub-card__num">04 / 04</p>
                    <h2 class="sl-hub-card__title"><?php echo esc_html(saltelli_page_field('hub_risorse_card4_title', 'Guide gratuite')); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php echo esc_html(saltelli_page_field('hub_risorse_card4_desc', 'PDF scaricabili: cosa fare se ricevi una cartella, come affrontare un licenziamento, separazione.')); ?>
                    </p>
                    <p class="sl-mono sl-hub-card__meta">
                        <?php if ($sl_guide_count > 0) :
                            printf(
                                esc_html(_n('%s guida', '%s guide', $sl_guide_count, 'saltelli')),
                                esc_html(number_format_i18n($sl_guide_count))
                            );
                        else :
                            echo esc_html(saltelli_page_field('hub_risorse_card4_empty_text', 'In arrivo'));
                        endif; ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php echo esc_html(saltelli_page_field('hub_risorse_card4_cta', 'Scarica →')); ?></span>
                </a>
            </li>
        </ul>
    </div>
</section>
