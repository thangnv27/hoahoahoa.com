<?php

/**
	Circle Counter
**/


// Register Shortcode
if( ! function_exists( 'shortcode_boc_circle_counter' ) ) {
	function shortcode_boc_circle_counter( $atts, $content = null ) {
	
		$atts = vc_map_get_attributes('boc_circle_counter', $atts );
		extract( $atts );

		$id = rand(0,10000);

		if(!$custom_color) {
			$color = boc_get_main_color();
		}
		if($centered) {
			$css_classes .= ' center';
		}
		if($angle) {
			$css_classes .= ' half_circle';
		}
		if($size == 120) {
			$css_classes .= ' small_counter';
		} elseif($size == 160) {
			$css_classes .= ' large_counter';
		}

		$str = '<div class="circ_numbers_holder '.esc_attr($css_classes).' '.($white_text ? "white_text" : "").'">
					<div class="circ_counter" data-color="'.esc_attr($color).'" data-angle="'.esc_attr($angle).'" data-size="'.esc_attr($size).'" data-white_text="'.($white_text ? "1" : "0").'">
						<canvas width='.esc_attr($size).' height='.esc_attr($size).'  data-end-nu="'.(int)$number.'"></canvas>
						<div class="circ_counter_text_holder"><span class="circ_counter_text"  id="circ_counter_text'.$id.'"></span><span class="counter_percent_sign heading_font">%</span></div>
						<div class="circ_counter_desc">'.wp_kses_post($title).'</div>
					</div>
				</div>';

		return $str;
	}
	
	add_shortcode('boc_circle_counter', 'shortcode_boc_circle_counter');
}

// Map Shortcode in Visual Composer
vc_map( array(
	"name" => __("Circle Counter", 'Fortuna'),
	"base" =>  "boc_circle_counter",
	"category" => "Fortuna Shortcodes",
	"icon" 		=> "boc_circle_counter",
	"weight"	=> 32,
	"params" => array(
		array(
			"type" 			=> "textfield",
			"admin_label"	=> true,
			"heading" 		=> __("Title", 'Fortuna'),
			"param_name" 	=> "title",
			"value" 		=> __("Title", 'Fortuna'),
			"description" 	=> __("Set your Counter Title", 'Fortuna'),
		),
		array(
			"type" => "textfield",
			"admin_label"	=> true,
			"heading" => __("Number", 'Fortuna'),
			"param_name" => "number",
			"value" => "87",
			"description" => __("Number to count to", 'Fortuna'),
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> __( "Size", 'Fortuna'),
			"param_name"	=> "size",
			"value"			=> array(
				"Small"		=> "120",
				"Medium"	=> "140",
				"Large"		=> "160",
			),
			"std"			=> "140",
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Pick Custom Color?", 'Fortuna'),
			"param_name" 	=> "custom_color",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Otherwise your Bar Graph will inherit your Main Theme Color", 'Fortuna'),
		),			
		array(
			"type"			=> "colorpicker",
			"heading"		=> "Color",
			"param_name"	=> "color",
			"value"			=> "#666666",
			"dependency"	=> Array(
				'element'	=> "custom_color",
				'not_empty'	=> true,
			)
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Half Circle Only?", 'Fortuna'),
			"param_name" 	=> "angle",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Will animate only half circle Gauge", 'Fortuna'),
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Centered Counter in Column", 'Fortuna'),
			"param_name" 	=> "centered",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Dark Background?", 'Fortuna'),
			"param_name" 	=> "white_text",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Will make the Title white and the circle BGR dark", 'Fortuna'),
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Extra class name", 'Fortuna'),
			"param_name" 	=> "css_classes",
			"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),
	)
));