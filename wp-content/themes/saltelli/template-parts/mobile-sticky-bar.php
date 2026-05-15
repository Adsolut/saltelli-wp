<?php
/**
 * Template part: Mobile Sticky Bottom Bar (Pattern 3)
 * Wave 6 — adapted from .sl-attorney__sticky + .sl-whatsapp-sticky
 *
 * 3 azioni sempre raggiungibili da mobile: tel: / whatsapp / scrivi.
 *
 * CAL-W6-06: usa PHP-level conditional (NO CSS :not() chains) per gestire
 * le exclusion. Più robusto post-Wave 5 dove molti slug sono cambiati
 * (chi-siamo → lo-studio, faq → domande-frequenti).
 *
 * Hook: footer.php prima di wp_footer().
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

// Exclusion rules — NO sticky bar dove c'è già un CTA dedicato o un form completo.
$exclude =
       is_singular('avvocato')              // single-avvocato ha .sl-attorney__sticky
    || is_page('contatti')                  // form completo
    || is_404();

// Per quartiere/lingue/famiglia, dove la sticky bar non aggiunge valore (TBD orchestratore):
// Nessuna exclusion ulteriore in v1 — orchestrator decide.

if ($exclude) {
    return;
}

$studio   = saltelli_studio_data();
$phone    = !empty($studio['phone'])    ? (string) $studio['phone']    : '';
$whatsapp = !empty($studio['whatsapp']) ? (string) $studio['whatsapp'] : '';
$contact_url = home_url('/contatti/');
?>
<aside class="sl-mobile-bar" aria-label="<?php esc_attr_e('Contatti rapidi', 'saltelli'); ?>">
    <?php if (!empty($phone)) : ?>
        <a href="tel:<?php echo esc_attr($phone); ?>" class="sl-mobile-bar__action" aria-label="<?php esc_attr_e('Chiama lo studio', 'saltelli'); ?>">
            <span class="sl-mobile-bar__icon" aria-hidden="true">&#9742;</span>
            <span class="sl-mobile-bar__label"><?php esc_html_e('Chiama', 'saltelli'); ?></span>
        </a>
    <?php endif; ?>
    <?php if (!empty($whatsapp)) : ?>
        <a
            href="<?php echo esc_url(saltelli_whatsapp_studio_url()); ?>"
            class="sl-mobile-bar__action"
            target="_blank"
            rel="noopener"
            aria-label="<?php esc_attr_e('Scrivi su WhatsApp', 'saltelli'); ?>">
            <span class="sl-mobile-bar__icon" aria-hidden="true">WA</span>
            <span class="sl-mobile-bar__label"><?php esc_html_e('WhatsApp', 'saltelli'); ?></span>
        </a>
    <?php endif; ?>
    <a href="<?php echo esc_url($contact_url); ?>" class="sl-mobile-bar__action" aria-label="<?php esc_attr_e('Scrivi un messaggio', 'saltelli'); ?>">
        <span class="sl-mobile-bar__icon" aria-hidden="true">&#9998;</span>
        <span class="sl-mobile-bar__label"><?php esc_html_e('Scrivi', 'saltelli'); ?></span>
    </a>
</aside>
