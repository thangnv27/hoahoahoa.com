<?php

/**
	Divider
**/

// Register Shortcode
if( ! function_exists( 'boc_shortcode_divider' ) ) {
	function boc_shortcode_divider( $atts ) {
		

		$atts = vc_map_get_attributes('boc_divider', $atts );
		extract( $atts );

		// CSS Animation
		$css_animation_classes = "";
		if ( $css_animation != '' ) {
			$css_animation_classes = 'boc_animate_when_almost_visible boc_'. $css_animation .'';
		}
		
		$custom_styles = array();

		if ( $margin_top ) {
			$custom_styles[] = 'margin-top: '. $margin_top .';';
		}
		
		if( $margin_bottom ) {
			$custom_styles[] = 'margin-bottom: '. $margin_bottom .';';
		}
		
		if ( $divider_width ) {
			$custom_styles[] = 'width: '. $divider_width .';';
			if($divider_position == "center"){
				$custom_styles[] = 'margin-left: auto; margin-right: auto;';
			}elseif($divider_position == "right"){
				$custom_styles[] = 'float: right;';
			}
		}
		if ( $divider_height ) {
			$custom_styles[] = 'height: '. $divider_height .';';
		}
		if ( $divider_color ) {
			$custom_styles[] = 'background: '. $divider_color .';';
		}
		
		$custom_styles = implode('', $custom_styles);

		if ( $custom_styles ) {
			$custom_styles = wp_kses( $custom_styles, array() );
			$custom_styles = ' style="' . esc_attr($custom_styles) . '"';
		}
		
		// Icon Style
		$icon_style = array();
		
		if ( $icon ) {
		
			if( $icon_size ) {
				$icon_style[] = 'font-size: '. $icon_size .'; line-height: '. $icon_size .';';
			}
			if ( $icon_color && $icon_color !== '#eeeeee' ) {
				$icon_style[] = 'color: '. $icon_color .';';
			}
			if ( $icon_position == 'center') {
				$icon_style[] = 'left: 50%; transform: translateY(-50%) translateX(-50%);';
			}
			if ( $icon_position == 'right') {
				$icon_style[] = 'left: 100%; transform: translateY(-50%) translateX(-100%);';
			}
			if ( $icon_bg ) {
				$icon_style[] = 'background-color: '. $icon_bg .';';
			}
			if ( $icon_bg_border ) {
				$icon_style[] = 'border: 1px solid '. $icon_bg_border .';';
			}
			if ( $icon_padding ) {
				$icon_style[] = 'padding: '. $icon_padding .';';
			}
			$icon_style = implode('', $icon_style);
		}

		if ( $icon_style ) {
			$icon_style = wp_kses( $icon_style, array() );
			$icon_style = ' style="' . esc_attr($icon_style) . '"';
		}		
		
		// Output
		if ( $icon ) {
			$str = '<div class="boc_divider_holder"><div class="boc_divider '. esc_attr($css_classes) .' '.esc_attr($css_animation_classes).'" '.$custom_styles.'><i class="'. esc_attr($icon) .'" '. $icon_style .'></i></div></div>';
		} else {
			$str = '<div class="boc_divider_holder"><div class="boc_divider '.  esc_attr($css_classes) .' '. esc_attr($css_animation_classes).'" '.$custom_styles.'></div></div>';
		}
		
		return $str;
	}
	
	add_shortcode( 'boc_divider', 'boc_shortcode_divider' );
}


