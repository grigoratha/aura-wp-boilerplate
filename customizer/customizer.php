<?php
function theme_customizer_hide($wp_customize) {
    // Panels
    $panels = [
        'nav_menus'        // Menus
    ];

    foreach ($panels as $panel_id) {
        if ($panel = $wp_customize->get_panel($panel_id)) {
            $panel->active_callback = '__return_false';
            log_info("Removed Panel <{$panel_id}>", LOG_DOMAIN_CUSTOMIZER);
        }
    }

    // Sections
    $sections = [
        'title_tagline',   // Site Identity
        'custom_css'       // Additional CSS
    ];

    foreach ($sections as $section_id) {
        if ($section = $wp_customize->get_section($section_id)) {
            $section->active_callback = '__return_false';
            log_info("Removed Section <{$section_id}>", LOG_DOMAIN_CUSTOMIZER);
        }
    }
}
add_action('customize_register', 'theme_customizer_hide', 20);

function theme_customizer_build($wp_customize) {
    $json_file = THEME_CUSTOMIZER_DIR . '/customizer.json';

    if (!file_exists($json_file)) {
        log_error("Could not find <{$json_file}>", LOG_DOMAIN_CUSTOMIZER);
        return;
    }

    $config = json_decode(file_get_contents($json_file), true);
    if (!$config) {
        log_error("Could not decode or empty <{$json_file}>", LOG_DOMAIN_CUSTOMIZER);
        return;
    }

    // Panels
    if (!empty($config['panels'])) {
        foreach ($config['panels'] as $id => $panel) {
            $wp_customize->add_panel($id, $panel);
            log_info("Added Panel <{$id}>", LOG_DOMAIN_CUSTOMIZER);
        }
    }

    // Sections
    if (!empty($config['sections'])) {
        foreach ($config['sections'] as $id => $section) {
            $wp_customize->add_section($id, $section);
            log_info("Added Section <{$id}>", LOG_DOMAIN_CUSTOMIZER);
        }
    }

    // Settings
    if (!empty($config['settings'])) {
        foreach ($config['settings'] as $id => $setting) {
            $wp_customize->add_setting($id, $setting);
            log_info("Added Setting <{$id}>", LOG_DOMAIN_CUSTOMIZER);
        }
    }

    // Controls
    if (!empty($config['controls'])) {
        foreach ($config['controls'] ?? [] as $id => $control) {

            $type = $control['type'] ?? 'text';

            switch ($type) {
                case 'color':
                    $wp_customize->add_control(
                        new WP_Customize_Color_Control($wp_customize, $id, $control)
                    );
                    log_info("Added Color Control <{$id}>", LOG_DOMAIN_CUSTOMIZER);
                    break;
                case 'date':
                    $wp_customize->add_control(
                        new WP_Customize_Date_Control($wp_customize, $id, $control)
                    );
                    log_info("Added Date Control <{$id}>", LOG_DOMAIN_CUSTOMIZER);
                    break;
                case 'image':
                    $wp_customize->add_control(
                        new WP_Customize_Image_Control($wp_customize, $id, $control)
                    );
                    log_info("Added Image Control <{$id}>", LOG_DOMAIN_CUSTOMIZER);
                    break;
                case 'cropped_image':
                    $wp_customize->add_control(
                        new WP_Customize_Cropped_Image_Control($wp_customize, $id, $control)
                    );
                    log_info("Added Image Cropped Control <{$id}>", LOG_DOMAIN_CUSTOMIZER);
                    break;
                case 'media':
                    $wp_customize->add_control(
                        new WP_Customize_Media_Control($wp_customize, $id, $control)
                    );
                    log_info("Added Media Control <{$id}>", LOG_DOMAIN_CUSTOMIZER);
                    break;
                case 'select_font': // Custom
                    $wp_customize->add_control(
                        new WP_Customize_Font_Select_Control($wp_customize, $id, $control)
                    );
                    log_info("Added Select Control (Fonts) <{$id}>", LOG_DOMAIN_CUSTOMIZER);
                    break;
                case 'select_animation': // Custom
                    $wp_customize->add_control(
                        new WP_Customize_Animation_Select_Control($wp_customize, $id, $control)
                    );
                    log_info("Added Select Control (Animations) <{$id}>", LOG_DOMAIN_CUSTOMIZER);
                    break;
                case 'select':
                case 'radio':
                case 'checkbox':
                case 'textarea':
                case 'text':
                case 'number':
                case 'email':
                case 'range':
                    $wp_customize->add_control($id, $control);
                    log_info("Added {$type} Control <{$id}>", LOG_DOMAIN_CUSTOMIZER);
                    break;
                default:
                    // Fallback to text input if unknown type
                    $wp_customize->add_control($id, array_merge($control, ['type' => 'text']));
                    log_info("Added Default Control <{$id}>", LOG_DOMAIN_CUSTOMIZER);
                    break;
            }
        }
    }
}
add_action('customize_register', 'theme_customizer_build');

