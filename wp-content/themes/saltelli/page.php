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

        <section class="sl-page__content">
            <div class="sl-container">
                <div class="sl-page__prose"><?php the_content(); ?></div>
            </div>
        </section>

        <?php if (is_page('contatti')) : ?>
            <section class="sl-page-contatti__map" aria-labelledby="contatti-map-h">
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
                            src="https://www.openstreetmap.org/export/embed.html?bbox=14.235%2C40.828%2C14.243%2C40.832&amp;layer=mapnik&amp;marker=40.830%2C14.239"
                            width="100%"
                            height="400"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="<?php esc_attr_e('Mappa Studio Legale Saltelli — Via Vannella Gaetani 27, 80121 Napoli', 'saltelli'); ?>">
                        </iframe>
                    </div>
                    <div class="sl-page-contatti__map-meta">
                        <a class="sl-mono sl-page-contatti__map-link"
                           href="https://www.openstreetmap.org/?mlat=40.830&amp;mlon=14.239#map=17/40.830/14.239"
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

    </article>
    <?php
endwhile;

get_footer();
