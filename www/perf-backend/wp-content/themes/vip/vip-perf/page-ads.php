<?php
get_header(); ?>

<div id="main-content" class="main-content">

<?php
	if ( is_front_page() && twentyfourteen_has_featured_posts() ) {
		// Include the featured content template.
		get_template_part( 'featured-content' );
	}
?>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php
				$query = new WP_Query( array( 'posts_per_page' => 10 ) );
				$index = 0;
				// Start the Loop.
				while ( $query->have_posts() ) : $query->the_post();
					$index++;

					if ( function_exists( 'pbe_show_ad' ) && 0 === $index % 3 ) {
						pbe_show_ad( 'id-' . $index, $index, '300x250' );
					}

					// Include the page content template.
					get_template_part( 'content', 'page' );

				endwhile;
				wp_reset_postdata();
			?>

		</div><!-- #content -->
	</div><!-- #primary -->
	<?php get_sidebar( 'content' ); ?>
</div><!-- #main-content -->

<?php
get_sidebar();
get_footer();
