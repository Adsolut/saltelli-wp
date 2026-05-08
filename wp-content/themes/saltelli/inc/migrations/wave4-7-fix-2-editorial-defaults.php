<?php
/**
 * Wave 4.7.fix.2 Phase 1 — Migration: editorial defaults backfill.
 *
 * Targeted fix per il bug "Studio Section admin vuoto frontend pieno" segnalato
 * da Duccio. Lo seed Wave 4.7.fix Phase 2 (commit 2b1053d) ha popolato
 * `options_studio_body` con stringa vuota perché il JSON `default_value` era
 * `""`. Ora il JSON contiene i 3 paragrafi editoriali; questo script forza
 * l'allineamento DB↔JSON SOLO per chiavi attualmente vuote/assenti
 * (mantenendo idempotency rispetto a editing manuale di Elena).
 *
 * Lista chiavi: solo field EDITORIAL confermati nella validazione Phase 1.A
 * (vedi `.claude/knowledge/audits/wave4-7-fix-2-true-fix/01-phase3b-validation.md`).
 *
 * Esecuzione (locale Docker o droplet via SSH):
 *   wp eval-file wp-content/themes/saltelli/inc/migrations/wave4-7-fix-2-editorial-defaults.php
 *
 * Idempotency: chiave con valore non-empty viene SKIPPATA (Elena ha ownership).
 *
 * @package Saltelli
 * @since 1.3.8 Wave 4.7.fix.2 Phase 1
 */

defined('ABSPATH') || exit;

if (!function_exists('saltelli_w47fix2_backfill_editorial_defaults')) :
/**
 * @return array{updated:int,kept:int,absent:int,total:int,details:array<string,string>}
 */
function saltelli_w47fix2_backfill_editorial_defaults() {
    $report = [
        'updated' => 0,
        'kept'    => 0,
        'absent'  => 0,
        'total'   => 0,
        'details' => [],
    ];

    $json_path = get_template_directory() . '/acf-json/group_theme_options_v1.json';
    if (!file_exists($json_path)) {
        $report['details']['__error'] = "JSON not found: {$json_path}";
        return $report;
    }

    $raw = file_get_contents($json_path);
    $data = json_decode($raw, true);
    if (!is_array($data) || empty($data['fields'])) {
        $report['details']['__error'] = 'JSON malformed';
        return $report;
    }

    // Editorial fields: empty DB value → backfill da JSON default. Whitelist
    // esplicita per limitare il blast radius (NO walk universale).
    $editorial_field_names = [
        'studio_body',
    ];

    $defaults = [];
    $walk = function (array $fields, string $prefix = '') use (&$walk, &$defaults) {
        foreach ($fields as $f) {
            if (!is_array($f)) continue;
            $type = $f['type'] ?? '';
            if (in_array($type, ['tab', 'message', 'accordion', 'repeater'], true)) {
                continue;
            }
            $name = $f['name'] ?? '';
            if ($name === '') continue;
            $full = $prefix === '' ? $name : ($prefix . $name);
            if ($type === 'group') {
                $sub = $f['sub_fields'] ?? [];
                if (is_array($sub) && !empty($sub)) {
                    $walk($sub, $full . '_');
                }
                continue;
            }
            if (array_key_exists('default_value', $f)) {
                $defaults[$full] = $f['default_value'];
            }
        }
    };
    $walk($data['fields']);

    $sentinel = '__W47FIX2_ABSENT__';
    $report['total'] = count($editorial_field_names);

    foreach ($editorial_field_names as $name) {
        $opt_key = 'options_' . $name;
        $current = get_option($opt_key, $sentinel);
        $default = isset($defaults[$name]) ? (string) $defaults[$name] : '';

        if ($current === $sentinel) {
            // Chiave assente — lo seed standard la inserirà.
            if ($default !== '') {
                add_option($opt_key, $default, '', false);
                $report['absent']++;
                $report['details'][$opt_key] = 'absent → added (' . strlen($default) . ' chars)';
            } else {
                $report['details'][$opt_key] = 'absent + JSON default empty → skipped';
            }
            continue;
        }

        if ($current === '' || $current === null || $current === false) {
            // Chiave esistente ma vuota → backfill.
            update_option($opt_key, $default, false);
            $report['updated']++;
            $report['details'][$opt_key] = 'empty → updated (' . strlen($default) . ' chars)';
            continue;
        }

        // Chiave già popolata → ownership Elena.
        $report['kept']++;
        $preview = substr(wp_strip_all_tags((string) $current), 0, 60);
        $report['details'][$opt_key] = 'kept (current=' . strlen((string) $current) . ' chars: "' . $preview . '...")';
    }

    return $report;
}
endif;

$result = saltelli_w47fix2_backfill_editorial_defaults();

echo "Total editorial fields: {$result['total']}\n";
echo "Updated (empty → default): {$result['updated']}\n";
echo "Added (absent → default): {$result['absent']}\n";
echo "Kept (Elena ownership): {$result['kept']}\n";
if (!empty($result['details'])) {
    echo "\n[Details]\n";
    foreach ($result['details'] as $k => $v) {
        echo "  {$k}: {$v}\n";
    }
}
