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

// Wave 4.7.fix.3: hub strings + 3 cluster cards attaccati a Page WP "Aree di Pratica"
// (page_slug=aree-di-pratica). Edita da WP-Admin → Pagine → Aree di Pratica.
$sl_hub_eyebrow = (string) saltelli_page_field('hub_aree_eyebrow', __('§ Aree di pratica', 'saltelli'));
$sl_hub_h1_main = (string) saltelli_page_field('hub_aree_h1_main', __('Diciassette aree,', 'saltelli'));
$sl_hub_h1_em   = (string) saltelli_page_field('hub_aree_h1_emphasis', __('tre cluster.', 'saltelli'));
$sl_hub_intro   = (string) saltelli_page_field('hub_aree_intro', __('Le materie sono ripartite per cluster di destinatario. Selezioniamo i casi dove la nostra esperienza fa la differenza concreta.', 'saltelli'));

$sl_clusters = [
    [
        'slug'    => 'privati',
        'num'     => '01 / 03',
        'title'   => (string) saltelli_page_field('hub_aree_cluster_privati_label', __('Per i privati', 'saltelli')),
        'desc'    => (string) saltelli_page_field('hub_aree_cluster_privati_desc', __('Famiglie e persone fisiche, lavoratori. Materie: tributario, lavoro, famiglia LGBTQ+, successioni, infortunistica, penale, bancario, condominio, immigrazione.', 'saltelli')),
    ],
    [
        'slug'    => 'imprese',
        'num'     => '02 / 03',
        'title'   => (string) saltelli_page_field('hub_aree_cluster_imprese_label', __('Per le imprese', 'saltelli')),
        'desc'    => (string) saltelli_page_field('hub_aree_cluster_imprese_desc', __('Aziende, freelance, partite IVA. Recupero crediti, domiciliazione d\'impresa, contenzioso commerciale.', 'saltelli')),
    ],
    [
        'slug'    => 'contenzioso-amministrativo',
        'num'     => '03 / 03',
        'title'   => (string) saltelli_page_field('hub_aree_cluster_contenzioso_label', __('Contenzioso amministrativo', 'saltelli')),
        'desc'    => (string) saltelli_page_field('hub_aree_cluster_contenzioso_desc', __('Ricorsi al TAR e al Consiglio di Stato. Atti della pubblica amministrazione, concessioni, procedure di gara.', 'saltelli')),
    ],
];
?>

<section class="sl-page-hero sl-hub-hero" aria-labelledby="hub-aree-h1">
    <div class="sl-container sl-hub-hero__inner">
        <?php saltelli_render_breadcrumb(); ?>
        <p class="sl-mono sl-hub-hero__eyebrow"><?php echo esc_html($sl_hub_eyebrow); ?></p>
        <h1 class="sl-page__title sl-hub-hero__h1" id="hub-aree-h1" data-split-reveal>
            <?php
            $sl_h1 = esc_html($sl_hub_h1_main) . '<br><em>' . esc_html($sl_hub_h1_em) . '</em>';
            echo wp_kses(saltelli_split_h1_words($sl_h1), [
                'span' => ['class' => true, 'data-i' => true],
                'em'   => [],
                'br'   => [],
            ]);
            ?>
        </h1>
        <p class="sl-page__lede sl-hub-hero__lede"><?php echo esc_html($sl_hub_intro); ?></p>
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
                        <span class="sl-mono sl-hub-card__cta"><?php echo esc_html(saltelli_page_field('hub_aree_card_cta', 'Esplora →')); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<?php
/* === Wave-S fix #9 — sezione .sl-hub-cta "Scrivici una nota: in 24 ore..."
   rimossa (feedback Elena: ridondante con CTA "§ Ultima chiamata" della pre-footer
   globale + voce contatti nel menu primary). I 4 SCF field associati
   (hub_aree_cta_eyebrow/title/url/btn_label) restano nel group JSON come orphan —
   da pulire post-cut con migration dedicata. CSS .sl-hub-cta orphan in sections.css. */
?>
