<?php 
/**
 * Template Name Posts: Portfolio Full Width
 */

get_header(); ?>


	<div class="container">	
		
		<div class="post_content section portfolio_page">

				
	
	<?php while(have_posts()): the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			

			<?php // IF Post type is Standard (false) 	
			if(function_exists( 'get_post_format' ) && get_post_format( $post->ID ) != 'gallery' && get_post_format( $post->ID ) != 'video' && has_post_thumbnail()) { ?> 

				<?php if(has_post_thumbnail()): ?>

					<?php $attachment_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'boc_thin'); ?>
					<?php $attachment_image_full = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); ?>
					<div class="pic">
						<a href="<?php echo esc_url($attachment_image_full[0]); ?>" class="mfp_popup" title="">
							<img src="<?php echo esc_url($attachment_image[0]); ?>" alt=" "/><div class="img_overlay"><span class="icon_zoom"></span></div>	  		
						</a>
					</div>

				<?php endif; ?>
			
			<?php } // IF Post type is Standard :: END ?>

			
			
			<?php // IF Post type is Gallery
			if (( function_exists( 'get_post_format' ) && get_post_format( $post->ID ) == 'gallery' )) {
				
				$images = get_post_meta($post->ID, "gallery", true);

				$i = 0;										
				if($images){ 

					$images_arr = explode( ',', $images);
				
					$str = "<div id='img_slider_single_portfolio_template' class='img_slider mfp_gallery big_arrows' style='opacity:0;'>";
					foreach ( $images_arr as $img_id ){
		
						// Attachment VARS
						$att_img_link 		= wp_get_attachment_image_src( $img_id, 'boc_thin');
						$att_img_link_full 	= wp_get_attachment_image_src( $img_id, 'full');
						$att_img_title 		= get_the_title($img_id);
						$att_img_alt 			= get_post_meta($img_id, '_wp_attachment_image_alt', true);

						$str .= '<div class="pic">
									<a href="'. esc_url($att_img_link_full[0]) .'" class="mfp_popup_gal" title="'. esc_attr($att_img_title) .'">
										<img src="'. esc_url($att_img_link[0]) .'" alt="'. esc_attr($att_img_alt) .'" class="boc_owl_lazy"/>
										<div class="img_overlay"><span class="hover_icon icon_zoom"></span></div>
									</a>
								</div>';
					}
					$str .= '</div>';
				
					echo $str;
				?>
				
					<!-- Image Slider -->
					<script type="text/javascript">

						jQuery(document).ready(function($) {
							
							preloadImages($("#img_slider_single_portfolio_template img"), function () {
								$("#img_slider_single_portfolio_template").owlCarousel({
									items: 				1,
									lazyLoad:			true,
									nav: 				true,
									dots:				false,
									autoHeight: 			true,
									smartSpeed:			600,
									navText:			
									["<span class='icon icon-arrow-left8'></span>","<span class='icon icon-uniE708'></span>"],
									slideBy: 			1,
									navRewind: 			false,
									onInitialized: 		bocShowWidePortfolioCarousel						
								});
							});
						});
						
						function bocShowWidePortfolioCarousel() {
							jQuery("#img_slider_single_portfolio_template").fadeTo(0,1);
							jQuery("#img_slider_single_portfolio_template .owl-item .boc_owl_lazy").css("opacity","1");
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
							echo "</div><div class='h20'></div>";
						}										
					} // IF Post type is Video :: END 
			?>
			
			<div class="h30 clear"></div>

			<?php the_content(); ?>
			
			<?php endwhile; // END LOOP ?>
			
		</div>
	  </div>
	</div>
 
	



	<?php get_template_part('includes/related_portfolio_items_inc'); ?>

<?php get_footer(); ?>