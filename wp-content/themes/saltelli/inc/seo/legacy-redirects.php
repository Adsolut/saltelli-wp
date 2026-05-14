<?php
/**
 * Saltelli — Legacy URL Redirect 301 (Wave 5 IA Refactor extension)
 *
 * Mappa unificata di redirect 301 con 3 stati URL:
 *
 *   Stato A — URL legacy Elementor (sito production pre-2026)
 *   Stato B — URL MVP recovery (Wave 0-3, /competenze/, /avvocati/, /faq/, /casi/, ecc.)
 *   Stato C — URL audit-aligned (Wave 5 target, /aree-di-pratica/{cluster}/, /chi-siamo/team/, ecc.)
 *
 * Funzioni:
 *   - saltelli_legacy_redirect_map()      → A → C  (legacy Elementor → audit-aligned)
 *   - saltelli_mvp_to_audit_redirect_map() → B → C  (MVP corrente → audit-aligned, CAL-03/04)
 *   - saltelli_legacy_redirect()          → applica entrambe le mappe + pattern dinamici
 *
 * Hook: init priority 1 (CAL-04 — esteso, NON aggiunto secondo hook su template_redirect).
 *
 * @package Saltelli
 * @since 0.13.0 IA Unification (legacy → MVP)
 * @since 1.1.0  Wave 5 IA Refactor (legacy → audit, MVP → audit, dynamic regex)
 */

defined('ABSPATH') || exit;

if (!function_exists('saltelli_legacy_redirect_map')) :
/**
 * Mappa A → C: legacy Elementor URL (pre-2026) → audit-aligned URL (Wave 5).
 *
 * Aggiornata Wave 5 con schema `/aree-di-pratica/{cluster}/{slug}/`.
 * Cluster source: cluster-mapping-17-areas.csv (DEC-021 cliente-firmato).
 */
function saltelli_legacy_redirect_map() {
    return [
        // CPT competenza — slug verificati su DB CPT publish post-Wave5
        '/recupero-crediti/'                        => '/aree-di-pratica/imprese/recupero-crediti/',
        '/cartelle-esattoriali-e-multe/'            => '/aree-di-pratica/privati/cartelle-esattoriali-e-multe/',
        '/diritto-bancario/'                        => '/aree-di-pratica/privati/diritto-bancario/',
        '/avvocato-divorzista/'                     => '/aree-di-pratica/privati/diritto-di-famiglia/',
        '/avvocato-divorzista-italia/'              => '/aree-di-pratica/privati/diritto-di-famiglia/',
        '/lavoro/'                                  => '/aree-di-pratica/privati/diritto-del-lavoro/',
        '/eredita-e-successioni/'                   => '/aree-di-pratica/privati/diritto-delle-successioni/',
        '/condominio-e-locazioni/'                  => '/aree-di-pratica/privati/diritto-condominiale/',
        '/responsabilita-medica/'                   => '/aree-di-pratica/privati/responsabilita-medica/',
        '/immigrazione/'                            => '/aree-di-pratica/privati/diritto-dellimmigrazione/',
        '/infortunistica-stradale/'                 => '/aree-di-pratica/privati/infortunistica-stradale/', // CAL-03 — nuova competenza autonoma
        '/infortunistica-stradale-italia/'          => '/aree-di-pratica/privati/infortunistica-stradale/', // CAL-03
        '/risarcimento-del-danno/'                  => '/aree-di-pratica/privati/risarcimento-danni/',
        '/diritto-tributario/'                      => '/aree-di-pratica/privati/diritto-tributario/',
        '/ricorsi-napoli-obiettivo-valore/'         => '/aree-di-pratica/privati/cartelle-esattoriali-e-multe/',
        '/invalidita-civile-diritto-previdenziale/' => '/aree-di-pratica/privati/diritto-previdenziale/',
        '/diritto-amministrativo/'                  => '/aree-di-pratica/contenzioso-amministrativo/diritto-amministrativo/',
        '/diritto-penale/'                          => '/aree-di-pratica/privati/diritto-penale/',
        // NB: slug CPT è "domiciliazione-dimpresa" (con d apostrofata, no spazio)
        '/domicilia-la-tua-azienda/'                => '/aree-di-pratica/imprese/domiciliazione-dimpresa/',

        // Pages legacy senza CPT corrispondente → archive aree-di-pratica
        '/diritto-societario/'                      => '/aree-di-pratica/',
        '/contrattualistica/'                       => '/aree-di-pratica/',
        '/aste-immobiliari/'                        => '/aree-di-pratica/privati/aste-immobiliari/', // CAL-03 — nuova competenza autonoma
        '/servizi-legali/'                          => '/aree-di-pratica/',

        // Funnel utility legacy → contatti
        '/prenota-un-appuntamento/'                 => '/contatti/',

        // Wave P7 consolidamento: /lo-studio/ e /chi-siamo/lo-studio/ → /chi-siamo/
        // (Page 2811 ex "Lo Studio" rinominata slug `chi-siamo`, Page 2822 hub cancellata).
        '/lo-studio/'                               => '/chi-siamo/',
        '/chi-siamo/lo-studio/'                     => '/chi-siamo/',
    ];
}
endif;

