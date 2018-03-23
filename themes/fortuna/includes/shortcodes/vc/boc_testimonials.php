<?php

/**
	Testimonial Slider
**/

// Register Shortcodes
if( ! function_exists( 'shortcode_boc_testimonials' ) ) {
	function shortcode_boc_testimonials($atts, $content = null) {
		
		$atts = vc_map_get_attributes('boc_testimonials', $atts );
		extract( $atts );	

		// Animation Type
		if ($animation == 'Slide') {
			$anim_out = "false";
			$anim_in = "false";
		} elseif ($animation == 'Fade') {
			$anim_out = "\"fadeOut\"";
			$anim_in = "\"fadeIn\"";
		} elseif ($animation == 'Bottom-Top') {
			$anim_out = "\"fadeOutDown\"";
			$anim_in = "\"fadeInUp\"";
		} elseif ($animation == 'Flip') {
			$anim_out = "\"fadeOutDown\"";
			$anim_in = "\"flipInX\"";		
		}
		
		// Arrows
		$nav = (($navigation == "Arrows") || ($navigation == "Both"));
		
		// Dots
		$dots = (($navigation == "Dots") || ($navigation == "Both"));
		
		$classes = 	($dots 			? ' owl_has_dot_nav'	: '').
					($style			? ' testimonial_style_'.$style : '').
					((($style=="small") && !$is_3d) 	? ' is_2d' 		: '').
					((($style=="big") && $is_minimal)	? ' is_minimal' : '').
					($css_classes	? ' '.$css_classes : '');
					

		$carousel_id = rand(0,100000);
		
		$str='';        
		$str .= '<!-- Testimonials -->
				<div class="testimonials">
					<div id="testimonial_carousel_'.$carousel_id.'" style="opacity: 0;" class="testimonials_carousel '.esc_attr($classes).'">';

		$str .= do_shortcode($content);
		
		$str .= '</div>
				</div>
				<!-- Testimonials::END -->

				<!-- Testimonials Carousel JS -->
				<script type="text/javascript">

					jQuery(document).ready(function($) {

						var carousel = $("#testimonial_carousel_'.$carousel_id.'");

						var args = {
							onInitialized:  		bocShowTestimonialCarousel_'.$carousel_id.',
							items: 1,
							autoplay:			'.($autoplay	? 'true': 'false').',
							autoplayTimeout:	'.(int)$autoplay_interval.',
							loop: 				'.($loop 		? 'true': 'false').',
							nav: 				'.($nav 		? 'true': 'false').',
							dots: 				'.($dots 		? 'true': 'false').',
							autoHeight: 			'.($auto_height	? 'true': 'false').',
							navText:				'.($style=="big" ?
								'["<span class=\'icon  icon-angle-left-circle\'></span>","<span class=\'icon icon-angle-right-circle\'></span>"]' :
								'["<span class=\'icon icon-arrow-left7\'></span>","<span class=\'icon icon-arrow-right7\'></span>"]').',
							navRewind: false,
							rtl: 				' . (is_rtl() ? 'true' : 'false') . ',
							smartSpeed:			600,
							margin: 				10,					
							animateOut: 		'.$anim_out.',
							animateIn: 			'.$anim_in.',
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
						function bocShowTestimonialCarousel_'.$carousel_id.'(){
							carousel.fadeTo(0,1);
							jQuery("#testimonial_carousel_'.$carousel_id.' .owl-item .boc_owl_lazy").css("opacity","1");
						}

					});
	
				</script>
				<!-- Testimonials Carousel JS: END -->	    
    
	
				';			 
				 

		return $str;
	}
	
	add_shortcode('boc_testimonials', 'shortcode_boc_testimonials');
}

// Single Testimonial
if( ! function_exists( 'shortcode_boc_testimonial' ) ) {
	function shortcode_boc_testimonial($atts, $content = null) {
		
		$atts = vc_map_get_attributes('boc_testimonial', $atts );
		extract( $atts );	
	
		// Adding a check for VC picture added
		$img = '';
		if($atts['picture_url']){
			$vc_image = wp_get_attachment_image($atts['picture_url'],'full');
			// If not passed via VC, we get the URL
			if($vc_image){
				$img = $vc_image;
			}
		}else {
			$img = '<img class="empty_user_testimonial_image" src="'.get_template_directory_uri().'/images/user.png" />';
		}
		
		$str = '';
		$str .= '	<div class="testimonial_quote boc_owl_lazy">
						<div class="quote_content">
							<p>'.$content.'</p>
							<span class="quote_arrow"></span>
						</div>
						<div class="quote_author heading_font">'.$img.'<div class="icon_testimonial">'.$atts['author'].'</div><span class="quote_author_description">'.$atts['author_title'].'</span>	</div>
					</div>';

		return $str;
	}
	
	add_shortcode('boc_testimonial', 'shortcode_boc_testimonial');
}

