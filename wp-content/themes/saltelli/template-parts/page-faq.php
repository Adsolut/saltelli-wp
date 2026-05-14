<?php
/**
 * Template part: page-faq.php
 *
 * Render della page /faq/ aggregator. Hero+TOC+CTA da ACF (Wave 2),
 * 28 FAQ items now query CPT saltelli_faq grouped by faq_topic taxonomy
 * (replace hardcoded $sl_faq_topics array pre-Wave3).
 *
 * @package Saltelli
 * @since 1.0.0 Wave 3
 */
defined('ABSPATH') || exit;

$pid = get_the_ID();

// Hero
$hero_eyebrow = saltelli_field('hero_eyebrow', $pid, '§ Risorse · Domande frequenti');
$hero_h1_pre  = saltelli_field('hero_h1_pre', $pid, 'Domande');
$hero_h1_em   = saltelli_field('hero_h1_em', $pid, 'frequenti.');
$hero_lede    = saltelli_field('hero_lede', $pid, "Le domande più ricorrenti che ci pongono privati e imprese. Sei aree tematiche, oltre 28 risposte, raccolte in un'unica pagina.");

// TOC
$toc_title = saltelli_field('toc_title', $pid, 'Indice');

// CTA
$cta_eyebrow = saltelli_field('cta_eyebrow', $pid, '§ Domanda specifica?');
$cta_h2_full = saltelli_field('cta_h2', $pid, 'La tua domanda non è qui?');
$cta_p       = saltelli_field('cta_p', $pid, 'Trenta minuti di prima consulenza gratuita per la tua pratica specifica. In studio o online. Risposta entro 24 ore.');
$cta_label   = saltelli_field('cta_label', $pid, 'Prenota un incontro');
$cta_url     = saltelli_field('cta_url', $pid, '/contatti/');

// Topic eyebrow + H2 mapping (preserve pre-Wave3 ordering + headlines).
// Topic ordering: tributario · lavoro · lgbtq · costi · metodo · prima-consulenza
$topic_meta = [
    'tributario'        => ['eyebrow' => '§ 01 — Diritto tributario',  'h2' => 'Cartelle, accertamenti, contenzioso.'],
    'lavoro'            => ['eyebrow' => '§ 02 — Diritto del lavoro',  'h2' => 'Licenziamenti, mobbing, INPS.'],
    'lgbtq'             => ['eyebrow' => '§ 03 — Famiglia LGBTQ+',     'h2' => 'Unioni civili, PMA, stepchild.'],
    'costi'             => ['eyebrow' => '§ 04 — Costi e tariffe',     'h2' => 'Trasparenza, dilazione, success fee.'],
    'metodo'            => ['eyebrow' => '§ 05 — Come lavoriamo',      'h2' => 'Atelier, ascolto, verità.'],
    'prima-consulenza'  => ['eyebrow' => '§ 06 — Prima consulenza',    'h2' => 'Trenta minuti, gratuiti, senza obbligo.'],
];

// Build FAQ topics array from CPT (preserve order via $topic_meta keys).
$sl_faq_topics = [];
foreach ($topic_meta as $topic_slug => $meta) {
    $faqs_in_topic = get_posts([
        'post_type'   => 'saltelli_faq',
        'numberposts' => -1,
        'tax_query'   => [
            ['taxonomy' => 'faq_topic', 'field' => 'slug', 'terms' => [$topic_slug]],
        ],
        'orderby' => 'menu_order',
        'order'   => 'ASC',
    ]);
    if (empty($faqs_in_topic)) continue;
    $faq_pairs = [];
    foreach ($faqs_in_topic as $f) {
        $faq_pairs[] = [
            (string) saltelli_field('domanda', $f->ID, get_the_title($f)),
            (string) saltelli_field('risposta', $f->ID, ''),
        ];
    }
    $sl_faq_topics[$topic_slug] = [
        'eyebrow' => $meta['eyebrow'],
        'h2'      => $meta['h2'],
        'faqs'    => $faq_pairs,
    ];
}

// Fallback: se CPT vuoto, ritorna a hardcoded pre-Wave 2 (5 topic editorial baseline).
if (empty($sl_faq_topics)) {
    $sl_faq_topics = [
        'tributario' => [
            'eyebrow' => __('§ 01 — Diritto tributario', 'saltelli'),
            'h2'      => __('Cartelle, accertamenti, contenzioso.', 'saltelli'),
            'faqs'    => [
                ['Quando conviene impugnare una cartella esattoriale?', 'Le cartelle vanno impugnate entro 60 giorni dalla notifica davanti alla Corte di Giustizia Tributaria competente.'],
            ],
        ],
    ];
}
?>
<article class="sl-faq-aggregator">

    <header class="sl-faq-aggregator__hero sl-page-hero">
        <div>
            <?php saltelli_render_breadcrumb(); ?>
            <div class="sl-mono" style="margin-bottom: 32px;"><?php echo esc_html($hero_eyebrow); ?></div>
            <h1 class="sl-faq-aggregator__h1" data-split-reveal>
                <?php echo esc_html($hero_h1_pre); ?><?php if ($hero_h1_em !== '') : ?><br>
                <em><?php echo esc_html($hero_h1_em); ?></em><?php endif; ?>
            </h1>
            <p class="sl-faq-aggregator__lede"><?php echo esc_html($hero_lede); ?></p>
        </div>
    </header>

    <section class="sl-faq-aggregator__body">
        <aside class="sl-faq-aggregator__toc" aria-label="<?php esc_attr_e('Indice domande', 'saltelli'); ?>">
            <div class="sl-mono sl-faq-aggregator__toc-label"><?php echo esc_html($toc_title); ?></div>
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
                                        <?php // Wave-Q fix #20 (feedback Elena): la risposta SCF saltelli_faq:risposta è inserita come HTML
                                              // (paragrafi <p>, link <a>) dall'editor. Pre-fix usava esc_html() → "&lt;p&gt;" letterale a video.
                                              // Switch a wp_kses_post() → render normale ma sanitizzato (tag safe-list WP). ?>
                                        <?php echo wp_kses_post($sl_qa[1]); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    </section>

    <?php /* Elena fix 2026-05-14: rimossa <section sl-info-page__cta>
       (CTA finale dark navy "La tua FAQ non è qui?") — ridondante con footer
       pre-CTA "§ Contattaci" cross-page. Helper vars $cta_eyebrow, $cta_h2_full,
       $cta_p, $cta_url, $cta_label restano definiti sopra (rimangono dead vars,
       cleanup minore Wave 6.1). */ ?>

    <?php
    // Schema FAQPage cumulativo (audit GEO §4.3 critical).
    $sl_faq_schema_entities = [];
    foreach ($sl_faq_topics as $sl_topic) {
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
