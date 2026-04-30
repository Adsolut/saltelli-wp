<?php
/**
 * Template: Page (standard).
 *
 * @package Saltelli
 */
get_header();

while (have_posts()) :
    the_post();
    ?>
    <article <?php post_class('sl-page'); ?>>

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

                <h1 class="sl-page__title"><?php the_title(); ?></h1>
            </div>
        </header>

        <?php if (is_page('contatti')) : ?>
            <section class="sl-page-contatti__form" aria-labelledby="contatti-form-h">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Scrivici', 'saltelli'); ?></div>
                    <h2 class="sl-section-title" id="contatti-form-h">
                        <?php esc_html_e('Raccontaci il tuo problema', 'saltelli'); ?>
                    </h2>
                    <p class="sl-page-contatti__form-lede">
                        <?php esc_html_e('Compila i campi qui sotto. La prima consulenza conoscitiva è gratuita e dura circa 30 minuti. Risponderemo entro 24 ore.', 'saltelli'); ?>
                    </p>
                    <?php
                    if (shortcode_exists('contact-form-7')) {
                        // Form ID locale 2703 (droplet) — shortcode tag-aware via slug fallback
                        $form_post = get_page_by_path('saltelli-contatti', OBJECT, 'wpcf7_contact_form');
                        if ($form_post) {
                            echo do_shortcode('[contact-form-7 id="' . (int) $form_post->ID . '" title="Saltelli Contatti"]');
                        } else {
                            echo '<p class="sl-mono">' . esc_html__('Modulo non disponibile. Scrivici a info@studiolegalesaltelli.it.', 'saltelli') . '</p>';
                        }
                    } else {
                        echo '<p class="sl-mono">' . esc_html__('Plugin form non attivo. Scrivici a info@studiolegalesaltelli.it.', 'saltelli') . '</p>';
                    }
                    ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="sl-page__content">
            <div class="sl-container">
                <div class="sl-page__prose"><?php the_content(); ?></div>
            </div>
        </section>

        <?php if (is_page('contatti')) : ?>
            <section class="sl-page-contatti__map" id="mappa" aria-labelledby="contatti-map-h">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Sede', 'saltelli'); ?></div>
                    <h2 class="sl-section-title" id="contatti-map-h">
                        <?php esc_html_e('Dove trovarci', 'saltelli'); ?>
                    </h2>
                    <p class="sl-page-contatti__map-lede">
                        <?php esc_html_e('Studio Legale Saltelli & Partners — Chiaia, Napoli. Si riceve solo su appuntamento, prima consulenza conoscitiva gratuita.', 'saltelli'); ?>
                    </p>
                    <div class="sl-page-contatti__map-wrap">
                        <iframe
                            src="https://www.openstreetmap.org/export/embed.html?bbox=14.232%2C40.829%2C14.240%2C40.835&amp;layer=mapnik&amp;marker=40.832%2C14.235"
                            width="100%"
                            height="400"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="<?php esc_attr_e('Mappa Studio Legale Saltelli — Via Vannella Gaetani 27, 80121 Napoli', 'saltelli'); ?>">
                        </iframe>
                    </div>
                    <div class="sl-page-contatti__map-meta">
                        <a class="sl-mono sl-page-contatti__map-link"
                           href="https://www.openstreetmap.org/?mlat=40.832&amp;mlon=14.235#map=17/40.832/14.235"
                           target="_blank" rel="noopener">
                            <?php esc_html_e('Apri in OpenStreetMap', 'saltelli'); ?> →
                        </a>
                        <span class="sl-mono">Via Vannella Gaetani 27 · 80121 Napoli · Chiaia</span>
                    </div>
                </div>
            </section>

            <section class="sl-page-contatti__cta" aria-labelledby="contatti-cta-h">
                <div class="sl-container">
                    <div class="sl-mono sl-contact__eyebrow">
                        <?php esc_html_e('Prima consulenza conoscitiva gratuita · Risposta entro 24 ore', 'saltelli'); ?>
                    </div>
                    <h2 class="sl-section-title" id="contatti-cta-h">
                        <?php esc_html_e('Scrivici, ti rispondiamo entro 24 ore', 'saltelli'); ?>
                    </h2>
                    <p class="sl-page-contatti__cta-lede">
                        <?php esc_html_e('Ogni mail viene letta direttamente dall\'avvocato di riferimento. Per richieste urgenti, chiama lo studio negli orari di apertura.', 'saltelli'); ?>
                    </p>
                    <div class="sl-page-contatti__cta-actions">
                        <a class="sl-btn sl-btn--primary" href="mailto:info@studiolegalesaltelli.it">
                            <span><?php esc_html_e('Scrivi una mail', 'saltelli'); ?></span>
                            <span class="arrow" aria-hidden="true">→</span>
                        </a>
                        <a class="sl-btn" href="tel:+390811813119">
                            <span><?php esc_html_e('Chiama lo studio', 'saltelli'); ?></span>
                            <span class="arrow" aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if (is_page('casi') && function_exists('saltelli_homepage_cases')) :
            $cases = saltelli_homepage_cases();
            if (!empty($cases)) : ?>
            <section class="sl-cases sl-cases--archive" aria-labelledby="casi-archive-h">
                <div class="sl-container">
                    <header class="sl-section-head">
                        <div class="sl-mono">§ <?php esc_html_e('Casi rappresentativi', 'saltelli'); ?></div>
                        <h2 class="sl-section-title" id="casi-archive-h">
                            <?php esc_html_e('Vittorie selezionate', 'saltelli'); ?>
                        </h2>
                        <p class="sl-cases__lede">
                            <?php esc_html_e('Identificativi anonimizzati per riservatezza, documentazione integrale visionabile in studio su richiesta.', 'saltelli'); ?>
                        </p>
                    </header>
                    <ol class="sl-cases__list">
                        <?php foreach ($cases as $case) : ?>
                            <li class="sl-cases__row">
                                <div class="sl-mono sl-cases__id"><?php echo esc_html($case['identifier']); ?></div>
                                <p class="sl-cases__desc"><?php echo esc_html($case['descrizione']); ?></p>
                                <div class="sl-mono sl-cases__outcome"><?php echo esc_html($case['outcome']); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </section>

            <section class="sl-cases__cta" aria-labelledby="casi-cta-h">
                <div class="sl-container">
                    <div class="sl-mono sl-contact__eyebrow">
                        <?php esc_html_e('Prima consulenza conoscitiva gratuita · Risposta entro 24 ore', 'saltelli'); ?>
                    </div>
                    <h2 class="sl-section-title" id="casi-cta-h">
                        <?php esc_html_e('Hai un caso simile?', 'saltelli'); ?>
                    </h2>
                    <p class="sl-cases__cta-lede">
                        <?php esc_html_e('Raccontaci la tua pratica. Trenta minuti di consulenza conoscitiva, gratuita e senza impegno.', 'saltelli'); ?>
                    </p>
                    <a class="sl-btn sl-btn--primary" href="<?php echo esc_url(home_url('/contatti/')); ?>">
                        <span><?php esc_html_e('Prenota una consulenza', 'saltelli'); ?></span>
                        <span class="arrow" aria-hidden="true">→</span>
                    </a>
                </div>
            </section>
        <?php endif; endif; ?>

    </article>
    <?php
endwhile;

get_footer();
