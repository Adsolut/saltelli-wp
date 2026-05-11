<?php
/**
 * Wave 4.7.fix.5 — Customizer + "CSS aggiuntivo" lock-down per ruolo editor.
 *
 * Elena (role `editor`) potrebbe vedere/raggiungere Aspetto → Personalizza →
 * "CSS aggiuntivo" e con una modifica accidentale rompere il design. Anche se il
 * ruolo `editor` di default WP NON ha `edit_theme_options`/`customize` (quindi il
 * Customizer di norma non gli appare), questo file è una rete di sicurezza
 * difensiva: se mai una caps modification (plugin / add_cap) sblocca quel ruolo,
 * il Customizer + CSS aggiuntivo restano accessibili SOLO all'administrator.
 *
 * 3 livelli di enforcement:
 *   1. `user_has_cap` — rimuove `customize` / `edit_css` / `edit_theme_options`
 *      per ogni utente non-administrator.
 *   2. `load-customize.php` — 403 wp_die con messaggio italiano (contatto Adsolut)
 *      se un non-admin tenta GET diretto su `/wp-admin/customize.php`.
 *   3. `admin_menu` — rimuove il submenu Aspetto → Personalizza per i non-admin.
 *
 * @package Saltelli
 * @since 1.3.11 Wave 4.7.fix.5
 */

defined('ABSPATH') || exit;

/**
 * Strip Customizer / Additional CSS / theme-options capabilities da ogni non-admin.
 *
 * `user_has_cap` opera sui caps primitivi: rimuovere `edit_theme_options` chiude
 * di fatto anche tutto ciò che ci mappa sopra (Customizer, CSS aggiuntivo, menu
 * Aspetto, widgets editor). `customize` viene rimosso esplicitamente per chiarezza.
 */
add_filter('user_has_cap', function ($allcaps, $caps, $args, $user) {
    if (!$user instanceof WP_User) {
        return $allcaps;
    }
    // ⚠️ NON usare is_super_admin()/current_user_can()/user_can() qui dentro:
    // farebbero ricorsione infinita (anche loro fanno una cap check → ri-trigger
    // del filtro `user_has_cap`). Si usa solo l'accesso diretto a ->roles, che
    // non passa dal filtro. (Wave 4.7.fix.5 incident: is_super_admin() qui dentro
    // → recursion → OOM staging.)
    if (in_array('administrator', (array) $user->roles, true)) {
        return $allcaps;
    }
    foreach (['customize', 'edit_css', 'edit_theme_options'] as $cap) {
        unset($allcaps[$cap]);
    }
    return $allcaps;
}, 10, 4);

/**
 * Blocco hard: GET diretto su customize.php da non-admin → 403 con messaggio italiano.
 */
add_action('load-customize.php', function () {
    if (current_user_can('administrator')) {
        return;
    }
    wp_die(
        esc_html__('Non hai i permessi per accedere al Customizer. Per modificare design, layout o CSS del sito contatta l\'amministratore: tech@adsolut.it', 'saltelli'),
        esc_html__('Accesso non autorizzato', 'saltelli'),
        ['response' => 403, 'back_link' => true]
    );
});

/**
 * Nasconde il submenu Aspetto → Personalizza (e qualsiasi sua variante con query
 * string `?return=...`) per i non-admin. Belt-and-suspenders: il caps strip sopra
 * già toglie l'intero menu Aspetto, ma rimuoviamo esplicitamente la voce per ogni
 * eventuale slug residuo.
 */
add_action('admin_menu', function () {
    if (current_user_can('administrator')) {
        return;
    }
    remove_submenu_page('themes.php', 'customize.php');
    global $submenu;
    if (!empty($submenu['themes.php'])) {
        foreach ($submenu['themes.php'] as $i => $item) {
            if (isset($item[2]) && strpos((string) $item[2], 'customize.php') === 0) {
                unset($submenu['themes.php'][$i]);
            }
        }
    }
}, 999);
