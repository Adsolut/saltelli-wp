<?php
/**
 * Template part: page-costi.php
 *
 * Render della page /costi/. Hero+aside+§03 calc body+CTA da ACF (Wave 2).
 * Modalità (3) + Scenari (3) + Trust plates (4) ora dal CPT (Wave 2 popolato 10 items).
 * FAQ list (5) resta hardcoded (HTML formattato fuori scope CPT plain-text).
 *
 * @package Saltelli
 * @since 1.0.0 Wave 3
 */
defined('ABSPATH') || exit;

$pid = get_the_ID();

// Hero
$hero_eyebrow = saltelli_field('hero_eyebrow', $pid, '§ Trasparenza · Costi e tariffe');
$hero_h1_pre  = saltelli_field('hero_h1_pre', $pid, 'Costi e prima');
$hero_h1_em   = saltelli_field('hero_h1_em', $pid, 'consulenza.');
$hero_lede    = saltelli_field('hero_lede', $pid, 'Trenta minuti gratuiti per ascoltarci, valutare insieme, decidere se procedere. Solo dopo, un preventivo personalizzato basato su complessità, tempi e probabilità di esito.');

// Aside trust box
$aside_eyebrow   = saltelli_field('aside_eyebrow', $pid, '§ Prima consulenza');
$aside_h3        = saltelli_field('aside_h3', $pid, 'GRATUITA · 30 MINUTI · IN STUDIO O ONLINE');
$aside_p         = saltelli_field('aside_p', $pid, 'Nessun obbligo · Nessun costo nascosto · Riservatezza assoluta');
$aside_cta_label = saltelli_field('aside_cta_label', $pid, 'Prenota un incontro');
$aside_cta_url   = saltelli_field('aside_cta_url', $pid, '/contatti/');

// § 03 Body editorial
$calc_body = saltelli_field('calc_body', $pid, '');

// CTA finale
$cta_eyebrow = saltelli_field('cta_eyebrow', $pid, '§ Pronto?');
$cta_h2      = saltelli_field('cta_h2', $pid, 'La prima consulenza è gratuita. Sempre.');
$cta_p       = saltelli_field('cta_p', $pid, 'Trenta minuti per ascoltarci, valutare insieme, capire se possiamo esserti utili. Senza obblighi e senza costi nascosti.');
$cta_label   = saltelli_field('cta_label', $pid, 'Prenota un incontro');
$cta_url     = saltelli_field('cta_url', $pid, '/contatti/');
$cta_trust   = saltelli_field('cta_trust', $pid, 'Risposta entro 24 ore · Riservatezza assoluta');

// CPT items: modalità, scenari, trust (Wave 2 popolato)
$modalita_items = get_posts(['post_type' => 'saltelli_modalita', 'numberposts' => 3, 'orderby' => 'menu_order', 'order' => 'ASC']);
$scenari_items  = get_posts(['post_type' => 'saltelli_scenario', 'numberposts' => 3, 'orderby' => 'menu_order', 'order' => 'ASC']);
$trust_items    = get_posts(['post_type' => 'saltelli_trust',    'numberposts' => 4, 'orderby' => 'menu_order', 'order' => 'ASC']);

// FAQ /costi/ — hardcoded HTML-formatted (HTML rich content out of CPT plain-text scope).
$sl_costi_phone_label = '+39 081 1813 1119';
$sl_costi_phone_href  = 'tel:+390818131119';
?>

