<?php
/**
 * Wave 4.7.fix.4 — Strategy A FULL SCF migration: post_content → SCF metabox.
 *
 * Per le 7 Page WP target:
 *   1. Backup `post_content` originale in postmeta `_legacy_post_content_backup`
 *      (mai sovrascritto se già presente — recoverable).
 *   2. Per Page 2713 (richiedi-preventivo) SOLO: migra `post_content` →
 *      SCF field `body_content` + shadow `_body_content = field_info_body_content`.
 *      Gli altri 6 Pages NON necessitano migrazione perché:
 *        - 23 contatti, 2708 faq: template non chiama the_content() → zombie pre-existing
 *        - 2709, 2712, 2711, 372: SCF body_content già popolato (Wave 2 Content Migration)
 *      → post_content è zombie su 6/7 pagine, sarà cleared in Phase 5.
 *   3. NON svuota post_content qui (Phase 5 lo fa dopo template refactor confermato).
 *
 * Idempotency:
 *   - Backup `_legacy_post_content_backup`: skip se già non-empty (presence-based).
 *   - body_content migration (2713): skip se body_content SCF già non-empty.
 *
 * Pattern di riferimento: inc/migrations/wave4-7-fix-3-options-to-postmeta.php.
 *
 * Esecuzione (solo via WP-CLI):
 *   sudo -u www-data wp eval-file \
 *     wp-content/themes/saltelli/inc/migrations/wave4-7-fix-4-postcontent-to-scf.php \
 *     --path=/var/www/saltelli
 *
 * @package Saltelli
 * @since 1.3.10 Wave 4.7.fix.4
 */

defined('ABSPATH') || exit;

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

/**
 * Target page IDs + slug.
 * NB: tutte le 7 page sono in scope per backup _legacy_post_content_backup.
 * Solo Page 2713 (richiedi-preventivo) è in scope per body_content migration.
 */
function saltelli_w47fix4_target_pages() {
    return [
        23   => 'contatti',
        2708 => 'domande-frequenti',
        2709 => 'guide-gratuite',
        2712 => 'come-lavoriamo',
        2711 => 'prima-consulenza',
        372  => 'lavora-con-noi',
        2713 => 'richiedi-preventivo',
    ];
}

$backed_up   = 0;
$bk_skipped  = 0;
$migrated    = 0;
$mg_skipped  = 0;
$errors      = [];
$detail_log  = [];

// ============================================================================
// STEP 1: Backup post_content for all 7 pages.
// ============================================================================

foreach (saltelli_w47fix4_target_pages() as $page_id => $slug) {
    $page = get_post($page_id);
    if (!$page || $page->post_type !== 'page') {
        $errors[] = "Page ID $page_id ($slug) non esiste o non è di tipo 'page'";
        continue;
    }

    $existing_backup = get_post_meta($page_id, '_legacy_post_content_backup', true);
    if ($existing_backup !== '' && $existing_backup !== null && $existing_backup !== false) {
        $bk_skipped++;
        $detail_log[] = sprintf(
            'BACKUP SKIP: page %d (%s) — _legacy_post_content_backup già presente (%d chars)',
            $page_id, $slug, strlen((string) $existing_backup)
        );
        continue;
    }

    $post_content = $page->post_content;
    if ($post_content === '' || $post_content === null) {
        $bk_skipped++;
        $detail_log[] = sprintf('BACKUP SKIP: page %d (%s) — post_content è vuoto, nulla da backuppare', $page_id, $slug);
        continue;
    }

    update_post_meta($page_id, '_legacy_post_content_backup', $post_content);
    $backed_up++;
    $detail_log[] = sprintf(
        'BACKUP: page %d (%s) → _legacy_post_content_backup (%d chars saved)',
        $page_id, $slug, strlen($post_content)
    );
}

// ============================================================================
// STEP 2: Migrate post_content → body_content SCF per Page 2713 (richiedi-preventivo).
// ============================================================================

$target_id    = 2713;
$target_slug  = 'richiedi-preventivo';
$scf_field    = 'body_content';
$scf_key      = 'field_info_body_content';

$page = get_post($target_id);
if (!$page || $page->post_type !== 'page') {
    $errors[] = "Page ID $target_id ($target_slug) non esiste";
} else {
    // Idempotency: skip se SCF body_content già popolato non-empty.
    $existing_scf = get_post_meta($target_id, $scf_field, true);
    if ($existing_scf !== '' && $existing_scf !== null && $existing_scf !== false) {
        $mg_skipped++;
        $detail_log[] = sprintf(
            'MIGRATION SKIP: page %d (%s) — SCF %s già popolato (%d chars), skip per idempotency',
            $target_id, $target_slug, $scf_field, strlen((string) $existing_scf)
        );
    } else {
        $post_content = $page->post_content;
        if ($post_content === '' || $post_content === null) {
            $mg_skipped++;
            $detail_log[] = sprintf(
                'MIGRATION SKIP: page %d (%s) — post_content vuoto, nulla da migrare',
                $target_id, $target_slug
            );
        } else {
            // Migrate: write SCF body_content + shadow reference.
            update_post_meta($target_id, $scf_field, $post_content);
            update_post_meta($target_id, '_' . $scf_field, $scf_key);
            $migrated++;
            $detail_log[] = sprintf(
                'MIGRATION: page %d (%s) → SCF %s (%d chars), shadow _%s = %s',
                $target_id, $target_slug, $scf_field, strlen($post_content), $scf_field, $scf_key
            );
        }
    }
}

// ============================================================================
// OUTPUT REPORT
// ============================================================================

WP_CLI::log('=== Wave 4.7.fix.4 Migration Report ===');
WP_CLI::log("Backed up (post_content → _legacy_post_content_backup): {$backed_up}");
WP_CLI::log("Backup skipped (already present or post_content empty):  {$bk_skipped}");
WP_CLI::log("Migrated (post_content → SCF body_content):              {$migrated}");
WP_CLI::log("Migration skipped (SCF already populated or empty):       {$mg_skipped}");
WP_CLI::log('Errors:                                                   ' . count($errors));
WP_CLI::log('');

WP_CLI::log('[Detail log]');
foreach ($detail_log as $line) {
    WP_CLI::log('  ' . $line);
}

if (!empty($errors)) {
    WP_CLI::log('');
    WP_CLI::log('[Errors]');
    foreach ($errors as $err) {
        WP_CLI::warning($err);
    }
}

if (count($errors) === 0) {
    WP_CLI::success(sprintf(
        'Migration complete: %d backed up, %d migrated, %d skipped backup, %d skipped migration, 0 errors.',
        $backed_up, $migrated, $bk_skipped, $mg_skipped
    ));
} else {
    WP_CLI::error('Migration completed with ' . count($errors) . ' errors.', false);
}
