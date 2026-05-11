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
        <p class="sl-mono sl-hub-hero__eyebrow"><?php echo esc_html(saltelli_page_field('hub_costi_eyebrow', '§ Costi e consulenze')); ?></p>
        <h1 class="sl-page__title sl-hub-hero__h1" id="hub-costi-h1" data-split-reveal>
            <?php
            $sl_h1 = esc_html(saltelli_page_field('hub_costi_h1_main', 'Trasparenza,')) . '<br>'
                . '<em>' . esc_html(saltelli_page_field('hub_costi_h1_em', 'non sorprese.')) . '</em>';
            echo wp_kses(saltelli_split_h1_words($sl_h1), [
                'span' => ['class' => true, 'data-i' => true],
                'em'   => [],
                'br'   => [],
            ]);
            ?>
        </h1>
        <p class="sl-page__lede sl-hub-hero__lede">
            <?php echo esc_html(saltelli_page_field('hub_costi_intro', 'Modalità di consulenza, scenari di costo, processo di lavoro. Quello che chiediamo lo scrivi prima.')); ?>
        </p>
    </div>
</section>

<section class="sl-hub-grid-section" aria-label="<?php esc_attr_e('Costi e consulenze', 'saltelli'); ?>">
    <div class="sl-container">
        <ul class="sl-hub-grid sl-hub-grid--4">
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/costi-e-consulenze/costi/')); ?>">
                    <p class="sl-mono sl-hub-card__num">01 / 04</p>
                    <h2 class="sl-hub-card__title"><?php echo esc_html(saltelli_page_field('hub_costi_card1_title', 'Costi')); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php echo esc_html(saltelli_page_field('hub_costi_card1_desc', 'Tre scenari tipo, range chiari, modalità di pagamento. Niente preventivi a sorpresa.')); ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php echo esc_html(saltelli_page_field('hub_costi_card1_cta', 'Scopri i costi →')); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/costi-e-consulenze/prima-consulenza/')); ?>">
                    <p class="sl-mono sl-hub-card__num">02 / 04</p>
                    <h2 class="sl-hub-card__title"><?php echo esc_html(saltelli_page_field('hub_costi_card2_title', 'Prima consulenza')); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php echo esc_html(saltelli_page_field('hub_costi_card2_desc', 'Formati disponibili (telefono, video, in studio), durata, costo, cosa portare.')); ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php echo esc_html(saltelli_page_field('hub_costi_card2_cta', 'Prenota →')); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/costi-e-consulenze/come-lavoriamo/')); ?>">
                    <p class="sl-mono sl-hub-card__num">03 / 04</p>
                    <h2 class="sl-hub-card__title"><?php echo esc_html(saltelli_page_field('hub_costi_card3_title', 'Come lavoriamo')); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php echo esc_html(saltelli_page_field('hub_costi_card3_desc', 'Il nostro processo dalla prima consulenza al provvedimento. Tappe, ruoli, tempi attesi.')); ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php echo esc_html(saltelli_page_field('hub_costi_card3_cta', 'Approfondisci →')); ?></span>
                </a>
            </li>
            <li class="sl-hub-card">
                <a class="sl-hub-card__link" href="<?php echo esc_url(home_url('/costi-e-consulenze/richiedi-preventivo/')); ?>">
                    <p class="sl-mono sl-hub-card__num">04 / 04</p>
                    <h2 class="sl-hub-card__title"><?php echo esc_html(saltelli_page_field('hub_costi_card4_title', 'Richiedi preventivo')); ?></h2>
                    <p class="sl-hub-card__desc">
                        <?php echo esc_html(saltelli_page_field('hub_costi_card4_desc', 'Form per ricevere un preventivo personalizzato in 48 ore lavorative. Niente call obbligatoria.')); ?>
                    </p>
                    <span class="sl-mono sl-hub-card__cta"><?php echo esc_html(saltelli_page_field('hub_costi_card4_cta', 'Richiedi →')); ?></span>
                </a>
            </li>
        </ul>
    </div>
</section>
