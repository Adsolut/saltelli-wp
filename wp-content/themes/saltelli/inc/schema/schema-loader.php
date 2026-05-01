<?php
/**
 * Schema JSON-LD loader.
 *
 * Si aggancia a wp_head con priorità alta (dopo i meta SEO) e include
 * i partial appropriati per il template corrente.
 *
 * Regole:
 *   - partial-organization SEMPRE (Organization + WebSite, header globale)
 *   - partial-attorney    se is_singular('avvocato')
 *   - partial-faqpage     se is_singular('competenza') AND >= 1 FAQ valida
 *   - partial-article     se is_singular('post')
 *   - partial-breadcrumb  su tutto tranne homepage
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

// Priorità 5 — dopo i meta tag SEO (saltelli_emit_meta_tags @ 4) e prima
// dei core wp_head defaults. La trasformazione UTF-8 → entità HTML che
// alcuni plugin applicano via DOMDocument (es. iubenda) è neutralizzata
// dall'helper saltelli_emit_jsonld() che emette JSON ASCII-safe (\uXXXX).
add_action('wp_head', 'saltelli_emit_schema', 5);
function saltelli_emit_schema() {

    $dir = SALTELLI_THEME_DIR . '/inc/schema';

    // 1) Globale — sempre.
    include $dir . '/partial-organization.php';

    // 2) Specifico al template corrente.
    if (is_singular('avvocato')) {
        include $dir . '/partial-attorney.php';
    } elseif (is_singular('competenza')) {
        if (function_exists('saltelli_count_faq') && saltelli_count_faq(get_the_ID()) >= 1) {
            include $dir . '/partial-faqpage.php';
        }
    } elseif (is_singular('post')) {
        include $dir . '/partial-article.php';
    } elseif (is_page('contatti')) {
        // WAVE3 TASK 6 — ContactPage scoped solo a /contatti/.
        include $dir . '/partial-contactpage.php';
    }

    // 3) Breadcrumb — ovunque tranne homepage.
    if (!is_front_page() && !is_404()) {
        include $dir . '/partial-breadcrumb.php';
    }
}
