<?php
function theme_URL($dir) {
    $dir = untrailingslashit($dir);
    $theme_dir = untrailingslashit(THEME_DIR);

    $url = str_replace($theme_dir, THEME_URL, $dir);

    return $url;
}

function theme_maintenance_mode() {

    if (!theme_is_maintenance_mode()) {
        return;
    }
	
    if (is_admin()) {
        return;
    }

    if (wp_doing_ajax() || wp_doing_cron() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    status_header(503);
    nocache_headers();

    include get_template_directory() . '/page-maintenance.php';
	
    exit;
}
add_action('template_redirect', 'theme_maintenance_mode', 0);

function theme_is_maintenance_mode() {
	$settings = get_theme_settings();
	return (bool) ($settings['maintenance_mode'] ?? false);
}

function is_403() {
    return defined('THEME_IS_403') && THEME_IS_403;
}

function theme_support_svg($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'theme_support_svg');

function theme_support_menus() {

    $menus = [
        'primary' => 'Primary Menu',
        'footer'  => 'Footer Menu',
    ];

    $menus = apply_filters('theme_nav_menus', $menus);

    register_nav_menus($menus);
}
add_action('after_setup_theme', 'theme_support_menus');

function theme_support_feature_image() {
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'theme_support_feature_image');

function theme_decode_json_file($json_file) {

    if (file_exists($json_file)) {
        $json_data = file_get_contents($json_file);
        return json_decode($json_data, true);
    } 
    else {
        log_error("Could not find <{$json_file}>", LOG_DOMAIN_FUNCTIONS);
        return null;
    }
}

function theme_is_google_font(string $family): bool {
    if (empty($family)) {
        log_warn("No font name has been supplied", LOG_DOMAIN_FUNCTIONS);
        return false;
    }

    $fonts = theme_decode_json_file(THEME_CONFIG_DIR . '/fonts.json');

    if (empty($fonts['google'])) {
        log_warn("No google fonts available", LOG_DOMAIN_FUNCTIONS);
        return false;
    }

    foreach ($fonts['google'] as $category => $list) {
        if (in_array($family, $list, true)) {
            return true;
        }
    }

    return false;
}

function theme_get_menu_tree(string $location): array {
    $locations = get_nav_menu_locations();

    if (!isset($locations[$location])) {
        log_warn("Unregistered location <{$location}>", LOG_DOMAIN_CORE);
        return [];
    }

    if (!has_nav_menu($location)) {
        log_warn("No registered menu in location <{$location}>", LOG_DOMAIN_CORE);
        return [];
    }

    $menu = wp_get_nav_menu_object($locations[$location]);
    $items = wp_get_nav_menu_items($menu->term_id);

    if (!$items) {
        log_warn("Could not find menu items or empty <{$menu->name} | {$location}>", LOG_DOMAIN_CORE);
        return [];
    }

    $tree    = [];
    $indexed = [];

    // Normalize
    foreach ($items as $item) {

        // ThemeIsle Menu Icons metadata
        $icon_meta = maybe_unserialize(
            get_post_meta($item->ID, 'menu-icons', true)
        );

        $icon_meta = is_array($icon_meta) ? $icon_meta : null;

        $icon = null;

        if ($icon_meta && !empty($icon_meta['icon'])) {
            $icon = [
                'value'    => $icon_meta['icon'],
                'position' => $icon_meta['position'] ?? 'before',
                'size'     => !empty($icon_meta['font_size']) ? $icon_meta['font_size'] . 'em' : null,
            ];
        }

        $indexed[$item->ID] = [
            'id'       => (int) $item->ID,
            'title'    => $item->title,
            'url'      => $item->url,
            'target'   => $item->target ?: null,
            'classes'  => array_filter($item->classes ?? []),
            'parent'   => (int) $item->menu_item_parent,
            'order'    => (int) $item->menu_order,
            'type'     => $item->type,
            'children' => [],
            'icon'     => $icon,
        ];
    }

    foreach ($indexed as $id => &$node) {
        if ($node['parent'] && isset($indexed[$node['parent']])) {
            $indexed[$node['parent']]['children'][] = &$node;
        } 
        else {
            $tree[] = &$node;
        }
    }
    unset($node);

    // Sort
    $sort_tree = function (array &$nodes) use (&$sort_tree) {
        usort($nodes, fn($a, $b) =>
            ($a['order'] <=> $b['order']) ?: ($a['id'] <=> $b['id'])
        );

        foreach ($nodes as &$node) {
            if (!empty($node['children'])) {
                $sort_tree($node['children']);
            }
        }
        unset($node);
    };

    $sort_tree($tree);

    return $tree;
}

function theme_get_menu_icon(?array $icon) {
    // This function references the ThemeIsle Menu Icons Plugin
    if (empty($icon) || empty($icon['value'])) {
        return null;
    }

    $styles = [];

    // Size
    if (!empty($icon['size'])) {
        $styles[] = 'font-size:' . esc_attr($icon['size']);
    }
   
    $styles[] = '';

    $style_attributes = $styles ? ' style="' . esc_attr(implode(';', $styles)) . '"' : '';

    return [
        'html' => sprintf('<i class="%s menu-icon" aria-hidden="true"%s></i>', esc_attr($icon['value']), $style_attributes),
        'position' => ($icon['position'] ?? 'before') === 'after' ? 'after' : 'before',
    ];
}
?>