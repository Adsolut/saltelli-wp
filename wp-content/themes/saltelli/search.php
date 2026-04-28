<?php
/**
 * Template: Search results.
 *
 * @package Saltelli
 */
get_header();
?>

<div class="container">

    <header class="search-header">
        <h1><?php
        printf(
            /* translators: %s: search query */
            esc_html__('Risultati per: %s', 'saltelli'),
            '<span>' . esc_html(get_search_query()) . '</span>'
        );
        ?></h1>
    </header>

    <?php if (have_posts()) : ?>
        <!-- TODO Style & Animation agent: search results layout -->
        <div class="search-results">
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class('search-result'); ?>>
                    <h2>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        <small>(<?php echo esc_html(get_post_type()); ?>)</small>
                    </h2>
                    <?php the_excerpt(); ?>
                </article>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination(); ?>

    <?php else : ?>
        <p><?php esc_html_e('Nessun risultato. Prova una ricerca diversa.', 'saltelli'); ?></p>
        <?php get_search_form(); ?>
    <?php endif; ?>

</div>

<?php
get_footer();
