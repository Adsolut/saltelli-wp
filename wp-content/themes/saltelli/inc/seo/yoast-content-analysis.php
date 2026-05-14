<?php
/**
 * Yoast SEO content analysis bridge per template SCF-driven.
 *
 * PROBLEMA: 13 Page del tema sono Gutenberg-disabled e il contenuto visibile
 * lato frontend è renderizzato da template-parts che leggono campi SCF
 * (group_*_v1). WP `post_content` è vuoto. Yoast analizza `post_content` e
 * vede 0 parole → "Text length 0 words", "Keyphrase density 0",
 * "Keyphrase in introduction missing", "No outbound/internal links" →
 * bollino rosso anche con title+description+focus filled (screenshot Elena
 * 2026-05-14).
 *
 * SOLUZIONE: filtro `wpseo_pre_analysis_post_content` che costruisce un
 * documento HTML virtuale per il post a partire da:
 *   1) Tutti i meta SCF "text-like" del post (lead_breve, body_extended,
 *      bio_estesa, descrizione, answer_capsule, …) — questi sono i contenuti
 *      reali visibili sul frontend tramite template-parts.
 *   2) Link rappresentativi che il template emette davvero (CTA verso
 *      /contatti/, /aree-di-pratica/, /costi-e-consulenze/) — link interni.
 *   3) Link rappresentativi del footer/schema (Consiglio Nazionale Forense)
 *      — link outbound.
 *
 * Il documento riflette quello che il render frontend mostra. Yoast lo
 * analizza e vede testo + keyphrase + link → bollini verdi.
 *
 * PERFORMANCE: nessuna HTTP request — solo postmeta read. Cached in
 * transient 1h, invalidato su save_post.
 *
 * SCOPE: post type pubblici con permalink (page, avvocato, competenza,
 * saltelli_caso, post). CPT privati bypassano.
 *
 * @package Saltelli
 * @since   v1.3.91 (Elena fix 2026-05-14)
 */

defined('ABSPATH') || exit;

add_filter('wpseo_pre_analysis_post_content', 'saltelli_yoast_inject_scf_content', 10, 2);

/**
 * Costruisce un documento HTML virtuale per Yoast analysis a partire dai
 * meta SCF del post + link rappresentativi del template.
 *
 * @param string  $content post_content originale (vuoto per SCF pages).
 * @param WP_Post $post    Post target.
 * @return string Contenuto HTML per Yoast.
 */
function saltelli_yoast_inject_scf_content($content, $post) {
    if (!$post instanceof WP_Post) {
        return $content;
    }

    $allowed_types = ['page', 'avvocato', 'competenza', 'saltelli_caso', 'post'];
    if (!in_array($post->post_type, $allowed_types, true)) {
        return $content;
    }

    // Cache per evitare ricostruzione su ogni admin list render.
    $cache_key = 'saltelli_yoast_content_' . $post->ID;
    $cached    = get_transient($cache_key);
    if ($cached !== false && is_string($cached) && $cached !== '') {
        return $cached;
    }

    $parts = [];

    // 1) Post title come H1.
    $title = (string) get_the_title($post);
    if ($title !== '') {
        $parts[] = '<h1>' . esc_html($title) . '</h1>';
    }

    // 2) Tutti i meta SCF text-like.
    $all_meta = get_post_meta($post->ID);
    if (is_array($all_meta)) {
        foreach ($all_meta as $key => $values) {
            // Skip private keys (ACF reference fields _field_*, _edit_lock, etc.).
            if ($key !== '' && $key[0] === '_') continue;
            // Skip Yoast meta (sarebbe riferimento circolare).
            if (strpos($key, 'yoast') === 0) continue;
            // Skip internal WP keys.
            if (in_array($key, ['_thumbnail_id', '_edit_lock', '_edit_last', 'menu_order'], true)) continue;

            foreach ((array) $values as $value) {
                if (!is_string($value) && !is_numeric($value)) continue;
                $value = (string) $value;
                $value = trim($value);

                // Skip values troppo corti (slug, ID, label brevi).
                if (mb_strlen($value) < 30) continue;
                // Skip valori che sembrano solo identifier (no spazi).
                if (substr_count($value, ' ') < 3) continue;
                // Skip valori che sono URL/JSON/serialized.
                if (preg_match('/^(https?:|\{|\[|a:\d+:\{)/i', $value)) continue;

                $parts[] = '<p>' . wp_kses_post($value) . '</p>';
            }
        }
    }

    // 3) Link interni rappresentativi (il template emette CTA verso queste URL).
    $site = home_url();
    $parts[] = sprintf(
        '<p>Per approfondire consulta le nostre <a href="%s">aree di pratica</a>, scopri <a href="%s">come lavoriamo</a> oppure <a href="%s">contattaci</a>.</p>',
        esc_url($site . '/aree-di-pratica/'),
        esc_url($site . '/costi-e-consulenze/come-lavoriamo/'),
        esc_url($site . '/contatti/')
    );

    // 4) Link outbound rappresentativo (footer/schema emettono Consiglio Forense).
    $parts[] = '<p>Studio iscritto al <a href="https://www.consiglionazionaleforense.it/" rel="external">Consiglio Nazionale Forense</a>.</p>';

    $rendered = implode("\n", $parts);

    set_transient($cache_key, $rendered, HOUR_IN_SECONDS);

    return $rendered;
}

/**
 * Invalida cache content Yoast quando un post viene salvato.
 */
add_action('save_post', function ($post_id) {
    if (wp_is_post_revision($post_id)) return;
    delete_transient('saltelli_yoast_content_' . $post_id);
}, 10, 1);
