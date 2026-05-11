<?php
/**
 * Wave 4.7.fix.5 — Notice editoriali contestuali nell'editor degli Articoli (blog).
 *
 * Il blog (`/risorse/blog/`) è 100% WordPress standard: editor Gutenberg + sidebar
 * "Documento". Elena è abituata al pattern SCF metabox delle Pages target e percepisce
 * l'editing del blog come "fantasma". Questo file aggiunge guidance leggera (PHP-only,
 * niente JS / niente refactor) per chiarire dove vivono i pezzi visibili sul frontend.
 *
 * Implementazione: `admin_notices` (banner in cima all'editor). Note: l'hook
 * `post_submitbox_misc_actions` suggerito nel prompt NON scatta in Gutenberg
 * (è un hook del classic editor), e gli Articoli usano Gutenberg → si usa
 * `admin_notices` che funziona in entrambi.
 *
 * @package Saltelli
 * @since 1.3.11 Wave 4.7.fix.5
 */

defined('ABSPATH') || exit;

/**
 * Banner promemoria nell'editor di un Articolo (post type `post`).
 *
 * Spiega in una riga la mappatura sidebar → elemento frontend per i 4 pezzi
 * che Elena tipicamente dimentica (Estratto/lede, Immagine in evidenza, Autore,
 * Categoria). Dismissibile (riappare al reload — è un promemoria, non onboarding).
 */
add_action('admin_notices', function () {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->base !== 'post' || $screen->post_type !== 'post') {
        return;
    }
    ?>
    <div class="notice notice-info is-dismissible" style="border-left-color:#1B2B4B;">
        <p style="font-size:13px;line-height:1.6;margin:.6em 0;">
            <strong>💡 Promemoria editoriale — dove finiscono i campi della sidebar destra:</strong><br>
            • <strong>Estratto</strong> (pannello "Riassunto"/"Estratto") → diventa il <em>lede italico</em> grande sotto il titolo dell'articolo. Se è vuoto, sotto il titolo non appare nulla.<br>
            • <strong>Immagine in evidenza</strong> → hero del post (e card nell'archivio blog). Praticamente obbligatoria: senza, l'articolo è "spoglio".<br>
            • <strong>Autore</strong> → la firma dell'articolo. Scegli l'avvocato giusto (Antonia Battista, Fabiana Saltelli, Stefano Gaetano Tedesco, Gabriele Cascone): se il nome combacia con una scheda avvocato, sotto il titolo compare anche la sua bio breve + i temi di competenza.<br>
            • <strong>Categoria</strong> → appare nel breadcrumb (Home / Editoriale / <em>Categoria</em> / …) e nel meta sopra il titolo. Scegline <strong>una</strong> principale.<br>
            <span style="color:#6B6B6B;">Il tempo di lettura ("X MIN") è calcolato in automatico dal numero di parole — non si imposta a mano.</span>
        </p>
    </div>
    <?php
});

/**
 * Banner sull'editor della Page contenitore del blog (`page_for_posts`, di norma ID 1413).
 *
 * Questa Page esiste solo come "slot" per `/risorse/blog/`: il suo contenuto Gutenberg
 * NON è mostrato sul frontend (il template `home.php` ignora `post_content` — il blog è
 * generato dal template: titolo "Editoriale.", lista articoli automatica, newsletter).
 * Elena potrebbe scriverci dentro pensando di modificare la pagina blog → non cambia nulla.
 */
add_action('admin_notices', function () {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->base !== 'post' || $screen->post_type !== 'page') {
        return;
    }
    $blog_page_id = (int) get_option('page_for_posts');
    if (!$blog_page_id) {
        return;
    }
    $editing_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    if ($editing_id !== $blog_page_id) {
        return;
    }
    ?>
    <div class="notice notice-warning is-dismissible" style="border-left-color:#B8860B;">
        <p style="font-size:13px;line-height:1.6;margin:.6em 0;">
            <strong>⚠️ Questa è la pagina-contenitore del blog</strong> (<code>/risorse/blog/</code>).<br>
            Il contenuto che scrivi qui sotto <strong>non viene mostrato sul frontend</strong>: la pagina blog è generata automaticamente dal tema (titolo "Editoriale.", lista articoli, newsletter). Modificare il titolo o il testo qui non cambia la pagina pubblica.<br>
            Per aggiungere o modificare articoli del blog vai a → <a href="<?php echo esc_url(admin_url('edit.php?post_type=post')); ?>"><strong>Articoli</strong></a>.
        </p>
    </div>
    <?php
});
