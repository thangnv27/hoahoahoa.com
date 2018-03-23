<?php 
/**
 * The Default template file.
 * 
 * @package WordPress
 */


get_header(); ?>



<!-- Posts Page -->

	<div class="container">
		<div class="section">	
		
			<?php 
			// Check Sidebar Layout
			$sidebar_layout = boc_post_sidebar_layout();

			// IF Sidebar Left
			if($sidebar_layout == 'left-sidebar'){
				get_sidebar();
			}
	
			if($sidebar_layout != 'full-width'){
				echo "<div class='col span_3_of_4'>";
			}
			?>


			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
						
					<!-- Post Loop :: Start -->
					<div class="post_item section">
						

			<?php 	// Do we use shortened Featured imgs
					$img_height = (ot_get_option('blog_full_img_height','off') == 'off') ? "boc_thin" : "full";
			
					// IF Post type is Standard (false) 	
					if(function_exists( 'get_post_format' ) && get_post_format( $post->ID ) != 'gallery' && get_post_format( $post->ID ) != 'video' && has_post_thumbnail()) { 
						$attachment_image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $img_height);
						$att_img_alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true);
			?>			
						<div class="pic">
							<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr($post->post_title); ?>">
								<img src="<?php echo esc_url($attachment_image[0]); ?>" alt="<?php echo $att_img_alt;?>"/><div class="img_overlay"><span class="hover_icon icon_plus"></span></div>
							</a>
						</div>
			<?php 	} // IF Post type is Standard :: END ?>
			

			
			
			<?php // IF Post type is Gallery
			if (( function_exists( 'get_post_format' ) && get_post_format( $post->ID ) == 'gallery' )) {
				
				$images = get_post_meta($post->ID, "gallery", true);
										
				if($images){ 

					$images_arr = explode( ',', $images);
				
					$str = "<div id='img_slider_index_template_".$post->ID."' class='img_slider' style='opacity:0;'>";
					foreach ( $images_arr as $img_id ){
		
						// Attachment VARS
						
						$att_img_link = wp_get_attachment_image_src( $img_id, $img_height);
						$att_img_title = get_the_title($img_id);
						$att_img_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);

						$str .= '<div class="pic">
									<a href="'. get_the_permalink() .'" title="'. esc_attr($att_img_title) .'">
										<img src="'. esc_url($att_img_link[0]) .'" alt="'. esc_attr($att_img_alt) .'" class="boc_owl_lazy"/>
										<div class="img_overlay"><span class="hover_icon icon_plus"></span></div>
									</a>
								</div>';

					}
					$str .= '</div>';
				
					echo $str;
				?>
				
					<!-- Image Slider -->
					<script type="text/javascript">
						jQuery(document).ready(function($) {
							
							// Load carousel after its images are loaded
							preloadImages($("#img_slider_index_template_<?php echo $post->ID;?> img"), function () {							
								$("#img_slider_index_template_<?php echo $post->ID;?>").owlCarousel({
										items: 				1,
										nav: 				true,
										dots:				false,
										autoHeight: 		true,
										smartSpeed:			600,
										navText:			
										["<span class='icon icon-arrow-left8'></span>","<span class='icon icon-uniE708'></span>"],
										slideBy: 			1,
										rtl: 				<?php echo (is_rtl() ? 'true' : 'false'); ?>,
										navRewind: 			false,
										onInitialized: 		bocShowPostCarousel
								});
							});
							
							/* Show after initialized */
							function bocShowPostCarousel() {
								$("#img_slider_index_template_<?php echo $post->ID;?>").fadeTo(0,1);
								$("#img_slider_index_template_<?php echo $post->ID;?> .owl-item .boc_owl_lazy").css("opacity","1");
							}
							
						});
					</script>
					<!-- Image Slider :: END -->	

				<?php } // If Images :: END ?> 
								
			<?php } // IF Post type is Gallery :: END ?>
	
	
	
			<?php	// IF Post type is Video 
					if (( function_exists( 'get_post_format' ) && get_post_format( $post->ID ) == 'video')  ) {				

						if($video_embed_code = get_post_meta($post->ID, 'video_embed_code', true)) {
							echo "<div class='video_max_scale'>";
							echo wp_kses($video_embed_code, boc_expand_allowed_tags());
							echo "</div>";
						}										
					} // IF Post type is Video :: END 
			?>
			
			
							<div class="post_list_left">
								<div class="day"><?php echo get_the_date('j');?></div>
								<div class="month"><?php echo get_the_date('M');?></div>
							</div>
							
							<div class="post_list_right">
								
								<?php if(get_the_title()!=''){ ?>
								<h3 class="post_title"><a href="<?php esc_url(the_permalink()); ?>" title="<?php printf(esc_attr__('Permalink to %s', 'Fortuna'), the_title_attribute('echo=0')); ?>"><?php esc_html(the_title()); ?></a></h3>
								<?php } ?>
								
								<p class="post_meta">
									<span class="author"><a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID' ))); ?>"><?php echo __('By ','Fortuna');?> <?php esc_html(the_author_meta('display_name')); ?></a></span>
									<span class="comments <?php if(!get_the_tags()){ echo "no-border-comments";}?>"><?php  comments_popup_link( __('No comments yet','Fortuna'), __('1 comment','Fortuna'), __('% comments','Fortuna'), 'comments-link', __('Comments are Off','Fortuna'));?></span>
								<?php if(get_the_tags()) { ?>	
									<span class="tags"><?php the_tags('',', '); ?></span> 
								<?php } ?>
								</p>
								
								<div class="post_description clearfix">

								
						<?php	// Show Content/Excerpt
								if(boc_blog_full_post_content()){
									the_content();
								}else {
									the_excerpt();
								}
						?>
						
						
								</div>
							
							</div>
						</div>
						<!-- Post Loop End -->
						
					<?php endwhile; ?>
					
					<?php boc_pagination($pages = '', $range = 2); ?>
					
					<div style="display: none;">
					<?php
					echo get_the_posts_pagination();
					?>					
					</div>
					
					
				<?php else: ?>
				
					<p><?php _e('Sorry, no posts matched your criteria.','Fortuna'); ?></p>
				
				<?php endif; ?>
		
					
		
			<?php 
			if($sidebar_layout != 'full-width'){
				echo "</div>";
			}

			// IF Sidebar Right
			if($sidebar_layout == 'right-sidebar'){
				get_sidebar();
			}
			?>
	</div>
</div>
<!-- Post :: END -->

<?php get_footer(); ?>	