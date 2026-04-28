<?php
/**
 * Template: Single post (blog).
 *
 * @package Saltelli
 */
get_header();

while (have_posts()) :
    the_post();
    ?>
    <article <?php post_class('post post--single'); ?>>

        <header class="post-header container">
            <h1><?php the_title(); ?></h1>
            <p class="post-meta">
                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                — <?php the_author(); ?>
                <?php
                $cats = get_the_category();
                if (!empty($cats)) {
                    echo ' — ' . esc_html($cats[0]->name);
                }
                ?>
            </p>
            <!-- TODO Style & Animation agent: featured image hero (se presente) -->
        </header>

        <div class="post-content container">
            <?php the_content(); ?>
        </div>

        <footer class="post-footer container">
            <!-- TODO Style & Animation agent: tag, sharing, autore card, articoli correlati -->
        </footer>

    </article>
    <?php
endwhile;

get_footer();
