<?php
/**
 * Template: Archive (generic).
 *
 * @package Saltelli
 */
get_header();
?>

<div class="container">

    <header class="archive-header">
        <h1>
            <?php
            if (is_category() || is_tag() || is_tax()) {
                single_term_title();
            } elseif (is_post_type_archive()) {
                post_type_archive_title();
            } elseif (is_author()) {
                the_author();
            } elseif (is_date()) {
                printf(esc_html__('Archivio: %s', 'saltelli'), esc_html(get_the_date('F Y')));
            } else {
                esc_html_e('Archivio', 'saltelli');
            }
            ?>
        </h1>
        <?php
        $desc = is_post_type_archive() ? get_the_post_type_description() : term_description();
        if ($desc) {
            echo '<div class="archive-description">' . wp_kses_post($desc) . '</div>';
        }
        ?>
    </header>

    <?php if (have_posts()) : ?>
        <!-- TODO Style & Animation agent: archive grid -->
        <div class="archive-list">
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class('archive-card'); ?>>
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
