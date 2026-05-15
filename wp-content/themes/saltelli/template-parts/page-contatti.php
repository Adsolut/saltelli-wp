<?php
/**
 * Template part: page-contatti.php
 *
 * Render della page /contatti/. Hero+map+trust da ACF (Wave 2).
 * Form CF7 + come-arrivare 3-list resta hardcoded (visual/structural critical).
 *
 * @package Saltelli
 * @since 1.0.0 Wave 3
 */
defined('ABSPATH') || exit;

$pid    = get_the_ID();
$studio = function_exists('saltelli_studio_data') ? saltelli_studio_data() : [];

// ACF fields (Wave 2 popolato).
$hero_eyebrow = saltelli_field('hero_eyebrow', $pid, '§ Contatti · Primo incontro gratuito');
$hero_h1_pre  = saltelli_field('hero_h1_pre', $pid, 'Contatti.');
$hero_h1_em   = saltelli_field('hero_h1_em', $pid, '');
$hero_lede    = saltelli_field('hero_lede', $pid, 'Chiedi qualsiasi cosa. In qualsiasi momento.');

$map_iframe   = saltelli_field('map_iframe', $pid, '');
$map_caption  = saltelli_field('map_caption', $pid, 'Chiaia · Napoli');

$come_title   = saltelli_field('come_arrivare_title', $pid, 'Come arrivare.');
$trust_signal = saltelli_field('trust_signal', $pid, 'Riceviamo solo su appuntamento. Risposta entro 24 ore.');

// Studio dynamic data via Theme Options (Wave 3) → fallback saltelli_studio_data().
$tel_label = saltelli_option('studio_telefono_pubblico', $studio['phone'] ?? '+39 081 1813 1119');
if (!preg_match('/^\+/', $tel_label)) {
    $tel_label = '+39 081 1813 1119';
}
$tel_href  = 'tel:' . preg_replace('/[^0-9+]/', '', $tel_label);

$email_pub = saltelli_option('studio_email', $studio['email'] ?? 'info@studiolegalesaltelli.it');
if ($email_pub === '') $email_pub = $studio['email'] ?? '';

$wa_digits = preg_replace('/[^0-9]/', '', (string) ($studio['whatsapp'] ?? ''));
$wa_href   = 'https://wa.me/' . $wa_digits;

$chain_contact = saltelli_get_breadcrumb_chain();
?>

