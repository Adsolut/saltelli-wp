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
?>

<section class="sl-page-hero sl-hub-hero" aria-labelledby="hub-risorse-h1">
    <div class="sl-container sl-hub-hero__inner">
        <?php saltelli_render_breadcrumb(); ?>
        <p class="sl-mono sl-hub-hero__eyebrow"><?php esc_html_e('§ Risorse', 'saltelli'); ?></p>
        <h1 class="sl-page__title sl-hub-hero__h1" id="hub-risorse-h1" data-split-reveal>
            <?php
            $sl_h1 = esc_html__('Approfondire,', 'saltelli') . '<br>'
                . '<em>' . esc_html__('senza fretta.', 'saltelli') . '</em>';
            echo wp_kses(saltelli_split_h1_words($sl_h1), [
                'span' => ['class' => true, 'data-i' => true],
                'em'   => [],
                'br'   => [],
            ]);
            ?>
        </h1>
        <p class="sl-page__lede sl-hub-hero__lede">
            <?php esc_html_e('Articoli, glossario, domande frequenti, guide gratuite. Materiale per orientarti prima di prenotare una consulenza.', 'saltelli'); ?>
        </p>
    </div>
</section>

<section class="sl-hub-grid-section" aria-label="<?php esc_attr_e('Risorse', 'saltelli'); ?>">
    <div class="sl-container">
        <ul class="sl-hub-grid sl-hub-grid--4">
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/risorse/blog/')); ?>">
                    <p class="sl-mono sl-hub-card__num">01 / 04</p>
                    <h2 class="sl-hub-card__title"><?php esc_html_e('Blog', 'saltelli'); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php esc_html_e('Articoli scritti dai nostri avvocati su sentenze, novità normative, casi reali.', 'saltelli'); ?>
                    </p>
                    <p class="sl-mono sl-hub-card__meta">
                        <?php
                        printf(
                            esc_html(_n('%s articolo', '%s articoli', $sl_blog_count, 'saltelli')),
                            esc_html(number_format_i18n($sl_blog_count))
                        );
                        ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php esc_html_e('Leggi →', 'saltelli'); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/risorse/domande-frequenti/')); ?>">
                    <p class="sl-mono sl-hub-card__num">02 / 04</p>
                    <h2 class="sl-hub-card__title"><?php esc_html_e('Domande frequenti', 'saltelli'); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php esc_html_e('Risposte sintetiche alle domande che riceviamo più spesso, divise per area di pratica.', 'saltelli'); ?>
                    </p>
                    <p class="sl-mono sl-hub-card__meta">
                        <?php
                        printf(
                            esc_html(_n('%s domanda', '%s domande', $sl_faq_count, 'saltelli')),
                            esc_html(number_format_i18n($sl_faq_count))
                        );
                        ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php esc_html_e('Apri le FAQ →', 'saltelli'); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/risorse/glossario-legale/')); ?>">
                    <p class="sl-mono sl-hub-card__num">03 / 04</p>
                    <h2 class="sl-hub-card__title"><?php esc_html_e('Glossario legale', 'saltelli'); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php esc_html_e('Termini giuridici spiegati con linguaggio piano. Ricerca alfabetica, voci compatte.', 'saltelli'); ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php esc_html_e('Sfoglia →', 'saltelli'); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/risorse/guide-gratuite/')); ?>">
                    <p class="sl-mono sl-hub-card__num">04 / 04</p>
                    <h2 class="sl-hub-card__title"><?php esc_html_e('Guide gratuite', 'saltelli'); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php esc_html_e('PDF scaricabili: cosa fare se ricevi una cartella, come affrontare un licenziamento, separazione.', 'saltelli'); ?>
                    </p>
                    <p class="sl-mono sl-hub-card__meta">
                        <?php
                        printf(
                            esc_html(_n('%s guida', '%s guide', $sl_guide_count, 'saltelli')),
                            esc_html(number_format_i18n($sl_guide_count))
                        );
                        ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php esc_html_e('Scarica →', 'saltelli'); ?></span>
                </a>
            </li>
        </ul>
    </div>
</section>