// Map Shortcodes in Visual Composer
vc_map( array(
	"name" => __("Testimonial Slider", 'Fortuna'),
	"base" => "boc_testimonials",
	"as_parent" => array('only' => 'boc_testimonial'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
	"content_element" => true,
	"icon" 		=> "boc_testimonials",
	"category" => "Fortuna Shortcodes",
	"show_settings_on_create" => true,
	"weight"	=> 66,
	"params" => array(
		array(
			"type"			=> 'dropdown',
			"heading" 		=> __("Design Style", 'Fortuna'),
			"param_name" 		=> "style",
			"value"			=> Array("Big-Centered" => 'big',"Small-Boxed" => 'small'),
			"description" 	=> __("Select your Testimonial Slider Style", 'Fortuna'),
		),	
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Is small boxed testimonial 3d style?", 'Fortuna'),
			"param_name" 		=> "is_3d",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want the 3d style for your small-boxed testimonial slider.", 'Fortuna'),
			"dependency"		=> Array(
				'element'	=> "style",
				'value'		=> Array("small"),
			),				
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Is Big-Centered minimal style activated?", 'Fortuna'),
			"param_name" 		=> "is_minimal",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want the minimal style for your Big Centered testimonial slider.", 'Fortuna'),
			"dependency"		=> Array(
				'element'	=> "style",
				'value'		=> Array("big"),
			),				
		),
		array(
			"type"			=> 'dropdown',
			"heading" 		=> __("Slider Navigation", 'Fortuna'),
			"param_name" 	=> "navigation",
			"value"			=> Array("Arrows", "Dots", "Both"),
			"std"			=> "Arrows",
			"description" 	=> __("Select a Navigation Type", 'Fortuna'),
		),
	
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Animation Type", 'Fortuna'),
			"param_name" 		=> "animation",
			"value" 			=> array('Slide', 'Fade', 'Bottom-Top', 'Flip'),
			"description" 	=> __("Set your Animation type", 'Fortuna'),
		),		
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("AutoPlay the slider?", 'Fortuna'),
			"param_name" 		=> "autoplay",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes'),
			"description" 	=> __("Set to Yes if you want your Testimonial Slider to autoplay", 'Fortuna'),					
		),
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("AutoPlay Interval", 'Fortuna'),
			"param_name" 		=> "autoplay_interval",
			"value" 			=> array('4000','6000','8000','10000','12000','14000'),
			"std"			=> "6000",
			"description" 	=> __("Set the Autoplay Interval (miliseconds).", 'Fortuna'),
			"dependency"		=> Array(
					'element'	=> "autoplay",
					'not_empty'	=> true,
			),				
		),			
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Loop the slider", 'Fortuna'),
			"param_name" 		=> "loop",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want your Slider to be Infinite", 'Fortuna'),								
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Enable AutoHeight", 'Fortuna'),
			"param_name" 		=> "auto_height",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes for an AutoHeight slider - it will resize according to the item heights in sight", 'Fortuna'),	
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Extra class name", 'Fortuna'),
			"param_name" 		=> "css_classes",
			"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),
		
	),
	"js_view" => 'VcColumnView'
) );

vc_map( array(
	"name" => __("Testimonial Item", 'Fortuna'),
	"base" => "boc_testimonial",
	"content_element" => true,
	"as_child" => array('only' => 'boc_testimonials'), // Use only|except attributes to limit parent (separate multiple values with comma)
	"icon" 	=> "boc_testimonials",
	"params" => array(
		array(
			"type" => "textfield",
			"heading" => __("Text", 'Fortuna'),
			"param_name" => "content",
			"value" => __("Testimonial Text", 'Fortuna'),
			"description" => __("Enter the text for your Testimonial", 'Fortuna'),
		),
		array(
			"type" => "textfield",
			"heading" => __("Testimonial Author", 'Fortuna'),
			"param_name" => "author",
			"value" => __("Lindsay Ford", 'Fortuna'),
			"admin_label"	=> true,
			"description" => __("Enter author name", 'Fortuna'),
		),
		array(
			"type" => "textfield",
			"heading" => __("Testimonial Author Title", 'Fortuna'),
			"value" => __("Designer", 'Fortuna'),
			"param_name" => "author_title",
			"description" => __("Enter author title", 'Fortuna'),
		),
		array(
			"type" => "attach_image",
			"heading" => __("Picture", 'Fortuna'),
			"param_name" => "picture_url",
			"description" => __("Add Testimonial Image", 'Fortuna'),
		),
	)
));

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_boc_testimonials extends WPBakeryShortCodesContainer {
	}
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_boc_testimonial extends WPBakeryShortCode {
	}
}