<div class="sl-contatti-w3">

    <section class="sl-contatti-w3__hero sl-page-hero" aria-labelledby="contatti-h1">
        <div class="sl-contatti-w3__hero-grid">
            <div class="sl-contatti-w3__hero-left">
                <?php if (!empty($chain_contact) && count($chain_contact) > 1) : ?>
                    <nav class="sl-mono sl-page__breadcrumb sl-contatti-w3__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'saltelli'); ?>">
                        <?php foreach ($chain_contact as $sl_idx => $sl_node) :
                            if ($sl_idx > 0) echo ' / ';
                            if (!empty($sl_node['url'])) : ?>
                                <a href="<?php echo esc_url($sl_node['url']); ?>"><?php echo esc_html($sl_node['name']); ?></a>
                            <?php else : ?>
                                <span><?php echo esc_html($sl_node['name']); ?></span>
                            <?php endif;
                        endforeach; ?>
                    </nav>
                <?php endif; ?>
                <div class="sl-mono sl-contatti-w3__eyebrow"><?php echo esc_html($hero_eyebrow); ?></div>
                <h1 class="sl-contatti-w3__h1" id="contatti-h1" data-split-reveal><?php echo wp_kses(saltelli_split_h1_words($hero_h1_pre), ['span' => ['class' => true, 'data-i' => true]]); ?></h1>
            </div>
            <div class="sl-contatti-w3__hero-right">
                <?php
                // Lede split su ". " (period+space) → 2 line: prima sentence + accent span.
                $lede_parts = preg_split('/\.\s+/', trim($hero_lede), 2);
                $lede_main  = isset($lede_parts[0]) ? trim($lede_parts[0]) . '.' : trim($hero_lede);
                $lede_accent = isset($lede_parts[1]) ? trim($lede_parts[1]) : '';
                ?>
                <p class="sl-contatti-w3__hero-lede">
                    <?php echo esc_html($lede_main); ?><?php if ($lede_accent !== '') : ?><br>
                    <span class="sl-contatti-w3__hero-lede-accent"><?php echo esc_html($lede_accent); ?></span><?php endif; ?>
                </p>
            </div>
        </div>
    </section>

    <section class="sl-contatti-w3__main" aria-labelledby="contatti-form-h">
        <div class="sl-contatti-w3__main-grid">

            <div class="sl-contatti-w3__form-col">
                <div class="sl-mono"><?php echo esc_html(saltelli_field('contatti_form_eyebrow', $pid, '§ 01 — Modulo')); ?></div>
                <h2 class="sl-contatti-w3__form-h" id="contatti-form-h">
                    <?php echo esc_html(saltelli_field('contatti_form_h2_main', $pid, 'Prenota un primo')); ?><br>
                    <em><?php echo esc_html(saltelli_field('contatti_form_h2_em', $pid, 'incontro gratuito.')); ?></em>
                </h2>

                <?php
                if (shortcode_exists('contact-form-7')) {
                    $form_post = get_page_by_path('saltelli-contatti', OBJECT, 'wpcf7_contact_form');
                    if ($form_post) {
                        echo do_shortcode('[contact-form-7 id="' . (int) $form_post->ID . '" title="Saltelli Contatti"]');
                    } else {
                        echo '<p class="sl-mono">' . esc_html__('Modulo non disponibile. Scrivici a info@studiolegalesaltelli.it.', 'saltelli') . '</p>';
                    }
                } else {
                    ?>
                    <form class="sl-contatti-w3__form" method="post" action="#" novalidate>
                        <label class="sl-contatti-w3__field">
                            <span class="sl-mono"><?php esc_html_e('Nome e cognome *', 'saltelli'); ?></span>
                            <input type="text" name="nome" class="sl-input" required>
                        </label>

                        <div class="sl-contatti-w3__field-row">
                            <label class="sl-contatti-w3__field">
                                <span class="sl-mono"><?php esc_html_e('Email *', 'saltelli'); ?></span>
                                <input type="email" name="email" class="sl-input" required>
                            </label>
                            <label class="sl-contatti-w3__field">
                                <span class="sl-mono"><?php esc_html_e('Telefono', 'saltelli'); ?></span>
                                <input type="tel" name="telefono" class="sl-input">
                            </label>
                        </div>

                        <label class="sl-contatti-w3__field">
                            <span class="sl-mono"><?php esc_html_e('Messaggio *', 'saltelli'); ?></span>
                            <textarea name="messaggio" rows="6" class="sl-input" required></textarea>
                        </label>

                        <label class="sl-contatti-w3__gdpr">
                            <input type="checkbox" name="gdpr" required>
                            <span>
                                <?php
                                printf(
                                    /* translators: %s wraps the privacy policy link */
                                    esc_html__('Consento il trattamento dei dati personali ai sensi del Reg. UE 2016/679 (GDPR), per le finalità descritte nell\'%s. *', 'saltelli'),
                                    '<a href="' . esc_url(home_url('/privacy-policy/')) . '" class="sl-link">' . esc_html__('informativa privacy', 'saltelli') . '</a>'
                                );
                                ?>
                            </span>
                        </label>

                        <div class="sl-contatti-w3__submit-row">
                            <button type="submit" class="sl-btn sl-btn--primary">
                                <span><?php esc_html_e('Prenota un incontro', 'saltelli'); ?></span>
                                <span class="arrow" aria-hidden="true">→</span>
                            </button>
                        </div>
                    </form>
                    <?php
                }
                ?>

                <div class="sl-contatti-w3__success" hidden role="status" aria-live="polite">
                    <div class="sl-mono sl-contatti-w3__success-eyebrow"><?php echo esc_html(saltelli_field('contatti_success_eyebrow', $pid, '§ Inviato')); ?></div>
                    <h3 class="sl-contatti-w3__success-h3"><?php echo esc_html(saltelli_field('contatti_success_h3', $pid, 'Grazie. Ci sentiamo entro 24 ore.')); ?></h3>
                    <p class="sl-contatti-w3__success-text">
                        <?php echo esc_html(saltelli_field('contatti_success_text', $pid, 'La tua richiesta è stata inviata correttamente. Riceverai una conferma via email e ti ricontatteremo entro 24 ore lavorative.')); ?>
                    </p>
                </div>
            </div>

            <aside class="sl-contatti-w3__aside" aria-labelledby="contatti-aside-h">
                <div class="sl-mono" id="contatti-aside-h"><?php echo esc_html(saltelli_field('contatti_aside_eyebrow', $pid, '§ 02 — Studio')); ?></div>

                <div class="sl-contatti-w3__nap">
                    <div class="sl-mono"><?php esc_html_e('Indirizzo', 'saltelli'); ?></div>
                    <address class="sl-contatti-w3__address">
                        <?php
                        $studio_via = saltelli_option('studio_indirizzo_via', 'Via Vannella Gaetani, 27');
                        $studio_cap = saltelli_option('studio_cap_citta', '80121 Napoli');
                        $studio_quartiere = saltelli_option('studio_quartiere', 'Chiaia');
                        // Split via su prima virgola per render 2-line (Via Vannella / Gaetani, 27)
                        $via_parts = preg_split('/,\s*/', $studio_via, 2);
                        echo esc_html($via_parts[0] ?? $studio_via);
                        ?><br>
                        <?php echo esc_html(isset($via_parts[1]) ? $via_parts[1] : ''); ?><?php if (!empty($via_parts[1])) : ?><br><?php endif; ?>
                        <?php echo esc_html($studio_cap . ' — ' . $studio_quartiere); ?>
                    </address>
                </div>

                <?php if ($map_iframe !== '') : ?>
                <div class="sl-contatti-w3__map" aria-label="<?php esc_attr_e('Mappa studio Saltelli — Chiaia, Napoli', 'saltelli'); ?>">
                    <?php echo wp_kses($map_iframe, [
                        'iframe' => [
                            'src'         => true,
                            'title'       => true,
                            'width'       => true,
                            'height'      => true,
                            'frameborder' => true,
                            'scrolling'   => true,
                            'loading'     => true,
                            'allow'       => true,
                            'allowfullscreen' => true,
                        ],
                    ]); ?>
                    <?php /* Elena fix 2026-05-15 — rimosso label overlay
                       <div class="sl-mono sl-contatti-w3__map-tag"> con caption
                       "CHIAIA · NAPOLI" sopra la mappa. Era posizionato absolute
                       top-left e intercettava i click sulla mappa, impedendo
                       l'interazione zoom/drag su Google Maps embed. La caption
                       è ridondante con l'indirizzo già mostrato accanto (Via
                       Vannella Gaetani 27 — Chiaia, Napoli). $map_caption
                       SCF resta in DB ma non più renderizzato (cleanup post-cut). */ ?>
                </div>
                <?php endif; ?>

                <div class="sl-contatti-w3__cta-list" role="list">
                    <a class="sl-contatti-w3__cta-row" role="listitem" href="<?php echo esc_attr($tel_href); ?>">
                        <span class="sl-mono"><?php esc_html_e('Telefono', 'saltelli'); ?></span>
                        <span class="sl-contatti-w3__cta-val"><?php echo esc_html($tel_label); ?> <span class="arrow" aria-hidden="true">→</span></span>
                    </a>
                    <a class="sl-contatti-w3__cta-row" role="listitem" href="mailto:<?php echo esc_attr($email_pub); ?>">
                        <span class="sl-mono"><?php esc_html_e('Email', 'saltelli'); ?></span>
                        <span class="sl-contatti-w3__cta-val"><?php echo esc_html($email_pub); ?> <span class="arrow" aria-hidden="true">→</span></span>
                    </a>
                    <a class="sl-contatti-w3__cta-row" role="listitem" href="<?php echo esc_url($wa_href); ?>" target="_blank" rel="noopener">
                        <span class="sl-mono"><?php esc_html_e('WhatsApp', 'saltelli'); ?></span>
                        <span class="sl-contatti-w3__cta-val"><?php echo esc_html(saltelli_field('contatti_whatsapp_cta_label', $pid, 'Scrivi su WhatsApp')); ?> <span class="arrow" aria-hidden="true">→</span></span>
                    </a>
                </div>

                <div class="sl-contatti-w3__hours">
                    <div class="sl-mono"><?php esc_html_e('Orari', 'saltelli'); ?></div>
                    <div class="sl-contatti-w3__hours-body">
                        <?php
                        $orari_settimana = saltelli_option('studio_orari_settimana', 'Lun – Ven · 10:00 – 19:00');
                        $orari_sabato    = saltelli_option('studio_orari_sabato', 'Sabato su appuntamento');
                        echo esc_html($orari_settimana);
                        ?><br>
                        <?php echo esc_html($orari_sabato); ?>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="sl-contatti-w3__come" aria-labelledby="contatti-come-h">
        <div class="sl-contatti-w3__come-grid">
            <div class="sl-mono sl-contatti-w3__come-mark"><?php echo esc_html(saltelli_field('contatti_come_eyebrow', $pid, '§ 03 — Come arrivare')); ?></div>
            <div class="sl-contatti-w3__come-body">
                <h2 class="sl-contatti-w3__come-h" id="contatti-come-h"><?php echo esc_html($come_title); ?></h2>
                <ul class="sl-contatti-w3__come-list" role="list">
                    <li class="sl-contatti-w3__come-item">
                        <div class="sl-mono"><?php echo esc_html(saltelli_field('contatti_come_item1_label', $pid, 'Metro')); ?></div>
                        <h3 class="sl-contatti-w3__come-t"><?php echo esc_html(saltelli_field('contatti_come_item1_title', $pid, 'Linea 6 · Mergellina')); ?></h3>
                        <p class="sl-contatti-w3__come-d"><?php echo esc_html(saltelli_field('contatti_come_item1_desc', $pid, '8 minuti a piedi lungo la Riviera di Chiaia.')); ?></p>
                    </li>
                    <li class="sl-contatti-w3__come-item">
                        <div class="sl-mono"><?php echo esc_html(saltelli_field('contatti_come_item2_label', $pid, 'Auto')); ?></div>
                        <h3 class="sl-contatti-w3__come-t"><?php echo esc_html(saltelli_field('contatti_come_item2_title', $pid, 'Parcheggio Mergellina')); ?></h3>
                        <p class="sl-contatti-w3__come-d"><?php echo esc_html(saltelli_field('contatti_come_item2_desc', $pid, 'Sosta a pagamento, 5 minuti a piedi.')); ?></p>
                    </li>
                    <li class="sl-contatti-w3__come-item">
                        <div class="sl-mono"><?php echo esc_html(saltelli_field('contatti_come_item3_label', $pid, 'Treno')); ?></div>
                        <h3 class="sl-contatti-w3__come-t"><?php echo esc_html(saltelli_field('contatti_come_item3_title', $pid, 'Napoli Mergellina')); ?></h3>
                        <p class="sl-contatti-w3__come-d"><?php echo esc_html(saltelli_field('contatti_come_item3_desc', $pid, 'Stazione FS, 10 minuti a piedi.')); ?></p>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <?php /* Elena fix 2026-05-14 revised: §04 Mappa standalone RIMOSSA — la mappa
       Google Maps va nel sidebar §02 Studio (già renderizzata via `map_iframe`
       SCF a riga 173). PR #37 era ridondante. */ ?>

    <section class="sl-contatti-w3__trust" aria-label="<?php esc_attr_e('La nostra professionalità', 'saltelli'); ?>">
        <div class="sl-contatti-w3__trust-inner">
            <div class="sl-mono sl-contatti-w3__trust-eyebrow"><?php echo esc_html(saltelli_field('contatti_trust_eyebrow', $pid, 'La nostra professionalità')); ?></div>
            <p class="sl-contatti-w3__trust-quote">
                <?php
                // Trust signal split su prima sentence per render 3-line (Riceviamo / su appuntamento / Risposta).
                $trust_parts = preg_split('/\.\s+/', trim($trust_signal), 2);
                $trust_main  = isset($trust_parts[0]) ? trim($trust_parts[0]) . '.' : trim($trust_signal);
                $trust_tail  = isset($trust_parts[1]) ? trim($trust_parts[1]) : '';
                // Split main su space mediano per 2 righe.
                $main_words = preg_split('/\s+/', $trust_main);
                $half = (int) ceil(count($main_words) / 2);
                $main_l1 = implode(' ', array_slice($main_words, 0, $half));
                $main_l2 = implode(' ', array_slice($main_words, $half));
                ?>
                <?php echo esc_html($main_l1); ?><br><?php echo esc_html($main_l2); ?><br>
                <?php if ($trust_tail !== '') : ?>
                    <span class="sl-contatti-w3__trust-tail"><?php echo esc_html($trust_tail); ?></span>
                <?php endif; ?>
            </p>
        </div>
    </section>

</div>
