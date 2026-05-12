<?php
/**
 * Header — site shell + <head> + sticky header con click-to-call e mobile menu.
 *
 * @package Saltelli
 */
/* Wave 4.6: legge da studio_telefono_pubblico (Wave 1 schema) con default ACF.
   Il dead alias contact_telefono_pubblico è rimosso (studio_* ha default ACF +39 081 1813 1119). */
$saltelli_phone_label = saltelli_option('studio_telefono_pubblico', '+39 081 1813 1119');
$saltelli_phone_e164  = saltelli_studio_phone_e164();
$saltelli_brand_payoff = (string) saltelli_option('brand_payoff', 'Diritto, con misura');
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
            <?php /* Wave 4.6: brand_payoff editabile via Theme Options. Hidden by default
                    (designer + editor possono renderlo visibile via CSS override). */ ?>
            <?php if ($saltelli_brand_payoff !== '') : ?>
                <span class="sl-brand__payoff" hidden><?php echo esc_html($saltelli_brand_payoff); ?></span>
            <?php endif; ?>
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

    <div class="sl-header__mobile-backdrop" aria-hidden="true" hidden></div>
    <div class="sl-header__mobile" id="sl-mobile-menu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Menu di navigazione', 'saltelli'); ?>" hidden>
        <div class="sl-header__mobile-bar">
            <span class="sl-header__mobile-eyebrow sl-mono"><?php esc_html_e('Menu', 'saltelli'); ?></span>
            <button class="sl-header__mobile-close" type="button" aria-label="<?php esc_attr_e('Chiudi menu', 'saltelli'); ?>" aria-controls="sl-mobile-menu">
                <span class="sl-header__mobile-close-label"><?php esc_html_e('Chiudi', 'saltelli'); ?></span>
                <svg class="sl-header__mobile-close-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path d="M5 5l14 14M19 5L5 19" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        <?php
        /* Wave Elena FB Batch 2 — Wave M (#2): depth 1 → 2 per esporre submenu
           cliccabili da mobile/tablet. JS in main.js gestisce accordion + back. */
        wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'sl-header__mobile-menu',
            'fallback_cb'    => 'saltelli_header_menu_fallback',
            'depth'          => 2,
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
    var update = function(){ h.setAttribute('data-scrolled', window.scrollY > 40 ? 'true' : 'false'); }; /* === design-handoff chrome (P1) === soglia 40px = JSX S2Header */
    update();
    window.addEventListener('scroll', update, { passive: true });

    /* Wave Elena FB Batch 2 — Wave M (#2): drawer mobile con submenu accordion.
       Inline script = safety net minimale (burger toggle + ESC + backdrop + close).
       L'accordion submenu è in assets/js/main.js (deferred, behavior più ricco). */
    var b = h.querySelector('.sl-header__burger');
    var m = h.querySelector('.sl-header__mobile');
    var bd = h.querySelector('.sl-header__mobile-backdrop');
    var closeBtn = h.querySelector('.sl-header__mobile-close');

    function setOpen(open) {
        if (!b || !m) return;
        b.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) {
            m.removeAttribute('hidden');
            if (bd) bd.removeAttribute('hidden');
        } else {
            m.setAttribute('hidden', '');
            if (bd) bd.setAttribute('hidden', '');
            /* collapse all open submenus */
            var openParents = m.querySelectorAll('.menu-item-has-children.is-open');
            for (var i = 0; i < openParents.length; i++) {
                openParents[i].classList.remove('is-open');
                var ctrl = openParents[i].querySelector(':scope > a, :scope > .sl-submenu-toggle');
                if (ctrl) ctrl.setAttribute('aria-expanded', 'false');
            }
        }
        document.documentElement.classList.toggle('sl-menu-open', open);
    }

    if (b && m && !b.dataset.slMenuBound) {
        b.addEventListener('click', function(){
            var open = b.getAttribute('aria-expanded') === 'true';
            setOpen(!open);
        });
        b.dataset.slMenuBound = '1';
    }
    if (closeBtn && !closeBtn.dataset.slMenuBound) {
        closeBtn.addEventListener('click', function(){ setOpen(false); });
        closeBtn.dataset.slMenuBound = '1';
    }
    if (bd && !bd.dataset.slMenuBound) {
        bd.addEventListener('click', function(){ setOpen(false); });
        bd.dataset.slMenuBound = '1';
    }
    if (!document.documentElement.dataset.slMenuEscBound) {
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape' && b && b.getAttribute('aria-expanded') === 'true') {
                setOpen(false);
                if (b) b.focus();
            }
        });
        document.documentElement.dataset.slMenuEscBound = '1';
    }
})();
</script>

<?php
/* === IMPECCABLE v0.20.0 [persuasion + harden] BEGIN — WhatsApp context-aware prefill ===
   Page-aware messaggio per ridurre friction e dare contesto allo studio. */
$sl_wa_context = '';
if (is_front_page()) {
    $sl_wa_context = '';
} elseif (is_singular('competenza')) {
    $sl_wa_context = sprintf(__('sto guardando la pagina "%s"', 'saltelli'), get_the_title());
} elseif (is_singular('avvocato')) {
    $sl_wa_context = sprintf(__('sto guardando il profilo di %s', 'saltelli'), get_the_title());
} elseif (is_tax('tipo-area')) {
    $sl_wa_term = get_queried_object();
    if ($sl_wa_term && isset($sl_wa_term->name)) {
        $sl_wa_context = sprintf(__('sto guardando l\'area "%s"', 'saltelli'), $sl_wa_term->name);
    }
} elseif (is_page()) {
    $sl_wa_context = sprintf(__('sto guardando la pagina "%s"', 'saltelli'), get_the_title());
} elseif (is_singular()) {
    $sl_wa_context = sprintf(__('sto leggendo "%s"', 'saltelli'), get_the_title());
}
// Wave 4.7.fix.2 P4: messaggio precompilato editable da SCF Brand tab.
// Default mantiene legacy text. Editor può sostituire con altra formula.
$sl_wa_msg_template = saltelli_option('whatsapp_message_default', __('Ciao, %s sul vostro sito. Vorrei una consulenza.', 'saltelli'));
$sl_wa_message = $sl_wa_context !== ''
    ? sprintf($sl_wa_msg_template, $sl_wa_context)
    : __('Ciao, vorrei una consulenza presso lo Studio Legale Saltelli & Partners.', 'saltelli');
/* === IMPECCABLE v0.20.0 [persuasion + harden] END === */
?>
<a class="sl-whatsapp-sticky"
   href="https://wa.me/<?php echo esc_attr(ltrim($saltelli_phone_e164, '+')); ?>?text=<?php echo rawurlencode($sl_wa_message); ?>"
   target="_blank"
   rel="noopener"
   aria-label="<?php esc_attr_e('Contatta lo studio su WhatsApp', 'saltelli'); ?>">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="currentColor" aria-hidden="true">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/>
    </svg>
    <span class="sl-whatsapp-sticky__label sl-mono"><?php esc_html_e('WhatsApp', 'saltelli'); ?></span>
</a>

<main id="main" class="sl-main" role="main">
