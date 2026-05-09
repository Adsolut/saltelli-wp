<?php
/**
 * Wave 4.7.fix.3 — Migrate SCF data from wp_options to wp_postmeta.
 *
 * Migra ~30 chiavi `options_<field>` (Theme Options, Saltelli Settings) ai
 * rispettivi `<page_id>:<field>` postmeta delle 4 Page WP target:
 *   - Page 17    (slug=home)               → 12 field (Hero, Studio Section, Team & Casi, Press)
 *   - Page 2822  (slug=chi-siamo)          → 4 field
 *   - Page 2812  (slug=aree-di-pratica)    → 10 field (Hero + 6 cluster cards)
 *   - Page 2813  (slug=risorse)            → 4 field
 *
 * Idempotency: presence-based check sul postmeta destination. Se già popolato
 * (non-empty), NON sovrascrive (safe re-run). Le chiavi `options_*` sorgenti
 * NON vengono cancellate da questo script (cleanup deferred a Phase 4).
 *
 * Repeater handling (press_outlets):
 *   wp_options[options_press_outlets] = "N" (row count, scalar)
 *   wp_options[options_press_outlets_<i>_<sub>] = value per ogni sub-field
 *   → postmeta[press_outlets] = "N" + meta[_press_outlets] = field_press_outlets
 *   → postmeta[press_outlets_<i>_<sub>] = value + meta[_press_outlets_<i>_<sub>] = sub_field_key
 *
 * SCF reference convention: ogni postmeta key ha shadow `_<key>` con la
 * SCF field key (es. `_studio_body` = `field_studio_body`). Senza questa
 * shadow meta, SCF non sa che il postmeta è un suo field e non lo rendrà
 * editabile correttamente nel metabox admin.
 *
 * Esecuzione (solo via WP-CLI):
 *   sudo -u www-data wp eval-file \
 *     wp-content/themes/saltelli/inc/migrations/wave4-7-fix-3-options-to-postmeta.php \
 *     --path=/var/www/saltelli
 *
 * @package Saltelli
 * @since 1.3.9 Wave 4.7.fix.3
 */

defined('ABSPATH') || exit;

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

/**
 * Migration plan: lista [option_key, page_id, postmeta_key, scf_field_key].
 *
 * NB: il 4° elemento (scf_field_key) DEVE corrispondere alla chiave SCF
 * definita in acf-json/group_*_v1.json — verificato manualmente.
 */
