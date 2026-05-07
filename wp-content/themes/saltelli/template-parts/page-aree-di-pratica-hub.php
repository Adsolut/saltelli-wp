<?php
/**
 * Template part: page-aree-di-pratica-hub.php
 *
 * Render della page hub /aree-di-pratica/ — pagina madre del cluster Aree di Pratica.
 * Wave 5 IA refactor: rimpiazza l'archive CPT competenza in cima alla gerarchia URL.
 * I cluster (privati / imprese / contenzioso-amministrativo) sono term tipo-area.
 *
 * Layout: hero editoriale + grid 3 cluster cards (con conteggio dinamico) + CTA.
 *
 * @package Saltelli
 * @since 1.1.0 Wave 5
 */
defined('ABSPATH') || exit;

$sl_clusters = [
    [
        'slug'    => 'privati',
        'num'     => '01 / 03',
        'title'   => __('Per i privati', 'saltelli'),
        'desc'    => __('Famiglie e persone fisiche, lavoratori. Materie: tributario, lavoro, famiglia LGBTQ+, successioni, infortunistica, penale, bancario, condominio, immigrazione.', 'saltelli'),
    ],
    [
        'slug'    => 'imprese',
        'num'     => '02 / 03',
        'title'   => __('Per le imprese', 'saltelli'),
        'desc'    => __('Aziende, freelance, partite IVA. Recupero crediti, domiciliazione d\'impresa, contenzioso commerciale.', 'saltelli'),
    ],
    [
        'slug'    => 'contenzioso-amministrativo',
        'num'     => '03 / 03',
        'title'   => __('Contenzioso amministrativo', 'saltelli'),
        'desc'    => __('Ricorsi al TAR e al Consiglio di Stato. Atti della pubblica amministrazione, concessioni, procedure di gara.', 'saltelli'),
    ],
];
?>

<section class="sl-page-hero sl-hub-hero" aria-labelledby="hub-aree-h1">
    <div class="sl-container sl-hub-hero__inner">
        <?php saltelli_render_breadcrumb(); ?>
        <p class="sl-mono sl-hub-hero__eyebrow"><?php esc_html_e('§ Aree di pratica', 'saltelli'); ?></p>
        <h1 class="sl-page__title sl-hub-hero__h1" id="hub-aree-h1" data-split-reveal>
            <?php
            $sl_h1 = esc_html__('Diciassette aree,', 'saltelli') . '<br>'
                . '<em>' . esc_html__('tre cluster.', 'saltelli') . '</em>';
            echo wp_kses(saltelli_split_h1_words($sl_h1), [
                'span' => ['class' => true, 'data-i' => true],
                'em'   => [],
                'br'   => [],
            ]);
            ?>
        </h1>
        <p class="sl-page__lede sl-hub-hero__lede">
            <?php esc_html_e('Le materie sono ripartite per cluster di destinatario. Selezioniamo i casi dove la nostra esperienza fa la differenza concreta.', 'saltelli'); ?>
        </p>
    </div>
</section>

<section class="sl-hub-grid-section" aria-label="<?php esc_attr_e('Cluster aree di pratica', 'saltelli'); ?>">
    <div class="sl-container">
        <ul class="sl-hub-grid sl-hub-grid--3">
            <?php foreach ($sl_clusters as $cluster) :
                $term = get_term_by('slug', $cluster['slug'], 'tipo-area');
                $count = $term && !is_wp_error($term) ? (int) $term->count : 0;
                $url   = $term && !is_wp_error($term) ? get_term_link($term) : '#';
                ?>
                <li class="sl-hub-card">
                    <a class="sl-hub-card__link" href="<?php echo esc_url($url); ?>">
                        <p class="sl-mono sl-hub-card__num"><?php echo esc_html($cluster['num']); ?></p>
                        <h2 class="sl-hub-card__title"><?php echo esc_html($cluster['title']); ?></h2>
                        <p class="sl-hub-card__desc"><?php echo esc_html($cluster['desc']); ?></p>
                        <p class="sl-mono sl-hub-card__meta">
                            <?php
                            printf(
                                esc_html(_n('%s area', '%s aree', $count, 'saltelli')),
                                esc_html(number_format_i18n($count))
                            );
                            ?>
                        </p>
                        <span class="sl-mono sl-hub-card__cta"><?php esc_html_e('Esplora →', 'saltelli'); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<section class="sl-hub-cta" aria-label="<?php esc_attr_e('CTA Aree di Pratica', 'saltelli'); ?>">
    <div class="sl-container sl-hub-cta__inner">
        <p class="sl-mono"><?php esc_html_e('§ Non trovi la materia?', 'saltelli'); ?></p>
        <h2 class="sl-hub-cta__title">
            <?php esc_html_e('Scrivici una nota: in 24 ore valutiamo se è di nostra competenza.', 'saltelli'); ?>
        </h2>
        <a class="sl-cta sl-cta--primary" href="<?php echo esc_url(home_url('/contatti/')); ?>">
            <?php esc_html_e('Contattaci', 'saltelli'); ?>
        </a>
    </div>
</section>