// Map Shortcode in Visual Composer
vc_map( array(
	"name"				=> "Divider",
	"description"		=> "Custom Separator",
	"base"				=> "boc_divider",
	"icon" 				=> "boc_divider",
	'category'			=> "Fortuna Shortcodes",
	"weight"			=> 78,
	"params"				=> array(
		array(
			"type"		=> "dropdown",
			"heading"	=> "Width",
			"param_name"	=> "divider_width",
			"value"		=> array(
				"100%"	=> "",
				"80%"	=> "80%",
				"60%"	=> "60%",
				"50%"	=> "50%",
				"200px"	=> "200px",
				"100px"	=> "100px",
				"60px"	=> "60px",
				"50px"	=> "50px",
				"40px"	=> "40px",
			),
			"std"	=> "",
			"description"	=> "Select your divider width. Can be either relative to its container (%) or a fixed width (px)",
		),
		array(
			"type"		=> "dropdown",
			"heading"	=> "Alignment",
			"param_name"	=> "divider_position",
			"value"		=> array(
				"Left"	=> "left",
				"Center"	=> "center",
				"Right"	=> "right",
			),
			"std"	=> "left",
			"description"	=> "Select your divider position if shorter than 100%.",
			"dependency"	=> array(
				'element'	=> "divider_width",
				'not_empty'	=> true,
				)
		),			
		array(
			"type"		=> "dropdown",
			"heading"	=> "Height",
			"param_name"	=> "divider_height",
			"admin_label"	=> true,
			"value"		=> array(
				"1px"	=> "1px",
				"2px"	=> "2px",
				"3px"	=> "3px",
				"4px"	=> "4px",
			),
			"std"		=> "1px",
			"description"	=> "Select your divider height in px",
		),
		array(
			"type"		=> "colorpicker",
			"heading"	=> "Divider Color",
			"param_name"	=> "divider_color",
			"value"		=> "#eee",
			"description"	=> "Select your divider color. Defaults to bright grey (#eeeeee)",
		),			
		array(
			"type"		=> "textfield",
			"heading"	=> "Margin Top",
			"param_name"	=> "margin_top",
			"std"		=> "20px",
			"description"	=> "Enter a top margin for your divider in pixels. Enter 0 for none.",
		),
		array(
			"type"		=> "textfield",
			"heading"	=> "Margin Bottom",
			"param_name"	=> "margin_bottom",
			"std"		=> "20px",
			"description"	=> "Enter a bottom margin for your divider in pixels. Enter 0 for none.",
		),
		array(
			"type"		=> "dropdown",
			"heading"	=> __("CSS Animation", "Fortuna"),
			"param_name"	=> "css_animation",
			"admin_label"	=> true,
			"value"			=> array(
				__("None", "Fortuna")					=> "",
				__("Top to bottom", "Fortuna")			=> "top-to-bottom",
				__("Bottom to top", "Fortuna")			=> "bottom-to-top",
				__("Left to right", "Fortuna")			=> "left-to-right",
				__("Right to left", "Fortuna")			=> "right-to-left",
				__("Fade In", "Fortuna")				=> "fade-in"
			),
			"description"	=> __("Select one if you want this element to be animated once it enters the browsers viewport.", "Fortuna"),
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> "Extra class name",
			"param_name" 	=> "css_classes",
			"description" 	=> "If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file"
		),
		array(
			"type"          => "iconpicker",
			"heading"       => "Icon",
			"param_name"    => "icon",
			"admin_label"   => true,
			"settings" => array(
				'type' => 'fortuna',
				'emptyIcon' => true, // default true, display an "EMPTY" icon?
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display, we use (big number) to display all icons in single page
			),
			'description'   => __( 'Select icon from library.', 'Fortuna' ),
			"group"         => __( 'Icon', 'Fortuna' ),
		),		
		
		array(
			"type"		=> "dropdown",
			"heading"	=> "Icon Position",
			"param_name"	=> "icon_position",
			"value"		=> array(
				"Left"	=> "left",
				"Center"	=> "center",
				"Right"	=> "right",
			),
			"std"         => "center",
			"dependency"	=> Array(
				'element'	=> "icon",
				'not_empty'	=> true,
			),			
			"description"	=> "Select your icon position",
			"group"		=> "Icon",
		),
		array(
			"type"		=> "colorpicker",
			"heading"	=> "Icon Color",
			"param_name"	=> "icon_color",
			"value"		=> "#bbbbbb",
			"dependency"	=> Array(
				'element'	=> "icon",
				'not_empty'	=> true,
			),
			"group"		=> "Icon",
		),
		array(
			"type"		=> "colorpicker",
			"heading"	=> "Icon Background",
			"param_name"	=> "icon_bg",
			"value"		=> "#ffffff",
			"dependency"	=> Array(
				'element'	=> "icon",
				'not_empty'	=> true,
			),
			"group"		=> "Icon",
		),	
		array(
			"type"		=> "colorpicker",
			"heading"	=> "Icon Background Border",
			"param_name"	=> "icon_bg_border",
			"value"		=> "",
			"dependency"	=> Array(
				'element'	=> "icon",
				'not_empty'	=> true,
			),
			"group"		=> "Icon",
		),			
		array(
			"type"		=> "textfield",
			"heading"	=> "Icon Size",
			"param_name"	=> "icon_size",
			"value"		=> "14px",
			"dependency"	=> Array(
				'element'	=> "icon",
				'not_empty' 	=> true
			),
			"description"	=> "Enter a custom icon size in pixels for your divider icon. Default is 14px",
			"group"		=> "Icon",
		),
		array(
			"type"		=> "textfield",
			"heading"	=> "Icon Padding",
			"param_name"	=> "icon_padding",
			"value"		=> "10px",
			"dependency"	=> Array(
				'element'	=> "icon",
				'not_empty'	=> true
			),
			"description"	=> "Change the default padding of the icon. Default is 10px",
			"group"		=> "Icon",				
		),			
	)
));	