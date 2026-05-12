<?php
/**
 * Migration script — CPT `competenza` post_content → body_extended SCF wysiwyg.
 *
 * Wave 6.0 partial pre-cut produzione.
 * ────────────────────────────────────────────────────
 * Sblocca Elena su CPT competenza con post_content classic HTML compilato
 * pre-Gutenberg (es. post 2670 `responsabilita-medica`): Gutenberg moderno
 * apre quel content in 1 "Blocco classico" monolitico → UX confusa.
 *
 * Dopo migration TUTTE le 19 CPT competenza userano body_extended come
 * canonical body source (pattern SCF-only Wave Elena FB Batch 2 #23).
 *
 * Prerequisito: template `single-competenza.php` già patchato per renderare
 * `body_extended` via `apply_filters('the_content', $body_ext)` invece di
 * `wp_kses_post()` — allineamento semantic con post_content render
 * (wpautop + shortcode + oEmbed). Frontend pixel-identical pre/post migration.
 *
 * ## Esecuzione (WP-CLI)
 *
 * ```sh
 * # DRY-RUN (default, mostra cosa farebbe senza modificare DB):
 * ssh deploy@178.62.207.50 "sudo -u www-data wp eval-file /var/www/saltelli/wp-content/themes/saltelli/scripts/migrate-cpt-competenza-content.php --dry-run --path=/var/www/saltelli"
 *
 * # WET-RUN (esegui modifiche reali su DB):
 * ssh deploy@178.62.207.50 "sudo -u www-data wp eval-file /var/www/saltelli/wp-content/themes/saltelli/scripts/migrate-cpt-competenza-content.php --wet-run --path=/var/www/saltelli"
 * ```
 *
 * ## Rollback per singolo post
 *
 * ```sh
 * # 1. estrai il backup dal postmeta legacy:
 * ssh deploy@178.62.207.50 "sudo -u www-data wp post meta get {ID} _legacy_post_content_backup --path=/var/www/saltelli" > /tmp/restore.html
 *
 * # 2. restora il post_content:
 * ssh deploy@178.62.207.50 "sudo -u www-data wp post update {ID} --post_content=\"\$(cat /tmp/restore.html)\" --path=/var/www/saltelli"
 *
 * # 3. svuota body_extended SCF:
 * ssh deploy@178.62.207.50 "sudo -u www-data wp post meta delete {ID} body_extended --path=/var/www/saltelli"
 * ```
 *
 * ## Logica per ogni post
 *
 *   1. SKIP `already`   se body_extended postmeta già non-vuota (idempotent re-run)
 *   2. SKIP `empty`     se post_content vuoto (niente da migrare)
 *   3. SKIP `gutenberg` se post_content contiene blocchi Gutenberg moderni `<!-- wp: -->`
 *                       (rischio rottura serialization — richiede gestione manuale)
 *   4. MIGRATED         backup `_legacy_post_content_backup` postmeta → copia in `body_extended`
 *                       → svuota post_content via wp_update_post()
 *
 * @package Saltelli
 */

// Hard guard: only WP-CLI execution.
if (! defined('WP_CLI') || ! WP_CLI) {
    if (function_exists('error_log')) {
        error_log('[migrate-cpt-competenza-content] Refusing to run outside WP-CLI context.');
    }
    return;
}

// Default to dry-run (fail-safe). Wet-run requires explicit `--wet-run`.
$sl_args = isset($args) && is_array($args) ? $args : [];
$sl_mode = in_array('--wet-run', $sl_args, true) ? 'wet' : 'dry';

WP_CLI::log('');
WP_CLI::log('================================================================');
WP_CLI::log(sprintf(' migrate-cpt-competenza-content — mode: %s', strtoupper($sl_mode)));
WP_CLI::log('================================================================');
if ($sl_mode === 'dry') {
    WP_CLI::log(' DRY-RUN: no DB writes. Add --wet-run to execute migration.');
}
WP_CLI::log('');

