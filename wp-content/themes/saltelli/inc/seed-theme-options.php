<?php
/**
 * Wave 4.7.fix Phase 2 — Seed Theme Options da default_value JSON.
 *
 * Script idempotente: legge `acf-json/group_theme_options_v1.json` e popola
 * le chiavi `options_<name>` mancanti in `wp_options` con i `default_value`
 * dalla field group definition. NON sovrascrive le chiavi già popolate da
 * Wave 4.6 migration (presence-based check, non value-based).
 *
 * Contesto: post-switch ACF Free → SCF (Wave 4.7.fix Phase 1) la options
 * page `saltelli-settings` esiste e i field group sono caricati, ma solo
 * 26/50 chiavi sono popolate in DB. I 24 mancanti cadono sui fallback
 * hardcoded DEC-029 nel frontend. Questo script chiude il gap.
 *
 * Esecuzione (solo staging via SSH):
 *   sudo -u www-data wp eval-file /tmp/seed-theme-options.php --path=/var/www/saltelli
 *
 * Idempotency: chiave esistente (anche con valore stringa vuota) viene SKIPPATA.
 * autoload=false per consistency con Wave 4.6 baseline (evita bloat options autoload).
 *
 * Hard rules:
 * - NO require/include in functions.php — file dormiente, eseguibile solo on-demand.
 * - NO overwrite Wave 4.6: 26 chiavi esistenti restano intoccate.
 *
 * @package Saltelli
 * @since 1.3.6 Wave 4.7.fix Phase 2
 */

defined('ABSPATH') || exit;

if (!function_exists('saltelli_w47fix_seed_theme_options')) :
/**
 * Esegue il seed dei Theme Options mancanti.
 *
 * @return array{seeded:int,skipped:int,total:int,seeded_keys:array<int,string>,skipped_keys:array<int,string>,warnings:array<int,string>}
 */
