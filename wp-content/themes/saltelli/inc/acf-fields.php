<?php
/**
 * ACF bootstrap.
 *
 * I field group sono in `inc/acf-json/`. ACF (Free o Pro) li picka
 * automaticamente. Per il repeater FAQ (e per `casi_rappresentativi`)
 * serve ACF Pro.
 *
 * Se ACF non è ancora installato, questo file è inerte: i filter non
 * vengono mai chiamati. Quando ACF arriva, il path è già pronto.
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

/**
 * Where ACF should SAVE field groups when modified in the UI.
 * (Save next to load, so the repo holds the source of truth.)
 */
add_filter('acf/settings/save_json', function () {
    return SALTELLI_THEME_DIR . '/inc/acf-json';
});

/**
 * Where ACF should LOAD field groups from.
 * Restituiamo un array di path: il default + il nostro.
 */
add_filter('acf/settings/load_json', function ($paths) {
    // Rimuove il path di default (opzionale: lo lasciamo per retro-compat).
    // unset($paths[0]);
    $paths[] = SALTELLI_THEME_DIR . '/inc/acf-json';
    return $paths;
});

/**
 * Options page (ACF Pro only). Skeleton — Settings tema globali.
 *
 * Quando ACF Pro è attivo, registriamo una pagina opzioni "Saltelli"
 * a cui agganciare il field group `group_settings.json`.
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
    // TODO: se ACF Free, il group_settings agirà solo come scheletro
    // finché non si decide se rendere ACF Pro requisito hard.
});
