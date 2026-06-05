<?php

/*  _                     __  __ _       _                 _ 
   / \  _   _ _ __ __ _  |  \/  (_)_ __ (_)_ __ ___   __ _| |
  / _ \| | | | '__/ _` | | |\/| | | '_ \| | '_ ` _ \ / _` | |
 / ___ \ |_| | | | (_| | | |  | | | | | | | | | | | | (_| | |
/_/   \_\__,_|_|  \__,_| |_|  |_|_|_| |_|_|_| |_| |_|\__,_|_|
*/

if (!defined('ABSPATH')) {
    exit;
}

function theme_define($name, $value) {
    if (!defined($name)) {
        define($name, $value);
    }
}

/* =========================================
   Theme Setup
   ========================================= */
require_once get_template_directory() . '/bootstrap/bootstrap.php';

/* =========================================
   Theme Resources
   ========================================= */
add_action('wp_enqueue_scripts', function () {
    theme_enqueue_js('/core/ajax.js');
    theme_enqueue_css('/core/menu-common.css');
});

/* =========================================
   Customizer Resources
   ========================================= */
add_action('customize_controls_enqueue_scripts', function () {
    theme_enqueue_js('/core/ajax.js');
    theme_enqueue_js('/customizer/customizer.js');
});

/* =========================================
   Admin Pages
   ========================================= */
theme_register_admin_page('log-viewer', function () {theme_render_admin_page('log-viewer');}, 'Log Viewer');

add_action('admin_enqueue_scripts', function () {
    theme_enqueue_admin_css('/admin/admin.css');
    theme_enqueue_admin_css("/admin/log-viewer.css");
    theme_enqueue_js_ajax("/core/ajax.js");
});