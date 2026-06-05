<?php
if (!defined('ABSPATH')) {
    exit;
}

$theme = wp_get_theme();

theme_define('THEME_NAME', $theme->get('Name'));
theme_define('THEME_SLUG', sanitize_key($theme->get('TextDomain')));
theme_define('THEME_TEXTDOMAIN', $theme->get('TextDomain'));
theme_define('THEME_VERSION', $theme->get('Version'));

require_once __DIR__ . '/domains.php';
require_once __DIR__ . '/paths.php';

/* =========================================
   LOGGER
   ========================================= */
require_once THEME_CORE_DIR . '/logger.php';

/* =========================================
   CORE
   ========================================= */
log_info("Initializing Core Modules", LOG_DOMAIN_API);
require_once THEME_CORE_DIR . '/func_setup.php';
require_once THEME_CORE_DIR . '/func_assets.php';
require_once THEME_CORE_DIR . '/func_enqueue.php';
require_once THEME_CORE_DIR . '/func_content.php';
require_once THEME_CORE_DIR . '/func_data.php';

/* =========================================
   API (HTTP Requests)
   ========================================= */
log_info("Initializing API Modules", LOG_DOMAIN_API);
require_once THEME_CORE_DIR . '/func_api.php';

/* =========================================
   AJAX
   ========================================= */
log_info("Initializing AJAX Modules", LOG_DOMAIN_AJAX);
require_once THEME_CORE_DIR . '/func_ajax.php';

/* =========================================
   Customizer
   ========================================= */
log_info("Initializing Customizer Modules", LOG_DOMAIN_ADMIN);
require_once THEME_CUSTOMIZER_DIR . '/customizer.php';
require_once THEME_CORE_DIR . '/func_customizer.php';

/* =========================================
   Admin
   ========================================= */
log_info("Initializing Admin Modules", LOG_DOMAIN_ADMIN);
require_once THEME_CORE_DIR . '/func_admin.php';

/* =========================================
   3rd Parties
   ========================================= */
log_info("Initializing Vendor Modules", LOG_DOMAIN_ADMIN);
require_once THEME_ADMIN_VENDOR_DIR . '/tgm/class-tgm-plugin-activation.php';
?>