// Fetch ALL competenza CPT (any status, defensive — but operate only on publish).
$sl_posts = get_posts([
    'post_type'      => 'competenza',
    'post_status'    => 'any',
    'posts_per_page' => -1,
    'orderby'        => 'ID',
    'order'          => 'ASC',
    'suppress_filters' => true,
]);

if (empty($sl_posts)) {
    WP_CLI::warning('No CPT competenza found. Nothing to do.');
    return;
}

$sl_counts = [
    'migrated'        => 0,
    'skip_already'    => 0,
    'skip_empty'      => 0,
    'skip_gutenberg'  => 0,
    'skip_not_publish'=> 0,
    'error'           => 0,
];

// Table header.
WP_CLI::log(sprintf('%-6s | %-40s | %-50s | %-10s | %s', 'ID', 'slug', 'title', 'status', 'action'));
WP_CLI::log(str_repeat('-', 140));

foreach ($sl_posts as $sl_p) {
    $sl_id     = (int) $sl_p->ID;
    $sl_slug   = (string) $sl_p->post_name;
    $sl_title  = (string) $sl_p->post_title;
    $sl_status = (string) $sl_p->post_status;
    $sl_action = '';

    // Truncate for table display.
    $sl_slug_t  = mb_substr($sl_slug, 0, 40);
    $sl_title_t = mb_substr($sl_title, 0, 50);

    // Operate only on publish.
    if ($sl_status !== 'publish') {
        $sl_action = 'SKIP not-publish';
        $sl_counts['skip_not_publish']++;
        WP_CLI::log(sprintf('%-6d | %-40s | %-50s | %-10s | %s', $sl_id, $sl_slug_t, $sl_title_t, $sl_status, $sl_action));
        continue;
    }

    $sl_body_ext = get_post_meta($sl_id, 'body_extended', true);
    $sl_content  = (string) $sl_p->post_content;

    // (1) SKIP already migrated.
    if (! empty($sl_body_ext) && trim((string) $sl_body_ext) !== '') {
        $sl_action = sprintf('SKIP already (body_extended len=%d)', strlen((string) $sl_body_ext));
        $sl_counts['skip_already']++;
        WP_CLI::log(sprintf('%-6d | %-40s | %-50s | %-10s | %s', $sl_id, $sl_slug_t, $sl_title_t, $sl_status, $sl_action));
        continue;
    }

    // (2) SKIP empty.
    if (trim($sl_content) === '') {
        $sl_action = 'SKIP empty (post_content vuoto)';
        $sl_counts['skip_empty']++;
        WP_CLI::log(sprintf('%-6d | %-40s | %-50s | %-10s | %s', $sl_id, $sl_slug_t, $sl_title_t, $sl_status, $sl_action));
        continue;
    }

    // (3) SKIP if contains Gutenberg blocks (modern serialized blocks → rischio rottura).
    // Decisione autonomous: SKIP + warn (NON migrare). Editor può fare manual copy in admin
    // o richiedere migration ad-hoc post-cut.
    if (strpos($sl_content, '<!-- wp:') !== false) {
        $sl_action = 'SKIP gutenberg-blocks (manual review required)';
        $sl_counts['skip_gutenberg']++;
        WP_CLI::log(sprintf('%-6d | %-40s | %-50s | %-10s | %s', $sl_id, $sl_slug_t, $sl_title_t, $sl_status, $sl_action));
        WP_CLI::warning(sprintf('Post %d has Gutenberg blocks in post_content — manual review.', $sl_id));
        continue;
    }

    // (4) MIGRATE: classic HTML content → body_extended SCF.
    $sl_content_len = strlen($sl_content);

    if ($sl_mode === 'wet') {
        // Backup originale post_content in postmeta legacy.
        $sl_backup_ok = update_post_meta($sl_id, '_legacy_post_content_backup', $sl_content);
        if ($sl_backup_ok === false) {
            $sl_action = 'ERROR backup failed';
            $sl_counts['error']++;
            WP_CLI::log(sprintf('%-6d | %-40s | %-50s | %-10s | %s', $sl_id, $sl_slug_t, $sl_title_t, $sl_status, $sl_action));
            WP_CLI::warning(sprintf('Post %d backup failed. Skipping.', $sl_id));
            continue;
        }

        // Copia in body_extended (postmeta SCF wysiwyg, raw HTML preserved).
        $sl_meta_ok = update_post_meta($sl_id, 'body_extended', $sl_content);
        if ($sl_meta_ok === false) {
            $sl_action = 'ERROR update_post_meta body_extended failed';
            $sl_counts['error']++;
            WP_CLI::log(sprintf('%-6d | %-40s | %-50s | %-10s | %s', $sl_id, $sl_slug_t, $sl_title_t, $sl_status, $sl_action));
            WP_CLI::warning(sprintf('Post %d body_extended write failed. Rolling back backup.', $sl_id));
            delete_post_meta($sl_id, '_legacy_post_content_backup');
            continue;
        }

        // Svuota post_content via wp_update_post (NON direct $wpdb — assicura revisione + cache flush).
        $sl_upd = wp_update_post([
            'ID'           => $sl_id,
            'post_content' => '',
        ], true);
        if (is_wp_error($sl_upd)) {
            $sl_action = sprintf('ERROR wp_update_post: %s', $sl_upd->get_error_message());
            $sl_counts['error']++;
            WP_CLI::log(sprintf('%-6d | %-40s | %-50s | %-10s | %s', $sl_id, $sl_slug_t, $sl_title_t, $sl_status, $sl_action));
            WP_CLI::warning(sprintf('Post %d wp_update_post failed. Rolling back body_extended.', $sl_id));
            delete_post_meta($sl_id, 'body_extended');
            // Backup left in place per safety (manual review).
            continue;
        }

        // Verifica nuova lunghezza body_extended.
        $sl_new_len = strlen((string) get_post_meta($sl_id, 'body_extended', true));
        $sl_action = sprintf('MIGRATED (post_content len=%d → body_extended len=%d)', $sl_content_len, $sl_new_len);
        $sl_counts['migrated']++;
    } else {
        // Dry-run: only report what would happen.
        $sl_action = sprintf('WOULD MIGRATE (post_content len=%d → body_extended)', $sl_content_len);
        $sl_counts['migrated']++;
    }

    WP_CLI::log(sprintf('%-6d | %-40s | %-50s | %-10s | %s', $sl_id, $sl_slug_t, $sl_title_t, $sl_status, $sl_action));
}

// Footer summary.
WP_CLI::log('');
WP_CLI::log(str_repeat('=', 140));
WP_CLI::log(sprintf(
    ' SUMMARY (mode=%s) — total=%d · migrated=%d · skipped=%d (already=%d, empty=%d, gutenberg=%d, not-publish=%d) · errors=%d',
    strtoupper($sl_mode),
    count($sl_posts),
    $sl_counts['migrated'],
    $sl_counts['skip_already'] + $sl_counts['skip_empty'] + $sl_counts['skip_gutenberg'] + $sl_counts['skip_not_publish'],
    $sl_counts['skip_already'],
    $sl_counts['skip_empty'],
    $sl_counts['skip_gutenberg'],
    $sl_counts['skip_not_publish'],
    $sl_counts['error']
));
WP_CLI::log(str_repeat('=', 140));

if ($sl_mode === 'dry') {
    WP_CLI::log('');
    WP_CLI::log('Dry-run complete. Review output, then re-run with --wet-run to execute.');
}

if ($sl_counts['error'] > 0) {
    WP_CLI::error(sprintf('%d errors during migration. Review log above.', $sl_counts['error']));
}

WP_CLI::success(sprintf('Migration %s completed.', strtoupper($sl_mode) . '-RUN'));
