<?php
/**
 * ACF bootstrap.
 *
 * Wave 1 v1: i field group sono in `acf-json/` (root del theme), path di
 * default ACF — nessun custom load/save filter necessario. ACF Free 6.8.0
 * picka automaticamente al boot. Sostituisce il setup precedente che
 * usava `inc/acf-json/` con field group placeholder a base repeater
 * (richiedevano ACF Pro mai installato).
 *
 * I 16 field group Wave 1 sono ACF Free compatible:
 *  - 10 CPT (Agent B): avvocato_v1, competenza_v1, *_item_v1
 *  - 5 page (Agent A):  costi_v1, casi_v1, contatti_v1, faq_v1, info_shared_v1
 *  - 1 options (Agent C): theme_options_v1
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

/**
 * Options page registration.
 *
 * ACF Free supporta `acf_add_options_page()` (no Pro requirement).
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
