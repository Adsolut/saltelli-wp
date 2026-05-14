<?php
/**
 * Yoast SEO editor metabox content bridge per template SCF-driven.
 *
 * PROBLEMA: il filtro server-side `wpseo_pre_analysis_post_content` (vedi
 * inc/seo/yoast-content-analysis.php) è chiamato da Yoast SOLO per estrazione
 * immagini, NON per l'analisi testo del metabox in edit screen. La metabox
 * Yoast legge il contenuto direttamente dal DOM dell'editor (Classic
 * textarea o Gutenberg block tree). Per le 13 Page SCF-driven con
 * post_content vuoto + Gutenberg-disabled, l'editor textarea è vuota →
 * Yoast vede 0 parole.
 *
 * SOLUZIONE: registriamo un plugin JS Yoast nell'editor che usa l'API
 * YoastSEO.app.registerModification('content', …) per intercettare il
 * content prima dell'analisi e iniettare il synthetic HTML costruito
 * server-side (stessi SCF fields + CTA links + outbound link del filtro
 * server-side).
 *
 * SCOPE: solo edit screen di post type pubblici (page, avvocato, competenza,
 * saltelli_caso, post). Nessun impatto frontend, nessun touch su post_content.
 *
 * @package Saltelli
 * @since   v1.3.92 (Elena fix 2026-05-14)
 */

defined('ABSPATH') || exit;

add_action('admin_enqueue_scripts', 'saltelli_yoast_editor_bridge_enqueue');

/**
 * Enqueue JS bridge sull'edit screen di post type rilevanti.
 *
 * @param string $hook current admin page hook.
 */
function saltelli_yoast_editor_bridge_enqueue($hook) {
    // Solo edit screen (post.php, post-new.php).
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }

    global $post;
    if (!$post instanceof WP_Post) {
        return;
    }

    $allowed_types = ['page', 'avvocato', 'competenza', 'saltelli_caso', 'post'];
    if (!in_array($post->post_type, $allowed_types, true)) {
        return;
    }

    // Solo se Yoast plugin attivo.
    if (!defined('WPSEO_VERSION')) {
        return;
    }

    // v1.3.93: builder unificato (HTTPS loopback + fallback SCF) in
    // inc/seo/yoast-content-analysis.php — saltelli_yoast_get_content_for_analysis().
    $synthetic_content = function_exists('saltelli_yoast_get_content_for_analysis')
        ? saltelli_yoast_get_content_for_analysis($post)
        : saltelli_yoast_build_synthetic_content($post);

    // Empty handle script (inline only) per beneficiare di wp_add_inline_script.
    wp_register_script(
        'saltelli-yoast-editor-bridge',
        '',
        ['wp-seo-post-scraper'], // Yoast post analysis script handle
        '1.0',
        true
    );
    wp_enqueue_script('saltelli-yoast-editor-bridge');

    // Pass synthetic content to JS context.
    wp_add_inline_script('saltelli-yoast-editor-bridge', sprintf(
        'window.SaltelliYoastSyntheticContent = %s;',
        wp_json_encode($synthetic_content)
    ), 'before');

    // JS plugin che registra modification su Yoast analyzer.
    $js = <<<'JS'
(function () {
    var max_attempts = 60; // ~12s
    var attempts = 0;
    var content = (typeof window.SaltelliYoastSyntheticContent === 'string')
        ? window.SaltelliYoastSyntheticContent
        : '';
    if (!content) return;

    function register() {
        if (typeof window.YoastSEO === 'undefined' || !window.YoastSEO.app) {
            attempts++;
            if (attempts < max_attempts) setTimeout(register, 200);
            return;
        }
        try {
            window.YoastSEO.app.registerPlugin('saltelliScfBridge', { status: 'ready' });
            window.YoastSEO.app.registerModification(
                'content',
                function (data) {
                    // Se il content reale è già abbondante (Elena ha scritto in editor), non sovrascrivere.
                    var trimmed = (typeof data === 'string') ? data.trim() : '';
                    if (trimmed.length > 200) return data;
                    return content;
                },
                'saltelliScfBridge',
                10
            );
            window.YoastSEO.app.refresh();
        } catch (e) {
            // Yoast API change — fallback: niente da fare.
            if (window.console && window.console.warn) {
                console.warn('Saltelli Yoast bridge: registerModification failed', e);
            }
        }
    }
    register();
})();
JS;

    wp_add_inline_script('saltelli-yoast-editor-bridge', $js, 'after');
}

/**
 * Costruisce synthetic HTML content per il post (stessa logica del filtro
 * server-side ma esposto separato per riutilizzo qui).
 *
 * @param WP_Post $post
 * @return string HTML.
 */
function saltelli_yoast_build_synthetic_content($post) {
    if (!$post instanceof WP_Post) return '';

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

    $parts[] = '<p>Studio iscritto al <a href="https://www.consiglionazionaleforense.it/" rel="external">Consiglio Nazionale Forense</a>.</p>';

    return implode("\n", $parts);
}
