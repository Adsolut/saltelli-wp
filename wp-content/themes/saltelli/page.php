<?php
/**
 * Template: Page (standard).
 *
 * @package Saltelli
 */
get_header();

while (have_posts()) :
    the_post();
    ?>
    <article <?php post_class('page'); ?>>

        <header class="page-header container">
            <h1><?php the_title(); ?></h1>
            <!-- TODO Style & Animation agent: hero pagina + breadcrumb visivo -->
        </header>

        <div class="page-content container">
            <?php the_content(); ?>
        </div>

    </article>
    <?php
endwhile;

get_footer();
