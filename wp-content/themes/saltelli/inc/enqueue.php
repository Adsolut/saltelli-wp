<?php
/**
 * Asset enqueue — CSS, JS, fonts.
 *
 * Stato: scaffold. Carichiamo SOLO i CSS minimali e l'entrypoint JS.
 * GSAP, Lenis, font WOFF2 e Splittext NON sono ancora caricati: lasciamo
 * gli hook pronti perché lo Style & Animation agent li popoli quando il
 * design sarà firmato.
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', 'saltelli_enqueue_assets');
function saltelli_enqueue_assets() {

    $ver = SALTELLI_THEME_VERSION;

    // CSS — design tokens (CSS variables)
    wp_enqueue_style(
        'saltelli-tokens',
        SALTELLI_THEME_URI . '/assets/css/tokens.css',
        [],
        $ver
    );

    // CSS — base (reset + tipografia minimale + container)
    wp_enqueue_style(
        'saltelli-base',
        SALTELLI_THEME_URI . '/assets/css/base.css',
        ['saltelli-tokens'],
        $ver
    );

    // JS — entrypoint, defer in footer
    wp_enqueue_script(
        'saltelli-main',
        SALTELLI_THEME_URI . '/assets/js/main.js',
        [],
        $ver,
        ['in_footer' => true, 'strategy' => 'defer']
    );

    // ------------------------------------------------------------------
    // TODO Style & Animation agent: enqueue Playfair Display + DM Sans
    // (o Cormorant + Satoshi se decisi diversi) come WOFF2 self-hosted,
    // con preload sui 2 weight critici e font-display: swap.
    // Esempio:
    // add_action('wp_head', function () {
    //     echo '<link rel="preload" href="' . SALTELLI_THEME_URI . '/assets/fonts/playfair-display-700.woff2" as="font" type="font/woff2" crossorigin>';
    // }, 2);
    // ------------------------------------------------------------------

    // ------------------------------------------------------------------
    // TODO Style & Animation agent: enqueue GSAP 3.15+ con SRI, ScrollTrigger, SplitText.
    // CDN (jsdelivr) + integrity hash. Esempio (commentato finché non firmato il design):
    //
    // wp_enqueue_script('gsap',           'https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js',          [],        '3.15', ['in_footer' => true, 'strategy' => 'defer']);
    // wp_enqueue_script('gsap-scroll',    'https://cdn.jsdelivr.net/npm/gsap@3.15/dist/ScrollTrigger.min.js', ['gsap'],  '3.15', ['in_footer' => true, 'strategy' => 'defer']);
    // wp_enqueue_script('gsap-splittext', 'https://cdn.jsdelivr.net/npm/gsap@3.15/dist/SplitText.min.js',     ['gsap'],  '3.15', ['in_footer' => true, 'strategy' => 'defer']);
    //
    // Aggiungere SRI via filtro `script_loader_tag` se richiesto.
    // ------------------------------------------------------------------

    // ------------------------------------------------------------------
    // TODO Style & Animation agent: enqueue Lenis (smooth momentum scroll)
    // wp_enqueue_script('lenis', 'https://cdn.jsdelivr.net/npm/lenis@latest/dist/lenis.min.js', [], null, ['in_footer' => true, 'strategy' => 'defer']);
    // ------------------------------------------------------------------
}

/**
 * Editor styles — placeholder.
 * TODO Style & Animation agent: caricare lo stesso tokens.css/base.css
 * sul Block Editor per parità visiva.
 */
add_action('after_setup_theme', function () {
    add_editor_style(['assets/css/tokens.css', 'assets/css/base.css']);
});
