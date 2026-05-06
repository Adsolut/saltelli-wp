<?php
/**
 * Template: Page (router refactored Wave 3).
 *
 * Dispatcher → template-parts/page-{slug}.php per le 9 page WP custom.
 * I 6 template-parts leggono i fields ACF popolati in Wave 2 + querano i
 * CPT items (saltelli_modalita/scenario/principio/trust/faq/caso). Fallback
 * grazioso: se ACF empty, hardcoded restano via saltelli_field() default.
 *
 * Pre-Wave3: page.php era 1274 righe con tutti i blocchi is_page() inline.
 * Post-Wave3: ~50 righe router + template-parts modulari.
 *
 * @package Saltelli
 * @since 1.0.0 Wave 3 (refactor)
 */

get_header();

while (have_posts()) :
    the_post();
    // Wave 5 routing post-rename:
    //   /chi-siamo/        → HUB Chi Siamo (page-chi-siamo-hub.php)
    //   /chi-siamo/lo-studio/ → page Lo Studio (page-lo-studio.php)
    //   /aree-di-pratica/  → HUB Aree (page-aree-di-pratica-hub.php)
    //   /risorse/          → HUB Risorse (page-risorse-hub.php)
    //   /costi-e-consulenze/ → HUB Costi (page-costi-e-consulenze-hub.php)
    $sl_lo_studio = is_page('lo-studio');
    $sl_chi_siamo_hub = is_page('chi-siamo');
    $sl_casi      = is_page('casi');
    $sl_hub_any   = $sl_chi_siamo_hub
        || is_page('aree-di-pratica')
        || is_page('risorse')
        || is_page('costi-e-consulenze');
    ?>
    <article <?php post_class('sl-page' . ($sl_lo_studio ? ' sl-chi-siamo' : '') . ($sl_casi ? ' sl-casi-page' : '') . ($sl_hub_any ? ' sl-page--hub' : '')); ?>>

        <?php
        // Wave 5 — hub pages (precedenza prima dei legacy is_page case).
        if ($sl_chi_siamo_hub) {
            get_template_part('template-parts/page', 'chi-siamo-hub');
        } elseif (is_page('aree-di-pratica')) {
            get_template_part('template-parts/page', 'aree-di-pratica-hub');
        } elseif (is_page('risorse')) {
            get_template_part('template-parts/page', 'risorse-hub');
        } elseif (is_page('costi-e-consulenze')) {
            get_template_part('template-parts/page', 'costi-e-consulenze-hub');
        } elseif ($sl_lo_studio) {
            get_template_part('template-parts/page', 'lo-studio');
        } elseif ($sl_casi) {
            get_template_part('template-parts/page', 'casi');
        } elseif (is_page('contatti')) {
            get_template_part('template-parts/page', 'contatti');
        } elseif (is_page('glossario-legale')) {
            // Render delegato a inc/wave3-glossario.php (legacy specialized).
            include SALTELLI_THEME_DIR . '/inc/wave3-glossario.php';
        } elseif (is_page(['faq', 'domande-frequenti'])) {
            get_template_part('template-parts/page', 'faq');
        } elseif (is_page(['guide-gratuite', 'come-lavoriamo', 'prima-consulenza', 'lavora-con-noi', 'richiedi-preventivo'])) {
            get_template_part('template-parts/page', 'info-shared');
        } elseif (is_page('costi')) {
            get_template_part('template-parts/page', 'costi');
        } else {
            // Default fallback: standard WordPress page rendering.
            ?>
            <header class="sl-page__hero">
                <div class="sl-container">
                    <?php
                    $chain = saltelli_get_breadcrumb_chain();
                    if (!empty($chain) && count($chain) > 1) :
                        ?>
                        <nav class="sl-mono sl-page__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'saltelli'); ?>">
                            <?php foreach ($chain as $idx => $node) :
                                if ($idx > 0) echo ' / ';
                                if (!empty($node['url'])) :
                                    ?>
                                    <a href="<?php echo esc_url($node['url']); ?>"><?php echo esc_html($node['name']); ?></a>
                                <?php else : ?>
                                    <span><?php echo esc_html($node['name']); ?></span>
                                <?php endif;
                            endforeach; ?>
                        </nav>
                    <?php endif; ?>
                    <h1 class="sl-page__title" data-split-reveal><?php echo wp_kses(saltelli_split_h1_words(get_the_title()), ['span' => ['class' => true, 'data-i' => true]]); ?></h1>
                </div>
            </header>
            <section class="sl-page__content">
                <div class="sl-container">
                    <div class="sl-page__prose"><?php the_content(); ?></div>
                </div>
            </section>
            <?php
        }
        ?>

    </article>
    <?php
endwhile;

get_footer();
