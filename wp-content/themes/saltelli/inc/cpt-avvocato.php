<?php
/**
 * Custom Post Type: avvocato
 *
 * Slug pubblico: /chi-siamo/team/{slug}/   (Wave 5 IA refactor)
 * Archive:       /chi-siamo/team/          (Wave 5 IA refactor)
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

add_action('init', 'saltelli_register_cpt_avvocato');
function saltelli_register_cpt_avvocato() {

    $labels = [
        'name'                  => _x('Avvocati', 'post type general name', 'saltelli'),
        'singular_name'         => _x('Avvocato', 'post type singular name', 'saltelli'),
        'menu_name'             => _x('Avvocati', 'admin menu', 'saltelli'),
        'name_admin_bar'        => _x('Avvocato', 'add new on admin bar', 'saltelli'),
        'add_new'               => _x('Aggiungi nuovo', 'avvocato', 'saltelli'),
        'add_new_item'          => __('Aggiungi nuovo avvocato', 'saltelli'),
        'new_item'              => __('Nuovo avvocato', 'saltelli'),
        'edit_item'             => __('Modifica avvocato', 'saltelli'),
        'view_item'             => __('Visualizza avvocato', 'saltelli'),
        'all_items'             => __('Tutti gli avvocati', 'saltelli'),
        'search_items'          => __('Cerca avvocati', 'saltelli'),
        'not_found'             => __('Nessun avvocato trovato.', 'saltelli'),
        'not_found_in_trash'    => __('Nessun avvocato nel cestino.', 'saltelli'),
        'archives'              => __('Archivio avvocati', 'saltelli'),
        'featured_image'        => __('Foto profilo', 'saltelli'),
        'set_featured_image'    => __('Imposta foto profilo', 'saltelli'),
        'remove_featured_image' => __('Rimuovi foto profilo', 'saltelli'),
        'use_featured_image'    => __('Usa come foto profilo', 'saltelli'),
    ];

    $args = [
        'labels'             => $labels,
        'description'        => __('Avvocati dello studio (CPT).', 'saltelli'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_nav_menus'  => true,
        'show_in_admin_bar'  => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => [
            'slug'       => 'chi-siamo/team',
            'with_front' => false,
            'feeds'      => false,
        ],
        'capability_type'    => 'post',
        'has_archive'        => 'chi-siamo/team',
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-businessperson',
        'supports'           => [
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'custom-fields',
            'page-attributes',
        ],
    ];

    register_post_type('avvocato', $args);
}
