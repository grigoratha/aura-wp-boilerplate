<?php
function theme_enqueue_css($src, $deps = [], $version = null, $media = 'all', $condition = null) {
    if (is_callable($condition) && !$condition()) {
        return;
    }

    // Normalize
    $files = is_array($src) ? $src : [$src];

    foreach ($files as $file) {
        // Generate style name
        $theme_name = THEME_SLUG;
        $filename   = pathinfo($file, PATHINFO_FILENAME);
        $handle     = "{$theme_name}-css-" . sanitize_title($filename);

        // Theme version fallback
        $file_version = $version ?? wp_get_theme()->get('Version');

        // URI
        $file_uri = theme_URL(THEME_CSS_DIR) . '/' . $file;

        // Check if exists
        $file_path = THEME_CSS_DIR . '/' . $file;

        if (!file_exists($file_path)) {
            log_warn("Could not find CSS file <{$file_path}>", LOG_DOMAIN_ENQUEUE);
            continue;
        }

        wp_enqueue_style($handle, $file_uri, $deps, $file_version, $media);
        log_info("Loading CSS <{$file_uri}>", LOG_DOMAIN_ENQUEUE);
    }
}

function theme_enqueue_css_for($page_ids, $src, $deps = [], $version = null, $media = 'all') {

    $page_ids = is_array($page_ids) ? $page_ids : [$page_ids];

    if (!is_page($page_ids)) {
        return;
    }

    theme_enqueue_css($src, $deps, $version, $media);
}

function theme_enqueue_js($src, $deps = [], $version = null, $inject_in_footer = true, $condition = null) {
    if (is_callable($condition) && !$condition()) {
        return;
    }

    // Normalize
    $files = is_array($src) ? $src : [$src];

    foreach ($files as $file) {
        // Generate script name
        $theme_name = THEME_SLUG;
        $filename   = pathinfo($file, PATHINFO_FILENAME);
        $handle     = "{$theme_name}-js-" . sanitize_title($filename);

        // Theme version fallback
        $file_version = $version ?? wp_get_theme()->get('Version');

        // URI
        $file_uri = theme_URL(THEME_JS_DIR) . '/' . $file;

        // Check if exists
        $file_path = THEME_JS_DIR . '/' . $file;

        if (!file_exists($file_path)) {
            log_warn("Could not find JS file <{$file_path}>", LOG_DOMAIN_ENQUEUE);
            continue;
        }

        wp_enqueue_script($handle, $file_uri, $deps, $file_version, $inject_in_footer);
        log_info("Loading JS <{$file_uri}>", LOG_DOMAIN_ENQUEUE);
    }
}

function theme_enqueue_js_ajax($src, $deps = [], $version = null, $inject_in_footer = true, $condition = null) {

    if (is_callable($condition) && !$condition()) {
        return;
    }

    $files = is_array($src) ? $src : [$src];

    foreach ($files as $file) {

        $theme_name   = THEME_SLUG;
        $filename     = pathinfo($file, PATHINFO_FILENAME);
        $handle       = "{$theme_name}-js-" . sanitize_title($filename);

        $file_version = $version ?? wp_get_theme()->get('Version');
        $file_uri     = theme_URL(THEME_JS_DIR) . '/' . $file;
        $file_path    = THEME_JS_DIR . '/' . $file;

        // Validate file exists
        if (!file_exists($file_path)) {
            log_warn("Could not find JS file <{$file_path}>", LOG_DOMAIN_ENQUEUE);
            continue;
        }

        // Enqueue script
        wp_enqueue_script($handle, $file_uri, $deps, $file_version, $inject_in_footer);
        log_info("Loading JS <{$file_uri}>", LOG_DOMAIN_ENQUEUE);

        // Inject AJAX config
        wp_add_inline_script(
            $handle,
            'window.themeConfig = window.themeConfig || {}; Object.assign(window.themeConfig, ' . wp_json_encode([
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('theme_ajax_nonce'),
            ]) . ');',
            'before'
        );

        log_info("Injecting AJAX config into <{$handle}>", LOG_DOMAIN_ENQUEUE);
    }
}

