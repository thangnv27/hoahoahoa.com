<?php
/**
	Custom Slider
**/

// Register Shortcodes

// Custom Slider
if( ! function_exists( 'shortcode_boc_custom_slider' ) ) {
	function shortcode_boc_custom_slider($atts, $content = null) {

		$atts = vc_map_get_attributes('boc_custom_slider', $atts );
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

		// Nav position
		$is_nav_top = true;
		if($nav_position != "Top") {
			$is_nav_top = false;
		}
		
		// generate rand ID
		$slider_id = rand(1,10000);

		$str = '<div class="custom_slides '.($is_nav_top ? "custom_slides_top_nav" : "").' nav_design_'.esc_attr($nav_design).' nav_style_'.esc_attr($nav_style).'" id="custom_slides_'.$slider_id.'">';
		$str .= do_shortcode($content);
		$str .= '</div>';

		$str .= '
					<!-- Custom Slider -->
					<script type="text/javascript">

						jQuery(document).ready(function($) {								
						
							var slider_el = $("#custom_slides_'.$slider_id.'");
							var active_class = " active '.($pulse_active ? "pulsate_icon" : "").'";
							// Create Custom Icons Nav
							slider_el.on("initialized.owl.carousel", function(e) {
							
			'.($is_nav_top ? 'slider_el.prepend("<div class=\"section\"><div class=\"cs_nav\"></div></div>");' :
							 'slider_el.append("<div class=\"section\"><div class=\"cs_nav\"></div></div>");').'						
								
								slider_el.find(".custom_slide_item").each( function(i, el) {
									if(!$(this).parent(".owl-item").hasClass("cloned")){
										var icon = $(this).data("icon");
										var has_text = $(this).data("has_text");
										var txt = $(this).data("txt");
										var sub_txt = $(this).data("sub_txt");
										var nav_item_class = (has_text!="") ? "has_text" : "has_no_text";
										
										if(i==0){
											nav_item_class += active_class;
										}
										var icon_btn_html = "<div class=\"col span_1_of_6 cs_nav_item "+ nav_item_class +"\"><div class=\"cs_nav_icon\"><i class=\"" + icon + "\"></i></div>";
										
										if(has_text!="") {
											icon_btn_html += "<div class=\"cs_sub_txt\">" + sub_txt + "</div><h4 class=\"cs_txt\">" + txt + "</h4>";
										}
										
										icon_btn_html += "</div>";
										
										slider_el.find(".section .cs_nav").append(icon_btn_html);
									}
								});
							});

							slider_el.owlCarousel({
									items: 				1,
									autoHeight: 		'.($auto_height	? "true" : "false").',
									smartSpeed:			'.(int)$speed.',
									dots:				true,
									slideBy: 			1,
									navRewind: 			false,
									rtl: 				' . (is_rtl() ? 'true' : 'false') . ',
									'.(($animation != "Slide") ? " mouseDrag:			false," : "").'	
									
									'.$anim_type_code.'
							});

							// Attach events to Custom Icons Nav
							slider_el.find(".cs_nav_item").each( function(i, el) {
								$(this).click(function(){
									slider_el.find(".cs_nav_item").removeClass(active_class);
									$(this).addClass(active_class);
									slider_el.trigger("to.owl.carousel", i);
								});
							});
							
							// Call back to set Active Icon Nav TODO - bug when loop
							slider_el.on("changed.owl.carousel",function(e) {
								slider_el.find(".cs_nav_item").each( function(i, el) {
									//console.log(e.item.index);
									if(i == e.item.index) {
										slider_el.find(".cs_nav_item").removeClass(active_class);
										$(this).addClass(active_class);
									}
								});
							});
							
						});
									
					</script>
					
					<style>
						#custom_slides_'.$slider_id.'.owl-carousel .animated {
							-webkit-animation-duration: '.(int)$speed.'ms;
							animation-duration: '.(int)$speed.'ms;
						}
					</style>
					<!-- Custom Slider :: END -->';
		
		return $str;		
	}
	
	add_shortcode('boc_custom_slider', 'shortcode_boc_custom_slider');
}	
		
