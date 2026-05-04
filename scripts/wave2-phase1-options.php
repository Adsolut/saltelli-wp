<?php
/**
 * Wave 2 Phase 1 — Theme Options migration.
 *
 * Source of truth values:
 *   - saltelli_studio_data() in inc/helpers.php (NAP, GPS, social, founding date)
 *   - footer.php hardcoded brand statement
 *   - Field Group group_theme_options_v1.json defaults (CTA, brand_payoff)
 *
 * Run:
 *   docker compose run --rm wpcli eval-file /var/www/html/wp-content/themes/saltelli/../../../scripts/wave2-phase1-options.php
 *   (or via Bash heredoc inline)
 */

defined('ABSPATH') || exit;

$updates = [
    // === TAB 1: Studio Info ===
    'studio_indirizzo_via'      => 'Via Vannella Gaetani, 27',
    'studio_cap_citta'          => '80121 Napoli',
    'studio_quartiere'          => 'Chiaia',
    // Cliente confermato 2026-04-28 (Ludovica session): orari 10:00-19:00.
    'studio_orari_settimana'    => 'Lun – Ven · 10:00 – 19:00',
    'studio_orari_sabato'       => 'Sabato su appuntamento',
    'studio_telefono_pubblico'  => '+39 081 1813 1119',
    'studio_email'              => 'info@studiolegalesaltelli.it',
    'studio_pec'                => 'emilianosaltelli@avvocatinapoli.legalmail.it',
    'studio_piva'               => '06685101211',
    'studio_ordine_avvocati'    => 'Ordine degli Avvocati di Napoli',

    // === TAB 2: Mappa (GPS ufficiali Google Business 2026-05-02) ===
    'studio_coordinate_lat'     => '40.8332541',
    'studio_coordinate_lng'     => '14.2414699',

    // === TAB 3: Brand ===
    'brand_payoff'              => 'Diritto, con misura',
    'brand_statement_short'     => "Un atelier legale italiano. Quattro avvocati a Chiaia. Vent'anni di pratica accanto a famiglie e imprese.",

    // === TAB 4: Footer ===
    'footer_credit_text'        => 'Realizzato da Adsolut Web Agency',
    'footer_credit_url'         => 'https://adsolut.it',
    'footer_newsletter_enabled' => true,
    // footer.php usa form Brevo legacy (id sib_signup_form_1 + endpoint link.studiolegalesaltelli.it)
    'footer_newsletter_provider' => 'brevo',

    // === TAB 5: Social (confermati 2026-04-28) ===
    'social_instagram'          => 'https://www.instagram.com/studiolegalesaltelli/',
    // LinkedIn: nessuna Company Page — il profilo Emiliano va su Person.sameAs (vedi memory)
    'social_linkedin'           => '',
    'social_twitter'            => '',
    'social_facebook'           => 'https://www.facebook.com/share/1D1jCY7BnW/',

    // === TAB 6: CTA Defaults ===
    'cta_default_label'         => 'Prenota un incontro →',
    'cta_default_url'           => '/contatti/',
    'cta_trust_signal'          => 'Risposta entro 24 ore · Riservatezza assoluta',
    'cta_subline_italic'        => 'Prima consulenza conoscitiva gratuita',
];

$ok = 0;
foreach ($updates as $name => $value) {
    $result = update_field($name, $value, 'options');
    $status = $result ? 'OK' : 'NO-CHANGE';
    printf("  [%s] %-30s = %s\n", $status, $name, is_bool($value) ? var_export($value, true) : substr((string) $value, 0, 60));
    if ($result) $ok++;
}

echo "\n";
echo "Phase 1 — Theme Options: $ok / " . count($updates) . " fields updated\n";
