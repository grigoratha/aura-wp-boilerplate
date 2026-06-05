<?php
function theme_render_header($name = '') {
    if($name) {
        $file = THEME_HEADER_DIR . "/header-{$name}.php";
    }
    else {
        $file = THEME_HEADER_DIR . "/header.php";
    }

    if (file_exists($file)) {
        include $file;
        log_info("Loading Header <{$file}>", LOG_DOMAIN_CORE);
    }
    else {
        log_error("Could not find Header <{$file}>", LOG_DOMAIN_CORE);
    }
}

function theme_render_footer($name = '') {
    if($name) {
        $file = THEME_FOOTER_DIR . "/footer-{$name}.php";
    }
    else {
        $file = THEME_FOOTER_DIR . "/footer.php";
    }

    if (file_exists($file)) {
        include $file;
        log_info("Loading Footer <{$file}>", LOG_DOMAIN_CORE);
    }
    else {
        log_error("Could not find Footer <{$file}>", LOG_DOMAIN_CORE);
    }
}

function theme_render_menu(string $name = '', string $location = 'primary') {

    if ($name) {
        $file = THEME_MENUS_DIR . "/menu-{$name}.php";
    } else {
        $file = THEME_MENUS_DIR . "/menu.php";
    }

    if (!file_exists($file)) {
        log_error("Could not find menu <{$file} | {$location}>", LOG_DOMAIN_CORE);
        return;
    }

    // Template arguments
    $menu_location = $location;
    include $file;

    log_info("Loading Menu <{$file} | {$location}>", LOG_DOMAIN_CORE);
}

function theme_render_menu_item($menu_item, $icon) {
    $url   = esc_url($menu_item['url']);
    $title = esc_html($menu_item['title']);

    $has_subitems = !empty($menu_item['children']);

    $target = !empty($menu_item['target']) ? " target='" . esc_attr($menu_item['target']) . "'" : "";
    $rel    = ($menu_item['target'] ?? '') === '_blank' ? " rel='noopener noreferrer'" : "";

    $attrs = "{$target}{$rel}";

    if(!$icon || empty($icon['html'])) {
        echo "<a href='{$url}'>";
        echo    "<span>{$title}</span>";
        if($has_subitems) {
        echo    theme_render_caret_icon();
        }
        echo "</a>";
        return;
    }

    $icon_html = $icon['html'];
    $icon_position = $icon['position'];

    if($icon_position === 'before') {
        echo "<a href='{$url}'{$attrs}>{$icon_html}";
        echo    "<span class='menu-label menu-icon-left'>{$title}</span>";
        if($has_subitems) {
        echo    theme_render_caret_icon();
        }
        echo "</a>";
    }
    else {
        echo "<a href='{$url}'{$attrs}>";
        echo    "<span class='menu-label menu-icon-right'>{$title}</span>{$icon_html}";
        if($has_subitems) {
        echo    theme_render_caret_icon();
        }
        echo "</a>";
    }
}

function theme_render_caret_icon() {

    return '
    <svg class="menu-caret-icon"
         xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 640 480"
         aria-hidden="true">
        <path fill="currentColor" fill-rule="nonzero"
              d="M42.47.01 469.5 0C492.96 0 512 19.04 512 42.5c0 11.07-4.23 21.15-11.17 28.72L294.18 320.97c-14.93 18.06-41.7 20.58-59.76 5.65-1.8-1.49-3.46-3.12-4.97-4.83L10.43 70.39C-4.97 52.71-3.1 25.86 14.58 10.47 22.63 3.46 32.57.02 42.47.01z"/>
    </svg>';
}

function theme_render_page($slug) {
    $file = THEME_PAGES_DIR . "/page-{$slug}.php";

    if (file_exists($file)) {
        include $file;
        log_info("Loading Page <{$file}>", LOG_DOMAIN_CORE);
    }
    else {
        log_error("Could not find Page <{$file}>", LOG_DOMAIN_CORE);
    }
}

function theme_render_single($slug, $data = []) {

    $file = THEME_SINGLES_DIR . "/single-{$slug}.php";

    if (!file_exists($file)) {
        log_error("Could not find Single <{$file}>", LOG_DOMAIN_CORE);
        return;
    }

    // expose data to template
    extract($data, EXTR_SKIP);

    include $file;

    log_info("Loading Single <{$file}>", LOG_DOMAIN_CORE);
}

function theme_render_post($slug) {
    $file = THEME_POSTS_DIR . "/post-{$slug}.php";

    if (file_exists($file)) {
        include $file;
        log_info("Loading Post <{$file}>", LOG_DOMAIN_CORE);
    }
    else {
        log_error("Could not find Post <{$file}>", LOG_DOMAIN_CORE);
    }
}

function theme_render_template($name, array $args = []) {
    $file = THEME_TEMPLATES_DIR . "/{$name}.php";

    if (file_exists($file)) {
        // Expose arguments
        if (!empty($args)) {

            extract($args, EXTR_SKIP);
        }

        include $file;
        log_info("Loading Template <{$file}>", LOG_DOMAIN_CORE);
    }
    else {
        log_error("Could not find Template <{$file}>", LOG_DOMAIN_CORE);
    }
}

function theme_has_posts($slug) {
    if (empty($slug)) {
        return false;
    }

    $category = get_term_by('slug', $slug, 'category');

    if (!$category || is_wp_error($category)) {
        return false;
    }

    return ((int) $category->count > 0);
}

function theme_get_posts($slug, $limit = 10, $paged = 1, $orderby = 'date', $order = 'DESC') {

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => $limit,
        'orderby'        => $orderby,
        'order'          => $order,
        'paged'          => $paged,
        'post_status'    => 'publish'
    ];

    if (!empty($slug)) {
        $args['category_name'] = $slug;
    }

    return new WP_Query($args);
}
?>