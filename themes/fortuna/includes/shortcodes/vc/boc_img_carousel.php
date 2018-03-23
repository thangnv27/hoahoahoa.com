<?php

/**
	Image Carousel (logos)
**/

// Register Shortcode
if( ! function_exists( 'shortcode_boc_img_carousel' ) ) {
	function shortcode_boc_img_carousel($atts, $content = null) {

		$atts = vc_map_get_attributes('boc_img_carousel', $atts );
		extract( $atts );

		// Get Attachments
		$images = explode(",",$image_ids);
		if($images){

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
			
			// Custom Links
			if ( $img_link == 'custom_link' ) {
				$custom_links = explode( ',', $custom_links);
			}

			// generate checksum of $atts array
			$slider_id = rand(0,10000);
			
			$str = '<div class="img_carousel_holder '.$css_classes.'">';
			$str .= '	<div class="img_carousel mfp_gallery" id="img_carousel_'.$slider_id.'" style="opacity:0;">';

			// Used for custom links array
			$count = 0;
			
			foreach ( $images as $img_id ){
				
				// Attachment VARS
				$att_img_url = wp_get_attachment_image_src( $img_id, ($fixed_size ? 'boc_medium' : 'full') );
				$att_img_link = wp_get_attachment_image_src( $img_id, 'full');
				$att_img_title = get_the_title($img_id);
				$att_img_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
				
				// Image output
				$image_output = '<img src="'. esc_url($att_img_url[0]) .'" alt="'. ($show_img_alt ? esc_attr($att_img_alt) : "") .'" />';

				// Slider item start
				$str .= '<div class="img_carousel_item boc_owl_lazy">';
				
				$link_class = '';
				
				// Link & class
				if ( 'lightbox' == $img_link ) {
					$link =  esc_url($att_img_link[0]);
					$link_class = 'mfp_popup_gal';
				}elseif ( 'custom_link' == $img_link ) {
					$att_img_link = !empty($custom_links[$count]) ? $custom_links[$count] : '#';
					if ( $att_img_link == '#' ) {
						$link = 'javascript:void(0)';
					} else {
						$link = esc_url($att_img_link);
					}
				} else {
					$link = 'javascript:void(0)';
				}
				
				$extra_tags = '';
				// Img Title
				if($show_img_title) {
					$link_class .= " tooltipsy";
					$extra_tags = 'original-title="'.esc_attr($att_img_title).'"';
				}
				
				$str .= '	<div class="pic '.$img_hover_effect.'">
								<a href="'.$link.'" class="'.$link_class.'" '.$extra_tags.' target="'. esc_attr($target) .'">
									'.$image_output.'
								</a>
							</div>
						</div>';
				
				$count ++;
			}		
			
			$str .= '</div>
				 </div>';	

			$str .= '
					<!-- Image Slider -->
					<script type="text/javascript">

						jQuery(document).ready(function($) {
							
							var carousel = jQuery("#img_carousel_'.$slider_id.'");
							
							var args = {
									items: 				'.(int)$items_visible.',
									slideBy: 			'.(int)$items_slided.',						
									autoplay:			'.($autoplay ? "true" : "false").',
									autoplayTimeout:	'.(int)$autoplay_interval.',
									nav: 				'.($nav 	? "true" : "false").',
									dots: 				'.($dots 	? "true" : "false").',
									loop: 				'.($loop	? "true" : "false").',
									smartSpeed:			'.(int)$speed.',
									navText:			'.(($nav && ($arrows_position!='top')) ? 
										"[\"<span class='icon  icon-angle-left-circle'></span>\",\"<span class='icon icon-angle-right-circle'></span>\"]" : 
										"[\"<span class='icon icon-arrow-left7'></span>\",\"<span class='icon icon-arrow-right7'></span>\"]").',
									navRewind: 			false,
									rtl : 				' . (is_rtl() ? 'true' : 'false') . ',
									margin: 			'.esc_js($spacing).',
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
									onInitialized: showCarousel_'.$slider_id.'
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
							
							/* Show once loaded */
							function showCarousel_'.$slider_id.'() {
								carousel.fadeTo(0,1);
								$("#img_carousel_'.$slider_id.' .owl-item .boc_owl_lazy").css("opacity","1");
							}
						});
						
					</script>
					<!-- Image Slider :: END -->';
			

			return $str;
		}
	}

	add_shortcode('boc_img_carousel', 'shortcode_boc_img_carousel');	
}