function theme_customizer_site_icon() {
    $icon_id = get_theme_mod('setting_site_icon');

    if ($icon_id) {
        return wp_get_attachment_image_url($icon_id);
    }

    return '';
}
add_filter('site_icon_url', 'theme_customizer_site_icon');

function theme_site_name() {
    return get_theme_mod('setting_site_info_name', '');
}
add_filter('pre_option_blogname', fn() => theme_site_name());

function theme_site_description() {
    return get_theme_mod('setting_site_info_description', '');
}
add_filter('pre_option_blogdescription', fn() => theme_site_description());

function theme_customizer_hex_color_to_rgb( $hex ) {
    $hex = ltrim( $hex, '#' );

    if ( strlen( $hex ) === 3 ) {
        $hex = "{$hex[0]}{$hex[0]}{$hex[1]}{$hex[1]}{$hex[2]}{$hex[2]}";
    }

    return [
        hexdec( substr( $hex, 0, 2 ) ),
        hexdec( substr( $hex, 2, 2 ) ),
        hexdec( substr( $hex, 4, 2 ) ),
    ];
}

function theme_customizer_hex_color_to_palette( $hex ) {
    $hex = ltrim( $hex, '#' );

    if ( strlen( $hex ) === 3 ) {
        $hex = "{$hex[0]}{$hex[0]}{$hex[1]}{$hex[1]}{$hex[2]}{$hex[2]}";
    }

    $r = hexdec( substr( $hex, 0, 2 ) );
    $g = hexdec( substr( $hex, 2, 2 ) );
    $b = hexdec( substr( $hex, 4, 2 ) );

    // Relative luminance (0 = black, 1 = white)
    $luminance = ( 0.2126 * $r + 0.7152 * $g + 0.0722 * $b ) / 255;

    $adjust = function ( $percent ) use ( $r, $g, $b ) {
        $calc = function ( $c ) use ( $percent ) {
            return max( 0, min( 255, round( $c + ( 255 * $percent ) ) ) );
        };

        return sprintf(
            '#%02x%02x%02x',
            $calc( $r ),
            $calc( $g ),
            $calc( $b )
        );
    };

    // Palette is generated based on luminance zones
    // Very Dark
    if ( $luminance < 0.20 ) {
        return [
            'contrast-1' => $adjust( 0.05 ),
            'contrast-2' => $adjust( 0.09 ),
            'contrast-3' => $adjust( 0.15 ),
            'contrast-4' => $adjust( 0.23 ),
            'contrast-5' => $adjust( 0.31 ),
            'contrast-6' => $adjust( 0.39 ),
            'reverse'    => "#f2f2f2",
        ];
    }
    // Very Light
    if ( $luminance > 0.80 ) {
        return [
            'contrast-1' => $adjust( -0.04 ),
            'contrast-2' => $adjust( -0.08 ),
            'contrast-3' => $adjust( -0.14 ),
            'contrast-4' => $adjust( -0.22 ),
            'contrast-5' => $adjust( -0.30 ),
            'contrast-6' => $adjust( -0.38 ),
            'reverse'    => "#1c1c1c",
        ];
    }
    // Midrange colors
    return [
        'contrast-1' => $adjust( -0.04 ),
        'contrast-2' => $adjust( -0.08 ),
        'contrast-3' => $adjust( -0.14 ),
        'contrast-4' => $adjust(  0.14 ),
        'contrast-5' => $adjust(  0.22 ),
        'contrast-6' => $adjust(  0.30 ),
        'reverse'    => "#a68585",
    ];
}

