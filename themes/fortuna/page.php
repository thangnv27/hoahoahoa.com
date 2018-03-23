<?php 
/**
 *
 * BOC main page template
 * @package WordPress
 */


get_header(); ?>


	<!-- Page template :: START -->
	<div <?php post_class(''); ?> id="post-<?php the_ID(); ?>" >
		<div class="container">
			<div class="section">
				<?php
				// Check Sidebar Layout
				$sidebar_layout = esc_html(boc_page_sidebar_layout());

				// IF Sidebar Left
				if($sidebar_layout == 'left-sidebar'){
					get_sidebar();
				}
		
				if($sidebar_layout != 'full-width'){
					echo "<div class='post_content col span_3_of_4'>";
				}else {
					echo "<div class='post_content'>";
				}
				?>

				<?php while (have_posts()) : the_post(); ?>
				<?php the_content() ?>
				<?php wp_link_pages(array(
					'before' => "<div class='pagination'><div class='links'>",
					'after' => "</div></div>",
				   )); ?>
				<?php endwhile; ?>
				
				<?php 
				$show_page_comments	= ot_get_option('show_page_comments','off') == 'on';
				if($show_page_comments) { 
					comments_template('', true);
				} 
				?>
				
				<?php 
				// Close "post_content"
				echo "</div>";

				// IF Sidebar Right
				if($sidebar_layout == 'right-sidebar'){
					get_sidebar();
				}
				?>
			</div>
		</div>
	</div>
	<!-- Page template :: END -->	


<?php get_footer(); ?>	