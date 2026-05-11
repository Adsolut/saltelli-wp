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
?>

<section class="sl-blog sl-blog--archive sl-blog--casi">
    <div class="sl-container">

        <header class="sl-section-head sl-page-hero sl-page-hero--compact">
            <?php if (function_exists('saltelli_render_breadcrumb')) saltelli_render_breadcrumb(); ?>
            <div class="sl-mono"><?php echo esc_html($sl_arch_eyebrow); ?></div>
            <h1 class="sl-section-title sl-team__archive-h1" data-split-reveal>
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
            <p class="sl-blog__archive-lede"><?php echo esc_html($sl_arch_intro); ?></p>
        </header>

        <?php if (have_posts()) : ?>
            <ul class="sl-blog__list">
                <?php while (have_posts()) : the_post();
                    $caso_terms = get_the_terms(get_the_ID(), 'caso_categoria');
                    $cat = ($caso_terms && !is_wp_error($caso_terms)) ? $caso_terms[0] : null;
                    $sl_data_caso = function_exists('get_field') ? get_field('data_caso') : '';
                    if (!empty($sl_data_caso)) {
                        $sl_caso_date_display = wp_date('Y', strtotime($sl_data_caso));
                    } elseif (preg_match('/·\s*(\d{4})/u', get_the_title(), $sl_caso_y_match)) {
                        $sl_caso_date_display = $sl_caso_y_match[1];
                    } else {
                        $sl_caso_date_display = get_the_date('Y');
                    }
                    ?>
                    <li class="sl-blog__row">
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
