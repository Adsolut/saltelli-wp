<?php
/**
 * Saltelli — Legacy URL Redirect 301 (v0.13.0 IA Unification)
 *
 * Mapping URL del vecchio sito (pre-2026, page WP Elementor-based)
 * verso i nuovi CPT competenza / page editoriali. Preserva SEO + backlink
 * esterni storici. Esegue su `init` priority 1 per intercettare prima del
 * canonical redirect WP (analogo a /lo-studio/ → /chi-siamo/ in setup.php).
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

if (!function_exists('saltelli_legacy_redirect_map')) :
function saltelli_legacy_redirect_map() {
    return [
        // CPT competenza — slug verificati su DB CPT publish
        '/recupero-crediti/'                        => '/competenze/recupero-crediti/',
        '/cartelle-esattoriali-e-multe/'            => '/competenze/cartelle-esattoriali-e-multe/',
        '/diritto-bancario/'                        => '/competenze/diritto-bancario/',
        '/avvocato-divorzista/'                     => '/competenze/diritto-di-famiglia/',
        '/avvocato-divorzista-italia/'              => '/competenze/diritto-di-famiglia/',
        '/lavoro/'                                  => '/competenze/diritto-del-lavoro/',
        '/eredita-e-successioni/'                   => '/competenze/diritto-delle-successioni/',
        '/condominio-e-locazioni/'                  => '/competenze/diritto-condominiale/',
        '/responsabilita-medica/'                   => '/competenze/responsabilita-medica/',
        '/immigrazione/'                            => '/competenze/diritto-dellimmigrazione/',
        '/infortunistica-stradale/'                 => '/competenze/risarcimento-danni/',
        '/infortunistica-stradale-italia/'          => '/competenze/risarcimento-danni/',
        '/risarcimento-del-danno/'                  => '/competenze/risarcimento-danni/',
        '/diritto-tributario/'                      => '/competenze/diritto-tributario/',
        '/ricorsi-napoli-obiettivo-valore/'         => '/competenze/cartelle-esattoriali-e-multe/',
        '/invalidita-civile-diritto-previdenziale/' => '/competenze/diritto-previdenziale/',
        '/diritto-amministrativo/'                  => '/competenze/diritto-amministrativo/',
        '/diritto-penale/'                          => '/competenze/diritto-penale/',
        // NB: slug CPT è "domiciliazione-dimpresa" (con d apostrofata, no spazio)
        '/domicilia-la-tua-azienda/'                => '/competenze/domiciliazione-dimpresa/',

        // Pages orfane senza CPT corrispondente → archive competenze
        '/diritto-societario/'                      => '/competenze/',
        '/contrattualistica/'                       => '/competenze/',
        '/aste-immobiliari/'                        => '/competenze/',
        '/servizi-legali/'                          => '/competenze/',

        // Funnel utility legacy → contatti (se page draft o non rilevante)
        '/prenota-un-appuntamento/'                 => '/contatti/',

        // Legacy menu /lo-studio/ → /chi-siamo/ (ridondante con setup.php hook,
        // mantenuto qui per centralizzazione mapping in unico file)
        '/lo-studio/'                               => '/chi-siamo/',
    ];
}
endif;

if (!function_exists('saltelli_legacy_redirect')) :
function saltelli_legacy_redirect() {
    if (is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) return;
    if (defined('WP_CLI') && WP_CLI) return;

    $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
    $path = (string) parse_url($request_uri, PHP_URL_PATH);

    // Normalizza: garantisce trailing slash
    if ($path === '' || $path === '/') return;
    if (substr($path, -1) !== '/') {
        $path .= '/';
    }

    $map = saltelli_legacy_redirect_map();
    if (isset($map[$path])) {
        wp_safe_redirect(home_url($map[$path]), 301);
        exit;
    }
}
endif;

// Hook su `init` priority 1 — early intercept prima di canonical redirect WP.
// Pattern verified su /lo-studio/ → /chi-siamo/ in setup.php.
add_action('init', 'saltelli_legacy_redirect', 1);
