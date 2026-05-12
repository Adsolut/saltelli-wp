<?php
/**
 * Template: Archive CPT saltelli_caso (/chi-siamo/casi-rappresentativi/).
 *
 * Wave 4.7.fix.2 P4 — extracted from archive.php fallback to enable SCF
 * editorial control (Theme Options tab "Archive Headers"). Render layout
 * identico a archive.php (sl-blog__list) ma con header editoriale split
 * eyebrow / H1 main + emphasis / intro lede.
 *
 * @package Saltelli
 */
get_header();

// Editorial copy via SCF (defaults Wave 4.7.fix.2 baseline).
$sl_arch_eyebrow = (string) saltelli_option('archive_caso_eyebrow', __('§ Studio · Casi rappresentativi', 'saltelli'));
$sl_arch_h1_main = (string) saltelli_option('archive_caso_h1_main', __('Casi', 'saltelli'));
$sl_arch_h1_em   = (string) saltelli_option('archive_caso_h1_emphasis', __('rappresentativi.', 'saltelli'));
$sl_arch_intro   = (string) saltelli_option('archive_caso_intro', __('Una selezione anonimizzata di pratiche dello Studio. Storie con un dato comune: la complessità non risolta da chi è venuto prima.', 'saltelli'));

// Wave Elena FB Batch 2 #13 — trust capsule (uniforma a archive-avvocato pattern).
// TODO Wave 5.1: spostare archive_caso_trust_* in SCF additive (Theme Options tab "Archive Headers").
$sl_casi_count           = (int) (wp_count_posts('saltelli_caso')->publish ?? 0);
$sl_arch_trust_eyebrow   = __('§ Anonimizzati', 'saltelli');
$sl_arch_trust_headline  = $sl_casi_count > 0
    ? sprintf(__('%d casi anonimizzati.', 'saltelli'), $sl_casi_count)
    : __('Casi anonimizzati.', 'saltelli');
$sl_arch_trust_headline_em = __('Dal 2008.', 'saltelli');
$sl_arch_trust_text      = __('Storie reali, dati protetti. Outcome verificabili. Pattern ricorrenti che attraversano materie e tribunali.', 'saltelli');

// Pull-quote "caso simbolo" — ADDITIVE Wave P9 Design Handoff. Default vuoti:
// se i 4 campi sono tutti vuoti la sezione non viene renderizzata.
$sl_caso_simbolo_eyebrow = (string) saltelli_option('archive_caso_simbolo_eyebrow', '');
$sl_caso_simbolo_number  = (string) saltelli_option('archive_caso_simbolo_number', '');
$sl_caso_simbolo_quote   = (string) saltelli_option('archive_caso_simbolo_quote', '');
$sl_caso_simbolo_attr    = (string) saltelli_option('archive_caso_simbolo_attr', '');
$sl_caso_simbolo_show    = ($sl_caso_simbolo_eyebrow !== '' || $sl_caso_simbolo_number !== '' || $sl_caso_simbolo_quote !== '' || $sl_caso_simbolo_attr !== '');
?>

