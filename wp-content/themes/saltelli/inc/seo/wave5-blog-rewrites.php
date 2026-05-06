<?php
/**
 * Wave 5 IA Refactor — rewrite rules + query var filter per /risorse/blog/* schema.
 *
 * Permalink struct globale è /%postname%/, quindi i blog post vivono al top-level
 * (es. /dividere-la-casa-familiare/). L'audit-aligned schema vuole invece
 * /risorse/blog/{slug}/ — questo richiede rewrite rules custom (modificare la
 * permalink struct globale rompeva CPT singoli e l'IA generale).
 *
 * Rules:
 *   /risorse/blog/{slug}/         → single post   (post_type=post, name=slug)
 *   /risorse/blog/category/{cat}/ → category archive
 *   /risorse/blog/tag/{tag}/      → tag archive
 *   /risorse/blog/author/{user}/  → author archive
 *
 * Note: /risorse/blog/ resta la page WP (ID page Blog, parent risorse).
 *
 * FIX (audit Wave 5 BLOCKER A — 2026-05-06):
 *   Le sole rewrite rules sono fragili: la page resolution standard di WP
 *   può ombrare il rule custom risolvendo /risorse/blog/{slug}/ come nested
 *   page (pagename=risorse/blog/{slug}) e ritornare 404. Il filter `request`
 *   priority 5 intercetta query_vars PRIMA del page resolution e forza la
 *   single-post resolution quando lo slug corrisponde a un post pubblicato.
 *
 * @package Saltelli
 * @since 1.1.0 Wave 5
 */

defined('ABSPATH') || exit;

add_action('init', function () {
    // /risorse/blog/category/{cat}/
    add_rewrite_rule(
        '^risorse/blog/category/([^/]+)/?$',
        'index.php?category_name=$matches[1]',
        'top'
    );
    // /risorse/blog/tag/{tag}/
    add_rewrite_rule(
        '^risorse/blog/tag/([^/]+)/?$',
        'index.php?tag=$matches[1]',
        'top'
    );
    // /risorse/blog/author/{user}/
    add_rewrite_rule(
        '^risorse/blog/author/([^/]+)/?$',
        'index.php?author_name=$matches[1]',
        'top'
    );
    // /risorse/blog/{slug}/  (single post — last to allow specific patterns above to win)
    add_rewrite_rule(
        '^risorse/blog/([^/]+)/?$',
        'index.php?name=$matches[1]&post_type=post',
        'top'
    );
}, 11);

/**
 * FIX BLOCKER A: filter `request` priority 5 — intercetta /risorse/blog/{slug}/
 * PRIMA del page resolution standard, sostituendo pagename con name + post_type
 * per forzare single-post resolution quando il rewrite rule non basta.
 *
 * Skippa sub-archive (category/tag/author): lascia il rewrite rule risolvere.
 * Skippa slug inesistenti: lascia che WP faccia 404 naturale.
 */
add_filter('request', 'saltelli_resolve_blog_post_request', 5);
function saltelli_resolve_blog_post_request($query_vars) {
    if (empty($query_vars['pagename'])) {
        return $query_vars;
    }

    $pagename = $query_vars['pagename'];

    if (!preg_match('#^risorse/blog/([^/]+)/?$#', $pagename, $matches)) {
        return $query_vars;
    }

    $slug = $matches[1];

    if (in_array($slug, ['category', 'tag', 'author'], true)) {
        return $query_vars;
    }

    $post = get_page_by_path($slug, OBJECT, 'post');

    if ($post) {
        unset($query_vars['pagename']);
        $query_vars['name'] = $slug;
        $query_vars['post_type'] = 'post';
    }

    return $query_vars;
}
