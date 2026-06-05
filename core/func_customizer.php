<?php
function get_customizer_settings(array $keys, $fetch_images_url = true) {
    $settings = [];
    $media_suffixes = ['_video'];
   

    foreach ($keys as $key) {
        $value = get_theme_mod($key) ?? '';

        // Return URL for media types
        foreach($media_suffixes as $media_suffix) {
            if((str_ends_with($key, $media_suffix))) {
                $value = wp_get_attachment_url($value) ?: '';
                break;
            }
        }

        $settings[$key] = $value;
        log_info("Loading Setting <{$key}: {$value}>", LOG_DOMAIN_CUSTOMIZER);
    }

    return $settings;
}

function get_theme_settings() {

    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    $settings = [];

    $keys = [
        'site_icon',
        'site_name',
        'site_description',

        'menu_font',
        'menu_background_color',
        'menu_item_color',
        'menu_item_hover_color',
        'menu_item_font_size',
        'submenu_background_color',
        'submenu_item_color',
        'submenu_item_hover_color',
        'submenu_item_font_size',

        'maintenance_mode',
    ];

    foreach ($keys as $key) {

        $value = get_theme_mod($key, '');

        $settings[$key] = $value;

        log_info(
            "Loading Theme Setting <{$key}: {$value}>",
            LOG_DOMAIN_CUSTOMIZER
        );
    }

    $cache = $settings;

    return $cache;
}
?>