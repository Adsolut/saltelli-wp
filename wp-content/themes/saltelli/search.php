<?php
/**
 * Template: Search results.
 *
 * @package Saltelli
 */
get_header();
?>

<section class="sl-search">
    <div class="sl-container">

        <header class="sl-section-head">
            <div class="sl-mono"><?php esc_html_e('Ricerca', 'saltelli'); ?></div>
            <h1 class="sl-section-title">
                <?php
                printf(
                    /* translators: %s: search query */
                    esc_html__('Risultati per %s', 'saltelli'),
                    '<em>' . esc_html(get_search_query()) . '</em>'
                );
                ?>
            </h1>
        </header>

        <?php get_search_form(); ?>

        <?php if (have_posts()) : ?>
            <ul class="sl-blog__list">
                <?php while (have_posts()) : the_post(); ?>
                    <li class="sl-blog__row">
                        <a href="<?php the_permalink(); ?>" class="sl-blog__row-inner">
                            <span class="sl-mono sl-blog__date"><?php echo esc_html(get_post_type()); ?></span>
                            <h2 class="sl-blog__title"><?php the_title(); ?></h2>
                            <p class="sl-blog__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 28, '…')); ?></p>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>

            <nav class="sl-blog__pagination" aria-label="<?php esc_attr_e('Paginazione', 'saltelli'); ?>">
                <?php the_posts_pagination([
                    'mid_size'  => 1,
                    'prev_text' => '← ' . __('Precedenti', 'saltelli'),
                    'next_text' => __('Successivi', 'saltelli') . ' →',
                ]); ?>
            </nav>

        <?php else : ?>
            <p class="sl-mono"><?php esc_html_e('Nessun risultato. Prova una ricerca diversa.', 'saltelli'); ?></p>
        <?php endif; ?>

    </div>
</section>

<?php
get_footer();
