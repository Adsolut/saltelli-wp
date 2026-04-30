<?php
/**
 * Saltelli Content Migration — Step D
 *
 * Run via: docker compose run --rm wpcli eval-file /var/www/html/wp-content/db-dump/migrate-content.php
 * (lo script sta in db-dump/ che è gitignored ma montato sul container)
 *
 * Carica db-dump/migration-data.json e applica:
 *   - 19 CPT competenza: post_content (da source con cleanup) + meta + ACF
 *   - 4 CPT avvocato: bio + specs + formazione + email + author mapping
 *
 * Idempotenza: re-run non rompe lo stato. Conferma update con WP_CLI::log().
 * Foto Emiliano (_thumbnail_id su 2660): NON sovrascritta — script non tocca foto_ritratto.
 */

if (!defined('WP_CLI') || !WP_CLI) {
    fwrite(STDERR, "Questo script richiede WP-CLI.\n");
    exit(1);
}

$data_path = '/var/www/html/wp-content/db-dump/migration-data.json';
if (!file_exists($data_path)) {
    WP_CLI::error("File config non trovato: $data_path");
}

$config = json_decode(file_get_contents($data_path), true);
if (!$config) {
    WP_CLI::error("Config JSON non parseable: " . json_last_error_msg());
}

/**
 * Light HTML cleanup: rimuove tag noise, normalizza whitespace, preserva semantica.
 */
function saltelli_clean_source_html($html) {
    if (!$html) return '';
    // Rimuovi <img> con URL absolute al vecchio dominio (saranno gestiti da featured image)
    $html = preg_replace('#<img[^>]*srcset=[^>]*>#i', '', $html);
    $html = preg_replace('#<img[^>]*src=["\']?https?://[^"\']*localhost:8080/wp-content/uploads/[^>]*>#i', '', $html);
    // Strip srcset/sizes restanti
    $html = preg_replace('#\s+srcset="[^"]*"#i', '', $html);
    $html = preg_replace('#\s+sizes="[^"]*"#i', '', $html);
    // Strip elementor data attributes (paranoia, già clean nei sample)
    $html = preg_replace('#\s+data-elementor-[a-z\-]+="[^"]*"#i', '', $html);
    $html = preg_replace('#\s+data-id="[^"]*"#i', '', $html);
    // Strip empty <a> linking to old contact form
    $html = preg_replace('#<a[^>]*href=["\']?http[^"\']*contatti[^>]*>\s*</a>#i', '', $html);
    // Collapse multi-line whitespace inside content
    $html = preg_replace("#\n\s*\n#", "\n\n", $html);
    $html = preg_replace('#[ \t]+#', ' ', $html);
    // Strip Wordpress autop trailing
    $html = trim($html);
    // Strip empty <p></p> or <p>&nbsp;</p>
    $html = preg_replace('#<p>(\s|&nbsp;)*</p>#i', '', $html);
    return $html;
}

/**
 * Assicura presenza ACF: degrade gracefully con update_post_meta se ACF non presente.
 */
function saltelli_set_field($field, $value, $post_id) {
    if (function_exists('update_field')) {
        update_field($field, $value, $post_id);
    } else {
        update_post_meta($post_id, $field, $value);
    }
}

// =========================================================================
// MIGRATION COMPETENZE
// =========================================================================
$competenze_processed = 0;
$competenze_with_source = 0;
$competenze_synth_only = 0;

foreach ($config['competenze'] as $slug => $data) {
    $cpt_id = (int) $data['cpt_id'];
    $source_id = isset($data['source_id']) ? (int) $data['source_id'] : null;
    $tier = (int) $data['tier'];

    // Carica e pulisci body
    if ($source_id) {
        $source_post = get_post($source_id);
        if (!$source_post) {
            WP_CLI::warning("Source page {$source_id} mancante per CPT {$cpt_id} ({$slug}). Skip body.");
            $body = $data['synth_body'] ?? '';
            $competenze_synth_only++;
        } else {
            $body = saltelli_clean_source_html($source_post->post_content);
            $competenze_with_source++;
        }
    } else {
        $body = $data['synth_body'] ?? '';
        $competenze_synth_only++;
    }

    // Update post_content
    if ($body) {
        $update = wp_update_post([
            'ID' => $cpt_id,
            'post_content' => $body,
        ], true);
        if (is_wp_error($update)) {
            WP_CLI::warning("Update post {$cpt_id} fallito: " . $update->get_error_message());
            continue;
        }
    }

    // ACF/meta — answer_capsule, lead_breve, is_tier_1_focus
    saltelli_set_field('answer_capsule', $data['answer_capsule'], $cpt_id);
    saltelli_set_field('lead_breve', $data['lead_breve'], $cpt_id);
    saltelli_set_field('is_tier_1_focus', $tier === 1, $cpt_id);

    // body_extended (Tier-1 only — wysiwyg)
    if ($tier === 1 && !empty($data['synth_body'])) {
        saltelli_set_field('body_extended', $data['synth_body'], $cpt_id);
    }

    // FAQ repeater
    if (!empty($data['faqs'])) {
        $faq_rows = [];
        foreach ($data['faqs'] as $faq) {
            $faq_rows[] = [
                'domanda' => $faq['q'],
                'risposta' => $faq['a'],
            ];
        }
        saltelli_set_field('faq', $faq_rows, $cpt_id);
    }

    // Casi rappresentativi (Tier-1 only)
    if (!empty($data['casi_rappresentativi'])) {
        $casi_rows = [];
        foreach ($data['casi_rappresentativi'] as $caso) {
            $casi_rows[] = [
                'titolo' => $caso['titolo'],
                'descrizione_anonimizzata' => $caso['descrizione'],
                'esito' => $caso['esito'],
            ];
        }
        saltelli_set_field('casi_rappresentativi', $casi_rows, $cpt_id);
    }

    $competenze_processed++;
    WP_CLI::log("✓ Competenza '{$slug}' (CPT {$cpt_id}, tier {$tier}) updated");
}

