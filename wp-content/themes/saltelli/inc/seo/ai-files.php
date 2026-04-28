<?php
/**
 * AI-friendly files — /llms.txt + robots.txt enrichment.
 *
 * 1) /llms.txt è servito dinamicamente leggendo geo-assets/llms.txt.
 *    In produzione, alternativa più solida: copiare il file in webroot
 *    (così nginx/Apache lo serve direttamente, senza bootstrap WP).
 *
 * 2) robots.txt: il filter `robots_txt` di WP arriva con un robots di base.
 *    Sostituiamo con il nostro file ottimizzato AI (geo-assets/robots.txt),
 *    mantenendo eventuali Sitemap appese da plugin SEO.
 *
 * Path file source:
 *   ../../../../geo-assets/llms.txt   (relativo al tema)
 *   ../../../../geo-assets/robots.txt
 *
 * Dato che il tema vive in /wp-content/themes/saltelli/, il file
 * geo-assets/ è 4 livelli sopra. Risolviamo in modo robusto via ABSPATH
 * + ricerca in due location possibili.
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

/**
 * Cerca il file `geo-assets/{name}` partendo da location plausibili.
 * Restituisce path assoluto o false.
 */
function saltelli_locate_geo_asset($name) {
    $candidates = [
        // Repo layout: il tema vive in saltelli-wp/wp-content/themes/saltelli/.
        // geo-assets sta in saltelli-wp/geo-assets/.
        dirname(ABSPATH) . '/geo-assets/' . $name,
        // Container Docker: ABSPATH = /var/www/html/, ma il bind mount
        // monta solo wp-content. Geo-assets non è quindi nel container.
        // In quel caso ricadiamo sul tema: copia statica in wp-content/themes/saltelli/geo-assets/.
        SALTELLI_THEME_DIR . '/geo-assets/' . $name,
        SALTELLI_THEME_DIR . '/' . $name,
    ];
    foreach ($candidates as $path) {
        if (is_readable($path)) {
            return $path;
        }
    }
    return false;
}

/**
 * Add rewrite rule for /llms.txt.
 */
add_action('init', function () {
    add_rewrite_rule('^llms\.txt$', 'index.php?saltelli_llms=1', 'top');
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'saltelli_llms';
    return $vars;
});

/**
 * Disabilita redirect_canonical su /llms.txt (WP cercherebbe di aggiungere
 * trailing slash). Va matchato sul path richiesto, prima del nostro
 * template_redirect handler.
 */
add_filter('redirect_canonical', function ($redirect_url, $requested_url) {
    $req_path = parse_url($requested_url, PHP_URL_PATH);
    if ($req_path === '/llms.txt') {
        return false;
    }
    return $redirect_url;
}, 10, 2);

/**
 * Serve /llms.txt as text/plain.
 */
add_action('template_redirect', function () {
    if ((int) get_query_var('saltelli_llms') !== 1) {
        return;
    }
    $path = saltelli_locate_geo_asset('llms.txt');
    if (!$path) {
        status_header(404);
        nocache_headers();
        echo "# llms.txt not found in this environment.\n";
        echo "# TODO: copy geo-assets/llms.txt to a path readable from PHP, or to webroot.\n";
        exit;
    }
    nocache_headers();
    header('Content-Type: text/plain; charset=utf-8');
    header('X-Robots-Tag: noindex'); // il file stesso non va indicizzato come HTML
    readfile($path);
    exit;
});

/**
 * Robots.txt — sovrascrivi con la nostra versione AI-ottimizzata.
 * Manteniamo le righe Sitemap aggiunte da WP/plugin.
 */
add_filter('robots_txt', function ($output, $public) {

    if (!$public) {
        return $output;
    }

    $path = saltelli_locate_geo_asset('robots.txt');
    if (!$path) {
        return $output; // fallback: lasciamo il robots WP standard
    }

    $custom = file_get_contents($path);
    if ($custom === false) {
        return $output;
    }

    // Estrai le linee Sitemap dall'output WP/plugin (Yoast, ecc.) e mergia.
    $existing_sitemaps = [];
    if (preg_match_all('/^Sitemap:\s*(.+)$/mi', $output, $m)) {
        $existing_sitemaps = array_unique(array_map('trim', $m[1]));
    }

    // Rimuovi le sitemap statiche del nostro file (sono URL produzione hardcoded)
    // e ri-aggiungiamo quelle reali del runtime.
    $custom = preg_replace('/^Sitemap:.*$/mi', '', $custom);
    $custom = rtrim($custom) . "\n\n";

    if (!empty($existing_sitemaps)) {
        foreach ($existing_sitemaps as $sm) {
            $custom .= "Sitemap: " . $sm . "\n";
        }
    } else {
        $custom .= "Sitemap: " . home_url('/sitemap.xml') . "\n";
    }

    return $custom;
}, 10, 2);

/**
 * Ricorda di flushare le rewrite rules dopo l'attivazione del tema.
 * Non lo facciamo in init (troppo pesante) — il dev runs `wp rewrite flush`.
 */
add_action('after_switch_theme', function () {
    flush_rewrite_rules(false);
});
