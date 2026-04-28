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
    if (empty($row['domanda']) || empty($row['risposta'])) {
        continue;
    }
    $main_entity[] = [
        '@type' => 'Question',
        'name'  => trim((string) $row['domanda']),
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text'  => trim((string) $row['risposta']),
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
