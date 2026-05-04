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
    $sl_casi      = is_page('casi');
    ?>
    <article <?php post_class('sl-page' . ($sl_chi_siamo ? ' sl-chi-siamo' : '') . ($sl_casi ? ' sl-casi-page' : '')); ?>>

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

            <section class="sl-chi-siamo__hero sl-page-hero sl-page-hero--extended" aria-labelledby="chi-siamo-h1">
                <div class="sl-container sl-chi-siamo__hero-grid">
                    <aside class="sl-chi-siamo__hero-aside">
                        <?php saltelli_render_breadcrumb(); ?>
                        <div class="sl-mono sl-chi-siamo__eyebrow"><?php esc_html_e('§ Lo studio · Chi siamo', 'saltelli'); ?></div>
                        <p class="sl-mono sl-chi-siamo__hero-meta">
                            <?php esc_html_e('Un atelier', 'saltelli'); ?><br>
                            <?php esc_html_e('di quattro avvocati', 'saltelli'); ?><br>
                            <?php esc_html_e('in Via Vannella Gaetani 27', 'saltelli'); ?><br>
                            <?php esc_html_e('Chiaia · Napoli', 'saltelli'); ?><br>
                            <?php esc_html_e('Dal 1999', 'saltelli'); ?>
                        </p>
                    </aside>
                    <h1 class="sl-chi-siamo__h1" id="chi-siamo-h1" data-split-reveal>
                        <?php
                        $sl_chi_title = esc_html__('Un atelier', 'saltelli') . '<br>'
                            . esc_html__('di quattro', 'saltelli') . '<br>'
                            . '<em>' . esc_html__('professionisti.', 'saltelli') . '</em>';
                        echo wp_kses(saltelli_split_h1_words($sl_chi_title), [
                            'span' => ['class' => true, 'data-i' => true],
                            'em'   => [],
                            'br'   => [],
                        ]);
                        ?>
                    </h1>
                </div>
            </section>

            <section class="sl-chi-siamo__lede" aria-label="<?php esc_attr_e('Lede editoriale', 'saltelli'); ?>">
                <div class="sl-container sl-chi-siamo__lede-grid">
                    <div class="sl-mono">§ 01 — <?php esc_html_e('Lede', 'saltelli'); ?></div>
                    <div class="sl-chi-siamo__prose sl-chi-siamo__prose--dropcap">
                        <p>
                            Un atelier di quattro professionisti che da oltre vent'anni accompagna famiglie e imprese di Napoli attraverso le materie di cui si occupa: il diritto tributario di Emiliano, il diritto del lavoro di Fabiana, la tutela LGBTQ+ in materia di famiglia di Antonia, il condominiale e immobiliare di Stefano.
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
                                /* === v0.25.0 T3 — bio breve 14 word truncated === */
                                $bio_breve_av = (string) saltelli_field('bio_breve', $av->ID, '');
                                if ($bio_breve_av !== '') {
                                    $bio_words_av = preg_split('/\s+/u', trim(wp_strip_all_tags($bio_breve_av)));
                                    if (is_array($bio_words_av) && count($bio_words_av) > 14) {
                                        $bio_breve_av = implode(' ', array_slice($bio_words_av, 0, 14)) . '…';
                                    }
                                }
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
                                                /* IMPECCABLE v0.21.0 [perf-T2]: width/height esplicite (CLS prevention) */
                                                echo '<img src="' . esc_url($foto_av['url']) . '" alt="' . esc_attr($foto_av['alt'] ?: get_the_title($av)) . '" loading="lazy" decoding="async" width="600" height="800">';
                                            } else {
                                                echo '<span class="sl-chi-siamo__team-placeholder" aria-hidden="true"><span class="sl-mono">' . esc_html__('Ritratto · 3:4', 'saltelli') . '</span></span>';
                                            }
                                            ?>
                                        </span>
                                        <?php if ($ruolo_av) : ?>
                                            <span class="sl-mono sl-chi-siamo__team-role"><?php echo esc_html($ruolo_av); ?></span>
                                        <?php endif; ?>
                                        <span class="sl-chi-siamo__team-name"><?php echo esc_html(get_the_title($av)); ?></span>
                                        <?php if ($bio_breve_av !== '') : ?>
                                            <span class="sl-chi-siamo__team-spec sl-chi-siamo__team-bio"><?php echo esc_html($bio_breve_av); ?></span>
                                        <?php elseif (!empty($specs_av)) : ?>
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

        <?php elseif ($sl_casi) :
            // === WAVE3 TASK 5 (casi) — JSX-faithful editorial layout ===
            $sl_casi_all      = function_exists('saltelli_cases_full')
                                    ? saltelli_cases_full()
                                    : (function_exists('saltelli_homepage_cases') ? saltelli_homepage_cases() : []);
            $sl_casi_count    = is_array($sl_casi_all) ? count($sl_casi_all) : 0;
            $sl_casi_filters  = ['Tutti', 'Privati', 'Imprese', 'Contenzioso', 'Altri'];
            $sl_casi_counts   = ['Tutti' => $sl_casi_count, 'Privati' => 0, 'Imprese' => 0, 'Contenzioso' => 0, 'Altri' => 0];
            $sl_casi_featured = null;
            $sl_casi_known    = ['Privati', 'Imprese', 'Contenzioso', 'Altri'];
            foreach ($sl_casi_all as &$sl_c) {
                $sl_cat_c = isset($sl_c['cat']) ? (string) $sl_c['cat'] : 'Altri';
                if (!in_array($sl_cat_c, $sl_casi_known, true)) {
                    $sl_cat_c = 'Altri';
                }
                $sl_c['cat'] = $sl_cat_c;
                $sl_casi_counts[$sl_cat_c]++;
                if (!$sl_casi_featured && !empty($sl_c['featured'])) {
                    $sl_casi_featured = $sl_c;
                }
            }
            unset($sl_c);
            $sl_casi_chain = saltelli_get_breadcrumb_chain();
            ?>

            <section class="sl-casi__hero sl-page-hero" aria-labelledby="casi-h1">
                <div class="sl-casi__hero-grid">
                    <div class="sl-casi__hero-left">
                        <?php if (!empty($sl_casi_chain) && count($sl_casi_chain) > 1) : ?>
                            <nav class="sl-mono sl-page__breadcrumb sl-casi__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'saltelli'); ?>">
                                <?php foreach ($sl_casi_chain as $sl_idx => $sl_node) :
                                    if ($sl_idx > 0) echo ' / ';
                                    if (!empty($sl_node['url'])) : ?>
                                        <a href="<?php echo esc_url($sl_node['url']); ?>"><?php echo esc_html($sl_node['name']); ?></a>
                                    <?php else : ?>
                                        <span><?php echo esc_html($sl_node['name']); ?></span>
                                    <?php endif;
                                endforeach; ?>
                            </nav>
                        <?php endif; ?>
                        <div class="sl-mono sl-casi__eyebrow"><?php esc_html_e('§ Risultati · Casi rappresentativi', 'saltelli'); ?></div>
                        <h1 class="sl-casi__h1" id="casi-h1" data-split-reveal>
                            <?php
                            $sl_casi_title = esc_html__('Casi', 'saltelli') . '<br><em>' . esc_html__('rappresentativi.', 'saltelli') . '</em>';
                            echo wp_kses(saltelli_split_h1_words($sl_casi_title), [
                                'span' => ['class' => true, 'data-i' => true],
                                'em'   => [],
                                'br'   => [],
                            ]);
                            ?>
                        </h1>
                    </div>
                    <div class="sl-casi__hero-right">
                        <p class="sl-casi__hero-lede">
                            <?php esc_html_e('Una selezione di vittorie. Identificativi anonimizzati per riservatezza, documentati e verificabili in studio.', 'saltelli'); ?>
                        </p>
                        <div class="sl-mono sl-casi__hero-meta">
                            <?php
                            printf(
                                /* translators: 1=numero casi, 2=range anni, 3=mese aggiornamento */
                                esc_html__('%1$d casi · %2$s · aggiornato %3$s', 'saltelli'),
                                (int) $sl_casi_count,
                                esc_html__('2022 → 2024', 'saltelli'),
                                esc_html__('Apr 2026', 'saltelli')
                            );
                            ?>
                        </div>
                    </div>
                </div>
            </section>

            <?php if ($sl_casi_featured) : ?>
            <section class="sl-casi__pull" aria-labelledby="casi-pull-h">
                <div class="sl-casi__pull-frame">
                    <div class="sl-casi__pull-meta">
                        <div class="sl-mono sl-casi__pull-eyebrow"><?php esc_html_e('Caso simbolo · 2024', 'saltelli'); ?></div>
                        <div class="sl-casi__pull-figure" id="casi-pull-h"><?php echo esc_html($sl_casi_featured['outcome']); ?></div>
                        <div class="sl-mono sl-casi__pull-label"><?php echo esc_html($sl_casi_featured['lbl']); ?></div>
                    </div>
                    <blockquote class="sl-casi__pull-quote">
                        <p>&ldquo;<?php echo esc_html($sl_casi_featured['desc']); ?>&rdquo;</p>
                        <footer class="sl-mono sl-casi__pull-cite"><?php echo esc_html($sl_casi_featured['id']); ?></footer>
                    </blockquote>
                </div>
            </section>
            <?php endif; ?>

            <section class="sl-casi__filter" aria-label="<?php esc_attr_e('Filtra casi per categoria', 'saltelli'); ?>">
                <div class="sl-casi__filter-bar" role="tablist">
                    <?php foreach ($sl_casi_filters as $sl_f) :
                        $sl_count_f = isset($sl_casi_counts[$sl_f]) ? (int) $sl_casi_counts[$sl_f] : 0;
                        $sl_filter_value = $sl_f === 'Tutti' ? '*' : $sl_f;
                        $sl_is_active = $sl_f === 'Tutti';
                        ?>
                        <button class="sl-casi__filter-btn sl-mono<?php echo $sl_is_active ? ' is-active' : ''; ?>"
                                type="button"
                                role="tab"
                                aria-pressed="<?php echo $sl_is_active ? 'true' : 'false'; ?>"
                                data-filter="<?php echo esc_attr($sl_filter_value); ?>">
                            <span><?php echo esc_html($sl_f); ?></span>
                            <span class="sl-casi__filter-count">(<?php echo (int) $sl_count_f; ?>)</span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="sl-casi__list-wrap" aria-labelledby="casi-list-h">
                <h2 class="sl-casi__sr screen-reader-text" id="casi-list-h"><?php esc_html_e('Elenco casi', 'saltelli'); ?></h2>
                <div class="sl-casi__list">
                    <?php foreach ($sl_casi_all as $sl_c) : ?>
                        <a class="sl-casi__row"
                           href="<?php echo esc_url(home_url('/contatti/')); ?>"
                           data-cat="<?php echo esc_attr($sl_c['cat']); ?>">
                            <div class="sl-casi__row-id">
                                <div class="sl-mono sl-casi__row-court"><?php echo esc_html($sl_c['id']); ?></div>
                                <div class="sl-mono sl-casi__row-cat"><?php echo esc_html($sl_c['cat']); ?> <span class="arrow" aria-hidden="true">→</span></div>
                            </div>
                            <p class="sl-casi__row-desc"><?php echo esc_html($sl_c['desc']); ?></p>
                            <div class="sl-casi__row-outcome">
                                <div class="sl-casi__row-figure"><?php echo esc_html($sl_c['outcome']); ?></div>
                                <div class="sl-mono sl-casi__row-label"><?php echo esc_html($sl_c['lbl']); ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="sl-casi__pagination">
                    <div class="sl-mono sl-casi__pagination-status">
                        <span data-casi-status>
                            <?php
                            printf(
                                /* translators: %d numero casi visibili */
                                esc_html__('Pagina 1 / 1 · %d casi visibili', 'saltelli'),
                                (int) $sl_casi_count
                            );
                            ?>
                        </span>
                    </div>
                    <button class="sl-btn sl-casi__pagination-btn" type="button" disabled aria-disabled="true">
                        <span><?php esc_html_e('Carica altri casi', 'saltelli'); ?></span>
                        <span class="arrow" aria-hidden="true">→</span>
                    </button>
                </div>
            </section>

            <section class="sl-casi__cta" aria-labelledby="casi-cta-h">
                <div class="sl-casi__cta-grid">
                    <div class="sl-mono sl-casi__cta-tag"><?php esc_html_e('§ Prossimo caso', 'saltelli'); ?></div>
                    <div class="sl-casi__cta-body">
                        <h2 class="sl-casi__cta-title" id="casi-cta-h">
                            <?php esc_html_e('Vorresti vincere', 'saltelli'); ?><br>
                            <em><?php esc_html_e('il tuo?', 'saltelli'); ?></em>
                        </h2>
                        <p class="sl-casi__cta-lede">
                            <?php esc_html_e("Il primo incontro è gratuito. Diciamo la verità anche quando significa sconsigliare un'azione legale.", 'saltelli'); ?>
                        </p>
                        <a class="sl-btn sl-btn--primary" href="<?php echo esc_url(home_url('/contatti/')); ?>">
                            <span><?php esc_html_e('Prenota gratuita', 'saltelli'); ?></span>
                            <span class="arrow" aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </section>

            <script>
            (function () {
                var root = document.querySelector('.sl-casi-page');
                if (!root) { return; }
                var bar    = root.querySelector('.sl-casi__filter-bar');
                var rows   = root.querySelectorAll('.sl-casi__row');
                var status = root.querySelector('[data-casi-status]');
                if (!bar) { return; }
                bar.addEventListener('click', function (e) {
                    var btn = e.target.closest('.sl-casi__filter-btn');
                    if (!btn) { return; }
                    var filter = btn.getAttribute('data-filter');
                    bar.querySelectorAll('.sl-casi__filter-btn').forEach(function (b) {
                        var on = b === btn;
                        b.classList.toggle('is-active', on);
                        b.setAttribute('aria-pressed', on ? 'true' : 'false');
                    });
                    var visible = 0;
                    rows.forEach(function (row) {
                        var cat = row.getAttribute('data-cat');
                        var match = filter === '*' || cat === filter;
                        row.classList.toggle('is-hidden', !match);
                        if (match) visible++;
                    });
                    if (status) {
                        status.textContent = 'Pagina 1 / 1 · ' + visible + ' casi visibili';
                    }
                });
            })();
            </script>

        <?php elseif (is_page('contatti')) :
            // === WAVE3 TASK 6 (contatti) — rebuild da JSX sessione-2 ===
            // Layout: hero asimmetrico (5fr/7fr) + 2-col 8fr/4fr (form|aside) +
            // come arrivare 3fr/9fr + trust signal full-width.
            // CF7 shortcode preserva backend logic (form ID/slug saltelli-contatti).
            $sl_studio        = saltelli_studio_data();
            $sl_phone_label   = '+39 081 1813 1119';
            $sl_phone_href    = 'tel:' . $sl_studio['phone'];
            $sl_email_pub     = (string) saltelli_option('contact_email_pubblica', $sl_studio['email']);
            if ($sl_email_pub === '') { $sl_email_pub = $sl_studio['email']; }
            $sl_wa_digits     = preg_replace('/[^0-9]/', '', (string) $sl_studio['whatsapp']);
            $sl_wa_href       = 'https://wa.me/' . $sl_wa_digits;
            $sl_chain_contact = saltelli_get_breadcrumb_chain();
            /* IMPECCABLE v0.20.1 [T1]: $sl_aree_select rimosso — field "area di interesse" droppato dal form */
            ?>

            <div class="sl-contatti-w3">

                <section class="sl-contatti-w3__hero sl-page-hero" aria-labelledby="contatti-h1">
                    <div class="sl-contatti-w3__hero-grid">
                        <div class="sl-contatti-w3__hero-left">
                            <?php if (!empty($sl_chain_contact) && count($sl_chain_contact) > 1) : ?>
                                <nav class="sl-mono sl-page__breadcrumb sl-contatti-w3__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'saltelli'); ?>">
                                    <?php foreach ($sl_chain_contact as $sl_idx => $sl_node) :
                                        if ($sl_idx > 0) echo ' / ';
                                        if (!empty($sl_node['url'])) : ?>
                                            <a href="<?php echo esc_url($sl_node['url']); ?>"><?php echo esc_html($sl_node['name']); ?></a>
                                        <?php else : ?>
                                            <span><?php echo esc_html($sl_node['name']); ?></span>
                                        <?php endif;
                                    endforeach; ?>
                                </nav>
                            <?php endif; ?>
                            <div class="sl-mono sl-contatti-w3__eyebrow">
                                <?php esc_html_e('§ Contatti · Primo incontro gratuito', 'saltelli'); ?>
                            </div>
                            <h1 class="sl-contatti-w3__h1" id="contatti-h1" data-split-reveal><?php echo wp_kses(saltelli_split_h1_words(__('Contatti.', 'saltelli')), ['span' => ['class' => true, 'data-i' => true]]); ?></h1>
                        </div>
                        <div class="sl-contatti-w3__hero-right">
                            <p class="sl-contatti-w3__hero-lede">
                                <?php esc_html_e('Chiedi qualsiasi cosa.', 'saltelli'); ?><br>
                                <span class="sl-contatti-w3__hero-lede-accent"><?php esc_html_e('In qualsiasi momento.', 'saltelli'); ?></span>
                            </p>
                        </div>
                    </div>
                </section>

                <section class="sl-contatti-w3__main" aria-labelledby="contatti-form-h">
                    <div class="sl-contatti-w3__main-grid">

                        <div class="sl-contatti-w3__form-col">
                            <div class="sl-mono"><?php esc_html_e('§ 01 — Modulo', 'saltelli'); ?></div>
                            <h2 class="sl-contatti-w3__form-h" id="contatti-form-h">
                                <?php esc_html_e('Prenota un primo', 'saltelli'); ?><br>
                                <em><?php esc_html_e('incontro gratuito.', 'saltelli'); ?></em>
                            </h2>

                            <?php
                            if (shortcode_exists('contact-form-7')) {
                                // Form locale slug saltelli-contatti — handler CF7 preservato.
                                $form_post = get_page_by_path('saltelli-contatti', OBJECT, 'wpcf7_contact_form');
                                if ($form_post) {
                                    echo do_shortcode('[contact-form-7 id="' . (int) $form_post->ID . '" title="Saltelli Contatti"]');
                                } else {
                                    echo '<p class="sl-mono">' . esc_html__('Modulo non disponibile. Scrivici a info@studiolegalesaltelli.it.', 'saltelli') . '</p>';
                                }
                            } else {
                                // Fallback editorial — display only (CF7 non attivo, es. ambiente locale senza plugin).
                                ?>
                                <form class="sl-contatti-w3__form" method="post" action="#" novalidate>
                                    <label class="sl-contatti-w3__field">
                                        <span class="sl-mono"><?php esc_html_e('Nome e cognome *', 'saltelli'); ?></span>
                                        <input type="text" name="nome" class="sl-input" required>
                                    </label>

                                    <div class="sl-contatti-w3__field-row">
                                        <label class="sl-contatti-w3__field">
                                            <span class="sl-mono"><?php esc_html_e('Email *', 'saltelli'); ?></span>
                                            <input type="email" name="email" class="sl-input" required>
                                        </label>
                                        <label class="sl-contatti-w3__field">
                                            <span class="sl-mono"><?php esc_html_e('Telefono', 'saltelli'); ?></span>
                                            <input type="tel" name="telefono" class="sl-input">
                                        </label>
                                    </div>

                                    <?php /* IMPECCABLE v0.20.1 [T1]: drop "Area di interesse" + "Data preferita" → 6 fields totali (allineato a CF7 live) */ ?>
                                    <label class="sl-contatti-w3__field">
                                        <span class="sl-mono"><?php esc_html_e('Messaggio *', 'saltelli'); ?></span>
                                        <textarea name="messaggio" rows="6" class="sl-input" required></textarea>
                                    </label>

                                    <label class="sl-contatti-w3__gdpr">
                                        <input type="checkbox" name="gdpr" required>
                                        <span>
                                            <?php
                                            printf(
                                                /* translators: %s wraps the privacy policy link */
                                                esc_html__('Consento il trattamento dei dati personali ai sensi del Reg. UE 2016/679 (GDPR), per le finalità descritte nell\'%s. *', 'saltelli'),
                                                '<a href="' . esc_url(home_url('/privacy-policy/')) . '" class="sl-link">' . esc_html__('informativa privacy', 'saltelli') . '</a>'
                                            );
                                            ?>
                                        </span>
                                    </label>

                                    <div class="sl-contatti-w3__submit-row">
                                        <?php /* IMPECCABLE v0.20.0 [ux-writing]: form submit context-specific = "Prenota un incontro" */ ?>
                                        <button type="submit" class="sl-btn sl-btn--primary">
                                            <span><?php esc_html_e('Prenota un incontro', 'saltelli'); ?></span>
                                            <span class="arrow" aria-hidden="true">→</span>
                                        </button>
                                    </div>
                                </form>
                                <?php
                            }
                            ?>

                            <?php /* === v0.24.0 TASK 6 — CF7 success state ===
                                  Hidden by default · revealed via wpcf7mailsent JS handler in main.js.
                                  Source: saltelli-s2-contatti.jsx (success state mancante). */ ?>
                            <div class="sl-contatti-w3__success" hidden role="status" aria-live="polite">
                                <div class="sl-mono sl-contatti-w3__success-eyebrow"><?php esc_html_e('§ Inviato', 'saltelli'); ?></div>
                                <h3 class="sl-contatti-w3__success-h3"><?php esc_html_e('Grazie. Ci sentiamo entro 24 ore.', 'saltelli'); ?></h3>
                                <p class="sl-contatti-w3__success-text">
                                    <?php esc_html_e('La tua richiesta è stata inviata correttamente. Riceverai una conferma via email e ti ricontatteremo entro 24 ore lavorative.', 'saltelli'); ?>
                                </p>
                            </div>
                        </div>

                        <aside class="sl-contatti-w3__aside" aria-labelledby="contatti-aside-h">
                            <div class="sl-mono" id="contatti-aside-h"><?php esc_html_e('§ 02 — Studio', 'saltelli'); ?></div>

                            <div class="sl-contatti-w3__nap">
                                <div class="sl-mono"><?php esc_html_e('Indirizzo', 'saltelli'); ?></div>
                                <address class="sl-contatti-w3__address">
                                    <?php esc_html_e('Via Vannella', 'saltelli'); ?><br>
                                    <?php esc_html_e('Gaetani, 27', 'saltelli'); ?><br>
                                    <?php esc_html_e('80121 Napoli — Chiaia', 'saltelli'); ?>
                                </address>
                            </div>

                            <div class="sl-contatti-w3__map" aria-label="<?php esc_attr_e('Mappa studio Saltelli — Chiaia, Napoli', 'saltelli'); ?>">
                                <iframe
                                    title="<?php esc_attr_e('Studio Saltelli — Via Vannella Gaetani 27', 'saltelli'); ?>"
                                    width="100%" height="100%" frameborder="0" scrolling="no"
                                    loading="lazy"
                                    <?php /* v0.21.16 [T4 final]: coordinate ufficiali Google Business 2026-05-02 = 40.8332541, 14.2414699 */ ?>
                                    src="https://www.openstreetmap.org/export/embed.html?bbox=14.236%2C40.830%2C14.246%2C40.837&amp;layer=mapnik&amp;marker=40.8332541%2C14.2414699"></iframe>
                                <div class="sl-mono sl-contatti-w3__map-tag"><?php esc_html_e('Chiaia · Napoli', 'saltelli'); ?></div>
                            </div>

                            <div class="sl-contatti-w3__cta-list" role="list">
                                <a class="sl-contatti-w3__cta-row" role="listitem" href="<?php echo esc_attr($sl_phone_href); ?>">
                                    <span class="sl-mono"><?php esc_html_e('Telefono', 'saltelli'); ?></span>
                                    <span class="sl-contatti-w3__cta-val"><?php echo esc_html($sl_phone_label); ?> <span class="arrow" aria-hidden="true">→</span></span>
                                </a>
                                <a class="sl-contatti-w3__cta-row" role="listitem" href="mailto:<?php echo esc_attr($sl_email_pub); ?>">
                                    <span class="sl-mono"><?php esc_html_e('Email', 'saltelli'); ?></span>
                                    <span class="sl-contatti-w3__cta-val"><?php echo esc_html($sl_email_pub); ?> <span class="arrow" aria-hidden="true">→</span></span>
                                </a>
                                <a class="sl-contatti-w3__cta-row" role="listitem" href="<?php echo esc_url($sl_wa_href); ?>" target="_blank" rel="noopener">
                                    <span class="sl-mono"><?php esc_html_e('WhatsApp', 'saltelli'); ?></span>
                                    <span class="sl-contatti-w3__cta-val"><?php esc_html_e('Scrivi su WhatsApp', 'saltelli'); ?> <span class="arrow" aria-hidden="true">→</span></span>
                                </a>
                            </div>

                            <div class="sl-contatti-w3__hours">
                                <div class="sl-mono"><?php esc_html_e('Orari', 'saltelli'); ?></div>
                                <div class="sl-contatti-w3__hours-body">
                                    <?php esc_html_e('Lun – Ven · 10:00 – 19:00', 'saltelli'); ?><br>
                                    <?php esc_html_e('Sabato su appuntamento', 'saltelli'); ?>
                                </div>
                            </div>
                        </aside>
                    </div>
                </section>

                <section class="sl-contatti-w3__come" aria-labelledby="contatti-come-h">
                    <div class="sl-contatti-w3__come-grid">
                        <div class="sl-mono sl-contatti-w3__come-mark"><?php esc_html_e('§ 03 — Come arrivare', 'saltelli'); ?></div>
                        <div class="sl-contatti-w3__come-body">
                            <h2 class="sl-contatti-w3__come-h" id="contatti-come-h"><?php esc_html_e('Come arrivare.', 'saltelli'); ?></h2>
                            <ul class="sl-contatti-w3__come-list" role="list">
                                <li class="sl-contatti-w3__come-item">
                                    <div class="sl-mono"><?php esc_html_e('Metro', 'saltelli'); ?></div>
                                    <h3 class="sl-contatti-w3__come-t"><?php esc_html_e('Linea 6 · Mergellina', 'saltelli'); ?></h3>
                                    <p class="sl-contatti-w3__come-d"><?php esc_html_e('8 minuti a piedi lungo la Riviera di Chiaia.', 'saltelli'); ?></p>
                                </li>
                                <li class="sl-contatti-w3__come-item">
                                    <div class="sl-mono"><?php esc_html_e('Auto', 'saltelli'); ?></div>
                                    <h3 class="sl-contatti-w3__come-t"><?php esc_html_e('Parcheggio Mergellina', 'saltelli'); ?></h3>
                                    <p class="sl-contatti-w3__come-d"><?php esc_html_e('Sosta a pagamento, 5 minuti a piedi.', 'saltelli'); ?></p>
                                </li>
                                <li class="sl-contatti-w3__come-item">
                                    <div class="sl-mono"><?php esc_html_e('Treno', 'saltelli'); ?></div>
                                    <h3 class="sl-contatti-w3__come-t"><?php esc_html_e('Napoli Mergellina', 'saltelli'); ?></h3>
                                    <p class="sl-contatti-w3__come-d"><?php esc_html_e('Stazione FS, 10 minuti a piedi.', 'saltelli'); ?></p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <?php /* v0.21.10: "Promessa di servizio" → "La nostra professionalità" (Duccio review) */ ?>
                <section class="sl-contatti-w3__trust" aria-label="<?php esc_attr_e('La nostra professionalità', 'saltelli'); ?>">
                    <div class="sl-contatti-w3__trust-inner">
                        <div class="sl-mono sl-contatti-w3__trust-eyebrow"><?php esc_html_e('La nostra professionalità', 'saltelli'); ?></div>
                        <p class="sl-contatti-w3__trust-quote">
                            <?php esc_html_e('Riceviamo solo', 'saltelli'); ?><br><?php esc_html_e('su appuntamento.', 'saltelli'); ?><br>
                            <span class="sl-contatti-w3__trust-tail"><?php esc_html_e('Risposta entro 24 ore.', 'saltelli'); ?></span>
                        </p>
                    </div>
                </section>

            </div>

        <?php elseif (is_page('glossario-legale')) :
            // === WAVE3 TASK 9 (glossario) — render delegato a inc/wave3-glossario.php
            include SALTELLI_THEME_DIR . '/inc/wave3-glossario.php';
            ?>

        <?php elseif (is_page('faq')) :
            // === v0.34.0 — FAQ aggregator (audit GEO §4.3 critical) ===
            // 6 topic sections + 28 FAQ aggregate + TOC sticky + Schema FAQPage cumulativo.
            $sl_faq_topics = [
                'tributario' => [
                    'eyebrow' => __('§ 01 — Diritto tributario', 'saltelli'),
                    'h2'      => __('Cartelle, accertamenti, contenzioso.', 'saltelli'),
                    'faqs'    => [
                        ['Quando conviene impugnare una cartella esattoriale?', 'Le cartelle vanno impugnate entro 60 giorni dalla notifica davanti alla Corte di Giustizia Tributaria competente. Lo Studio valuta gratuitamente la fondatezza dell\'impugnazione nel primo incontro.'],
                        ['Cosa fare se l\'Agenzia delle Entrate avvia un accertamento sintetico?', 'Prima dell\'accertamento si apre un contraddittorio preventivo: è la fase più delicata. Documentare correttamente la propria posizione in questa sede può evitare il contenzioso.'],
                        ['Quali sono i tempi medi di un contenzioso tributario?', 'Primo grado in CGT: 12-18 mesi. Appello in CGT 2: ulteriori 18-24 mesi. Cassazione: 24-36 mesi. La sospensione cautelare è quasi sempre concedibile.'],
                        ['Si possono rateizzare le somme dovute?', 'Sì, fino a 72 rate mensili (120 in casi di grave difficoltà). Lo Studio assiste anche nella negoziazione dei piani di rateizzazione.'],
                        ['Quanto costa un contenzioso tributario?', 'Il primo incontro è gratuito. Il preventivo è scritto, fisso o a percentuale del beneficio. Le parcelle seguono i parametri ministeriali, sempre concordate prima del mandato.'],
                    ],
                ],
                'lavoro' => [
                    'eyebrow' => __('§ 02 — Diritto del lavoro', 'saltelli'),
                    'h2'      => __('Licenziamenti, mobbing, INPS.', 'saltelli'),
                    'faqs'    => [
                        ['Il licenziamento è impugnabile? Entro quando?', 'Sì, entro 60 giorni dalla comunicazione (180 giorni per discriminazione). Lo Studio valuta gratuitamente la fondatezza nel primo incontro.'],
                        ['Cos\'è il mobbing e come si dimostra?', 'Il mobbing richiede prova documentale di vessazioni reiterate. Servono messaggi, testimoni, note mediche. Lo Studio coordina la raccolta probatoria.'],
                        ['Cosa cambia tra contestazione disciplinare e licenziamento?', 'La contestazione è il primo step: difesa scritta entro 5 giorni è critica per evitare il provvedimento.'],
                        ['Sono lavoratore autonomo: che tutele ho?', 'Anche il lavoro autonomo gode di tutele crescenti (legge 81/2017): equo compenso, recesso illegittimo, dipendenza economica.'],
                        ['INPS contestato: cosa fare?', 'Ricorso amministrativo entro 90 giorni, poi giudiziale entro un anno. Lo Studio assiste in entrambe le sedi.'],
                    ],
                ],
                'lgbtq' => [
                    'eyebrow' => __('§ 03 — Famiglia LGBTQ+', 'saltelli'),
                    'h2'      => __('Unioni civili, PMA, stepchild.', 'saltelli'),
                    'faqs'    => [
                        ['L\'unione civile dà gli stessi diritti del matrimonio?', 'L\'unione civile (legge 76/2016) dà la maggior parte dei diritti del matrimonio salvo adozione e fecondazione assistita. Trascrizione, eredità, pensione di reversibilità: sì.'],
                        ['Trascrizione di nascita all\'estero (PMA o GPA): è possibile?', 'Dipende dalla giurisdizione di nascita. Cassazione 38162/2022 e successive aprono spiragli per trascrizione integrale. Lo Studio ha ottenuto il primo riconoscimento in Campania nel 2023.'],
                        ['Stepchild adoption: in quali casi è possibile?', 'Adozione coparentale (art. 44 lett. d L.184/1983) su minore già genitore biologico del partner. Procedura giudiziale, esito favorevole consolidato post-Cassazione 2014.'],
                        ['Cosa succede in caso di separazione tra coppie LGBTQ+?', 'Per unioni civili: scioglimento giudiziale come divorzio. Per coppie di fatto: contratti di convivenza. Affido figli: principio del miglior interesse del minore.'],
                        ['L\'identità di genere è riconosciuta legalmente?', 'Sì, legge 164/1982. Procedura giudiziale o amministrativa post-Cassazione 15138/2015. Lo Studio assiste in tutti i passaggi.'],
                    ],
                ],
                'costi' => [
                    'eyebrow' => __('§ 04 — Costi e tariffe', 'saltelli'),
                    'h2'      => __('Trasparenza, dilazione, success fee.', 'saltelli'),
                    'faqs'    => [
                        ['Quanto costa una pratica di diritto tributario?', 'Range orientativo €800-€3500 a seconda di tipologia atto, importo contestato, necessità di periti. Esempio: opposizione cartella €5000 → forfait €1200.'],
                        ['Pagamento dilazionato è possibile?', 'Sì per pratiche oltre €1500. Concordiamo rate trimestrali in funzione del flusso atti. Trasparenza totale: nessun interesse, solo dilazione fisica.'],
                        ['Se non vinco, devo comunque pagare?', 'Sì. Le tariffe forensi prevedono onorari per il lavoro svolto, indipendentemente dall\'esito (Codice deontologico). Possiamo valutare in prima consulenza la percorribilità della causa.'],
                        ['Recupero crediti: solo se vinciamo?', 'Per pratiche specifiche di recupero crediti < €5000 proponiamo success fee (X% sul recuperato + spese vive). Da concordare in prima consulenza.'],
                    ],
                ],
                'metodo' => [
                    'eyebrow' => __('§ 05 — Come lavoriamo', 'saltelli'),
                    'h2'      => __('Atelier, ascolto, verità.', 'saltelli'),
                    'faqs'    => [
                        ['Chi seguirà la mia pratica?', 'Uno dei quattro avvocati personalmente, dall\'inizio alla fine. Niente call center, niente passaggi. Lavoriamo in atelier.'],
                        ['Posso scegliere l\'avvocato?', 'Sì. Nel primo incontro valutiamo insieme chi è più indicato per la tua materia. Ti presentiamo l\'avvocato di riferimento prima del mandato.'],
                        ['Quanto è davvero gratuito il primo incontro?', '30 minuti, in studio o videocall, senza obblighi. Se decidi di non procedere, abbiamo solo investito tempo. Costa solo se decidiamo insieme di procedere.'],
                        ['Cosa succede se la mia causa non è percorribile?', 'Te lo diciamo subito. Ti suggeriamo un percorso alternativo o ti rimandiamo a un professionista più indicato. La nostra reputazione vale più di un mandato.'],
                    ],
                ],
                'prima-consulenza' => [
                    'eyebrow' => __('§ 06 — Prima consulenza', 'saltelli'),
                    'h2'      => __('Trenta minuti, gratuiti, senza obbligo.', 'saltelli'),
                    'faqs'    => [
                        ['Devo portare documenti al primo incontro?', 'Se hai documenti relativi alla pratica (contratti, cartelle, lettere), portali. Altrimenti basta una sintesi orale della situazione.'],
                        ['Posso fare videocall invece di venire in studio?', 'Sì. Google Meet, Zoom o piattaforma a tua scelta. Stessa efficacia, zero spostamento.'],
                        ['Quanto preavviso serve per fissare l\'appuntamento?', 'Riceviamo solo su appuntamento. Tipicamente entro 3-5 giorni lavorativi. Per urgenze contattaci telefonicamente.'],
                        ['Posso portare un familiare o un consulente?', 'Sì, se ritieni utile. Lo Studio si adatta alle tue esigenze comunicative.'],
                        ['Il primo incontro è in italiano?', 'Sì. Disponibilità anche in inglese su richiesta (Emiliano e Antonia parlano inglese fluente).'],
                    ],
                ],
            ];
            ?>
            <article class="sl-faq-aggregator">

                <header class="sl-faq-aggregator__hero sl-page-hero">
                    <div>
                        <?php saltelli_render_breadcrumb(); ?>
                        <div class="sl-mono" style="margin-bottom: 32px;"><?php esc_html_e('§ Risorse · Domande frequenti', 'saltelli'); ?></div>
                        <h1 class="sl-faq-aggregator__h1" data-split-reveal>
                            <?php esc_html_e('Domande', 'saltelli'); ?><br>
                            <em><?php esc_html_e('frequenti.', 'saltelli'); ?></em>
                        </h1>
                        <p class="sl-faq-aggregator__lede">
                            <?php esc_html_e('Le domande più ricorrenti che ci pongono privati e imprese. Sei aree tematiche, oltre 28 risposte, raccolte in un\'unica pagina.', 'saltelli'); ?>
                        </p>
                    </div>
                </header>

                <section class="sl-faq-aggregator__body">
                    <?php /* TOC sticky sidebar */ ?>
                    <aside class="sl-faq-aggregator__toc" aria-label="<?php esc_attr_e('Indice domande', 'saltelli'); ?>">
                        <div class="sl-mono sl-faq-aggregator__toc-label"><?php esc_html_e('Indice', 'saltelli'); ?></div>
                        <ul class="sl-faq-aggregator__toc-list" role="list">
                            <?php foreach ($sl_faq_topics as $sl_topic_id => $sl_topic) : ?>
                                <li>
                                    <a class="sl-faq-aggregator__toc-link" href="#faq-<?php echo esc_attr($sl_topic_id); ?>">
                                        <?php echo esc_html($sl_topic['eyebrow']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </aside>

                    <div class="sl-faq-aggregator__topics">
                        <?php $sl_topic_idx = 0; foreach ($sl_faq_topics as $sl_topic_id => $sl_topic) : $sl_topic_idx++; ?>
                            <section class="sl-faq-aggregator__topic" id="faq-<?php echo esc_attr($sl_topic_id); ?>">
                                <div class="sl-mono sl-faq-aggregator__topic-eyebrow"><?php echo esc_html($sl_topic['eyebrow']); ?></div>
                                <h2 class="sl-faq-aggregator__topic-h2"><?php echo esc_html($sl_topic['h2']); ?></h2>
                                <div class="sl-acc" data-sl-acc>
                                    <?php foreach ($sl_topic['faqs'] as $sl_qa_idx => $sl_qa) :
                                        $sl_acc_id = 'faq-' . $sl_topic_id . '-' . $sl_qa_idx;
                                    ?>
                                        <div class="sl-acc__item" data-open="false">
                                            <button class="sl-acc__btn" type="button"
                                                    aria-expanded="false"
                                                    aria-controls="<?php echo esc_attr($sl_acc_id); ?>">
                                                <span><?php echo esc_html($sl_qa[0]); ?></span>
                                                <span class="sl-acc__icon" aria-hidden="true">+</span>
                                            </button>
                                            <div class="sl-acc__panel" id="<?php echo esc_attr($sl_acc_id); ?>">
                                                <div class="sl-acc__inner">
                                                    <?php echo esc_html($sl_qa[1]); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endforeach; ?>
                    </div>
                </section>

                <?php /* CTA finale dark navy (riusa .sl-info-page__cta) */ ?>
                <section class="sl-info-page__cta">
                    <div class="sl-info-page__cta-inner">
                        <div class="sl-mono sl-info-page__cta-eyebrow"><?php esc_html_e('§ Domanda specifica?', 'saltelli'); ?></div>
                        <div>
                            <h2 class="sl-info-page__cta-h2">
                                <?php esc_html_e('La tua domanda', 'saltelli'); ?><br>
                                <em><?php esc_html_e('non è qui?', 'saltelli'); ?></em>
                            </h2>
                            <p class="sl-info-page__cta-p">
                                <?php esc_html_e('Trenta minuti di prima consulenza gratuita per la tua pratica specifica. In studio o online. Risposta entro 24 ore.', 'saltelli'); ?>
                            </p>
                            <a class="sl-info-page__cta-btn" href="<?php echo esc_url(home_url('/contatti/')); ?>">
                                <span><?php esc_html_e('Prenota un incontro', 'saltelli'); ?></span>
                                <span class="arrow" aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </section>

                <?php /* Schema FAQPage cumulativo (audit GEO §4.3 critical) */ ?>
                <?php
                $sl_faq_schema_entities = [];
                foreach ($sl_faq_topics as $sl_topic_id => $sl_topic) {
                    foreach ($sl_topic['faqs'] as $sl_qa) {
                        $sl_faq_schema_entities[] = [
                            '@type' => 'Question',
                            'name'  => $sl_qa[0],
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text'  => $sl_qa[1],
                            ],
                        ];
                    }
                }
                if (!empty($sl_faq_schema_entities) && function_exists('saltelli_emit_jsonld')) {
                    saltelli_emit_jsonld([
                        '@context'   => 'https://schema.org',
                        '@type'      => 'FAQPage',
                        '@id'        => get_permalink() . '#faq-aggregator',
                        'mainEntity' => $sl_faq_schema_entities,
                        'inLanguage' => 'it-IT',
                    ]);
                }
                ?>

            </article>

        <?php elseif (is_page(['guide-gratuite', 'come-lavoriamo', 'prima-consulenza', 'lavora-con-noi', 'richiedi-preventivo'])) :
            // === v0.33.0 — Info Page shared template (4 page informative) ===
            // Hero asym 8/4 + drop-cap 84px + body editorial + CTA dark navy.
            // Uses .sl-info-page__* shared CSS scope.
            $sl_info_data = [
                'guide-gratuite' => [
                    'eyebrow' => __('§ Risorse · Guide gratuite', 'saltelli'),
                    'h1'      => __('Schede sintetiche.', 'saltelli'),
                    'h1_em'   => null,
                    'lede'    => __('Scarica le nostre guide gratuite: dispense pratiche su materie ricorrenti, scritte dai nostri avvocati per privati e imprese.', 'saltelli'),
                    'trust_eyebrow'  => __('§ Disponibili', 'saltelli'),
                    'trust_headline' => __('12 schede in PDF · gratuite · no email obbligatoria', 'saltelli'),
                    'trust_list' => [__('✓ Senza registrazione', 'saltelli'), __('✓ Aggiornamento periodico', 'saltelli'), __('✓ Lettura ~10 minuti', 'saltelli')],
                    'cta_h2'      => __('Hai bisogno di un caso specifico?', 'saltelli'),
                    'cta_h2_em'   => null,
                    'cta_p'       => __('Le schede generali non sostituiscono una consulenza personalizzata. Trenta minuti gratuiti per valutare la tua pratica.', 'saltelli'),
                    'cta_btn'     => __('Prenota un primo incontro', 'saltelli'),
                ],
                'come-lavoriamo' => [
                    'eyebrow' => __('§ Lo studio · Come lavoriamo', 'saltelli'),
                    'h1'      => __('Ascolto prima,', 'saltelli'),
                    'h1_em'   => __('carte dopo.', 'saltelli'),
                    'lede'    => __('Crediamo che il diritto sia, prima di tutto, un\'arte di ascolto. Le carte vengono dopo. Per questo non offriamo pacchetti né formule.', 'saltelli'),
                    'trust_eyebrow'  => __('§ Tre principi', 'saltelli'),
                    'trust_headline' => __('Ascoltiamo · Lavoriamo in atelier · Diciamo la verità', 'saltelli'),
                    'trust_list' => [__('✓ Primo incontro gratuito', 'saltelli'), __('✓ Una pratica, un avvocato', 'saltelli'), __('✓ Onesta valutazione percorribilità', 'saltelli')],
                    'cta_h2'      => __('Vuoi raccontarci', 'saltelli'),
                    'cta_h2_em'   => __('la tua pratica?', 'saltelli'),
                    'cta_p'       => __('Trenta minuti di prima consulenza gratuita. In studio o online. Risposta entro 24 ore.', 'saltelli'),
                    'cta_btn'     => __('Prenota un incontro', 'saltelli'),
                ],
                'prima-consulenza' => [
                    'eyebrow' => __('§ Servizio · Prima consulenza', 'saltelli'),
                    'h1'      => __('Trenta minuti', 'saltelli'),
                    'h1_em'   => __('gratuiti.', 'saltelli'),
                    'lede'    => __('Trenta minuti di prima consulenza conoscitiva, gratuita. In studio a Chiaia o in videocall. Senza obblighi, senza costi nascosti.', 'saltelli'),
                    'trust_eyebrow'  => __('§ Modalità', 'saltelli'),
                    'trust_headline' => __('GRATUITA · 30 MIN · IN STUDIO O ONLINE', 'saltelli'),
                    'trust_list' => [__('✓ Nessun obbligo', 'saltelli'), __('✓ Nessun costo nascosto', 'saltelli'), __('✓ Riservatezza assoluta', 'saltelli')],
                    'cta_h2'      => __('Pronto?', 'saltelli'),
                    'cta_h2_em'   => __('Iniziamo.', 'saltelli'),
                    'cta_p'       => __('Risposta entro 24 ore. Riservatezza assoluta. Cancellazione 1 click.', 'saltelli'),
                    'cta_btn'     => __('Prenota un incontro', 'saltelli'),
                ],
                'lavora-con-noi' => [
                    'eyebrow' => __('§ Studio · Carriera', 'saltelli'),
                    'h1'      => __('Cerchiamo', 'saltelli'),
                    'h1_em'   => __('praticanti.', 'saltelli'),
                    'lede'    => __('Cerchiamo praticanti motivati e curiosi, disponibili a un percorso strutturato in tutte le materie dello studio. Nessuna formula stage-mascherato.', 'saltelli'),
                    'trust_eyebrow'  => __('§ Cosa offriamo', 'saltelli'),
                    'trust_headline' => __('Mentorship · 18 mesi · Compenso adeguato', 'saltelli'),
                    'trust_list' => [__('✓ Mentorship 1-1 con i quattro avvocati', 'saltelli'), __('✓ Rotazione su tutte le materie', 'saltelli'), __('✓ Compenso conforme al CCNL', 'saltelli')],
                    'cta_h2'      => __('Inviaci il tuo', 'saltelli'),
                    'cta_h2_em'   => __('curriculum.', 'saltelli'),
                    'cta_p'       => __('Solo CV reali, no autocandidature standardizzate. Risposta entro 7 giorni lavorativi.', 'saltelli'),
                    'cta_btn'     => __('Scrivici', 'saltelli'),
                ],
                'richiedi-preventivo' => [
                    'eyebrow' => __('§ Servizio · Richiedi un preventivo', 'saltelli'),
                    'h1'      => __('Richiedi un', 'saltelli'),
                    'h1_em'   => __('preventivo.', 'saltelli'),
                    'lede'    => __('Compila un breve modulo per ricevere un preventivo personalizzato. Trasparente, vincolato alla complessità reale della pratica.', 'saltelli'),
                    'trust_eyebrow'  => __('§ Come funziona', 'saltelli'),
                    'trust_headline' => __('Preventivo scritto in 48h', 'saltelli'),
                    'trust_list' => [__('✓ Risposta entro 24h', 'saltelli'), __('✓ Preventivo dettagliato scritto', 'saltelli'), __('✓ Onorario, contributo unificato, spese vive separati', 'saltelli')],
                    'cta_h2'      => __('Pronto a richiedere', 'saltelli'),
                    'cta_h2_em'   => __('un preventivo?', 'saltelli'),
                    'cta_p'       => __('Trenta minuti di prima consulenza gratuita prima del preventivo. Niente sorprese, niente costi nascosti.', 'saltelli'),
                    'cta_btn'     => __('Apri modulo', 'saltelli'),
                ],
            ];
            $sl_info_slug = get_post_field('post_name', get_the_ID());
            $sl_info = $sl_info_data[$sl_info_slug] ?? null;
            if ($sl_info) :
            ?>
            <article class="sl-info-page sl-info-page--<?php echo esc_attr($sl_info_slug); ?>">

                <header class="sl-info-page__hero sl-page-hero sl-page-hero--compact">
                    <div class="sl-info-page__hero-text">
                        <?php saltelli_render_breadcrumb(); ?>
                        <div class="sl-mono sl-info-page__hero-eyebrow"><?php echo esc_html($sl_info['eyebrow']); ?></div>
                        <h1 class="sl-info-page__h1" data-split-reveal>
                            <?php echo esc_html($sl_info['h1']); ?>
                            <?php if (!empty($sl_info['h1_em'])) : ?><br><em><?php echo esc_html($sl_info['h1_em']); ?></em><?php endif; ?>
                        </h1>
                        <p class="sl-info-page__lede"><?php echo esc_html($sl_info['lede']); ?></p>
                    </div>
                    <aside class="sl-info-page__trust">
                        <div class="sl-mono sl-info-page__trust-eyebrow"><?php echo esc_html($sl_info['trust_eyebrow']); ?></div>
                        <p class="sl-info-page__trust-headline"><?php echo esc_html($sl_info['trust_headline']); ?></p>
                        <ul class="sl-info-page__trust-list" role="list">
                            <?php foreach ($sl_info['trust_list'] as $sl_li) : ?>
                                <li><?php echo esc_html($sl_li); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </aside>
                </header>

                <section class="sl-info-page__body">
                    <div class="sl-mono sl-info-page__body-eyebrow"><?php esc_html_e('§ 01 — Approfondimento', 'saltelli'); ?></div>
                    <div class="sl-info-page__prose">
                        <?php
                        if (get_the_content() !== '') {
                            the_content();
                        } else {
                            // Fallback editorial content per ogni page
                            switch ($sl_info_slug) {
                                case 'guide-gratuite':
                                    echo '<p>' . esc_html__('Schede sintetiche e gratuite, scritte dai nostri avvocati per privati e imprese. Ogni guida copre una materia ricorrente con linguaggio chiaro, esempi reali, e indicazioni pratiche su come muoversi nei primi 7 giorni.', 'saltelli') . '</p>';
                                    echo '<p>' . esc_html__('Sono pensate per chi vuole una prima orientazione prima di decidere se rivolgersi a un avvocato. Le aggiorniamo periodicamente quando cambia la giurisprudenza o la normativa.', 'saltelli') . '</p>';
                                    break;
                                case 'come-lavoriamo':
                                    echo '<p>' . esc_html__('Ascoltiamo prima delle carte. Il primo incontro dura il tempo necessario, è gratuito e dedicato esclusivamente a capire la tua storia. Le scartoffie le firmiamo solo quando abbiamo capito cosa serve davvero.', 'saltelli') . '</p>';
                                    echo '<p>' . esc_html__('Lavoriamo in atelier: ogni pratica è seguita personalmente da uno dei quattro avvocati, dall\'inizio alla fine. Niente call center, niente passaggi di mano, niente "il collega le richiamerà".', 'saltelli') . '</p>';
                                    echo '<p>' . esc_html__('Diciamo la verità anche quando significa sconsigliare un\'azione legale. La nostra reputazione vale più di un mandato. Se la causa non è percorribile, te lo diciamo subito.', 'saltelli') . '</p>';
                                    break;
                                case 'prima-consulenza':
                                    echo '<p>' . esc_html__('Trenta minuti gratuiti, in studio a Chiaia o in videocall. Sufficienti per ascoltare la pratica, valutare la percorribilità e decidere insieme se procedere. Senza obblighi, senza costi nascosti.', 'saltelli') . '</p>';
                                    echo '<p>' . esc_html__('Solo dopo il primo incontro formuliamo un preventivo personalizzato basato su complessità, tempi e probabilità di esito. Il preventivo è scritto, fisso o a percentuale del beneficio. Lo concordiamo prima del mandato.', 'saltelli') . '</p>';
                                    break;
                                case 'lavora-con-noi':
                                    echo '<p>' . esc_html__('Cerchiamo praticanti motivati e curiosi, con voglia di studiare in profondità. Offriamo un percorso strutturato di 18 mesi su tutte le materie dello studio: tributario, lavoro, famiglia LGBTQ+, immobiliare, condominiale, contenzioso.', 'saltelli') . '</p>';
                                    echo '<p>' . esc_html__('Niente formula stage-mascherato. Compenso conforme al CCNL forense, mentorship 1-1 con i quattro avvocati, casi reali fin dalla prima settimana. Cerchiamo persone che vogliano fare l\'avvocato in maniera seria.', 'saltelli') . '</p>';
                                    break;
                            }
                        }
                        ?>
                    </div>
                </section>

                <section class="sl-info-page__cta">
                    <div class="sl-info-page__cta-inner">
                        <div class="sl-mono sl-info-page__cta-eyebrow"><?php esc_html_e('§ Pronto?', 'saltelli'); ?></div>
                        <div>
                            <h2 class="sl-info-page__cta-h2">
                                <?php echo esc_html($sl_info['cta_h2']); ?>
                                <?php if (!empty($sl_info['cta_h2_em'])) : ?><br><em><?php echo esc_html($sl_info['cta_h2_em']); ?></em><?php endif; ?>
                            </h2>
                            <p class="sl-info-page__cta-p"><?php echo esc_html($sl_info['cta_p']); ?></p>
                            <a class="sl-info-page__cta-btn" href="<?php echo esc_url(home_url('/contatti/')); ?>">
                                <span><?php echo esc_html($sl_info['cta_btn']); ?></span>
                                <span class="arrow" aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </section>

            </article>
            <?php endif; ?>

        <?php elseif (is_page('costi')) :
            // === v0.23.0 TASK B — /costi/ Sessione 2 pixel-perfect ===
            // JSX source: .claude/knowledge/design/sessione-2/saltelli-s2-costi.jsx
            // Bypassa post_content WP page id 2695 (legacy) — content hardcoded da JSX.
            // Schema FAQPage emesso solo se Yoast NOT attivo (vedi inc/schema/).
            $sl_costi_phone_label = '+39 081 1813 1119';
            $sl_costi_phone_href  = 'tel:+390818131119';
            ?>

            <article class="sl-costi-w4">

                <?php /* 1. HERO 8fr/4fr — h1 + lede SX, trust sticky DX */ ?>
                <header class="sl-costi-w4__hero sl-page-hero">
                    <div class="sl-container sl-costi-w4__hero-grid">
                        <div class="sl-costi-w4__hero-text">
                            <?php saltelli_render_breadcrumb(); ?>
                            <div class="sl-mono sl-costi-w4__hero-eyebrow">
                                <?php esc_html_e('§ Trasparenza · Costi e tariffe', 'saltelli'); ?>
                            </div>
                            <h1 class="sl-costi-w4__h1" data-split-reveal>
                                <?php
                                echo wp_kses(
                                    saltelli_split_h1_words(__('Costi e prima consulenza.', 'saltelli'), 'sl-costi-w4__h1-word'),
                                    ['span' => ['class' => true, 'data-i' => true]]
                                );
                                ?>
                            </h1>
                            <p class="sl-costi-w4__lede">
                                <?php esc_html_e('Trenta minuti gratuiti per ascoltarci, valutare insieme, decidere se procedere. Solo dopo, un preventivo personalizzato basato su complessità, tempi e probabilità di esito.', 'saltelli'); ?>
                            </p>
                        </div>
                        <aside class="sl-costi-w4__hero-trust">
                            <div class="sl-mono sl-costi-w4__hero-trust-eyebrow">
                                <?php esc_html_e('§ Prima consulenza', 'saltelli'); ?>
                            </div>
                            <div class="sl-costi-w4__hero-trust-headline">
                                <?php esc_html_e('GRATUITA · 30 MINUTI', 'saltelli'); ?><br>
                                <?php esc_html_e('IN STUDIO O ONLINE', 'saltelli'); ?>
                            </div>
                            <ul class="sl-costi-w4__hero-trust-list" role="list">
                                <li><span aria-hidden="true">✓</span> <?php esc_html_e('Nessun obbligo', 'saltelli'); ?></li>
                                <li><span aria-hidden="true">✓</span> <?php esc_html_e('Nessun costo nascosto', 'saltelli'); ?></li>
                                <li><span aria-hidden="true">✓</span> <?php esc_html_e('Riservatezza assoluta', 'saltelli'); ?></li>
                            </ul>
                            <a class="sl-btn sl-btn--primary" href="<?php echo esc_url(home_url('/contatti/')); ?>">
                                <span><?php esc_html_e('Prenota un incontro', 'saltelli'); ?></span>
                                <span class="arrow" aria-hidden="true">→</span>
                            </a>
                        </aside>
                    </div>
                </header>

                <?php /* 2. § 01 · Come funziona — 3 col scenari */ ?>
                <section class="sl-costi-w4__come" aria-labelledby="costi-w4-come-h">
                    <div class="sl-container">
                        <header class="sl-costi-w4__section-head">
                            <div class="sl-mono"><?php esc_html_e('§ 01 · Come funziona', 'saltelli'); ?></div>
                            <h2 class="sl-costi-w4__h2" id="costi-w4-come-h">
                                <?php esc_html_e('La prima consulenza, tre modalità.', 'saltelli'); ?>
                            </h2>
                        </header>
                        <div class="sl-costi-w4__come-grid">
                            <article class="sl-costi-w4__scenario-card">
                                <div class="sl-mono"><?php esc_html_e('01 / Modalità classica', 'saltelli'); ?></div>
                                <h3 class="sl-costi-w4__scenario-title"><?php esc_html_e('Vieni a Chiaia', 'saltelli'); ?></h3>
                                <p><?php esc_html_e('Via Vannella Gaetani 27, sala riunioni del nostro studio. Lunedì-venerdì 09:30-18:30, su appuntamento.', 'saltelli'); ?></p>
                                <div class="sl-mono sl-costi-w4__scenario-trust"><?php esc_html_e('Caffè incluso', 'saltelli'); ?></div>
                            </article>
                            <article class="sl-costi-w4__scenario-card">
                                <div class="sl-mono"><?php esc_html_e('02 / Modalità remota', 'saltelli'); ?></div>
                                <h3 class="sl-costi-w4__scenario-title"><?php esc_html_e('Videocall riservata', 'saltelli'); ?></h3>
                                <p><?php esc_html_e('Google Meet, Zoom o piattaforma a tua scelta. Ideale se vivi fuori Napoli o per pratiche urgenti.', 'saltelli'); ?></p>
                                <div class="sl-mono sl-costi-w4__scenario-trust"><?php esc_html_e('Stesso valore, zero spostamento', 'saltelli'); ?></div>
                            </article>
                            <article class="sl-costi-w4__scenario-card">
                                <div class="sl-mono"><?php esc_html_e('03 / Modalità rapida', 'saltelli'); ?></div>
                                <h3 class="sl-costi-w4__scenario-title"><?php esc_html_e('Per casi semplici', 'saltelli'); ?></h3>
                                <p><?php esc_html_e('Per situazioni che richiedono solo un primo orientamento o verifica di percorribilità.', 'saltelli'); ?></p>
                                <div class="sl-mono sl-costi-w4__scenario-trust"><?php esc_html_e('Massimo 30 minuti', 'saltelli'); ?></div>
                            </article>
                        </div>
                    </div>
                </section>

                <?php /* 3. § 02 · Cosa succede dopo i 30 minuti — 4fr/8fr */ ?>
                <section class="sl-costi-w4__scenari" aria-labelledby="costi-w4-dopo-h">
                    <div class="sl-container sl-costi-w4__scenari-grid">
                        <header class="sl-costi-w4__scenari-head">
                            <div class="sl-mono"><?php esc_html_e('§ 02 · Dopo i 30 minuti', 'saltelli'); ?></div>
                            <h2 class="sl-costi-w4__h2 sl-costi-w4__h2--italic" id="costi-w4-dopo-h">
                                <?php esc_html_e('Tre scenari possibili.', 'saltelli'); ?>
                            </h2>
                        </header>
                        <ol class="sl-costi-w4__scenari-list" role="list">
                            <li class="sl-costi-w4__scenari-item">
                                <span class="sl-mono sl-costi-w4__scenari-num">01</span>
                                <div>
                                    <div class="sl-mono sl-costi-w4__scenari-label"><?php esc_html_e('NON PROCEDIAMO', 'saltelli'); ?></div>
                                    <p><?php esc_html_e('Se la pratica non ha solidi presupposti, te lo diciamo subito. Ti suggeriamo un percorso alternativo o ti rimandiamo a un professionista più indicato.', 'saltelli'); ?></p>
                                    <div class="sl-mono sl-costi-w4__scenari-trust"><?php esc_html_e('Risparmio: 100% costi inutili', 'saltelli'); ?></div>
                                </div>
                            </li>
                            <li class="sl-costi-w4__scenari-item">
                                <span class="sl-mono sl-costi-w4__scenari-num">02</span>
                                <div>
                                    <div class="sl-mono sl-costi-w4__scenari-label"><?php esc_html_e('PRATICA SEMPLICE — TARIFFA FORFETTARIA', 'saltelli'); ?></div>
                                    <p><?php esc_html_e('Se la complessità è prevedibile, ti proponiamo un preventivo a forfait. Tutto incluso, nessuna sorpresa successiva.', 'saltelli'); ?></p>
                                    <div class="sl-mono sl-costi-w4__scenari-trust"><?php esc_html_e('Trasparenza: tariffa fissa concordata', 'saltelli'); ?></div>
                                </div>
                            </li>
                            <li class="sl-costi-w4__scenari-item">
                                <span class="sl-mono sl-costi-w4__scenari-num">03</span>
                                <div>
                                    <div class="sl-mono sl-costi-w4__scenari-label"><?php esc_html_e('PRATICA COMPLESSA — TARIFFA ORARIA', 'saltelli'); ?></div>
                                    <p><?php esc_html_e('Se richiede analisi approfondita o iter giudiziale lungo, formuliamo preventivo orario con stima totale + check-in ogni 10 ore lavorate.', 'saltelli'); ?></p>
                                    <div class="sl-mono sl-costi-w4__scenari-trust"><?php esc_html_e('Controllo: budget capped + reportistica', 'saltelli'); ?></div>
                                </div>
                            </li>
                        </ol>
                    </div>
                </section>

                <?php /* 4. § 03 · Come calcoliamo — 6fr/6fr drop-cap T */ ?>
                <section class="sl-costi-w4__calc" aria-labelledby="costi-w4-calc-h">
                    <div class="sl-container">
                        <header class="sl-costi-w4__section-head">
                            <div class="sl-mono"><?php esc_html_e('§ 03 · Metodologia', 'saltelli'); ?></div>
                            <h2 class="sl-costi-w4__h2" id="costi-w4-calc-h">
                                <?php esc_html_e('Come calcoliamo i preventivi.', 'saltelli'); ?>
                            </h2>
                        </header>
                        <div class="sl-costi-w4__calc-grid">
                            <div class="sl-costi-w4__calc-prose">
                                <p>
                                    <?php esc_html_e("Trasparenza è la nostra prima regola. I nostri preventivi considerano tre fattori: complessità della pratica (analisi atti, ricerca giurisprudenza, perizie tecniche), tempo stimato (ore di lavoro su atti, udienze, comunicazioni), probabilità di esito favorevole (incide sulla strategia consigliata).", 'saltelli'); ?>
                                </p>
                                <p>
                                    <?php
                                    echo wp_kses(
                                        __("Quando possibile, lavoriamo a tariffa forfettaria: ti diamo un numero finale al primo incontro e quello rimane. Quando la complessità non lo permette, lavoriamo a tariffa oraria con budget cap concordato in anticipo. <em>Niente fatturazione a sorpresa, mai.</em>", 'saltelli'),
                                        ['em' => []]
                                    );
                                    ?>
                                </p>
                            </div>
                            <div class="sl-costi-w4__calc-cards">
                                <article class="sl-costi-w4__calc-card">
                                    <div class="sl-mono"><?php esc_html_e('Fattore 1', 'saltelli'); ?></div>
                                    <h4 class="sl-costi-w4__calc-h4"><?php esc_html_e('Analisi della pratica', 'saltelli'); ?></h4>
                                    <p><?php esc_html_e('Tipologia atti, normativa applicabile, giurisprudenza di riferimento e perizie tecniche eventuali.', 'saltelli'); ?></p>
                                </article>
                                <article class="sl-costi-w4__calc-card">
                                    <div class="sl-mono"><?php esc_html_e('Fattore 2', 'saltelli'); ?></div>
                                    <h4 class="sl-costi-w4__calc-h4"><?php esc_html_e('Ore stimate', 'saltelli'); ?></h4>
                                    <p><?php esc_html_e('Redazione atti, partecipazione a udienze, comunicazioni con controparte, contraddittorio.', 'saltelli'); ?></p>
                                </article>
                                <article class="sl-costi-w4__calc-card">
                                    <div class="sl-mono"><?php esc_html_e('Fattore 3', 'saltelli'); ?></div>
                                    <h4 class="sl-costi-w4__calc-h4"><?php esc_html_e('Probabilità', 'saltelli'); ?></h4>
                                    <p><?php esc_html_e('Incide sulla strategia consigliata e sul timing. Influenza la scelta forfait vs orario.', 'saltelli'); ?></p>
                                </article>
                            </div>
                        </div>
                    </div>
                </section>

                <?php /* 5. § 04 · FAQ accordion 5Q */ ?>
                <section class="sl-costi-w4__faq" aria-labelledby="costi-w4-faq-h">
                    <div class="sl-container">
                        <header class="sl-costi-w4__section-head">
                            <div class="sl-mono"><?php esc_html_e('§ 04 · Sui costi, in chiaro', 'saltelli'); ?></div>
                            <h2 class="sl-costi-w4__h2" id="costi-w4-faq-h">
                                <?php esc_html_e('Domande frequenti sui costi.', 'saltelli'); ?>
                            </h2>
                        </header>
                        <div class="sl-acc sl-costi-w4__faq-list" data-sl-acc>
                            <?php
                            $sl_costi_faq = [
                                [
                                    'q' => __('Quanto costa una pratica di diritto tributario?', 'saltelli'),
                                    'a' => '<p>' . __('Range orientativo <strong>800–3500€</strong> a seconda di tipologia atto (cartella semplice → ricorso CTP/CGT), importo contestato e necessità di periti tecnici.', 'saltelli') . '</p><p><em>' . esc_html__('Esempio reale', 'saltelli') . '</em>: ' . esc_html__('opposizione cartella esattoriale 5.000€ → forfait 1.200€ + 200€ contributo unificato.', 'saltelli') . '</p>',
                                ],
                                [
                                    'q' => __('Pagamento dilazionato è possibile?', 'saltelli'),
                                    'a' => '<p>' . esc_html__('Sì per pratiche oltre 1.500€. Concordiamo rate trimestrali in funzione del flusso atti. Trasparenza totale: nessun interesse, solo dilazione fisica.', 'saltelli') . '</p>',
                                ],
                                [
                                    'q' => __('Se non vinco, devo comunque pagare?', 'saltelli'),
                                    'a' => '<p>' . esc_html__("Sì. Le tariffe forensi prevedono onorari per il lavoro svolto, indipendentemente dall'esito (è regola del Codice deontologico). Quello che possiamo fare: valutare seriamente in prima consulenza se la causa è effettivamente percorribile.", 'saltelli') . '</p>',
                                ],
                                [
                                    'q' => __('Il primo incontro è davvero gratuito?', 'saltelli'),
                                    'a' => '<p>' . esc_html__('Sì, sempre. Trenta minuti senza costi né obblighi. Se decidi di non procedere, abbiamo solo investito tempo. Il nostro tempo costa solo se decidiamo insieme di procedere.', 'saltelli') . '</p>',
                                ],
                                [
                                    'q' => __('Recupero crediti: solo se vinciamo?', 'saltelli'),
                                    'a' => '<p>' . esc_html__('Per pratiche specifiche di recupero crediti < 5.000€ proponiamo success fee (X% sul recuperato + spese vive). Da concordare in prima consulenza in base alla concretezza del credito.', 'saltelli') . '</p>',
                                ],
                            ];
                            foreach ($sl_costi_faq as $i => $row) :
                                $is_open = ($i === 3); // Q4 default open per CRO emphasis
                                ?>
                                <div class="sl-acc__item" data-open="<?php echo $is_open ? 'true' : 'false'; ?>">
                                    <button class="sl-acc__btn" type="button" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="costi-faq-panel-<?php echo (int) $i; ?>">
                                        <span><?php echo esc_html($row['q']); ?></span>
                                        <span class="sl-acc__icon" aria-hidden="true">+</span>
                                    </button>
                                    <div class="sl-acc__panel" id="costi-faq-panel-<?php echo (int) $i; ?>">
                                        <div class="sl-acc__inner">
                                            <?php echo wp_kses_post($row['a']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <?php /* 6. § 05 · Trust signals 4-col grid */ ?>
                <section class="sl-costi-w4__trust-grid" aria-label="<?php esc_attr_e('Garanzie e trust signals', 'saltelli'); ?>">
                    <div class="sl-container">
                        <ul class="sl-costi-w4__trust-list" role="list">
                            <li class="sl-costi-w4__trust-plate sl-mono"><?php esc_html_e('Iscritti Ordine Avvocati Napoli', 'saltelli'); ?></li>
                            <li class="sl-costi-w4__trust-plate sl-mono"><?php esc_html_e('P.IVA 06685101211', 'saltelli'); ?></li>
                            <li class="sl-costi-w4__trust-plate sl-mono"><?php esc_html_e('Codice deontologico forense', 'saltelli'); ?></li>
                            <li class="sl-costi-w4__trust-plate sl-mono"><?php esc_html_e('Riservatezza assoluta', 'saltelli'); ?></li>
                        </ul>
                    </div>
                </section>

                <?php /* 7. CTA finale editoriale */ ?>
                <section class="sl-costi-w4__cta-final">
                    <div class="sl-container">
                        <div class="sl-mono"><?php esc_html_e('§ Pronto?', 'saltelli'); ?></div>
                        <h2 class="sl-costi-w4__cta-h2">
                            <?php esc_html_e('La prima consulenza è gratuita. Sempre.', 'saltelli'); ?>
                        </h2>
                        <p class="sl-costi-w4__cta-sub">
                            <?php esc_html_e('Trenta minuti per ascoltarci, valutare insieme, capire se possiamo esserti utili. Senza obblighi e senza costi nascosti.', 'saltelli'); ?>
                        </p>
                        <a class="sl-btn sl-btn--primary" href="<?php echo esc_url(home_url('/contatti/')); ?>">
                            <span><?php esc_html_e('Prenota un incontro', 'saltelli'); ?></span>
                            <span class="arrow" aria-hidden="true">→</span>
                        </a>
                        <div class="sl-mono sl-costi-w4__cta-trust">
                            <?php esc_html_e('Risposta entro 24 ore · Riservatezza assoluta', 'saltelli'); ?>
                        </div>
                    </div>
                </section>

            </article>

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

                <h1 class="sl-page__title" data-split-reveal><?php echo wp_kses(saltelli_split_h1_words(get_the_title()), ['span' => ['class' => true, 'data-i' => true]]); ?></h1>
            </div>
        </header>

        <section class="sl-page__content">
            <div class="sl-container">
                <div class="sl-page__prose"><?php the_content(); ?></div>
            </div>
        </section>

        <?php endif; // sl_chi_siamo / sl_casi / contatti / glossario / default ?>

    </article>
    <?php
endwhile;

get_footer();
