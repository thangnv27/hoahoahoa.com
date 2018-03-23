<?php get_header(); ?>
		
<!-- Post -->
<div <?php post_class(''); ?> id="post-<?php the_ID(); ?>" >
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
				echo "<div class='post_content col span_3_of_4'>";
			}else {
				echo "<div class='post_content'>";
			}
			?>

					
					
			<?php while (have_posts()) : the_post(); ?>
				
					<div class="section">
								
						
					<?php // IF Post type is Standard (false) 	
						if(function_exists( 'get_post_format' ) && get_post_format( $post->ID ) != 'gallery' && get_post_format( $post->ID ) != 'video' && has_post_thumbnail()) { 
								$attachment_image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
					?>
							<div class="pic">
								<a href="<?php echo esc_url($attachment_image[0]);?>" class="mfp_popup" title="<?php echo esc_attr($post->post_title); ?>">
									<img src="<?php echo esc_url($attachment_image[0]); ?>" alt=" "/><div class="img_overlay"><span class="icon_zoom"></span></div>
								</a>
							</div>

							<div class="h20"></div>	
			
					<?php } // IF Post type is Standard :: END ?>
			
		
					<?php // IF Post type is Gallery
					if (( function_exists( 'get_post_format' ) && get_post_format( $post->ID ) == 'gallery' )) {
						
						$images = get_post_meta($post->ID, "gallery", true);
												
						if($images){ 

							$images_arr = explode( ',', $images);
						
							$str = "<div id='img_slider_single_template' class='img_slider mfp_gallery' style='opacity:0;'>";
							foreach ( $images_arr as $img_id ){
				
								// Attachment VARS
								$att_img_link = wp_get_attachment_image_src( $img_id, 'full');
								$att_img_title = get_the_title($img_id);
								$att_img_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);

								$str .= '<div class="pic">
											<a href="'. esc_url($att_img_link[0]) .'" class="mfp_popup_gal" title="'. esc_attr($att_img_title) .'">
												<img src="'. esc_url($att_img_link[0]) .'" alt="'. esc_attr($att_img_alt) .'" class="boc_owl_lazy"/>
												<div class="img_overlay"><span class="hover_icon icon_zoom"></span></div>
											</a>
										</div>';

							}
							$str .= '</div>';
						
							echo $str;
						?>
						
							<div class="h20"></div>			
							
							<!-- Image Slider -->
							<script type="text/javascript">

								jQuery(document).ready(function($) {
									
														
									preloadImages($("#img_slider_single_template img"), function () {
										$("#img_slider_single_template").owlCarousel({
											items: 				1,
											lazyLoad:			true,
											nav: 				true,
											dots:				false,
											autoHeight: 			true,
											smartSpeed:			600,
											navText:			
											["<span class='icon icon-arrow-left8'></span>","<span class='icon icon-uniE708'></span>"],
											slideBy: 			1,
											rtl: 				<?php echo (is_rtl() ? 'true' : 'false'); ?>,
											navRewind: 			false,
											onInitialized: 		bocShowPostCarousel						
										});
									});
									
								});
								
								function bocShowPostCarousel() {
									jQuery("#img_slider_single_template").fadeTo(0,1);
									jQuery("#img_slider_single_template .owl-item .boc_owl_lazy").css("opacity","1");
								}
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
									echo '<div class="h20"></div>';
								}										
							} // IF Post type is Video :: END 
					?>
		
			
						<p class="post_meta">
							<span class="calendar_date"><?php printf('%1$s', get_the_date()); ?></span>
							<span class="author"><a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID' ))); ?>"><?php echo __('By ','Fortuna');?> <?php the_author_meta('display_name'); ?></a></span>
							<span class="comments <?php if(!get_the_tags()){ echo "no-border-comments";}?>"><?php  comments_popup_link( __('No comments yet','Fortuna'), __('1 comment','Fortuna'), __('% comments','Fortuna'), 'comments-link', __('Comments are Off','Fortuna'));?></span>
					<?php if(get_the_tags()) { ?>								
							<span class="tags"><?php the_tags('',', '); ?></span>
					<?php } ?>
						</p>
					
						<div class="post_description">
						<?php the_content(); ?>
						</div>

						<!-- Post End -->

			
			</div>
								
			<?php wp_link_pages(); ?>

			<?php endwhile; // Loop End  ?>

			
			<?php comments_template('', true); ?>

			
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
<!-- Post :: END -->	  

	
<?php get_footer(); ?>	