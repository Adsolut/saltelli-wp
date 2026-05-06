<?php
/**
 * Template part: page-lo-studio.php
 *
 * Render della page /chi-siamo/lo-studio/ — la pagina "Lo Studio" sotto l'hub Chi Siamo.
 * Wave 5 IA refactor: rinominato da page-chi-siamo.php (slug 'chi-siamo' è ora la pagina HUB).
 * NO Field Group ACF dedicato — content hardcoded resta inalterato.
 *
 * Le CSS classes `.sl-chi-siamo__*` mantengono il prefix legacy per non rompere
 * sections.css existing rules — sono semantiche del template, non della page slug.
 *
 * Sezioni:
 *  § 01 — Lede + drop-cap
 *  § 02 — Founding 1999 (atelier napoletano)
 *  § 03 — Team mini (4 lawyer da CPT)
 *  § 04 — Tre principi (CPT saltelli_principio se popolato, altrimenti hardcoded)
 *  § 05 — Cronologia 1999 → 2026 (timeline hardcoded)
 *  § 06 — CTA finale
 *
 * @package Saltelli
 * @since 1.0.0 Wave 3 (orig. page-chi-siamo.php)
 * @since 1.1.0 Wave 5 (renamed page-lo-studio.php + CAL-05)
 */
defined('ABSPATH') || exit;

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

// § 04 Principi — query CPT (Wave 2 popolato), fallback hardcoded.
$sl_principles_posts = get_posts([
    'post_type'   => 'saltelli_principio',
    'numberposts' => 3,
    'orderby'     => 'menu_order',
    'order'       => 'ASC',
]);
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
                    // Wave 3: prefer hero_role (Wave 1 schema) → fallback ruolo_breve (legacy Wave 0).
                    $ruolo_av = (string) saltelli_field('hero_role', $av->ID, '');
                    if ($ruolo_av === '') {
                        $ruolo_av = (string) saltelli_field('ruolo_breve', $av->ID, '');
                    }
                    $specs_av = saltelli_get_attorney_specializations($av->ID);
                    $foto_av  = saltelli_field('foto_ritratto', $av->ID);
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
                // Wave 3: prefer CPT saltelli_principio (Wave 2 popolato), fallback hardcoded.
                if (!empty($sl_principles_posts)) :
                    foreach ($sl_principles_posts as $pp) :
                        $pn = (string) saltelli_field('num', $pp->ID, '');
                        $pt = (string) saltelli_field('title', $pp->ID, get_the_title($pp));
                        $pd = (string) saltelli_field('desc', $pp->ID, '');
                        ?>
                        <li class="sl-chi-siamo__principle">
                            <span class="sl-mono sl-chi-siamo__principle-n"><?php echo esc_html($pn); ?></span>
                            <div class="sl-chi-siamo__principle-body">
                                <h3 class="sl-chi-siamo__principle-t"><?php echo esc_html($pt); ?></h3>
                                <p class="sl-chi-siamo__principle-d"><?php echo esc_html($pd); ?></p>
                            </div>
                        </li>
                        <?php
                    endforeach;
                else :
                    // Fallback editoriale hardcoded (pre-Wave2).
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
                    <?php endforeach;
                endif;
                ?>
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