function saltelli_w47fix3_migration_plan() {
    return [
        // === Page 17 (Homepage, slug=home) — 12 field ===
        ['options_hero_eyebrow',                   17, 'hero_eyebrow',                   'field_hero_eyebrow'],
        ['options_hero_headline',                  17, 'hero_headline',                  'field_hero_headline'],
        ['options_hero_subheadline',               17, 'hero_subheadline',               'field_hero_subheadline'],
        ['options_hero_cta_label',                 17, 'hero_cta_label',                 'field_hero_cta_label'],
        ['options_hero_cta_url',                   17, 'hero_cta_url',                   'field_hero_cta_url'],
        ['options_studio_titolo_sezione',          17, 'studio_titolo_sezione',          'field_studio_titolo_sezione'],
        ['options_studio_body',                    17, 'studio_body',                    'field_studio_body'],
        ['options_studio_foto_facciata',           17, 'studio_foto_facciata',           'field_studio_foto_facciata'],
        ['options_team_titolo',                    17, 'team_titolo',                    'field_team_titolo'],
        ['options_cases_titolo',                   17, 'cases_titolo',                   'field_cases_titolo'],
        ['options_casi_rappresentativi_home',      17, 'casi_rappresentativi_home',      'field_casi_rappresentativi_home'],
        ['options_press_outlets',                  17, 'press_outlets',                  'field_press_outlets'],

        // === Page 2822 (Chi Siamo) — 4 field ===
        ['options_hub_chisiamo_eyebrow',           2822, 'hub_chisiamo_eyebrow',         'field_hub_chisiamo_eyebrow'],
        ['options_hub_chisiamo_h1_main',           2822, 'hub_chisiamo_h1_main',         'field_hub_chisiamo_h1_main'],
        ['options_hub_chisiamo_h1_emphasis',       2822, 'hub_chisiamo_h1_emphasis',     'field_hub_chisiamo_h1_emphasis'],
        ['options_hub_chisiamo_intro',             2822, 'hub_chisiamo_intro',           'field_hub_chisiamo_intro'],

        // === Page 2812 (Aree di Pratica) — 10 field ===
        ['options_hub_aree_eyebrow',               2812, 'hub_aree_eyebrow',             'field_hub_aree_eyebrow'],
        ['options_hub_aree_h1_main',               2812, 'hub_aree_h1_main',             'field_hub_aree_h1_main'],
        ['options_hub_aree_h1_emphasis',           2812, 'hub_aree_h1_emphasis',         'field_hub_aree_h1_emphasis'],
        ['options_hub_aree_intro',                 2812, 'hub_aree_intro',               'field_hub_aree_intro'],
        ['options_hub_aree_cluster_privati_label', 2812, 'hub_aree_cluster_privati_label', 'field_hub_aree_cluster_privati_label'],
        ['options_hub_aree_cluster_privati_desc',  2812, 'hub_aree_cluster_privati_desc',  'field_hub_aree_cluster_privati_desc'],
        ['options_hub_aree_cluster_imprese_label', 2812, 'hub_aree_cluster_imprese_label', 'field_hub_aree_cluster_imprese_label'],
        ['options_hub_aree_cluster_imprese_desc',  2812, 'hub_aree_cluster_imprese_desc',  'field_hub_aree_cluster_imprese_desc'],
        ['options_hub_aree_cluster_contenzioso_label', 2812, 'hub_aree_cluster_contenzioso_label', 'field_hub_aree_cluster_contenzioso_label'],
        ['options_hub_aree_cluster_contenzioso_desc',  2812, 'hub_aree_cluster_contenzioso_desc',  'field_hub_aree_cluster_contenzioso_desc'],

        // === Page 2813 (Risorse) — 4 field ===
        ['options_hub_risorse_eyebrow',            2813, 'hub_risorse_eyebrow',          'field_hub_risorse_eyebrow'],
        ['options_hub_risorse_h1_main',            2813, 'hub_risorse_h1_main',          'field_hub_risorse_h1_main'],
        ['options_hub_risorse_h1_emphasis',        2813, 'hub_risorse_h1_emphasis',      'field_hub_risorse_h1_emphasis'],
        ['options_hub_risorse_intro',              2813, 'hub_risorse_intro',            'field_hub_risorse_intro'],
    ];
}

/**
 * Repeater sub-fields plan: per ogni repeater root, migra anche tutte le row sub-keys.
 * Pattern: options_<repeater>_<index>_<sub_field> → postmeta <repeater>_<index>_<sub_field>
 * + shadow `_<key>` con la sub_field key SCF.
 */
function saltelli_w47fix3_repeater_subfields_plan() {
    return [
        'press_outlets' => [
            'page_id' => 17,
            'sub_fields' => [
                'name' => 'field_press_outlet_name',
                'logo' => 'field_press_outlet_logo',
                'url'  => 'field_press_outlet_url',
            ],
        ],
    ];
}

$migrated  = 0;
$skipped   = 0;
$errors    = [];
$detail_log = [];

