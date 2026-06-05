<div class="site-section flex-1 p-6">

<?php
if (is_front_page()) {

    theme_render_template('pages/front-page');
} 
elseif (is_page()) {
   
    while (have_posts()) : the_post(); ?>
        <h1><?php the_title(); ?></h1>
        <div class="page-content prose max-w-none">
            <?php the_content(); ?>
        </div>
    <?php endwhile;
} 
else {
    // Fallback
    while (have_posts()) : the_post(); ?>
        <h2><?php the_title(); ?></h2>
        <div class="post-content prose max-w-none">
            <?php the_content(); ?>
        </div>
    <?php endwhile;
?>
</div>