<article class="sl-costi-w4">

    <header class="sl-costi-w4__hero sl-page-hero">
        <div class="sl-container sl-costi-w4__hero-grid">
            <div class="sl-costi-w4__hero-text">
                <?php saltelli_render_breadcrumb(); ?>
                <div class="sl-mono sl-costi-w4__hero-eyebrow"><?php echo esc_html($hero_eyebrow); ?></div>
                <h1 class="sl-costi-w4__h1" data-split-reveal>
                    <?php
                    $sl_costi_h1 = esc_html($hero_h1_pre) . ' ' . esc_html($hero_h1_em);
                    echo wp_kses(
                        saltelli_split_h1_words($sl_costi_h1, 'sl-costi-w4__h1-word'),
                        ['span' => ['class' => true, 'data-i' => true]]
                    );
                    ?>
                </h1>
                <p class="sl-costi-w4__lede"><?php echo esc_html($hero_lede); ?></p>
            </div>
            <aside class="sl-costi-w4__hero-trust">
                <div class="sl-mono sl-costi-w4__hero-trust-eyebrow"><?php echo esc_html($aside_eyebrow); ?></div>
                <div class="sl-costi-w4__hero-trust-headline">
                    <?php
                    // aside_h3 contiene "GRATUITA · 30 MINUTI · IN STUDIO O ONLINE" (3 segmenti separati da " · ").
                    // Pre-Wave3 markup: due righe ("GRATUITA · 30 MINUTI" / "IN STUDIO O ONLINE").
                    $h3_segs = preg_split('/\s+·\s+/', $aside_h3);
                    if (is_array($h3_segs) && count($h3_segs) >= 3) {
                        echo esc_html($h3_segs[0] . ' · ' . $h3_segs[1]) . '<br>' . esc_html($h3_segs[2]);
                    } else {
                        echo esc_html($aside_h3);
                    }
                    ?>
                </div>
                <ul class="sl-costi-w4__hero-trust-list" role="list">
                    <?php
                    // aside_p contiene "Nessun obbligo · Nessun costo nascosto · Riservatezza assoluta" (3 bullet).
                    $aside_bullets = preg_split('/\s+·\s+/', $aside_p);
                    foreach ($aside_bullets as $b) :
                        if (trim($b) === '') continue;
                        ?>
                        <li><span aria-hidden="true">✓</span> <?php echo esc_html(trim($b)); ?></li>
                        <?php
                    endforeach;
                    ?>
                </ul>
                <a class="sl-btn sl-btn--primary" href="<?php echo esc_url($aside_cta_url); ?>">
                    <span><?php echo esc_html(rtrim($aside_cta_label, ' →')); ?></span>
                    <span class="arrow" aria-hidden="true">→</span>
                </a>
            </aside>
        </div>
    </header>

    <?php /* 2. § 01 · Come funziona — 3 col modalità (CPT) */ ?>
    <section class="sl-costi-w4__come" aria-labelledby="costi-w4-come-h">
        <div class="sl-container">
            <header class="sl-costi-w4__section-head">
                <div class="sl-mono"><?php esc_html_e('§ 01 · Come funziona', 'saltelli'); ?></div>
                <h2 class="sl-costi-w4__h2" id="costi-w4-come-h">
                    <?php esc_html_e('La prima consulenza, tre modalità.', 'saltelli'); ?>
                </h2>
            </header>
            <div class="sl-costi-w4__come-grid">
                <?php if (!empty($modalita_items)) : ?>
                    <?php foreach ($modalita_items as $m) :
                        $num   = (string) saltelli_field('num_label', $m->ID, '');
                        $title = (string) saltelli_field('title', $m->ID, get_the_title($m));
                        $body  = (string) saltelli_field('body', $m->ID, '');
                        $trust = (string) saltelli_field('trust_mini', $m->ID, '');
                        ?>
                        <article class="sl-costi-w4__scenario-card">
                            <div class="sl-mono"><?php echo esc_html($num); ?></div>
                            <h3 class="sl-costi-w4__scenario-title"><?php echo esc_html($title); ?></h3>
                            <p><?php echo esc_html($body); ?></p>
                            <?php if ($trust !== '') : ?>
                                <div class="sl-mono sl-costi-w4__scenario-trust"><?php echo esc_html($trust); ?></div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php else : ?>
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
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php /* 3. § 02 · Cosa succede dopo i 30 minuti — 4fr/8fr scenari (CPT) */ ?>
    <section class="sl-costi-w4__scenari" aria-labelledby="costi-w4-dopo-h">
        <div class="sl-container sl-costi-w4__scenari-grid">
            <header class="sl-costi-w4__scenari-head">
                <div class="sl-mono"><?php esc_html_e('§ 02 · Dopo i 30 minuti', 'saltelli'); ?></div>
                <h2 class="sl-costi-w4__h2 sl-costi-w4__h2--italic" id="costi-w4-dopo-h">
                    <?php esc_html_e('Tre scenari possibili.', 'saltelli'); ?>
                </h2>
            </header>
            <ol class="sl-costi-w4__scenari-list" role="list">
                <?php if (!empty($scenari_items)) : foreach ($scenari_items as $s) :
                    $num   = (string) saltelli_field('num_label', $s->ID, '');
                    // num format "01 / NON PROCEDIAMO" → split per number+label
                    $num_parts = preg_split('/\s*\/\s*/', $num, 2);
                    $num_short = $num_parts[0] ?? $num;
                    $num_lbl   = $num_parts[1] ?? '';
                    $body  = (string) saltelli_field('body', $s->ID, '');
                    $trust = (string) saltelli_field('trust_mini', $s->ID, '');
                    ?>
                    <li class="sl-costi-w4__scenari-item">
                        <span class="sl-mono sl-costi-w4__scenari-num"><?php echo esc_html($num_short); ?></span>
                        <div>
                            <?php if ($num_lbl !== '') : ?>
                                <div class="sl-mono sl-costi-w4__scenari-label"><?php echo esc_html($num_lbl); ?></div>
                            <?php endif; ?>
                            <p><?php echo esc_html($body); ?></p>
                            <?php if ($trust !== '') : ?>
                                <div class="sl-mono sl-costi-w4__scenari-trust"><?php echo esc_html($trust); ?></div>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; endif; ?>
            </ol>
        </div>
    </section>

    <?php /* 4. § 03 · Come calcoliamo — 6fr/6fr drop-cap T (calc_body wysiwyg da ACF) */ ?>
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
                    <?php
                    if ($calc_body !== '') {
                        echo wp_kses_post($calc_body);
                    } else {
                        ?>
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
                        <?php
                    }
                    ?>
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

    <?php /* 5. § 04 · FAQ accordion 5Q — hardcoded (HTML rich content) */ ?>
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
                    $is_open = ($i === 3);
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

    <?php /* 6. § 05 · Trust signals 4-col grid (CPT) */ ?>
    <section class="sl-costi-w4__trust-grid" aria-label="<?php esc_attr_e('Garanzie e trust signals', 'saltelli'); ?>">
        <div class="sl-container">
            <ul class="sl-costi-w4__trust-list" role="list">
                <?php if (!empty($trust_items)) : foreach ($trust_items as $t) :
                    // valore field contiene il full-text trust signal.
                    $valore = (string) saltelli_field('valore', $t->ID, get_the_title($t));
                    ?>
                    <li class="sl-costi-w4__trust-plate sl-mono"><?php echo esc_html($valore); ?></li>
                <?php endforeach; else : ?>
                    <li class="sl-costi-w4__trust-plate sl-mono"><?php esc_html_e('Iscritti Ordine Avvocati Napoli', 'saltelli'); ?></li>
                    <li class="sl-costi-w4__trust-plate sl-mono"><?php esc_html_e('P.IVA 06685101211', 'saltelli'); ?></li>
                    <li class="sl-costi-w4__trust-plate sl-mono"><?php esc_html_e('Codice deontologico forense', 'saltelli'); ?></li>
                    <li class="sl-costi-w4__trust-plate sl-mono"><?php esc_html_e('Riservatezza assoluta', 'saltelli'); ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </section>

    <?php /* 7. CTA finale editoriale */ ?>
    <section class="sl-costi-w4__cta-final">
        <div class="sl-container">
            <div class="sl-mono"><?php echo esc_html($cta_eyebrow); ?></div>
            <h2 class="sl-costi-w4__cta-h2">
                <?php echo esc_html($cta_h2); ?>
            </h2>
            <p class="sl-costi-w4__cta-sub">
                <?php echo esc_html($cta_p); ?>
            </p>
            <a class="sl-btn sl-btn--primary" href="<?php echo esc_url($cta_url); ?>">
                <span><?php echo esc_html(rtrim($cta_label, ' →')); ?></span>
                <span class="arrow" aria-hidden="true">→</span>
            </a>
            <div class="sl-mono sl-costi-w4__cta-trust">
                <?php echo esc_html($cta_trust); ?>
            </div>
        </div>
    </section>

</article>
