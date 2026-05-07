<?php
/**
 * Wave 4.5 — Per-template critical CSS injection.
 *
 * Inietta inline il CSS critical estratto via penthouse per il template
 * corrente + viewport (mobile/desktop), così first paint avvenga senza
 * render-blocking del CSS principale (~328KB tokens+base+components+
 * logo+cro+sections).
 *
 * Pattern:
 * 1. wp_head priority 1 → emette <style id="saltelli-critical-css" data-pattern="...">
 *    con il critical CSS file relevant (~14-17KB).
 * 2. style_loader_tag filter → converte 4 main bundle (tokens/base/components/
 *    cro) in <link rel="preload" as="style" onload="...">; sections.css resta
 *    gestito dal filter dedicato `saltelli_defer_sections_css` (legacy v0.21.2).
 * 3. logo.css resta SYNC (cross-template above-fold safety, ~12KB).
 *
 * Patterns supportati (matched in `saltelli_detect_template_pattern()`):
 *   - home              — front page
 *   - competenza-tier1  — DEC-021 deep slugs (tributario, lavoro, lgbtq+)
 *   - competenza-tier2  — altre 16 competenze
 *   - single-avvocato   — CPT avvocato single
 *   - page-generic      — page / single post / archive / 404
 *
 * Wave 4 lesson learned (DEC-026 #1): deferring TUTTI i bundle senza un
 * critical CSS extracto provoca CLS 0.001→0.29. Wave 4.5 sblocca il
 * deferral aggressivo perché ora il critical extracto via penthouse
 * COPRE l'above-fold cross-template.
 *
 * @package Saltelli
 * @since 1.3.1 Wave 4.5
 */

defined('ABSPATH') || exit;

/**
 * Detect template pattern for current request.
 *
 * @return string|null Pattern key or null if no critical available.
 */
if (!function_exists('saltelli_detect_template_pattern')) :
function saltelli_detect_template_pattern() {
    if (is_front_page()) {
        return 'home';
    }
    if (is_singular('competenza')) {
        // Tier-1 deep slugs (DEC-021 cliente firmati)
        $tier1_slugs = [
            'diritto-tributario',
            'diritto-del-lavoro',
            'diritto-di-famiglia-lgbtq',
            'diritto-di-famiglia-lgbtq-plus',
            'famiglia-lgbtq',
        ];
        $current_slug = get_post_field('post_name', get_the_ID());
        return in_array($current_slug, $tier1_slugs, true) ? 'competenza-tier1' : 'competenza-tier2';
    }
    if (is_singular('avvocato')) {
        return 'single-avvocato';
    }
    if (is_page() || is_single() || is_archive() || is_home() || is_404()) {
        return 'page-generic';
    }
    return null;
}
endif;

/**
 * Inline critical CSS in <head> (priority 1, prima di tutti gli enqueue).
 */
if (!function_exists('saltelli_inline_critical_css')) :
function saltelli_inline_critical_css() {
    $pattern = saltelli_detect_template_pattern();
    if (!$pattern) {
        return; // Graceful: full CSS chain loads normally.
    }

    $suffix = wp_is_mobile() ? 'mobile' : 'desktop';
    $critical_path = SALTELLI_THEME_DIR . "/assets/css/critical/{$pattern}-{$suffix}.css";

    if (!file_exists($critical_path)) {
        return;
    }

    $critical_css = file_get_contents($critical_path);
    if (empty($critical_css)) {
        return;
    }

    printf(
        '<style id="saltelli-critical-css" data-pattern="%s-%s">%s</style>' . "\n",
        esc_attr($pattern),
        esc_attr($suffix),
        $critical_css
    );
}
endif;
add_action('wp_head', 'saltelli_inline_critical_css', 1);

/**
 * Async-load 4 main CSS bundles via preload + onload swap.
 *
 * Handles deferred:
 *   - saltelli-tokens
 *   - saltelli-base
 *   - saltelli-components
 *   - saltelli-cro
 *
 * NOT deferred (rationale):
 *   - saltelli-logo:     header logo styles, cross-template above-fold safety.
 *   - saltelli-sections: gestito dal filter dedicato `saltelli_defer_sections_css`
 *                        (legacy v0.21.2 baseline, già funzionante).
 */
if (!function_exists('saltelli_async_main_css')) :
function saltelli_async_main_css($html, $handle) {
    if (is_admin()) {
        return $html;
    }

    static $async_handles = [
        'saltelli-tokens',
        'saltelli-base',
        'saltelli-components',
        'saltelli-cro',
    ];
    if (!in_array($handle, $async_handles, true)) {
        return $html;
    }

    if (!preg_match("/href=['\"]([^'\"]+)['\"]/", $html, $href_m)) {
        return $html;
    }
    $href = $href_m[1];
    $id_match = preg_match("/id=['\"]([^'\"]+)['\"]/", $html, $id_m);
    $id = $id_match ? $id_m[1] : ($handle . '-css');

    $preload = '<link rel="preload" as="style" id="' . esc_attr($id) . '" href="' . esc_url($href) . '" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
    $noscript = '<noscript><link rel="stylesheet" href="' . esc_url($href) . '"></noscript>' . "\n";

    return $preload . $noscript;
}
endif;
add_filter('style_loader_tag', 'saltelli_async_main_css', 10, 2);

/**
 * Defer sections.css via preload+onload pattern (legacy v0.21.2 baseline).
 *
 * Pattern identico a saltelli_async_main_css ma scope al solo handle
 * 'saltelli-sections' — preserva la behaviour pre-Wave 4.5 (era già
 * defer da v0.21.2).
 */
if (!function_exists('saltelli_defer_sections_css')) :
function saltelli_defer_sections_css($html, $handle) {
    if ($handle !== 'saltelli-sections') {
        return $html;
    }
    if (!preg_match("/href=['\"]([^'\"]+)['\"]/", $html, $href_m)) {
        return $html;
    }
    $href = $href_m[1];
    $id_match = preg_match("/id=['\"]([^'\"]+)['\"]/", $html, $id_m);
    $id = $id_match ? $id_m[1] : 'saltelli-sections-css';

    $preload  = '<link rel="preload" as="style" id="' . esc_attr($id) . '" href="' . esc_url($href) . '" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
    $noscript = '<noscript><link rel="stylesheet" href="' . esc_url($href) . '"></noscript>' . "\n";

    return $preload . $noscript;
}
endif;
add_filter('style_loader_tag', 'saltelli_defer_sections_css', 10, 2);
