<?php
/**
 * v1.0.0 CMS Recovery — CPT "fake repeater" per editor handoff.
 *
 * 8 CPT per Elena/Ludovica (modifica diretta WP-Admin senza ACF Pro repeater):
 *  - saltelli_faq          (+ taxonomy faq_topic)        cross-page FAQ items
 *  - saltelli_caso         (+ taxonomy caso_categoria)   /casi/, single-avvocato, single-competenza
 *  - saltelli_modalita     /costi/ § 01 — 3 modalità
 *  - saltelli_scenario     /costi/ § 02 — 3 scenari
 *  - saltelli_principio    /chi-siamo/, /avvocati/
 *  - saltelli_trust        /costi/ § 05 — 4 plates
 *  - saltelli_formazione   single-avvocato (sub-menu Avvocati)
 *  - saltelli_guida        /guide-gratuite/ (public, slug "guida")
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

add_action('init', function () {

    register_post_type('saltelli_faq', [
        'label' => __('FAQ', 'saltelli'),
        'labels' => [
            'name'          => __('Domande frequenti', 'saltelli'),
            'singular_name' => __('FAQ', 'saltelli'),
            'add_new_item'  => __('Aggiungi nuova FAQ', 'saltelli'),
            'edit_item'     => __('Modifica FAQ', 'saltelli'),
            'all_items'     => __('Tutte le FAQ', 'saltelli'),
        ],
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 25,
        'menu_icon'          => 'dashicons-format-status',
        'capability_type'    => 'post',
        'has_archive'        => false,
        'rewrite'            => false,
        'supports'           => ['title', 'page-attributes'],
        'show_in_rest'       => true,
    ]);

    // Wave 5: saltelli_caso passa public (URL /chi-siamo/risultati/{slug}/, B5.4 cliente)
    register_post_type('saltelli_caso', [
        'label' => __('Caso rappresentativo', 'saltelli'),
        'labels' => [
            'name'          => __('Casi rappresentativi', 'saltelli'),
            'singular_name' => __('Caso', 'saltelli'),
            'add_new_item'  => __('Aggiungi nuovo caso', 'saltelli'),
            'edit_item'     => __('Modifica caso', 'saltelli'),
            'all_items'     => __('Tutti i casi', 'saltelli'),
        ],
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 26,
        'menu_icon'          => 'dashicons-awards',
        'capability_type'    => 'post',
        'has_archive'        => 'chi-siamo/risultati',
        'rewrite'            => [
            'slug'       => 'chi-siamo/risultati',
            'with_front' => false,
            'feeds'      => false,
        ],
        'supports'           => ['title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes'],
        'show_in_rest'       => true,
    ]);

    register_post_type('saltelli_modalita', [
        'label' => __('Modalità consulenza', 'saltelli'),
        'labels' => [
            'name'          => __('Modalità consulenza', 'saltelli'),
            'singular_name' => __('Modalità', 'saltelli'),
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=page',
        'capability_type' => 'post',
        'has_archive'     => false,
        'rewrite'         => false,
        'supports'        => ['title', 'page-attributes'],
        'show_in_rest'    => false,
    ]);

    register_post_type('saltelli_scenario', [
        'label' => __('Scenario costi', 'saltelli'),
        'labels' => [
            'name'          => __('Scenari costi', 'saltelli'),
            'singular_name' => __('Scenario', 'saltelli'),
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=page',
        'capability_type' => 'post',
        'has_archive'     => false,
        'rewrite'         => false,
        'supports'        => ['title', 'page-attributes'],
    ]);

    register_post_type('saltelli_principio', [
        'label' => __('Principio studio', 'saltelli'),
        'labels' => [
            'name'          => __('Principi studio', 'saltelli'),
            'singular_name' => __('Principio', 'saltelli'),
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=page',
        'capability_type' => 'post',
        'has_archive'     => false,
        'rewrite'         => false,
        'supports'        => ['title', 'page-attributes'],
    ]);

    register_post_type('saltelli_trust', [
        'label' => __('Trust signal', 'saltelli'),
        'labels' => [
            'name'          => __('Trust signals', 'saltelli'),
            'singular_name' => __('Trust signal', 'saltelli'),
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=page',
        'capability_type' => 'post',
        'has_archive'     => false,
        'rewrite'         => false,
        'supports'        => ['title', 'page-attributes'],
    ]);

    register_post_type('saltelli_formazione', [
        'label' => __('Formazione', 'saltelli'),
        'labels' => [
            'name'          => __('Formazione & Titoli', 'saltelli'),
            'singular_name' => __('Formazione', 'saltelli'),
        ],
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=avvocato',
        'capability_type' => 'post',
        'has_archive'     => false,
        'rewrite'         => false,
        'supports'        => ['title', 'page-attributes'],
    ]);

    register_post_type('saltelli_guida', [
        'label' => __('Guida gratuita', 'saltelli'),
        'labels' => [
            'name'          => __('Guide gratuite', 'saltelli'),
            'singular_name' => __('Guida', 'saltelli'),
        ],
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 28,
        'menu_icon'          => 'dashicons-download',
        'capability_type'    => 'post',
        'has_archive'        => false,
        'rewrite'            => ['slug' => 'guida'],
        'supports'           => ['title', 'editor', 'page-attributes', 'thumbnail'],
        'show_in_rest'       => true,
    ]);

    register_taxonomy('faq_topic', 'saltelli_faq', [
        'label'             => __('FAQ Topic', 'saltelli'),
        'public'            => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'hierarchical'      => true,
        'rewrite'           => false,
    ]);

    register_taxonomy('caso_categoria', 'saltelli_caso', [
        'label'             => __('Casi categoria', 'saltelli'),
        'public'            => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'hierarchical'      => true,
        'rewrite'           => false,
    ]);
});
