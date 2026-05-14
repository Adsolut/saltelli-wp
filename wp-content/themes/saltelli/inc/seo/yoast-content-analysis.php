<?php
/**
 * Yoast SEO content analysis bridge per template SCF-driven.
 *
 * PROBLEMA: 13 Page del tema sono Gutenberg-disabled e il contenuto vivibile
 * lato frontend è renderizzato da template-parts che leggono campi SCF
 * (group_*_v1). WP `post_content` è vuoto. Yoast analizza `post_content` e
 * vede 0 parole → "Text length 0 words", "Keyphrase density 0",
 * "Keyphrase in introduction missing", "No outbound/internal links" →
 * bollino rosso anche con title+description+focus filled.
 *
 * SOLUZIONE: filtro `wpseo_pre_analysis_post_content` che fa una HTTP GET sul
 * permalink della pagina (server-loopback), estrae <main>...</main> dal HTML
 * renderizzato e lo passa a Yoast come contenuto da analizzare. Risultato:
 * Yoast vede esattamente quello che vede l'utente sul frontend (testo, link
 * interni dai CTA, link esterni dal footer/schema).
 *
 * PERFORMANCE: risultato cached in transient 1h per post_id. Reindex Yoast
 * triggera 1 wp_remote_get per post. Su admin list / metabox edit le chiamate
 * successive sono no-op (cache hit). Recursion-guard via constant.
 *
 * SCOPE: solo post type pubblici con permalink (page, avvocato, competenza,
 * saltelli_caso, post). Altri tipi (saltelli_faq private, etc.) → bypass.
 *
 * @package Saltelli
 * @since   v1.3.90 (Elena fix 2026-05-14)
 */

defined('ABSPATH') || exit;

add_filter('wpseo_pre_analysis_post_content', 'saltelli_yoast_inject_rendered_html', 10, 2);

/**
 * Ritorna l'HTML <main> renderizzato del permalink della pagina così Yoast
 * lo analizza al posto del post_content vuoto.
 *
 * @param string  $content post_content originale (di solito vuoto per SCF pages).
 * @param WP_Post $post    Post target.
 * @return string Contenuto per Yoast content analysis.
 */
function saltelli_yoast_inject_rendered_html($content, $post) {
    if (!$post instanceof WP_Post) {
        return $content;
    }

    // Solo post type con permalink pubblico.
    $allowed_types = ['page', 'avvocato', 'competenza', 'saltelli_caso', 'post'];
    if (!in_array($post->post_type, $allowed_types, true)) {
        return $content;
    }

    // Recursion guard: durante il loopback fetch evita di rientrare nel filtro.
    if (defined('SALTELLI_YOAST_LOOPBACK') && SALTELLI_YOAST_LOOPBACK) {
        return $content;
    }

    // Cache: stato pagina cambia raramente in admin. Transient 1h.
    $cache_key = 'saltelli_yoast_html_' . $post->ID;
    $cached    = get_transient($cache_key);
    if ($cached !== false && is_string($cached)) {
        return $cached;
    }

    $url = get_permalink($post);
    if (empty($url) || !is_string($url)) {
        return $content;
    }

    // Marker per recursion guard.
    if (!defined('SALTELLI_YOAST_LOOPBACK')) {
        define('SALTELLI_YOAST_LOOPBACK', true);
    }

    $response = wp_remote_get($url, [
        'timeout'     => 8,
        'sslverify'   => false, // staging self-signed possibile
        'redirection' => 3,
        'user-agent'  => 'Saltelli Yoast Content Analysis/1.0',
    ]);

    if (is_wp_error($response)) {
        return $content;
    }

    if (wp_remote_retrieve_response_code($response) !== 200) {
        return $content;
    }

    $html = (string) wp_remote_retrieve_body($response);
    if ($html === '') {
        return $content;
    }

    // Estrai <main>...</main> (template Saltelli usa <main class="site-main">).
    // Fallback su <body>...</body>.
    if (preg_match('/<main\b[^>]*>(.+?)<\/main>/is', $html, $m)) {
        $rendered = $m[1];
    } elseif (preg_match('/<body\b[^>]*>(.+?)<\/body>/is', $html, $m)) {
        $rendered = $m[1];
    } else {
        return $content;
    }

    // Strip <script>/<style> blocks: Yoast analyzer not dovrebbe vederli.
    $rendered = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $rendered);
    $rendered = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $rendered);

    set_transient($cache_key, $rendered, HOUR_IN_SECONDS);

    return $rendered;
}

/**
 * Invalida cache HTML Yoast quando un post viene salvato.
 */
add_action('save_post', function ($post_id) {
    if (wp_is_post_revision($post_id)) return;
    delete_transient('saltelli_yoast_html_' . $post_id);
}, 10, 1);