// Custom Slide Item
if( ! function_exists( 'shortcode_boc_custom_slider_item' ) ) {
	function shortcode_boc_custom_slider_item($atts, $content = null) {
		
		$atts = vc_map_get_attributes('boc_custom_slider_item', $atts );
		extract( $atts );	

		$str = '<div class="custom_slide_item '.esc_attr($css_classes).'" data-icon="'.esc_attr($icon).'" data-has_text="'.esc_attr($has_text).'" data-txt="'.esc_attr($txt).'" data-sub_txt="'.esc_attr($sub_txt).'">';
		$str .= do_shortcode($content);
		$str .= '</div>';

		return $str;
	}
	
	add_shortcode('boc_custom_slider_item', 'shortcode_boc_custom_slider_item');
}	
	
// Map Shortcodes in Visual Composer
vc_map( array(
	"name" 		=> __("Custom Slider", 'Fortuna'),
	"base" 		=> "boc_custom_slider",
	"as_parent" => array('only' => 'boc_custom_slider_item'), //limit child shortcodes
	"content_element" => true,
	"icon" 		=> "boc_custom_slider",
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
			"type" 			=> "dropdown",
			"heading" 		=> __("Navigation position", 'Fortuna'),
			"param_name" 	=> "nav_position",
			"value" 		=> array('Top','Bottom'),
			"std"			=> "Top",
			"description" 	=> __("Do you want the navigation above or below the slides", 'Fortuna'),
		),
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Navigation Style", 'Fortuna'),
			"param_name" 	=> "nav_style",
			"value"			=> array(
				__("Icon + Text Below", "Fortuna")		=> '1',
				__("Icon + Text on Hover", "Fortuna")	=> '2'
			),
			"std"			=> '1'
		),
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Navigation Design", 'Fortuna'),
			"param_name" 	=> "nav_design",
			"value"			=> array(
				__("Simple Icons", "Fortuna")		=> '1',
				__("Round Icons", "Fortuna")		=> '2'			
			),
			"std"			=> '1'
		),		
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Animation Speed", 'Fortuna'),
			"param_name" 	=> "speed",
			"value" 		=> array('250','500','750','1000'),
			"std"			=> "500",
			"description" 	=> __("Set the length of the Slider Animation (miliseconds)", 'Fortuna'),
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Enable AutoHeight", 'Fortuna'),
			"param_name" 	=> "auto_height",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes for an AutoHeight slider - it will resize according to the item heights in sight", 'Fortuna'),
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Pulsate on Active Icon", 'Fortuna'),
			"param_name" 	=> "pulse_active",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want the Active Icon to Pulsate", 'Fortuna'),
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
	"name" => __("Custom Slider Item", 'Fortuna'),
	"base" => "boc_custom_slider_item",
	"icon" 		=> "boc_custom_slider",
	"as_child" => array('only' => 'boc_custom_slider'),
	"as_parent" => array('except' => 'boc_custom_slider,boc_custom_slider_item,boc_content_slider,boc_content_slider_item,boc_img_carousel,boc_img_slider,boc_portfolio_carousel,boc_posts_carousel,boc_testimonials'),
	"js_view" => 'VcColumnView',
	"params" => array(

		array(
			"type"          => "iconpicker",
			"heading"       => "Button Icon",
			"param_name"    => "icon",
			"admin_label"   => true,
			"settings" => array(
				'type' => 'fortuna',
				'emptyIcon' => false, // default true, display an "EMPTY" icon?
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display, we use (big number) to display all icons in single page
			),
			'description'   => __( 'Select Navigation icon from library.', 'Fortuna' ),
			"group"         => __( 'Icon', 'Fortuna' ),
		),

		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Has Text?", 'Fortuna'),
			"param_name" 		=> "has_text",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want animated text to appear on Icon Hover", 'Fortuna'),
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Button Sub-Text", 'Fortuna'),
			"param_name" 	=> "sub_txt",
			"description" 	=> __("Set your Navigation Item Sub-Text", 'Fortuna'),
			"value" 		=> "",
			"dependency"	=> Array(
					'element'	=> "has_text",
					'not_empty'	=> true,
			),			
		),		
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Button Text", 'Fortuna'),
			"param_name" 	=> "txt",
			"value" 		=> "Our Company",
			"description" 	=> __("Set your Navigation Item Text", 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "has_text",
					'not_empty'	=> true,
			),				
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Extra class name", 'Fortuna'),
			"param_name" 	=> "css_classes",
			"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),		
	)
));

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_boc_custom_slider extends WPBakeryShortCodesContainer {
	}
}
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_boc_custom_slider_item extends WPBakeryShortCodesContainer {
	}
}