if (!function_exists('saltelli_mvp_to_audit_redirect_map')) :
/**
 * Mappa B → C: URL MVP corrente (Wave 0-3) → audit-aligned URL (Wave 5).
 *
 * NUOVA Wave 5. Copertura URL del MVP recovery che non esistono nel pre-2026
 * Elementor ma sono stati introdotti durante Wave 0-3 e ora vanno migrati.
 */
function saltelli_mvp_to_audit_redirect_map() {
    return [
        // Sezioni hub rinomina (statici)
        '/avvocati/'                              => '/chi-siamo/team/',
        '/competenze/'                            => '/aree-di-pratica/',
        // Wave 4.7.fix.2: rename slug `risultati` → `casi-rappresentativi`
        // (CPT saltelli_caso has_archive + rewrite slug). Old URL ridireziona,
        // single-caso URLs gestiti dal pattern regex Step 5 sotto.
        '/casi/'                                  => '/chi-siamo/casi-rappresentativi/',
        '/chi-siamo/risultati/'                   => '/chi-siamo/casi-rappresentativi/',
        // Wave 4.7.fix.5: Page WP 2699 `risultati` (child di 2811 lo-studio) trashed.
        // Il suo permalink reale era /chi-siamo/lo-studio/risultati/ (nested sotto
        // lo-studio), non /chi-siamo/risultati/ — quindi serve un'entry dedicata.
        '/chi-siamo/lo-studio/risultati/'         => '/chi-siamo/casi-rappresentativi/',
        '/blog/'                                  => '/risorse/blog/',
        '/faq/'                                   => '/risorse/domande-frequenti/',
        '/glossario-legale/'                      => '/risorse/glossario-legale/',
        '/guide-gratuite/'                        => '/risorse/guide-gratuite/',
        '/come-lavoriamo/'                        => '/costi-e-consulenze/come-lavoriamo/',
        '/prima-consulenza/'                      => '/costi-e-consulenze/prima-consulenza/',
        '/richiedi-preventivo/'                   => '/costi-e-consulenze/richiedi-preventivo/',
        '/lavora-con-noi/'                        => '/contatti/lavora-con-noi/',
        '/costi/'                                 => '/costi-e-consulenze/',
        '/tipo-area/privati/'                     => '/aree-di-pratica/privati/',
        '/tipo-area/imprese/'                     => '/aree-di-pratica/imprese/',
        '/tipo-area/contenzioso-amministrativo/'  => '/aree-di-pratica/contenzioso-amministrativo/',
        '/tipo-area/contenzioso/'                 => '/aree-di-pratica/contenzioso-amministrativo/', // pre-Phase 2 slug

        // 4 PENDING DELETE (DEC-021) — backlink esterni storici → archive
        '/competenze/assicurazioni/'              => '/aree-di-pratica/',
        '/competenze/diritto-delle-assicurazioni/' => '/aree-di-pratica/', // slug REALE che esisteva nel DB MVP
        '/competenze/responsabilita-civile/'      => '/aree-di-pratica/',
        '/competenze/consulenze-online/'          => '/aree-di-pratica/',
        '/competenze/diritto-commerciale/'        => '/aree-di-pratica/',

        // DISCOVERY-01 consolidamento (Wave 5 mini-fix BLOCKER B, 2026-05-06):
        // Post 2669 `diritto-di-famiglia` (NO LGBTQ+) eliminato per consolidamento
        // con sibling 2666 `diritto-di-famiglia-lgbtq` (DEC-021 cliente-firmato).
        '/aree-di-pratica/privati/diritto-di-famiglia/' => '/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/',

        // Elena fix 2026-05-14 — GSC audit recovery 4 URL 404 con backlink Google:
        // a) Typo apostrofo /bonifico-ai-figli .../l-agenzia .../ (38 impr Google,
        //    URL mai esistito ma indicizzato per backlink errato): redirect a
        //    versione corretta post 1573 senza dash.
        '/bonifico-ai-figli-i-limiti-da-non-superare-per-evitare-problemi-con-l-agenzia-delle-entrate/' => '/bonifico-ai-figli-i-limiti-da-non-superare-per-evitare-problemi-con-lagenzia-delle-entrate/',
        // b) /donazione-ai-figli/ (38 impr, articolo mai migrato e non esistente
        //    nemmeno sul vecchio sito): topic match all'articolo bonifico-ai-figli
        //    (stesso tema "donazioni/bonifici figli vs agenzia entrate").
        '/donazione-ai-figli-i-limiti-da-non-superare-per-evitare-problemi-con-l-agenzia-delle-entrate/' => '/bonifico-ai-figli-i-limiti-da-non-superare-per-evitare-problemi-con-lagenzia-delle-entrate/',
    ];
}
endif;

