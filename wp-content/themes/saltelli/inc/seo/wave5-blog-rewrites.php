<?php
/**
 * Wave 5 IA Refactor — rewrite rules per /risorse/blog/* schema.
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
