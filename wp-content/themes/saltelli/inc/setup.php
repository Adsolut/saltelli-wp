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

/**
 * Editorial Refinement v0.10.0 (C1) — Map /lo-studio/ legacy slug → /chi-siamo/.
 *
 * Background: il menu "Studio" puntava a /lo-studio/ (URL custom storica).
 * Non esiste page WP con slug `lo-studio`, quindi WP rewrite catturava il
 * primo blog post che inizia con "lo-studio-..." → redirect canonical 301
 * indesiderato. Fix v0.10.0: aggiornato menu_item _menu_item_url a /chi-siamo/
 * (link OK). Questo hook copre bookmark/link diretti / backlinks SEO.
 *
 * Hook su `init` priority 1 (PRIMA di WP_Query parse_request → prima di
 * redirect_canonical). template_redirect è troppo tardi: redirect canonical
 * WP fa il match al post che inizia con "lo-studio-..." prima di noi.
 */
add_action('init', function () {
    if (is_admin() || (defined('WP_CLI') && WP_CLI)) return;
    $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
    $path = (string) parse_url($request_uri, PHP_URL_PATH);
    if ($path === '/lo-studio/' || $path === '/lo-studio') {
        wp_safe_redirect(home_url('/chi-siamo/'), 301);
        exit;
    }
}, 1);

/**
 * v0.24.0 TASK 6 — Inject .sl-input class on CF7 form fields (input/textarea/select).
 *
 * CF7 outputs <input class="wpcf7-form-control wpcf7-text"> (and similar). We piggy-back
 * .sl-input so our editorial underline-only spec applies without overriding CF7 markup.
 * Idempotent: skips elements already carrying sl-input.
 */
add_filter('wpcf7_form_elements', function ($html) {
    if (strpos($html, 'wpcf7-form-control') === false) {
        return $html;
    }
    $html = preg_replace_callback(
        '#<(input|textarea|select)([^>]*?)class="([^"]*?wpcf7-form-control[^"]*?)"#i',
        function ($m) {
            $tag     = $m[1];
            $rest    = $m[2];
            $classes = $m[3];
            if (strpos($classes, 'sl-input') !== false) {
                return $m[0];
            }
            $skip = ['wpcf7-submit', 'wpcf7-acceptance', 'wpcf7-checkbox', 'wpcf7-radio'];
            foreach ($skip as $needle) {
                if (strpos($classes, $needle) !== false) {
                    return $m[0];
                }
            }
            $new = trim($classes . ' sl-input');
            return '<' . $tag . $rest . 'class="' . $new . '"';
        },
        $html
    );
    return $html;
}, 10, 1);
