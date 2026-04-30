<?php
/**
 * Template: Index — fallback generico (loop standard, blog index).
 *
 * @package Saltelli
 */
get_header();
?>

<section class="sl-blog sl-blog--archive">
    <div class="sl-container">

        <header class="sl-section-head">
            <?php saltelli_render_breadcrumb(); ?>

            <div class="sl-mono"><?php esc_html_e('Editoriale', 'saltelli'); ?></div>
            <h1 class="sl-section-title">
                <?php
                if (is_home() && !is_front_page() && get_option('page_for_posts')) {
                    echo esc_html(get_the_title(get_option('page_for_posts')));
                } elseif (is_category()) {
                    single_cat_title();
                } elseif (is_tag()) {
                    single_tag_title();
                } elseif (is_author()) {
                    the_post(); echo esc_html(get_the_author()); rewind_posts();
                } elseif (is_year()) {
                    echo esc_html(get_the_date('Y'));
                } elseif (is_month()) {
                    echo esc_html(get_the_date('F Y'));
                } else {
                    esc_html_e('Articoli recenti', 'saltelli');
                }
                ?>
            </h1>
        </header>

        <?php if (have_posts()) : ?>
            <ul class="sl-blog__list">
                <?php while (have_posts()) : the_post();
                    $cats = get_the_category();
                    $cat  = !empty($cats) ? $cats[0] : null;
                    ?>
                    <li class="sl-blog__row">
                        <a href="<?php the_permalink(); ?>" class="sl-blog__row-inner">
                            <span class="sl-mono sl-blog__date"><?php echo esc_html(get_the_date()); ?></span>
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
            <p class="sl-mono"><?php esc_html_e('Nessun contenuto trovato.', 'saltelli'); ?></p>
        <?php endif; ?>

    </div>
</section>

<?php
get_footer();
