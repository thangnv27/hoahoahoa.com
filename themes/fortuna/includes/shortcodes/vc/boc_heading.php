<?php

/**
	Heading Shortcode
**/

// Register Shortcode
if( ! function_exists( 'boc_shortcode_heading' ) ) {
	function boc_shortcode_heading( $atts, $content = null ) {

		$atts = vc_map_get_attributes('boc_heading', $atts );
		extract( $atts );

		// CSS Animation
		$css_animation_classes = "";
		if ( $css_animation !== '' ) {
			$css_animation_classes = 'boc_animate_when_almost_visible boc_'. $css_animation .'';
		}
		
		$custom_styles = array();
		
		if( $margin_bottom ) {
			$custom_styles[] = 'margin-bottom: '. $margin_bottom .';';
		}
		if ( $margin_top ) {
			$custom_styles[] = 'margin-top: '. $margin_top .';';
		}

		if ( $alignment ) {
			if($alignment == "left"){
				// $custom_styles[] = 'text-align: left;';
				$css_classes .= ' al_left ';
			}elseif($alignment == "center"){
				// $custom_styles[] = 'text-align: center;';
				$css_classes .= ' center ';
			}elseif($alignment == "right"){
				// $custom_styles[] = 'text-align: right;';
				$css_classes .= ' al_right ';
			}
		}

		if ( $color ) {
			$custom_styles[] = 'color: '. $color .';';
		}
		if ( $font_size ) {
			$custom_styles[] = 'font-size: '. $font_size .';';
			if($html_element == 'div' || $html_element == 'p'){
				$custom_styles[] = 'line-height: 1.7em;';
			}
		}
		if ( $subheading ) {
			$css_classes .= ' boc_subheading';
		}
		if ( $background ) {
			$css_classes .= " ".$background." ";
		}
		
		$custom_styles = implode('', $custom_styles);

		if ( $custom_styles ) {
			$custom_styles = wp_kses( $custom_styles, array() );
			$custom_styles = ' style="' . esc_attr($custom_styles) . '"';
		}
			
		return '<'.wp_kses_post($html_element).' class="boc_heading '. esc_attr($css_classes) .' '.esc_attr($css_animation_classes).'" '.$custom_styles.'><span>'.do_shortcode($content).'</span></'.wp_kses_post($html_element).'>';
	}

	add_shortcode('boc_heading', 'boc_shortcode_heading');
}



// Map Shortcode in Visual Composer
vc_map( array(
	"name" 		=> __("Fortuna Heading", 'Fortuna'),
	"base" 		=> "boc_heading",
	"icon" 		=> "boc_heading",
	"category" 	=> "Fortuna Shortcodes",
	"weight"	=> 76,
	"params" => array(
		array(
		 "type" 		=> "textfield",
		 "heading" 		=> "Text",
		 "param_name"	=> "content",
		 "admin_label"	=> true,
		 "value" 		=> "Fortuna <strong>Heading</strong>  Text",
		 "description"=> "Heading Text. Wrap in a &lt;strong&gt; tag for accent color of heading elements H1-H5"
		),
		array(
			"type"		=> "dropdown",
			"heading"	=> "HTML Element",
			"param_name"	=> "html_element",
			"admin_label"	=> true,
			"value"		=> array(
				"h1"	=> "h1",
				"h2"	=> "h2",
				"h3"	=> "h3",
				"h4"	=> "h4",
				"h5"	=> "h5",
				"div"=> "div",
				"p"	=> "p",
			),
			"std"         => "h2",
			"description"	=> "Select your Heading element type (H1 - H5, etc). To change default design of the element click on Design tab",
		),
		array(
			"type"		=> "dropdown",
			"heading"	=> "Alignment",
			"param_name"	=> "alignment",
			"value"		=> array(
				"Left"	=> "left",
				"Center"=> "center",
				"Right"	=> "right",
			),
			"std"         => "left",
			"description"	=> "Select your Heading alignment",
		),
		array(
			"type"		=> "dropdown",
			"heading"	=> __("CSS Animation", "Fortuna"),
			"param_name"	=> "css_animation",
			"admin_label"	=> true,				
			"value"			=> array(
				__("None", "Fortuna")					=> '',
				__("Top to bottom", "Fortuna")			=> "top-to-bottom",
				__("Bottom to top", "Fortuna")			=> "bottom-to-top",
				__("Left to right", "Fortuna")			=> "left-to-right",
				__("Right to left", "Fortuna")			=> "right-to-left",
				__("Fade In", "Fortuna")				=> "fade-in"),
			"description"	=> __("Select one if you want this element to be animated once it enters the browsers viewport.", "Fortuna"),
		),		
		array(
			"type" 		=> "textfield",
			"heading" 	=> "Extra class name",
			"param_name" 	=> "css_classes",
			"description"=> "If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file"
		),			
		array(
			"type"		=> "colorpicker",
			"heading"	=> "Heading Color",
			"param_name"	=> "color",
			"value"		=> "#333",
			"description"	=> "Select your Heading text color. Defaults to dark grey (#333)",
			"group"		=> "Design",	
		),
		array(
			"type"		=> "textfield",
			"heading"	=> "Font size",
			"param_name"	=> "font_size",
			"description"	=> "Overwrite default font-size (px), leave out empty for default value of the HTML element you picked",
			"group"		=> "Design",
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Use Subheading Italic Font", 'Fortuna'),
			"param_name" 	=> "subheading",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Use default theme subheading font (Droid Serif - Italic)", 'Fortuna'),
			"group"			=> "Design",
		),		
		array(
			"type"		=> "dropdown",
			"heading"	=> "Custom Background",
			"param_name"	=> "background",
			"value"		=> array(
				"None"				=> "",
				"Diagonal"			=> "bgr_diagonal",
				"Dotted"			=> "bgr_dotted",
				"Multi-Dotted"		=> "bgr_multidotted",
				"Single Line"		=> "bgr_single",
				"Double Line"		=> "bgr_double",
			),
			"description"	=> "Select your Heading alignment",
			"group"		=> "Design",				
		),			
		array(
			"type"			=> "textfield",
			"heading"		=> "Margin Top",
			"param_name"	=> "margin_top",
			"std"			=> "0px",
			"description"	=> "Overwrite default top margin (px), leave out empty for default value of the HTML element you picked",
			"group"			=> "Design",
		),
		array(
			"type"			=> "textfield",
			"heading"		=> "Margin Bottom",
			"param_name"	=> "margin_bottom",
			"std"			=> "20px",
			"description"	=> "Overwrite default bottom margin (px), leave out empty for default value of the HTML element you picked",
			"group"			=> "Design",				
		),
   )
));
