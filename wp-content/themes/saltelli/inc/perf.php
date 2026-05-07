<?php
/**
 * Wave 4 — Performance optimizations (JS defer, jQuery migrate removal,
 * emoji + low-value head cleanup).
 *
 * Companion to inc/critical-css.php (CSS deferral) and inc/enqueue.php
 * (font preload + GSAP SRI). All filters guard `is_admin()` so wp-admin
 * keeps full default behaviour.
 *
 * @package Saltelli
 * @since 1.3.0 Wave 4
 */
defined('ABSPATH') || exit;

/* ------------------------------------------------------------------ */
/* 1. Defer jQuery + jquery-migrate + honeypot wpa.js                  */
/* ------------------------------------------------------------------ */
/* Pre-Wave 4 the WP-core jQuery + jquery-migrate were render-blocking
 * in <head> (~108 KB unused JS per Lighthouse). Honeypot's wpa.js is
 * also enqueued in <head> via plugin default. All three are deferred so
 * they execute at DOMContentLoaded (after parsing) instead of blocking
 * first paint.
 *
 * Safety:
 * - No theme code uses jQuery (verified: zero $(...) or jQuery(...) in
 *   wp-content/themes/saltelli/).
 * - The wpascript-js-after inline localize block only declares a global
 *   `wpa_field_info` object — no jQuery calls.
 * - Defer preserves DOM order, so jquery → jquery-migrate → wpa.js still
 *   execute in correct dependency order.
 */
add_filter('script_loader_tag', 'saltelli_defer_legacy_scripts', 11, 2);
function saltelli_defer_legacy_scripts($tag, $handle) {
    if (is_admin()) {
        return $tag;
    }
    static $defer_handles = [
        'jquery',
        'jquery-core',
        'jquery-migrate',
        'wpascript', // honeypot plugin
    ];
    if (!in_array($handle, $defer_handles, true)) {
        return $tag;
    }
    if (strpos($tag, ' defer') !== false) {
        return $tag;
    }
    return str_replace(' src=', ' defer src=', $tag);
}

/* ------------------------------------------------------------------ */
/* 2. Remove jquery-migrate dependency from jquery                     */
/* ------------------------------------------------------------------ */
/* jquery-migrate (~10 KB) only matters for very old plugin code that
 * still uses removed jQuery 1.x APIs. Saltelli theme + active plugins
 * (ACF, Redirection, honeypot, Yoast) are all jQuery 3.x clean. */
add_action('wp_default_scripts', 'saltelli_remove_jquery_migrate');
function saltelli_remove_jquery_migrate(&$scripts) {
    if (is_admin()) {
        return;
    }
    if (!isset($scripts->registered['jquery'])) {
        return;
    }
    $jquery = $scripts->registered['jquery'];
    if (!empty($jquery->deps)) {
        $jquery->deps = array_values(array_diff($jquery->deps, ['jquery-migrate']));
    }
}

/* ------------------------------------------------------------------ */
/* 3. Remove WP emoji detection + styles                               */
/* ------------------------------------------------------------------ */
/* wp-emoji-release.min.js (~14 KB), wp-emoji-settings inline JSON, and
 * the inline emoji <style> block are all unused: site copy is Italian,
 * emojis are not part of the brand. */
add_action('init', 'saltelli_disable_emoji');
function saltelli_disable_emoji() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    // Remove emoji from TinyMCE plugin list (defensive, ACF/Yoast safe)
    add_filter('tiny_mce_plugins', function ($plugins) {
        return is_array($plugins) ? array_values(array_diff($plugins, ['wpemoji'])) : [];
    });

    // Stop browsers from looking up s.w.org for emoji svg
    add_filter('emoji_svg_url', '__return_false');
}

/* ------------------------------------------------------------------ */
/* 4. Disable XML-RPC (security + perf)                                */
/* ------------------------------------------------------------------ */
add_filter('xmlrpc_enabled', '__return_false');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

/* ------------------------------------------------------------------ */
/* 5. Remove WP generator meta + RSS generator                         */
/* ------------------------------------------------------------------ */
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');
