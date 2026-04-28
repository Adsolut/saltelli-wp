<?php
/**
 * Header — site shell + <head>.
 *
 * @package Saltelli
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1B2B4B">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e('Vai al contenuto', 'saltelli'); ?></a>

<header class="site-header" role="banner">
    <div class="container">

        <!-- TODO Style & Animation agent: header layout (logo + nav primary + CTA contatti) -->
        <a class="site-branding" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
            <?php
            if (function_exists('the_custom_logo') && has_custom_logo()) {
                the_custom_logo();
            } else {
                bloginfo('name');
            }
            ?>
        </a>

        <nav class="site-nav" aria-label="<?php esc_attr_e('Menu principale', 'saltelli'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'menu menu--primary',
                'fallback_cb'    => '__return_false',
                'depth'          => 2,
            ]);
            ?>
        </nav>

    </div>
</header>

<main id="main" class="site-main" role="main">
