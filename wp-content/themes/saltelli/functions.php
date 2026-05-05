<?php
/**
 * Saltelli Theme — bootstrap.
 * Tutta la logica vive in inc/. Questo file solo orchestra.
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

define('SALTELLI_THEME_VERSION', '1.0.0-recovery-wave3-debug');
define('SALTELLI_THEME_DIR', get_template_directory());
define('SALTELLI_THEME_URI', get_template_directory_uri());

require_once SALTELLI_THEME_DIR . '/inc/setup.php';
require_once SALTELLI_THEME_DIR . '/inc/enqueue.php';
require_once SALTELLI_THEME_DIR . '/inc/critical-css.php';
require_once SALTELLI_THEME_DIR . '/inc/helpers.php';
require_once SALTELLI_THEME_DIR . '/inc/cpt-avvocato.php';
require_once SALTELLI_THEME_DIR . '/inc/cpt-competenza.php';
require_once SALTELLI_THEME_DIR . '/inc/cpt-recovery.php';
require_once SALTELLI_THEME_DIR . '/inc/acf-fields.php';
require_once SALTELLI_THEME_DIR . '/inc/schema/schema-loader.php';
require_once SALTELLI_THEME_DIR . '/inc/seo/meta-tags.php';
require_once SALTELLI_THEME_DIR . '/inc/seo/ai-files.php';
require_once SALTELLI_THEME_DIR . '/inc/seo/legacy-redirects.php';
require_once SALTELLI_THEME_DIR . '/inc/seo/yoast-schema-extensions.php';
