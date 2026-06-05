<?php

/********************
 * BASE
 *******************/
theme_define('THEME_URL', get_template_directory_uri());
theme_define('THEME_DIR', get_template_directory());

/********************
 * ADMIN
 *******************/
theme_define('THEME_ADMIN_DIR', THEME_DIR . '/admin');
theme_define('THEME_ADMIN_ASSETS_DIR', THEME_ADMIN_DIR . '/assets');
theme_define('THEME_ADMIN_PAGES_DIR', THEME_ADMIN_DIR . '/pages');
theme_define('THEME_ADMIN_SERVICES_DIR', THEME_ADMIN_DIR . '/services');
theme_define('THEME_ADMIN_VENDOR_DIR', THEME_ADMIN_DIR . '/vendor');
theme_define('THEME_ADMIN_VIEWS_DIR', THEME_ADMIN_DIR . '/views');

/********************
 * ASSETS
 *******************/
theme_define('THEME_CSS_DIR', THEME_DIR . '/assets/css');
theme_define('THEME_IMAGES_DIR', THEME_DIR . '/assets/images');
theme_define('THEME_JS_DIR', THEME_DIR . '/assets/js');
theme_define('THEME_PACKAGES_DIR', THEME_DIR . '/assets/packages');

/********************
 * BOOTSTRAP
 *******************/
theme_define('THEME_BOOTSTRAP_DIR', THEME_DIR . '/bootstrap');

/********************
 * CONFIG
 *******************/
theme_define('THEME_CONFIG_DIR', THEME_DIR . '/config');

/********************
 * CORE
 *******************/
theme_define('THEME_CORE_DIR', THEME_DIR . '/core');

/********************
 * CUSTOMIZER
 *******************/
theme_define('THEME_CUSTOMIZER_DIR', THEME_DIR . '/customizer');

/********************
 * DATA
 *******************/
theme_define('THEME_DATA_DIR', THEME_DIR . '/data');
theme_define('THEME_LOGS_DIR', THEME_DATA_DIR . '/logs');
theme_define('THEME_LOG_FILE', THEME_LOGS_DIR . '/theme_log.json');

/********************
 * TEMPLATE PARTS
 *******************/
theme_define('THEME_TEMPLATES_DIR', THEME_DIR . '/template-parts');

theme_define('THEME_COMPONENTS_DIR', THEME_TEMPLATES_DIR . '/components');
theme_define('THEME_CONTENT_DIR', THEME_TEMPLATES_DIR . '/content');
theme_define('THEME_FOOTER_DIR', THEME_TEMPLATES_DIR . '/footer');
theme_define('THEME_HEADER_DIR', THEME_TEMPLATES_DIR . '/header');
theme_define('THEME_MENUS_DIR', THEME_TEMPLATES_DIR . '/menu');
theme_define('THEME_PAGES_DIR', THEME_TEMPLATES_DIR . '/pages');
theme_define('THEME_SECTIONS_DIR', THEME_TEMPLATES_DIR . '/sections');