function theme_enqueue_js_for($page_ids, $src, $deps = [], $version = null, $inject_in_footer = true, $condition = null) {

    $page_ids = is_array($page_ids) ? $page_ids : [$page_ids];

    if (!is_page($page_ids)) {
        return;
    }

    theme_enqueue_js($src, $deps, $version, $inject_in_footer, $condition);
}

function theme_enqueue_js_module($src, $deps = [], $version = null, $inject_in_footer = true, $condition = null) {
    if (is_callable($condition) && !$condition()) {
        return;
    }

    // Normalize
    $files = is_array($src) ? $src : [$src];

    foreach ($files as $file) {
        // Generate script name
        $theme_name = THEME_SLUG;
        $filename   = pathinfo($file, PATHINFO_FILENAME);
        $handle     = "{$theme_name}-js-module-" . sanitize_title($filename);

        // Theme version fallback
        $file_version = $version ?? wp_get_theme()->get('Version');

        // URI
        $file_uri = theme_URL(THEME_JS_DIR) . '/' .$file;

        // Enqueue script with type="module"
       wp_enqueue_script_module($handle, $file_uri, $deps, $file_version, ['footer' => $inject_in_footer ]);
       log_info("Loading JS Module <{$file_uri}>", LOG_DOMAIN_ENQUEUE);
    }
}

function theme_enqueue_package($package, $deps = [], $condition = null, $inject_js_in_footer = true)  {
    if (is_callable($condition) && !$condition()) {
        return;
    }

    $theme_name    = THEME_SLUG;
    $package_dir   = THEME_PACKAGES_DIR . '/' . $package;
    $package_uri   = theme_URL(THEME_PACKAGES_DIR) . '/' . $package;
    $theme_version = wp_get_theme()->get('Version');

    if (!is_dir($package_dir)) {
        log_error("Could not find Package <{$package_dir}>", LOG_DOMAIN_ENQUEUE);
        return;
    }

    $files = array_diff(scandir($package_dir), ['.', '..']);
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);

    $css_files = [];
    $js_files  = [];

    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if ($ext === 'css') {
            $css_files[] = $file;
        } elseif ($ext === 'js') {
            $js_files[] = $file;
        }
    }

    // Enqueue CSS
    foreach ($css_files as $file) {
        $filename = pathinfo($file, PATHINFO_FILENAME);
        $handle   = "{$theme_name}-pkg-{$package}-" . sanitize_title($filename) . "-css";
        $uri      = "{$package_uri}/{$file}";

        wp_enqueue_style($handle, $uri, $deps, $theme_version, 'all');

        log_info("Loading Package {$package} | CSS <{$uri}>", LOG_DOMAIN_ENQUEUE);
    }

    // Enqueue JS
    foreach ($js_files as $file) {
        $filename = pathinfo($file, PATHINFO_FILENAME);
        $handle   = "{$theme_name}-pkg-{$package}-" . sanitize_title($filename) . "-js";
        $uri      = "{$package_uri}/{$file}";

        wp_enqueue_script($handle, $uri, $deps, $theme_version, $inject_js_in_footer);

        log_info("Loading Package {$package} | JS <{$uri}>", LOG_DOMAIN_ENQUEUE);
    }
}

function theme_enqueue_package_for($page_ids, $package, $deps = [], $condition = null, $inject_js_in_footer = true) {

    $page_ids = is_array($page_ids) ? $page_ids : [$page_ids];

    if (!is_page($page_ids)) {
        return;
    }

    theme_enqueue_package($package, $deps, $condition, $inject_js_in_footer);
}

function theme_enqueue_google_font(string $family, array $weights = [300, 400, 500, 600, 700]) {
    if (empty($family)) {
        return;
    }

    $family = trim($family);
    $handle = THEME_SLUG . '-google-font-' . sanitize_title($family);

    if (wp_style_is($handle, 'enqueued')) {
        return;
    }

    $family_slug = str_replace(' ', '+', $family);
    $weights_str = implode(';', array_map('intval', $weights));

    $href = sprintf(
        'https://fonts.googleapis.com/css2?family=%s:wght@%s&display=swap',
        $family_slug,
        $weights_str
    );

    wp_enqueue_style($handle, $href, [], null);
    log_info("Loading Google Font <{$family}>", LOG_DOMAIN_ENQUEUE);
}