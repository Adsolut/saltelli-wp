<?php
/**
 * Schema partial — BreadcrumbList.
 *
 * Replica geo-assets/schema/04-breadcrumblist-template.json.
 * Genera dinamicamente itemListElement[] usando saltelli_get_breadcrumb_chain().
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

if (is_front_page()) {
    return;
}

$chain = saltelli_get_breadcrumb_chain();
if (empty($chain) || count($chain) < 2) {
    return;
}

$items = [];
$position = 1;
foreach ($chain as $node) {
    $item = [
        '@type'    => 'ListItem',
        'position' => $position,
        'name'     => $node['name'],
    ];
    if (!empty($node['url'])) {
        $item['item'] = $node['url'];
    }
    $items[] = $item;
    $position++;
}

$current_url = saltelli_canonical_url();

$schema = [
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    '@id'      => $current_url . '#breadcrumb',
    'itemListElement' => $items,
];

saltelli_emit_jsonld($schema);
