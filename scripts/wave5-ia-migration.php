<?php
/**
 * Wave 5 IA Refactor — Migration DB script (idempotente)
 *
 * Applica al DB la migrazione IA Wave 5 (sync con codice theme `inc/seo/legacy-redirects.php`):
 *
 * Phase 1 — Opzione B chi-siamo
 *   - rinomina pagina esistente `chi-siamo` (ex Lo Studio) → slug `lo-studio`
 *   - crea nuovo hub top-level `chi-siamo`
 *   - reparenta `lo-studio` come child di `chi-siamo`
 *
 * Phase 2 — Hub pages (top-level)
 *   - crea `aree-di-pratica`, `risorse`
 *   - rinomina `costi` (page esistente) → `costi-e-consulenze` come hub root
 *
 * Phase 3 — Rename + reparent 8 pagine
 *   - casi → risultati (parent: chi-siamo)
 *   - faq → domande-frequenti (parent: risorse)
 *   - glossario-legale (parent: risorse)
 *   - guide-gratuite (parent: risorse)
 *   - come-lavoriamo (parent: costi-e-consulenze)
 *   - prima-consulenza (parent: costi-e-consulenze)
 *   - richiedi-preventivo (parent: costi-e-consulenze)
 *   - lavora-con-noi (parent: contatti)
 *
 * Phase 4 — Flush rewrite + cache
 *
 * Eseguire:
 *     sudo -u www-data wp eval-file /tmp/wave5-ia-migration.php --path=/var/www/saltelli
 *
 * Idempotente: ogni operazione verifica lo stato corrente prima di applicare.
 *
 * @package Saltelli
 * @since 1.1.0  Wave 5 IA Refactor
 */

if (!defined('ABSPATH')) {
    exit;
}

echo "=== Wave 5 IA Refactor — DB migration ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

$w5_stats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'failed' => 0];

function w5_get_page($slug) {
    return get_page_by_path($slug, OBJECT, 'page');
}

function w5_create_hub($slug, $title) {
    global $w5_stats;
    $existing = w5_get_page($slug);
    if ($existing) {
        echo "  [skip]    create '{$slug}' (già presente, ID {$existing->ID})\n";
        $w5_stats['skipped']++;
        return $existing->ID;
    }
    $id = wp_insert_post([
        'post_type'    => 'page',
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_status'  => 'publish',
        'post_content' => '',
        'post_parent'  => 0,
    ], true);
    if (is_wp_error($id)) {
        echo "  [FAIL]    create '{$slug}': " . $id->get_error_message() . "\n";
        $w5_stats['failed']++;
        return null;
    }
    echo "  [created] '{$slug}' (ID {$id})\n";
    $w5_stats['created']++;
    return $id;
}

function w5_rename_or_reparent($current_slug, $target_slug, $target_parent_id) {
    global $w5_stats;

    $current = w5_get_page($current_slug);

    if ($current_slug === $target_slug) {
        if (!$current) {
            echo "  [skip]    rename '{$current_slug}' (pagina non trovata)\n";
            $w5_stats['skipped']++;
            return null;
        }
        if ((int)$current->post_parent === (int)$target_parent_id) {
            echo "  [skip]    '{$current_slug}' (già parent={$target_parent_id})\n";
            $w5_stats['skipped']++;
            return $current->ID;
        }
        $r = wp_update_post(['ID' => $current->ID, 'post_parent' => $target_parent_id], true);
        if (is_wp_error($r)) {
            echo "  [FAIL]    reparent '{$current_slug}': " . $r->get_error_message() . "\n";
            $w5_stats['failed']++;
            return null;
        }
        echo "  [reparent] '{$current_slug}' → parent={$target_parent_id}\n";
        $w5_stats['updated']++;
        return $current->ID;
    }

    if (!$current) {
        $alt = w5_get_page($target_slug);
        if ($alt) {
            if ((int)$alt->post_parent === (int)$target_parent_id) {
                echo "  [skip]    '{$current_slug}'→'{$target_slug}' (già in target state, ID {$alt->ID})\n";
                $w5_stats['skipped']++;
                return $alt->ID;
            }
            $r = wp_update_post(['ID' => $alt->ID, 'post_parent' => $target_parent_id], true);
            if (is_wp_error($r)) {
                echo "  [FAIL]    reparent '{$target_slug}': " . $r->get_error_message() . "\n";
                $w5_stats['failed']++;
                return null;
            }
            echo "  [reparent] '{$target_slug}' → parent={$target_parent_id}\n";
            $w5_stats['updated']++;
            return $alt->ID;
        }
        echo "  [skip]    '{$current_slug}'→'{$target_slug}' (nessuna pagina sorgente né target)\n";
        $w5_stats['skipped']++;
        return null;
    }

    // current esiste → rinomina + reparent
    $r = wp_update_post([
        'ID'          => $current->ID,
        'post_name'   => $target_slug,
        'post_parent' => $target_parent_id,
    ], true);
    if (is_wp_error($r)) {
        echo "  [FAIL]    rename '{$current_slug}'→'{$target_slug}': " . $r->get_error_message() . "\n";
        $w5_stats['failed']++;
        return null;
    }
    echo "  [renamed]  '{$current_slug}' → '{$target_slug}' (ID {$current->ID}, parent={$target_parent_id})\n";
    $w5_stats['updated']++;
    return $current->ID;
}

// ============================================================
// Phase 1 — Opzione B per chi-siamo
// ============================================================
echo "Phase 1 — chi-siamo Opzione B (rinomina existing → lo-studio + crea hub chi-siamo)\n";

$existing_chi_siamo = w5_get_page('chi-siamo');
$existing_lo_studio = w5_get_page('lo-studio');