function theme_customizer_root_css_palette($hex, $rule_name) {
    if(!is_string($hex) || !preg_match('/^#?[0-9a-fA-F]{3,6}$/', $hex )) {
        log_warn("Invalid HEX Color <{$hex}>", LOG_DOMAIN_CUSTOMIZER);
        return;
    }

    if(!is_string($rule_name) || $rule_name === '') {
        log_warn("Invalid CSS Rule Name <{$rule_name}>", LOG_DOMAIN_CUSTOMIZER);
        return;
    }

    $palette = theme_customizer_hex_color_to_palette($hex);

    foreach ($palette as $variant => $color) {
        echo "{$rule_name}-{$variant}: {$color};\n";
    }
}

function theme_customizer_root_css() {
    $site_settings = get_theme_settings();
    ?>
    <style id="theme-site-root-css">
        :root {
            /* Menu Settings */
            --menu-font-family: <?php echo esc_html($site_settings['menu_font']); ?>;
            --menu-background-color: <?php echo esc_html($site_settings['menu_background_color']); ?>;
            --menu-item-color: <?php echo esc_html($site_settings['menu_item_color']); ?>;
            --menu-item-hover-color: <?php echo esc_html($site_settings['menu_item_hover_color']); ?>;
            --menu-item-font-size: <?php echo esc_html($site_settings['menu_item_font_size']); ?>em;
            --menu-dropdown-background-color: <?php echo esc_html($site_settings['submenu_background_color']); ?>;
            --menu-dropdown-item-color: <?php echo esc_html($site_settings['submenu_item_color']); ?>;
            --menu-dropdown-item-hover-color: <?php echo esc_html($site_settings['submenu_item_hover_color']); ?>;
            --menu-dropdown-item-font-size: <?php echo esc_html($site_settings['submenu_item_font_size']); ?>em;

            /*  Color Palette */
            <?php
            theme_customizer_root_css_palette($menu_settings['submenu_background_color'], "--menu-dropdown-background-color");
            ?>
        }
    </style>
    <?php
    log_info("Generating Site Settings CSS (Root)", LOG_DOMAIN_CUSTOMIZER);
}
add_action('wp_head', 'theme_customizer_root_css');


function sanitize_float($value) {
    return is_numeric($value) ? (float) $value : 1;
}

/* =========================================
   Custom controls
   ========================================= */
if (class_exists('WP_Customize_Control')) {

    class WP_Customize_Font_Select_Control extends WP_Customize_Control {
        public $type = 'select_font'; 
        public $template_file = '';

        public function __construct($manager, $id, $args = array()) {
            parent::__construct($manager, $id, $args);
        }

        public function render_content() {
            if (isset($this->label)) {
                echo '<label class="customize-control-title" for="' . esc_attr($this->id) . '">' . esc_html($this->label) . '</label>';
            }

            $json_file = THEME_CONFIG_DIR . '/fonts.json';

            if(!file_exists($json_file)) {
                echo '<div class="customize-control-warning">⚠️ Could not load available fonts</div>';
                log_error("Could not find <{$json_file}>", LOG_DOMAIN_CUSTOMIZER, "WP_Customize_Font_Select_Control Class");
                return;
            }

            $fonts_data = theme_decode_json_file($json_file);
            $has_fonts = !empty($fonts_data);

            if(!$has_fonts) {
                echo '<div class="customize-control-warning">⚠️ No available fonts</div>';
                log_warn("Could not decode or empty <{$json_file}>", LOG_DOMAIN_CUSTOMIZER, "WP_Customize_Font_Select_Control Class");
                return;
            }

            echo '<select id="' . esc_attr($this->id) . '" ';
            $this->link();
            echo '>';

            foreach ($fonts_data as $provider => $categories) { 
                // Font Provider
                echo '<optgroup label="' . ucfirst($provider) . '">';

                foreach ($categories as $category => $fonts) { 
                    // Font category
                    echo '<option value="" disabled>-- ' . ucfirst($category) . ' --</option>';

                    foreach ($fonts as $font) { 
                        // Font
                        $font_css = str_replace("'", "\\'", $font);
                        echo '<option value="' . esc_attr($font) . '" style="font-family:\'' . esc_attr($font_css) . '\';">' . esc_html($font) . '</option>';
                    }
                }
                echo '</optgroup>';
            }
            echo '</select>';
        }
    }
}

