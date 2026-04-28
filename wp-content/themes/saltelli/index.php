<?php
/**
 * Template: Index — fallback generico (loop standard).
 *
 * @package Saltelli
 */
get_header();
?>

<div class="container">

    <?php if (have_posts()) : ?>

        <header class="archive-header">
            <h1><?php
                if (is_home() && !is_front_page()) {
                    single_post_title();
                } else {
                    esc_html_e('Articoli recenti', 'saltelli');
                }
            ?></h1>
        </header>

        <!-- TODO Style & Animation agent: archive layout (card list / mosaic) -->
        <div class="post-list">
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class('post-card'); ?>>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php the_excerpt(); ?>
                </article>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination(); ?>

    <?php else : ?>

        <p><?php esc_html_e('Nessun contenuto trovato.', 'saltelli'); ?></p>

    <?php endif; ?>

</div>

<?php
get_footer();
