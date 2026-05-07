<?php
/**
 * Template: Single post (blog) — Frame 5 design.
 *
 * @package Saltelli
 */
get_header();

while (have_posts()) :
    the_post();
    $post_id = get_the_ID();
    $cats    = get_the_category();
    $cat     = !empty($cats) ? $cats[0] : null;
    $reading = saltelli_reading_time($post_id);

    // Try to find an "avvocato" matching the author / post meta.
    $author_id     = (int) get_post_field('post_author', $post_id);
    $author_name   = get_the_author_meta('display_name', $author_id);
    $author_email  = get_the_author_meta('user_email', $author_id);
    $linked_avv_id = 0;
    if ($author_name) {
        $matches = get_posts([
            'post_type'   => 'avvocato',
            'numberposts' => 1,
            's'           => $author_name,
        ]);
        if (!empty($matches)) {
            $linked_avv_id = (int) $matches[0]->ID;
        }
    }
    ?>
    <article <?php post_class('sl-post'); ?>>

        <header class="sl-post__hero">
            <div class="sl-container sl-post__hero-inner">
                <?php saltelli_render_breadcrumb(); ?>

                <a class="sl-mono sl-post__back" href="<?php echo esc_url(home_url('/blog/')); ?>">
                    ← <?php esc_html_e('Editoriale', 'saltelli'); ?>
                </a>

                <div class="sl-post__meta">
                    <?php if ($cat) : ?>
                        <a class="sl-mono sl-post__cat" href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"><?php echo esc_html(strtoupper($cat->name)); ?></a>
                        <span class="sl-mono">·</span>
                    <?php endif; ?>
                    <time class="sl-mono" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
                    <span class="sl-mono">·</span>
                    <span class="sl-mono sl-post__author">
                        <?php
                        if ($linked_avv_id) {
                            echo '<a href="' . esc_url(get_permalink($linked_avv_id)) . '">' . esc_html($author_name) . '</a>';
                        } else {
                            echo esc_html($author_name);
                        }
                        ?>
                    </span>
                    <span class="sl-mono">·</span>
                    <span class="sl-mono"><?php
                        printf(
                            /* translators: %d minuti di lettura. */
                            esc_html(_n('%d min', '%d min', $reading, 'saltelli')),
                            (int) $reading
                        );
                    ?></span>
                </div>

                <h1 class="sl-post__title" data-split-reveal><?php echo wp_kses(saltelli_split_h1_words(get_the_title()), ['span' => ['class' => true, 'data-i' => true]]); ?></h1>

                <?php
                $excerpt = get_the_excerpt();
                if ($excerpt) :
                    ?>
                    <p class="sl-post__lede"><?php echo esc_html($excerpt); ?></p>
                <?php endif; ?>

                <?php
                /* Wave 6 Pattern 9 — Author byline ricca (bio_extended + expertise tags). Render solo se l'autore è linkato a un CPT avvocato. */
                if ($linked_avv_id) :
                    $sl_byline_ext  = (string) saltelli_field('byline_extended', $linked_avv_id, '');
                    $sl_expertise   = saltelli_field('expertise_topics', $linked_avv_id, []);
                    if (!is_array($sl_expertise)) $sl_expertise = [];
                    if ($sl_byline_ext !== '' || !empty($sl_expertise)) : ?>
                        <div class="sl-author-byline">
                            <?php if ($sl_byline_ext !== '') : ?>
                                <p class="sl-author-byline__bio"><em><?php echo esc_html($sl_byline_ext); ?></em></p>
                            <?php endif; ?>
                            <?php if (!empty($sl_expertise)) : ?>
                                <ul class="sl-author-expertise">
                                    <?php foreach ($sl_expertise as $sl_exp) :
                                        $sl_exp_id = is_object($sl_exp) ? (int) $sl_exp->ID : (int) $sl_exp;
                                        if (!$sl_exp_id) continue;
                                    ?>
                                        <li><a href="<?php echo esc_url(get_permalink($sl_exp_id)); ?>" class="sl-tag"><?php echo esc_html(get_the_title($sl_exp_id)); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endif;
                endif; ?>
            </div>
        </header>

        <?php if (has_post_thumbnail()) : ?>
            <figure class="sl-post__featured">
                <?php /* Wave 4: blog featured image is above-fold LCP candidate */ ?>
                <?php the_post_thumbnail('saltelli-hero', ['loading' => 'eager', 'decoding' => 'async', 'fetchpriority' => 'high']); ?>
            </figure>
        <?php endif; ?>

        <div class="sl-post__layout">
            <div class="sl-container sl-post__layout-inner">
                <aside class="sl-toc" data-toc aria-label="<?php esc_attr_e('Indice articolo', 'saltelli'); ?>">
                    <div class="sl-mono"><?php esc_html_e('Indice', 'saltelli'); ?></div>
                    <!-- popolato da JS Style Agent: scroll-spy su h2/h3 dentro .sl-post__body -->
                </aside>

                <div class="sl-post__body" data-drop-cap>
                    <?php the_content(); ?>
                </div>
            </div>
        </div>

        <footer class="sl-post__footer">
            <div class="sl-container">
                <?php if ($linked_avv_id) :
                    $ruolo = (string) saltelli_field('ruolo_breve', $linked_avv_id, '');
                    $bio   = (string) saltelli_field('bio_breve', $linked_avv_id, '');
                    ?>
                    <section class="sl-post__author-card">
                        <a class="sl-team__portrait sl-post__author-portrait" href="<?php echo esc_url(get_permalink($linked_avv_id)); ?>">
                            <?php
                            if (has_post_thumbnail($linked_avv_id)) {
                                echo get_the_post_thumbnail($linked_avv_id, 'saltelli-attorney-square', [
                                    'loading'  => 'lazy',
                                    'decoding' => 'async',
                                    'alt'      => esc_attr(get_the_title($linked_avv_id)),
                                ]);
                            } else {
                                echo '<span class="sl-team__placeholder" aria-hidden="true"></span>';
                            }
                            ?>
                        </a>
                        <div class="sl-post__author-text">
                            <div class="sl-mono"><?php esc_html_e("L'autore", 'saltelli'); ?></div>
                            <h2 class="sl-team__name">
                                <a href="<?php echo esc_url(get_permalink($linked_avv_id)); ?>"><?php echo esc_html(get_the_title($linked_avv_id)); ?></a>
                            </h2>
                            <?php if ($ruolo) : ?>
                                <div class="sl-mono sl-team__role"><?php echo esc_html($ruolo); ?></div>
                            <?php endif; ?>
                            <?php if ($bio) : ?>
                                <p><?php echo esc_html($bio); ?></p>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php
                $related = new WP_Query([
                    'post_type'      => 'post',
                    'posts_per_page' => 3,
                    'post__not_in'   => [$post_id],
                    'no_found_rows'  => true,
                    'category__in'   => $cat ? [$cat->term_id] : [],
                ]);
                if ($related->have_posts()) : ?>
                    <section class="sl-post__related" aria-labelledby="related-h">
                        <div class="sl-mono">§ <?php esc_html_e('Editoriale', 'saltelli'); ?></div>
                        <h2 class="sl-section-title" id="related-h"><?php esc_html_e('Continua a leggere', 'saltelli'); ?></h2>
                        <ul class="sl-articles">
                            <?php while ($related->have_posts()) : $related->the_post(); ?>
                                <li class="sl-articles__item">
                                    <a href="<?php the_permalink(); ?>">
                                        <span class="sl-mono"><?php echo esc_html(get_the_date()); ?></span>
                                        <span class="sl-articles__title"><?php the_title(); ?></span>
                                    </a>
                                </li>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </ul>
                    </section>
                <?php endif; ?>

                <section class="sl-post__cta">
                    <h2 class="sl-section-title"><?php esc_html_e('Hai un caso simile?', 'saltelli'); ?></h2>
                    <a class="sl-btn sl-btn--primary" href="<?php echo esc_url(home_url('/contatti/')); ?>">
                        <span><?php esc_html_e('Prenota un primo incontro', 'saltelli'); ?></span>
                        <span class="arrow" aria-hidden="true">→</span>
                    </a>
                </section>
            </div>
        </footer>

    </article>
    <?php
endwhile;

get_footer();
