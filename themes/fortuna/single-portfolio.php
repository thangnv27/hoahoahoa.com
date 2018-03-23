<?php
/**
 * Template Name Posts: Portfolio List
 */
 
get_header(); ?>


<?php // IF Post type is Gallery - Only then do the SCROLLING Sidebar
	if (( function_exists( 'get_post_format' ) && get_post_format( $post->ID ) == 'gallery' )) { ?>

	<!-- SCROLL -->
	<script type="text/javascript">
			jQuery(window).load(function(){

				var $float_box = jQuery('.portfolio_description_scrolling');

				if(jQuery('.portfolio_page').length > 0){
					var header_h = jQuery('header').height();
					var bodyY = parseInt(jQuery('.portfolio_page').offset().top)  - header_h;
					var float_right_h = jQuery('.portfolio_description').height();

					var all =    jQuery('.portfolio_media').height() + bodyY - float_right_h;

					jQuery(window).scroll(function () { 
					
						var win_width = jQuery(window).width();
						if(win_width>1050){
							var scrollY = jQuery(window).scrollTop();
							var isfixed = $float_box.css('position') == 'fixed';
							var end = jQuery('.portfolio_page').height() - float_right_h - 62 ;
							//var end2 = jQuery('.portfolio_page').height() + (jQuery('.portfolio_description').height()/2);
							
							if($float_box.length > 0){

								if ( scrollY > bodyY && scrollY < all && !isfixed ) {
									$float_box.css({width: $float_box.width()});
									$float_box.stop().css({position: 'fixed', top: header_h});
								} 
								else if ( scrollY > all) {
									$float_box.stop().css({position: 'relative', top:end });
									$float_box.css({width: 'auto'});
					
								} else if ( scrollY < bodyY && scrollY < all && isfixed ) {
									$float_box.css({position: 'relative', top:0 });
									$float_box.css({width: 'auto'});
								}		
							}
						}
					});
				}
             });
	</script>
	<!-- SCROLL :: END -->

<?php } // IF Post type is Gallery :: END ?>

		
	<div class="container">	
		
		<div class="section portfolio_page">

			<div class="portfolio_media col span_2_of_3">
				
	
	<?php 
		while(have_posts()): the_post(); 
			
			
				$args = array(
					'post_type' => 'attachment',
					'numberposts' => '20',
					'post_status' => null,
					'post_parent' => $post->ID,
					'orderby' => 'menu_order',
					'order' => 'ASC',
					'exclude' => get_post_thumbnail_id()
				);
				$attachments = get_posts($args);
				
				
				if($attachments || has_post_thumbnail()){
				
					// IF Post type is Standard (false) 	
					if(function_exists( 'get_post_format' ) && get_post_format( $post->ID ) != 'gallery' && get_post_format( $post->ID ) != 'video' && has_post_thumbnail()) {

						if(has_post_thumbnail()){

							$attachment_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); ?>
							
							<div class="pic">
								<a href="<?php echo esc_url($attachment_image[0]); ?>" class="mfp_popup" title="">
									<img src="<?php echo esc_url($attachment_image[0]); ?>" alt=" "/><div class="img_overlay"><span class="icon_zoom"></span></div>	  		
								</a>
							</div>
							<div class="h40"></div>	
					<?php 	
						}
					} // IF Post type is Standard :: END 


					
					// IF Post type is Gallery
					if (( function_exists( 'get_post_format' ) && get_post_format( $post->ID ) == 'gallery' )) {
					
						$images = get_post_meta($post->ID, "gallery", true);
											
						if($images){ 

							$images_arr = explode( ',', $images);
						
							$str = "<div id='img_slider_single_portfolio_template' class='img_slider mfp_gallery'>";
							foreach ( $images_arr as $img_id ){
				
								// Attachment VARS
								$att_img_link = wp_get_attachment_image_src( $img_id, 'full');
								$att_img_title = get_the_title($img_id);
								$att_img_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);

								$str .= '<div class="pic">
											<a href="'. esc_url($att_img_link[0]) .'" class="mfp_popup_gal" title="'. esc_attr($att_img_title) .'">
												<img src="'. esc_url($att_img_link[0]) .'" alt="'. esc_attr($att_img_alt) .'" />
												<div class="img_overlay"><span class="hover_icon icon_zoom"></span></div>
											</a>
										</div>
										<div class="h30"></div>
										';

							}
							$str .= '</div>';
						
							echo $str;
							echo '<div class="h40"></div>';

				
						} // If Images :: END  
									
					} // IF Post type is Gallery :: END 
					
					
					// IF Post type is Video 
					if (( function_exists( 'get_post_format' ) && get_post_format( $post->ID ) == 'video')  ) {				

						if($video_embed_code = get_post_meta($post->ID, 'video_embed_code', true)) {
							echo "<div class='video_max_scale'>";
							echo wp_kses($video_embed_code, boc_expand_allowed_tags());
							echo "</div>";
							echo '<div class="h40"></div>';
						}										
					} // IF Post type is Video :: END 
					
				}

			endwhile; // END LOOP ?>
			</div>
			
			<div class="post_content col span_1_of_3 portfolio_description">
				<div class="portfolio_description_scrolling" style="width: auto;">			
					<?php the_content(); ?>
				</div>
			</div>

		</div>
	</div>


 
	<?php get_template_part('includes/related_portfolio_items_inc'); ?>

<?php get_footer(); ?>