if (class_exists('WP_Customize_Control')) {

    class WP_Customize_Animation_Select_Control extends WP_Customize_Control
    {
        public $type = 'select_animation';

        protected function format_animation_label(string $key): string
        {
            $map = [
                // directions
                'top'    => 'Top',
                'right'  => 'Right',
                'bottom' => 'Bottom',
                'left'   => 'Left',
                'center' => 'Center',
                // corners
                'tr' => 'Top Right',
                'tl' => 'Top Left',
                'br' => 'Bottom Right',
                'bl' => 'Bottom Left',
                // axes
                'hor'  => 'Horizontal',
                'ver'  => 'Vertical',
                'vert' => 'Vertical',
                // motion
                'cw'  => 'Clockwise',
                'ccw' => 'Counter Clockwise',
                'fwd' => 'Forward',
                'bck' => 'Backward',
                // misc
                'bg'        => 'Background',
                'pan'       => 'Pan',
                'kenburns'  => 'Ken Burns',
            ];

            $tokens = explode('-', $key);

            // Base animation (first token)
            $base = ucfirst(array_shift($tokens));

            $modifiers = [];

            foreach ($tokens as $token) {
                if (is_numeric($token)) {
                    $modifiers[] = $token;
                } else {
                    $modifiers[] = $map[$token] ?? ucfirst($token);
                }
            }

            return empty($modifiers)
                ? $base
                : sprintf('%s (%s)', $base, implode(' ', $modifiers));
        }

        public function render_content()
        {
            if (!empty($this->label)) {
                echo '<label class="customize-control-title" for="' . esc_attr($this->id) . '">' . esc_html($this->label) . '</label>';
            }

            $json_file = THEME_CONFIG_DIR . '/animista.json';

            if(!file_exists($json_file)) {
                echo '<div class="customize-control-warning">⚠️ Could not load available animations</div>';
                log_error("Could not find <{$json_file}>", LOG_DOMAIN_CUSTOMIZER, "WP_Customize_Animation_Select_Control");
                return;
            }

            $animations = theme_decode_json_file($json_file);
            $has_animations = !empty($animations);

            if(!$has_animations) {
                echo '<div class="customize-control-warning">⚠️ No available animations</div>';
                log_warn("Could not decode or empty <{$json_file}>", LOG_DOMAIN_CUSTOMIZER, "WP_Customize_Animation_Select_Control");
                return;
            }

            echo '<select id="' . esc_attr($this->id) . '" ';
            $this->link();
            echo '>';

            //
            echo '<option value="none">None</option>';
            foreach ($animations as $category => $data) {
                echo '<optgroup label="' . esc_attr(ucfirst($category)) . '">';

                foreach ($data['groups'] as $group) {
                    foreach ($group['variations'] as $key => $_) {
                        echo sprintf(
                            '<option value="%s">%s</option>',
                            esc_attr($key),
                            esc_html($this->format_animation_label($key))
                        );
                    }
                }
                echo '</optgroup>';
            }
            echo '</select>';
        }
    }
}

if (class_exists('WP_Customize_Control')) {
    class WP_Customize_Date_Control extends WP_Customize_Control {
        public $type = 'date';

        public function render_content() {
            if (!empty($this->label)) {
                echo '<label>' . esc_html($this->label) . '</label>';
            }

            echo '<input type="date" ' . $this->get_link() .
                ' value="' . esc_attr($this->value()) . '" />';
        }
    }
}
?>
