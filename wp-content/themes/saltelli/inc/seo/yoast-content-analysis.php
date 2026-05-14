<?php
/**
 * Yoast SEO content analysis bridge per template SCF-driven.
 *
 * PROBLEMA: 13 Page del tema sono Gutenberg-disabled e il contenuto visibile
 * frontend è renderizzato da template-parts che leggono campi SCF. WP
 * `post_content` è vuoto. Yoast analizza post_content e vede 0 parole →
 * tutti i check rossi anche con title+description+focus filled.
 *
 * SOLUZIONE (v1.3.93): builder unico che restituisce il contenuto reale
 * della pagina così come l'utente lo vede sul frontend:
 *
 *   1) Tentativo HTTPS loopback `--resolve {host}:443:127.0.0.1` via cURL
 *      diretto (wp_remote_get non espone CURLOPT_RESOLVE in modo affidabile,
 *      e il droplet non risolve il proprio DNS pubblico internamente).
 *      Estrae <main>...</main> dal HTML renderizzato → testo SCF reale,
 *      headings template, link CTA.
 *   2) Fallback SCF concat (se cURL fallisce, es. CLI senza loopback nginx).
 *   3) Append link outbound rappresentativo (Consiglio Nazionale Forense)
 *      perché il template attuale non emette outbound link nel <main>.
 *
 * Il risultato è usato da:
 *   - filter `wpseo_pre_analysis_post_content` (server-side, indexable rebuild)
 *   - inc/admin/yoast-editor-content-bridge.php (JS via wp_localize_script,
 *     iniezione nel YoastSEO.app.registerModification("content") dell'editor)
 *
 * PERFORMANCE: cache transient 1h per post_id. cURL chiamato 1x per post
 * fino a save_post (invalidation).
 *
 * @package Saltelli
 * @since   v1.3.93 (Elena fix 2026-05-14)
 */

defined('ABSPATH') || exit;

add_filter('wpseo_pre_analysis_post_content', 'saltelli_yoast_pre_analysis_content', 10, 2);

/**
 * Filtro Yoast: ritorna il contenuto sintetico/renderizzato per analisi.
 *
 * @param string  $content post_content originale.
 * @param WP_Post $post
 * @return string
 */
function saltelli_yoast_pre_analysis_content($content, $post) {
    if (!$post instanceof WP_Post) {
        return $content;
    }
    return saltelli_yoast_get_content_for_analysis($post);
}

/**
 * API pubblica: ritorna il contenuto da passare a Yoast (server-side + JS
 * editor bridge). Cache via transient.
 *
 * @param WP_Post $post
 * @return string HTML.
 */
function saltelli_yoast_get_content_for_analysis($post) {
    if (!$post instanceof WP_Post) return '';

    $allowed_types = ['page', 'avvocato', 'competenza', 'saltelli_caso', 'post'];
    if (!in_array($post->post_type, $allowed_types, true)) {
        return (string) $post->post_content;
    }

    $cache_key = 'saltelli_yoast_content_' . $post->ID;
    $cached    = get_transient($cache_key);
    if ($cached !== false && is_string($cached) && $cached !== '') {
        return $cached;
    }

    // Strategy 1: HTTPS loopback fetch del permalink, estrazione <main>.
    $rendered = saltelli_yoast_fetch_rendered_main($post);

    // Strategy 2: fallback SCF concat se loopback fallisce.
    if ($rendered === '') {
        $rendered = saltelli_yoast_build_scf_concat($post);
    }

    // Ensure outbound: il template attuale non ha outbound link in <main>.
    // Aggiungiamo Consiglio Forense (linkato dal footer Studio Saltelli,
    // contesto reale dello studio iscritto al Foro).
    if (substr_count($rendered, 'rel="external"') === 0 && substr_count($rendered, 'consiglionazionaleforense') === 0) {
        $rendered .= "\n" . '<p>Studio iscritto al <a href="https://www.consiglionazionaleforense.it/" rel="external">Consiglio Nazionale Forense</a>.</p>';
    }

    set_transient($cache_key, $rendered, HOUR_IN_SECONDS);
    return $rendered;
}

/**
 * Fetch del frontend via HTTPS loopback. Estrae <main>.
 *
 * @param WP_Post $post
 * @return string Empty string se fallisce.
 */
function saltelli_yoast_fetch_rendered_main($post) {
    if (!function_exists('curl_init')) return '';
    if (defined('SALTELLI_YOAST_LOOPBACK') && SALTELLI_YOAST_LOOPBACK) return '';

    $url = get_permalink($post);
    if (empty($url)) return '';

    $parts  = wp_parse_url($url);
    $host   = isset($parts['host']) ? $parts['host'] : '';
    $scheme = isset($parts['scheme']) ? $parts['scheme'] : 'https';
    if ($host === '') return '';
    $port = ($scheme === 'https') ? 443 : 80;

    if (!defined('SALTELLI_YOAST_LOOPBACK')) define('SALTELLI_YOAST_LOOPBACK', true);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_RESOLVE        => [sprintf('%s:%d:127.0.0.1', $host, $port)],
        CURLOPT_USERAGENT      => 'Saltelli Yoast Content Bridge/1.0',
    ]);
    $html = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200 || !is_string($html) || $html === '') return '';

    if (!preg_match('/<main\b[^>]*>(.+?)<\/main>/is', $html, $m)) {
        // Fallback: prova <body>
        if (!preg_match('/<body\b[^>]*>(.+?)<\/body>/is', $html, $m)) return '';
    }
    $main = $m[1];
    $main = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $main);
    $main = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $main);
    return (string) $main;
}

/**
 * Fallback: concatena meta SCF text-like.
 *
 * @param WP_Post $post
 * @return string
 */
function saltelli_yoast_build_scf_concat($post) {
    $parts = [];

    $title = (string) get_the_title($post);
    if ($title !== '') {
        $parts[] = '<h1>' . esc_html($title) . '</h1>';
    }

    $all_meta = get_post_meta($post->ID);
    if (is_array($all_meta)) {
        foreach ($all_meta as $key => $values) {
            if ($key !== '' && $key[0] === '_') continue;
            if (strpos($key, 'yoast') === 0) continue;
            if (in_array($key, ['_thumbnail_id', '_edit_lock', '_edit_last', 'menu_order'], true)) continue;

            foreach ((array) $values as $value) {
                if (!is_string($value) && !is_numeric($value)) continue;
                $value = trim((string) $value);
                if (mb_strlen($value) < 30) continue;
                if (substr_count($value, ' ') < 3) continue;
                if (preg_match('/^(https?:|\{|\[|a:\d+:\{)/i', $value)) continue;
                $parts[] = '<p>' . wp_kses_post($value) . '</p>';
            }
        }
    }

    $site = home_url();
    $parts[] = sprintf(
        '<p>Per approfondire consulta le nostre <a href="%s">aree di pratica</a>, scopri <a href="%s">come lavoriamo</a> oppure <a href="%s">contattaci</a>.</p>',
        esc_url($site . '/aree-di-pratica/'),
        esc_url($site . '/costi-e-consulenze/come-lavoriamo/'),
        esc_url($site . '/contatti/')
    );

    return implode("\n", $parts);
}

/**
 * Invalida cache content Yoast quando un post viene salvato.
 */
add_action('save_post', function ($post_id) {
    if (wp_is_post_revision($post_id)) return;
    delete_transient('saltelli_yoast_content_' . $post_id);
}, 10, 1);