function saltelli_w47fix_seed_theme_options() {
    $report = [
        'seeded'       => 0,
        'skipped'      => 0,
        'total'        => 0,
        'seeded_keys'  => [],
        'skipped_keys' => [],
        'warnings'     => [],
    ];

    $json_path = get_template_directory() . '/acf-json/group_theme_options_v1.json';
    if (!file_exists($json_path)) {
        $report['warnings'][] = "JSON not found: {$json_path}";
        return $report;
    }

    $raw = file_get_contents($json_path);
    if ($raw === false) {
        $report['warnings'][] = "JSON unreadable: {$json_path}";
        return $report;
    }

    $data = json_decode($raw, true);
    if (!is_array($data) || empty($data['fields'])) {
        $report['warnings'][] = 'JSON malformed or no fields[] root.';
        return $report;
    }

    // Tipi field che hanno un default_value sensato per seed.
    $seedable_types = [
        'text', 'textarea', 'email', 'url', 'number', 'image', 'wysiwyg',
        'select', 'radio', 'checkbox', 'true_false', 'link', 'post_object',
    ];

    $sentinel = '__W47FIX_SEED_NULL__';

    /**
     * Walk ricorsivo dei field definitions.
     *
     * @param array  $fields Array fields[] dal JSON.
     * @param string $prefix Prefisso name accumulato (per group nested).
     * @return array<int,array{name:string,type:string,default:mixed}>
     */
    $walk = function (array $fields, string $prefix = '') use (&$walk, $seedable_types) {
        $collected = [];
        foreach ($fields as $f) {
            if (!is_array($f)) {
                continue;
            }
            $type = isset($f['type']) ? (string) $f['type'] : '';
            if ($type === 'tab' || $type === 'message' || $type === 'accordion') {
                continue;
            }
            $name = isset($f['name']) ? (string) $f['name'] : '';
            if ($name === '') {
                continue;
            }
            $full = $prefix === '' ? $name : ($prefix . $name);

            if ($type === 'repeater') {
                // Repeater root: la chiave options_<name> contiene il count rows.
                // Default JSON quasi sempre vuoto/0; setta 0 per consistency ACF.
                $default = $f['default_value'] ?? '';
                $row_count = 0;
                if (is_array($default)) {
                    $row_count = count($default);
                }
                $collected[] = [
                    'name'    => $full,
                    'type'    => 'repeater',
                    'default' => $row_count,
                ];
                // sub_fields del repeater: NON seedati (richiederebbero default_value
                // riga-per-riga, che non esiste nel JSON; ACF crea le righe runtime
                // quando l'editor aggiunge entries).
                continue;
            }

            if ($type === 'group') {
                // Group: ricorsivo su sub_fields con prefix <name>_.
                $sub = $f['sub_fields'] ?? [];
                if (is_array($sub) && !empty($sub)) {
                    $collected = array_merge($collected, $walk($sub, $full . '_'));
                }
                continue;
            }

            if (in_array($type, $seedable_types, true)) {
                $collected[] = [
                    'name'    => $full,
                    'type'    => $type,
                    'default' => $f['default_value'] ?? '',
                ];
            }
        }
        return $collected;
    };

    $fields = $walk($data['fields']);
    $report['total'] = count($fields);

    foreach ($fields as $field) {
        $option_name = 'options_' . $field['name'];
        $default     = $field['default'];
        $type        = $field['type'];

        // Idempotency presence-based: se la row esiste (anche valore vuoto), skippa.
        // get_option ritorna $sentinel SOLO se la chiave è completamente assente.
        $current = get_option($option_name, $sentinel);
        if ($current !== $sentinel) {
            $report['skipped']++;
            $report['skipped_keys'][] = $option_name;
            continue;
        }

        // Coerce default per tipo.
        $value = $default;
        switch ($type) {
            case 'true_false':
                // ACF salva boolean come "1" o "0".
                $value = ($default === true || $default === 1 || $default === '1') ? '1' : '0';
                break;
            case 'image':
            case 'post_object':
                // Default vuoto = no attachment / no post selezionato.
                if ($default === '' || $default === null) {
                    $value = '';
                }
                break;
            case 'repeater':
                $value = (string) (int) $default;
                break;
            default:
                if (is_array($default)) {
                    // Edge case: default array per tipi scalar — serializza.
                    $value = $default;
                } elseif ($default === null) {
                    $value = '';
                } else {
                    $value = (string) $default;
                }
        }

        // add_option con autoload=false (consistency Wave 4.6).
        // add_option ritorna false se la chiave già esiste, ma abbiamo già
        // verificato con get_option+sentinel quindi non dovrebbe accadere.
        $added = add_option($option_name, $value, '', false);

        if ($added) {
            $report['seeded']++;
            $report['seeded_keys'][] = $option_name;
        } else {
            $report['warnings'][] = "add_option failed for {$option_name} (race or pre-existing)";
            $report['skipped']++;
            $report['skipped_keys'][] = $option_name;
        }
    }

    return $report;
}
endif;

// ============================================================================
// EXECUTION (via wp eval-file).
// ============================================================================

$result = saltelli_w47fix_seed_theme_options();

echo "Seeded: {$result['seeded']}\n";
echo "Skipped: {$result['skipped']}\n";
echo "Total walked: {$result['total']}\n";

if (!empty($result['seeded_keys'])) {
    echo "\n[Seeded keys]\n";
    foreach ($result['seeded_keys'] as $k) {
        echo "  + {$k}\n";
    }
}

if (!empty($result['skipped_keys'])) {
    echo "\n[Skipped keys]\n";
    foreach ($result['skipped_keys'] as $k) {
        echo "  = {$k}\n";
    }
}

if (!empty($result['warnings'])) {
    echo "\n[Warnings]\n";
    foreach ($result['warnings'] as $w) {
        echo "  ! {$w}\n";
    }
}
