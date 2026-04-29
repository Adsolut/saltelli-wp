<?php
/**
 * Asset enqueue — CSS, JS, fonts.
 *
 * Style & Animation agent (SHIP MODE 24H, 2026-04-29):
 *  - CSS chain: tokens → base → components → sections
 *  - GSAP 3.12.5 + ScrollTrigger + SplitText + Lenis 1.1.13 da CDN, defer in footer
 *  - main.js dipende da gsap-core e lenis
 *  - SRI hash: TODO (hardening fase post-demo)
 *  - WOFF2 self-hosted: TODO Duccio (fallback Google Fonts via @import in base.css)
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', 'saltelli_enqueue_assets');
function saltelli_enqueue_assets() {

    $ver = SALTELLI_THEME_VERSION;

    // ------------------------------------------------------------------
    // CSS chain — tokens → base → components → sections
    // ------------------------------------------------------------------

    wp_enqueue_style(
        'saltelli-tokens',
        SALTELLI_THEME_URI . '/assets/css/tokens.css',
        [],
        $ver
    );

    wp_enqueue_style(
        'saltelli-base',
        SALTELLI_THEME_URI . '/assets/css/base.css',
        ['saltelli-tokens'],
        $ver
    );

    wp_enqueue_style(
        'saltelli-components',
        SALTELLI_THEME_URI . '/assets/css/components.css',
        ['saltelli-base'],
        $ver
    );

    wp_enqueue_style(
        'saltelli-sections',
        SALTELLI_THEME_URI . '/assets/css/sections.css',
        ['saltelli-components'],
        $ver
    );

    // ------------------------------------------------------------------
    // JS — GSAP stack + Lenis (CDN, defer, footer)
    // SRI hash: TODO hardening post-demo.
    // ------------------------------------------------------------------

    wp_enqueue_script(
        'saltelli-gsap',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
        [],
        null,
        ['in_footer' => true, 'strategy' => 'defer']
    );

    wp_enqueue_script(
        'saltelli-gsap-scrolltrigger',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
        ['saltelli-gsap'],
        null,
        ['in_footer' => true, 'strategy' => 'defer']
    );

    wp_enqueue_script(
        'saltelli-gsap-splittext',
        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/SplitText.min.js',
        ['saltelli-gsap'],
        null,
        ['in_footer' => true, 'strategy' => 'defer']
    );

    wp_enqueue_script(
        'saltelli-lenis',
        'https://cdnjs.cloudflare.com/ajax/libs/lenis/1.1.13/lenis.min.js',
        [],
        null,
        ['in_footer' => true, 'strategy' => 'defer']
    );

    // ------------------------------------------------------------------
    // JS — entrypoint Saltelli (dipende da gsap-core e lenis)
    // ------------------------------------------------------------------

    wp_enqueue_script(
        'saltelli-main',
        SALTELLI_THEME_URI . '/assets/js/main.js',
        ['saltelli-gsap', 'saltelli-gsap-scrolltrigger', 'saltelli-gsap-splittext', 'saltelli-lenis'],
        $ver,
        ['in_footer' => true, 'strategy' => 'defer']
    );

    // ------------------------------------------------------------------
    // TODO Style & Animation agent: enqueue Playfair Display + DM Sans
    // come WOFF2 self-hosted in assets/fonts/, con preload sui 2 weight
    // critici e font-display: swap. Per ora fallback a Google Fonts via
    // @import dentro base.css.
    //
    // Esempio futuro (commentato):
    // add_action('wp_head', function () {
    //     echo '<link rel="preload" href="' . SALTELLI_THEME_URI . '/assets/fonts/playfair-display-700.woff2" as="font" type="font/woff2" crossorigin>';
    //     echo '<link rel="preload" href="' . SALTELLI_THEME_URI . '/assets/fonts/dm-sans-400.woff2" as="font" type="font/woff2" crossorigin>';
    // }, 2);
    // ------------------------------------------------------------------
}

/**
 * Editor styles — parità visiva nel Block Editor.
 */
add_action('after_setup_theme', function () {
    add_editor_style([
        'assets/css/tokens.css',
        'assets/css/base.css',
        'assets/css/components.css',
        'assets/css/sections.css',
    ]);
});
