<?php
/**
 * Saltelli Theme — bootstrap.
 * Tutta la logica vive in inc/. Questo file solo orchestra.
 *
 * @package   Saltelli
 * @author    Adsolut Web Agency <https://adsolut.it>
 * @copyright © 2026 Adsolut Web Agency
 * @license   Proprietary
 */

defined('ABSPATH') || exit;

define('SALTELLI_THEME_VERSION', '1.3.15-wave5-design-handoff-p7-chi-siamo-consolidamento');
define('SALTELLI_THEME_DIR', get_template_directory());
define('SALTELLI_THEME_URI', get_template_directory_uri());

require_once SALTELLI_THEME_DIR . '/inc/setup.php';
require_once SALTELLI_THEME_DIR . '/inc/enqueue.php';
require_once SALTELLI_THEME_DIR . '/inc/critical-css.php';
require_once SALTELLI_THEME_DIR . '/inc/perf.php';
require_once SALTELLI_THEME_DIR . '/inc/security.php';
require_once SALTELLI_THEME_DIR . '/inc/helpers.php';
require_once SALTELLI_THEME_DIR . '/inc/cpt-avvocato.php';
require_once SALTELLI_THEME_DIR . '/inc/cpt-competenza.php';
require_once SALTELLI_THEME_DIR . '/inc/cpt-recovery.php';
require_once SALTELLI_THEME_DIR . '/inc/acf-fields.php';
require_once SALTELLI_THEME_DIR . '/inc/schema/schema-loader.php';
require_once SALTELLI_THEME_DIR . '/inc/seo/meta-tags.php';
require_once SALTELLI_THEME_DIR . '/inc/seo/ai-files.php';
require_once SALTELLI_THEME_DIR . '/inc/seo/legacy-redirects.php';
require_once SALTELLI_THEME_DIR . '/inc/seo/wave5-blog-rewrites.php';
require_once SALTELLI_THEME_DIR . '/inc/seo/yoast-schema-extensions.php';
require_once SALTELLI_THEME_DIR . '/inc/wave4-6-migration.php';

// Wave 4.7.fix.4 admin UX (Strategy A FULL SCF).
// Filters are registered always; they're cheap when their hooks don't fire.
// `use_block_editor_for_post` checked on admin Page edit, `admin_bar_menu` fires
// su frontend logged-in. Caricare in CLI context permette anche eval testing.
require_once SALTELLI_THEME_DIR . '/inc/admin/disable-gutenberg-for-scf-pages.php';
require_once SALTELLI_THEME_DIR . '/inc/admin/scf-archive-headers-shortcuts.php';

// Wave 4.7.fix.5 admin UX: notice editoriali blog (Articoli + Page contenitore blog)
// + lock-down Customizer / CSS aggiuntivo per ruolo editor (Elena).
require_once SALTELLI_THEME_DIR . '/inc/admin/post-editor-notices.php';
require_once SALTELLI_THEME_DIR . '/inc/admin/customizer-lockdown.php';
