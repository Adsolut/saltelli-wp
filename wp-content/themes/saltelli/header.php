<?php
/**
 * Header — site shell + <head> + sticky header con click-to-call e mobile menu.
 *
 * @package Saltelli
 */
$saltelli_phone_label = saltelli_option('contact_telefono_pubblico', '+39 081 1813 1119');
$saltelli_phone_e164  = saltelli_studio_phone_e164();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1B2B4B">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class('sl-root'); ?>>

<?php wp_body_open(); ?>

<a class="sl-skip-link" href="#main"><?php esc_html_e('Vai al contenuto', 'saltelli'); ?></a>

<header class="sl-header" data-scrolled="false" role="banner">
    <div class="sl-header__inner sl-container">

        <a class="sl-header__brand" href="<?php echo esc_url(home_url('/')); ?>" rel="home" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
            <span class="sl-header__brand-name">Saltelli &amp; Partners</span>
            <span class="sl-header__brand-sub sl-mono">Studio Legale · Napoli</span>
        </a>

        <nav class="sl-header__nav" aria-label="<?php esc_attr_e('Menu principale', 'saltelli'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'sl-header__menu',
                'fallback_cb'    => 'saltelli_header_menu_fallback',
                'depth'          => 2,
            ]);
            ?>
        </nav>

        <a class="sl-header__phone sl-mono" href="tel:<?php echo esc_attr($saltelli_phone_e164); ?>">
            <?php echo esc_html($saltelli_phone_label); ?>
        </a>

        <button class="sl-header__burger" type="button" aria-label="<?php esc_attr_e('Apri menu', 'saltelli'); ?>" aria-expanded="false" aria-controls="sl-mobile-menu">
            <span class="sl-header__burger-line"></span>
            <span class="sl-header__burger-line"></span>
        </button>

    </div>

    <div class="sl-header__mobile" id="sl-mobile-menu" hidden>
        <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'sl-header__mobile-menu',
            'fallback_cb'    => 'saltelli_header_menu_fallback',
            'depth'          => 1,
        ]);
        ?>
        <div class="sl-header__mobile-foot">
            <a class="sl-mono" href="tel:<?php echo esc_attr($saltelli_phone_e164); ?>"><?php echo esc_html($saltelli_phone_label); ?></a>
        </div>
    </div>
</header>

<script>
(function(){
    var h = document.querySelector('.sl-header');
    if (!h) return;
    var update = function(){ h.setAttribute('data-scrolled', window.scrollY > 80 ? 'true' : 'false'); };
    update();
    window.addEventListener('scroll', update, { passive: true });
    var b = h.querySelector('.sl-header__burger');
    var m = h.querySelector('.sl-header__mobile');
    if (b && m) {
        b.addEventListener('click', function(){
            var open = b.getAttribute('aria-expanded') === 'true';
            b.setAttribute('aria-expanded', open ? 'false' : 'true');
            if (open) { m.setAttribute('hidden', ''); } else { m.removeAttribute('hidden'); }
            document.documentElement.classList.toggle('sl-menu-open', !open);
        });
    }
})();
</script>

<main id="main" class="sl-main" role="main">
