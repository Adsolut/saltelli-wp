<?php
/**
 * Template: Page (standard).
 *
 * @package Saltelli
 */
get_header();

while (have_posts()) :
    the_post();
    $sl_chi_siamo = is_page('chi-siamo');
    ?>
    <article <?php post_class('sl-page' . ($sl_chi_siamo ? ' sl-chi-siamo' : '')); ?>>

        <?php if ($sl_chi_siamo) :
            $sl_lawyers_chi = get_posts([
                'post_type'   => 'avvocato',
                'numberposts' => 4,
                'orderby'     => ['menu_order' => 'ASC', 'date' => 'ASC'],
            ]);
            $sl_timeline = [
                ['y' => '1999', 't' => __('Fondazione', 'saltelli'),         'd' => __('Emiliano Saltelli apre lo studio in Via Vannella Gaetani, focalizzato sul contenzioso tributario.', 'saltelli')],
                ['y' => '2007', 't' => __('Ingresso di Fabiana', 'saltelli'),'d' => __('Si aggiunge la prima associate — area diritto del lavoro.', 'saltelli')],
                ['y' => '2014', 't' => __('Apertura LGBTQ+', 'saltelli'),    'd' => __('Antonia Battista inaugura una pratica dedicata, prima a Napoli sud.', 'saltelli')],
                ['y' => '2019', 't' => __("Vent'anni", 'saltelli'),          'd' => __("Lo studio passa da 2 a 4 professionisti stabili. Atelier a tutti gli effetti.", 'saltelli')],
                ['y' => '2024', 't' => __('Cassazione + AGE', 'saltelli'),   'd' => __('Annullamento cartella €240k. Conferma in Cassazione su licenziamento illegittimo.', 'saltelli')],
                ['y' => '2026', 't' => __('Oggi', 'saltelli'),               'd' => __('19 aree presidiate, 4 professionisti, un solo atelier.', 'saltelli')],
            ];
            ?>

            <section class="sl-chi-siamo__hero" aria-labelledby="chi-siamo-h1">
                <div class="sl-container sl-chi-siamo__hero-grid">
                    <aside class="sl-chi-siamo__hero-aside">
                        <div class="sl-mono sl-chi-siamo__eyebrow"><?php esc_html_e('§ Lo studio · Chi siamo', 'saltelli'); ?></div>
                        <p class="sl-mono sl-chi-siamo__hero-meta">
                            <?php esc_html_e('Un atelier', 'saltelli'); ?><br>
                            <?php esc_html_e('di quattro avvocati', 'saltelli'); ?><br>
                            <?php esc_html_e('in Via Vannella Gaetani 27', 'saltelli'); ?><br>
                            <?php esc_html_e('Chiaia · Napoli', 'saltelli'); ?><br>
                            <?php esc_html_e('Dal 1999', 'saltelli'); ?>
                        </p>
                    </aside>
                    <h1 class="sl-chi-siamo__h1" id="chi-siamo-h1">
                        <?php esc_html_e('Un atelier', 'saltelli'); ?><br>
                        <?php esc_html_e('di quattro', 'saltelli'); ?><br>
                        <em><?php esc_html_e('professionisti.', 'saltelli'); ?></em>
                    </h1>
                </div>
            </section>

            <section class="sl-chi-siamo__lede" aria-label="<?php esc_attr_e('Lede editoriale', 'saltelli'); ?>">
                <div class="sl-container sl-chi-siamo__lede-grid">
                    <div class="sl-mono">§ 01 — <?php esc_html_e('Lede', 'saltelli'); ?></div>
                    <div class="sl-chi-siamo__prose sl-chi-siamo__prose--dropcap">
                        <p>
                            <span class="sl-chi-siamo__dropcap" aria-hidden="true">U</span>n atelier di quattro professionisti che da oltre vent'anni accompagna famiglie e imprese di Napoli attraverso le materie di cui si occupa: il diritto tributario di Emiliano, il diritto del lavoro di Fabiana, la tutela LGBTQ+ in materia di famiglia di Antonia, il condominiale e immobiliare di Stefano.
                        </p>
                        <p>
                            <?php esc_html_e("Crediamo che il diritto sia, prima di tutto, un'arte di ascolto. Le carte vengono dopo. Per questo non offriamo pacchetti né formule: ogni cliente è una storia, e ogni storia merita il tempo di essere capita.", 'saltelli'); ?>
                        </p>
                    </div>
                </div>
            </section>

            <section class="sl-chi-siamo__plate" aria-hidden="true">
                <div class="sl-container">
                    <div class="sl-chi-siamo__plate-frame">
                        <div class="sl-mono sl-chi-siamo__plate-tl"><?php esc_html_e('Plate I · Facciata studio', 'saltelli'); ?></div>
                        <div class="sl-mono sl-chi-siamo__plate-br"><?php esc_html_e('Foto B/N · 1440 × 560 · placeholder', 'saltelli'); ?></div>
                        <div class="sl-chi-siamo__plate-caption">
                            <div class="sl-chi-siamo__plate-line1"><?php esc_html_e('Via Vannella Gaetani, 27', 'saltelli'); ?></div>
                            <div class="sl-mono sl-chi-siamo__plate-line2"><?php esc_html_e('Palazzo nobiliare · Chiaia · Napoli', 'saltelli'); ?></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sl-chi-siamo__founding" aria-labelledby="chi-siamo-founding-h">
                <div class="sl-container sl-chi-siamo__founding-grid">
                    <aside class="sl-chi-siamo__founding-mark">
                        <div class="sl-mono">§ 02 — 1999</div>
                        <div class="sl-chi-siamo__founding-year">1999.</div>
                    </aside>
                    <div>
                        <h2 class="sl-section-title sl-chi-siamo__h2" id="chi-siamo-founding-h">
                            <?php esc_html_e('Un atelier, in senso napoletano.', 'saltelli'); ?>
                        </h2>
                        <div class="sl-chi-siamo__prose">
                            <?php
                            // Use post_content if Step D editorial migration populated it; otherwise emit hardcoded prose.
                            if (get_the_content() !== '') {
                                the_content();
                            } else {
                                ?>
                                <p><?php esc_html_e("Lo Studio Saltelli & Partners nasce per iniziativa di Emiliano Saltelli, giovane tributarista formatosi alla Federico II, che apre una stanza al secondo piano di un palazzo nobiliare a Chiaia.", 'saltelli'); ?></p>
                                <p><?php esc_html_e('Nel quarto di secolo successivo, lo Studio è cresciuto come si cresce a Napoli — per accumulazione paziente, una pratica alla volta, un avvocato alla volta — fino a diventare oggi un atelier di quattro professionisti.', 'saltelli'); ?></p>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </section>

            <?php if (!empty($sl_lawyers_chi)) : ?>
                <section class="sl-chi-siamo__team-mini" aria-labelledby="chi-siamo-team-h">
                    <div class="sl-container">
                        <header class="sl-chi-siamo__team-head">
                            <div class="sl-mono">§ 03 — <?php esc_html_e('I nostri quattro', 'saltelli'); ?></div>
                            <h2 class="sl-section-title sl-chi-siamo__h2" id="chi-siamo-team-h">
                                <?php esc_html_e('Quattro avvocati,', 'saltelli'); ?><br>
                                <em><?php esc_html_e('diciannove aree.', 'saltelli'); ?></em>
                            </h2>
                        </header>
                        <ul class="sl-chi-siamo__team-grid" role="list">
                            <?php foreach ($sl_lawyers_chi as $idx => $av) :
                                $ruolo_av = (string) saltelli_field('ruolo_breve', $av->ID, '');
                                $specs_av = saltelli_get_attorney_specializations($av->ID);
                                $foto_av  = saltelli_field('foto_ritratto', $av->ID);
                                ?>
                                <li class="sl-chi-siamo__team-card<?php echo ($idx % 2 === 1) ? ' sl-chi-siamo__team-card--offset' : ''; ?>">
                                    <a class="sl-chi-siamo__team-link" href="<?php echo esc_url(get_permalink($av)); ?>">
                                        <span class="sl-chi-siamo__team-portrait">
                                            <?php
                                            if (has_post_thumbnail($av->ID)) {
                                                echo get_the_post_thumbnail($av->ID, 'saltelli-attorney-square', [
                                                    'loading'  => 'lazy',
                                                    'decoding' => 'async',
                                                    'alt'      => esc_attr(get_the_title($av) . ($ruolo_av ? ' · ' . $ruolo_av : '')),
                                                ]);
                                            } elseif (is_array($foto_av) && !empty($foto_av['url'])) {
                                                echo '<img src="' . esc_url($foto_av['url']) . '" alt="' . esc_attr($foto_av['alt'] ?: get_the_title($av)) . '" loading="lazy" decoding="async">';
                                            } else {
                                                echo '<span class="sl-chi-siamo__team-placeholder" aria-hidden="true"><span class="sl-mono">' . esc_html__('Ritratto · 3:4', 'saltelli') . '</span></span>';
                                            }
                                            ?>
                                        </span>
                                        <?php if ($ruolo_av) : ?>
                                            <span class="sl-mono sl-chi-siamo__team-role"><?php echo esc_html($ruolo_av); ?></span>
                                        <?php endif; ?>
                                        <span class="sl-chi-siamo__team-name"><?php echo esc_html(get_the_title($av)); ?></span>
                                        <?php if (!empty($specs_av)) : ?>
                                            <span class="sl-chi-siamo__team-spec"><?php echo esc_html(implode(' · ', array_slice($specs_av, 0, 3))); ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </section>
            <?php endif; ?>

            <section class="sl-chi-siamo__principles" aria-labelledby="chi-siamo-princ-h">
                <div class="sl-container sl-chi-siamo__principles-grid">
                    <div class="sl-mono">§ 04 — <?php esc_html_e('Come lavoriamo', 'saltelli'); ?></div>
                    <div>
                        <h2 class="sl-section-title sl-chi-siamo__h2" id="chi-siamo-princ-h">
                            <?php esc_html_e('Tre', 'saltelli'); ?> <em><?php esc_html_e('principi.', 'saltelli'); ?></em>
                        </h2>
                        <ol class="sl-chi-siamo__principles-list" role="list">
                            <?php
                            $sl_principles = [
                                ['n' => '01', 't' => __('Ascoltiamo prima', 'saltelli'),     'd' => __("Il primo incontro è gratuito e dura il tempo necessario. Capire la storia viene sempre prima delle carte.", 'saltelli')],
                                ['n' => '02', 't' => __("Lavoriamo in atelier", 'saltelli'), 'd' => __("Ogni pratica è seguita personalmente da uno dei quattro avvocati. Niente call center, niente passaggi.", 'saltelli')],
                                ['n' => '03', 't' => __('Diciamo la verità', 'saltelli'),    'd' => __("Anche quando significa sconsigliare un'azione legale. La nostra reputazione vale più di un mandato.", 'saltelli')],
                            ];
                            foreach ($sl_principles as $p) : ?>
                                <li class="sl-chi-siamo__principle">
                                    <span class="sl-mono sl-chi-siamo__principle-n"><?php echo esc_html($p['n']); ?></span>
                                    <div class="sl-chi-siamo__principle-body">
                                        <h3 class="sl-chi-siamo__principle-t"><?php echo esc_html($p['t']); ?></h3>
                                        <p class="sl-chi-siamo__principle-d"><?php echo esc_html($p['d']); ?></p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="sl-chi-siamo__timeline" aria-labelledby="chi-siamo-time-h">
                <div class="sl-container">
                    <header class="sl-chi-siamo__timeline-head">
                        <div class="sl-mono">§ 05 — <?php esc_html_e('Cronologia', 'saltelli'); ?></div>
                        <h2 class="sl-section-title sl-chi-siamo__h2" id="chi-siamo-time-h">1999 → 2026.</h2>
                    </header>
                    <ol class="sl-chi-siamo__timeline-list" role="list">
                        <?php $tl_count = count($sl_timeline); foreach ($sl_timeline as $tl_i => $ev) :
                            $is_last = ($tl_i === $tl_count - 1); ?>
                            <li class="sl-chi-siamo__timeline-row<?php echo $is_last ? ' is-current' : ''; ?>">
                                <span class="sl-chi-siamo__timeline-year"><?php echo esc_html($ev['y']); ?></span>
                                <div class="sl-chi-siamo__timeline-body">
                                    <h3 class="sl-chi-siamo__timeline-t"><?php echo esc_html($ev['t']); ?></h3>
                                    <p class="sl-chi-siamo__timeline-d"><?php echo esc_html($ev['d']); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </section>

            <section class="sl-chi-siamo__cta" aria-labelledby="chi-siamo-cta-h">
                <div class="sl-container sl-chi-siamo__cta-grid">
                    <div class="sl-mono">§ 06 — <?php esc_html_e('Primo incontro', 'saltelli'); ?></div>
                    <div>
                        <h2 class="sl-chi-siamo__cta-title" id="chi-siamo-cta-h">
                            <?php esc_html_e('Prenota', 'saltelli'); ?><br>
                            <em><?php esc_html_e('una consulenza', 'saltelli'); ?><br><?php esc_html_e('gratuita.', 'saltelli'); ?></em>
                        </h2>
                        <p class="sl-chi-siamo__cta-lede">
                            <?php esc_html_e('Il primo incontro è gratuito e dura il tempo necessario. Riceviamo solo su appuntamento.', 'saltelli'); ?>
                        </p>
                        <a class="sl-btn sl-btn--primary" href="<?php echo esc_url(home_url('/contatti/')); ?>">
                            <span><?php esc_html_e('Prenota un primo incontro', 'saltelli'); ?></span>
                            <span class="arrow" aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </section>

        <?php elseif (is_page('glossario-legale')) :
            // === WAVE3 TASK 9 (glossario) — render delegato a inc/wave3-glossario.php
            // (60 termini · DefinedTermSet + FAQPage schema · search+a-z sticky)
            include SALTELLI_THEME_DIR . '/inc/wave3-glossario.php';
            ?>

        <?php else : ?>

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
            <section class="sl-page-contatti__sede" id="sede" aria-labelledby="contatti-sede-h">
                <div class="sl-container">
                    <div class="sl-mono">§ <?php esc_html_e('Sede', 'saltelli'); ?></div>
                    <h2 class="sl-section-title" id="contatti-sede-h">
                        <?php esc_html_e('Dove trovarci', 'saltelli'); ?>
                    </h2>
                    <p class="sl-page-contatti__sede-lede">
                        <?php esc_html_e('Studio Legale Saltelli & Partners — Chiaia, Napoli. Si riceve solo su appuntamento, prima consulenza conoscitiva gratuita.', 'saltelli'); ?>
                    </p>
                    <address class="sl-page-contatti__sede-address">
                        <span class="sl-page-contatti__sede-street">Via Vannella Gaetani 27</span>
                        <span class="sl-page-contatti__sede-city">80121 Napoli · Chiaia</span>
                    </address>
                    <div class="sl-page-contatti__sede-actions">
                        <a class="sl-mono sl-page-contatti__sede-link"
                           href="https://www.google.com/maps/search/?api=1&amp;query=Via+Vannella+Gaetani+27+Napoli"
                           target="_blank" rel="noopener">
                            <?php esc_html_e('Apri in Google Maps', 'saltelli'); ?> →
                        </a>
                        <a class="sl-mono sl-page-contatti__sede-link"
                           href="https://www.openstreetmap.org/?mlat=40.832&amp;mlon=14.235#map=17/40.832/14.235"
                           target="_blank" rel="noopener">
                            <?php esc_html_e('Apri in OpenStreetMap', 'saltelli'); ?> →
                        </a>
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

        <?php endif; // sl_chi_siamo ?>

    </article>
    <?php
endwhile;

get_footer();
