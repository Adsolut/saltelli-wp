<?php
/**
 * Wave 4.6 — Migrazione idempotente field ACF page Lo Studio.
 *
 * Pre-popola `timeline_events` (group_lo_studio_v1) con i 6 eventi storici
 * hardcoded la prima volta che la page Lo Studio viene visitata in admin
 * (one-shot via admin_init). Skippa se già popolato (idempotency).
 *
 * Pattern derivato da Wave 1 ACF migration. Si attiva solo in admin per non
 * appesantire le request frontend.
 *
 * @package Saltelli
 * @since 1.3.2 Wave 4.6
 */
defined('ABSPATH') || exit;

if (!function_exists('saltelli_w46_migrate_lo_studio_timeline')) :
function saltelli_w46_migrate_lo_studio_timeline() {
    if (!function_exists('update_field') || !function_exists('get_field')) {
        return; // ACF non disponibile.
    }
    // La page Lo Studio è figlia di Chi Siamo (path: chi-siamo/lo-studio).
    $page = get_page_by_path('chi-siamo/lo-studio', OBJECT, 'page');
    if (!$page) {
        // Fallback: ricerca via slug (legacy compat se gerarchia cambia).
        $found = get_posts([
            'post_type'      => 'page',
            'name'           => 'lo-studio',
            'numberposts'    => 1,
            'post_status'    => 'publish',
        ]);
        $page = !empty($found) ? $found[0] : null;
    }
    if (!$page) {
        return;
    }

    $existing = get_field('timeline_events', $page->ID);
    if (is_array($existing) && count($existing) >= 3) {
        return; // Idempotente: già popolato.
    }

    $events = [
        ['year' => '2008', 'title' => 'Fondazione',         'description' => 'Emiliano Saltelli apre lo studio in Via Vannella Gaetani, focalizzato sul contenzioso tributario.'],
        ['year' => '2007', 'title' => 'Ingresso di Fabiana', 'description' => 'Si aggiunge la prima associate — area diritto del lavoro.'],
        ['year' => '2014', 'title' => 'Apertura LGBTQ+',     'description' => 'Antonia Battista inaugura una pratica dedicata, prima a Napoli sud.'],
        ['year' => '2019', 'title' => "Vent'anni",           'description' => 'Lo studio passa da 2 a 4 professionisti stabili. Atelier a tutti gli effetti.'],
        ['year' => '2024', 'title' => 'Cassazione + AGE',    'description' => 'Annullamento cartella €240k. Conferma in Cassazione su licenziamento illegittimo.'],
        ['year' => '2026', 'title' => 'Oggi',                'description' => '17 aree presidiate, 4 professionisti, un solo atelier.'],
    ];

    update_field('timeline_events', $events, $page->ID);

    $year_range = (string) get_field('timeline_year_range', $page->ID);
    if ($year_range === '') {
        update_field('timeline_year_range', '2008 → 2026.', $page->ID);
    }
}
endif;

add_action('admin_init', 'saltelli_w46_migrate_lo_studio_timeline', 999);

/**
 * Wave 4.6 — Migrazione idempotente Theme Options legacy.
 *
 * Pre-Wave 4.6 alcuni Theme Options erano già salvati in wp_options con
 * valori che non matchano la presentazione visiva legacy (es. ordine = "Ordine
 * degli Avvocati di Napoli", quando il footer hardcoded mostrava "Iscritto
 * Ordine Avvocati Napoli"). Wave 4.6 ora cabla questi field; per preservare
 * NO breaking change visivo, migriamo i wp_options legacy alla nuova baseline
 * matching i defaults ACF aggiornati.
 *
 * Idempotente: aggiorna solo se il valore corrente è il legacy noto. Non tocca
 * i valori che l'editor ha già modificato.
 */
if (!function_exists('saltelli_w46_migrate_legacy_options')) :
function saltelli_w46_migrate_legacy_options() {
    $migrations = [
        // option_key → [legacy_value, new_value]
        'options_studio_ordine_avvocati' => [
            'Ordine degli Avvocati di Napoli',
            'Iscritto Ordine Avvocati Napoli',
        ],
        'options_brand_statement_short'  => [
            "Un atelier legale italiano. Quattro avvocati a Chiaia. Vent'anni di pratica accanto a famiglie e imprese.",
            "Un atelier legale italiano.\nQuattro avvocati a Chiaia.\nVent'anni di pratica accanto\na famiglie e imprese.",
        ],
    ];

    foreach ($migrations as $key => [$legacy, $target]) {
        $current = get_option($key, null);
        if ($current === $legacy) {
            update_option($key, $target);
        }
    }
}
endif;

add_action('admin_init', 'saltelli_w46_migrate_legacy_options', 999);
