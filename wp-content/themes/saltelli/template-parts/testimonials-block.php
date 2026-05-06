<?php
/**
 * Template part: Testimonials block (Pattern 6)
 * Wave 6 — adapted from .sl-mono + Playfair italic + em-dash attribution
 *
 * Loop su CPT saltelli_trust con meta_query testimonial_type='testimonianza'.
 * Limite 3 (no carousel, solo grid statico DEC-019).
 *
 * Trade-off DEC-019:
 *  - NO foto cliente (privacy + brand editoriale)
 *  - NO rating star (anti-stock)
 *  - NO carousel JS (solo grid)
 *
 * Args:
 *   $args['limit']  int  Numero testimonials da mostrare (default 3)
 *   $args['title']  string  H2 override
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

$args  = isset($args) && is_array($args) ? $args : [];
$limit = !empty($args['limit']) ? (int) $args['limit'] : 3;
$title = !empty($args['title']) ? (string) $args['title'] : __('Storie di chi ci ha scelto', 'saltelli');

$query = new WP_Query([
    'post_type'      => 'saltelli_trust',
    'posts_per_page' => $limit,
    'no_found_rows'  => true,
    'meta_query'     => [
        [
            'key'     => 'testimonial_type',
            'value'   => 'testimonianza',
            'compare' => '=',
        ],
    ],
]);

if (!$query->have_posts()) {
    wp_reset_postdata();
    return;
}
?>
<section class="sl-testimonials-section" aria-labelledby="sl-testimonials-h">
    <p class="sl-mono"><?php esc_html_e('§ Voci dei clienti', 'saltelli'); ?></p>
    <h2 class="sl-testimonials__h" id="sl-testimonials-h"><?php echo esc_html($title); ?></h2>

    <div class="sl-testimonials">
        <?php while ($query->have_posts()) : $query->the_post();
            $tid     = get_the_ID();
            $text    = (string) saltelli_field('testimonial_text', $tid);
            $author  = (string) saltelli_field('testimonial_author', $tid);
            $city    = (string) saltelli_field('testimonial_city', $tid, 'Napoli');
            $topic   = (string) saltelli_field('testimonial_topic', $tid);
            if (empty($text) || empty($author)) {
                continue;
            }
            $attribution_parts = array_filter([$author, $city]);
            $attribution = implode(' · ', $attribution_parts);
        ?>
            <article class="sl-testimonial">
                <?php if (!empty($topic)) : ?>
                    <div class="sl-testimonial__topic"><?php echo esc_html($topic); ?></div>
                <?php endif; ?>
                <blockquote class="sl-testimonial__quote">
                    <?php echo esc_html($text); ?>
                </blockquote>
                <p class="sl-testimonial__attribution"><?php echo esc_html($attribution); ?></p>
            </article>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>
