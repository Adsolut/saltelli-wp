<?php
/**
 * Template part: Inline mini-form contestuale (Pattern 4)
 * Wave 6 — extracted/reduced from CF7 saltelli-contatti
 *
 * Strategy:
 *  1. Se esiste CF7 form con slug "saltelli-mini" → render shortcode con topic_default pre-fill (via filter)
 *  2. Altrimenti, fallback a CTA progressive verso /contatti/?topic={slug}
 *
 * Args:
 *   $args['topic_default']  string  Slug area pratica corrente (per pre-fill select / query string)
 *   $args['title']          string  Title override (default editoriale)
 *   $args['lede']           string  Lede italic override
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

$args   = isset($args) && is_array($args) ? $args : [];
$topic  = !empty($args['topic_default']) ? sanitize_title($args['topic_default']) : '';
$title  = !empty($args['title'])         ? (string) $args['title']                : __('Parlane con noi', 'saltelli');
$lede   = !empty($args['lede'])          ? (string) $args['lede']                 : __('Prima consulenza conoscitiva. Risposta entro 24 ore.', 'saltelli');

$cta_label = saltelli_option('cta_default_label', __('Prenota un incontro →', 'saltelli'));
$contact_base_url = home_url('/contatti/');
$contact_url = !empty($topic)
    ? add_query_arg(['topic' => $topic], $contact_base_url)
    : $contact_base_url;
?>
<section class="sl-mini-form" aria-labelledby="sl-mini-form-h">
    <p class="sl-mono"><?php esc_html_e('§ Parla con noi', 'saltelli'); ?></p>
    <h2 class="sl-mini-form__title" id="sl-mini-form-h"><?php echo esc_html($title); ?></h2>
    <p class="sl-mini-form__lede"><em><?php echo esc_html($lede); ?></em></p>

    <?php
    $rendered = false;

    if (shortcode_exists('contact-form-7')) {
        $mini_form = get_page_by_path('saltelli-mini', OBJECT, 'wpcf7_contact_form');
        if ($mini_form) {
            // Pre-fill topic via filter wpcf7_form_hidden_fields (CF7 nativo).
            $topic_filter = function ($fields) use ($topic) {
                if (!empty($topic)) {
                    $fields['topic-default'] = $topic;
                }
                return $fields;
            };
            add_filter('wpcf7_form_hidden_fields', $topic_filter);
            echo do_shortcode('[contact-form-7 id="' . (int) $mini_form->ID . '" title="Saltelli Mini Form"]');
            remove_filter('wpcf7_form_hidden_fields', $topic_filter);
            $rendered = true;
        }
    }

    if (!$rendered) :
    ?>
        <div class="sl-mini-form__cta">
            <a class="sl-btn sl-btn--primary" href="<?php echo esc_url($contact_url); ?>" aria-label="<?php esc_attr_e('Vai al modulo di contatto', 'saltelli'); ?>">
                <?php echo esc_html($cta_label); ?>
            </a>
            <p class="sl-mono sl-mini-form__hint">
                <?php esc_html_e('Modulo completo · Risposta entro 24 ore · Riservatezza assoluta', 'saltelli'); ?>
            </p>
        </div>
    <?php endif; ?>
</section>
