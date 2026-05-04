<?php
/**
 * Template part: page-info-shared.php
 *
 * Render condiviso per le 5 info-page+ (guide-gratuite, come-lavoriamo,
 * prima-consulenza, lavora-con-noi, richiedi-preventivo).
 *
 * Sostituisce il blocco hardcoded `is_page([...])` di page.php pre-Wave3.
 * Tutti i fields letti da ACF Field Group `group_info_shared_v1` (Wave 1)
 * popolato in Wave 2.
 *
 * @package Saltelli
 * @since 1.0.0 Wave 3
 */
defined('ABSPATH') || exit;

$pid = get_the_ID();
$slug = get_post_field('post_name', $pid);

// Hero
$hero_eyebrow = saltelli_field('hero_eyebrow', $pid, '');
$hero_h1_pre  = saltelli_field('hero_h1_pre', $pid, '');
$hero_h1_em   = saltelli_field('hero_h1_em', $pid, '');
$hero_lede    = saltelli_field('hero_lede', $pid, '');

// Aside trust box
$aside_eyebrow   = saltelli_field('aside_eyebrow', $pid, '');
$aside_h3        = saltelli_field('aside_h3', $pid, '');
$aside_p         = saltelli_field('aside_p', $pid, '');
$aside_cta_label = saltelli_field('aside_cta_label', $pid, '');
$aside_cta_url   = saltelli_field('aside_cta_url', $pid, '');

// Body editorial
$body_content = saltelli_field('body_content', $pid, '');

// CTA finale
$cta_final_eyebrow   = saltelli_field('cta_final_eyebrow', $pid, '§ Pronto?');
$cta_final_h2        = saltelli_field('cta_final_h2', $pid, '');
$cta_final_p         = saltelli_field('cta_final_p', $pid, '');
$cta_final_cta_label = saltelli_field('cta_final_cta_label', $pid, 'Prenota un incontro');
$cta_final_cta_url   = saltelli_field('cta_final_cta_url', $pid, '/contatti/');
$cta_final_trust     = saltelli_field('cta_final_trust', $pid, 'Risposta entro 24 ore · Riservatezza assoluta');

// Aside trust list — dal aside_p split su " · " per render compat con markup pre-Wave3.
// Pre-Wave3 il template usava trust_list array; ora il valore è singola stringa concatenata.
$aside_trust_list = $aside_p !== '' ? array_filter(array_map('trim', explode('·', str_replace(' · ', '·', $aside_p)))) : [];

// trust_headline alias dell'aside_h3 per back-compat markup.
$trust_headline = $aside_h3;
?>
<article class="sl-info-page sl-info-page--<?php echo esc_attr($slug); ?>">

    <header class="sl-info-page__hero sl-page-hero sl-page-hero--compact">
        <div class="sl-info-page__hero-text">
            <?php saltelli_render_breadcrumb(); ?>
            <?php if ($hero_eyebrow !== '') : ?>
                <div class="sl-mono sl-info-page__hero-eyebrow"><?php echo esc_html($hero_eyebrow); ?></div>
            <?php endif; ?>
            <h1 class="sl-info-page__h1" data-split-reveal>
                <?php echo esc_html($hero_h1_pre); ?>
                <?php if ($hero_h1_em !== '') : ?><br><em><?php echo esc_html($hero_h1_em); ?></em><?php endif; ?>
            </h1>
            <?php if ($hero_lede !== '') : ?>
                <p class="sl-info-page__lede"><?php echo esc_html($hero_lede); ?></p>
            <?php endif; ?>
        </div>
        <aside class="sl-info-page__trust">
            <?php if ($aside_eyebrow !== '') : ?>
                <div class="sl-mono sl-info-page__trust-eyebrow"><?php echo esc_html($aside_eyebrow); ?></div>
            <?php endif; ?>
            <?php if ($trust_headline !== '') : ?>
                <p class="sl-info-page__trust-headline"><?php echo esc_html($trust_headline); ?></p>
            <?php endif; ?>
            <?php if (!empty($aside_trust_list)) : ?>
                <ul class="sl-info-page__trust-list" role="list">
                    <?php foreach ($aside_trust_list as $li) : ?>
                        <li><?php echo esc_html(trim($li)); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </aside>
    </header>

    <section class="sl-info-page__body">
        <div class="sl-mono sl-info-page__body-eyebrow"><?php esc_html_e('§ 01 — Approfondimento', 'saltelli'); ?></div>
        <div class="sl-info-page__prose">
            <?php
            // Priority 1: ACF body_content (Wave 2 popolato).
            // Priority 2: get_the_content() (post_content WP nativo).
            // Priority 3: empty (richiedi-preventivo non ha body editorial).
            if ($body_content !== '') {
                echo wp_kses_post($body_content);
            } elseif (get_the_content() !== '') {
                the_content();
            }
            ?>
        </div>
    </section>

    <section class="sl-info-page__cta">
        <div class="sl-info-page__cta-inner">
            <div class="sl-mono sl-info-page__cta-eyebrow"><?php echo esc_html($cta_final_eyebrow); ?></div>
            <div>
                <h2 class="sl-info-page__cta-h2"><?php echo esc_html($cta_final_h2); ?></h2>
                <?php if ($cta_final_p !== '') : ?>
                    <p class="sl-info-page__cta-p"><?php echo esc_html($cta_final_p); ?></p>
                <?php endif; ?>
                <a class="sl-info-page__cta-btn" href="<?php echo esc_url($cta_final_cta_url); ?>">
                    <span><?php echo esc_html($cta_final_cta_label); ?></span>
                    <span class="arrow" aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </section>

</article>
