<?php
/**
 * SEO meta tags — description, Open Graph, Twitter Cards.
 *
 * Skip se Yoast / Rank Math sono attivi: rispettiamo il loro output
 * e non duplichiamo i tag (controllo via funzioni canoniche dei plugin).
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

/**
 * Detect: SEO plugin che già gestisce meta?
 */
function saltelli_seo_plugin_active() {
    if (defined('WPSEO_VERSION') || function_exists('YoastSEO')) {
        return 'yoast';
    }
    if (defined('RANK_MATH_VERSION') || class_exists('RankMath')) {
        return 'rankmath';
    }
    if (defined('AIOSEO_VERSION')) {
        return 'aioseo';
    }
    return false;
}

add_action('wp_head', 'saltelli_emit_meta_tags', 4);
function saltelli_emit_meta_tags() {

    if (saltelli_seo_plugin_active()) {
        // Un plugin SEO già emette description + OG + Twitter.
        return;
    }

    $title = wp_get_document_title();
    $url   = saltelli_canonical_url();
    $site  = get_bloginfo('name');
    $locale = get_locale();

    // Description.
    $desc = '';
    if (is_singular()) {
        $post_id = get_the_ID();
        $desc = (string) saltelli_field('answer_capsule', $post_id, '');
        if ($desc === '') {
            $desc = (string) saltelli_field('bio_breve', $post_id, '');
        }
        if ($desc === '') {
            $desc = wp_strip_all_tags(get_the_excerpt($post_id));
        }
    }
    if ($desc === '') {
        $desc = (string) get_bloginfo('description');
    }
    $desc = trim(mb_substr($desc, 0, 200));

    // OG image: featured image > custom_logo > placeholder.
    $og_image = '';
    if (is_singular() && has_post_thumbnail()) {
        $og_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
    } else {
        $logo_id = get_theme_mod('custom_logo');
        if ($logo_id) {
            $og_image = wp_get_attachment_image_url($logo_id, 'full');
        }
    }

    $type = is_singular('post') ? 'article' : (is_front_page() ? 'website' : 'website');

    echo "\n<!-- Saltelli — meta tags -->\n";
    if ($desc !== '') {
        printf('<meta name="description" content="%s">' . "\n", esc_attr($desc));
    }
    printf('<link rel="canonical" href="%s">' . "\n", esc_url($url));

    // Open Graph.
    printf('<meta property="og:locale" content="%s">' . "\n", esc_attr($locale));
    printf('<meta property="og:type" content="%s">' . "\n", esc_attr($type));
    printf('<meta property="og:title" content="%s">' . "\n", esc_attr($title));
    if ($desc !== '') {
        printf('<meta property="og:description" content="%s">' . "\n", esc_attr($desc));
    }
    printf('<meta property="og:url" content="%s">' . "\n", esc_url($url));
    printf('<meta property="og:site_name" content="%s">' . "\n", esc_attr($site));
    if ($og_image) {
        printf('<meta property="og:image" content="%s">' . "\n", esc_url($og_image));
    }

    // Twitter Cards.
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    printf('<meta name="twitter:title" content="%s">' . "\n", esc_attr($title));
    if ($desc !== '') {
        printf('<meta name="twitter:description" content="%s">' . "\n", esc_attr($desc));
    }
    if ($og_image) {
        printf('<meta name="twitter:image" content="%s">' . "\n", esc_url($og_image));
    }
}
