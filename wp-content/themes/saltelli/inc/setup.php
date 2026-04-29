<?php
/**
 * Theme setup — supports, image sizes, menus, text domain.
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

add_action('after_setup_theme', 'saltelli_setup');
function saltelli_setup() {

    // Translations
    load_theme_textdomain('saltelli', SALTELLI_THEME_DIR . '/languages');

    // Core supports
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    /*
     * NO custom-logo support — il tema usa un wordmark testuale "Saltelli & Partners"
     * dentro <a class="sl-header__brand"> (vedi header.php).
     * add_theme_support('custom-logo', [...]) deliberatamente assente.
     */
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('align-wide');

    // Image sizes — used dappertutto nei template
    add_image_size('saltelli-attorney-portrait', 600, 800, true); // 3:4 ritratto
    add_image_size('saltelli-attorney-square',   600, 600, true); // 1:1
    add_image_size('saltelli-hero',              1920, 1080, true);
    add_image_size('saltelli-card',              800, 500, true);

    // Nav menus
    register_nav_menus([
        'primary'       => __('Menu principale', 'saltelli'),
        'footer-studio' => __('Footer — Lo Studio', 'saltelli'),
        'footer-aree'   => __('Footer — Aree di pratica', 'saltelli'),
        'footer-legal'  => __('Footer — Legali', 'saltelli'),
    ]);
}

/**
 * Allow SVG sized in admin (logo upload).
 */
add_filter('upload_mimes', function ($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});
