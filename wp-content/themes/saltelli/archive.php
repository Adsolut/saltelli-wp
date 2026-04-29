<?php
/**
 * Template: Archive (generic — usato per category, tag, tax, date, author).
 * Per CPT specifici: archive-avvocato.php / archive-competenza.php (override).
 *
 * @package Saltelli
 */
get_header();
?>

<section class="sl-blog sl-blog--archive">
    <div class="sl-container">

        <header class="sl-section-head">
            <div class="sl-mono">
                <?php
                if (is_category()) esc_html_e('Editoriale · Categoria', 'saltelli');
                elseif (is_tag())  esc_html_e('Editoriale · Tag', 'saltelli');
                elseif (is_author()) esc_html_e('Editoriale · Autore', 'saltelli');
                elseif (is_date()) esc_html_e('Editoriale · Archivio', 'saltelli');
                else esc_html_e('Archivio', 'saltelli');
                ?>
            </div>
            <h1 class="sl-section-title">
                <?php
                if (is_category() || is_tag() || is_tax()) {
                    single_term_title();
                } elseif (is_post_type_archive()) {
                    post_type_archive_title();
                } elseif (is_author()) {
                    echo esc_html(get_the_author());
                } elseif (is_date()) {
                    echo esc_html(get_the_date('F Y'));
                } else {
                    esc_html_e('Archivio', 'saltelli');
                }
                ?>
            </h1>
            <?php
            $desc = is_post_type_archive() ? get_the_post_type_description() : term_description();
            if ($desc) {
                echo '<div class="sl-blog__archive-lede">' . wp_kses_post($desc) . '</div>';
            }
            ?>
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
