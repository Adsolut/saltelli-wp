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
    // Wave P7 routing post-consolidamento (chi-siamo = lo-studio):
    //   /chi-siamo/        → page editoriale (page-lo-studio.php) — Page 2811 rinominata slug
    //   /aree-di-pratica/  → HUB Aree (page-aree-di-pratica-hub.php)
    //   /risorse/          → HUB Risorse (page-risorse-hub.php)
    //   /costi-e-consulenze/ → HUB Costi (page-costi-e-consulenze-hub.php)
    // NB: 'lo-studio' slug non esiste più (redirect 301 → /chi-siamo/), il check resta
    //     per safety se per qualche motivo l'URL viene servito direttamente.
    $sl_chi_siamo = is_page('chi-siamo') || is_page('lo-studio');
    $sl_casi      = is_page('casi');
    $sl_hub_any   = is_page('aree-di-pratica')
        || is_page('risorse')
        || is_page('costi-e-consulenze');
    ?>
    <article <?php post_class('sl-page' . ($sl_chi_siamo ? ' sl-chi-siamo' : '') . ($sl_casi ? ' sl-casi-page' : '') . ($sl_hub_any ? ' sl-page--hub' : '')); ?>>

        <?php
        // Wave 5 — hub pages (precedenza prima dei legacy is_page case).
        if ($sl_chi_siamo) {
            // Wave P7 consolidamento: /chi-siamo/ rende ora la pagina editoriale completa
            // (ex "Lo Studio", Page 2811) — l'hub 3-card legacy (page-chi-siamo-hub.php) è dismesso.
            get_template_part('template-parts/page', 'lo-studio');
        } elseif (is_page('aree-di-pratica')) {
            get_template_part('template-parts/page', 'aree-di-pratica-hub');
        } elseif (is_page('risorse')) {
            get_template_part('template-parts/page', 'risorse-hub');
        } elseif (is_page('costi-e-consulenze')) {
            get_template_part('template-parts/page', 'costi-e-consulenze-hub');
        } elseif ($sl_casi) {
            get_template_part('template-parts/page', 'casi');
        } elseif (is_page('contatti')) {
            get_template_part('template-parts/page', 'contatti');
        } elseif (is_page('glossario-legale')) {
            // Render delegato a inc/wave3-glossario.php (legacy specialized).
            include SALTELLI_THEME_DIR . '/inc/wave3-glossario.php';
        } elseif (is_page(['faq', 'domande-frequenti'])) {
            get_template_part('template-parts/page', 'faq');
        } elseif (is_page(['guide-gratuite', 'come-lavoriamo', 'prima-consulenza', 'lavora-con-noi', 'richiedi-preventivo', 'prenota-appuntamento'])) {
            // Wave Elena FB Batch 3 #22 — prenota-appuntamento layout uniforma richiedi-preventivo.
            // Page 2714 (prenota-appuntamento) ora renderizza con il template editoriale shared
            // (hero 8/4 + trust aside + body + CTA navy finale) invece del default page.php fallback.
            // Backward-compat: il template-part legge prenota_intro (SCF group_prenota_appuntamento_v1
            // legacy, ancora attivo per data-preservation) come fallback per body_content quando vuoto.
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
                    <div class="sl-page__prose"><?php
                    /* Wave Elena FB Batch 3 #22 — /prenota-appuntamento/ legacy block rimosso.
                       Page 2714 ora rotta da page-info-shared.php (vedi router sopra). Questo
                       blocco resta come default fallback per qualsiasi altra Page senza routing
                       dedicato (es. Pages nuove create via wp-admin senza slug noto). */
                    the_content();
                    ?></div>
                </div>
            </section>
            <?php
        }
        ?>

    </article>
    <?php
endwhile;

get_footer();
