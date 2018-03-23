<?php
/**
	Content Slider
**/

// Register Shortcodes

// Content Slider
if( ! function_exists( 'shortcode_boc_content_slider' ) ) {
	function shortcode_boc_content_slider($atts, $content = null) {

		$atts = vc_map_get_attributes('boc_content_slider', $atts );
		extract( $atts );

		if(!$content) return;
		
		// Animation Type
		if ($animation == 'Slide') {
			$anim_type_code = "";
		} elseif ($animation == 'Fade') {
			$anim_type_code = 'animateOut:"fadeOut",	animateIn: "fadeIn"';
		} elseif ($animation == 'Bottom-Top') {
			$anim_type_code = 'animateOut:"fadeOutDown",	animateIn: "fadeInUp"';
		} elseif ($animation == 'Flip') {
			$anim_type_code = 'animateOut:"fadeOutDown",	animateIn: "flipInX"';
		}

		// Arrows
		$nav = (($navigation == "Arrows") || ($navigation == "Both"));

		// Dots
		$dots = (($navigation == "Dots") || ($navigation == "Both"));
		if($dots){
			$css_classes .= ' has_dots'; 
		}			

		// generate rand ID
		$slider_id = rand(1,10000);

		$str = '<div class="content_slides '.($nav ? "content_slides_arrowed" : "").' '.($dots ? "has_dots" : "").'" id="content_slides_'.$slider_id.'">';
		$str .= do_shortcode($content);
		$str .= '</div>';

		$str .= '
					<!-- content Slider -->
					<script type="text/javascript">

						jQuery(document).ready(function($) {								
						
							var slider_el = $("#content_slides_'.$slider_id.'");

							// Create content Icons Nav
							

							slider_el.owlCarousel({
									items: 				1,
		'.(($animation != "Slide") ? " mouseDrag:			false," : "").'	
									autoplay:			'.($autoplay ? "true" : "false").',
									autoplayTimeout:		'.(int)$autoplay_interval.',
									nav: 				'.($nav 	? "true" : "false").',
						'.( $nav ? "	navText:			[\"<span class='icon icon-angle-left-circle'></span>\",\"<span class='icon icon-angle-right-circle'></span>\"]," : "").'
									autoHeight: 			'.($auto_height	? "true" : "false").',
									smartSpeed:			'.(int)$speed.',
									dots:				'.($dots	? "true" : "false").',
									loop: 				'.($loop	? "true" : "false").',
									slideBy: 			1,
									navRewind: 			false,
									
									'.$anim_type_code.'
							});
						});
						
					</script>
					
					<style>
						#content_slides_'.$slider_id.'.owl-carousel .animated {
							-webkit-animation-duration: '.(int)$speed.'ms;
							animation-duration: '.(int)$speed.'ms;
						}
					</style>
					<!-- content Slider :: END -->';
		
		return $str;		
	}
	
	add_shortcode('boc_content_slider', 'shortcode_boc_content_slider');
}	
		
// Content Slide Item
if( ! function_exists( 'shortcode_boc_content_slider_item' ) ) {
	function shortcode_boc_content_slider_item($atts, $content = null) {
		
		$atts = vc_map_get_attributes('boc_content_slider_item', $atts );
		extract( $atts );	

		$str = '<div class="content_slide_item '.esc_attr($css_classes).'">';
		$str .= do_shortcode($content);
		$str .= '</div>';

		return $str;
	}
	
	add_shortcode('boc_content_slider_item', 'shortcode_boc_content_slider_item');
}	
	
// Map Shortcodes in Visual Composer
vc_map( array(
	"name" 		=> __("Content Slider", 'Fortuna'),
	"base" 		=> "boc_content_slider",
	"as_parent" => array('only' => 'boc_content_slider_item'), //limit child shortcodes
	"content_element" => true,
	"icon" 		=> "boc_content_slider",
	"category" 	=> "Fortuna Shortcodes",
	"show_settings_on_create" => true,
	"js_view" 	=> 'VcColumnView',
	"weight"  	=> 26,
	"params" => array(
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Animation Type", 'Fortuna'),
			"param_name" 	=> "animation",
			"value" 		=> array('Slide','Fade','Bottom-Top','Flip'),
			"std"			=> "Slide",
			"description" 	=> __("Set your Animation type", 'Fortuna'),
		),		
			array(
			"type"			=> 'dropdown',
			"heading" 		=> __("Slider Navigation", 'Fortuna'),
			"param_name" 		=> "navigation",
			"value"			=> Array("Arrows", "Dots", "Both"),
			"description" 	=> __("Select a Navigation Type", 'Fortuna'),
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
			"type"			=> 'checkbox',
			"heading" 		=> __("Enable AutoHeight", 'Fortuna'),
			"param_name" 	=> "auto_height",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes for an AutoHeight slider - it will resize according to the item heights in sight", 'Fortuna'),
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Extra class name", 'Fortuna'),
			"param_name" 	=> "css_classes",
			"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),
	)
));

vc_map( array(
	"name" => __("Content Slider Item", 'Fortuna'),
	"base" => "boc_content_slider_item",
	"icon" 		=> "boc_content_slider",
	"as_child" => array('only' => 'boc_content_slider'),
	"as_parent" => array('except' => 'boc_custom_slider,boc_custom_slider_item,boc_content_slider,boc_content_slider_item,boc_img_carousel,boc_img_slider,boc_portfolio_carousel,boc_posts_carousel,boc_testimonials'),
	"js_view" => 'VcColumnView',
	"params" => array(
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Extra class name", 'Fortuna'),
			"param_name" 	=> "css_classes",
			"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),		
	)
));

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_boc_content_slider extends WPBakeryShortCodesContainer {
	}
}
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_boc_content_slider_item extends WPBakeryShortCodesContainer {
	}
}
