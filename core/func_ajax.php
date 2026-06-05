<?php
// Admin
function theme_clear_log_ajax() {

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized'], 403);
    }

    $result = log_clear();

    if (empty($result['success'])) {
        wp_send_json_error([
            'message' => $result['error'] ?? 'Unknown error'
        ]);
    }

    wp_send_json_success([
        'message' => $result['message'] ?? 'OK'
    ]);
}
add_action('wp_ajax_theme_clear_log', 'theme_clear_log_ajax');

function theme_load_posts() {

    $paged     = isset($_POST['paged']) ? max(1, intval($_POST['paged'])) : 1;
    $slug      = isset($_POST['slug']) ? sanitize_text_field($_POST['slug']) : '';
    $limit     = isset($_POST['limit']) ? intval($_POST['limit']) : 10;

    $query = theme_get_posts($slug, $limit, $paged);

    // Transform post data
    $posts = [];

    if ($query->have_posts()) {
        foreach ($query->posts as $post) {

            $posts[] = [
                'id'            => $post->ID,
                'title'         => get_the_title($post),
                'slug'          => $post->post_name,
                'content'       => apply_filters('the_content', $post->post_content),
                'excerpt'       => get_the_excerpt($post),
                'status'        => $post->post_status,
                'type'          => $post->post_type,
                'date'          => $post->post_date,
                'modified'      => $post->post_modified,
                'url'           => get_permalink($post),
                'featured_image'=> get_the_post_thumbnail_url($post->ID, 'full'),
            ];
        }
    }

    // Clean post data
    wp_reset_postdata();

    wp_send_json_success([
        'posts'         => $posts,
        'max_pages'     => $query->max_num_pages,
        'total_posts'   => $query->found_posts,
    ]);
}

function theme_load_posts_html() {

    $paged     = isset($_POST['paged']) ? max(1, intval($_POST['paged'])) : 1;
    $slug      = isset($_POST['slug']) ? sanitize_text_field($_POST['slug']) : '';
    $limit     = isset($_POST['limit']) ? intval($_POST['limit']) : 10;

    // Page settings
    $default_image = null;
    $default_animation = null;
    $fallback_image = get_template_directory_uri() . '/wp-content/uploads/2026/05/no-image-5.jpg';

    switch ($slug) {
        case 'news':
            $settings = get_news_customizer_settings();
            $default_image = $settings['news_default_img'] ?? $fallback_image;
            $default_animation = $settings['news_post_animation'] ?? 'fade-in';
            break;
        case 'motorcycles':
            $settings = get_motorcycles_customizer_settings();
            $default_image = $settings['motorcycles_default_img'] ?? $fallback_image;
            $default_animation = $settings['motorcycles_post_animation'] ?? 'fade-in';
            break;
        case 'accessories':
            $settings = get_accessories_customizer_settings();
            $default_image = $settings['accessories_default_img'] ?? $fallback_image;
            $default_animation = $settings['accessories_post_animation'] ?? 'fade-in';
            break;
        default:
            $default_image = get_template_directory_uri() . '/assets/images/default.jpg';
            $default_animation = "fade-in";
    }

    // No content post
    $no_content_map = [
        'news'        => 290,
        'motorcycles' => 288,
        'accessories' => 286,
    ];

    $query = theme_get_posts($slug, $limit, $paged);

    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $card = [
                'title'         => get_the_title(),
                'text'          => get_the_excerpt(),
                'url'           => get_permalink(),
                'image'         => get_the_post_thumbnail_url(get_the_ID(), 'large') ?: $default_image,
                'image_class'   => has_post_thumbnail() ? '' : 'na-image',
                'animation'     => $default_animation
            ];

            include get_template_directory() . "/content/templates/card-post.php";
        }
    }
     else {

        // Map to page ID
        $page_id = $no_content_map[$slug] ?? null;
        // Get page by ID
        $no_posts = $page_id ? get_post($page_id): null;

        if ($no_posts) {
            echo apply_filters('the_content', $no_posts->post_content);
        } 
        else {
            // Fallback
            echo '<p>Δεν βρέθηκε διαθέσιμο περιεχόμενο.</p>';
        }
    }

    $html = ob_get_clean();

    wp_reset_postdata();

    wp_send_json_success([
        'html'        => $html,
        'max_pages'   => $query->max_num_pages,
        'total_posts' => $query->found_posts,
    ]);
}

function theme_ajax_dispatcher() {

    // Security: Global Nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'theme_ajax_nonce')) {
        wp_send_json_error(['message' => 'Invalid nonce'], 403);
    }

    $action = $_POST['sub_action'] ?? '';

    switch ($action) {

        case 'load_posts':
            return theme_load_posts();
            break;

        case 'load_posts_html':
            theme_load_posts_html();
            break;

        case 'load_bikes':
            return theme_load_bikes();
            break;

        case 'load_bikes_html':
            theme_load_bikes_html();
            break;

        case 'load_parts':
            return theme_load_parts();
            break;

        case 'load_parts_html':
            theme_load_parts_html();
            break;

        case 'verify_recaptcha':
            return theme_verify_recaptcha_ajax();
            break;

        case 'submit_contact_form':
            // Site | Contact Form
            return theme_submit_contact_form();
            break;

        case 'update_yamaha_lineup':
            // Admin | Update Yamaha Lineup
            return yamaha_lineup_update_ajax();
            break;

        case 'delete_contact_message':
            // Admin | Delete Contact Form Message
            return theme_delete_contact_message();
            break;

        case 'clear_log':
            // Admin | Clear Log
            return theme_clear_log_ajax();
            break;


        default:
            wp_send_json_error(['message' => 'Unknown action'], 400);
    }

    wp_die();
}
add_action('wp_ajax_theme_ajax_dispatcher', 'theme_ajax_dispatcher');
add_action('wp_ajax_nopriv_theme_ajax_dispatcher', 'theme_ajax_dispatcher');
?>