if ($existing_chi_siamo && !$existing_lo_studio) {
    // L'attuale chi-siamo è la pagina "Lo Studio" rinominata in passato → step 1: rinomina a lo-studio
    $r = wp_update_post([
        'ID'        => $existing_chi_siamo->ID,
        'post_name' => 'lo-studio',
    ], true);
    if (is_wp_error($r)) {
        echo "  [FAIL]    rename chi-siamo→lo-studio: " . $r->get_error_message() . "\n";
        $w5_stats['failed']++;
    } else {
        echo "  [renamed]  'chi-siamo' (ex Lo Studio) → 'lo-studio' (ID {$existing_chi_siamo->ID})\n";
        $w5_stats['updated']++;
    }
} elseif ($existing_chi_siamo && $existing_lo_studio) {
    echo "  [info]    sia 'chi-siamo' sia 'lo-studio' esistono → assumo Opzione B già applicata, skip rename step1\n";
    $w5_stats['skipped']++;
} else {
    echo "  [info]    nessuna pagina 'chi-siamo' esistente, salto Opzione B step1\n";
    $w5_stats['skipped']++;
}

// step 2: crea hub chi-siamo
$chi_siamo_id = w5_create_hub('chi-siamo', 'Chi Siamo');

// step 3: reparent lo-studio sotto chi-siamo (se entrambi esistono)
if ($chi_siamo_id) {
    $lo_studio = w5_get_page('lo-studio');
    if ($lo_studio) {
        if ((int)$lo_studio->post_parent !== (int)$chi_siamo_id) {
            wp_update_post(['ID' => $lo_studio->ID, 'post_parent' => $chi_siamo_id]);
            echo "  [reparent] 'lo-studio' → parent chi-siamo (ID {$chi_siamo_id})\n";
            $w5_stats['updated']++;
        } else {
            echo "  [skip]    'lo-studio' già sotto chi-siamo\n";
            $w5_stats['skipped']++;
        }
    }
}

echo "\n";

// ============================================================
// Phase 2 — Hub pages aree-di-pratica, risorse, costi-e-consulenze
// ============================================================
echo "Phase 2 — Hub pages (aree-di-pratica, risorse, costi-e-consulenze)\n";

$aree_id    = w5_create_hub('aree-di-pratica', 'Aree di Pratica');
$risorse_id = w5_create_hub('risorse', 'Risorse');

// costi-e-consulenze: se esiste 'costi' page → rinomina; altrimenti crea hub
$existing_costi = w5_get_page('costi');
$existing_cec   = w5_get_page('costi-e-consulenze');
$cec_id         = null;

if ($existing_cec) {
    $cec_id = $existing_cec->ID;
    echo "  [skip]    'costi-e-consulenze' già presente (ID {$cec_id})\n";
    $w5_stats['skipped']++;
} elseif ($existing_costi) {
    $r = wp_update_post([
        'ID'          => $existing_costi->ID,
        'post_name'   => 'costi-e-consulenze',
        'post_title'  => 'Costi e Consulenze',
        'post_parent' => 0,
    ], true);
    if (is_wp_error($r)) {
        echo "  [FAIL]    rename costi→costi-e-consulenze: " . $r->get_error_message() . "\n";
        $w5_stats['failed']++;
    } else {
        $cec_id = $existing_costi->ID;
        echo "  [renamed]  'costi' → 'costi-e-consulenze' (ID {$cec_id}, hub root)\n";
        $w5_stats['updated']++;
    }
} else {
    $cec_id = w5_create_hub('costi-e-consulenze', 'Costi e Consulenze');
}

echo "\n";

// ============================================================
// Phase 3 — Page renames + reparent
// ============================================================
echo "Phase 3 — Page renames + reparent (8 pagine)\n";

$contatti = w5_get_page('contatti');
$contatti_id = $contatti ? (int)$contatti->ID : 0;

$moves = [
    ['casi',                'risultati',           $chi_siamo_id ? (int)$chi_siamo_id : 0],
    ['faq',                 'domande-frequenti',   $risorse_id   ? (int)$risorse_id   : 0],
    ['glossario-legale',    'glossario-legale',    $risorse_id   ? (int)$risorse_id   : 0],
    ['guide-gratuite',      'guide-gratuite',      $risorse_id   ? (int)$risorse_id   : 0],
    ['come-lavoriamo',      'come-lavoriamo',      $cec_id       ? (int)$cec_id       : 0],
    ['prima-consulenza',    'prima-consulenza',    $cec_id       ? (int)$cec_id       : 0],
    ['richiedi-preventivo', 'richiedi-preventivo', $cec_id       ? (int)$cec_id       : 0],
    ['lavora-con-noi',      'lavora-con-noi',      $contatti_id],
];

foreach ($moves as $m) {
    list($cur, $tgt, $parent) = $m;
    w5_rename_or_reparent($cur, $tgt, $parent);
}

echo "\n";

// ============================================================
// Phase 4 — Flush rewrite + cache
// ============================================================
echo "Phase 4 — Flush rewrite + cache\n";

flush_rewrite_rules(true);
wp_cache_flush();

echo "  [done]    rewrite rules + object cache flushed\n";
echo "\n";

// ============================================================
// Summary
// ============================================================
echo "=== Summary ===\n";
printf("Created: %d · Updated: %d · Skipped: %d · Failed: %d\n",
    $w5_stats['created'], $w5_stats['updated'], $w5_stats['skipped'], $w5_stats['failed']);
echo "Done at: " . date('Y-m-d H:i:s') . "\n";

if ($w5_stats['failed'] > 0) {
    echo "\n⚠ Alcune operazioni FALLITE — review log sopra.\n";
}
