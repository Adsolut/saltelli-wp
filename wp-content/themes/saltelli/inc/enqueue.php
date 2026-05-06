<?php
/**
 * Asset enqueue — CSS, JS, fonts.
 *
 * v0.21.0 Performance Hardening (2026-05-01):
 *  - CSS chain: tokens → base → components → logo → sections
 *  - WOFF2 self-hosted in /assets/fonts/ (Latin subset, ~144KB total)
 *  - Font preload: Playfair Display variable + DM Sans variable (LCP critical)
 *  - GSAP 3.12.5 + ScrollTrigger + SplitText + Lenis 1.1.13 da CDN cdnjs
 *    + SRI sha384 integrity + crossorigin="anonymous" via script_loader_tag filter
 *  - main.js dipende da gsap-core e lenis
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
        'saltelli-logo',
        SALTELLI_THEME_URI . '/assets/css/logo.css',
        ['saltelli-components'],
        $ver
    );

    wp_enqueue_style(
        'saltelli-sections',
        SALTELLI_THEME_URI . '/assets/css/sections.css',
        ['saltelli-logo'],
        $ver
    );

    /* === Wave 6 [GEO/CRO blocks] — cro.css bundle (10 pattern adapted) === */
    wp_enqueue_style(
        'saltelli-cro',
        SALTELLI_THEME_URI . '/assets/css/components/cro.css',
        ['saltelli-components'],
        $ver
    );

    // ------------------------------------------------------------------
    // JS — GSAP core + ScrollTrigger (CDN cdnjs, defer, footer, SRI hash).
    //
    // v0.21.2: SplitText (paid GSAP plugin, NOT su cdnjs free) + Lenis
    // (URL cdnjs inesistente, libreria disabilitata in main.js da Polish
    // Agent) RIMOSSI. Erano 2 404 silent + 2 MIME-refused warning su
    // ogni page load (Lighthouse "Browser errors logged to the console").
    // main.js ha fallback logic: typeof window.SplitText/Lenis !== 'undefined'.
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

    // ------------------------------------------------------------------
    // JS — entrypoint Saltelli (dipende solo da gsap-core + scrolltrigger)
    // ------------------------------------------------------------------

    wp_enqueue_script(
        'saltelli-main',
        SALTELLI_THEME_URI . '/assets/js/main.js',
        ['saltelli-gsap', 'saltelli-gsap-scrolltrigger'],
        $ver,
        ['in_footer' => true, 'strategy' => 'defer']
    );

}

/* === IMPECCABLE v0.21.0 [perf-T1] Font preload — LCP critical paths ===
   Playfair Display variable (H1 hero italic) + DM Sans variable (body lede).
   Preload via wp_head priority 2 (after charset/viewport, before CSS chain).
   crossorigin="anonymous" obbligatorio per WOFF2 same-origin. */
add_action('wp_head', function () {
    $base = SALTELLI_THEME_URI . '/assets/fonts';
    $ver  = SALTELLI_THEME_VERSION;
    echo '<link rel="preload" href="' . esc_url($base . '/playfair-display-variable.woff2?v=' . $ver) . '" as="font" type="font/woff2" crossorigin="anonymous">' . "\n";
    echo '<link rel="preload" href="' . esc_url($base . '/dm-sans-variable.woff2?v=' . $ver) . '" as="font" type="font/woff2" crossorigin="anonymous">' . "\n";
}, 2);

/* === IMPECCABLE v0.21.0 [perf-T3] SRI sha384 + crossorigin per CDN script ===
   GSAP/ScrollTrigger/SplitText (cdnjs gsap 3.12.5) + Lenis (cdnjs 1.1.13).
   Hash generati con: curl -sL <url> | openssl dgst -sha384 -binary | openssl base64 -A
   Update mandatory se la version dei file cambia (mismatch = browser blocca). */
add_filter('script_loader_tag', function ($tag, $handle) {
    $sri = [
        'saltelli-gsap'                => 'sha384-g4NTh/Iv5PPU4xPyhEWqPcwtNXOvdaDI8LLnyYfyNZOjKJeYQyjzQ9X5275eBjpt',
        'saltelli-gsap-scrolltrigger'  => 'sha384-Z3REaz79l2IaAZqJsSABtTbhjgOUYyV3p90XNnAPCSHg3EMTz1fouunq9WZRtj3d',
        // SplitText + Lenis rimossi v0.21.2 (404 cdnjs, vedi commento sopra).
    ];
    if (!isset($sri[$handle])) {
        return $tag;
    }
    $integrity = $sri[$handle];
    // Inietta integrity + crossorigin senza rompere l'attribute order esistente.
    return preg_replace(
        '/<script /',
        '<script integrity="' . esc_attr($integrity) . '" crossorigin="anonymous" referrerpolicy="no-referrer" ',
        $tag,
        1
    );
}, 10, 2);

/**
 * Editor styles — parità visiva nel Block Editor.
 */
add_action('after_setup_theme', function () {
    add_editor_style([
        'assets/css/tokens.css',
        'assets/css/base.css',
        'assets/css/components.css',
        'assets/css/logo.css',
        'assets/css/sections.css',
        'assets/css/components/cro.css',
    ]);
});

/**
 * Favicon — Logo system v1.1 monogramma SVG.
 * Saltelli "S" Playfair italic con cerchio doppio (navy + bronze accent).
 * Disabilita Customizer Site Icon (cropped-icon1-*.jpg legacy 2021/04).
 */
remove_action('wp_head', 'wp_site_icon', 99);
add_action('wp_head', function () {
    $brand = SALTELLI_THEME_URI . '/assets/img/brand';
    $ver = SALTELLI_THEME_VERSION;
    echo '<link rel="icon" type="image/svg+xml" href="' . esc_url($brand . '/favicon.svg?v=' . $ver) . '">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url($brand . '/apple-touch-icon.svg?v=' . $ver) . '">' . "\n";
    echo '<meta name="apple-mobile-web-app-title" content="Saltelli">' . "\n";
}, 1);
