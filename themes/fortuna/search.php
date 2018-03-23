<?php 

get_header(); ?>



<div class="container">
	<div class="section">

			<?php 
			// Check Sidebar Layout
			$sidebar_layout = boc_page_sidebar_layout();

			// IF Sidebar Left
			if($sidebar_layout == 'left-sidebar'){
				get_sidebar();
			}
	
			if($sidebar_layout != 'full-width'){
				echo "<div class='col span_3_of_4'>";
			}
			?>
			
				<ol class="search_res">
				<?php $posts = query_posts($query_string . '&posts_per_page=-1'); ?>
				<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<li>
					<!-- Post Loop Begin -->
					<div class="post_item">
							<h3><a href="<?php the_permalink(); ?>" title="<?php printf(esc_attr__('Permalink to %s', 'Fortuna'), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a> <span class="post_type_in_search"> (<?php echo get_post_type( $post ) ?>) </span> </h3>
							
							<?php echo wp_trim_words( get_the_excerpt(), 20, $more = null ); ?>
							
							<p></p><p><a class="more-link1" href="<?php the_permalink(); ?>"><?php _e('Read more','Fortuna');?></a></p>
					</div>
					<!-- Post Loop End -->
					</li>
				<?php endwhile; ?>
				
				<?php boc_pagination($pages = '', $range = 2); ?>
				
				</ol>
				
				<?php else: ?>
				<p><?php _e('Sorry, no posts matched your criteria.','Fortuna'); ?></p>
				<?php endif; // Loop End  ?>
		
			<?php 
			if($sidebar_layout != 'full-width'){
				echo "</div>";
			}

			// IF Sidebar Right
			if($sidebar_layout == 'right-sidebar'){
				get_sidebar();
			}
			?>
		
			
		<div class="h40 clear"></div>
	</div>	
</div>

<?php get_footer(); ?>	