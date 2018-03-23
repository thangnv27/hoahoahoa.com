<?php

/**
	Portfolio Carousel
**/

if ( ! function_exists('is_plugin_active')){ 
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // load is_plugin_active() function if no available
}

if(is_plugin_active('fortuna_portfolio_cpt/fortuna_portfolio_cpt.php')){ 

	// Register Shortcode
	if( ! function_exists( 'shortcode_boc_portfolio_carousel' ) ) {
		function shortcode_boc_portfolio_carousel($atts, $content = null) {
			
			$atts = vc_map_get_attributes('boc_portfolio_carousel', $atts );
			extract( $atts );
		
			// Arrows
			$nav = (($navigation == "Arrows") || ($navigation == "Both"));
			if ($nav && ($arrows_position!='top')){
				$css_classes .= ' owl_side_arrows';
			}
			
			// Dots
			$dots = (($navigation == "Dots") || ($navigation == "Both"));
			if($dots){
				$css_classes .= ' has_dots'; 
			}	

			// Img Hover Effect
			if ($img_hover_effect){
				$img_hover_effect = ' img_hover_effect'.$img_hover_effect;
			}

			// CSS Animation
			$css_animation_classes = "";
			if ( $css_items_animation != '' ) {
				$css_animation_classes = 'boc_animate_when_almost_visible boc_'. $css_items_animation .'';
			}	
			
			// Display Type
			$portfolio_style = 'type'.$display_style;

			$projects = boc_get_portfolio_items($limit, $order_by, $order, $category_name);

			$str = '';

			if($projects->have_posts()){

				$carousel_id = rand(0,10000);

				$str.='
				<div class="info_block">
					<div class="portfolio_carousel_holder '.esc_attr($css_classes).' '.($display_style==6 ? "padded_carousel" : "").'">
						<div id="portfolio_carousel_'.$carousel_id.'" style="opacity:0;">';
							while($projects->have_posts()): $projects->the_post(); 
							if(has_post_thumbnail()): 
							
								$taxonomy = 'portfolio_category';
								$terms = get_the_terms( $projects->post->ID , $taxonomy );
								$cats = array();
								
								if (! empty( $terms ) ) :
									foreach ( $terms as $term ) {
										
										$link = get_term_link( $term, $taxonomy );
										if ( !is_wp_error( $link ) )
											$cats[] = $term->name;
									}
								endif;
								
								// Feat. Image								
								if($img_width && $img_height){
									$featured_img_url	= wp_get_attachment_url( get_post_thumbnail_id($projects->post->ID) );
									$thumbnail_hard_crop = true;
									$new_feat_img_url = aq_resize( $featured_img_url, intval($img_width), intval($img_height), $thumbnail_hard_crop );
									$feat_img = '<img src="'.esc_url($new_feat_img_url).'" alt="'.esc_attr(get_post_meta(get_post_thumbnail_id(), "_wp_attachment_image_alt", true)).'">';
								}else{
									//$feat_img_arr = wp_get_attachment_image_src( get_post_thumbnail_id(), 'boc_medium');
									$feat_img = get_the_post_thumbnail($projects->post->ID, 'boc_medium');
								}

								$str.=
								'	<div class="info_item '.esc_attr($css_animation_classes).' '.(!$css_animation_classes ? "boc_owl_lazy" : "").'">
										<a href="'.get_permalink().'" title="" class="pic_info_link_'.esc_attr($portfolio_style).'">
											<div class="pic_info '.esc_attr($portfolio_style).'">
												<div class="pic '.esc_attr($img_hover_effect).'"><div class="plus_overlay"></div><div class="plus_overlay_icon"></div>'.$feat_img.'<div class="img_overlay_icon"><span class="portfolio_icon icon_'.esc_attr(getPortfolioItemIcon($projects->post->ID)).'"></span></div></div>
												<div class="info_overlay">
													<div class="info_overlay_padding">
														<div class="info_desc">
															<span class="portfolio_icon icon_'.esc_attr(getPortfolioItemIcon($projects->post->ID)).'"></span>
															<h3>'.get_the_title().'</h3>
															<p>'.implode(' / ', $cats).'</p>
														</div>
													</div>
												</div>
											</div>
										</a>
									</div>
								';

							endif; endwhile;
							
							wp_reset_postdata();
							
							$str.='
						</div>
					</div>
				</div>

				<div class="h10 clear"></div>
							
							<!-- Portfolio Carousel -->
							<script type="text/javascript">
										
								jQuery(document).ready(function($) {			

									// Load carousel after its images are loaded
									preloadImages($("#portfolio_carousel_'.$carousel_id.' img"), function () {
									
										var carousel = $("#portfolio_carousel_'.$carousel_id.'");
										
										var args = {
												items: 				'.(int)$items_visible.',
												autoplay:			'.(esc_js($autoplay) ? "true" : "false").',
												autoplayTimeout:	'.(int)$autoplay_interval.',
												loop: 				'.($loop	? "true" : "false").',
												nav: 				'.(esc_js($nav) 	? "true" : "false").',
												dots: 				'.(esc_js($dots) 	? "true" : "false").',
												autoHeight: 		false,
												smartSpeed:			'.(int)$speed.',
												navText:			'.((esc_js($nav) && ($arrows_position!='top')) ? 
												"[\"<span class='icon icon-angle-left-circle'></span>\",\"<span class='icon icon-angle-right-circle'></span>\"]" : 
												"[\"<span class='icon icon-arrow-left7'></span>\",\"<span class='icon icon-arrow-right7'></span>\"]").',
												slideBy: 			'.(int)$items_slided.',

												navRewind: false,
												rtl: 				' . (is_rtl() ? 'true' : 'false') . ',
												margin:30,
												responsive:{
													0:{
													  items:1,
													},
													480:{
													  items:1,
													  margin:20,
													},
													769:{
													  items:'.(int)$items_visible.'
													}
												},
												onInitialized:  showCarousel_'.$carousel_id.'
										}
										
										carousel.owlCarousel(args);

										var initital_width = carousel.css("width");		
										
										/* Refresh it for full width rows */
										$(window).load(function(){
											if(carousel.css("width") != initital_width) {
												carousel.trigger("destroy.owl.carousel").removeClass("owl-carousel owl-loaded");
												carousel.find(".owl-stage-outer").children().unwrap();
												carousel.owlCarousel(args);
											}
										});
									});
									
									
									/* Show after initialized */
									function showCarousel_'.$carousel_id.'() {
										$("#portfolio_carousel_'.$carousel_id.'").fadeTo(0,1);
										$("#portfolio_carousel_'.$carousel_id.' .owl-item .boc_owl_lazy").css("opacity","1");
									}

								});
								
							</script>
							<!-- Portfolio Carousel :: END -->';
			}
			
			return $str;

		}
		
		add_shortcode('boc_portfolio_carousel', 'shortcode_boc_portfolio_carousel');
	}


	// Map Shortcode in Visual Composer
	vc_map( array(
		"name" 		=> __("Portfolio Carousel", 'Fortuna'),
		"base" 		=> "boc_portfolio_carousel",
		"category" 	=> "Fortuna Shortcodes",
		"icon" 		=> "boc_portfolio_carousel",
		"weight"	=> 58,
		"params" => array(
			array(
				"type" 			=> "dropdown",
				"heading" 		=> __("Total Visible Items", 'Fortuna'),
				"param_name" 	=> "items_visible",
				"value" 		=> Array(1,2,3,4,5,6),
				"std"			=> "3",
				"description" 	=> __("How many items you want the viewport to be consisted of.", 'Fortuna'),
				"group"			=> "Slider Settings",
			),
			array(
				"type" 			=> "dropdown",
				"heading" 		=> __("Slide Items at once", 'Fortuna'),
				"param_name" 	=> "items_slided",
				"value" 		=> Array(1,2,3,4,5,6),
				"std"			=> "1",
				"description" 	=> __("How many items will slide per click.", 'Fortuna'),
				"group"			=> "Slider Settings",
			),			
			array(
				"type"			=> 'dropdown',
				"heading" 		=> __("Slider Navigation", 'Fortuna'),
				"param_name" 	=> "navigation",
				"value"			=> Array("Arrows", "Dots", "Both"),
				"description" 	=> __("Select a Navigation Type", 'Fortuna'),
				"group"			=> "Slider Settings",
			),
			array(
				"type"			=> 'dropdown',
				"heading" 		=> __("Arrows Position", 'Fortuna'),
				"param_name" 	=> "arrows_position",
				"value"			=> Array("Top"=>"top", "Side"=>"side"),
				"description" 	=> __("Select Position of Carousel Arrows. Top arrows are absolutely positioned above the carousel!", 'Fortuna'),
				"dependency"	=> Array(
						'element'	=> "navigation",
						'value'		=> Array("Arrows", "Both"),
				),
				"group"			=> "Slider Settings",
			),			
			array(
				"type"			=> 'checkbox',
				"heading" 		=> __("AutoPlay the slider?", 'Fortuna'),
				"param_name" 	=> "autoplay",
				"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
				"description" 	=> __("Set to Yes if you want your Testimonial Slider to autoplay", 'Fortuna'),
				"group"			=> "Slider Settings",						
			),
			array(
				"type" 			=> "dropdown",
				"heading" 		=> __("AutoPlay Interval", 'Fortuna'),
				"param_name" 	=> "autoplay_interval",
				"value" 		=> array('4000','6000','8000','10000','12000','14000'),
				"std"			=> "6000",
				"description" 	=> __("Set the Autoplay Interval (miliseconds).", 'Fortuna'),
				"dependency"	=> Array(
						'element'	=> "autoplay",
						'not_empty'	=> true,
				),
				"group"			=> "Slider Settings",
			),
			array(
				"type" 			=> "dropdown",
				"heading" 		=> __("Animation Speed", 'Fortuna'),
				"param_name" 	=> "speed",
				"value" 		=> array('250','500','750','1000'),
				"std"			=> "250",
				"description" 	=> __("Set the length of the Slider Animation (miliseconds)", 'Fortuna'),
				"group"			=> "Slider Settings",
			),
			array(
				"type"			=> 'checkbox',
				"heading" 		=> __("Loop the slider", 'Fortuna'),
				"param_name" 	=> "loop",
				"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
				"description" 	=> __("Set to Yes if you want your Slider to be Infinite", 'Fortuna'),
				"group"			=> "Slider Settings",
			),
			array(
				"type"		=> "dropdown",
				"heading"	=> __("CSS Animation", "Fortuna"),
				"param_name"	=> "css_items_animation",
				"admin_label"	=> true,				
				"value"			=> array(
					__("None", "Fortuna")					=> '',
					__("Top to bottom", "Fortuna")			=> "top-to-bottom",
					__("Bottom to top", "Fortuna")			=> "bottom-to-top",
					__("Left to right", "Fortuna")			=> "left-to-right",
					__("Right to left", "Fortuna")			=> "right-to-left",
					__("Fade In", "Fortuna")				=> "fade-in"),
				"description"	=> __("Animation will be applied to each single Item in your grid for a better effect", "Fortuna"),
				"group"			=> "Slider Settings",
			),
			array(
				"type" 			=> "textfield",
				"heading" 		=> __("Extra class name", 'Fortuna'),
				"param_name" 	=> "css_classes",
				"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
				"group"			=> "Slider Settings",
			),
			array(
				"type" 			=> "textfield",
				"heading" 		=> __("Category Name Filter (slug)", 'Fortuna'),
				"param_name" 	=> "category_name",
				"value" 		=> "",
				"description" 	=> __("Filter only a certain Category from your portfolio items (comma-separated)", 'Fortuna'),
				"group"			=> "Query",
			),				
			array(
				"type" 			=> "dropdown",
				"heading" 		=> __("'Order By' Clause", 'Fortuna'),
				"param_name" 	=> "order_by",
				"value" 		=> array('none','ID','title','name','date','rand'),
				"std"			=> 'date',
				"description" 	=> __("Order results by a certain field. <a href='http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters' target='_blank'>More on the available WP options here</a>.", 'Fortuna'),
				"group"			=> "Query",
			),
			array(
				"type" 			=> "dropdown",
				"heading" 		=> __("'Order' Clause", 'Fortuna'),
				"param_name" 	=> "order",
				"value" 		=> array('DESC','ASC'),
				"description" 	=> __("Order results in a Descending or Ascending order. <a href='http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters' target='_blank'>More on the order here</a>.", 'Fortuna'),
				"group"			=> "Query",
			),
			array(
				"type" 			=> "dropdown",
				"heading" 		=> __("Display Style", 'Fortuna'),
				"param_name" 	=> "display_style",
				"value" 		=> array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14'),
				"description" 	=> __("Pick a Display Style. Explore them all to see what best fits your needs.", 'Fortuna'),
				"group"			=> "Design",
			),
			array(
				"type" 			=> "dropdown",
				"heading" 		=> __("Max Number of Items in Carousel", 'Fortuna'),
				"param_name" 	=> "limit",
				"value" 		=> Array(1,2,3,4,5,6,7,8,9,10,12,16,20,40),
				"std"			=> "10",
				"description" 	=> __("How many items you want to limit your carousel to. Default is  10.", 'Fortuna'),
				"group"			=> "Design",
			),
			array(
				"type"			=> 'dropdown',
				"heading" 		=> __("Hover Image Effect", 'Fortuna'),
				"param_name" 	=> "img_hover_effect",
				"value"			=> Array("None" => '', "Zoom Out" => 1, "Zoom In" => 2, "Side" => 3, "Spin" => 4),
				"description" 	=> __("Pick a hover Image Effect", 'Fortuna'),
				"group"			=> "Images",
			),
			array(
				'type'			=> "textfield",
				"heading"		=> __( "Overwrite Image Width", 'Fortuna'),
				'param_name'	=> "img_width",
				'std'			=> "",
				'description'	=> __( "Enter a width in pixels if you want to overwrite the default (600x380). Both W and H should be changed to define a new dimension. Leave empty for default.", 'Fortuna'),
				'group'			=> "Images",
			),
			array(
				'type'			=> "textfield",
				"heading"		=> __( "Overwrite Image Height", 'Fortuna'),
				'param_name'	=> "img_height",
				'std'			=> "",
				'description'	=> __( 'Enter a height in pixels if you want to overwrite the default (380). Both W and H should be changed to define a new dimension. Leave empty for default.', 'Fortuna'),
				'group'			=> "Images",
			),
	   )
	));
}