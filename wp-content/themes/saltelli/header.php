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

        <a class="sl-header__brand sl-logo--horizontal" href="<?php echo esc_url(home_url('/')); ?>" rel="home" aria-label="Studio Legale Saltelli — Home">
            <span class="sl-logo__h-left">
                <span class="sl-logo__h-top">Studio Legale</span>
                <span class="sl-logo__h-bot">Napoli · 1999</span>
            </span>
            <span class="sl-logo__h-rule" aria-hidden="true"></span>
            <span class="sl-logo__h-name"><span class="sl-logo__swash">S</span>altelli</span>
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

<a class="sl-whatsapp-sticky"
   href="https://wa.me/<?php echo esc_attr(ltrim($saltelli_phone_e164, '+')); ?>?text=<?php echo rawurlencode('Ciao, vorrei una consulenza presso lo Studio Legale Saltelli & Partners.'); ?>"
   target="_blank"
   rel="noopener"
   aria-label="<?php esc_attr_e('Contatta lo studio su WhatsApp', 'saltelli'); ?>">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="currentColor" aria-hidden="true">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/>
    </svg>
    <span class="sl-whatsapp-sticky__label sl-mono"><?php esc_html_e('WhatsApp', 'saltelli'); ?></span>
</a>

<main id="main" class="sl-main" role="main">
