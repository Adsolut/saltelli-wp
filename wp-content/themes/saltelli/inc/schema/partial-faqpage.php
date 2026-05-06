<?php
/**
 * Schema partial — FAQPage.
 *
 * Replica geo-assets/schema/03-faqpage-example-tributario.json applicato
 * dinamicamente alla competenza corrente. Loop su ACF repeater "faq".
 * Skip se 0 FAQ valide (lo loader controlla a monte, ma teniamo il guard).
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

if (!is_singular('competenza')) {
    return;
}

$post_id = get_the_ID();
$faq_raw = saltelli_field('faq', $post_id, []);
if (!is_array($faq_raw) || empty($faq_raw)) {
    return;
}

$main_entity = [];
foreach ($faq_raw as $row) {
    /* Wave 6 — supporta sia il pattern legacy fake-repeater (row['domanda'/'risposta'])
     * sia il pattern Wave 1+ post_object (relationship verso saltelli_faq CPT,
     * dove title = domanda e ACF field 'risposta' = risposta WYSIWYG). */
    $domanda = '';
    $risposta = '';
    if (is_array($row)) {
        $domanda  = isset($row['domanda'])  ? (string) $row['domanda']  : '';
        $risposta = isset($row['risposta']) ? (string) $row['risposta'] : '';
    } elseif (is_object($row) && isset($row->ID)) {
        $faq_id   = (int) $row->ID;
        $domanda  = get_the_title($faq_id);
        $risposta = (string) saltelli_field('risposta', $faq_id, '');
    } elseif (is_numeric($row) && (int) $row > 0) {
        $faq_id   = (int) $row;
        $domanda  = get_the_title($faq_id);
        $risposta = (string) saltelli_field('risposta', $faq_id, '');
    }
    $domanda  = trim(wp_strip_all_tags($domanda));
    $risposta = trim(wp_strip_all_tags($risposta));
    if ($domanda === '' || $risposta === '') {
        continue;
    }
    $main_entity[] = [
        '@type' => 'Question',
        'name'  => $domanda,
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text'  => $risposta,
        ],
    ];
}

if (empty($main_entity)) {
    return;
}

$url = get_permalink($post_id);

$schema = [
    '@context'  => 'https://schema.org',
    '@type'     => 'FAQPage',
    '@id'       => $url . '#faq',
    'url'       => $url,
    'inLanguage' => 'it-IT',
    'isPartOf'  => ['@id' => $url],
    'about'     => [
        '@type' => 'Thing',
        'name'  => get_the_title($post_id),
    ],
    'mainEntity' => $main_entity,
];

saltelli_emit_jsonld($schema);
