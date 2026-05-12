<?php
/**
 * Wave 4.7.fix.4 — Disable Gutenberg block editor for Pages with SCF metabox attached.
 *
 * Strategy A FULL SCF MIGRATION: eliminata la dualità `post_content` ↔ SCF metabox
 * disabilitando il block editor sulle Pages target. Elena vede ora SOLO il
 * metabox SCF + un notice di guidance. Una sola sorgente di verità per Page.
 *
 * Page IDs target (12 totale post-Wave P7):
 *   - 3 hub Wave 4.7.fix.3: 17 (home), 2812 (aree-di-pratica), 2813 (risorse)
 *   - 7 target Wave 4.7.fix.4: 23 (contatti), 2708 (faq), 2709 (guide-gratuite),
 *                              2712 (come-lavoriamo), 2711 (prima-consulenza),
 *                              372 (lavora-con-noi), 2713 (richiedi-preventivo)
 *   - 1 editorial SCF-driven: 2811 (chi-siamo — ex lo-studio, rinominata slug in Wave P7;
 *                             Page 2822 hub legacy cancellata, consolidamento chi-siamo = lo-studio)
 *   - 1 Wave 5 STEP 3 coverage-completion: 2714 (prenota-appuntamento — group_prenota_appuntamento_v1)
 *
 * NB: gli ID si riferiscono al DB di staging; in locale (Docker) alcuni slug hanno
 * ID diversi (es. prenota-appuntamento = 2711 in locale, 2714 su staging). I match
 * per-ID che non corrispondono in locale sono no-op innocui; le location dei field
 * group usano `page_slug ==` per la portabilità (Debug-QA bug-04).
 *
 * @package Saltelli
 * @since 1.3.10 Wave 4.7.fix.4
 * @since 1.3.13 Wave 5 STEP 3 coverage-completion — +2714 prenota-appuntamento
 * @since 1.3.15 Wave P7 consolidamento — −2822 (cancellata), 2811 ora slug `chi-siamo`. 13 → 12 IDs.
 */

defined('ABSPATH') || exit;

if (!defined('SALTELLI_SCF_ONLY_PAGES')) {
    define('SALTELLI_SCF_ONLY_PAGES', [
        17,    // home (page_on_front)
        23,    // contatti
        372,   // lavora-con-noi
        2708,  // domande-frequenti
        2709,  // guide-gratuite
        2711,  // prima-consulenza
        2712,  // come-lavoriamo
        2713,  // richiedi-preventivo
        2714,  // prenota-appuntamento (Wave 5 STEP 3 coverage-completion)
        2811,  // chi-siamo (ex lo-studio — rinominata slug in Wave P7 consolidamento)
        2812,  // aree-di-pratica (hub)
        2813,  // risorse (hub)
        // 2822 RIMOSSO — Page hub chi-siamo cancellata in Wave P7 (consolidamento chi-siamo = lo-studio)
    ]);
}

/**
 * Disable block editor (Gutenberg) for target Pages.
 *
 * Hook filter `use_block_editor_for_post` ritorna false sui Pages target,
 * forzando classic editor visual area. Combinato con `edit_form_after_title`
 * (sotto) che nasconde il classic editor entirely, l'editor vede solo title +
 * SCF metabox.
 */
add_filter('use_block_editor_for_post', function ($use_block_editor, $post) {
    if ($post && in_array((int) $post->ID, SALTELLI_SCF_ONLY_PAGES, true)) {
        return false;
    }
    return $use_block_editor;
}, 10, 2);

/**
 * Disable classic editor visual area + add admin notice on target Pages.
 *
 * Iniettato sotto il post title (post=page) tramite `edit_form_after_title`:
 *   - CSS inline nasconde #postdivrich, wp-content-editor, ed_toolbar
 *   - Notice editoriale spiega che il contenuto si modifica dai field SCF sotto
 *
 * `do_action('edit_form_after_title', $post)` scatta solo se Gutenberg
 * NON è attivo per quel post (cosa che il filter sopra già garantisce).
 * Quindi per i Pages target: filter disabilita Gutenberg → WP carica classic
 * editor → hook scatta → CSS + notice iniettati.
 */
add_action('edit_form_after_title', function ($post) {
    if (!$post || !in_array((int) $post->ID, SALTELLI_SCF_ONLY_PAGES, true)) {
        return;
    }
    ?>
    <style>
        /* Hide classic editor visual area entirely on SCF-only Pages.
         * Lascia visibili: post title, slug edit, publish box, SCF metabox. */
        #postdivrich,
        #wp-content-editor-container,
        #ed_toolbar,
        .wp-editor-tools,
        #wp-content-media-buttons,
        #post-status-info,
        .wp-editor-tabs {
            display: none !important;
        }
    </style>
    <div class="notice notice-info inline saltelli-scf-only-notice" style="margin: 20px 0; padding: 15px; border-left: 4px solid #2271b1; background: #f0f6fc;">
        <p style="font-size: 14px; margin: 0; line-height: 1.5;">
            <strong>Modifica il contenuto qui sotto.</strong>
            Il contenuto di questa pagina si modifica esclusivamente dai campi qui sotto (sezione <em>Saltelli — Page&hellip;</em>).
            L'editor di contenuto classico è stato disabilitato per evitare conflitti tra sorgenti diverse.
        </p>
    </div>
    <?php
});
