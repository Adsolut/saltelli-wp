<?php
/**
 * Schema partial — Article (single post blog).
 *
 * Replica geo-assets/schema/05-article-template.json.
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

if (!is_singular('post')) {
    return;
}

// Coabitazione: Yoast / Rank Math / AIOSEO emettono Article nel proprio
// @graph (con WebPage isPartOf). Evitiamo duplicati: skippiamo il nostro
// Article quando un plugin SEO è attivo. FAQPage e Person/Attorney NON
// vengono emessi dai plugin, quindi quei partial restano sempre attivi.
if (function_exists('saltelli_seo_plugin_active') && saltelli_seo_plugin_active()) {
    return;
}

$post_id = get_the_ID();
$url     = get_permalink($post_id);
$title   = get_the_title($post_id);
$excerpt = wp_strip_all_tags(get_the_excerpt($post_id));

// Image: featured image (ridimensionata 1200x630 — saltelli-card è 800x500 ma
// per OG/Article serve >= 1200x630, usiamo "full" se non c'è size 1200).
$image = [];
if (has_post_thumbnail($post_id)) {
    $img_id  = get_post_thumbnail_id($post_id);
    $img_src = wp_get_attachment_image_src($img_id, 'full');
    if ($img_src) {
        $image[] = [
            '@type'  => 'ImageObject',
            'url'    => $img_src[0],
            'width'  => $img_src[1],
            'height' => $img_src[2],
        ];
    }
}

// Author: opzionalmente collegato a un avvocato CPT via ACF "avvocato_autore".
// Se mancante, fallback a Organization come da geo-assets/schema 05.
$author_node = ['@id' => home_url('/#organization')];
$avvocato_autore = saltelli_field('avvocato_autore', $post_id, null);
if ($avvocato_autore) {
    $av_id = is_array($avvocato_autore) ? ($avvocato_autore['ID'] ?? 0) : (int) $avvocato_autore;
    if ($av_id && get_post_type($av_id) === 'avvocato') {
        $author_node = [
            '@type' => 'Person',
            '@id'   => get_permalink($av_id) . '#person',
            'name'  => get_the_title($av_id),
            'url'   => get_permalink($av_id),
        ];
    }
}

// Categoria primaria → articleSection.
$cats = get_the_category($post_id);
$primary_cat = !empty($cats) ? $cats[0]->name : '';

// Keywords da tag.
$tags = get_the_tags($post_id);
$keywords = '';
if (!empty($tags) && !is_wp_error($tags)) {
    $keywords = implode(', ', wp_list_pluck($tags, 'name'));
}

// Word count — non strettamente necessario, ma indicato nel template.
$content = get_post_field('post_content', $post_id);
$word_count = str_word_count(wp_strip_all_tags($content));

$schema = [
    '@context'         => 'https://schema.org',
    '@type'            => 'Article',
    '@id'              => $url . '#article',
    'url'              => $url,
    'headline'         => mb_substr($title, 0, 110),
    'description'      => mb_substr($excerpt, 0, 160),
    'datePublished'    => get_the_date('c', $post_id),
    'dateModified'     => get_the_modified_date('c', $post_id),
    'author'           => $author_node,
    'publisher'        => ['@id' => home_url('/#organization')],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id'   => $url,
    ],
    'inLanguage'           => 'it-IT',
    'isAccessibleForFree'  => true,
    'wordCount'            => $word_count,
];

if (!empty($image)) {
    $schema['image'] = $image;
}
if ($primary_cat !== '') {
    $schema['articleSection'] = $primary_cat;
}
if ($keywords !== '') {
    $schema['keywords'] = $keywords;
}

saltelli_emit_jsonld($schema);
