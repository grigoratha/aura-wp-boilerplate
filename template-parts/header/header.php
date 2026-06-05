<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- DaisyUI CDN -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />

    <?php 
        wp_head(); 
        $settings = get_theme_settings();
    ?>
    </head>
    <body id="<?php echo THEME_TEXTDOMAIN ?>-site" class="min-h-screen flex flex-col">
            <div id="header" class="header-container">
                <div class="hero bg-base-200">
                    <div class="hero-content text-center">
                        <div>
                        <h1 class="text-5xl font-bold"><?= $settings['site_name']; ?></h1>
                        <p class="py-2">
                            <?= $settings['site_description']; ?>
                        </p>
                        <p class="py-2">
                            Aura WP is a lightweight modular theme system for building custom WordPress themes with a consistent structure and fast development workflow.
                        </p>
                    </div>
                </div>
            </div>
           