<?php
    log_info("Loading page.php", LOG_DOMAIN_CORE, "page.php");

    theme_render_header();
    theme_render_menu();

    theme_render_template('pages/page');

    theme_render_footer(); 
?>