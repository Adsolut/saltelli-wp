<?php
/**
 * Template part: Trust Bar globale (Pattern 2 + 7)
 * Wave 6 — adapted from .sl-mono + .sl-rule
 *
 * 4 segnali credibilità da Theme Options Brand tab.
 * Ogni segnale: label (Playfair grande) + caption (mono uppercase).
 * Opzionalmente, source legenda micro-mono (Pattern 7) — TODO Wave 6.1
 *
 * Usage:
 *   get_template_part('template-parts/trust-bar');
 *
 * Graceful fallback: nessun signal popolato → blocco non renderizzato.
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

$signals = [];
for ($i = 1; $i <= 4; $i++) {
    $label   = saltelli_option("trust_signal_{$i}_label", '');
    $caption = saltelli_option("trust_signal_{$i}_caption", '');
    if (!empty($label) || !empty($caption)) {
        $signals[] = [
            'label'   => $label,
            'caption' => $caption,
        ];
    }
}

if (empty($signals)) {
    return;
}
?>
<aside class="sl-trust-bar" aria-label="<?php esc_attr_e('Trust signals dello studio', 'saltelli'); ?>">
    <?php foreach ($signals as $s) : ?>
        <div class="sl-trust-bar__item">
            <?php if (!empty($s['label'])) : ?>
                <div class="sl-trust-bar__label"><?php echo esc_html($s['label']); ?></div>
            <?php endif; ?>
            <?php if (!empty($s['caption'])) : ?>
                <div class="sl-trust-bar__caption"><?php echo esc_html($s['caption']); ?></div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</aside>
