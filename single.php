<?php
    log_info("Loading single.php", LOG_DOMAIN_CORE, "single.php");
    theme_render_header();
    theme_render_menu();
    theme_render_menu('mobile-s');
    theme_render_header_ex(); 

    $categories = get_the_category();

    // Category
    $category   = $categories[0] ?? null;
    // Slug
    $slug = ($category && !empty($category->slug)) ? $category->slug : null;
    // Template
    $template = $slug ? "{$slug}" : null;

    if ($template && file_exists(THEME_SINGLES_DIR . "/single-{$template}.php")) {
        theme_render_single($template);

        theme_render_footer();
        return;
    }
?>

<div class="site-section category-default">

    <div class="post-wrapper">
        <?php while (have_posts()) : the_post(); ?>
            <h1><?php the_title(); ?></h1>

            <div class="post-content">
                <?php the_content(); ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<?php theme_render_footer(); ?>