if (!function_exists('saltelli_legacy_redirect')) :
/**
 * Esegue redirect 301 in 4 step:
 *   Step 1 — legacy Elementor → audit-aligned (mappa A → C)
 *   Step 2 — MVP corrente → audit-aligned (mappa B → C)
 *   Step 3 — pattern dynamic /competenze/{slug}/ → permalink CPT (post Phase 3 rewrite)
 *   Step 4 — pattern dynamic /avvocati/{slug}/, /blog/{...}, /category|tag|author/{...}
 */
function saltelli_legacy_redirect() {
    if (is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) return;
    if (defined('WP_CLI') && WP_CLI) return;

    $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
    $path = (string) parse_url($request_uri, PHP_URL_PATH);

    if ($path === '' || $path === '/') return;
    if (substr($path, -1) !== '/') {
        $path .= '/';
    }

    // Step 1 — Legacy Elementor → audit-aligned
    $legacy_map = saltelli_legacy_redirect_map();
    if (isset($legacy_map[$path])) {
        wp_safe_redirect(home_url($legacy_map[$path]), 301);
        exit;
    }

    // Step 2 — MVP corrente → audit-aligned (CAL-03/04)
    $mvp_map = saltelli_mvp_to_audit_redirect_map();
    if (isset($mvp_map[$path])) {
        wp_safe_redirect(home_url($mvp_map[$path]), 301);
        exit;
    }

    // Step 3 — Pattern dynamic per /competenze/{slug}/ — risolve permalink CPT corrente
    if (preg_match('#^/competenze/([^/]+)/?$#', $path, $matches)) {
        $slug = $matches[1];
        $post = get_page_by_path($slug, OBJECT, 'competenza');
        if ($post) {
            wp_safe_redirect(get_permalink($post->ID), 301);
            exit;
        }
    }

    // Step 4 — Pattern dynamic post-IA Refactor
    // /avvocati/{slug}/ → /chi-siamo/team/{slug}/
    if (preg_match('#^/avvocati/([^/]+)/?$#', $path, $matches)) {
        wp_safe_redirect(home_url("/chi-siamo/team/{$matches[1]}/"), 301);
        exit;
    }
    // /blog/{slug-or-path}/ → /risorse/blog/{slug-or-path}/
    if (preg_match('#^/blog/(.+)$#', $path, $matches)) {
        wp_safe_redirect(home_url("/risorse/blog/{$matches[1]}"), 301);
        exit;
    }
    // /category|tag|author/{path}/ → /risorse/blog/category|tag|author/{path}/
    if (preg_match('#^/(category|tag|author)/(.+)$#', $path, $matches)) {
        wp_safe_redirect(home_url("/risorse/blog/{$matches[1]}/{$matches[2]}"), 301);
        exit;
    }

    // Step 5 — Wave 4.7.fix.2: rename `risultati` → `casi-rappresentativi`
    // /chi-siamo/risultati/{slug}/ → /chi-siamo/casi-rappresentativi/{slug}/
    // (single-caso URLs; archive root path già gestito Step 2 via mvp_map).
    if (preg_match('#^/chi-siamo/risultati/(.+)$#', $path, $matches)) {
        wp_safe_redirect(home_url("/chi-siamo/casi-rappresentativi/{$matches[1]}"), 301);
        exit;
    }
}
endif;

// Hook su `init` priority 1 — early intercept prima di canonical redirect WP.
// CAL-04: NON aggiungere secondo hook su template_redirect.
add_action('init', 'saltelli_legacy_redirect', 1);
