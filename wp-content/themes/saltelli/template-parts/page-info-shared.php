<?php
/**
 * Template part: page-info-shared.php
 *
 * Render condiviso per le 6 info-page+ (guide-gratuite, come-lavoriamo,
 * prima-consulenza, lavora-con-noi, richiedi-preventivo, prenota-appuntamento).
 *
 * Sostituisce il blocco hardcoded `is_page([...])` di page.php pre-Wave3.
 * Tutti i fields letti da ACF Field Group `group_info_shared_v1` (Wave 1)
 * popolato in Wave 2.
 *
 * Wave Elena FB Batch 3 #22: aggiunto prenota-appuntamento al pool template
 * (Elena: "layout deve essere uguale a Richiedi preventivo"). Per backward-compat
 * il body_content per /prenota-appuntamento/ fa fallback a SCF legacy `prenota_intro`
 * (group_prenota_appuntamento_v1) quando body_content è vuoto: nessuna perdita
 * di dati editor pre-esistenti. Defaults specifici per prenota-appuntamento sotto.
 *
 * @package Saltelli
 * @since 1.0.0 Wave 3
 * @since 1.3.19 Wave Elena FB Batch 3 — extended to prenota-appuntamento
 */
defined('ABSPATH') || exit;

$pid = get_the_ID();
$slug = get_post_field('post_name', $pid);

// Wave Elena FB Batch 3 #22 — defaults per slug.
// Per richiedi-preventivo (e per gli altri 4 info-page) i default sono vuoti:
// content popolato già in DB da Wave 2 Content Migration / Elena editing.
// Per prenota-appuntamento (nuovo arrivo) servono defaults editorialmente sensati:
// la pagina ha solo 1 SCF legacy (prenota_intro wysiwyg) — gli altri 15 field
// info-shared sono nuovi qui, quindi forniamo defaults conservative.
$is_prenota = ($slug === 'prenota-appuntamento');

$default_hero_eyebrow   = $is_prenota ? '§ Appuntamento' : '';
$default_hero_h1_pre    = $is_prenota ? 'Prenota un'      : '';
$default_hero_h1_em     = $is_prenota ? 'appuntamento'    : '';
$default_hero_lede      = $is_prenota ? 'Solo su appuntamento, in studio a Chiaia o in videocall. Risposta entro 24 ore dalla richiesta.' : '';
$default_aside_eyebrow  = $is_prenota ? '§ Modalità'      : '';
$default_aside_h3       = $is_prenota ? 'Tre canali, una sola promessa: rispondiamo entro 24 ore.' : '';
$default_aside_p        = $is_prenota ? 'In studio · Videocall · Telefono · Risposta in 24h · Riservatezza assoluta' : '';
$default_aside_cta_label = $is_prenota ? 'Compila il modulo' : '';
$default_aside_cta_url  = $is_prenota ? '/contatti/'      : '';
$default_cta_final_h2   = $is_prenota ? 'Pronto a parlarci del tuo caso?' : '';
$default_cta_final_p    = $is_prenota ? 'Compila il modulo: leggiamo ogni richiesta personalmente, rispondiamo entro 24 ore.' : '';
$default_cta_final_label = $is_prenota ? 'Compila il modulo' : 'Prenota un incontro';

// Hero
$hero_eyebrow = saltelli_field('hero_eyebrow', $pid, $default_hero_eyebrow);
$hero_h1_pre  = saltelli_field('hero_h1_pre', $pid, $default_hero_h1_pre);
$hero_h1_em   = saltelli_field('hero_h1_em', $pid, $default_hero_h1_em);
$hero_lede    = saltelli_field('hero_lede', $pid, $default_hero_lede);

// Aside trust box
$aside_eyebrow   = saltelli_field('aside_eyebrow', $pid, $default_aside_eyebrow);
$aside_h3        = saltelli_field('aside_h3', $pid, $default_aside_h3);
$aside_p         = saltelli_field('aside_p', $pid, $default_aside_p);
$aside_cta_label = saltelli_field('aside_cta_label', $pid, $default_aside_cta_label);
$aside_cta_url   = saltelli_field('aside_cta_url', $pid, $default_aside_cta_url);

// Body editorial
$body_content = saltelli_field('body_content', $pid, '');

// Wave Elena FB Batch 3 #22 — backward-compat per prenota-appuntamento.
// Page 2714 nasce con SCF legacy group_prenota_appuntamento_v1 (1 field wysiwyg
// `prenota_intro`). Se body_content è vuoto E lo slug è prenota-appuntamento,
// leggiamo prenota_intro come fonte: zero perdita di dati pre-esistenti editor.
if ($body_content === '' && $is_prenota && function_exists('get_field')) {
    $legacy_prenota = (string) get_field('prenota_intro', $pid, false);
    if ($legacy_prenota !== '') {
        $body_content = $legacy_prenota;
    }
}

// CTA finale
$cta_final_eyebrow   = saltelli_field('cta_final_eyebrow', $pid, '§ Pronto?');
$cta_final_h2        = saltelli_field('cta_final_h2', $pid, $default_cta_final_h2);
$cta_final_p         = saltelli_field('cta_final_p', $pid, $default_cta_final_p);
$cta_final_cta_label = saltelli_field('cta_final_cta_label', $pid, $default_cta_final_label);
$cta_final_cta_url   = saltelli_field('cta_final_cta_url', $pid, '/contatti/');
$cta_final_trust     = saltelli_field('cta_final_trust', $pid, 'Risposta entro 24 ore · Riservatezza assoluta');

