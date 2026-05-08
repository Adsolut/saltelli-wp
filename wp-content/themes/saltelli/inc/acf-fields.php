<?php
/**
 * ACF bootstrap.
 *
 * Wave 1 v1: i field group sono in `acf-json/` (root del theme), path di
 * default ACF — nessun custom load/save filter necessario. Il plugin
 * (ACF/SCF) picka automaticamente al boot.
 *
 * Wave 4.7.fix (2026-05-08): switch da Advanced Custom Fields Free 6.8.0
 * a Secure Custom Fields 6.8.4 (fork Automattic, Q4 2024). Motivo: ACF
 * Free non include `acf_add_options_page()` (feature ACF Pro-only) →
 * silent no-op del menu Saltelli Settings. SCF è drop-in compatible API
 * e include options pages free. CMS Diagnosis Round 2 REPORT.md.
 *
 * I 17 field group Wave 1+ sono ACF/SCF compatible:
 *  - 10 CPT (Agent B): avvocato_v1, competenza_v1, *_item_v1
 *  - 5 page (Agent A):  costi_v1, casi_v1, contatti_v1, faq_v1, info_shared_v1
 *  - 1 options (Agent C): theme_options_v1
 *  - 1 page (post-Wave 1): lo_studio_v1
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

/**
 * Options page registration.
 *
 * SCF (Secure Custom Fields, Automattic fork) supporta `acf_add_options_page()`
 * come feature free. Pre-Wave 4.7.fix il theme girava su ACF Free 6.8.0 che
 * NON include questa funzione (Pro-only): il `function_exists()` guard
 * mascherava l'errore in fase di boot e il menu non veniva mai registrato.
 *
 * Il field group `group_theme_options_v1` (Agent C) si aggancia qui via
 * location `options_page == saltelli-settings`.
 */
add_action('init', function () {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title' => __('Impostazioni tema Saltelli', 'saltelli'),
            'menu_title' => __('Saltelli — Settings', 'saltelli'),
            'menu_slug'  => 'saltelli-settings',
            'capability' => 'manage_options',
            'redirect'   => false,
            'icon_url'   => 'dashicons-admin-customizer',
            'position'   => 60,
        ]);
    }
});

/**
 * Custom ACF location rule: page_slug == <slug>
 *
 * Debug-QA bug-04 fix (env-portable Field Group locations).
 * Le page IDs differiscono tra Docker locale e droplet staging — usare slug
 * invece di ID rende le Field Group location rules portable cross-env.
 *
 * Usage in acf-json/group_*.json:
 *   {"param":"page_slug","operator":"==","value":"faq"}
 *   {"param":"page_slug","operator":"!=","value":"chi-siamo"}
 *
 * @since 1.0.0 Debug-QA
 */
add_filter('acf/location/rule_types', function ($choices) {
    $choices[__('Page', 'acf')]['page_slug'] = __('Page Slug', 'saltelli');
    return $choices;
});

add_filter('acf/location/rule_values/page_slug', function ($choices) {
    // Auto-completare con tutti i page slug pubblicati (UI WP-Admin Field Group editor).
    $pages = get_posts([
        'post_type'   => 'page',
        'post_status' => ['publish', 'draft', 'private'],
        'numberposts' => -1,
        'fields'      => ['ID', 'post_name', 'post_title'],
        'orderby'     => 'title',
        'order'       => 'ASC',
    ]);
    $choices = [];
    foreach ($pages as $p) {
        if ($p->post_name !== '') {
            $choices[$p->post_name] = $p->post_title . ' (' . $p->post_name . ')';
        }
    }
    return $choices;
});

add_filter('acf/location/rule_match/page_slug', function ($match, $rule, $screen) {
    if (empty($screen['post_id'])) return false;
    $page = get_post($screen['post_id']);
    if (!$page || $page->post_type !== 'page') return false;
    $value = (string) ($rule['value'] ?? '');
    if ($rule['operator'] === '==') return $page->post_name === $value;
    if ($rule['operator'] === '!=') return $page->post_name !== $value;
    return false;
}, 10, 3);
