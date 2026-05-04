<?php
/**
 * Template part: page-casi.php
 *
 * Render della page /casi/. Hero+lede da ACF (Wave 2), casi list ora
 * querata dal CPT saltelli_caso invece di saltelli_cases_full() hardcoded.
 *
 * @package Saltelli
 * @since 1.0.0 Wave 3
 */
defined('ABSPATH') || exit;

$pid = get_the_ID();

// Hero — ACF (fallback hardcoded pre-Wave 2 wording).
$hero_eyebrow = saltelli_field('hero_eyebrow', $pid, '§ Risultati · Casi rappresentativi');
$hero_h1_pre  = saltelli_field('hero_h1_pre', $pid, 'Casi');
$hero_h1_em   = saltelli_field('hero_h1_em', $pid, 'rappresentativi.');
$hero_lede    = saltelli_field('hero_lede', $pid, 'Una selezione di vittorie. Identificativi anonimizzati per riservatezza, documentati e verificabili in studio.');
$intro_body   = saltelli_field('intro_body', $pid, '');

// CTA finale — ACF (fallback hardcoded).
$cta_eyebrow = saltelli_field('cta_eyebrow', $pid, '§ Prossimo caso');
$cta_h2      = saltelli_field('cta_h2', $pid, 'Vorresti vincere il tuo?');
$cta_p       = saltelli_field('cta_p', $pid, "Il primo incontro è gratuito. Diciamo la verità anche quando significa sconsigliare un'azione legale.");
$cta_label   = saltelli_field('cta_label', $pid, 'Prenota gratuita →');
$cta_url     = saltelli_field('cta_url', $pid, '/contatti/');

// Casi list — query CPT saltelli_caso (Wave 2 popolato 10 items).
// Fallback: saltelli_cases_full() / saltelli_homepage_cases() helpers se CPT vuoto.
$sl_casi_posts = get_posts([
    'post_type'   => 'saltelli_caso',
    'numberposts' => -1,
    'post_status' => 'publish',
    'orderby'     => 'menu_order',
    'order'       => 'ASC',
]);

$sl_casi_all = [];
foreach ($sl_casi_posts as $cp) {
    $cat_terms = get_the_terms($cp->ID, 'caso_categoria');
    $cat_name  = ($cat_terms && !is_wp_error($cat_terms)) ? $cat_terms[0]->name : 'Altri';
    // Normalizza categoria al set conosciuto (Privati/Imprese/Contenzioso/Altri).
    $known_cats = ['Privati', 'Imprese', 'Contenzioso', 'Altri'];
    if (!in_array($cat_name, $known_cats, true)) {
        $cat_name = 'Altri';
    }
    $outcome_full = (string) saltelli_field('outcome_label', $cp->ID, '');
    // outcome_label format: "VALORE · LABEL" (es. "€240.000 · Annullamento")
    $outcome_parts = array_map('trim', explode('·', $outcome_full, 2));
    $outcome_val   = $outcome_parts[0] ?? '';
    $outcome_lbl   = $outcome_parts[1] ?? '';
    $featured      = ($cp->post_excerpt === 'featured');

    $sl_casi_all[] = [
        'id'       => (string) saltelli_field('id_label', $cp->ID, get_the_title($cp)),
        'cat'      => $cat_name,
        'outcome'  => $outcome_val,
        'lbl'      => $outcome_lbl,
        'desc'     => (string) saltelli_field('descrizione', $cp->ID, ''),
        'featured' => $featured,
    ];
}

// Fallback: helpers se CPT vuoto.
if (empty($sl_casi_all) && function_exists('saltelli_cases_full')) {
    $sl_casi_all = saltelli_cases_full();
} elseif (empty($sl_casi_all) && function_exists('saltelli_homepage_cases')) {
    $sl_casi_all = saltelli_homepage_cases();
}

$sl_casi_count    = count($sl_casi_all);
$sl_casi_filters  = ['Tutti', 'Privati', 'Imprese', 'Contenzioso', 'Altri'];
$sl_casi_counts   = ['Tutti' => $sl_casi_count, 'Privati' => 0, 'Imprese' => 0, 'Contenzioso' => 0, 'Altri' => 0];
$sl_casi_featured = null;
foreach ($sl_casi_all as &$sl_c) {
    $sl_cat_c = isset($sl_c['cat']) ? (string) $sl_c['cat'] : 'Altri';
    if (!isset($sl_casi_counts[$sl_cat_c])) {
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
            <div class="sl-mono sl-casi__eyebrow"><?php echo esc_html($hero_eyebrow); ?></div>
            <h1 class="sl-casi__h1" id="casi-h1" data-split-reveal>
                <?php
                $sl_casi_title = esc_html($hero_h1_pre) . '<br><em>' . esc_html($hero_h1_em) . '</em>';
                echo wp_kses(saltelli_split_h1_words($sl_casi_title), [
                    'span' => ['class' => true, 'data-i' => true],
                    'em'   => [],
                    'br'   => [],
                ]);
                ?>
            </h1>
        </div>
        <div class="sl-casi__hero-right">
            <p class="sl-casi__hero-lede"><?php echo esc_html($hero_lede); ?></p>
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
        <div class="sl-mono sl-casi__cta-tag"><?php echo esc_html($cta_eyebrow); ?></div>
        <div class="sl-casi__cta-body">
            <h2 class="sl-casi__cta-title" id="casi-cta-h"><?php echo esc_html($cta_h2); ?></h2>
            <?php if ($cta_p) : ?>
                <p class="sl-casi__cta-lede"><?php echo esc_html($cta_p); ?></p>
            <?php endif; ?>
            <a class="sl-btn sl-btn--primary" href="<?php echo esc_url($cta_url); ?>">
                <span><?php echo esc_html(rtrim($cta_label, ' →')); ?></span>
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
