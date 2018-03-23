<?php 
/**
 * Template Name Posts: Portfolio Empty Page
 */

get_header(); ?>

	<div class="container">
		<div class="post_content section portfolio_page">
		<?php while(have_posts()): the_post(); ?>
		
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php the_content(); ?>
			</div>
		
		<?php endwhile; // END LOOP ?>
		</div>
	</div>
 
	<?php get_template_part('includes/related_portfolio_items_inc'); ?>

<?php get_footer(); ?>