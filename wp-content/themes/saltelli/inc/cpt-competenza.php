<?php
/**
 * Custom Post Type: competenza
 * Tassonomia:      tipo-area (gerarchica, slug /tipo/)
 *
 * Slug pubblico: /competenze/{slug}/
 * Archive:       /competenze/
 *
 * Termini consigliati per tipo-area (NON creati qui — solo documentati):
 *   civile, penale, tributario, lavoro, famiglia, amministrativo,
 *   commerciale, immobiliare.
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

add_action('init', 'saltelli_register_cpt_competenza');
function saltelli_register_cpt_competenza() {

    // ----- CPT competenza -----
    $labels = [
        'name'                  => _x('Competenze', 'post type general name', 'saltelli'),
        'singular_name'         => _x('Competenza', 'post type singular name', 'saltelli'),
        'menu_name'             => _x('Competenze', 'admin menu', 'saltelli'),
        'name_admin_bar'        => _x('Competenza', 'add new on admin bar', 'saltelli'),
        'add_new'               => _x('Aggiungi nuova', 'competenza', 'saltelli'),
        'add_new_item'          => __('Aggiungi nuova area di competenza', 'saltelli'),
        'new_item'              => __('Nuova area di competenza', 'saltelli'),
        'edit_item'             => __('Modifica area di competenza', 'saltelli'),
        'view_item'             => __('Visualizza area di competenza', 'saltelli'),
        'all_items'             => __('Tutte le competenze', 'saltelli'),
        'search_items'          => __('Cerca competenze', 'saltelli'),
        'not_found'             => __('Nessuna competenza trovata.', 'saltelli'),
        'not_found_in_trash'    => __('Nessuna competenza nel cestino.', 'saltelli'),
        'archives'              => __('Archivio competenze', 'saltelli'),
        'featured_image'        => __('Immagine area', 'saltelli'),
        'set_featured_image'    => __('Imposta immagine area', 'saltelli'),
        'remove_featured_image' => __('Rimuovi immagine area', 'saltelli'),
        'use_featured_image'    => __('Usa come immagine area', 'saltelli'),
    ];

    $args = [
        'labels'             => $labels,
        'description'        => __('Aree di pratica legale (CPT).', 'saltelli'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_nav_menus'  => true,
        'show_in_admin_bar'  => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => [
            'slug'       => 'competenze',
            'with_front' => false,
            'feeds'      => false,
        ],
        'capability_type'    => 'post',
        'has_archive'        => 'competenze',
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => [
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'custom-fields',
            'page-attributes',
        ],
    ];

    register_post_type('competenza', $args);

    // ----- Tassonomia: tipo-area -----
    $tax_labels = [
        'name'              => _x('Tipi di area', 'taxonomy general name', 'saltelli'),
        'singular_name'     => _x('Tipo di area', 'taxonomy singular name', 'saltelli'),
        'search_items'      => __('Cerca tipo di area', 'saltelli'),
        'all_items'         => __('Tutti i tipi di area', 'saltelli'),
        'parent_item'       => __('Tipo di area padre', 'saltelli'),
        'parent_item_colon' => __('Tipo di area padre:', 'saltelli'),
        'edit_item'         => __('Modifica tipo di area', 'saltelli'),
        'update_item'       => __('Aggiorna tipo di area', 'saltelli'),
        'add_new_item'      => __('Aggiungi nuovo tipo di area', 'saltelli'),
        'new_item_name'     => __('Nome nuovo tipo di area', 'saltelli'),
        'menu_name'         => __('Tipi di area', 'saltelli'),
    ];

    register_taxonomy('tipo-area', ['competenza'], [
        'labels'            => $tax_labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => [
            'slug'         => 'tipo',
            'with_front'   => false,
            'hierarchical' => true,
        ],
    ]);
}