// Aside trust list — dal aside_p split su " · " per render compat con markup pre-Wave3.
// Pre-Wave3 il template usava trust_list array; ora il valore è singola stringa concatenata.
$aside_trust_list = $aside_p !== '' ? array_filter(array_map('trim', explode('·', str_replace(' · ', '·', $aside_p)))) : [];

// trust_headline alias dell'aside_h3 per back-compat markup.
$trust_headline = $aside_h3;
?>
<article class="sl-info-page sl-info-page--<?php echo esc_attr($slug); ?>">

    <header class="sl-info-page__hero sl-page-hero sl-page-hero--compact">
        <div class="sl-info-page__hero-text">
            <?php saltelli_render_breadcrumb(); ?>
            <?php if ($hero_eyebrow !== '') : ?>
                <div class="sl-mono sl-info-page__hero-eyebrow"><?php echo esc_html($hero_eyebrow); ?></div>
            <?php endif; ?>
            <h1 class="sl-info-page__h1" data-split-reveal>
                <?php echo esc_html($hero_h1_pre); ?>
                <?php if ($hero_h1_em !== '') : ?><br><em><?php echo esc_html($hero_h1_em); ?></em><?php endif; ?>
            </h1>
            <?php if ($hero_lede !== '') : ?>
                <p class="sl-info-page__lede"><?php echo esc_html($hero_lede); ?></p>
            <?php endif; ?>
        </div>
        <aside class="sl-info-page__trust">
            <?php if ($aside_eyebrow !== '') : ?>
                <div class="sl-mono sl-info-page__trust-eyebrow"><?php echo esc_html($aside_eyebrow); ?></div>
            <?php endif; ?>
            <?php if ($trust_headline !== '') : ?>
                <p class="sl-info-page__trust-headline"><?php echo esc_html($trust_headline); ?></p>
            <?php endif; ?>
            <?php if (!empty($aside_trust_list)) : ?>
                <ul class="sl-info-page__trust-list" role="list">
                    <?php foreach ($aside_trust_list as $li) : ?>
                        <li><?php echo esc_html(trim($li)); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </aside>
    </header>

    <?php if ($body_content !== '') : ?>
    <section class="sl-info-page__body">
        <div class="sl-mono sl-info-page__body-eyebrow"><?php esc_html_e('§ 01 — Approfondimento', 'saltelli'); ?></div>
        <div class="sl-info-page__prose">
            <?php
            // Wave 4.7.fix.4 STRATEGY A: source unica = SCF body_content.
            // Pre-fix.4: aveva fallback the_content() WP nativo per pagine senza
            // body_content popolato. Post-fix.4: post_content è stato bonificato +
            // Gutenberg disabled, una sola sorgente di verità per pagina.
            // Se body_content è vuoto, l'intera sezione __body è skippata (silent).
            echo wp_kses_post($body_content);
            ?>
        </div>
    </section>
    <?php endif; ?>

    <?php /* Elena fix 2026-05-14: rimossa <section sl-info-page__cta>
       (CTA finale dark navy) — ridondante con footer pre-CTA "§ Contattaci"
       cross-page. Helper vars $cta_final_* restano definiti sopra (dead
       vars cleanup minore Wave 6.1). */ ?>

    <?php
    /* Elena fix 2026-05-15 — modulo CF7 inline su /costi-e-consulenze/richiedi-preventivo/
       (e /prenota-appuntamento/ per consistenza). Stesso form ID di /contatti/
       ("Saltelli Contatti", ID 2703, slug `saltelli-contatti`). L'utente
       chiede di NON dover navigare a /contatti/ ma compilare direttamente
       dalla pagina richiedi-preventivo. Markup riusa le classi della
       page-contatti.php per design consistency. */
    if (in_array($slug, ['richiedi-preventivo', 'prenota-appuntamento'], true) && shortcode_exists('contact-form-7')) :
        $form_post = get_page_by_path('saltelli-contatti', OBJECT, 'wpcf7_contact_form');
        if ($form_post) :
            $form_eyebrow = ($slug === 'richiedi-preventivo')
                ? __('§ 02 — Richiedi preventivo', 'saltelli')
                : __('§ 02 — Prenota appuntamento', 'saltelli');
            $form_h2_main = ($slug === 'richiedi-preventivo')
                ? __('Raccontaci il tuo caso', 'saltelli')
                : __('Prenota un primo', 'saltelli');
            $form_h2_em = ($slug === 'richiedi-preventivo')
                ? __('e prepariamo un preventivo.', 'saltelli')
                : __('incontro gratuito.', 'saltelli');
            ?>
            <section class="sl-info-page__form sl-contatti-w3__main" aria-labelledby="info-page-form-h">
                <div class="sl-contatti-w3__main-grid">
                    <div class="sl-contatti-w3__form-col">
                        <div class="sl-mono"><?php echo esc_html($form_eyebrow); ?></div>
                        <h2 class="sl-contatti-w3__form-h" id="info-page-form-h">
                            <?php echo esc_html($form_h2_main); ?><br>
                            <em><?php echo esc_html($form_h2_em); ?></em>
                        </h2>
                        <?php echo do_shortcode('[contact-form-7 id="' . (int) $form_post->ID . '" title="Saltelli Contatti"]'); ?>
                    </div>
                </div>
            </section>
        <?php endif;
    endif; ?>

</article>
