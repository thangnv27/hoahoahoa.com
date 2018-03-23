<?php

/**
	Counter
**/


// Register Shortcode
if( ! function_exists( 'shortcode_boc_counter' ) ) {
	function shortcode_boc_counter( $atts, $content = null ) {


		$atts = vc_map_get_attributes('boc_counter', $atts );
		extract( $atts );

		$id = rand(1,10000);

		$str = '';
		if($custom_color) {
			$str .= '<style> #counter'.$id.' .counter-digit { color: '.esc_attr($color).';} </style>';
		}
		if($centered) {
			$css_classes .= ' center';
		}	
		if($smaller_counter) {
			$css_classes .= ' smaller_counter';
		}	
		
		$str .= '<div class="single_numbers_holder boc_anim_hidden '.esc_attr($css_classes).' '.($centered ? 'centered_digits' : "").' '.($white_text ? "white_text" : "").'">';
		$str  .= '<div id="counter'.$id.'" class="counter">
						<input type="hidden" class="counter_hidden" data-end-nu="'.(int)$number.'" name="counter'.$id.'-value" value="" />
						'.($title ? '<div class="counter_desc">'.wp_kses_post($title).'</div>' : '').'
					</div>';
		$str .= '</div>';			
		return $str;
	}
	
	add_shortcode('boc_counter', 'shortcode_boc_counter');
}


// Map Shortcode in Visual Composer
vc_map( array(
	"name" => __("Animated Counter", 'Fortuna'),
	"base" =>  "boc_counter",
	"category" => "Fortuna Shortcodes",
	"icon" 		=> "boc_counter",
	"weight"	=> 34,
	"params" => array(
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Title Below the Counter (optional)", 'Fortuna'),
			"admin_label"	=> true,
			"param_name" 	=> "title",
			"std" 			=> "",
			"description"	=> __("Set your Counter Title", 'Fortuna'),
		),		
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Number", 'Fortuna'),
			"admin_label"	=> true,
			"param_name" 	=> "number",
			"value" 		=> "87",
			"description" 	=> __("Number to count to", 'Fortuna'),
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Smaller Counter?", 'Fortuna'),
			"param_name" 	=> "smaller_counter",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Tick to use a smaller text-size for your counter Digits", 'Fortuna'),
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Pick Custom Color?", 'Fortuna'),
			"param_name" 	=> "custom_color",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Otherwise your Counter Digits will inherit your Main Theme Color", 'Fortuna'),
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
			"heading" 		=> __("Dark Background?", 'Fortuna'),
			"param_name" 	=> "white_text",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Will make the Title white", 'Fortuna'),
		),			array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Centered Counter in Column", 'Fortuna'),
			"param_name" 	=> "centered",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Extra class name", 'Fortuna'),
			"param_name" 	=> "css_classes",
			"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),			
	)
));