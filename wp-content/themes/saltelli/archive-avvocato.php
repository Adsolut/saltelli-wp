<?php
/**
 * Template: Archive CPT avvocato.
 *
 * @package Saltelli
 */
get_header();
?>

<div class="container">

    <header class="archive-header">
        <h1><?php esc_html_e('Avvocati', 'saltelli'); ?></h1>
        <!-- TODO: copy intro team da Elena -->
    </header>

    <?php if (have_posts()) : ?>
        <!-- TODO Style & Animation agent: grid 4 ritratti avvocati con hover reveal ruolo/specializzazioni -->
        <div class="avvocati-grid">
            <?php while (have_posts()) : the_post();
                $ruolo = saltelli_field('ruolo_breve', get_the_ID(), '');
                ?>
                <article <?php post_class('avvocato-card'); ?>>
                    <a href="<?php the_permalink(); ?>" class="avvocato-card__link">
                        <?php
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('saltelli-attorney-portrait', ['loading' => 'lazy', 'alt' => esc_attr(get_the_title())]);
                        } else {
                            echo '<!-- TODO: foto Saltelli -->';
                        }
                        ?>
                        <h2><?php the_title(); ?></h2>
                        <?php if ($ruolo) : ?>
                            <p class="avvocato-card__ruolo"><?php echo esc_html($ruolo); ?></p>
                        <?php endif; ?>
                    </a>
                </article>
            <?php endwhile; ?>
        </div>

    <?php else : ?>
        <p><?php esc_html_e('Nessun avvocato pubblicato.', 'saltelli'); ?></p>
    <?php endif; ?>

</div>

<?php
get_footer();