WP_CLI::log("");
WP_CLI::log("Competenze: {$competenze_processed}/19 processate ({$competenze_with_source} con source, {$competenze_synth_only} synth)");

// =========================================================================
// MIGRATION AVVOCATI
// =========================================================================
$avvocati_processed = 0;
$author_mapped = 0;

foreach ($config['avvocati'] as $slug => $data) {
    $cpt_id = (int) $data['cpt_id'];
    $wp_user_id = isset($data['wp_user_id']) ? (int) $data['wp_user_id'] : null;

    // Update post_content con bio_estesa per il rendering single-avvocato
    if (!empty($data['bio_estesa'])) {
        $update = wp_update_post([
            'ID' => $cpt_id,
            'post_content' => $data['bio_estesa'],
        ], true);
        if (is_wp_error($update)) {
            WP_CLI::warning("Update avvocato {$cpt_id} fallito: " . $update->get_error_message());
            continue;
        }
    }

    // ACF fields
    saltelli_set_field('bio_breve', $data['bio_breve'], $cpt_id);
    saltelli_set_field('bio_estesa', $data['bio_estesa'], $cpt_id);
    saltelli_set_field('ruolo_breve', $data['ruolo_breve'], $cpt_id);
    saltelli_set_field('email_pubblica', $data['email_pubblica'], $cpt_id);
    saltelli_set_field('telefono_pubblico', $data['telefono_pubblico'], $cpt_id);

    // Specializzazioni repeater (struttura: array di {label})
    if (!empty($data['specializzazioni'])) {
        $rows = [];
        foreach ($data['specializzazioni'] as $sp) {
            $rows[] = ['label' => $sp];
        }
        saltelli_set_field('specializzazioni', $rows, $cpt_id);
    }

    // Formazione repeater
    if (!empty($data['formazione'])) {
        $rows = [];
        foreach ($data['formazione'] as $f) {
            $rows[] = [
                'anno' => $f['anno'] ?? '',
                'titolo' => $f['titolo'] ?? '',
                'istituzione' => $f['istituzione'] ?? '',
            ];
        }
        saltelli_set_field('formazione', $rows, $cpt_id);
    }

    // Author mapping (D6) — meta che permette al template blog di linkare al CPT
    if ($wp_user_id) {
        update_post_meta($cpt_id, '_wp_author_id', $wp_user_id);
        $author_mapped++;
    }

    $avvocati_processed++;
    WP_CLI::log("✓ Avvocato '{$slug}' (CPT {$cpt_id}) updated, author user_id={$wp_user_id}");
}

WP_CLI::log("");
WP_CLI::log("Avvocati: {$avvocati_processed}/4 processati, {$author_mapped} mappati a wp_users");

// =========================================================================
// VERIFICA FINALE
// =========================================================================
WP_CLI::log("");
WP_CLI::log("=== Sanity check ===");

// Check Emiliano _thumbnail_id integro (deve restare 2683)
$emi_thumb = get_post_meta(2660, '_thumbnail_id', true);
WP_CLI::log("Emiliano (CPT 2660) _thumbnail_id = " . ($emi_thumb ?: '(none)') . " — atteso 2683");

// Sample answer_capsule sample
$sample = get_post_meta(2664, 'answer_capsule', true);
$sample_short = mb_substr((string) $sample, 0, 80) . '...';
WP_CLI::log("Sample CPT 2664 answer_capsule: {$sample_short}");

// Conta post pubblicati per autore mappato
$post_counts = [];
foreach ($config['avvocati'] as $slug => $data) {
    if (empty($data['wp_user_id'])) continue;
    $count = (int) wp_count_posts_by_user($data['wp_user_id'], 'post');
    $post_counts[$slug] = $count;
}
WP_CLI::log("Post per CPT avvocato (via user mapping): " . json_encode($post_counts));

WP_CLI::success("Migration Step D completata");

/**
 * Helper inline: count post per autore (WP non ha funzione standard wp_count_posts_by_user).
 */
function wp_count_posts_by_user($user_id, $post_type = 'post') {
    global $wpdb;
    return $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_author = %d AND post_type = %s AND post_status = 'publish'",
        $user_id, $post_type
    ));
}
