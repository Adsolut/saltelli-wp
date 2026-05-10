<?php
/**
 * Wave 4.7.fix.4 — Admin shortcuts per archive CPT (avvocato, saltelli_caso).
 *
 * Le pagine archive CPT (`/chi-siamo/team/` e `/chi-siamo/casi-rappresentativi/`)
 * non hanno una Page WP corrispondente — il loro header (titolo + intro) si
 * modifica dalla tab "Archive Headers" della pagina Saltelli — Settings.
 * I singoli avvocati / casi rappresentativi si modificano via i loro CPT.
 *
 * Senza guidance esplicito Elena fatica a capire dove editare cosa. Questo file
 * aggiunge:
 *   1. Admin bar "Modifica" link quando si visita gli archive CPT loggati come admin
 *   2. Notice esplicativo nella tab "Archive Headers" della Saltelli — Settings
 *
 * @package Saltelli
 * @since 1.3.10 Wave 4.7.fix.4
 */

defined('ABSPATH') || exit;

/**
 * Admin bar shortcut quando si visita archive CPT su frontend.
 *
 * Aggiunge node "Modifica header pagina" che porta a Saltelli — Settings tab
 * Archive Headers (l'URL anchor `#tab_archive_headers` fa scroll alla tab SCF).
 * Aggiunge anche shortcut "Tutti gli avvocati" / "Tutti i casi" per accesso
 * diretto al CPT list.
 */
add_action('admin_bar_menu', function ($wp_admin_bar) {
    if (!is_admin_bar_showing()) return;
    if (is_admin()) return; // Solo frontend

    $is_archive_avvocato = is_post_type_archive('avvocato');
    $is_archive_caso     = is_post_type_archive('saltelli_caso');

    if (!$is_archive_avvocato && !$is_archive_caso) return;

    $settings_url = admin_url('admin.php?page=saltelli-settings');

    if ($is_archive_avvocato) {
        $wp_admin_bar->add_node([
            'id'    => 'saltelli-edit-archive-header',
            'title' => 'Modifica header archivio',
            'href'  => $settings_url,
            'meta'  => [
                'title' => 'Modifica titolo + intro di /chi-siamo/team/ in Saltelli Settings',
            ],
        ]);
        $wp_admin_bar->add_node([
            'parent' => 'saltelli-edit-archive-header',
            'id'     => 'saltelli-edit-all-avvocati',
            'title'  => 'Tutti gli avvocati',
            'href'   => admin_url('edit.php?post_type=avvocato'),
        ]);
    } elseif ($is_archive_caso) {
        $wp_admin_bar->add_node([
            'id'    => 'saltelli-edit-archive-header',
            'title' => 'Modifica header archivio',
            'href'  => $settings_url,
            'meta'  => [
                'title' => 'Modifica titolo + intro di /chi-siamo/casi-rappresentativi/ in Saltelli Settings',
            ],
        ]);
        $wp_admin_bar->add_node([
            'parent' => 'saltelli-edit-archive-header',
            'id'     => 'saltelli-edit-all-casi',
            'title'  => 'Tutti i casi rappresentativi',
            'href'   => admin_url('edit.php?post_type=saltelli_caso'),
        ]);
    }
}, 100);

/**
 * Notice nella tab "Archive Headers" della Saltelli — Settings.
 *
 * SCF rendering tab field non offre un hook diretto per messaggi per-tab.
 * Workaround: enqueue inline script + CSS sulle settings page che inietta
 * il notice via JS sotto il tab handler "Archive Headers" quando attivo.
 *
 * Approccio più robusto: usare `acf/load_field/key=field_<archive_headers_tab>`
 * per modificare l'instruction visibile sulla tab — più semplice e SCF-native.
 */
add_filter('acf/load_field', function ($field) {
    // Solo admin (evita overhead micro su frontend reads via get_field).
    if (!is_admin()) return $field;

    // Target: il primo field DELLA tab "Archive Headers" (in group_theme_options_v1).
    // Aggiunge instructions content come "guidance bar" sotto la tab handler.
    if (!is_array($field)) return $field;

    // Cerchiamo il marker tab field "Archive Headers" (label/name signature).
    $is_archive_headers_tab = false;
    if (
        ($field['type'] ?? '') === 'tab' &&
        (
            stripos($field['label'] ?? '', 'archive') !== false ||
            stripos($field['name'] ?? '', 'archive') !== false
        )
    ) {
        $is_archive_headers_tab = true;
    }

    if (!$is_archive_headers_tab) return $field;

    // Existing instructions get preserved + augmented with guidance HTML.
    $existing = (string) ($field['instructions'] ?? '');
    $guidance = sprintf(
        '%s<div style="margin-top: 10px; padding: 12px; background: #f0f6fc; border-left: 4px solid #2271b1; font-size: 13px; line-height: 1.5;">'
        . '<strong>📚 Header per le pagine archivio CPT</strong><br>'
        . 'Modifica qui titolo + intro che appaiono in cima a:<br>'
        . '<ul style="margin: 8px 0 0 18px;">'
        . '<li><a href="%s">/chi-siamo/team/</a> &mdash; per modificare i singoli avvocati: <a href="%s">vai a Avvocati</a></li>'
        . '<li><a href="%s">/chi-siamo/casi-rappresentativi/</a> &mdash; per modificare i singoli casi: <a href="%s">vai a Casi rappresentativi</a></li>'
        . '</ul>'
        . '</div>',
        $existing !== '' ? $existing . '<br>' : '',
        esc_url(home_url('/chi-siamo/team/')),
        esc_url(admin_url('edit.php?post_type=avvocato')),
        esc_url(home_url('/chi-siamo/casi-rappresentativi/')),
        esc_url(admin_url('edit.php?post_type=saltelli_caso'))
    );

    $field['instructions'] = $guidance;
    return $field;
}, 20);