// Map Shortcode in Visual Composer
vc_map( array(
	"name" => __("Image Carousel", 'Fortuna'),
	"base" => "boc_img_carousel",
	"category" => "Fortuna Shortcodes",
	"icon" 		=> "boc_img_carousel",
	"weight"	=> 48,
	"params" => array(
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Total Visible Items", 'Fortuna'),
			"param_name" 	=> "items_visible",
			"value" 		=> Array(2,3,4,5,6),
			"std"			=> "3",
			"description" 	=> __("How many items you want the viewport to be consisted of.", 'Fortuna'),
		),
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Slide Items at once", 'Fortuna'),
			"param_name" 	=> "items_slided",
			"value" 		=> Array(1,2,3,4,5,6),
			"std"			=> "1",
			"description" 	=> __("How many items will slide per click.", 'Fortuna'),
		),
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Item Spacing", 'Fortuna'),
			"param_name" 	=> "spacing",
			"value" 		=> array('Big Spacing'=>'50','Small Spacing'=>'25','No Spacing'=>'0'),
			"std"			=> '25',
			"description" 	=> __("Pick a spacing between the items in the grid", 'Fortuna'),
		),		
		array(
			"type"			=> 'dropdown',
			"heading" 		=> __("Slider Navigation", 'Fortuna'),
			"param_name" 	=> "navigation",
			"value"			=> Array("Arrows", "Dots", "Both"),
			"std"			=> 'Arrows',
			"description" 	=> __("Select a Navigation Type", 'Fortuna'),
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
			"std"			=> 'top',
		),			
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("AutoPlay the slider?", 'Fortuna'),
			"param_name" 	=> "autoplay",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want your Testimonial Slider to autoplay", 'Fortuna'),
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
		),
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Animation Speed", 'Fortuna'),
			"param_name" 	=> "speed",
			"value" 		=> array('250','500','750','1000'),
			"std"			=> "250",
			"description" 	=> __("Set the length of the Slider Animation (miliseconds)", 'Fortuna'),
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Loop the slider", 'Fortuna'),
			"param_name" 	=> "loop",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want your Slider to be Infinite", 'Fortuna'),
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Extra class name", 'Fortuna'),
			"param_name" 	=> "css_classes",
			"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),
		array(
			"type"			=> "attach_images",
			"admin_label"	=> true,
			"heading"		=> __("Attach Images", 'Fortuna'),
			"param_name"	=> "image_ids",
			"description"	=> __('Select the images you want to include in your Slider.', 'Fortuna'),
			"group"			=> "Gallery",
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> __( "Image Link", 'Fortuna'),
			"param_name"	=> "img_link",
			"value"			=> array(
				"None"			=> "none",
				"Lightbox"		=> "lightbox",
				"Custom Links"	=> "custom_link",
			),
			"description"	=> __( "Where should the slider images link to?", 'Fortuna'),
			"group"			=> "Gallery",
		),
		array(
			"type"			=> "exploded_textarea",
			"heading"		=> __("Custom Links", 'Fortuna'),
			"param_name"	=> "custom_links",
			"description"	=> __('Enter links for each separate image here (starting with "http://"). Divide each link with a line-break (Enter). For images without a link enter the "#" symbol.', 'Fortuna'),
			"dependency"	=> Array(
				'element'	=> "img_link",
				'value'		=> array( 'custom_link' )
			),
			"group"			=> "Gallery",
		),
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Target", 'Fortuna'),
			"param_name" 	=> "target",
			"value" 		=> array('_self','_blank'),
			"description" 	=> __("Pick '_blank' if you want the button link to open in a new tab.", 'Fortuna'),
			"dependency"	=> Array(
				'element'	=> "img_link",
				'value'		=> array( 'custom_link' )
			),
			"group"			=> "Gallery",
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Fixed Image Size", 'Fortuna'),
			"param_name" 	=> "fixed_size",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want to use the fixed size (600x380) image preset", 'Fortuna'),
			"group"			=> "Gallery",
		),
		array(
			"type"			=> 'dropdown',
			"heading" 		=> __("Hover Image Effect", 'Fortuna'),
			"param_name" 	=> "img_hover_effect",
			"value"			=> Array("None" => '', "Zoom Out" => 1, "Zoom In" => 2, "Grey Out"=>9),
			"description" 	=> __("Pick a hover Image Effect", 'Fortuna'),
			"group"			=> "Gallery",
		),		
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Add Image Title", 'Fortuna'),
			"param_name" 	=> "show_img_title",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want the Title Attribute added to your images' links", 'Fortuna'),
			"group"			=> "Gallery",
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Add Image Alt", 'Fortuna'),
			"param_name" 	=> "show_img_alt",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want the Alt Attribute added to your image(s)", 'Fortuna'),
			"group"			=> "Gallery",
		),
	)
));