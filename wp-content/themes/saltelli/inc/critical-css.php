<?php
/**
 * Critical CSS inline — above-fold only.
 *
 * Strategy v0.21.2:
 * - Inline ~10KB of critical above-fold CSS in <head> via wp_head priority 1.
 * - sections.css (190KB) deferred via preload+onload pattern.
 * - tokens.css/base.css/components.css/logo.css continuano a caricare
 *   linkati (sono piccoli ~30KB totali e cacheabili).
 *
 * Above-fold target:
 * - body reset + box-sizing
 * - .sl-container, .sl-skip-link
 * - .sl-mono utility (eyebrows ovunque)
 * - .sl-header full (sticky + scroll states)
 * - .sl-hero structure (homepage)
 * - .sl-chi-siamo__hero, .sl-casi__hero, .sl-contatti-w3__hero per hero cross-template
 *
 * @package Saltelli
 * @since 0.21.2
 */

defined('ABSPATH') || exit;

/**
 * Inline critical CSS in <head> (priority 1, prima di tutti gli enqueue).
 */
function saltelli_inline_critical_css() {
    // Critical CSS minified manually. Mantenere sotto i 14KB (TCP slow-start window).
    $css = <<<'CSS'
/*! v0.21.2 critical above-fold ~9KB */
*,*::before,*::after{box-sizing:border-box}html{-webkit-text-size-adjust:100%;tab-size:4;scroll-behavior:smooth}body{margin:0;background:#FAFAF8;color:#2D2D2D;font-family:"DM Sans","Satoshi",-apple-system,sans-serif;font-size:clamp(16px,1.1vw,18px);line-height:1.65;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}img,svg,video{max-width:100%;height:auto;display:block}a{color:#1B2B4B;text-decoration:none}h1,h2,h3,h4,h5,h6{font-family:"Playfair Display",Georgia,serif;color:#1B2B4B;line-height:1.15;margin:0 0 16px;font-weight:400;letter-spacing:-0.02em}p{margin:0 0 16px}.sl-container,.sl-saltelli-container{width:100%;max-width:1440px;margin-inline:auto;padding-inline:clamp(24px,5vw,96px)}.skip-link,.sl-skip-link{position:absolute;left:-9999px;top:-9999px}.skip-link:focus,.sl-skip-link:focus{left:1rem;top:1rem;z-index:9999;background:#1B2B4B;color:#FAFAF8;padding:8px 16px;font-family:"JetBrains Mono",monospace;font-size:14px}.sl-mono{font-family:"JetBrains Mono",ui-monospace,monospace;font-size:11px;letter-spacing:0.32em;text-transform:uppercase;color:#6B6B6B}.sl-main{min-height:60vh}
/* Header sticky transparent → solid on scroll */
.sl-header{position:sticky;top:0;z-index:50;background:transparent;border-bottom:1px solid transparent;transition:background 180ms ease-out,border-color 180ms ease-out,box-shadow 180ms ease-out;will-change:background-color}
.sl-header.is-scrolled,.sl-header[data-scrolled="true"]{background:#FAFAF8;border-bottom-color:#E5E0D5;box-shadow:0 1px 0 rgba(27,43,75,0.04)}
.sl-header__inner{max-width:1440px;margin:0 auto;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px}
.sl-header__brand-title{font-family:"Playfair Display",Georgia,serif;font-size:17px;color:#1B2B4B;letter-spacing:-0.01em;line-height:1.1}
.sl-header__brand-sub{font-size:10px;margin-top:2px}
.sl-header__nav{display:none}
.sl-header__phone{color:#1B2B4B;font-size:11px;font-family:"JetBrains Mono",monospace;letter-spacing:0.08em;transition:color 200ms cubic-bezier(0.25,0.46,0.45,0.94)}
.sl-header__phone:hover,.sl-header__phone:focus-visible{color:#B8860B}
.sl-header__menu-btn{background:none;border:0;padding:4px;cursor:pointer;display:inline-flex;flex-direction:column;justify-content:center;gap:6px}
.sl-header__menu-btn span{display:block;width:24px;height:1px;background:#2D2D2D}
@media(min-width:1024px){.sl-header__inner{padding:24px 96px;display:grid;grid-template-columns:auto 1fr auto;gap:48px;align-items:center}.sl-header__brand-title{font-size:22px}.sl-header__nav{display:flex;gap:40px;justify-content:center;font-size:14px;font-weight:500}.sl-header__phone{font-size:12px}.sl-header__menu-btn{display:none}}
/* Hero homepage */
.sl-hero{position:relative;padding-block:clamp(32px,5vh,56px);margin:0 auto;overflow:visible}
.sl-hero__eyebrow{margin-bottom:40px;font-size:10px}
.sl-hero__headline{font-size:64px;line-height:0.98;letter-spacing:-0.03em;font-weight:400;margin-bottom:32px;font-family:"Playfair Display",Georgia,serif;color:#1B2B4B}
.sl-hero__headline .sl-word{display:inline-block;margin-right:12px;opacity:0;transform:translateY(30px);transition:opacity 700ms cubic-bezier(0.25,0.46,0.45,0.94),transform 700ms cubic-bezier(0.25,0.46,0.45,0.94)}
.sl-hero__headline .sl-word.is-revealed{opacity:1;transform:translateY(0)}
.sl-hero__lede{font-family:"Playfair Display",Georgia,serif;font-size:17px;font-style:italic;font-weight:400;line-height:1.5;color:#2D2D2D;margin-bottom:40px;max-width:44ch}
.sl-hero__cta{display:inline-block}
@media(min-width:1024px){.sl-hero__headline{font-size:clamp(96px,12vw,180px)}.sl-hero__lede{font-size:22px;max-width:50ch}}
/* Chi-siamo / casi / contatti hero — above-fold cross-template */
.sl-chi-siamo__hero,.sl-casi__hero,.sl-contatti-w3__hero{padding-block:clamp(48px,8vw,96px)}
.sl-chi-siamo__h1,.sl-casi__h1,.sl-contatti-w3__h1{font-family:"Playfair Display",Georgia,serif;font-size:clamp(48px,7vw,96px);line-height:0.98;letter-spacing:-0.025em;color:#1B2B4B;font-weight:400;margin:0 0 32px}
/* Skip-link a11y */
.skip-link:focus{position:fixed;left:8px;top:8px;background:#1B2B4B;color:#FAFAF8;padding:12px 16px;z-index:99999;outline:2px solid #B8860B;outline-offset:4px}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:0.01ms!important;animation-iteration-count:1!important;transition-duration:0.01ms!important}html{scroll-behavior:auto}}
CSS;

    echo "<style id=\"sl-critical-css\">{$css}</style>\n";
}
add_action('wp_head', 'saltelli_inline_critical_css', 1);

/**
 * Defer non-critical CSS bundles via preload+onload pattern.
 *
 * Wave 4 (1.3.0): extended from sections-only (v0.21.2) to also include
 * components, logo, cro. tokens + base remain render-blocking because:
 *   - tokens.css (~3 KB) defines CSS variables consumed by every selector;
 *     deferring it would FOUC any below-the-fold component during the
 *     async load window
 *   - base.css (~5 KB) carries the @font-face declarations and font-display
 *     swap mechanism — must be parsed ASAP so the browser begins font
 *     fetching before script-driven layout work
 *
 * Render-blocking budget post-Wave 4: tokens + base ≈ 8 KB + WP core
 * wpa-css (≈1 KB) + inline critical (~9 KB) = ~18 KB. Just over the
 * 14 KB TCP slow-start window but acceptable since the inline blob is
 * served same-packet with the HTML response.
 *
 * Pattern: <link rel="preload" as="style" onload="this.rel='stylesheet'">
 *          <noscript><link rel="stylesheet" ...></noscript>
 */
function saltelli_defer_main_css($html, $handle) {
    static $async_handles = [
        'saltelli-components',
        'saltelli-logo',
        'saltelli-sections',
        'saltelli-cro',
    ];
    if (!in_array($handle, $async_handles, true)) {
        return $html;
    }
    $href_match = preg_match("/href=['\"]([^'\"]+)['\"]/", $html, $href_m);
    if (!$href_match) {
        return $html;
    }
    $href = $href_m[1];
    $id_match = preg_match("/id=['\"]([^'\"]+)['\"]/", $html, $id_m);
    $id = $id_match ? $id_m[1] : ($handle . '-css');

    $preload  = '<link rel="preload" as="style" id="' . esc_attr($id) . '" href="' . esc_url($href) . '" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
    $noscript = '<noscript><link rel="stylesheet" href="' . esc_url($href) . '"></noscript>' . "\n";

    return $preload . $noscript;
}
add_filter('style_loader_tag', 'saltelli_defer_main_css', 10, 2);
