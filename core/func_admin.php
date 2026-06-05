<?php
function theme_supported_plugins_list() {
    return [
        'leaflet-map' => ['name' => 'Leaflet Map', 'required' => false, 'reason' => 'Enables interactive maps support.',],
        'menu-icons' => ['name'=> 'Menu Icons by ThemeIsle','required' => false, 'reason'=> 'Enables navigation menu icon support',],
    ];
}

function theme_tgma_check_supported_plugins() {

    $plugins = [];

    foreach (theme_supported_plugins_list() as $slug => $plugin) {
        $plugins[] = [
            'name'     => $plugin['name'],
            'slug'     => $slug,
            'required' => $plugin['required'],
        ];
    }

    tgmpa($plugins, [
        'id'           => THEME_SLUG . '-tgm',
        'menu'         => 'tgmpa-install-plugins',
        'parent_slug'  => 'themes.php',
        'has_notices'  => false,
    ]);
}
add_action('tgmpa_register', 'theme_tgma_check_supported_plugins');

function theme_notice_supported_plugins() {

    if (!current_user_can('install_plugins')) {
        log_warn("Current user can not install plugins", LOG_DOMAIN_ADMIN);
        return;
    }

    $user_id = get_current_user_id();

    // Dismissed by user
    if (get_user_meta($user_id, 'theme-notice-supported-plugins-dismiss', true)) {
        return;
    }

    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    $missing = [];

    foreach (theme_supported_plugins_list() as $slug => $plugin) {
        if (!is_plugin_active($slug . '/' . $slug . '.php')) {
            $missing[$slug] = $plugin;
        }
    }

    if (empty($missing)) {
        return;
    }

    $install_url = admin_url('themes.php?page=tgmpa-install-plugins');
    $dismiss_url = wp_nonce_url( add_query_arg('theme-notice-supported-plugins-dismiss', '1'), 'theme_notice_supported_plugins_dismiss');

    echo '<div class="notice notice-info is-dismissible">';
    echo '  <div class="plugin-notice-container">';
    echo '      <p>ℹ️ <strong>' . esc_html(THEME_NAME) . ' theme recommends the following plugins for enhanced functionality:<strong></p>';
    echo '      <table class="widefat striped" style="max-width:600px;">';
    echo '          <thead>';
    echo '              <tr>';
    echo '                  <th>Plugin</th>';
    echo '                  <th>Functionality</th>';
    echo '              </tr>';
    echo '          </thead>';
    echo '          <tbody>';
    foreach ($missing as $plugin) {
        echo '      <tr>';
        echo '          <td>📌 <strong>' . esc_html($plugin['name']) . '</strong></td>';
        echo '          <td style="font-weight:normal; font-style:italic;">' . esc_html($plugin['reason']) . '</td>';
        echo '      </tr>';
    }
    echo '          </tbody>';
    echo '      </table>';
    echo '      <p style="margin-top: 1em;">';
    echo '          <a href="' . esc_url($install_url) . '" class="button button-primary">Install Plugins</a>';
    echo '          <a href="' . esc_url($dismiss_url) . '" class="button button-secondary">Dismiss</a>';
    echo '      </p>';
    echo '  </div>';
    echo '</div>';
}
add_action('admin_notices', 'theme_notice_supported_plugins');

function theme_notice_supported_plugins_dismiss() {

    if (isset($_GET['theme-notice-supported-plugins-dismiss']) &&  check_admin_referer('theme_notice_supported_plugins_dismiss')) {

        update_user_meta( get_current_user_id(), 'theme-notice-supported-plugins-dismiss', 1);
        wp_safe_redirect( remove_query_arg(['theme-notice-supported-plugins-dismiss', '_wpnonce']));
        exit;
    }
}
add_action('admin_init', 'theme_notice_supported_plugins_dismiss');

function theme_notice_supported_plugins_dismiss_reset() {
    $user_id = get_current_user_id();
    delete_user_meta((int) $user_id, 'theme-notice-supported-plugins-dismiss');
}

function theme_register_admin_page($slug, $callback, $title = null, $capability = 'manage_options') {
    add_action('admin_menu', function () use ($slug, $callback, $title, $capability) {

        add_menu_page(
            $title,        // Page title 
            $title,        // Menu title
            $capability,   // Permission required
            $slug,         // URL slug (?page=slug)
            $callback      // Page renderer callback
        );

    });
}

function theme_render_admin_page($slug) {
    $file = THEME_ADMIN_DIR . "/pages/{$slug}.php";

    if (file_exists($file)) {
        include $file;
        log_info("Loading Admin page <{$file}>", LOG_DOMAIN_CORE);
    }
    else {
        log_error("Could not find Admin page <{$file}>", LOG_DOMAIN_CORE);
    }
}

function theme_enqueue_admin_css($src, $deps = [], $version = null, $media = 'all', $condition = null) {

    $files = is_array($src) ? $src : [$src];

    foreach ($files as $file) {

        $file_version = $version ?? wp_get_theme()->get('Version');
        $file_uri = theme_URL(THEME_CSS_DIR) . '/' . $file;

        wp_enqueue_style(
            THEME_SLUG . '-admin-' . sanitize_title(pathinfo($file, PATHINFO_FILENAME)),
            $file_uri,
            $deps,
            $file_version,
            $media
        );
    }
}

function theme_enqueue_admin_css_for($target_slug, $src, $deps = [], $version = null, $media = 'all') {

    add_action('admin_enqueue_scripts', function ($hook) use (
        $target_slug, $src, $deps, $version, $media
    ) {

        // Convert slug → WordPress hook
        $expected_hook = 'toplevel_page_' . $target_slug;

        if ($hook !== $expected_hook) {
            return;
        }

        $files = is_array($src) ? $src : [$src];

        foreach ($files as $file) {

            $filename = pathinfo($file, PATHINFO_FILENAME);

            $handle = THEME_SLUG . '-admin-' . sanitize_title($filename);

            $file_uri = theme_URL(THEME_CSS_DIR) . '/' . $file;

            wp_enqueue_style(
                $handle,
                $file_uri,
                $deps,
                $version ?? wp_get_theme()->get('Version'),
                $media
            );

            log_info("Loading Admin CSS <{$file_uri}>", LOG_DOMAIN_ENGUEUE);
        }
    });
}
?>