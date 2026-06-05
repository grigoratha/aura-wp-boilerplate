<div class="site-section flex-1 p-6">
<?php while (have_posts()) : the_post(); ?>
    <h1><?php the_title(); ?></h1>
    <div class="page-content prose max-w-none">
        <?php the_content(); ?>
    </div>
    <?php endwhile; ?>
</div>
