<?php

/**
	Image Slider 
**/

// Register Shortcode
if( ! function_exists( 'shortcode_boc_img_slider' ) ) {
	function shortcode_boc_img_slider($atts, $content = null) {

		$atts = vc_map_get_attributes('boc_img_slider', $atts );
		extract( $atts );

		if ($animation == 'Fade') {
			$anim_type_code = 'animateOut:"fadeOut", animateIn: "fadeIn"';
		}else {
			$anim_type_code = '';
		}
			
		// Get Attachments
		$images = explode(",",$image_ids);
		if($images){

			// Arrows
			$nav = (($navigation == "Arrows") || ($navigation == "Both"));

			// Dots
			$dots = (($navigation == "Dots") || ($navigation == "Both"));
			if($dots){
				$css_classes .= ' has_dots'; 
			}			
			
			// Big arrows
			if($big_arrows){
				$css_classes .= ' big_arrows'; 
			}

			// Custom Links
			if ( $img_link == 'custom_link' ) {
				$custom_links = explode( ',', $custom_links);
			}

			// generate checksum of $atts array
			$slider_id = md5(serialize($atts));
			
			$str = '';
			$str .= '
					<!-- Image Slider -->
					<script type="text/javascript">

						jQuery(document).ready(function($) {
							
							// Load carousel after its images are loaded
							preloadImages($("#img_slider_'.$slider_id.' img"), function () {
								
								var carousel = $("#img_slider_'.$slider_id.'");
								
								var args = {
									items: 			1,
									mouseDrag:		true,
									autoplay:		'.($autoplay ? "true" : "false").',
									autoplayTimeout:'.(int)$autoplay_interval.',
									nav: 			'.($nav 	? "true" : "false").',
									dots: 			'.($dots 	? "true" : "false").',
									loop: 			'.($loop	? "true" : "false").',
									autoHeight: 	'.($auto_height	? "true" : "false").',
									smartSpeed:		'.(int)$speed.',
						'.( $nav ? "	navText:			[\"<span class='icon icon-arrow-left8'></span>\",\"<span class='icon icon-uniE708'></span>\"]," : "").'
									slideBy: 		1,
									navRewind: 		false,
									rtl: 			' . (is_rtl() ? 'true' : 'false') . ',
									onInitialized:  	showCarousel_'.$slider_id.',
									'.$anim_type_code.'
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
							
							
							/* Show after initialized */
							function showCarousel_'.$slider_id.'() {
								$("#img_slider_'.$slider_id.'").fadeTo(0,1);
								$("#img_slider_'.$slider_id.' .owl-item .boc_owl_lazy").css("opacity","1");
							}							
							
							
						});

					</script>
					<!-- Image Slider :: END -->';
					
			$str .= '<div class="img_slider mfp_gallery '.esc_attr($css_classes).'" id="img_slider_'.esc_attr($slider_id).'" style="opacity:0;">';


			// Used for custom links array
			$count = 0;
			
			foreach ( $images as $img_id ){
				
				// Attachment VARS
				$att_img_link = wp_get_attachment_image_src( $img_id, 'full');
				$att_img_url = wp_get_attachment_url( $img_id );
				$att_img_title = get_the_title($img_id);
				$att_img_alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
				
				// Image output
				$image_output = '<img src="'. esc_url($att_img_url) .'" alt="'. ($show_img_alt ? esc_attr($att_img_alt) : "") .'" title="'. ($show_img_title ? esc_attr($att_img_title) : "") .'"/>';

				// Slider item start 
				$str .= '<div class="img_slider_item boc_owl_lazy">';

					
				if ( 'lightbox' == $img_link ) {
					$str .= '<div class="pic">
								<a href="'. esc_url($att_img_link[0]) .'" class="mfp_popup_gal" title="'. ($show_img_title ? esc_attr($att_img_title) : "") .'">
									'.$image_output.'
									'.($show_hover_overlay ? "<div class='img_overlay'><span class='hover_icon icon_zoom'></span></div>" : "").'
								</a>
							</div>';
								
				}elseif ( 'custom_link' == $img_link ) {

					$att_img_link = !empty($custom_links[$count]) ? $custom_links[$count] : '#';
					if ( $att_img_link == '#' ) {
						$str .= $image_output;
					} else {
						$str .= '
							<div class="pic">
								<a href="'. esc_url($att_img_link) .'" title="'. ($title ? esc_attr($att_img_title) : "") .'" target="'. esc_attr($target) .'">
									'.$image_output.''.($show_hover_overlay ? "<div class='img_overlay'><span class='hover_icon icon_zoom'></span></div>" : "").'
								</a>
							</div>';
					}
				} else {
					$str .= $image_output;
				}
					
				// Close main wrap	
				$str .= '</div>';
				
				$count ++;
			}
			
			$str .= '</div>';				

			return $str;
		}
	}
	
	add_shortcode('boc_img_slider', 'shortcode_boc_img_slider');
}


// Map Shortcode in Visual Composer
vc_map( array(
	"name" => __("Image Slider", 'Fortuna'),
	"base" => "boc_img_slider",
	"category" => "Fortuna Shortcodes",
	"icon" 		=> "boc_img_slider",
	"weight"	=> 52,
	"params" => array(
		
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Animation", 'Fortuna'),
			"param_name" 		=> "animation",
			"value" 			=> array('Slide','Fade'),
			"description" 	=> __("Pick Animation type", 'Fortuna'),
			"group"			=> "Slider Settings",
		),		
			array(
			"type"			=> 'dropdown',
			"heading" 		=> __("Slider Navigation", 'Fortuna'),
			"param_name" 		=> "navigation",
			"value"			=> Array("Arrows", "Dots", "Both"),
			"description" 	=> __("Select a Navigation Type", 'Fortuna'),
			"group"			=> "Slider Settings",
		),
		array(
			"type"			=> "checkbox",
			"heading"		=> __("Big arrows?", 'Fortuna'),
			"param_name"		=> "big_arrows",
			"description"		=> __('Set if you want bigger arrows.', 'Fortuna'),
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"dependency"		=> Array(
				'element'	=> "navigation",
				'value'		=> array( 'Arrows', 'Both' )
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
			"type"			=> 'checkbox',
			"heading" 		=> __("Enable AutoHeight", 'Fortuna'),
			"param_name" 	=> "auto_height",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes for an AutoHeight slider - it will resize according to the item heights in sight", 'Fortuna'),
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
			"heading" 		=> __("Show Dark Overlay on Image Hover", 'Fortuna'),
			"param_name" 	=> "show_hover_overlay",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Show Dark Overlay with Zoom Icon upon hovering linked images", 'Fortuna'),
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