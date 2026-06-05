<?php
    // Site
    function theme_enqueue_site_font() {
        $font = get_theme_mod('site_font');

        if (!$font) {
            log_warn("Could not find Font setting", LOG_DOMAIN_FUNCTIONS);
            return;
        }

        if (theme_is_google_font($font)) {
            log_info("Requesting Google Font <{$font}> (Site)", LOG_DOMAIN_FUNCTIONS);
            theme_enqueue_google_font($font);
        }
    }
    add_action('wp_enqueue_scripts', 'theme_enqueue_site_font');

    // Customizer
    function theme_enqueue_customizer_css() {
        log_info("Requesting Customizer CSS", LOG_DOMAIN_FUNCTIONS);
        theme_enqueue_css('customizer/customizer-controls.css');
    }
    add_action('customize_controls_enqueue_scripts', 'theme_enqueue_customizer_css');

    function theme_customizer_preview() {
        theme_enqueue_js_module('customizer/customizer-preview.js');
    }
    add_action('customize_preview_init', 'theme_customizer_preview');

    function theme_customizer_controls() {
        theme_enqueue_js_module('customizer/customizer-controls.js');
    }
    add_action('customize_controls_enqueue_scripts', 'theme_customizer_controls');

    // Admin
    function theme_admin_enqueue_nav_menu_css($hook) {
        
        theme_enqueue_css('admin/nav-menu.css', [], null, 'all',
            function () use ($hook) {
                return $hook === 'nav-menus.php';
            }
        );
    }
    add_action('admin_enqueue_scripts', 'theme_admin_enqueue_nav_menu_css');
?>