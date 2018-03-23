<?php

/**
	Bar Graph
**/

// Register Shortcode
if( ! function_exists( 'boc_bar_graph' ) ) {
	function boc_bar_graph($atts, $content = null) {
		
		$atts = vc_map_get_attributes('boc_bar_graph', $atts );
		extract( $atts );

		$graph_style = '';
		if($custom_color){
			$graph_style = 'style="background-color: '.esc_attr($color).';"';
		}

		$css_classes = '';
		if($thin_style) {
			$css_classes .= ' thin_style';
		}
		if($animated_bgr) {
			$css_classes .= ' animated_bgr';
		}
		if($dark_percent) {
			$css_classes .= ' dark_percent';
		}

		$bar = '
		<div class="bar_graph boc_animate_when_almost_visible_custom_start boc_fade-in '.$css_classes.'">

			<p>' . wp_kses_post($title) . '</p>
			<div class="bar_container"><span '.$graph_style.' data-width="' . esc_attr($percent) . '"> <strong>' . (int)$percent . '%</strong> </span></div>

		</div>';
		return $bar;
	}
	
	add_shortcode('boc_bar_graph', 'boc_bar_graph');
}

// Map Shortcode in Visual Composer
vc_map( array(
	"name" => __("Bar Graph Item", 'Fortuna'),
	"base" => "boc_bar_graph",
	"category" => "Fortuna Shortcodes",
	"icon" 	=> "boc_bar_graph",
	"weight"	=> 40,
	"params" => array(
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Title", 'Fortuna'),
			"param_name" 	=> "title",
			"value" 		=> __("Graph Title", 'Fortuna'),
			"description" 	=> __("Set your Graph Title", 'Fortuna'),
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Percent", 'Fortuna'),
			"param_name" 	=> "percent",
			"value"			=> "85",
			"description" 	=> __("Enter your Percentage Value (0-100)", 'Fortuna'),
		),
		array(
			"type"			=> 'checkbox',
			"heading"		=> __("Thin Line Style?", "Fortuna", 'Fortuna'),
			"param_name"	=> "thin_style",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
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
			"heading"		=> __("Animated Background?", "Fortuna"),
			"param_name"	=> "animated_bgr",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
		),			
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Dark Percent Background", 'Fortuna'),
			"param_name" 	=> "dark_percent",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Otherwise it is grey with dark text", 'Fortuna'),
		),				
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Extra class name", 'Fortuna'),
			"param_name" 	=> "css_classes",
			"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),
	)
));