<section class="sl-blog sl-blog--archive sl-blog--casi sl-archive-casi">
    <div class="sl-container">

        <header class="sl-archive-casi__hero sl-page-hero sl-page-hero--compact">
            <div>
                <?php if (function_exists('saltelli_render_breadcrumb')) saltelli_render_breadcrumb(); ?>
                <div class="sl-mono sl-archive-casi__eyebrow" style="margin-bottom: 32px;">
                    <?php echo esc_html($sl_arch_eyebrow); ?>
                </div>
                <h1 class="sl-archive-casi__h1" data-split-reveal>
                    <?php
                    $sl_h1 = esc_html($sl_arch_h1_main) . '<br><em>' . esc_html($sl_arch_h1_em) . '</em>';
                    if (function_exists('saltelli_split_h1_words')) {
                        echo wp_kses(saltelli_split_h1_words($sl_h1), [
                            'span' => ['class' => true, 'data-i' => true],
                            'em'   => [],
                            'br'   => [],
                        ]);
                    } else {
                        echo wp_kses($sl_h1, ['em' => [], 'br' => []]);
                    }
                    ?>
                </h1>
                <p class="sl-archive-casi__lede"><?php echo esc_html($sl_arch_intro); ?></p>
            </div>
            <aside class="sl-archive-casi__trust" role="complementary" aria-label="<?php esc_attr_e('Studio · casi', 'saltelli'); ?>">
                <div class="sl-mono sl-archive-casi__trust-eyebrow">
                    <?php echo esc_html($sl_arch_trust_eyebrow); ?>
                </div>
                <p class="sl-archive-casi__trust-headline">
                    <?php echo esc_html($sl_arch_trust_headline); ?><br>
                    <em><?php echo esc_html($sl_arch_trust_headline_em); ?></em>
                </p>
                <p class="sl-archive-casi__trust-text">
                    <?php echo esc_html($sl_arch_trust_text); ?>
                </p>
            </aside>
        </header>

        <?php /* === design-handoff archive-casi P9 — pull-quote "caso simbolo" (conditional) === */ ?>
        <?php if ($sl_caso_simbolo_show) : ?>
            <div class="sl-casi__pull-frame" role="region" aria-label="<?php esc_attr_e('Caso simbolo', 'saltelli'); ?>">
                <div>
                    <?php if ($sl_caso_simbolo_eyebrow !== '') : ?>
                        <div class="sl-mono sl-casi__pull-eyebrow"><?php echo esc_html($sl_caso_simbolo_eyebrow); ?></div>
                    <?php endif; ?>
                    <?php if ($sl_caso_simbolo_number !== '') : ?>
                        <div class="sl-casi__pull-figure"><?php echo esc_html($sl_caso_simbolo_number); ?></div>
                    <?php endif; ?>
                </div>
                <?php if ($sl_caso_simbolo_quote !== '') : ?>
                    <blockquote class="sl-casi__pull-quote">
                        <p>&ldquo;<?php echo esc_html($sl_caso_simbolo_quote); ?>&rdquo;</p>
                        <?php if ($sl_caso_simbolo_attr !== '') : ?>
                            <cite class="sl-mono sl-casi__pull-cite"><?php echo esc_html($sl_caso_simbolo_attr); ?></cite>
                        <?php endif; ?>
                    </blockquote>
                <?php elseif ($sl_caso_simbolo_attr !== '') : ?>
                    <p class="sl-mono sl-casi__pull-cite"><?php echo esc_html($sl_caso_simbolo_attr); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php /* === design-handoff archive-casi P9 — filtri caso_categoria (5 tab hardcode, JS vanilla client-side) === */ ?>
        <?php if (have_posts()) : ?>
            <nav class="sl-casi__filter-bar" aria-label="<?php esc_attr_e('Filtra casi per categoria', 'saltelli'); ?>">
                <button type="button" class="sl-casi__filter-btn is-active" data-filter="all"><?php esc_html_e('Tutti', 'saltelli'); ?></button>
                <button type="button" class="sl-casi__filter-btn" data-filter="privati"><?php esc_html_e('Privati', 'saltelli'); ?></button>
                <button type="button" class="sl-casi__filter-btn" data-filter="imprese"><?php esc_html_e('Imprese', 'saltelli'); ?></button>
                <button type="button" class="sl-casi__filter-btn" data-filter="contenzioso"><?php esc_html_e('Contenzioso', 'saltelli'); ?></button>
                <button type="button" class="sl-casi__filter-btn" data-filter="altri"><?php esc_html_e('Altri', 'saltelli'); ?></button>
            </nav>
        <?php endif; ?>

        <?php if (have_posts()) : ?>
            <ul class="sl-blog__list">
                <?php while (have_posts()) : the_post();
                    $caso_terms = get_the_terms(get_the_ID(), 'caso_categoria');
                    $cat = ($caso_terms && !is_wp_error($caso_terms)) ? $caso_terms[0] : null;
                    $sl_caso_cat_slug = $cat ? sanitize_html_class($cat->slug) : 'altri';
                    $sl_data_caso = function_exists('get_field') ? get_field('data_caso') : '';
                    if (!empty($sl_data_caso)) {
                        $sl_caso_date_display = wp_date('Y', strtotime($sl_data_caso));
                    } elseif (preg_match('/·\s*(\d{4})/u', get_the_title(), $sl_caso_y_match)) {
                        $sl_caso_date_display = $sl_caso_y_match[1];
                    } else {
                        $sl_caso_date_display = get_the_date('Y');
                    }
                    ?>
                    <li class="sl-blog__row" data-category="<?php echo esc_attr($sl_caso_cat_slug); ?>">
                        <a href="<?php the_permalink(); ?>" class="sl-blog__row-inner">
                            <span class="sl-mono sl-blog__date"><?php echo esc_html($sl_caso_date_display); ?></span>
                            <?php if ($cat) : ?>
                                <span class="sl-mono sl-blog__cat"><?php echo esc_html(strtoupper($cat->name)); ?></span>
                            <?php endif; ?>
                            <h2 class="sl-blog__title"><?php the_title(); ?></h2>
                            <p class="sl-blog__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 28, '…')); ?></p>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>

            <nav class="sl-blog__pagination" aria-label="<?php esc_attr_e('Paginazione', 'saltelli'); ?>">
                <?php the_posts_pagination([
                    'mid_size'  => 1,
                    'prev_text' => '← ' . __('Più recenti', 'saltelli'),
                    'next_text' => __('Meno recenti', 'saltelli') . ' →',
                ]); ?>
            </nav>

        <?php else : ?>
            <p class="sl-mono"><?php echo esc_html(saltelli_option('archive_caso_empty_text', 'Nessun caso pubblicato.')); ?></p>
        <?php endif; ?>

    </div>
</section>

<?php
get_footer();