foreach (saltelli_w47fix3_migration_plan() as [$opt_key, $page_id, $meta_key, $scf_key]) {
    // Verifica che la Page esista (sanity check)
    $page = get_post($page_id);
    if (!$page || $page->post_type !== 'page') {
        $errors[] = "Page ID $page_id non esiste o non è di tipo 'page'";
        continue;
    }

    $opt_value = get_option($opt_key, '__SENTINEL__');
    if ($opt_value === '__SENTINEL__') {
        $skipped++;
        $detail_log[] = sprintf('SKIP: %s (option non presente)', $opt_key);
        continue;
    }

    // Idempotency check: se postmeta destination already populated (non-empty), skip
    $existing = get_post_meta($page_id, $meta_key, true);
    if ($existing !== '' && $existing !== null && $existing !== false) {
        $skipped++;
        $detail_log[] = sprintf('SKIP: %s → page %d:%s (postmeta già popolato: %s)',
            $opt_key, $page_id, $meta_key,
            is_string($existing) ? substr($existing, 0, 40) : '(non-string)'
        );
        continue;
    }

    // Empty option value: lasciamo postmeta vuoto, ma scriviamo lo shadow `_<key>` per
    // far sì che SCF riconosca il field nel metabox UI.
    $is_empty = ($opt_value === '' || $opt_value === null || $opt_value === false ||
                 (is_array($opt_value) && empty($opt_value)));

    if ($is_empty) {
        // Solo shadow meta, no value
        update_post_meta($page_id, '_' . $meta_key, $scf_key);
        $migrated++;
        $detail_log[] = sprintf('MIG (empty): %s → page %d shadow only (_%s = %s)',
            $opt_key, $page_id, $meta_key, $scf_key
        );
        continue;
    }

    // Migra il valore + scrivi shadow SCF reference
    update_post_meta($page_id, $meta_key, $opt_value);
    update_post_meta($page_id, '_' . $meta_key, $scf_key);
    $migrated++;
    $detail_log[] = sprintf('MIG: %s → page %d:%s = %s',
        $opt_key, $page_id, $meta_key,
        is_string($opt_value) ? substr($opt_value, 0, 60) : '(non-string ' . gettype($opt_value) . ')'
    );
}

// Repeater sub-fields migration
foreach (saltelli_w47fix3_repeater_subfields_plan() as $rep_name => $rep_config) {
    $page_id = $rep_config['page_id'];
    $sub_fields = $rep_config['sub_fields'];

    // Determina il count dal repeater root option
    $row_count = (int) get_option('options_' . $rep_name, 0);
    if ($row_count <= 0) {
        $detail_log[] = sprintf('REPEATER: %s row_count=0, no sub-fields da migrare', $rep_name);
        continue;
    }

    for ($i = 0; $i < $row_count; $i++) {
        foreach ($sub_fields as $sub_name => $sub_field_key) {
            $opt_key = sprintf('options_%s_%d_%s', $rep_name, $i, $sub_name);
            $meta_key = sprintf('%s_%d_%s', $rep_name, $i, $sub_name);

            $opt_value = get_option($opt_key, '__SENTINEL__');
            if ($opt_value === '__SENTINEL__') {
                $skipped++;
                continue;
            }

            $existing = get_post_meta($page_id, $meta_key, true);
            if ($existing !== '' && $existing !== null && $existing !== false) {
                $skipped++;
                continue;
            }

            update_post_meta($page_id, $meta_key, $opt_value);
            update_post_meta($page_id, '_' . $meta_key, $sub_field_key);
            $migrated++;
            $detail_log[] = sprintf('MIG repeater: %s → page %d:%s = %s',
                $opt_key, $page_id, $meta_key,
                is_string($opt_value) ? substr($opt_value, 0, 40) : '(non-string)'
            );
        }
    }
}

// Output report
WP_CLI::log("=== Wave 4.7.fix.3 Migration Report ===");
WP_CLI::log("Migrated: {$migrated}");
WP_CLI::log("Skipped:  {$skipped}");
WP_CLI::log("Errors:   " . count($errors));
WP_CLI::log("");

WP_CLI::log("[Detail log]");
foreach ($detail_log as $line) {
    WP_CLI::log('  ' . $line);
}

if (!empty($errors)) {
    WP_CLI::log("");
    WP_CLI::log("[Errors]");
    foreach ($errors as $err) {
        WP_CLI::warning($err);
    }
}

if (count($errors) === 0) {
    WP_CLI::success("Migration complete: $migrated migrated, $skipped skipped, 0 errors.");
} else {
    WP_CLI::error("Migration completed with " . count($errors) . " errors.", false);
}
