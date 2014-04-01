<?php
get_header(); ?>

<div id="main-content" class="main-content">

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php
				$query = new WP_Query( array( 'meta_key' => 'featured', 'meta_value' => 'on', 'posts_per_page' => 10 ) );

				while ( $query->have_posts() ) : $query->the_post();
					$cats = get_the_category();
					$related_cats = false;
					foreach ( $cats as $cat ) {
						if ( 'Featured' == $cat->name ) {
							continue;
						}
						$related_cats[] = $cat->term_id;
					}

					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="margin-top: 20px; margin-left: 30px; padding-bottom: 20px; border-bottom: 1px dotted #555;">
						<header class="entry-header" style="margin: 0;">
							<div class="entry-meta">
								<span class="cat-links"><?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfourteen' ) ); ?></span>
							</div>
							<?php
							the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
						?>
						</header>
						<?php the_excerpt(); ?>

						<?php
						$related = get_posts( array( 'category__in' => array( $related_cats[0] ), 'posts_per_page' => 1, 'post__not_in' => array( get_the_ID() ), 'fields' => 'ids' ) );
						if ( ! empty( $related ) ) {
							$related_post = $related[0];
							echo '<div style="clear:both;">';
							echo '<h4>Related</h4>';
							echo '<a href="' . esc_url( get_permalink( $related_post ) ) . '">' . esc_html( get_the_title( $related_post ) ) . '</a>';
							echo '</div>';
						}
					?>
					</article>
					<?php
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
