<?php

/**
	Posts Carousel
**/

// Register Shortcode
if( ! function_exists( 'shortcode_boc_posts_carousel' ) ) {
	
	function shortcode_boc_posts_carousel($atts, $content = null) {
		
		$atts = vc_map_get_attributes('boc_posts_carousel', $atts );
		extract( $atts );	

		// Arrows
		$nav = (($navigation == "Arrows") || ($navigation == "Both"));
		if ($nav && ($arrows_position!='top')) {
			$css_classes .= ' owl_side_arrows';
		}
		
		// Img Hover Effect
		if ($img_hover_effect) {
			$img_hover_effect = ' img_hover_effect'.$img_hover_effect;
		}
		
		// CSS Animation
		$css_animation_classes = "";
		if ( $css_items_animation != '' ) {
			$css_animation_classes = 'boc_animate_when_almost_visible boc_'. $css_items_animation .'';
		}		
		
		// Dots
		$dots = (($navigation == "Dots") || ($navigation == "Both"));
		if($dots){
			$css_classes .= ' has_dots'; 
		}	

				
		$exclude_post = 0;
		if($exclude_current){
			$exclude_post = get_the_ID();
		}	
		
		
		$args = array(
				'post_type' 	=> array($post_type),
			//	'category_name' => ($category_slug ? $category_slug : null),  # MOVED TO BOTTOM IF Statement
				'orderby'		=> $order_by,
				'order'			=> $order,
				'showposts' 	=> $limit,
				'post__not_in' 	=> array($exclude_post),
		);
		
		//  WPML compatibility
		if($category_slug) {
			$args['tax_query'] = array(
			  array(
				'taxonomy' => 'category',
				'field' => 'slug',
				'terms' => (explode( "," , str_replace(' ', '', $category_slug))),
			  )
			);
		}
		
		
		$wp_query = new WP_Query($args);
		
		$str = '';

		if ( $wp_query->have_posts() ):
			
			// generate checksum of $atts array
			$carousel_id = rand(0,10000);
			
			$str = '<div class="posts_carousel_holder '.esc_attr($css_classes).'">
						<div id="posts_carousel_'.$carousel_id.'" style="opacity:0;">';
			
			while( $wp_query->have_posts() ) : $wp_query->the_post();
				//$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($wp_query->post->ID), 'boc_medium'); 
				$excerpt = get_the_excerpt();
				$short_excerpt = boc_limitString($excerpt,$excerpt_char_limit);
				$str .='<div class="post_item_block '.esc_attr($css_animation_classes).' '.(!$css_animation_classes ? "boc_owl_lazy" : "").' '.($is_boxed ? "boxed" : "").'">';
				$id = $wp_query->post->ID;

				// Show Featured Image
				if( function_exists( 'get_post_format' ) && $show_pic){
					if($img_width && $img_height){

						$featured_img_url	= wp_get_attachment_url( get_post_thumbnail_id() );
						$thumbnail_hard_crop = true;
						$feat_img = aq_resize( $featured_img_url, intval($img_width), intval($img_height), $thumbnail_hard_crop );
						
					}else{

						$feat_img_arr = wp_get_attachment_image_src( get_post_thumbnail_id(), 'boc_medium');
						$feat_img = $feat_img_arr[0];
					}
					
					// If image isset
					if($feat_img){					
						$str .='<div class="pic '.esc_attr($img_hover_effect).'"><a href="'. get_permalink().'"><img src="'.esc_url($feat_img).'"/><div class="img_overlay"><span class="hover_icon icon_plus"></span></div></a></div>'; 
					}
				}

				$str .= '<div class="post_item_desc dark_links">';

				// Show date according to style set (1 = side, 2 = below title)
				$str .= (($show_date == 1) ? 	'<div class="small_post_date_left">
												<span class="small_day">'.get_the_date('j').'</span>
												<span class="small_month">'.get_the_date('M').'</span>
											</div>' : '');
									
				$str .= (($show_date == 1) ? '<div class="small_post_desc_right">' : '');			
				
				$str .='<h4><a href="'. get_permalink().'">'.esc_html(get_the_title()).'</a></h4>';
				$str .= (($show_date == 2) ? '<div class="small_post_date"><span class="icon icon-calendar2"></span> &nbsp; '.get_the_date().'</div>' : "");
				$str .= ($show_excerpt ? '<p>'.esc_html($short_excerpt).' '.($add_dots ? "..." : "").'</p>': '');
				$str .= ($show_read_more ? '<a href="'. get_permalink().'" class=\''.boc_more_link_classes_sh($read_more_style).'\'>'.__('Read more','Fortuna').'</a>' : '');			
				$str .= (($show_date == 1) ? '</div>' : '');

				$str .='		</div>
				</div>';
			endwhile;  // close the Loop
			
			wp_reset_postdata();
					
			$str .='</div></div>
							<!-- Posts Carousel -->
							<script type="text/javascript">
										
								jQuery(document).ready(function($) {			
									
									// Load carousel after its images are loaded
									preloadImages($("#posts_carousel_'.$carousel_id.' img"), function () {
										
										var carousel = $("#posts_carousel_'.$carousel_id.'");
										
										var args = {
											items: 				'.(int)$items_visible.',
											autoplay:			'.($autoplay ? "true" : "false").',
											autoplayTimeout:	'.(int)$autoplay_interval.',
											loop: 				'.($loop	? "true" : "false").',
											nav: 				'.($nav		? "true" : "false").',
											dots: 				'.($dots	? "true" : "false").',
											autoHeight: 		'.(($auto_height && ($items_visible==1)) ? "true" : "false").',
											navText:			'.(($nav && ($arrows_position!='top')) ? 
											"[\"<span class='icon icon-angle-left-circle'></span>\",\"<span class='icon icon-angle-right-circle'></span>\"]" : 
											"[\"<span class='icon icon-arrow-left7'></span>\",\"<span class='icon icon-arrow-right7'></span>\"]").',
											slideBy: 			'.(int)$items_slided.',
											
											navRewind: false,
											rtl : 				' . (is_rtl() ? 'true' : 'false') . ',
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
										};

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
									
									
									/* Show once loaded */
									function showCarousel_'.$carousel_id.'() {
										$("#posts_carousel_'.$carousel_id.'").fadeTo(0,1);
										$("#posts_carousel_'.$carousel_id.' .owl-item .boc_owl_lazy").css("opacity","1");
									}
									
								});
								
							</script>
							<!-- Posts Carousel :: END -->	            
					';
					
					endif;
					wp_reset_postdata();
					return $str;
	}
	
	add_shortcode('boc_posts_carousel', 'shortcode_boc_posts_carousel');
}




// Map Shortcode in Visual Composer
vc_map( array(
   "name" 		=> __("Posts Carousel", 'Fortuna'),
   "base" 		=> "boc_posts_carousel",
   "category" 	=> "Fortuna Shortcodes",
   "icon" 		=> "boc_posts_carousel",
   "weight"		=> 62,
   "params" 	=> array(
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
			"type"			=> 'checkbox',
			"heading" 		=> __("Loop the slider", 'Fortuna'),
			"param_name" 	=> "loop",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want your Slider to be Infinite", 'Fortuna'),
			"group"			=> "Slider Settings",							
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Enable AutoHeight", 'Fortuna'),
			"param_name" 	=> "auto_height",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes for an AutoHeight slider - it will resize according to the item heights in sight. Works fine only when Visible Items equals '1'.", 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "items_visible",
					'value'		=> "1",
			),
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
			"heading" 		=> __("Post Type Filter", 'Fortuna'),
			"param_name" 	=> "post_type",
			"admin_label"	=> true,
			"value" 		=> "post",
			"description" 	=> __("Filter only a certain Post Type. Could be used for Custom Post Types as well. Default is 'post'.", 'Fortuna'),
			"group"			=> "Query",
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Category Slug Filter", 'Fortuna'),
			"param_name" 	=> "category_slug",
			"value" 		=> "",
			"description" 	=> __("Filter only a certain Category Slug from the specified Post Type. You can list more than one (comma-separated).", 'Fortuna'),
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
			"type" 			=> "checkbox",
			"heading" 		=> __("Exclude current post", 'Fortuna'),
			"param_name" 	=> "exclude_current",
			"value" 		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Useful when using the carousel in a Post to show related post items for example.", 'Fortuna'),
			"group"			=> "Query",
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
			"type" 			=> "checkbox",
			"heading" 		=> __("Boxed Post Item", 'Fortuna'),
			"param_name" 	=> "is_boxed",
			"value" 		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Adds a bordered container to your Post Items. (Slightly changes the styling of the Post Date)", 'Fortuna'),
			"group"			=> "Design",
		),
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Post Date", 'Fortuna'),
			"param_name" 	=> "show_date",
			"value" 		=> Array("Don't show" => 0, "Left Date/Month" => 1, "Below Title" =>2 ),
			"description" 	=> __("Select Post Date Style", 'Fortuna'),
			"group"			=> "Design",
		),
		array(
			"type" 			=> "checkbox",
			"heading" 		=> __("Show Excerpt", 'Fortuna'),
			"param_name" 	=> "show_excerpt",
			"value" 		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want an excerpt from each post to be displayed.", 'Fortuna'),
			"group"			=> "Design",
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Excerpt Character limit", 'Fortuna'),
			"param_name" 	=> "excerpt_char_limit",
			"value" 		=> "64",
			"description" 	=> __("How many characters from a post you wish to be shown as excerpt. Play around with the number for best results.", 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "show_excerpt",
					'not_empty'	=> true,
			),
			"group"			=> "Design",
		),
		array(
			"type" 			=> "checkbox",
			"heading" 		=> __("Add dots to excerpt", 'Fortuna'),
			"param_name" 	=> "add_dots",
			"value" 		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want to add '...' to the excerpt end", 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "show_excerpt",
					'not_empty'	=> true,
			),
			"group"			=> "Design",
		),
		array(
			"type" 			=> "checkbox",
			"heading" 		=> __("Show 'Read More' link", 'Fortuna'),
			"param_name" 	=> "show_read_more",
			"value" 		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want a 'Read More' link below each item in the carousel.", 'Fortuna'),
			"group"			=> "Design",
		),
		array(
			"type"			=> 'dropdown',
			"heading" 		=> __("'Read More' link style", 'Fortuna'),
			"param_name" 	=> "read_more_style",
			"value"			=> Array(__("Simple", "Fortuna") => '1', __("Round Icon", "Fortuna") => '2', __("Square Icon", "Fortuna") => '3' ),
			"description" 	=> __("Pick a style for your 'Read More' link.", 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "show_read_more",
					'value'		=> "yes",
			),
			"std"			=> "2",
			"group"			=> "Design",
		),			
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Show Featured Image", 'Fortuna'),
			"param_name" 	=> "show_pic",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"std"			=> "yes",
			"description" 	=> __("Set to Yes if you want the Featured Images of displayed posts to be shown", 'Fortuna'),
			"group"			=> "Images",
		),
		array(
			"type"			=> 'dropdown',
			"heading" 		=> __("Hover Image Effect", 'Fortuna'),
			"param_name" 	=> "img_hover_effect",
			"value"			=> Array("None" => '', "Zoom Out" => 1, "Zoom In" => 2, "Side" => 3, "Spin" => 4),
			"description" 	=> __("Pick a hover Image Effect", 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "show_pic",
					'value'		=> "yes",
			),
			"group"			=> "Images",
		),
		array(
			'type'			=> "textfield",
			"heading"		=> __( "Overwrite Image Width", 'Fortuna'),
			'param_name'	=> "img_width",
			'value'			=> "",
			'description'	=> __( "Enter a width in pixels if you want to overwrite the default (460). Leave empty for default.", 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "show_pic",
					'value'		=> "yes" ,
			),
			'group'			=> "Images",
		),
		array(
			'type'			=> "textfield",
			"heading"		=> __( "Overwrite Image Height", 'Fortuna'),
			'param_name'	=> "img_height",
			'value'			=> "",
			'description'	=> __( 'Enter a height in pixels if you want to overwrite the default (290). Leave empty for default.', 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "show_pic",
					'value'		=> "yes" ,
			),
			'group'			=> "Images",
		),
   )
));