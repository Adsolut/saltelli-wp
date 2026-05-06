<?php
/**
 * Template part: page-costi-e-consulenze-hub.php
 *
 * Render della page hub /costi-e-consulenze/ — pagina madre Costi e Consulenze.
 * Wave 5 IA refactor.
 *
 * Layout: hero editoriale + grid 4 cards (costi, prima-consulenza, come-lavoriamo, richiedi-preventivo).
 *
 * @package Saltelli
 * @since 1.1.0 Wave 5
 */
defined('ABSPATH') || exit;
?>

<section class="sl-page-hero sl-hub-hero" aria-labelledby="hub-costi-h1">
    <div class="sl-container sl-hub-hero__inner">
        <?php saltelli_render_breadcrumb(); ?>
        <p class="sl-mono sl-hub-hero__eyebrow"><?php esc_html_e('§ Costi e consulenze', 'saltelli'); ?></p>
        <h1 class="sl-page__title sl-hub-hero__h1" id="hub-costi-h1" data-split-reveal>
            <?php
            $sl_h1 = esc_html__('Trasparenza,', 'saltelli') . '<br>'
                . '<em>' . esc_html__('non sorprese.', 'saltelli') . '</em>';
            echo wp_kses(saltelli_split_h1_words($sl_h1), [
                'span' => ['class' => true, 'data-i' => true],
                'em'   => [],
                'br'   => [],
            ]);
            ?>
        </h1>
        <p class="sl-page__lede sl-hub-hero__lede">
            <?php esc_html_e('Modalità di consulenza, scenari di costo, processo di lavoro. Quello che chiediamo lo scrivi prima.', 'saltelli'); ?>
        </p>
    </div>
</section>

<section class="sl-hub-grid-section" aria-label="<?php esc_attr_e('Costi e consulenze', 'saltelli'); ?>">
    <div class="sl-container">
        <ul class="sl-hub-grid sl-hub-grid--4">
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/costi-e-consulenze/costi/')); ?>">
                    <p class="sl-mono sl-hub-card__num">01 / 04</p>
                    <h2 class="sl-hub-card__title"><?php esc_html_e('Costi', 'saltelli'); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php esc_html_e('Tre scenari tipo, range chiari, modalità di pagamento. Niente preventivi a sorpresa.', 'saltelli'); ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php esc_html_e('Scopri i costi →', 'saltelli'); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/costi-e-consulenze/prima-consulenza/')); ?>">
                    <p class="sl-mono sl-hub-card__num">02 / 04</p>
                    <h2 class="sl-hub-card__title"><?php esc_html_e('Prima consulenza', 'saltelli'); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php esc_html_e('Formati disponibili (telefono, video, in studio), durata, costo, cosa portare.', 'saltelli'); ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php esc_html_e('Prenota →', 'saltelli'); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/costi-e-consulenze/come-lavoriamo/')); ?>">
                    <p class="sl-mono sl-hub-card__num">03 / 04</p>
                    <h2 class="sl-hub-card__title"><?php esc_html_e('Come lavoriamo', 'saltelli'); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php esc_html_e('Il nostro processo dalla prima consulenza al provvedimento. Tappe, ruoli, tempi attesi.', 'saltelli'); ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php esc_html_e('Approfondisci →', 'saltelli'); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/costi-e-consulenze/richiedi-preventivo/')); ?>">
                    <p class="sl-mono sl-hub-card__num">04 / 04</p>
                    <h2 class="sl-hub-card__title"><?php esc_html_e('Richiedi preventivo', 'saltelli'); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php esc_html_e('Form per ricevere un preventivo personalizzato in 48 ore lavorative. Niente call obbligatoria.', 'saltelli'); ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php esc_html_e('Richiedi →', 'saltelli'); ?></span>
                </a>
            </li>
        </ul>
    </div>
</section>
